#!/bin/bash
set -e

echo "=> Fetching credentials from Terraform and AWS Secrets Manager..."

cd terraform-k8s
DB_HOST=$(terraform output -raw db_host)
REDIS_HOST=$(terraform output -raw redis_host)
cd ..

DB_PASSWORD=$(aws secretsmanager get-secret-value --secret-id laravel-sales-k8s-dev/db-password --query SecretString --output text)
APP_KEY="base64:rlMRMUtjXxEzBbNo+fdpyXhg6WNfgbNz4wszDF1MoSo="

echo "=> Creating Kubernetes Secret (sales-app-secrets)..."

kubectl create secret generic sales-app-secrets -n sales-app \
  --from-literal=APP_KEY="${APP_KEY}" \
  --from-literal=DB_CONNECTION="mysql" \
  --from-literal=DB_HOST="${DB_HOST}" \
  --from-literal=DB_PORT="3306" \
  --from-literal=DB_DATABASE="sales_management" \
  --from-literal=DB_USERNAME="salesdbuser" \
  --from-literal=DB_PASSWORD="${DB_PASSWORD}" \
  --from-literal=REDIS_HOST="${REDIS_HOST}" \
  --from-literal=REDIS_PASSWORD="" \
  --from-literal=REDIS_PORT="6379" \
  --dry-run=client -o yaml | kubectl apply -f -

echo "=> Secret created/updated successfully!"
