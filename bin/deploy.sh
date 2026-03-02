#!/bin/bash

# Exit on error
set -e

# Load AWS Profile if set, or ensure it's set
if [ -z "$AWS_PROFILE" ]; then
    echo "Warning: AWS_PROFILE is not set. Using default profile."
else
    echo "Using AWS_PROFILE: $AWS_PROFILE"
fi

# 1. Get ECR URL from Terraform output
echo "Getting ECR Repository URL..."

# Get absolute path to project root
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

cd "$PROJECT_ROOT/terraform-ecs"
ECR_URL=$(terraform output -raw ecr_repository_url)
cd "$PROJECT_ROOT"

if [ -z "$ECR_URL" ]; then
    echo "Error: Could not get ECR URL from Terraform. Make sure terraform apply has been run."
    exit 1
fi

echo "ECR URL: $ECR_URL"

# 2. Login to ECR
echo "Logging in to ECR..."
aws ecr get-login-password --region ap-northeast-1 | docker login --username AWS --password-stdin $ECR_URL

# 3. Build Docker Image (arm64 for Graviton/Fargate)
echo "Building Docker Image..."
# Use linux/arm64 to match Mac native build and Fargate ARM64 setting
docker build --platform linux/arm64 -t $ECR_URL:latest -f Dockerfile.prod .

# 4. Push to ECR
echo "Pushing Image to ECR..."
docker push $ECR_URL:latest

# 5. Update terraform-ecs/terraform.tfvars or apply with var
echo "Updating Infrastructure..."
cd terraform-ecs
# We pass the image URL explicitly to ensure the task definition updates
terraform apply -var="app_image=$ECR_URL:latest" -auto-approve

echo "Deployment Complete!"
echo "Check the migration task logs in CloudWatch if needed."
