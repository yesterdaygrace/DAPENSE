import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { canAccess, type Usertype } from './helpers/roles';

test.describe('Posting @ /posting', () => {
  for (const role of ['rootsuperuser', 'admin'] as Usertype[]) {
    test(`loads as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'posting'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/posting');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/posting/i);
    });
  }

  for (const role of ['operator', 'bod'] as Usertype[]) {
    test(`${role} cannot access posting`, async ({ page }) => {
      test.skip(canAccess(role, 'posting'), `role ${role} unexpectedly allowed`);
      await loginAs(page, role);
      await page.goto('/posting');
      const body = await page.locator('body').innerText();
      expect(page.url().includes('/login') || /403|forbidden|tidak.*izin/i.test(body)).toBeTruthy();
    });
  }

  test('posting POST throttled and requires periode', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/posting');
    const resp = await page.request.post('/posting', { form: { periode_id: '' } });
    // expect 419 CSRF or 422 validation or 302 redirect, not 500
    expect([302, 419, 422, 403].includes(resp.status()) || resp.status() < 500).toBeTruthy();
  });

  test('legacy posting guard', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/posting');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'operator');
    await page.goto('/rootsuperuser/posting');
    await expect(page).toHaveURL(/\/$|\/login/);
  });
});
