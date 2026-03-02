# =============================================================================
# ステージ1: フロントエンドアセットのビルド
# 【セキュリティ上の意味】
#   Node.js / npm / node_modules を最終イメージに含めない。
#   ビルドツールを分離することで脆弱性の多い依存関係のアタックサーフェスを削減する。
# =============================================================================
FROM node:18-alpine AS node-build

WORKDIR /app

# package.json / package-lock.json を先にコピーしてキャッシュを活用する
COPY package.json package-lock.json ./

# npm ci: package-lock.json を厳密に使用し、再現性・一貫性のあるビルドを保証する
RUN npm ci

# Webpack が Laravel のソースファイルを参照するためにアプリ全体をコピーする
COPY . .

# Laravel Mix でプロダクション用アセットをビルド（minification込み）
RUN npm run prod

# =============================================================================
# ステージ2: 本番用 Nginx イメージ（最終成果物）
# 【セキュリティ上の意味】
#   PHP ランタイム・Composer・Node.js を一切含まない純粋な静的ファイル配信コンテナ。
#   責務を「リクエスト受付・静的ファイル配信・PHP-FPMへのプロキシ」に限定することで
#   侵害時の影響範囲を最小化する（コンテナ間の権限分離）。
#   Alpine Linux ベースにより OS レベルのアタックサーフェスをさらに削減する。
# =============================================================================
FROM nginx:alpine AS production

# デフォルトのNginx設定を削除する。
# 不要なデフォルト設定が残ると意図しないエンドポイントが露出するリスクがある。
RUN rm /etc/nginx/conf.d/default.conf

# プロジェクト独自のNginx設定をコピーする
# PHP-FPMへの転送・静的ファイル配信・セキュリティヘッダー等を定義する
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# 静的ファイル配信用のドキュメントルートを作成する
RUN mkdir -p /var/www/public

# ステージ1(node-build)からビルド済みの静的アセットのみをコピーする。
# node_modules・npm・webpack等のビルドツールは最終イメージに含まれない。
COPY --from=node-build /app/public /var/www/public

# 【最小権限の原則】静的ファイルのオーナーをnginxユーザーに変更する。
# Nginxのワーカープロセスはnginx.confの"user nginx;"設定により非rootで動作する。
# ファイル権限を755とすることで他ユーザーからの書き込みを禁止する。
RUN chown -R nginx:nginx /var/www/public \
    && chmod -R 755 /var/www/public

# read-only ファイルシステム環境では nginx 公式の entrypoint スクリプトが
# 設定ファイルを動的に書き換えようとして Permission Denied になるため、全て削除する。
RUN rm -rf /docker-entrypoint.d/*

# 【非rootユーザーでの実行について】
# ルートレスコンテナとして実行するため、1024番以上のポート(8080)を使用し、
# コンテナプロセス全体を nginx ユーザーとして起動します。
USER nginx

EXPOSE 8080

CMD ["nginx", "-g", "daemon off;"]
