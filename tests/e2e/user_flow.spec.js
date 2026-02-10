const { test, expect } = require('@playwright/test');

test.describe('User Flows', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('#submit');
    });

    test('user flow sequence', async ({ page }) => {
        // 1. Access Quotations List
        await page.goto('/quotations');
        // Verify table exists (page title might not be h1.page-title)
        await expect(page.locator('.common-table-stripes-row')).toBeVisible();

        // 2. Access Profile Edit
        // Open modal
        await page.click('#icon_button');
        
        // Wait for modal to be visible (Bootstrap adds 'show' class)
        await expect(page.locator('#userInfoModal')).toHaveClass(/show/);
        await expect(page.locator('#userInfoModal')).toBeVisible();

        // Click "Profile Change"
        await page.click('text=プロフィールを変更');
        
        // Verify URL
        await expect(page).toHaveURL(/\/users\/edit\/\d+/);
        
        // Verify Form
        await expect(page.locator('input[name="fc[company_name]"]')).toBeVisible();

        // 3. Logout
        // Open modal
        await expect(page.locator('#icon_button').first()).toBeVisible();
        await page.locator('#icon_button').first().click();
        
        // Wait for modal
        await expect(page.locator('#userInfoModal')).toHaveClass(/show/);
        await expect(page.locator('#userInfoModal')).toBeVisible();

        // Click Logout
        await page.click('text=ログアウト');
        
        // Verify Redirect to Login
        await expect(page).toHaveURL(/\/login/);
        
        // Confirm we are logged out (e.g. check for login form)
        await expect(page.locator('form[action$="login"]')).toBeVisible();
    });
});
