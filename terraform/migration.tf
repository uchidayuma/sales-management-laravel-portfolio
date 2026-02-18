# Task Definition for Database Migration/Seeding
resource "aws_ecs_task_definition" "db_migrate" {
  family                   = "${local.name_prefix}-db-migrate"
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = var.fargate_cpu
  memory                   = var.fargate_memory
  execution_role_arn       = aws_iam_role.ecs_task_execution_role.arn
  task_role_arn            = aws_iam_role.ecs_task_role.arn

  runtime_platform {
    operating_system_family = "LINUX"
    cpu_architecture        = "ARM64"
  }

  container_definitions = jsonencode([
    {
      name      = "db-migrate"
      image     = var.app_image
      essential = true
      # Override command to run migration and seed
      # sleep 30 to ensure DB is fully ready after creation
      command   = ["/bin/sh", "-c", "sleep 30 && php artisan migrate:fresh --seed --force"]
      
      logConfiguration = {
        logDriver = "awslogs"
        options = {
          awslogs-group         = aws_cloudwatch_log_group.main.name
          awslogs-region        = var.region
          awslogs-stream-prefix = "migration"
        }
      }
      environment = [
        { name = "APP_ENV", value = var.environment },
        { name = "DB_HOST", value = aws_db_instance.default.address },
        { name = "DB_DATABASE", value = aws_db_instance.default.db_name },
        { name = "DB_USERNAME", value = var.db_username },
        { name = "DB_PASSWORD", value = var.db_password },
        { name = "REDIS_HOST", value = aws_elasticache_cluster.default.cache_nodes[0].address },
      ]
    }
  ])
}

# Run the migration task on initial apply
resource "null_resource" "run_db_migrate" {
  depends_on = [
    aws_db_instance.default,
    aws_ecs_cluster.main,
    aws_ecs_task_definition.db_migrate
  ]

  triggers = {
    # Run only when the task definition changes (or on fresh install)
    # To force re-run, taint this resource: terraform taint null_resource.run_db_migrate
    task_def_arn = aws_ecs_task_definition.db_migrate.arn
  }

  provisioner "local-exec" {
    command = <<EOT
      aws ecs run-task \
        --cluster ${aws_ecs_cluster.main.name} \
        --task-definition ${aws_ecs_task_definition.db_migrate.family} \
        --launch-type FARGATE \
        --network-configuration "awsvpcConfiguration={subnets=[${join(",", aws_subnet.private[*].id)}],securityGroups=[${aws_security_group.ecs_tasks.id}],assignPublicIp=DISABLED}" \
        --count 1
    EOT
  }
}
