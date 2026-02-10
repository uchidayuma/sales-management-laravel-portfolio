---
name: playwright-skill
description: Playwrightによる完全なブラウザ自動化。開発サーバーを自動検出し、クリーンなテストスクリプトを/tmpに書き込みます。ページのテスト、フォームの入力、スクリーンショットの撮影、レスポンシブデザインの確認、UXの検証、ログインフローのテスト、リンクのチェック、あらゆるブラウザタスクの自動化に使用します。ユーザーがウェブサイトのテスト、ブラウザ操作の自動化、ウェブ機能の検証、またはあらゆるブラウザベースのテストを実行したい場合に使用してください。
---

**重要 - パス解決:**
このスキルは、さまざまな場所（プラグインシステム、手動インストール、グローバル、またはプロジェクト固有）にインストールできます。コマンドを実行する前に、この SKILL.md ファイルをロードした場所に基づいてスキルディレクトリを特定し、以下のすべてのコマンドでそのパスを使用してください。`$SKILL_DIR` を実際に検出されたパスに置き換えてください。

一般的なインストールパス:

-   プラグインシステム: `~/.claude/plugins/marketplaces/playwright-skill/skills/playwright-skill`
-   手動グローバル: `~/.claude/skills/playwright-skill`
-   プロジェクト固有: `<project>/.claude/skills/playwright-skill`

# Playwright ブラウザ自動化

汎用ブラウザ自動化スキル。リクエストされたあらゆる自動化タスクに対し、カスタムの Playwright コードを記述し、ユニバーサルエグゼキュータを介して実行します。

**重要ワークフロー - 以下の手順に沿って実行してください:**

1. **開発サーバーの自動検出** - ローカルホストのテストでは、常にサーバー検出を最初に実行してください:

    ```bash
    cd $SKILL_DIR && node -e "require('./lib/helpers').detectDevServers().then(servers => console.log(JSON.stringify(servers)))"
    ```

    - **サーバーが 1 つ見つかった場合**: 自動的に使用し、ユーザーに通知します
    - **複数のサーバーが見つかった場合**: ユーザーにテストするサーバーを尋ねます
    - **サーバーが見つからない場合**: URL を尋ねるか、開発サーバーの起動を支援することを申し出ます

2. **/tmp にスクリプトを書き込む** - テストファイルをスキルディレクトリに書き込まず、常に `/tmp/playwright-test-*.js` を使用してください

3. **デフォルトで可視ブラウザを使用** - ユーザーが明示的にヘッドレスモードを要求しない限り、常に `headless: false` を使用してください

4. **URL のパラメータ化** - 常にスクリプトの先頭で環境変数または定数を介して URL を設定可能にしてください

## 仕組み

1. テスト/自動化したい内容を記述します
2. 実行中の開発サーバーを自動検出します（外部サイトをテストする場合は URL を尋ねます）
3. カスタムの Playwright コードを `/tmp/playwright-test-*.js` に書き込みます（プロジェクトを汚しません）
4. `cd $SKILL_DIR && node run.js /tmp/playwright-test-*.js` を介して実行します
5. 結果はリアルタイムで表示され、デバッグのためにブラウザウィンドウが表示されます
6. テストファイルは OS によって `/tmp` から自動的にクリーンアップされます

## セットアップ (初回のみ)

```bash
cd $SKILL_DIR
npm run setup
```

これにより、Playwright と Chromium ブラウザがインストールされます。初回のみ必要です。

## 実行パターン

**ステップ 1: 開発サーバーを検出します (ローカルホストのテスト用)**

```bash
cd $SKILL_DIR && node -e "require('./lib/helpers').detectDevServers().then(s => console.log(JSON.stringify(s)))"
```

**ステップ 2: URL パラメータを含むテストスクリプトを /tmp に書き込みます**

