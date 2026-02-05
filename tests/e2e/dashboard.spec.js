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
  await expect(page.locator('#pills-drow-tab')).toBeVisible();
  
  // Check if Charts are present (canvas elements)
  await expect(page.locator('#myChart')).toBeVisible();
  await expect(page.locator('#myChart2')).toBeVisible();

  const drawingTab = page.locator('#pills-profile-tab');
  await expect(drawingTab).toHaveClass(/active/);
});

test('admin can switch dashboard tabs', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'admin@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('#submit');

  // Switch to "Visit Estimate" (pills-home-tab)
  await page.click('#pills-home-tab');
  
  // Verify Visit Estimate tab is active
  await expect(page.locator('#pills-home-tab')).toHaveClass(/active/);
  // Verify Visit Estimate content is visible (checking for header "訪問見積もりFC未連絡リスト")
  // Note: The content pane id is #pills-visit
  await expect(page.locator('#pills-visit')).toBeVisible();

  // Verify seeded data exists (e.g., ID 301 from previous seed)
  await expect(page.getByRole('link', { name: '301' })).toBeVisible();
});

test('admin can view leave alone list', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'admin@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('#submit');

  // Click "Leave Alone" tab (pills-leave-tab)
  await page.click('#pills-leave-tab');

  // Verify tab active
  await expect(page.locator('#pills-leave-tab')).toHaveClass(/active/);
  
  // Verify content pane visible
  await expect(page.locator('#pills-leave')).toBeVisible();

  // Verify seeded "Leave Alone" data exists (e.g., ID 331)
  await expect(page.getByRole('link', { name: '331' })).toBeVisible();
});
