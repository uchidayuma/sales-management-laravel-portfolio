resource "random_password" "db" {
  length           = 20
  special          = true
  override_special = "!#$%&*()-_=+[]{}<>:?"
}

resource "aws_secretsmanager_secret" "db_password" {
  name                    = "${local.name_prefix}/db-password"
  recovery_window_in_days = 0

  tags = {
    Name = "${local.name_prefix}-db-password"
  }
}

resource "aws_secretsmanager_secret_version" "db_password" {
  secret_id     = aws_secretsmanager_secret.db_password.id
  secret_string = random_password.db.result
}

resource "aws_db_subnet_group" "default" {
  name       = "${local.name_prefix}-db-subnet-group"
  subnet_ids = aws_subnet.private[*].id

  tags = {
    Name = "${local.name_prefix}-db-subnet-group"
  }
}

resource "aws_db_instance" "default" {
  identifier        = "${local.name_prefix}-db"
  allocated_storage = 20
  storage_type      = "gp2"
  engine            = "mysql"
  engine_version    = "8.0" 
  instance_class    = "db.t3.micro"
  db_name           = "sales_management"
  username          = var.db_username
  password          = random_password.db.result
  
  db_subnet_group_name   = aws_db_subnet_group.default.name
  vpc_security_group_ids = [aws_security_group.db.id]

  # Bulletproof Destruction Settings
  skip_final_snapshot     = true
  deletion_protection     = false
  backup_retention_period = 0 # Disable backups to speed up deletion
  apply_immediately       = true

  tags = {
    Name = "${local.name_prefix}-db"
  }
}
