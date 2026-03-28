import { test, expect } from '@playwright/test';

test('Full User Journey: Login to Expense Creation', async ({ page }) => {
    // 1. LOGIN
    await page.goto('/');
    const phone = '9' + Math.floor(Math.random() * 900000000 + 100000000).toString();
    await page.fill('input[name="phone"]', phone);
    await page.click('button[type="submit"]');

    // 2. VERIFY OTP
    await expect(page).toHaveURL(/.*verify-otp/);

    // Extract OTP from debug message - precisely locate the debug card
    const debugLocator = page.locator('.card', { hasText: /DEBUG: Your OTP is/ });
    await expect(debugLocator).toBeVisible();
    const debugText = await debugLocator.innerText();
    const otpMatch = debugText.match(/OTP is (\d+)/);
    const otp = otpMatch ? otpMatch[1] : null;

    if (!otp) throw new Error("Could not find OTP in debug message: " + debugText);

    await page.fill('input[name="otp"]', otp);
    await page.click('button:has-text("Verify")');

    // 3. PROFILE SETUP
    // Give it more time for the heavy redirect/session handling
    await expect(page).toHaveURL(/.*profile-setup/, { timeout: 10000 });
    await page.fill('input[name="name"]', 'Test User');
    await page.click('button[type="submit"]');

    // 4. GROUP CREATION
    // If no groups, should be on dashboard or groups page
    await page.goto('/groups/create');
    await page.fill('input[name="name"]', 'Testing App Group');
    await page.click('button[type="submit"]');

    // 5. DASHBOARD VERIFICATION
    await expect(page).toHaveURL(/.*dashboard/);
    await expect(page.locator('header span').filter({ hasText: /TESTING APP GROUP/i })).toBeVisible();

    // 6. ADD ROOMMATE
    await page.click('a[href*="roommates"]');
    await page.fill('input[name="name"]', 'Roomie Friend');
    await page.fill('input[name="phone"]', '1122334455');
    await page.click('button[type="submit"]');
    await expect(page.locator('text=Roomie Friend')).toBeVisible();

    // 7. ADD EXPENSE
    // Click the FAB or navigate
    await page.goto('/expenses/create');
    await page.fill('input[name="description"]', 'Team Lunch');
    await page.fill('input[name="amount"]', '1000');

    // Use Split Equally
    await page.click('button:has-text("Split Equally")');

    // Submit
    await page.click('button[type="submit"]');

    // 8. FINAL VERIFICATION
    await expect(page).toHaveURL(/.*dashboard/);
    await expect(page.locator('text=Team Lunch')).toBeVisible();

    // User paid 1000, borrowed 500. Friend borrowed 500.
    // User should have a positive balance of 500
    const userCard = page.locator('.card', { has: page.locator('text=You') });
    await expect(userCard).toBeVisible();
    await expect(userCard.locator('text=is owed')).toBeVisible();
    await expect(userCard.locator('text=+₹500.00')).toBeVisible();
});