```javascript
// /tmp/playwright-test-page.js
const { chromium } = require("playwright");

// パラメータ化されたURL (検出されたもの、またはユーザーが提供したもの)
const TARGET_URL = "http://localhost:3001"; // <-- 自動検出、またはユーザーから

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto(TARGET_URL);
    console.log("Page loaded:", await page.title());

    await page.screenshot({ path: "/tmp/screenshot.png", fullPage: true });
    console.log("📸 Screenshot saved to /tmp/screenshot.png");

    await browser.close();
})();
```

**ステップ 3: スキルディレクトリから実行します**

```bash
cd $SKILL_DIR && node run.js /tmp/playwright-test-page.js
```

## 一般的なパターン

### ページのテスト (複数ビューポート)

```javascript
// /tmp/playwright-test-responsive.js
const { chromium } = require("playwright");

const TARGET_URL = "http://localhost:3001"; // 自動検出

(async () => {
    const browser = await chromium.launch({ headless: false, slowMo: 100 });
    const page = await browser.newPage();

    // デスクトップテスト
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto(TARGET_URL);
    console.log("Desktop - Title:", await page.title());
    await page.screenshot({ path: "/tmp/desktop.png", fullPage: true });

    // モバイルテスト
    await page.setViewportSize({ width: 375, height: 667 });
    await page.screenshot({ path: "/tmp/mobile.png", fullPage: true });

    await browser.close();
})();
```

### ログインフローのテスト

```javascript
// /tmp/playwright-test-login.js
const { chromium } = require("playwright");

const TARGET_URL = "http://localhost:3001"; // 自動検出

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto(`${TARGET_URL}/login`);

    await page.fill('input[name="email"]', "test@example.com");
    await page.fill('input[name="password"]', "password123");
    await page.click('button[type="submit"]');

    // リダイレクトを待機
    await page.waitForURL("**/dashboard");
    console.log("✅ ログイン成功、ダッシュボードにリダイレクトされました");

    await browser.close();
})();
```

### フォームの入力と送信

```javascript
// /tmp/playwright-test-form.js
const { chromium } = require("playwright");

const TARGET_URL = "http://localhost:3001"; // 自動検出

(async () => {
    const browser = await chromium.launch({ headless: false, slowMo: 50 });
    const page = await browser.newPage();

    await page.goto(`${TARGET_URL}/contact`);

    await page.fill('input[name="name"]', "John Doe");
    await page.fill('input[name="email"]', "john@example.com");
    await page.fill('textarea[name="message"]', "Test message");
    await page.click('button[type="submit"]');

    // 送信を確認
    await page.waitForSelector(".success-message");
    console.log("✅ フォームが正常に送信されました");

    await browser.close();
})();
```

### 壊れたリンクのチェック

```javascript
const { chromium } = require("playwright");

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    await page.goto("http://localhost:3000");

    const links = await page.locator('a[href^="http"]').all();
    const results = { working: 0, broken: [] };

    for (const link of links) {
        const href = await link.getAttribute("href");
        try {
            const response = await page.request.head(href);
            if (response.ok()) {
                results.working++;
            } else {
                results.broken.push({ url: href, status: response.status() });
            }
        } catch (e) {
            results.broken.push({ url: href, error: e.message });
        }
    }

    console.log(`✅ 有効なリンク: ${results.working}`);
    console.log(`❌ 壊れたリンク:`, results.broken);

    await browser.close();
})();
```

### エラー処理付きスクリーンショットの取得

```javascript
const { chromium } = require("playwright");

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        await page.goto("http://localhost:3000", {
            waitUntil: "networkidle",
            timeout: 10000,
        });

        await page.screenshot({
            path: "/tmp/screenshot.png",
            fullPage: true,
        });

        console.log("📸 Screenshot saved to /tmp/screenshot.png");
    } catch (error) {
        console.error("❌ エラー:", error.message);
    } finally {
        await browser.close();
    }
})();
```

### レスポンシブデザインのテスト

