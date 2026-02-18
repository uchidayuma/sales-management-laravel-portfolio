resource "aws_s3_bucket" "uploads" {
  bucket_prefix = "${local.name_prefix}-uploads-"
  # Bulletproof Destruction: force_destroy allows deleting bucket with objects
  force_destroy = true 

  tags = {
    Name = "${local.name_prefix}-uploads"
  }
}

resource "aws_s3_bucket_public_access_block" "uploads" {
  bucket = aws_s3_bucket.uploads.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

# Add other buckets if necessary (e.g., for logs or other storage)
