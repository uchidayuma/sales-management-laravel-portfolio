#!/bin/bash
# =============================================================================
# デプロイスクリプト: ECR イメージビルド・プッシュ・ECS 更新
#
# 使い方:
#   ./deploy.sh              # 通常デプロイ
#   ./deploy.sh --wait       # デプロイ完了まで待機
# =============================================================================
set -euo pipefail

# --- 設定 ---
readonly AWS_ACCOUNT_ID="200144044244"
readonly AWS_REGION="ap-northeast-1"
readonly ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
readonly APP_REPO="laravel-sales-dev-app-repo"
readonly WEB_REPO="laravel-sales-dev-web-repo"
readonly ECS_CLUSTER="laravel-sales-dev-cluster"
readonly ECS_SERVICE="laravel-sales-dev-service"
readonly TASK_FAMILY="laravel-sales-dev-app"

# --- オプション解析 ---
WAIT_FOR_DEPLOY=false
for arg in "$@"; do
  [[ "${arg}" == "--wait" ]] && WAIT_FOR_DEPLOY=true
done

# --- 依存コマンド確認 ---
for cmd in aws docker jq; do
  if ! command -v "${cmd}" &>/dev/null; then
    echo "[ERROR] '${cmd}' がインストールされていません。" >&2
    exit 1
  fi
done

# --- タイムスタンプタグ (例: 20260228153000) ---
readonly TAG=$(date +%Y%m%d%H%M%S)
readonly APP_IMAGE="${ECR_REGISTRY}/${APP_REPO}:${TAG}"
readonly WEB_IMAGE="${ECR_REGISTRY}/${WEB_REPO}:${TAG}"

echo "=================================================="
echo " デプロイ開始"
echo " タグ : ${TAG}"
echo " app  : ${APP_IMAGE}"
echo " web  : ${WEB_IMAGE}"
echo "=================================================="

# =============================================================================
# 1. ECR ログイン
# =============================================================================
echo ""
echo "[1/5] ECR にログイン..."
aws ecr get-login-password --region "${AWS_REGION}" \
  | docker login --username AWS --password-stdin "${ECR_REGISTRY}"

# =============================================================================
# 2. Docker イメージビルド (linux/arm64: ECS タスク定義の cpu_architecture に合わせる)
# =============================================================================
echo ""
echo "[2/5] Docker イメージをビルド..."

echo "  => app イメージ (docker/php/Dockerfile.prod)"
docker buildx build \
  --platform linux/arm64 \
  --file docker/php/Dockerfile.prod \
  --tag "${APP_IMAGE}" \
  --load \
  .

echo "  => web イメージ (docker/nginx/Dockerfile)"
docker buildx build \
  --platform linux/arm64 \
  --file docker/nginx/Dockerfile \
  --tag "${WEB_IMAGE}" \
  --load \
  .

# =============================================================================
# 3. ECR プッシュ
# =============================================================================
echo ""
echo "[3/5] ECR にプッシュ..."
docker push "${APP_IMAGE}"
docker push "${WEB_IMAGE}"

# =============================================================================
# 4. ECS タスク定義を更新
#    現在の定義からビルドツール系フィールドを除き、イメージ URI だけ差し替えて再登録する
# =============================================================================
echo ""
echo "[4/5] ECS タスク定義を更新..."

# 一時ファイルを作成し、スクリプト終了時に自動削除する
TASK_DEF_JSON=$(mktemp /tmp/ecs-task-def-XXXXXX.json)
trap "rm -f ${TASK_DEF_JSON}" EXIT

CURRENT_TASK_DEF=$(aws ecs describe-task-definition \
  --task-definition "${TASK_FAMILY}" \
  --region "${AWS_REGION}" \
  --query 'taskDefinition' \
  --output json)

