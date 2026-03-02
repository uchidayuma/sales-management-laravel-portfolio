#!/bin/sh
# =============================================================================
# PHP-FPM コンテナ エントリポイント
#
# 【ECS Fargate ボリューム権限の修正】
# ECS Fargate は名前付きボリューム（host_path なし）を空の root:root ディレクトリとして作成する。
# /var/www/storage をボリュームマウントすると、イメージ内の www-data:www-data 所有権が
# root:root に上書きされ、PHP-FPM の www-data ワーカーが書き込めなくなる。
#
# PHP-FPM のマスタプロセスは root で起動するため chown が可能。
# CAP_CHOWN は Fargate のデフォルトケーパビリティセットに含まれているため add 不要。
# =============================================================================
set -e

# ECS Fargate ボリュームは空ディレクトリとして作成されるため、
# Laravel が必要とするサブディレクトリ構造を明示的に作成する。
mkdir -p \
  /var/www/storage/framework/views \
  /var/www/storage/framework/cache/data \
  /var/www/storage/framework/sessions \
  /var/www/storage/logs \
  /var/www/storage/app/public

# www-data ワーカーが書き込めるよう所有権を変更する
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

exec "$@"
