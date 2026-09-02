import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { type Usertype, canAccess } from './helpers/roles';

test.describe('Neraca Saldo @ /neraca-saldo', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator', 'bod'] as Usertype[]) {
    test(`loads as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'neracasaldo'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/neraca-saldo');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/neraca.*saldo|trial balance/i);
    });
  }

  test('neraca saldo export endpoints', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    // need a periode_id; hit index first to discover
    await page.goto('/neraca-saldo');
    // try export of periode 1 (seed has periode 1)
    for (const url of ['/neraca-saldo/exportexcel/1', '/neraca-saldo/exportpdf/1']) {
      const resp = await page.request.get(url);
      expect(resp.status() < 500).toBeTruthy();
    }
  });

  test('per-periode view', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/neraca-saldo/1');
    await expect(page.locator('body')).toContainText(/neraca|saldo|periode/i);
  });

  test('legacy neracasaldo guard', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/neracasaldo/1');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'operator');
    // legacy is rootsuperuser-only, operator should be blocked
    await page.goto('/rootsuperuser/neracasaldo/1');
    await expect(page).toHaveURL(/\/$|\/login/);
  });
});
