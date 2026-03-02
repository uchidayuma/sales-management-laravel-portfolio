locals {
  name_prefix = "${var.project_name}-${var.environment}"
}

data "aws_availability_zones" "available" {}

# VPC for EKS Cluster
module "vpc" {
  source  = "terraform-aws-modules/vpc/aws"
  version = "~> 5.0"

  name = "${local.name_prefix}-vpc"
  cidr = var.vpc_cidr

  azs             = slice(data.aws_availability_zones.available.names, 0, 2)
  private_subnets = [for k, v in [0, 1] : cidrsubnet(var.vpc_cidr, 8, k)]
  public_subnets  = [for k, v in [0, 1] : cidrsubnet(var.vpc_cidr, 8, k + 10)]

  enable_nat_gateway   = true
  single_nat_gateway   = true
  enable_dns_hostnames = true

  # Tags required by Kubernetes/EKS to discover subnets for ELB/ALB placement
  public_subnet_tags = {
    "kubernetes.io/role/elb" = 1
  }

  private_subnet_tags = {
    "kubernetes.io/role/internal-elb" = 1
  }
}
