# ECS Cluster
resource "aws_ecs_cluster" "main" {
  name = "${local.name_prefix}-cluster"

  tags = {
    Name = "${local.name_prefix}-cluster"
  }
}

# CloudWatch Logs
resource "aws_cloudwatch_log_group" "main" {
  name              = "/ecs/${local.name_prefix}"
  retention_in_days = 7

  tags = {
    Name = "${local.name_prefix}-logs"
  }
}

# IAM Roles
## Task Execution Role (Pulling images, writing logs)
resource "aws_iam_role" "ecs_task_execution_role" {
  name = "${local.name_prefix}-ecs-execution-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })
}

resource "aws_iam_role_policy_attachment" "ecs_task_execution_role_policy" {
  role       = aws_iam_role.ecs_task_execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

resource "aws_iam_role_policy" "ecs_execution_secrets_policy" {
  name = "${local.name_prefix}-ecs-execution-secrets-policy"
  role = aws_iam_role.ecs_task_execution_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = ["secretsmanager:GetSecretValue"]
        Resource = [
          aws_secretsmanager_secret.db_password.arn,
          aws_secretsmanager_secret.app_key.arn,
        ]
      }
    ]
  })
}

# APP_KEY を Secrets Manager で管理する
# laravel の暗号化（セッション・Cookie 等）に必須のキー
resource "aws_secretsmanager_secret" "app_key" {
  name                    = "${local.name_prefix}/app-key"
  recovery_window_in_days = 0
}

## Task Role (App permissions to S3/SSM etc)
resource "aws_iam_role" "ecs_task_role" {
  name = "${local.name_prefix}-ecs-task-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })
}

# IAM Policy for Task Role (S3 & SSM)
resource "aws_iam_role_policy" "ecs_task_role_policy" {
  name = "${local.name_prefix}-ecs-task-policy"
  role = aws_iam_role.ecs_task_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "s3:PutObject",
          "s3:GetObject",
          "s3:DeleteObject",
          "s3:ListBucket"
        ]
        Resource = [
          aws_s3_bucket.uploads.arn,
          "${aws_s3_bucket.uploads.arn}/*"
        ]
      },
      {
        Effect = "Allow"
        Action = [
          "ssm:GetParameters",
          "ssm:GetParameter"
        ]
        Resource = "arn:aws:ssm:${var.region}:*:parameter/${var.project_name}/${var.environment}/*"
      }
    ]
  })
}

