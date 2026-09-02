import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { canAccess, type Usertype } from './helpers/roles';
import { uniqueName, waitForLivewire } from './helpers/livewire';

test.describe('Saldo Awal @ /saldo-awal', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator'] as Usertype[]) {
    test(`crud as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'saldoawal'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/saldo-awal');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/saldo/i);

      const btn = page.getByRole('button', { name: /tambah|add|create/i }).first();
      if (await btn.count() > 0) {
        await btn.click();
        await waitForLivewire(page);
        const nominal = page.getByLabel(/nominal|saldo|amount/i).first();
        if (await nominal.count() > 0) await nominal.fill('1000000');
        const save = page.getByRole('button', { name: /simpan|save/i }).first();
        if (await save.count() > 0) {
          await save.click();
          await waitForLivewire(page);
        }
      }
      await expect(page).not.toHaveURL(/\/login/);
    });
  }

  test('bod cannot access saldo-awal', async ({ page }) => {
    await loginAs(page, 'bod');
    await page.goto('/saldo-awal');
    const body = await page.locator('body').innerText();
    expect(page.url().includes('/login') || /403|forbidden|tidak.*izin/i.test(body)).toBeTruthy();
  });

  test('legacy saldoawal guard', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/saldoawal');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'bod');
    await page.goto('/rootsuperuser/saldoawal');
    await expect(page).toHaveURL(/\/$|\/login/);
  });
});
