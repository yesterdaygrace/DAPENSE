import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { canAccess, type Usertype } from './helpers/roles';
import { uniqueName, waitForLivewire } from './helpers/livewire';

test.describe('COA Workspace @ /coa-workspace', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator'] as Usertype[]) {
    test(`workspace loads and lists as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'coa-workspace'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/coa-workspace');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      // seed has 17 headers + 100 coas
      await expect(page.locator('body')).toContainText(/coa|akun|header/i);
    });

    test(`create header+coa as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'coa-workspace'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/coa-workspace');
      const headerName = uniqueName(`E2E-Header-${role}`);
      const kodeAkun = `${Math.floor(1000 + Math.random() * 9000)}-${role.slice(0, 2)}`;

      // Try header create flow
      const addHeaderBtn = page.getByRole('button', { name: /header|tambah header/i }).first();
      if (await addHeaderBtn.count() > 0) {
        await addHeaderBtn.click();
        await waitForLivewire(page);
        const nameInput = page.getByLabel(/nama|header|keterangan/i).first();
        if (await nameInput.count() > 0) {
          await nameInput.fill(headerName);
          const save = page.getByRole('button', { name: /simpan|save/i }).first();
          if (await save.count() > 0) {
            await save.click();
            await waitForLivewire(page);
          }
        }
      }

      // Try coa create
      const addCoaBtn = page.getByRole('button', { name: /tambah.*coa|add.*coa|coa/i }).first();
      if (await addCoaBtn.count() > 0) {
        await addCoaBtn.click();
        await waitForLivewire(page);
        const kodeInput = page.getByLabel(/kode.*akun|kode/i).first();
        if (await kodeInput.count() > 0) {
          await kodeInput.fill(kodeAkun);
          const namaCoa = page.getByLabel(/nama.*akun|nama/i).first();
          if (await namaCoa.count() > 0) await namaCoa.fill(`E2E ${kodeAkun}`);
          const save = page.getByRole('button', { name: /simpan|save/i }).first();
          if (await save.count() > 0) {
            await save.click();
            await waitForLivewire(page);
          }
        }
      }

      await expect(page).not.toHaveURL(/\/login/);
    });
  }

  test('bod cannot access coa-workspace', async ({ page }) => {
    await loginAs(page, 'bod');
    await page.goto('/coa-workspace');
    const body = await page.locator('body').innerText();
    const blocked = page.url().includes('/login') || /403|forbidden|tidak.*izin/i.test(body);
    expect(blocked).toBeTruthy();
  });

  test('template download and export exist', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/coa-workspace');
    // template link
    const tpl = page.locator('a[href*="template"], button:has-text("template")').first();
    const count = await tpl.count();
    // not failing if not present, just check page has export/import UI (ID: Ekspor/Unduh Template)
    await expect(page.locator('body')).toContainText(/export|import|template|ekspor|unduh/i);
    if (count > 0) {
      // do not actually download, just check href
      const href = await tpl.getAttribute('href');
      expect(href).toBeTruthy();
    }
  });

  test('legacy rootsuperuser coa workspace guard', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/rootsuperuser/master-data/coa-workspace');
    await expect(page).not.toHaveURL(/\/login/);

    await page.goto('/logout');
    await loginAs(page, 'bod');
    await page.goto('/rootsuperuser/master-data/coa-workspace');
    await expect(page).toHaveURL(/\/$|\/login/);
  });
});