# Task Definition（サイドカーパターン: app + web の2コンテナ構成）
resource "aws_ecs_task_definition" "app" {
  family                   = "${local.name_prefix}-app"
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

    # -----------------------------------------------------------------------
    # app コンテナ: PHP-FPM (PHP 8.4)
    # ・Laravelのソースコードを保持し、PHPリクエストを処理する
    # ・ポート9000でリッスンし、webコンテナ(Nginx)からのみ接続を受け付ける
    # ・ALBには直接公開しない（internal-only）
    # -----------------------------------------------------------------------
    {
      name      = "app"
      image     = var.app_image
      essential = true

      portMappings = [
        {
          # PHP-FPM のポート: タスク内部でのみ使用（ALBには公開しない）
          containerPort = 9000
          hostPort      = 9000
          protocol      = "tcp"
        }
      ]

      # 【読み取り専用ルートファイルシステム】
      # コンテナのルートファイルシステムを読み取り専用にする。
      # 攻撃者がコンテナ内でコードを実行できたとしても、マルウェアや
      # バックドアをファイルシステムに書き込むことができなくなる。
      # 書き込みが必要なパスは mountPoints で tmpfs を明示的にマウントする。
      readonlyRootFilesystem = true

      # 【Linux ケーパビリティの制限】
      # PHP-FPM はマスタを root で起動し、pool 設定に従いワーカーを www-data に setuid する。
      # この setuid には CAP_SETUID / CAP_SETGID が必要であり、drop = ["ALL"] は使用できない。
      # Fargate のデフォルトケーパビリティセットは既に危険な権限（SYS_ADMIN 等）を含まないため、
      # 明示的に危険なケーパビリティのみを個別に drop することで同等のリスク低減を実現する。
      #
      # 【init プロセスの有効化】
      # initProcessEnabled = true により ECS が軽量な init プロセス（tini）を
      # PID 1 として起動する。
      # 通常 PHP-FPM が PID 1 になると SIGTERM を正しくハンドリングできず
      # ゾンビプロセスが蓄積することがある。init プロセスを挟むことで
      # シグナル伝播とゾンビ刈り取りを適切に処理する。
      linuxParameters = {
        initProcessEnabled = true
        capabilities = {
          add  = []
          drop = ["MKNOD", "AUDIT_WRITE", "SETFCAP", "NET_RAW"]
        }
        tmpfs = [
          { containerPath = "/tmp", size = 64 }
        ]
      }

      # 【ヘルスチェック】
      # ECS がコンテナの死活を定期的に確認する仕組み。
      # unhealthy になったコンテナは自動で再起動され、サービスの可用性を維持する。
      # php-fpm の ping エンドポイントで PHP-FPM プロセス自体が応答できるかを確認する。
      # startPeriod: コンテナ起動直後の猶予時間（この間は unhealthy でも再起動しない）
      healthCheck = {
        command     = ["CMD-SHELL", "SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1"]
        interval    = 30
        timeout     = 5
        retries     = 3
        startPeriod = 60
      }

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          awslogs-group         = aws_cloudwatch_log_group.main.name
          awslogs-region        = var.region
          awslogs-stream-prefix = "app"
        }
      }
      environment = [
        { name = "APP_ENV", value = var.environment },
        { name = "APP_DEBUG", value = "true" },
        # セッション・キャッシュを Redis に向けることで storage/ への書き込みを排除する
        { name = "SESSION_DRIVER", value = "redis" },
        { name = "CACHE_DRIVER", value = "redis" },
        # ログを stderr に向けることで CloudWatch に集約し storage/logs への書き込みを排除する
        { name = "LOG_CHANNEL", value = "stderr" },
        { name = "DB_HOST", value = aws_db_instance.default.address },
        { name = "DB_DATABASE", value = aws_db_instance.default.db_name },
        { name = "DB_USERNAME", value = var.db_username },
        { name = "REDIS_HOST", value = aws_elasticache_cluster.default.cache_nodes[0].address },
      ]
      secrets = [
        { name = "DB_PASSWORD", valueFrom = aws_secretsmanager_secret.db_password.arn },
        # APP_KEY は暗号化に必須。Secrets Manager で管理し平文を環境変数に残さない。
        { name = "APP_KEY", valueFrom = aws_secretsmanager_secret.app_key.arn },
      ]

      mountPoints = []
    },
    # -----------------------------------------------------------------------
    # web コンテナ: Nginx
    # ・静的ファイル（CSS/JS/画像等）を直接配信する
    # ・PHPリクエストをlocalhost:9000のappコンテナへ転送する
    # ・awsvpcモードでは同一タスク内コンテナがネットワーク名前空間を共有するため
    #   127.0.0.1でappコンテナに接続できる
    # ・ポート80でALBからのリクエストを受け付ける（ALBターゲットはwebコンテナ）
    # -----------------------------------------------------------------------
    {
      name      = "web"
      image     = var.web_image
      essential = true
      portMappings = [
        {
          # Nginxのポート: ALBがこのポートにリクエストを転送する
          containerPort = var.app_port
          hostPort      = var.app_port
          protocol      = "tcp"
        }
      ]

      # 【読み取り専用ルートファイルシステム】
      # app コンテナと同様の理由で Nginx コンテナも読み取り専用にする。
      # Nginx は静的ファイルの配信と PHP-FPM へのプロキシのみを担うため、
      # ルートファイルシステムへの書き込みは本来不要。
      readonlyRootFilesystem = true

      # 【Linux ケーパビリティの制限】
      # Fargate では add できるケーパビリティが SYS_PTRACE のみに制限されている。
      # CHOWN は add 不可だが、Fargate のデフォルトケーパビリティセットに含まれている。
      # Nginx の entrypoint は /var/cache/nginx 配下を nginx ユーザー（uid 101）に
      # chown するため CAP_CHOWN が必要であり、drop = ["ALL"] は使用できない。
      # 代わりに危険な特権ケーパビリティのみを個別に drop する。
      # （Fargate はデフォルトで NET_ADMIN / SYS_ADMIN 等の危険なケーパビリティを
      #   持たないため、drop = ["ALL"] と同等のリスク低減効果が得られる）
      #
      # 【init プロセスの有効化】
      # app コンテナと同様にシグナル伝播とゾンビプロセス防止のために有効化する。
      linuxParameters = {
        initProcessEnabled = true
        capabilities = {
          add  = []
          drop = ["MKNOD", "AUDIT_WRITE", "SETFCAP", "NET_RAW"]
        }
        tmpfs = [
          {
            containerPath = "/var/cache/nginx"
            size          = 128
            mountOptions  = ["uid=101", "gid=101"]
          },
          {
            containerPath = "/var/run"
            size          = 64
            mountOptions  = ["uid=101", "gid=101"]
          }
        ]
      }

      # 【ヘルスチェック】
      # Nginx が HTTP リクエストに正常に応答できるかを確認する。
      # app コンテナがヘルシーであることを前提としているため、
      # Nginx 自身のポート(80)への疎通確認で十分。
      healthCheck = {
        command     = ["CMD-SHELL", "curl -f http://localhost:${var.app_port}/ || exit 1"]
        interval    = 30
        timeout     = 5
        retries     = 3
        startPeriod = 60
      }

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          awslogs-group         = aws_cloudwatch_log_group.main.name
          awslogs-region        = var.region
          awslogs-stream-prefix = "web"
        }
      }

      # 【tmpfs マウント: Nginx の一時ファイル領域】
      # readonlyRootFilesystem = true により /var/cache/nginx と /var/run への
      # 書き込みが拒否される。Nginx はプロキシのバッファキャッシュや
      # PID ファイルをこれらのパスに書き込むため、tmpfs として明示的に確保する。
      # tmpfs マウントは linuxParameters の tmpfs で設定するため mountPoints からは削除
      mountPoints = []

      # appコンテナが起動してからwebコンテナを起動する
      dependsOn = [
        {
          containerName = "app"
          condition     = "START"
        }
      ]
    }
  ])

  # 【コンテナイメージの変更を Terraform 管理から除外する】
  # イメージ URI の更新は deploy.sh が担う（タイムスタンプタグによるイミュータブル運用）。
  # この設定がないと terraform apply がタスク定義を `:latest` タグに戻し、
  # ECR のイミュータブルポリシーと組み合わさって CannotPullContainerError が発生する。
  # lifecycle {
  #   ignore_changes = [container_definitions]
  # }
}

