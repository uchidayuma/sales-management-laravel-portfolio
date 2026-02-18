resource "aws_ecr_repository" "app" {
  name                 = "${local.name_prefix}-repo"
  image_tag_mutability = "MUTABLE"
  force_delete         = true # Automated destruction

  image_scanning_configuration {
    scan_on_push = true
  }

  tags = {
    Name = "${local.name_prefix}-repo"
  }
}

output "ecr_repository_url" {
  value = aws_ecr_repository.app.repository_url
  description = "The URL of the ECR repository"
}
