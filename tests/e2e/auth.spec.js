import { test, expect } from '@playwright/test';

test('user can see login page and initiate OTP', async ({ page }) => {
    await page.goto('/');

    // Expect title to contain Roomie
    await expect(page).toHaveTitle(/Roomie/);

    // Fill in a 10-digit phone number
    const phone = '9' + Math.floor(Math.random() * 900000000 + 100000000).toString();
    await page.fill('input[name="phone"]', phone);

    // Submit
    await page.click('button:has-text("Send OTP")');

    // Should redirect to verify-otp
    await expect(page).toHaveURL(/.*verify-otp/);
    await expect(page.locator('text=Verification')).toBeVisible();
});

test('submitting invalid phone shows error', async ({ page }) => {
    await page.goto('/');
    await page.fill('input[name="phone"]', '1235'); // Invalid length
    await page.click('button:has-text("Send OTP")');

    // Should show error message (inline error we added)
    await expect(page.locator('form p')).toContainText(/must be 10 digits/i);
});
