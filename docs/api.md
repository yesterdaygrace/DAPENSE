# API Contract — DAPENSE

> Authoritative reference for all HTTP routes, controllers, and Livewire components.

DAPENSE is a **server-rendered application** — there is no REST/JSON API. All routes return Blade views or Livewire components. This document catalogs the full route table for reference.

## 1. Route Groups

| Group | Middleware | Prefix | Purpose |
|-------|-----------|--------|---------|
| Public | none | `/` | Login page, demo login |
| Guest | `guest` | `/` | Registration, password reset |
| Auth | `auth`, `verified` | `/` | Main application routes |
| RootSuperuser | `auth`, `role:rootsuperuser` | `/rootsuperuser` | Legacy role-prefixed routes |
| Admin | `auth`, `role:admin` | `/admin` | Admin-only routes |
| Operator | `auth`, `role:operator` | `/operator` | Operator-only routes |
| BOD | `auth`, `role:bod` | `/bod` | Board of Directors routes |

## 2. Public Routes

| Method | URI | Handler | Description |
|--------|-----|---------|-------------|
| GET | `/` | Closure | Redirects to login |
| GET | `/demo-login` | Closure | Auto-creates demo user, logs in as rootsuperuser |
| GET | `/health` | Closure | Health check → `{"status":"ok"}` |

## 3. Authentication Routes (`routes/auth.php`)

| Method | URI | Handler | Middleware |
|--------|-----|---------|-----------|
| GET | `/register` | `RegisteredUserController@create` | guest |
| POST | `/register` | `RegisteredUserController@store` | guest |
| GET | `/login` | `AuthenticatedSessionController@create` | guest |
| POST | `/login` | `AuthenticatedSessionController@store` | guest |
| GET | `/login/periode` | `PeriodeController@create` | guest |
| POST | `/login/periode/save` | `PeriodeController@save` | guest |
| GET | `/forgot-password` | `PasswordResetLinkController@create` | guest |
| POST | `/forgot-password` | `PasswordResetLinkController@store` | guest |
| GET | `/reset-password/{token}` | `NewPasswordController@create` | guest |
| POST | `/reset-password` | `NewPasswordController@store` | guest |
| GET | `/verify-email` | `EmailVerificationPromptController` | auth |
| GET | `/verify-email/{id}/{hash}` | `VerifyEmailController` | auth, signed |
| POST | `/email/verification-notification` | `EmailVerificationNotificationController@store` | auth |
| GET | `/confirm-password` | `ConfirmablePasswordController@show` | auth |
| POST | `/confirm-password` | `ConfirmablePasswordController@store` | auth |
| PUT | `/password` | `PasswordController@update` | auth |
| POST | `/logout` | `AuthenticatedSessionController@destroy` | auth |

## 4. Main Application Routes (Auth Group)

### 4.1 Profile

| Method | URI | Handler |
|--------|-----|---------|
| GET | `/profile` | `ProfileController@edit` |
| PATCH | `/profile` | `ProfileController@update` |
| DELETE | `/profile` | `ProfileController@destroy` |
| GET | `/logout` | `AuthenticatedSessionController@logout` |

### 4.2 Livewire Full-Page Components

