docker run -d --name test-fpm -e CACHE_DRIVER=redis -e SESSION_DRIVER=redis -e REDIS_HOST=invalid.host --user 33 --read-only --tmpfs /tmp --tmpfs /var/run --tmpfs /var/www/storage --tmpfs /var/www/bootstrap/cache test-laravel sh -c "mkdir -p /var/www/storage/framework/cache/data /var/www/storage/framework/sessions /var/www/storage/framework/views /var/www/storage/logs /var/www/bootstrap/cache && php-fpm -D && sleep 1000"
sleep 2
docker exec test-fpm sh -c "REQUEST_METHOD=GET SCRIPT_FILENAME=/var/www/public/index.php SCRIPT_NAME=/index.php cgi-fcgi -bind -connect 127.0.0.1:9000" || true
docker rm -f test-fpm
