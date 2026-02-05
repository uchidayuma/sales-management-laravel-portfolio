const { test, expect } = require('@playwright/test');

test.describe('Customer Search & Detail', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@example.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('#submit');
    });

    test('can access search page', async ({ page }) => {
        // Navigate to Search Page via URL
        await page.goto('/search');
        
        // Verify Breadcrumbs contain '顧客検索' (since there is no h1)
        await expect(page.locator('.breadcrumbs')).toContainText('顧客検索');
        
        // Verify Search Form exists
        await expect(page.locator('form.common-form')).toBeVisible();
    });

    test('can search for a customer', async ({ page }) => {
        await page.goto('/search');

        // Fill search term (Search by PREF as TEL is type=number but data has hyphens)
        // Seed 301: pref 'サンプル市' (Exact match required)
        await page.fill('input[name="pref"]', 'サンプル市');
        
        // Click Search
        await page.click('input[type="submit"]');

        // Verify Results
        // Should contain ID 302 in the table (which is Step 11/Report Complete)
        // Use specific table class to avoid debugbar tables
        await expect(page.locator('table.common-table-stripes-row')).toContainText('302');
    });

    test('can view customer detail from dashboard', async ({ page }) => {
        // Go to dashboard (default after login)
        await page.goto('/');

        // Click on a customer ID (e.g., 311 from Drawing Estimate tab which is default active)
        // Seed 311 should be in the default active "Drawing Estimate" list
        const link = page.getByRole('link', { name: '311' });
        await expect(link).toBeVisible();
        await link.click();

        // Verify URL is /contact/311
        await expect(page).toHaveURL(/\/contact\/311/);

        // Verify Detail Page Content
        await expect(page.locator('body')).toContainText('案件詳細');
        await expect(page.locator('body')).toContainText('311');
    });
});
