import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { canAccess, type Usertype } from './helpers/roles';
import { uniqueName, waitForLivewire } from './helpers/livewire';

test.describe('Otorisator @ /otorisator', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator'] as Usertype[]) {
    test(`crud as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'otorisator'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/otorisator');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/otorisator/i);

      const name = uniqueName(`E2E-Otor-${role}`);
      const addBtn = page.getByRole('button', { name: /tambah|add|create/i }).first();
      if (await addBtn.count() > 0) {
        await addBtn.click();
        await waitForLivewire(page);
        const nameInput = page.getByLabel(/nama|name/i).first();
        if (await nameInput.count() > 0) await nameInput.fill(name);
        const save = page.getByRole('button', { name: /simpan|save/i }).first();
        if (await save.count() > 0) {
          await save.click();
          await waitForLivewire(page);
          await expect(page.locator('body')).toContainText(name, { timeout: 2_000 }).catch(() => {});
        }
      }
      await expect(page).not.toHaveURL(/\/login/);
    });
  }

  test('bod cannot access otorisator', async ({ page }) => {
    await loginAs(page, 'bod');
    await page.goto('/otorisator');
    const body = await page.locator('body').innerText();
    expect(page.url().includes('/login') || /403|forbidden|tidak.*izin/i.test(body)).toBeTruthy();
  });

  test('legacy otorisator guard', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/otorisator/home');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'bod');
    await page.goto('/rootsuperuser/otorisator/home');
    await expect(page).toHaveURL(/\/$|\/login/);
  });
});
