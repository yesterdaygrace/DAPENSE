import type { Page } from '@playwright/test';

export async function waitForLivewire(page: Page) {
  // Livewire 3/4 uses fetch to /livewire/update ; wait briefly after interaction
  await page.waitForTimeout(300);
  // optionally wait for network idle for livewire
  try {
    await page.waitForResponse(
      (r) => r.url().includes('/livewire/update') || r.url().includes('/livewire/message'),
      { timeout: 2_000 },
    );
  } catch {
    // ignore if no livewire request
  }
}

export async function fillLivewireInput(page: Page, nameOrLabel: string | RegExp, value: string) {
  // try label first, then placeholder, then name
  const byLabel = page.getByLabel(nameOrLabel);
  if ((await byLabel.count()) > 0) {
    await byLabel.first().fill(value);
    return;
  }
  const byPlaceholder = page.getByPlaceholder(nameOrLabel);
  if ((await byPlaceholder.count()) > 0) {
    await byPlaceholder.first().fill(value);
    return;
  }
  // fallback: input[name*="..."]
  const sel = typeof nameOrLabel === 'string' ? `input[name*="${nameOrLabel}"], textarea[name*="${nameOrLabel}"]` : 'input, textarea';
  await page.locator(sel).first().fill(value);
}

export async function selectLivewireOption(page: Page, labelOrName: string | RegExp, value: string) {
  const combo = page.getByLabel(labelOrName);
  if ((await combo.count()) > 0) {
    await combo.first().selectOption(value);
    return;
  }
  const sel = page.locator('select').first();
  if ((await sel.count()) > 0) {
    await sel.selectOption(value);
  }
}

export function uniqueName(prefix: string) {
  return `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2, 6)}`;
}
