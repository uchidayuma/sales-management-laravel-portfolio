# Kubernetes (EKS) デプロイ手順書

この手順書では、Terraformを用いたAWSリソース（VPC, EKSクラスター, ALB, DB, Cache等）の構築から、Kubernetesマニフェストを用いたアプリケーションのデプロイまでを一貫して行います。

## 前提条件
- AWS環境の構築権限を持つIAMユーザーのアクセスキーが設定されていること（`aws configure` 完了済）
- 実行環境に `terraform` および `kubectl` がインストールされていること

## ステップ 1: AWSインフラの構築

まず、AWS上にインフラストラクチャーを作成します。EKSクラスターやDBの作成には通常15〜20分程度かかります。

```bash
# この README がある `terraform-k8s` ディレクトリ内で実行します

# 1. ワークスペースの初期化
terraform init

# 2. 差分の確認（任意）
terraform plan

# 3. インフラのデプロイ
terraform apply
# -> `yes` と入力して作成を開始
```

## ステップ 2: クラスターへの接続設定 (Kubeconfig)

`terraform apply` が完了すると、画面の最後（Outputs）に接続用のコマンドが表示されます。これを実行して `kubectl` がEKSを操作できるようにします。

```bash
# （例）Outputsに出力されたコマンドを実行
aws eks --region ap-northeast-1 update-kubeconfig --name laravel-sales-k8s-dev-cluster

# 接続確認: Nodeの一覧が表示されれば成功
kubectl get nodes
```

## ステップ 3: IRSA (IAM Role) の割り当て設定

TerraformのOutputsに表示された `sales_app_irsa_role_arn` の値（IAMロールのARN）をコピーします。
続いて、K8s側のServiceAccountマニフェストファイルを開き、ARNの値を書き換えます。

* **編集ファイル:** `../k8s-manifests/03-service-account.yaml`

```yaml
# 03-service-account.yaml (7行目付近)
  annotations:
    # 以下の行の ARN を、コピーした自分の ARN に書き換える
    eks.amazonaws.com/role-arn: "arn:aws:iam::123456789012:role/laravel-sales-k8s-dev-sales-app-irsa-role"
```

## ステップ 4: アプリケーションのデプロイ

最後に、Kubernetesのマニフェストを適用して、アプリケーション本体（Nginx, PHP-FPM等）とセキュリティ設定をデプロイします。

```bash
# プロジェクトのルートディレクトリ（またはターミナル）から実行
# ※相対パスで `k8s-manifests` フォルダを指定します
cd ../

# マニフェストフォルダ内の全YAMLを一括適用
kubectl apply -f k8s-manifests/

# 起動状況の確認
kubectl get pods -n sales-app
```

PodsのSTATUSがすべて `Running` になっていればデプロイ成功です！

## 確認（ブラウザへのアクセス）

デプロイ完了後、再度 `terraform-k8s` フォルダに戻り、Outputsとして出力されている `alb_dns_name` （例：`laravel-sales-k8s-dev-alb-12345.ap-northeast-1.elb.amazonaws.com`）をブラウザに入力して、アプリケーションにアクセスできるか確認してください。

---

## 🧹 環境の完全な削除手順 (課金の停止)

AWS環境（EKSクラスター、NAT Gateway、RDSなど）を長期間起動したままにすると利用料金が継続して発生します。検証が終わったら必ず以下の2ステップで「確実な削除」を行ってください。

Terraformで基盤を削除する前に、まずKubernetesで作成したALBなどのリソースを削除する必要があります。そうしないと、ALBがVPC等のネットワークを掴んだままになり、Terraformの削除コマンド（`terraform destroy`）がエラーで失敗してしまいます。

### ステップ 1: Kubernetes上のリソース（ALBなど）を削除
ALBなどの「上に乗っている機能」から削除します。プロジェクトのルートディレクトリで実行します。

```bash
kubectl delete -f k8s-manifests/
```
※ コマンド実行後、AWS側で実際にALBが削除されるまで3〜5分ほどかかります。必ず完了（EC2画面のロードバランサー一覧から消失）まで待機してください。

### ステップ 2: TerraformでAWSインフラストラクチャーを削除
上のステップを行って数分待った後、Terraformで構築した「土台」をすべて解体します。

```bash
cd terraform-k8s/
terraform destroy
```

途中で `Do you really want to destroy all resources?` （本当にすべて削除しますか？）と聞かれるため、キーボードで `yes` と入力してEnterを押してください。
これで課金が完全にストップします。
