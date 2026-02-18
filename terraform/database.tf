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
  password          = var.db_password
  
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
