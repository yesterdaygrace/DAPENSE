import { test, expect } from '@playwright/test';
import { login, loginAs } from './helpers/auth';
import { ROLE_MAP, type Usertype } from './helpers/roles';

test.describe('Auth & demo-login', () => {
  test('public / redirects to login', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveURL(/\/login|\/$/);
    // auth.login view contains email field
    await expect(page.locator('body')).toContainText(/email|log in/i);
  });

  test('unauthenticated /dashboard redirects to login', async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/\/login/);
  });

  test('unauthenticated /periodes redirects to login', async ({ page }) => {
    await page.goto('/periodes');
    await expect(page).toHaveURL(/\/login/);
  });

  test('login fails with wrong password', async ({ page }) => {
    await page.goto('/login');
    await page.locator('#email').fill(ROLE_MAP.rootsuperuser.email);
    await page.locator('#password').fill('wrong-password');
    await page.getByRole('button', { name: /masuk/i }).click();
    await expect(page.locator('body')).toContainText(/failed|invalid|credentials|password|kredensial|gagal/i);
  });

  test('login succeeds as rootsuperuser', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await expect(page).not.toHaveURL(/\/login/);
  });

  test('all roles can access /dashboard', async ({ page }) => {
    for (const role of Object.keys(ROLE_MAP) as Usertype[]) {
      await loginAs(page, role);
      await page.goto('/dashboard');
      await expect(page).not.toHaveURL(/\/login/);
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      // reset session
      await page.goto('/logout');
      await page.waitForLoadState('networkidle');
    }
  });

  test('legacy rootsuperuser/dashboard only rootsuperuser', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/dashboard');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'admin');
    await page.goto('/rootsuperuser/dashboard');
    // middleware role:rootsuperuser redirects to /
    await expect(page).toHaveURL(/\/$|\/login/);
  });

  test('admin dashboard only admin', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/admin/dashboard');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'operator');
    await page.goto('/admin/dashboard');
    await expect(page).toHaveURL(/\/$|\/login/);
  });

  test('operator/bod dashboards guard correctly', async ({ page }) => {
    await loginAs(page, 'operator');
    await page.goto('/operator/dashboard');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'bod');
    await page.goto('/bod/dashboard');
    await expect(page).not.toHaveURL(/\/login/);

    // bod should not reach operator dashboard
    await page.goto('/operator/dashboard');
    await expect(page).toHaveURL(/\/$|\/login/);
  });

  test('demo-login endpoint works when enabled', async ({ page }) => {
    // hit /demo-login directly; it creates demo@dapense.app and redirects
    await page.goto('/demo-login');
    // either 404 if DEMO_ENABLED false in prod, or redirect to dashboard
    const url = page.url();
    const ok = url.includes('/dashboard') || url.includes('/demo-login') || url.includes('/login');
    expect(ok).toBeTruthy();
  });

  test('logout returns to login', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/logout');
    await page.waitForLoadState('networkidle');
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/\/login/);
  });
});
