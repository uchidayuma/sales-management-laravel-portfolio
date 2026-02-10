---
name: playwright-skill
description: Laravel Sail環境用のPlaywrightブラウザ自動化スキル。http://localhost:80を固定ベースURLとし、routes/web.phpとresources/views/を事前解析してから正確なE2Eテストを作成します。テスト成功後はtests/e2e/に自動保存。ページのテスト、フォームの入力、スクリーンショットの撮影、レスポンシブデザインの確認、UXの検証、ログインフローのテスト、リンクのチェック、あらゆるブラウザタスクの自動化に使用します。
priority: high
trigger:
    keywords:
        - playwright
        - "E2Eテスト"
        - "自動テスト"
        - "ブラウザテスト"
allowed_tools:
    - shell
    - bash
    - node
    - read
    - write
    - edit
---

**重要 - パス解決:**
このスキルは、さまざまな場所（プラグインシステム、手動インストール、グローバル、またはプロジェクト固有）にインストールできます。コマンドを実行する前に、この SKILL.md ファイルをロードした場所に基づいてスキルディレクトリを特定し、以下のすべてのコマンドでそのパスを使用してください。`$SKILL_DIR` を実際に検出されたパスに置き換えてください。

一般的なインストールパス:

-   プラグインシステム: `~/.claude/plugins/marketplaces/playwright-skill/skills/playwright-skill`
-   手動グローバル: `~/.claude/skills/playwright-skill`
-   プロジェクト固有: `<project>/.claude/skills/playwright-skill`

# Playwright ブラウザ自動化（Laravel Sail 環境専用）

Laravel Sail 環境（http://localhost:80）専用のブラウザ自動化スキル。テスト前に必ずプロジェクト構造（routes/web.php、resources/views/）を事前解析し、実際の DOM 構造に基づいた正確な E2E テストを作成します。テスト成功後は tests/e2e/に自動保存します。

**重要ワークフロー - 以下の手順に沿って実行してください:**

## 🔴 Laravel Sail 環境専用ルール（最優先事項）

### 0. **前提条件**

-   ログイン情報は、 README.md から取得してください。

### 1. **ホストの固定 (Laravel Sail 環境)**

-   テストのベース URL は **常に `http://localhost:80`** を使用してください
-   Laravel Sail 環境で動作するため、開発サーバーの検出は**不要**です
-   すべてのスクリプトで `const TARGET_URL = "http://localhost:80";` を使用してください

### 2. **事前解析の徹底（テスト計画前の必須ステップ）**

テストコードを書く前に、**必ず以下の順序で**プロジェクトの構造を解析してください：

**ステップ 2.1: ルーティングの確認**

```bash
# プロジェクトルートの routes/web.php を読み取る
Read ../../../routes/web.php
```

-   利用可能なパス（例: `/login`, `/dashboard`, `/products`）を特定
-   ルート名、ミドルウェア、コントローラーアクションを確認

**ステップ 2.2: ビューファイルの解析**

```bash
# 関連する Blade ファイルを読み取る
Read ../../../resources/views/[対象ファイル].blade.php
```

-   フォーム要素の `id`, `name`, `class` 属性を特定
-   ボタン、入力フィールド、セレクタを確認
-   CSRF トークンフィールドの存在を確認

**ステップ 2.3: テスト計画の策定**

-   事前解析で得た情報を基に、正確なセレクタとパスを使用したテストコードを作成
-   推測や仮定を避け、実際の DOM 構造に基づいたテストを実装

### 3. **テストコードの永続化**

テストが成功した場合、以下の手順で永続的に保存してください：

**ステップ 3.1: テストディレクトリの確認**

```bash
# tests/e2e/ ディレクトリの存在を確認（なければ作成）
Glob tests/e2e/**/*.js
```

**ステップ 3.2: テストファイルの保存**

```bash
# /tmp から tests/e2e/ に適切な名前でコピー
# 例: login_test.js, product_create_test.js, dashboard_navigation_test.js
Write ../../../tests/e2e/[適切な名前].js
```

-   ファイル名は機能を明確に表す命名規則を使用（例: `login_test.js`, `form_submission_test.js`）
-   テストが成功したことをユーザーに報告し、保存先を明示

## 🟢 一般的なワークフロー

4. **/tmp にスクリプトを書き込む** - テストファイルをスキルディレクトリに書き込まず、常に `/tmp/playwright-test-*.js` を使用してください

5. **デフォルトで可視ブラウザを使用** - ユーザーが明示的にヘッドレスモードを要求しない限り、常に `headless: false` を使用してください

## 仕組み（Laravel Sail 環境）

1. テスト/自動化したい内容を記述します
2. **プロジェクト構造を事前解析します**（routes/web.php と resources/views/ を読み取り）
3. カスタムの Playwright コードを `/tmp/playwright-test-*.js` に書き込みます（プロジェクトを汚しません）
4. `cd $SKILL_DIR && node run.js /tmp/playwright-test-*.js` を介して実行します（ベース URL: `http://localhost:80`）
5. 結果はリアルタイムで表示され、デバッグのためにブラウザウィンドウが表示されます
6. **テストが成功したら、`tests/e2e/` に永続的に保存します**
7. テストファイルは OS によって `/tmp` から自動的にクリーンアップされます

## セットアップ (初回のみ)

```bash
cd $SKILL_DIR
npm run setup
```

これにより、Playwright と Chromium ブラウザがインストールされます。初回のみ必要です。

## 実行パターン（Laravel Sail 環境）

**ステップ 1: プロジェクト構造を事前解析（最優先）**