| Method | URI | Component/Handler | Middleware |
|--------|-----|-------------------|-----------|
| GET | `/dashboard` | `Dashboard` (Livewire) | no-cache |
| GET | `/activity` | `ActivityController@index` | — |
| GET | `/coa-workspace` | `COAWorkspace` (Livewire) | — |
| POST | `/coa-workspace/export` | `COAWorkspaceController@exportData` | throttle:export |
| POST | `/coa-workspace/import` | `COAWorkspaceController@importStore` | throttle:import |
| GET | `/coa-workspace/template` | `COAWorkspaceController@downloadTemplate` | — |
| GET | `/jurnal-entry` | `JournalEntry` (Livewire) | — |
| POST | `/jurnal-entry` | `JournalEntryController@store` | — |
| GET | `/jurnaling` | `JurnalManager` (Livewire) | — |
| GET | `/jurnaling-list` | `JurnalList` (Livewire) | — |
| GET | `/jurnaling/export` | `JurnalingController@exportJurnaling` | throttle:export |
| GET | `/bukubesar` | `BukuBesar` (Livewire) | — |
| GET | `/bukubesar/export` | `BukuBesarController@exportExcel` | throttle:export |
| GET | `/neraca-saldo/{periode?}` | `NeracaSaldo` (Livewire) | — |
| GET | `/neraca-saldo/exportexcel/{periode_id}` | `NeracaSaldoController@exportExcel` | throttle:export |
| GET | `/neraca-saldo/exportpdf/{periode_id}` | `NeracaSaldoController@exportPdf` | throttle:export |
| GET | `/saldo-awal` | `SaldoAwal` (Livewire) | — |
| GET | `/periodes` | `PeriodeManager` (Livewire) | — |
| GET | `/otorisator` | `OtorisatorManager` (Livewire) | — |
| GET | `/users` | `UserManager` (Livewire) | — |
| GET | `/posting` | `Posting` (Livewire) | — |
| POST | `/posting` | `PostingControllerRootSuperuser@postJurnal` | throttle:posting |

### 4.3 Module Hub Pages (View-only)

| Method | URI | View |
|--------|-----|------|
| GET | `/master-data` | `modules.master-data.index` |
| GET | `/transactions` | `modules.transactions.index` |
| GET | `/reports` | `modules.reports.index` |
| GET | `/finance` | `modules.finance.index` |
| GET | `/administration` | `modules.administration.index` |
| GET | `/settings` | `modules.settings.index` |

## 5. Legacy Role-Prefixed Routes (`/rootsuperuser/...`)

Protected by `role:rootsuperuser` middleware. Kept for backward compatibility.

### 5.1 COA Management

| Method | URI | Handler |
|--------|-----|---------|
| GET | `/rootsuperuser/account/header` | `HeaderController@index` |
| GET | `/rootsuperuser/account/header/create` | `HeaderController@create` |
| POST | `/rootsuperuser/account/header/save` | `HeaderController@save` |
| GET | `/rootsuperuser/account/header/edit/{id}` | `HeaderController@update` |
| PUT | `/rootsuperuser/account/header/update/{id}` | `HeaderController@updateSave` |
| GET | `/rootsuperuser/account/header/delete/{id}` | `HeaderController@delete` |
| GET | `/rootsuperuser/account/coa` | `CoaController@index` |
| GET | `/rootsuperuser/account/coa/create` | `CoaController@create` |
| POST | `/rootsuperuser/account/coa/save` | `CoaController@save` |
| GET | `/rootsuperuser/account/coa/edit/{id}` | `CoaController@update` |
| PUT | `/rootsuperuser/account/coa/update/{id}` | `CoaController@updateSave` |
| GET | `/rootsuperuser/account/coa/delete/{id}` | `CoaController@delete` |

### 5.2 Period Management

| Method | URI | Handler |
|--------|-----|---------|
| GET | `/rootsuperuser/periodes` | `PeriodeController@index` |
| GET | `/rootsuperuser/periodes/create` | `PeriodeController@create` |
| POST | `/rootsuperuser/periodes/save` | `PeriodeController@save` |
| GET | `/rootsuperuser/periodes/edit/{id}` | `PeriodeController@update` |
| PUT | `/rootsuperuser/periodes/update/{id}` | `PeriodeController@updateSave` |
| GET | `/rootsuperuser/periodes/delete/{id}` | `PeriodeController@delete` |

### 5.3 Journal Entries (6 sub-types)

