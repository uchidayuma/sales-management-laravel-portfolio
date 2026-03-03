output "alb_dns_name" {
  value = aws_lb.main.dns_name
  description = "The DNS name of the ALB"
}

output "db_endpoint" {
  value = aws_db_instance.default.endpoint
  description = "The endpoint of the database"
}

output "db_password_secret_arn" {
  value       = aws_secretsmanager_secret.db_password.arn
  description = "ARN of the Secrets Manager secret storing the DB password"
}
