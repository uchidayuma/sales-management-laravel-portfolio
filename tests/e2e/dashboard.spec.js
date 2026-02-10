const { test, expect } = require('@playwright/test');

test('admin can login and view dashboard', async ({ page }) => {
  // 1. Visit Login Page
  await page.goto('/login');

  // 2. Fill Credentials (Admin)
  await page.fill('input[name="email"]', 'admin@example.com');
  await page.fill('input[name="password"]', 'password');

  // 3. Submit
  await page.click('#submit');

  // 4. Assert Redirect to Dashboard (/)
  await expect(page).toHaveURL('/');

  // 5. Verify Dashboard Content
  // Check for the new tab we made default: "Drawing Estimate" / "図面見積もり"
  // Use specific ID to avoid ambiguity with table content
  await expect(page.locator('#pills-profile-tab')).toBeVisible();
  
  // Check if Charts are present (canvas elements)
  await expect(page.locator('#myChart')).toBeVisible();
  await expect(page.locator('#myChart2')).toBeVisible();

  const drawingTab = page.locator('#pills-profile-tab');
  await expect(drawingTab).toHaveClass(/active/);
});




