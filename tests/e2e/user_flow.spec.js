const { test, expect } = require('@playwright/test');

test.describe('User Flows', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('#submit');
    });

    test('can access quotations list', async ({ page }) => {
        await page.goto('/quotations');
        // Verify page title or table
        await expect(page.locator('h1.page-title')).toContainText('見積もり一覧');
        // Verify table exists
        await expect(page.locator('.common-table')).toBeVisible();
    });

    test('can access profile edit', async ({ page }) => {
        // Open Modal (top right icon)
        await page.click('#icon_button');
        
        // Wait for modal to be visible
        await expect(page.locator('#userInfoModal')).toBeVisible();

        // Click "Profile Change"
        await page.click('text=プロフィールを変更');

        // Verify URL /users/edit/{id}
        await expect(page).toHaveURL(/\/users\/edit\/\d+/);
        
        // Verify Form
        await expect(page.locator('input[name="company_name"]')).toBeVisible();
    });

    test('can logout', async ({ page }) => {
        // Open Modal
        await page.click('#icon_button');
        
        // Wait for modal
        await expect(page.locator('#userInfoModal')).toBeVisible();

        // Click Logout
        await page.click('#logout');

        // Verify redirect to login
        await expect(page).toHaveURL(/\/login/);
        
        // Confirm we are logged out (e.g. check for login form)
        await expect(page.locator('form[action$="login"]')).toBeVisible();
    });
});