# イメージ URI を差し替えて必要フィールドのみ抽出し、null 値を除去して一時ファイルに書き出す
# NOTE: terraform-ecs/ecs.tf の lifecycle.ignore_changes により terraform apply は
#       コンテナ定義を更新しないため、セキュリティ設定の変更もここで明示的に適用する。
echo "${CURRENT_TASK_DEF}" \
  | jq --arg app_image "${APP_IMAGE}" \
       --arg web_image "${WEB_IMAGE}" \
    '
      .containerDefinitions |= map(
        if .name == "app" then
          # イメージ URI を更新
          .image = $app_image |
          # PHP-FPM マスタは root で起動し pool 設定で www-data ワーカーを fork する。
          # setuid/setgid に CAP_SETUID/CAP_SETGID が必要なため drop=ALL は使用不可。
          # 危険なケーパビリティのみ個別に drop する（Fargate はデフォルトで SYS_ADMIN 等を持たない）。
          .linuxParameters.capabilities = {"add": [], "drop": ["MKNOD","AUDIT_WRITE","SETFCAP","NET_RAW"]}
        elif .name == "web" then
          # イメージ URI を更新
          .image = $web_image |
          # Fargate は CAP_CHOWN を add 不可のため drop = ["ALL"] は使えない。
          # 危険なケーパビリティのみ個別に drop し、Nginx entrypoint の chown を許可する。
          .linuxParameters.capabilities = {"add": [], "drop": ["MKNOD","AUDIT_WRITE","SETFCAP","NET_RAW"]}
        else .
        end
      ) |
      {
        family:                  .family,
        networkMode:             .networkMode,
        containerDefinitions:    .containerDefinitions,
        requiresCompatibilities: .requiresCompatibilities,
        cpu:                     .cpu,
        memory:                  .memory,
        executionRoleArn:        .executionRoleArn,
        taskRoleArn:             .taskRoleArn,
        runtimePlatform:         .runtimePlatform,
        volumes:                 .volumes
      }
      | with_entries(select(.value != null))
    ' > "${TASK_DEF_JSON}"

NEW_TASK_DEF_ARN=$(aws ecs register-task-definition \
  --region "${AWS_REGION}" \
  --cli-input-json "file://${TASK_DEF_JSON}" \
  --query 'taskDefinition.taskDefinitionArn' \
  --output text)

echo "  => 新しいタスク定義: ${NEW_TASK_DEF_ARN}"

# =============================================================================
# 5. ECS サービス更新
# =============================================================================
echo ""
echo "[5/5] ECS サービスを更新..."
aws ecs update-service \
  --cluster "${ECS_CLUSTER}" \
  --service "${ECS_SERVICE}" \
  --task-definition "${NEW_TASK_DEF_ARN}" \
  --region "${AWS_REGION}" \
  --output json \
  | jq -r '.service | "  => サービス: \(.serviceName) / ステータス: \(.status) / タスク定義: \(.taskDefinition)"'

# =============================================================================
# (オプション) デプロイ完了待機 --wait 指定時のみ
# =============================================================================
if "${WAIT_FOR_DEPLOY}"; then
  echo ""
  echo "デプロイ完了を待機中... (Ctrl+C でキャンセル)"
  aws ecs wait services-stable \
    --cluster "${ECS_CLUSTER}" \
    --services "${ECS_SERVICE}" \
    --region "${AWS_REGION}"
  echo "  => デプロイ完了"
fi

# =============================================================================
# Terraform tfvars を最新タグで更新 (terraform apply 時の整合性維持)
# =============================================================================
TFVARS_FILE="terraform-ecs/terraform.tfvars"
if [[ -f "${TFVARS_FILE}" ]]; then
  sed -i.bak \
    -e "s|${ECR_REGISTRY}/${APP_REPO}:[^\"]*|${APP_IMAGE}|g" \
    -e "s|${ECR_REGISTRY}/${WEB_REPO}:[^\"]*|${WEB_IMAGE}|g" \
    "${TFVARS_FILE}"
  rm -f "${TFVARS_FILE}.bak"
  echo ""
  echo "  => terraform-ecs/terraform.tfvars を更新しました"
fi

echo ""
echo "=================================================="
echo " デプロイ完了"
echo " タグ : ${TAG}"
echo " app  : ${APP_IMAGE}"
echo " web  : ${WEB_IMAGE}"
echo "=================================================="
