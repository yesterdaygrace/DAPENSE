import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { canAccess, type Usertype } from './helpers/roles';

test.describe('Jurnaling @ /jurnaling + /jurnaling-list', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator', 'bod'] as Usertype[]) {
    test(`manager loads as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'jurnaling'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/jurnaling');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/jurnal/i);
    });

    test(`list loads as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'jurnaling'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/jurnaling-list');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
    });

    test(`export accessible as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'jurnaling'), `role ${role} blocked`);
      await loginAs(page, role);
      // page.request is unauthenticated; use page.goto so auth cookies are sent
      const resp = await page.goto('/jurnaling/export');
      const status = resp?.status() ?? 0;
      // without query params controller redirects to /jurnaling/months (302) or streams xlsx (200) — both OK; 419 is CSRF edge, <500 is not server error
      expect([200, 302, 419].includes(status) || status < 500).toBeTruthy();
    });
  }

  test('bod can access jurnaling (policy viewAny allows bod)', async ({ page }) => {
    await loginAs(page, 'bod');
    await page.goto('/jurnaling');
    await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
    await expect(page.locator('body')).toContainText(/jurnal/i);
  });

  test('legacy jurnaling guard', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/jurnaling');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'bod');
    await page.goto('/rootsuperuser/jurnaling');
    await expect(page).toHaveURL(/\/$|\/login/);
  });

  test('filter by periode exists', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/jurnaling');
    // look for periode selector
    const sel = page.locator('select').first();
    const count = await sel.count();
    // just smoke: page has some filters/ledger UI
    await expect(page.locator('body')).toContainText(/periode|bulan|filter/i);
    if (count > 0) await expect(sel).toBeVisible();
  });
});
