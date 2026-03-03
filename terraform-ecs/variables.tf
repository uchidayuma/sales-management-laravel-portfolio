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
  default     = "dev"
}

variable "vpc_cidr" {
  description = "CIDR block for VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "db_username" {
  description = "Database master username"
  type        = string
  default     = "admin"
}

variable "db_password" {
  description = "Database master password"
  type        = string
  sensitive   = true
}

variable "app_image" {
  description = "Docker image for the app (PHP-FPM) container"
  type        = string
  default     = "uchidayuma/sales-management-laravel-app:latest" # Placeholder; replace with ECR URL after first push
}

variable "web_image" {
  description = "Docker image for the web (Nginx) container"
  type        = string
  default     = "uchidayuma/sales-management-laravel-web:latest" # Placeholder; replace with ECR URL after first push
}

variable "app_port" {
  description = "Port exposed by the docker container"
  type        = number
  default     = 8080
}

variable "app_count" {
  description = "Number of docker containers to run"
  type        = number
  default     = 1
}

variable "fargate_cpu" {
  description = "Fargate instance CPU units to provision (1 vCPU = 1024 CPU units)"
  type        = number
  default     = 256
}

variable "fargate_memory" {
  description = "Fargate instance memory to provision (in MiB)"
  type        = number
  default     = 512
}
