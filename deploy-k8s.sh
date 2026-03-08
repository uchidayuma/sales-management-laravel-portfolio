#!/bin/bash
# =============================================================================
# EKSデプロイスクリプト: ECR イメージビルド (AMD64)・プッシュ・K8s マニフェスト更新
#
# 使い方:
#   ./deploy-k8s.sh              # 通常デプロイ
#   ./deploy-k8s.sh --wait       # デプロイ完了まで待機
# =============================================================================
set -euo pipefail

# --- 設定 ---
readonly AWS_ACCOUNT_ID="200144044244"
readonly AWS_REGION="ap-northeast-1"
readonly ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
readonly APP_REPO="laravel-sales-dev-app-repo"
readonly WEB_REPO="laravel-sales-dev-web-repo"
readonly K8s_NAMESPACE="sales-app"
readonly K8s_DEPLOYMENT="sales-deployment"

# --- オプション解析 ---
WAIT_FOR_DEPLOY=false
for arg in "$@"; do
  [[ "${arg}" == "--wait" ]] && WAIT_FOR_DEPLOY=true
done

# --- 依存コマンド確認 ---
for cmd in aws docker kubectl; do
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
echo " EKSデプロイ開始 (対象アーキテクチャ: linux/amd64)"
echo " タグ : ${TAG}"
echo " app  : ${APP_IMAGE}"
echo " web  : ${WEB_IMAGE}"
echo "=================================================="

# =============================================================================
# 1. ECR ログイン
# =============================================================================
echo ""
echo "[1/4] ECR にログイン..."
aws ecr get-login-password --region "${AWS_REGION}" \
  | docker login --username AWS --password-stdin "${ECR_REGISTRY}"

# =============================================================================
# 2. Docker イメージビルド (linux/amd64: EKS Fargate は現状 AMD64 のみ対応)
# =============================================================================
echo ""
echo "[2/4] Docker イメージをビルド (linux/amd64)..."

echo "  => app イメージ (docker/php/Dockerfile.prod)"
docker buildx build \
  --platform linux/amd64 \
  --file docker/php/Dockerfile.prod \
  --tag "${APP_IMAGE}" \
  --load \
  .

echo "  => web イメージ (docker/nginx/Dockerfile)"
docker buildx build \
  --platform linux/amd64 \
  --file docker/nginx/Dockerfile \
  --tag "${WEB_IMAGE}" \
  --load \
  .

# =============================================================================
# 3. ECR プッシュ
# =============================================================================
echo ""
echo "[3/4] ECR にプッシュ..."
docker push "${APP_IMAGE}"
docker push "${WEB_IMAGE}"

# =============================================================================
# 4. K8s マニフェスト (04-deployment.yaml) のイメージタグ更新と apply
# =============================================================================
echo ""
echo "[4/4] Kubernetes へのデプロイ..."
MANIFEST_FILE="k8s-manifests/04-deployment.yaml"

if [[ -f "${MANIFEST_FILE}" ]]; then
  # sedでイメージURIを置換
  # （MacOSのsed仕様のため -i ".bak" としています）
  sed -i.bak \
    -e "s|${ECR_REGISTRY}/${APP_REPO}:[^\"]*|${APP_IMAGE}|g" \
    -e "s|${ECR_REGISTRY}/${WEB_REPO}:[^\"]*|${WEB_IMAGE}|g" \
    "${MANIFEST_FILE}"
  rm -f "${MANIFEST_FILE}.bak"
  echo "  => ${MANIFEST_FILE} のイメージタグを更新しました"

  # マニフェストの適用
  kubectl apply -f k8s-manifests/
else
  echo "[WARN] ${MANIFEST_FILE} が見つかりませんでした。マニフェストの更新はスキップされます。"
fi

# =============================================================================
# (オプション) デプロイ完了待機 --wait 指定時のみ
# =============================================================================
if "${WAIT_FOR_DEPLOY}"; then
  echo ""
  echo "デプロイ（PodのRolling Update）完了を待機中..."
  kubectl rollout status deployment/"${K8s_DEPLOYMENT}" -n "${K8s_NAMESPACE}"
  echo "  => デプロイ完了"
fi

echo ""
echo "=================================================="
echo " デプロイ完了"
echo " タグ : ${TAG}"
echo " app  : ${APP_IMAGE}"
echo " web  : ${WEB_IMAGE}"
echo "=================================================="
