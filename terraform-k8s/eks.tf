module "eks" {
  source  = "terraform-aws-modules/eks/aws"
  version = "~> 19.0"

  cluster_name    = "${local.name_prefix}-cluster"
  cluster_version = "1.28" # Please adjust version according to latest stable

  vpc_id                         = module.vpc.vpc_id
  subnet_ids                     = module.vpc.private_subnets
  cluster_endpoint_public_access = true # Allow kubectl access from local computer

  # IRSA: Enable IAM Roles for Service Accounts
  enable_irsa = true

  eks_managed_node_groups = {
    default_node_group = {
      min_size       = 1
      max_size       = 3
      desired_size   = 2
      instance_types = ["t3.medium"]

      # Security: Ensuring the Node Group IAM Role has ECR Read Permissions
      # so it can pull the image built previously for ECS
      iam_role_additional_policies = {
        AmazonEC2ContainerRegistryReadOnly = "arn:aws:iam::aws:policy/AmazonEC2ContainerRegistryReadOnly"
      }
    }
  }

  tags = {
    Environment = var.environment
  }
}