| Method | URI | Handler |
|--------|-----|---------|
| GET | `/rootsuperuser/jurnaling` | `JurnalingController@index` |
| GET | `/rootsuperuser/jurnaling/kaskeluar` | `JurnalingController@indexkaskeluar` |
| GET | `/rootsuperuser/jurnaling/bankmasuk` | `JurnalingController@indexbankmasuk` |
| GET | `/rootsuperuser/jurnaling/bankkeluar` | `JurnalingController@indexbankkeluar` |
| GET | `/rootsuperuser/jurnaling/memorial` | `JurnalingController@indexmemorial` |
| GET | `/rootsuperuser/jurnaling/memorialpenutup` | `JurnalingController@indexmemorialpenutup` |
| POST | `/rootsuperuser/jurnaling/store` | `JurnalingController@store` |
| POST | `/rootsuperuser/jurnaling/storekaskeluar` | `JurnalingController@storekaskeluar` |
| POST | `/rootsuperuser/jurnaling/storebankmasuk` | `JurnalingController@storebankmasuk` |
| POST | `/rootsuperuser/jurnaling/storebankkeluar` | `JurnalingController@storebankkeluar` |
| POST | `/rootsuperuser/jurnaling/storememorial` | `JurnalingController@storememorial` |
| POST | `/rootsuperuser/jurnaling/storememorialpenutup` | `JurnalingController@storememorialpenutup` |
| POST | `/rootsuperuser/jurnaling/rekap/{periode_id}` | `JurnalingController@rekapJurnal` |
| POST | `/rootsuperuser/jurnaling/unrekap/{periode_id}` | `JurnalingController@unrekapJurnal` |
| GET | `/rootsuperuser/jurnaling/export` | `JurnalingController@exportJurnaling` |

### 5.4 General Ledger & Trial Balance

| Method | URI | Handler |
|--------|-----|---------|
| GET | `/rootsuperuser/bukubesar` | `BukuBesarController@showLedgerForm` |
| GET | `/rootsuperuser/bukubesar/showAll` | `BukuBesarController@showAll` |
| GET | `/rootsuperuser/bukubesar/export` | `BukuBesarController@exportExcel` |
| GET | `/rootsuperuser/neracasaldo/{periode_id}` | `NeracaSaldoController@index` |
| GET | `/rootsuperuser/neracasaldo/exportexcel/{periode_id}` | `NeracaSaldoController@exportExcel` |
| GET | `/rootsuperuser/neracasaldo/exportpdf/{periode_id}` | `NeracaSaldoController@exportPdf` |

### 5.5 Opening Balances, Posting, Otorisator

| Method | URI | Handler |
|--------|-----|---------|
| GET | `/rootsuperuser/saldoawal` | `SaldoAwalController@index` |
| POST | `/rootsuperuser/saldoawal/store` | `SaldoAwalController@store` |
| PUT | `/rootsuperuser/saldoawal/{id}/update` | `SaldoAwalController@update` |
| DELETE | `/rootsuperuser/saldoawal/{id}/destroy` | `SaldoAwalController@destroy` |
| GET | `/rootsuperuser/posting` | `PostingControllerRootSuperuser@index` |
| POST | `/rootsuperuser/posting` | `PostingControllerRootSuperuser@postJurnal` |
| GET | `/rootsuperuser/otorisator/home` | `OtorisatorController@index` |
| POST | `/rootsuperuser/otorisator/save` | `OtorisatorController@store` |
| PUT | `/rootsuperuser/otorisator/update/{id}` | `OtorisatorController@update` |
| DELETE | `/rootsuperuser/otorisator/delete/{id}` | `OtorisatorController@destroy` |

## 6. Throttling

| Key | Limit | Scope |
|-----|-------|-------|
| `throttle:export` | 30 req/min | Export endpoints |
| `throttle:import` | 10 req/min | Import endpoints |
| `throttle:posting` | 5 req/min | Period posting |
| `throttle:6,1` | 6 req/min | Email verification |

## 7. Livewire Component ↔ Route Mapping

| Livewire Component | Route | Full-page? |
|-------------------|-------|-----------|
| `Dashboard` | `/dashboard` | yes |
| `COAWorkspace` | `/coa-workspace` | yes |
| `JournalEntry` | `/jurnal-entry` | yes |
| `JurnalManager` | `/jurnaling` | yes |
| `JurnalList` | `/jurnaling-list` | yes |
| `BukuBesar` | `/bukubesar` | yes |
| `NeracaSaldo` | `/neraca-saldo/{periode?}` | yes |
| `SaldoAwal` | `/saldo-awal` | yes |
| `PeriodeManager` | `/periodes` | yes |
| `OtorisatorManager` | `/otorisator` | yes |
| `UserManager` | `/users` | yes |
| `Posting` | `/posting` | yes |
