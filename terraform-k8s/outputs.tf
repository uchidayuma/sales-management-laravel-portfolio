output "cluster_endpoint" {
  description = "Endpoint for EKS control plane"
  value       = module.eks.cluster_endpoint
}

output "cluster_security_group_id" {
  description = "Security group ids attached to the cluster control plane"
  value       = module.eks.cluster_security_group_id
}

output "region" {
  description = "AWS region"
  value       = var.region
}

output "cluster_name" {
  description = "Kubernetes Cluster Name"
  value       = module.eks.cluster_name
}

output "configure_kubectl" {
  description = "Command to configure kubectl"
  value       = "aws eks --region ${var.region} update-kubeconfig --name ${module.eks.cluster_name}"
}

output "sales_app_irsa_role_arn" {
  description = "IAM Role ARN for the sales-app ServiceAccount (IRSA)"
  value       = aws_iam_role.sales_app_irsa_role.arn
}

output "db_host" {
  description = "RDS instance hostname"
  value       = aws_db_instance.default.address
}

output "redis_host" {
  description = "ElastiCache Redis cluster hostname"
  value       = aws_elasticache_cluster.default.cache_nodes[0].address
}

