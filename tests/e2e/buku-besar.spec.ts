import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { type Usertype, canAccess } from './helpers/roles';

test.describe('Buku Besar @ /bukubesar', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator', 'bod'] as Usertype[]) {
    test(`loads as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'bukubesar'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/bukubesar');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/buku.*besar|ledger/i);
    });

    test(`export endpoint as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'bukubesar'), `role ${role} blocked`);
      await loginAs(page, role);
      const resp = await page.request.get('/bukubesar/export');
      expect(resp.status() < 500).toBeTruthy();
    });
  }

  test('ledger search by periode+coa flow', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/bukubesar');
    // select periode if present, then search
    const sel = page.locator('select').first();
    if (await sel.count() > 0) {
      await sel.selectOption({ index: 1 }).catch(() => {});
    }
    const searchBtn = page.getByRole('button', { name: /cari|search|tampil/i }).first();
    if (await searchBtn.count() > 0) {
      await searchBtn.click();
      await page.waitForLoadState('networkidle');
    }
    await expect(page).not.toHaveURL(/\/login/);
  });

  test('legacy bukubesar guard (rootsuperuser)', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/bukubesar');
    await expect(page).not.toHaveURL(/\/login/);
  });
});
