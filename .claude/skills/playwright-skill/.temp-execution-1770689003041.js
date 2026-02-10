const { chromium } = require("playwright");

// Laravel Sail環境の固定URL
const TARGET_URL = "http://localhost:80";

// テストユーザー情報（READMEから取得）
const TEST_USER = {
    email: "admin@example.com",
    password: "password",
};

(async () => {
    const browser = await chromium.launch({ headless: false, slowMo: 100 });
    const page = await browser.newPage();

    try {
        console.log("🚀 顧客登録E2Eテストを開始します...");

        // ログインページに移動
        await page.goto(`${TARGET_URL}/login`);
        console.log("✅ ログインページを開きました");

        // ログイン情報を入力
        await page.fill('input[dusk="login-mail"]', TEST_USER.email);
        await page.fill('input[dusk="login-password"]', TEST_USER.password);
        console.log(`✅ ログイン情報を入力: ${TEST_USER.email}`);

        // ログインボタンをクリック
        await page.click('button#submit');
        console.log("✅ ログインボタンをクリック");

        // ダッシュボードへのリダイレクトを待機
        await page.waitForLoadState("networkidle");
        await page.waitForTimeout(2000);
        console.log("✅ ログイン成功");

        // 顧客登録フォームページに移動
        await page.goto(`${TARGET_URL}/contact/new`);
        console.log("✅ 顧客登録フォームページを開きました");

        // ページが完全に読み込まれるまで待機
        await page.waitForLoadState("networkidle");
        await page.waitForSelector('input[dusk="type2"]', { timeout: 10000 });

        // お問い合わせ種別を選択（個人/図面見積もり: type2）
        await page.click('input[dusk="type2"]');
        console.log("✅ お問い合わせ種別: 個人/図面見積もりを選択");

        // フォームが表示されるのを待つ
        await page.waitForTimeout(1500);
        await page.waitForSelector('input[dusk="p2-surname"]', { timeout: 5000 });

        // 無料サンプルラジオボタンを選択
        await page.click('input[value="請求済み"]');
        console.log("✅ 無料サンプル: 請求済みを選択");
        await page.waitForTimeout(1000);

        // 顧客情報を入力
        await page.fill('input[dusk="p2-surname"]', "テスト");
        await page.fill('input[dusk="p2-name"]', "太郎");
        console.log("✅ 名前を入力: テスト 太郎");

        // フリガナを入力
        await page.fill('input[dusk="p2-surname_ruby"]', "テスト");
        await page.fill('input[dusk="p2-name_ruby"]', "タロウ");
        console.log("✅ フリガナを入力: テスト タロウ");

        // 郵便番号を入力
        await page.fill('input[dusk="p2-zipcode"]', "1000001");
        console.log("✅ 郵便番号を入力: 1000001");

        // 住所を入力
        await page.waitForTimeout(1000); // 郵便番号自動入力の待機
        await page.fill('input[dusk="p2-pref"]', "東京都");
        await page.fill('input[dusk="p2-city"]', "千代田区");
        await page.fill('input[dusk="p2-street"]', "千代田1-1-1");
        console.log("✅ 住所を入力: 東京都千代田区千代田1-1-1");

        // 電話番号を入力
        await page.fill('input[dusk="p2-tel"]', "03-1234-5678");
        await page.fill('input[dusk="p2-tel2"]', "090-9876-5432");
        console.log("✅ 電話番号を入力: 03-1234-5678, 090-9876-5432");

        // 年代を選択
        await page.selectOption('select[dusk="p2-age"]', "1980");
        console.log("✅ 年代を選択: 1980年代");

        // グランドコンディションをチェック（Duskテストと同じ）
        await page.check('#p2-ground1');
        await page.check('#p2-ground2');
        await page.check('#p2-ground3');
        await page.fill('input[name="c[ground_condition][etc]"]', "その他グランドコンディション");
        console.log("✅ グランドコンディションを選択");

        // 施工サイズを入力
        await page.fill('input[dusk="p2-vertical_size"]', "30");
        await page.fill('input[dusk="p2-horizontal_size"]', "20");
        console.log("✅ 施工サイズを入力: 縦30m x 横20m");

        // 希望商品をチェック（Duskテストと同じ）
        await page.check('#p2-product1');
        await page.check('#p2-product3');
        await page.check('#p2-product4');
        console.log("✅ 希望商品を選択");

        // 使用用途をチェック（Duskテストと同じ）
        await page.check('#p2-use1');
        await page.check('#p2-use3');
        await page.check('#p2-use4');
        await page.fill('input[dusk="p2-use_application-etc"]', "その他花壇の整備");
        console.log("✅ 使用用途を選択");

        // コメントを入力
        await page.fill('textarea[dusk="p2-comment"]', "Playwrightによる自動テストです");
        console.log("✅ コメントを入力");

        // メモを入力（管理者のみ表示、NOT NULL制約あり）
        await page.fill('textarea[dusk="p2-memo"]', "Playwright E2Eテスト用のメモです");
        console.log("✅ メモを入力（管理者用）");

        // スクリーンショットを撮影（送信前）
        await page.screenshot({
            path: "/tmp/customer-register-before-submit.png",
            fullPage: true,
        });
        console.log("📸 送信前のスクリーンショットを保存");

        // 送信ボタンをクリック
        await page.click('button[dusk="contact2-submit"]');
        console.log("✅ 送信ボタンをクリックしました");

        // 送信後のページ遷移を待機（networkidleはタイムアウトする可能性があるので、loadで判定）
        try {
            await page.waitForLoadState("load");
            await page.waitForTimeout(3000);
            // networkidleも試みるが、失敗しても続行
            await page.waitForLoadState("networkidle", { timeout: 10000 }).catch(() => {});
        } catch (e) {
            console.log("⚠️ ページ読み込み待機中にタイムアウト（処理は続行）");
        }

        // エラーメッセージの確認
        const errorAlert = await page.locator('.alert-danger').count();
        if (errorAlert > 0) {
            const errorText = await page.locator('.alert-danger').textContent();
            console.log("❌ バリデーションエラー:");
            console.log(errorText);
        }

        // 送信後のスクリーンショットを撮影
        await page.screenshot({
            path: "/tmp/customer-register-after-submit.png",
            fullPage: true,
        });
        console.log("📸 送信後のスクリーンショットを保存");

        // ページ上部のスクリーンショットも撮影
        await page.evaluate(() => window.scrollTo(0, 0));
        await page.waitForTimeout(500);
        await page.screenshot({
            path: "/tmp/customer-register-error-top.png",
        });
        console.log("📸 ページ上部のスクリーンショットを保存");

        // 現在のURLを確認
        const currentUrl = page.url();
        console.log(`📍 現在のURL: ${currentUrl}`);

        // 成功メッセージを確認
        const pageContent = await page.content();
        if (pageContent.includes("新しいお問い合わせを登録しました") ||
            currentUrl.includes("/contact/customers/list")) {
            console.log("✅✅✅ 顧客登録テスト成功！");
            console.log("    - ページ遷移: /contact/customers/list");
            console.log("    - 顧客情報: テスト 太郎");
        } else {
            console.log("⚠️ 成功メッセージが確認できませんでした");
            console.log("⚠️ バリデーションエラーまたはその他の問題が発生しています");
        }

        console.log("\n🎉 顧客登録E2Eテストが完了しました");

    } catch (error) {
        console.error("❌ エラーが発生しました:", error.message);

        // エラー時のスクリーンショット
        await page.screenshot({
            path: "/tmp/customer-register-error.png",
            fullPage: true,
        });
        console.log("📸 エラー時のスクリーンショット: /tmp/customer-register-error.png");

        throw error;
    } finally {
        await browser.close();
    }
})();
