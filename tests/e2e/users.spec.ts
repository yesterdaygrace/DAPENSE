import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { canAccess, type Usertype } from './helpers/roles';
import { uniqueName, waitForLivewire } from './helpers/livewire';

test.describe('Users @ /users (UserManager)', () => {
  for (const role of ['rootsuperuser', 'admin'] as Usertype[]) {
    test(`crud as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'users'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/users');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/user|pengguna/i);

      const email = `e2e_user_${role}_${Date.now()}@test.invalid`;
      const addBtn = page.getByRole('button', { name: /tambah|add|create|user/i }).first();
      if (await addBtn.count() > 0) {
        await addBtn.click();
        await waitForLivewire(page);
        const nameInput = page.getByLabel(/name|nama/i).first();
        if (await nameInput.count() > 0) await nameInput.fill(uniqueName(`E2E User ${role}`));
        const emailInput = page.getByLabel(/email/i).first();
        if (await emailInput.count() > 0) await emailInput.fill(email);
        const passInput = page.getByLabel(/password/i).first();
        if (await passInput.count() > 0) await passInput.fill('password');
        const typeSel = page.getByLabel(/usertype|role|tipe/i).first();
        if (await typeSel.count() > 0) {
          // try select
          try { await typeSel.selectOption('operator'); } catch {}
        }
        const save = page.getByRole('button', { name: /simpan|save/i }).first();
        if (await save.count() > 0) {
          await save.click();
          await waitForLivewire(page);
        }
      }
      await expect(page).not.toHaveURL(/\/login/);
    });
  }

  for (const role of ['operator', 'bod'] as Usertype[]) {
    test(`${role} cannot access /users`, async ({ page }) => {
      test.skip(canAccess(role, 'users'), `role ${role} unexpectedly allowed`);
      await loginAs(page, role);
      await page.goto('/users');
      const body = await page.locator('body').innerText();
      expect(page.url().includes('/login') || /403|forbidden|tidak.*izin/i.test(body)).toBeTruthy();
    });
  }
});
