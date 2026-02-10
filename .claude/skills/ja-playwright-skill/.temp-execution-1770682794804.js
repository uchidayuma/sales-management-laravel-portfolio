const { chromium } = require("playwright");

// Target URL for the Laravel application
const TARGET_URL = "http://localhost:80";

// Test credentials from README.md
const TEST_USERS = [
    { type: "Admin (本部)", email: "admin@example.com", password: "password" },
    { type: "FC User", email: "user2@example.com", password: "password" }
];

(async () => {
    const browser = await chromium.launch({ headless: false, slowMo: 150 });

    for (const user of TEST_USERS) {
        const page = await browser.newPage();

        try {
            console.log(`\n${"=".repeat(60)}`);
            console.log(`🔐 Testing Login Flow: ${user.type}`);
            console.log(`   Email: ${user.email}`);
            console.log(`${"=".repeat(60)}\n`);

            // Step 1: Navigate to login page
            console.log("📍 Step 1: Navigating to login page...");
            await page.goto(`${TARGET_URL}/login`, { waitUntil: "networkidle" });
            console.log(`✅ Login page loaded: ${await page.title()}`);
            await page.screenshot({
                path: `/tmp/login-1-page-${user.type.replace(/\s+/g, '-')}.png`,
                fullPage: true
            });
            console.log(`📸 Screenshot: login-1-page-${user.type.replace(/\s+/g, '-')}.png`);

            // Step 2: Fill in credentials
            console.log("\n📝 Step 2: Filling in credentials...");
            await page.fill('input[type="email"], input[name="email"]', user.email);
            console.log(`   ✅ Email filled: ${user.email}`);

            await page.fill('input[type="password"], input[name="password"]', user.password);
            console.log(`   ✅ Password filled: ********`);

            await page.screenshot({
                path: `/tmp/login-2-filled-${user.type.replace(/\s+/g, '-')}.png`,
                fullPage: true
            });
            console.log(`📸 Screenshot: login-2-filled-${user.type.replace(/\s+/g, '-')}.png`);

            // Step 3: Submit form
            console.log("\n🚀 Step 3: Submitting login form...");
            await page.click('button[type="submit"], input[type="submit"]');

            // Wait for navigation
            await page.waitForLoadState("networkidle", { timeout: 10000 });

            const currentURL = page.url();
            console.log(`✅ Redirected to: ${currentURL}`);

            // Step 4: Verify successful login
            console.log("\n✔️  Step 4: Verifying authentication...");

            // Check if we're no longer on the login page
            const isLoginPage = currentURL.includes('/login');

            if (!isLoginPage) {
                console.log(`✅ SUCCESS: Authentication successful!`);
                console.log(`   - Redirected away from /login`);
                console.log(`   - Current page: ${currentURL}`);
                console.log(`   - Page title: ${await page.title()}`);

                // Take screenshot of dashboard/home page
                await page.screenshot({
                    path: `/tmp/login-3-success-${user.type.replace(/\s+/g, '-')}.png`,
                    fullPage: true
                });
                console.log(`📸 Screenshot: login-3-success-${user.type.replace(/\s+/g, '-')}.png`);

                // Check for common authenticated elements
                const hasLogout = await page.locator('a:has-text("ログアウト"), a:has-text("Logout"), button:has-text("ログアウト")').count() > 0;
                const hasUserMenu = await page.locator('[class*="user"], [class*="profile"], [class*="account"]').count() > 0;

                console.log(`\n🔍 Authenticated UI Elements:`);
                console.log(`   - Logout button: ${hasLogout ? "✅ Found" : "⚠️  Not found"}`);
                console.log(`   - User menu: ${hasUserMenu ? "✅ Found" : "⚠️  Not found"}`);

            } else {
                console.log(`❌ FAILED: Still on login page`);
                console.log(`   - Authentication may have failed`);
                console.log(`   - Check for error messages`);

                // Check for error messages
                const errorMessages = await page.locator('.alert-danger, .error, .invalid-feedback').allTextContents();
                if (errorMessages.length > 0) {
                    console.log(`\n⚠️  Error messages found:`);
                    errorMessages.forEach(msg => console.log(`   - ${msg.trim()}`));
                }

                await page.screenshot({
                    path: `/tmp/login-3-failed-${user.type.replace(/\s+/g, '-')}.png`,
                    fullPage: true
                });
                console.log(`📸 Screenshot: login-3-failed-${user.type.replace(/\s+/g, '-')}.png`);
            }

            // Wait a moment before closing
            await page.waitForTimeout(1500);

        } catch (error) {
            console.error(`\n❌ ERROR during ${user.type} login:`, error.message);
            await page.screenshot({
                path: `/tmp/login-error-${user.type.replace(/\s+/g, '-')}.png`,
                fullPage: true
            });
            console.log(`📸 Error screenshot: login-error-${user.type.replace(/\s+/g, '-')}.png`);
        } finally {
            await page.close();
        }
    }

    console.log(`\n${"=".repeat(60)}`);
    console.log(`🏁 All login flow tests completed!`);
    console.log(`📸 Screenshots saved to /tmp/`);
    console.log(`${"=".repeat(60)}\n`);

    await browser.close();
})();
