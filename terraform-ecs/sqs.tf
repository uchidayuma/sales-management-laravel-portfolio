/**
 * AWS SQS Configuration for Contact Import Job Processing
 * Issue #12: 大量顧客データの一括CSVインポート機能
 */

# SQS Queue for Contact Import Processing
resource "aws_sqs_queue" "contact_import_queue" {
  name                       = "${local.name_prefix}-contact-import-queue"
  delay_seconds              = 0
  max_message_size           = 262144 # 256 KB
  message_retention_seconds  = 1209600 # 14 days
  visibility_timeout_seconds = 300 # 5 minutes
  
  # Enable long polling
  receive_wait_time_seconds = 20

  # Enable content-based deduplication for FIFO
  fifo_queue = false

  tags = {
    Name = "${local.name_prefix}-contact-import-queue"
  }
}

# DLQ (Dead Letter Queue) for failed messages
resource "aws_sqs_queue" "contact_import_dlq" {
  name = "${local.name_prefix}-contact-import-dlq"
  
  message_retention_seconds = 1209600 # 14 days

  tags = {
    Name = "${local.name_prefix}-contact-import-dlq"
  }
}

# SQS Queue Redrive Policy (link main queue to DLQ)
resource "aws_sqs_queue_redrive_policy" "contact_import_redrive" {
  queue_url = aws_sqs_queue.contact_import_queue.id

  redrive_policy = jsonencode({
    deadLetterTargetArn = aws_sqs_queue.contact_import_dlq.arn
    maxReceiveCount     = 3 # Move to DLQ after 3 failed attempts
  })
}

# IAM User for SQS Access
resource "aws_iam_user" "contact_import_sqs_user" {
  name = "${local.name_prefix}-contact-import-sqs-user"
  path = "/service-accounts/"

  tags = {
    Name = "${local.name_prefix}-contact-import-sqs-user"
  }
}

# IAM Policy for SQS Queue Access
resource "aws_iam_user_policy" "contact_import_sqs_policy" {
  name   = "${local.name_prefix}-contact-import-sqs-policy"
  user   = aws_iam_user.contact_import_sqs_user.name
  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid    = "SendMessage"
        Effect = "Allow"
        Action = [
          "sqs:SendMessage"
        ]
        Resource = aws_sqs_queue.contact_import_queue.arn
      },
      {
        Sid    = "ReceiveMessage"
        Effect = "Allow"
        Action = [
          "sqs:ReceiveMessage",
          "sqs:DeleteMessage",
          "sqs:GetQueueAttributes",
          "sqs:ChangeMessageVisibility"
        ]
        Resource = [
          aws_sqs_queue.contact_import_queue.arn,
          aws_sqs_queue.contact_import_dlq.arn
        ]
      },
      {
        Sid    = "GetQueueUrl"
        Effect = "Allow"
        Action = [
          "sqs:GetQueueUrl"
        ]
        Resource = "arn:aws:sqs:${var.region}:${data.aws_caller_identity.current.account_id}:${local.name_prefix}-*"
      }
    ]
  })
}

# Access Key for IAM User
resource "aws_iam_access_key" "contact_import_sqs_key" {
  user = aws_iam_user.contact_import_sqs_user.name

  lifecycle {
    create_before_destroy = true
  }
}

# CloudWatch Alarms for SQS Queue Monitoring

# Alarm for queue depth
resource "aws_cloudwatch_metric_alarm" "contact_import_queue_depth_alarm" {
  alarm_name          = "${local.name_prefix}-contact-import-queue-depth-alarm"
  comparison_operator = "GreaterThanThreshold"
  evaluation_periods  = "2"
  metric_name         = "ApproximateNumberOfMessagesVisible"
  namespace           = "AWS/SQS"
  period              = 300
  statistic           = "Average"
  threshold           = 100
  alarm_description   = "Alert when contact import queue has more than 100 visible messages"
  treat_missing_data  = "notBreaching"

  dimensions = {
    QueueName = aws_sqs_queue.contact_import_queue.name
  }

  alarm_actions = []
}

# Alarm for DLQ messages
resource "aws_cloudwatch_metric_alarm" "contact_import_dlq_alarm" {
  alarm_name          = "${local.name_prefix}-contact-import-dlq-alarm"
  comparison_operator = "GreaterThanOrEqualToThreshold"
  evaluation_periods  = "1"
  metric_name         = "ApproximateNumberOfMessagesVisible"
  namespace           = "AWS/SQS"
  period              = 300
  statistic           = "Average"
  threshold           = 1
  alarm_description   = "Alert when messages are sent to contact import DLQ"
  treat_missing_data  = "notBreaching"

  dimensions = {
    QueueName = aws_sqs_queue.contact_import_dlq.name
  }

  alarm_actions = []
}

# CloudWatch Log Group for SQS Processing
resource "aws_cloudwatch_log_group" "contact_import_log_group" {
  name              = "/aws/sqs/${local.name_prefix}/contact-import"
  retention_in_days = 30

  tags = {
    Name = "${local.name_prefix}-contact-import-logs"
  }
}

# Data source to get current AWS account ID
data "aws_caller_identity" "current" {}

# Outputs for SQS configuration
output "sqs_queue_url" {
  description = "URL of the Contact Import SQS Queue"
  value       = aws_sqs_queue.contact_import_queue.url
}

output "sqs_queue_arn" {
  description = "ARN of the Contact Import SQS Queue"
  value       = aws_sqs_queue.contact_import_queue.arn
}

output "sqs_dlq_url" {
  description = "URL of the Contact Import DLQ"
  value       = aws_sqs_queue.contact_import_dlq.url
}

output "sqs_dlq_arn" {
  description = "ARN of the Contact Import DLQ"
  value       = aws_sqs_queue.contact_import_dlq.arn
}

output "iam_user_name" {
  description = "IAM User name for SQS access"
  value       = aws_iam_user.contact_import_sqs_user.name
}

output "iam_access_key_id" {
  description = "IAM Access Key ID (Save this securely)"
  value       = aws_iam_access_key.contact_import_sqs_key.id
  sensitive   = true
}

output "iam_secret_access_key" {
  description = "IAM Secret Access Key (Save this securely)"
  value       = aws_iam_access_key.contact_import_sqs_key.secret
  sensitive   = true
}