```javascript
// /tmp/playwright-test-responsive-full.js
const { chromium } = require("playwright");

const TARGET_URL = "http://localhost:3001"; // 自動検出

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    const viewports = [
        { name: "Desktop", width: 1920, height: 1080 },
        { name: "Tablet", width: 768, height: 1024 },
        { name: "Mobile", width: 375, height: 667 },
    ];

    for (const viewport of viewports) {
        console.log(
            `Testing ${viewport.name} (${viewport.width}x${viewport.height})`
        );

        await page.setViewportSize({
            width: viewport.width,
            height: viewport.height,
        });

        await page.goto(TARGET_URL);
        await page.waitForTimeout(1000);

        await page.screenshot({
            path: `/tmp/${viewport.name.toLowerCase()}.png`,
            fullPage: true,
        });
    }

    console.log("✅ すべてのビューポートがテストされました");
    await browser.close();
})();
```

## インライン実行（シンプルなタスク）

ワンオフのクイックタスク用に、ファイルを作成せずにコードをインラインで実行できます：

```bash
# Take a quick screenshot
cd $SKILL_DIR && node run.js "
const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();
await page.goto('http://localhost:3001');
await page.screenshot({ path: '/tmp/quick-screenshot.png', fullPage: true });
console.log('Screenshot saved');
await browser.close();
"
```

**インラインとファイルの使い分け：**

-   **インライン**: ワンオフのクイックタスク（スクリーンショット、要素の存在確認、ページタイトルの取得）
-   **ファイル**: 複雑なテスト、レスポンシブデザイン確認、ユーザーが再実行したい場合

## 利用可能なヘルパー

`lib/helpers.js` のオプションユーティリティ関数：

```javascript
const helpers = require("./lib/helpers");

// Detect running dev servers (CRITICAL - use this first!)
const servers = await helpers.detectDevServers();
console.log("Found servers:", servers);

// Safe click with retry
await helpers.safeClick(page, "button.submit", { retries: 3 });

// Safe type with clear
await helpers.safeType(page, "#username", "testuser");

// Take timestamped screenshot
await helpers.takeScreenshot(page, "test-result");

// Handle cookie banners
await helpers.handleCookieBanner(page);

// Extract table data
const data = await helpers.extractTableData(page, "table.results");
```

完全なリストについては `lib/helpers.js` を参照してください。

## カスタム HTTP ヘッダー

環境変数を介してすべての HTTP リクエストのカスタムヘッダーを設定します。以下の場合に便利です：

-   バックエンドへの自動化されたトラフィックを識別する
-   LLM 最適化レスポンスを取得する（例：スタイル化された HTML ではなくプレーンテキストエラー）
-   認証トークンをグローバルに追加する

### 設定

**単一ヘッダー（一般的な場合）：**

```bash
PW_HEADER_NAME=X-Automated-By PW_HEADER_VALUE=playwright-skill \
  cd $SKILL_DIR && node run.js /tmp/my-script.js
```

**複数ヘッダー（JSON 形式）：**

```bash
PW_EXTRA_HEADERS='{"X-Automated-By":"playwright-skill","X-Debug":"true"}' \
  cd $SKILL_DIR && node run.js /tmp/my-script.js
```

### 仕組み

`helpers.createContext()` を使用する場合、ヘッダーは自動的に適用されます：

```javascript
const context = await helpers.createContext(browser);
const page = await context.newPage();
// All requests from this page include your custom headers
```

raw Playwright API を使用するスクリプトの場合、インジェクションされた `getContextOptionsWithHeaders()` を使用します：

```javascript
const context = await browser.newContext(
    getContextOptionsWithHeaders({ viewport: { width: 1920, height: 1080 } })
);
```

## 高度な使用方法

包括的な Playwright API ドキュメントについては [API_REFERENCE.md](API_REFERENCE.md) を参照してください：

