import { expect, type Page } from '@playwright/test';
import { ROLE_MAP, type Usertype } from './roles';

export async function login(page: Page, email: string, password: string) {
  await page.goto('/login');
  // Indonesian login form: Email / Kata Sandi + button Masuk
  await page.locator('#email').fill(email);
  await page.locator('#password').fill(password);
  await page.getByRole('button', { name: /masuk/i }).click();
  await page.waitForLoadState('networkidle');
}

export async function loginAs(page: Page, role: Usertype) {
  const creds = ROLE_MAP[role];
  await login(page, creds.email, creds.password);
  // after login should be on /dashboard or rootsuperuser/dashboard legacy; accept either
  await expect(page).not.toHaveURL(/\/login/);
}

export async function logout(page: Page) {
  // Breeze logout is POST /logout with CSRF; for test harness clear cookies is reliable.
  try {
    const token = await page.evaluate(() => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '');
    if (token) {
      await page.request.post('/logout', { headers: { 'X-CSRF-TOKEN': token } });
    }
  } catch {}
  await page.context().clearCookies();
  await page.evaluate(() => localStorage.clear()).catch(() => {});
  await page.goto('/login');
  await page.waitForLoadState('networkidle');
}

export async function ensureAuthenticated(page: Page, role: Usertype) {
  await loginAs(page, role);
}

export async function gotoAndExpect(page: Page, url: string, shouldSucceed: boolean) {
  await page.goto(url);
  if (shouldSucceed) {
    await expect(page).not.toHaveURL(/\/login/);
    await expect(page.locator('body')).not.toContainText(/403|unauthorized/i);
  } else {
    // Livewire components redirect or show 403; we assert either login redirect or forbidden
    const current = page.url();
    const body = await page.locator('body').innerText();
    const isBlocked = current.includes('/login') || /403|forbidden|unauthorized|tidak.*izin/i.test(body);
    expect(isBlocked).toBeTruthy();
  }
}
