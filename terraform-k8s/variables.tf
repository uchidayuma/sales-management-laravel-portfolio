variable "region" {
  description = "AWS Region"
  type        = string
  default     = "ap-northeast-1"
}

variable "project_name" {
  description = "Project Name"
  type        = string
  default     = "laravel-sales"
}

variable "environment" {
  description = "Environment Name"
  type        = string
  default     = "k8s-dev"
}

variable "vpc_cidr" {
  description = "CIDR block for VPC"
  type        = string
  default     = "10.1.0.0/16"
}

variable "db_password" {
  description = "Database master password for the EKS app (if RDS is provisioned)"
  type        = string
  sensitive   = true
  default     = "dummy-password" # Will be overridden in production
}

# The AWS account ID is used to dynamically construct the ECR ARN for NodeGroup permissions
data "aws_caller_identity" "current" {}