-   セレクタとロケータのベストプラクティス
-   ネットワークインターセプション＆API モッキング
-   認証とセッション管理
-   ビジュアルリグレッションテスト
-   モバイルデバイスエミュレーション
-   パフォーマンステスト
-   デバッグ技法
-   CI/CD 統合

## ヒント

-   **重要：サーバーを最初に検出** - localhost テスト用のテストコードを書く前に、常に `detectDevServers()` を実行してください
-   **カスタムヘッダー** - `PW_HEADER_NAME`/`PW_HEADER_VALUE` 環境変数を使用してバックエンドへの自動化トラフィックを識別します
-   **/tmp をテストファイルに使用** - `/tmp/playwright-test-*.js` に書き込み、スキルディレクトリやユーザーのプロジェクトには書き込まない
-   **URL をパラメータ化** - 検出/提供された URL をすべてのスクリプトの最上部の `TARGET_URL` 定数に入力します
-   **デフォルト：ブラウザを表示** - ユーザーが明示的にヘッドレスモードを要求しない限り、常に `headless: false` を使用
-   **ヘッドレスモード** - ユーザーが明確に「headless」または「background」実行を要求した場合のみ `headless: true` を使用
-   **減速：** `slowMo: 100` を使用してアクションを見やすく、フォローしやすくします
-   **待機戦略：** 固定タイムアウトの代わりに `waitForURL`、`waitForSelector`、`waitForLoadState` を使用
-   **エラー処理：** 堅牢な自動化のために try-catch を常に使用
-   **コンソール出力：** `console.log()` を使用してプログレスを追跡し、何が起こっているかを表示

## トラブルシューティング

**Playwright がインストールされていない：**

```bash
cd $SKILL_DIR && npm run setup
```

**モジュールが見つからない：**
`run.js` ラッパーを介してスキルディレクトリから実行していることを確認

**ブラウザが開かない：**
`headless: false` をチェックし、ディスプレイが利用可能であることを確認

**要素が見つからない：**
wait を追加：`await page.waitForSelector('.element', { timeout: 10000 })`

## 使用例

```
ユーザー：「マーケティングページの見た目がいいか確認してください」

Claude：複数のビューポートでマーケティングページをテストします。まず実行中のサーバーを検出させてください...
[実行：detectDevServers()]
[出力：Found server on port 3001]
あなたの開発サーバーが http://localhost:3001 で実行されているのを見つけました

[URLをパラメータ化してカスタム自動化スクリプトを /tmp/playwright-test-marketing.js に書き込む]
[実行：cd $SKILL_DIR && node run.js /tmp/playwright-test-marketing.js]
[/tmp/ からのスクリーンショット付きの結果を表示]
```

```
ユーザー：「ログインが正しくリダイレクトされるか確認してください」

Claude：ログインフローをテストします。まず実行中のサーバーを確認させてください...
[実行：detectDevServers()]
[出力：Found servers on ports 3000 and 3001]
2つの開発サーバーを見つけました。どちらをテストしますか？
- http://localhost:3000
- http://localhost:3001

ユーザー：「3001を使ってください」

[ログイン自動化を /tmp/playwright-test-login.js に書き込む]
[実行：cd $SKILL_DIR && node run.js /tmp/playwright-test-login.js]
[レポート：✅ ログイン成功、/dashboard にリダイレクト]
```

## 注記

-   各自動化はあなたの特定のリクエストのためにカスタム記述されます
-   事前構築されたスクリプトに限定されません - あらゆるブラウザタスク可能
-   実行中の開発サーバーを自動検出してハードコードされた URL を排除
-   テストスクリプトは自動クリーンアップ用に `/tmp` に書き込まれます（雑然とした状態なし）
-   `run.js` を介した適切なモジュール解決により、コードは確実に実行されます
-   段階的開示 - 高度な機能が必要な場合のみ API_REFERENCE.md が読み込まれます