```bash
# ルーティングを確認
Read ../../../routes/web.php

# 関連するビューファイルを確認
Read ../../../resources/views/[対象ファイル].blade.php
```

**ステップ 2: 固定 URL でテストスクリプトを /tmp に書き込みます**

```javascript
// /tmp/playwright-test-page.js
const { chromium } = require("playwright");

// Laravel Sail環境の固定URL
const TARGET_URL = "http://localhost:80";

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

**ステップ 4: テスト成功後、永続的に保存します**

```bash
# tests/e2e/ ディレクトリに適切な名前で保存
Write ../../../tests/e2e/[機能名]_test.js
```

## 一般的なパターン

### ページのテスト (複数ビューポート)

```javascript
// /tmp/playwright-test-responsive.js
const { chromium } = require("playwright");

const TARGET_URL = "http://localhost:80"; // Laravel Sail環境固定

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

const TARGET_URL = "http://localhost:80"; // Laravel Sail環境固定

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    // 事前解析で確認したパスとセレクタを使用
    await page.goto(`${TARGET_URL}/login`);

    // resources/views/auth/login.blade.php から特定した実際のセレクタを使用
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

const TARGET_URL = "http://localhost:80"; // Laravel Sail環境固定

(async () => {
    const browser = await chromium.launch({ headless: false, slowMo: 50 });
    const page = await browser.newPage();

    // 事前に routes/web.php で /contact パスを確認
    // 事前に resources/views/contact.blade.php でフォーム要素を確認
    await page.goto(`${TARGET_URL}/contact`);

    // 実際のBlade テンプレートで確認したname属性を使用
    await page.fill('input[name="name"]', "John Doe");
    await page.fill('input[name="email"]', "john@example.com");
    await page.fill('textarea[name="message"]', "Test message");
    await page.click('button[type="submit"]');

    // 送信を確認（Bladeファイルで確認した成功メッセージのセレクタを使用）
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

    await page.goto("http://localhost:80");

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

const TARGET_URL = "http://localhost:80"; // Laravel Sail環境固定

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
# Take a quick screenshot (Laravel Sail環境)
cd $SKILL_DIR && node run.js "
const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();
await page.goto('http://localhost:80');
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

## ヒント（Laravel Sail 環境）

-   **🔴 最重要：事前解析を最初に実行** - テストコードを書く前に、常に `routes/web.php` と関連する `resources/views/` ファイルを読み取ってください
-   **🔴 固定 URL** - 常に `const TARGET_URL = "http://localhost:80";` を使用してください（Laravel Sail 環境）
-   **🔴 テストの永続化** - テスト成功後は必ず `tests/e2e/` に適切な名前で保存してください
-   **カスタムヘッダー** - `PW_HEADER_NAME`/`PW_HEADER_VALUE` 環境変数を使用してバックエンドへの自動化トラフィックを識別します
-   **/tmp をテストファイルに使用** - `/tmp/playwright-test-*.js` に書き込み、スキルディレクトリには書き込まない（成功後に tests/e2e/ に保存）
-   **正確なセレクタ** - 事前解析で確認した実際の `id`、`name`、`class` 属性を使用し、推測を避ける
-   **CSRF トークン** - Laravel のフォームには CSRF トークンが含まれていることを確認
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

## 使用例（Laravel Sail 環境）

```
ユーザー：「マーケティングページの見た目がいいか確認してください」

Claude：複数のビューポートでマーケティングページをテストします。まずプロジェクト構造を確認させてください...

[実行：Read routes/web.php]
[出力：/marketing ルートを確認]

[実行：Read resources/views/marketing.blade.php]
[出力：ページ構造とセレクタを確認]

Laravel Sail環境（http://localhost:80）でテストを実行します。

[固定URLでカスタム自動化スクリプトを /tmp/playwright-test-marketing.js に書き込む]
[実行：cd $SKILL_DIR && node run.js /tmp/playwright-test-marketing.js]
[/tmp/ からのスクリーンショット付きの結果を表示]

✅ テスト成功！tests/e2e/marketing_test.js に保存しました。
```

```
ユーザー：「ログインが正しくリダイレクトされるか確認してください」

Claude：ログインフローをテストします。まずプロジェクト構造を確認させてください...

[実行：Read routes/web.php]
[出力：/login ルートとリダイレクト先を確認]

[実行：Read resources/views/auth/login.blade.php]
[出力：フォーム要素のname属性、id、セレクタを確認]

実際のDOM構造に基づいてテストを作成します（http://localhost:80）。

[ログイン自動化を /tmp/playwright-test-login.js に書き込む]
[実行：cd $SKILL_DIR && node run.js /tmp/playwright-test-login.js]
[レポート：✅ ログイン成功、/dashboard にリダイレクト]

✅ テスト成功！tests/e2e/login_test.js に保存しました。
```

## 注記（Laravel Sail 環境）

-   各自動化はあなたの特定のリクエストのためにカスタム記述されます
-   事前構築されたスクリプトに限定されません - あらゆるブラウザタスク可能
-   **Laravel Sail 環境では常に `http://localhost:80` を使用**（開発サーバー検出は不要）
-   **テスト前に必ず `routes/web.php` と `resources/views/` を解析**してセレクタを確認
-   テストスクリプトは `/tmp` に書き込まれ、成功後は `tests/e2e/` に永続化
-   `run.js` を介した適切なモジュール解決により、コードは確実に実行されます
-   段階的開示 - 高度な機能が必要な場合のみ API_REFERENCE.md が読み込まれます