# ECS Service
resource "aws_ecs_service" "main" {
  name            = "${local.name_prefix}-service"
  cluster         = aws_ecs_cluster.main.id
  task_definition = aws_ecs_task_definition.app.arn
  desired_count   = var.app_count
  launch_type     = "FARGATE"

  network_configuration {
    security_groups  = [aws_security_group.ecs_tasks.id]
    subnets          = aws_subnet.private[*].id
    assign_public_ip = false
  }

  # ALBのターゲットはwebコンテナ（Nginx）のポート80
  # appコンテナ（PHP-FPM）はタスク内部からのみアクセス可能
  load_balancer {
    target_group_arn = aws_lb_target_group.app.arn
    container_name   = "web"
    container_port   = var.app_port
  }

  # Bulletproof Destruction: Prevent hanging on deletion
  deployment_circuit_breaker {
    enable   = true
    rollback = true
  }

  depends_on = [aws_lb_listener.http, aws_nat_gateway.main, aws_iam_role_policy_attachment.ecs_task_execution_role_policy]

  # 【サービスが参照するタスク定義を Terraform 管理から除外する】
  # deploy.sh が aws ecs update-service でタスク定義を更新するため、
  # terraform apply が古いリビジョン（:latest タグ）に戻すことを防ぐ。
  # lifecycle {
  #   ignore_changes = [task_definition]
  # }
}
