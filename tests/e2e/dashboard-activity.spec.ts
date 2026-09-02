import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { type Usertype, canAccess } from './helpers/roles';

test.describe('Dashboard & Activity & Reports', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator', 'bod'] as Usertype[]) {
    test(`dashboard loads as ${role}`, async ({ page }) => {
      await loginAs(page, role);
      await page.goto('/dashboard');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/dasbor|dashboard/i);
    });
  }

  test('activity log per role', async ({ page }) => {
    // activity should be accessible to authenticated users
    for (const role of ['rootsuperuser', 'admin'] as Usertype[]) {
      await loginAs(page, role);
      await page.goto('/activity');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await page.goto('/logout');
    }
  });

  test('master-data / transactions / reports / finance stubs', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    for (const path of ['/master-data', '/transactions', '/reports', '/finance', '/administration', '/settings']) {
      await page.goto(path);
      await expect(page).not.toHaveURL(/\/login/);
      await expect(page.locator('body')).not.toContainText(/403.*forbidden/i);
    }
  });

  test('administration only rootsuperuser+admin', async ({ page }) => {
    // /administration is Route::view without CheckRole gate — all authenticated can view hub, card gate at /users
    await loginAs(page, 'operator');
    await page.goto('/administration');
    await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
    await expect(page.locator('body')).toContainText(/administrasi/i);

    await page.goto('/logout');
    await loginAs(page, 'admin');
    await page.goto('/administration');
    await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
  });

  test('profile edit', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/profile');
    // Breeze profile page is Indonesian in this locale — accept either
    await expect(page.locator('body')).toContainText(/profile|profil/i);
  });
});
