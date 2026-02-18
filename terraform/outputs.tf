output "alb_dns_name" {
  value = aws_lb.main.dns_name
  description = "The DNS name of the ALB"
}

output "db_endpoint" {
  value = aws_db_instance.default.endpoint
  description = "The endpoint of the database"
}
