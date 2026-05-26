module "eks" {
  source  = "terraform-aws-modules/eks/aws"
  version = "~> 19.0"

  cluster_name    = "${local.name_prefix}-cluster"
  cluster_version = "1.29" # EKS requires sequential upgrades (1.28 -> 1.29)

  vpc_id                         = module.vpc.vpc_id
  subnet_ids                     = module.vpc.private_subnets
  cluster_endpoint_public_access = true # Allow kubectl access from local computer

  # 【レクチャー 35: EKS固有のIRSA】
  # PodにIAMロールを割り当てるための機能(IAM Roles for Service Accounts)を有効化します。
  enable_irsa = true

  fargate_profiles = {
    sales_app = {
      name = "sales-app-profile"
      selectors = [
        {
          namespace = "sales-app"
        }
      ]
    }
    kube_system = {
      name = "kube-system-profile"
      selectors = [
        {
          namespace = "kube-system"
        }
      ]
    }
  }

  tags = {
    Environment = var.environment
  }
}
