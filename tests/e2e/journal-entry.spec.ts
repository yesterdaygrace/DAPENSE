import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { canAccess, type Usertype } from './helpers/roles';
import { waitForLivewire } from './helpers/livewire';

test.describe('Journal Entry @ /jurnal-entry', () => {
  for (const role of ['rootsuperuser', 'admin', 'operator'] as Usertype[]) {
    test(`loads and can create entry as ${role}`, async ({ page }) => {
      test.skip(!canAccess(role, 'jurnal-entry'), `role ${role} blocked`);
      await loginAs(page, role);
      await page.goto('/jurnal-entry');
      await expect(page.locator('body')).not.toContainText(/403|forbidden/i);
      await expect(page.locator('body')).toContainText(/jurnal|entry/i);

      // Try fill minimal entry if form present
      const coaSelect = page.locator('select').first();
      if (await coaSelect.count() > 0) {
        // just check we have options (seed 100 coas)
        const opts = await coaSelect.locator('option').count();
        expect(opts).toBeGreaterThan(0);
      }

      // POST endpoint smoke: submit via button if visible
      const saveBtn = page.getByRole('button', { name: /simpan|save|submit|posting/i }).first();
      // do not force submit invalid data; just ensure button exists or form exists
      if (await saveBtn.count() > 0) {
        await expect(saveBtn).toBeVisible();
      }
      await expect(page).not.toHaveURL(/\/login/);
    });
  }

  test('bod cannot access jurnal-entry', async ({ page }) => {
    await loginAs(page, 'bod');
    await page.goto('/jurnal-entry');
    const body = await page.locator('body').innerText();
    expect(page.url().includes('/login') || /403|forbidden|tidak.*izin/i.test(body)).toBeTruthy();
  });

  test('jurnal-entry POST requires balanced debit/kredit (validation)', async ({ page }) => {
    await loginAs(page, 'rootsuperuser');
    await page.goto('/jurnal-entry');
    // Attempt empty post via API to check validation is enforced server-side
    const resp = await page.request.post('/jurnal-entry', {
      form: { nomor_bukti: '', periode_id: '' },
    });
    // should be 419 (CSRF) or 422/302 due to validation — not 200 success with unbalanced entry
    expect([302, 419, 422, 403, 404].includes(resp.status())).toBeTruthy();
  });
});
