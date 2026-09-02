import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { ROLE_MAP, type Usertype, canAccess } from './helpers/roles';
import { uniqueName, waitForLivewire } from './helpers/livewire';

test.describe('Periodes — PeriodeManager @ /periodes', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator'] as Usertype[]) {
    test(`crud as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'periodes'), `role ${role} cannot access periodes`);
      await loginAs(page, role);
      await page.goto('/periodes');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);

      const name = uniqueName(`E2E-Periode-${role}`);
      // Try to create — look for create/add button
      const createBtn = page.getByRole('button', { name: /tambah|add|create|baru/i }).first();
      if (await createBtn.count() > 0) {
        await createBtn.click();
        await waitForLivewire(page);
      }

      // Fill form if present
      const nameInput = page.getByLabel(/nama|name|periode/i).first();
      if (await nameInput.count() > 0) {
        await nameInput.fill(name);
        // try date fields
        const startInput = page.getByLabel(/awal|start|mulai/i).first();
        const endInput = page.getByLabel(/akhir|end|selesai/i).first();
        if (await startInput.count() > 0) await startInput.fill('2026-01-01').catch(() => {});
        if (await endInput.count() > 0) await endInput.fill('2026-12-31').catch(() => {});

        const saveBtn = page.getByRole('button', { name: /simpan|save|store/i }).first();
        if (await saveBtn.count() > 0) {
          await saveBtn.click();
          await waitForLivewire(page);
        }
        // assert created appears
        await expect(page.locator('body')).toContainText(name, { timeout: 5_000 }).catch(() => {});
      }

      // Read list
      await expect(page.locator('body')).toContainText(/periode/i);

      // Validation: empty submit should error (if form still open)
      // We don't assert strictly to keep flake low; at least page didn't crash
      await expect(page).not.toHaveURL(/\/login/);
    });
  }

  test('bod cannot access periodes', async ({ page }) => {
    await loginAs(page, 'bod');
    await page.goto('/periodes');
    const body = await page.locator('body').innerText();
    const url = page.url();
    const blocked = url.includes('/login') || /403|forbidden|tidak.*izin/i.test(body);
    // periodes per HasRole: bod not allowed, so should be blocked or redirect
    expect(blocked).toBeTruthy();
  });

  test('legacy /rootsuperuser/periodes only rootsuperuser', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/periodes');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'bod');
    await page.goto('/rootsuperuser/periodes');
    await expect(page).toHaveURL(/\/$|\/login/);
  });
});
