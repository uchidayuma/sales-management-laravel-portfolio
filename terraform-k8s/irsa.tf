

locals {
  # OIDCプロバイダのURLから "https://" を除外
  oidc_provider_url = replace(module.eks.cluster_oidc_issuer_url, "https://", "")
}

# 【レクチャー 35: EKS固有のIRSA (信頼ポリシー)】
# ServiceAccount "sales-app-sa" がこのIAMロールを引き受ける(AssumeRole)ための信頼ポリシーを定義します。
data "aws_iam_policy_document" "sales_app_sa_assume_role" {
  statement {
    actions = ["sts:AssumeRoleWithWebIdentity"]
    effect  = "Allow"

    principals {
      type        = "Federated"
      identifiers = [module.eks.oidc_provider_arn]
    }

    condition {
      test     = "StringEquals"
      variable = "${local.oidc_provider_url}:sub"
      # Kubernetes側で作成する Namespace と ServiceAccount 名を指定
      values = ["system:serviceaccount:sales-app:sales-app-sa"]
    }

    condition {
      test     = "StringEquals"
      variable = "${local.oidc_provider_url}:aud"
      values   = ["sts.amazonaws.com"]
    }
  }
}

# 【レクチャー 35: EKS固有のIRSA (IAMロール)】
# IRSA用のIAMロール本体です。K8sのPodは、このロールを通じてAWSリソースへアクセスします。
resource "aws_iam_role" "sales_app_irsa_role" {
  name               = "${local.name_prefix}-sales-app-irsa-role"
  assume_role_policy = data.aws_iam_policy_document.sales_app_sa_assume_role.json
}

# 【レクチャー 35: EKS固有のIRSA (ポリシーのアタッチ)】
# ここにPodへ付与したいAWS権限（ポリシー）をアタッチします。
# 例：SSM Parameter Store からDBパスワード等を読み取る権限など（コースの要件に合わせて追加・変更してください）
resource "aws_iam_role_policy_attachment" "sales_app_ssm_readonly" {
  role       = aws_iam_role.sales_app_irsa_role.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMReadOnlyAccess"
}
