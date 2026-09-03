# Testing — DAPENSE

> Testing conventions, patterns, and toolchain.

## 1. Test Framework

| Component | Version | Purpose |
|-----------|---------|---------|
| Pest | 4.x | Test framework (PHPUnit wrapper) |
| PHPUnit | 10.x | Underlying test runner |
| RefreshDatabase | — | Isolated test databases |
| Laravel Sail | 1.x | Docker test environment |

## 2. Configuration

### 2.1 phpunit.xml

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory>tests/Feature</directory>
    </testsuite>
</testsuites>
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="APP_MAINTENANCE_DRIVER" value="file"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="CACHE_STORE" value="array"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
</php>
```

### 2.2 Pest Bootstrap

```php
// tests/Pest.php
uses(TestCase::class, RefreshDatabase::class)->in('Feature');
```

## 3. Test Structure

```
tests/
├── Pest.php              # Pest configuration + helpers
├── TestCase.php          # Base test case
├── Unit/
│   └── ExampleTest.php   # Unit test placeholder
└── Feature/
    ├── Auth/
    │   ├── AuthenticationTest.php
    │   ├── EmailVerificationTest.php
    │   ├── PasswordConfirmationTest.php
    │   ├── PasswordResetTest.php
    │   ├── PasswordUpdateTest.php
    │   └── RegistrationTest.php
    ├── AuthorizationTest.php
    ├── DashboardTotalsTest.php
    ├── ExampleTest.php
    ├── HttpTest.php
    ├── ModuleTest.php
    ├── ProfileTest.php
    ├── UiConsistencyTest.php
    ├── UiStateTest.php
    └── ValidationTest.php
```

## 4. Test Categories

### 4.1 Authentication Tests (6 files)

| Test | Asserts |
|------|---------|
| `AuthenticationTest` | Login form renders, login works, invalid credentials fail |
| `RegistrationTest` | Registration form renders, registration works |
| `EmailVerificationTest` | Verification prompt renders, verification works |
| `PasswordResetTest` | Reset form renders, reset works |
| `PasswordUpdateTest` | Password update works |
| `PasswordConfirmationTest` | Confirmation form renders, confirmation works |

### 4.2 Authorization Tests

```php
test('admin can access admin dashboard', function () {
    $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();
});

test('operator cannot access admin-specific dashboard route', function () {
    $this->actingAs($this->operator)->get('/admin/dashboard')->assertRedirect('/');
});

test('bod cannot access master-data modules', function () {
    $this->actingAs($this->bod)->get('/periodes')->assertForbidden();
    $this->actingAs($this->bod)->get('/coa-workspace')->assertForbidden();
    $this->actingAs($this->bod)->get('/users')->assertForbidden();
});

test('unauthenticated user is redirected to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});
```

### 4.3 Livewire Component Tests

```php
test('admin can access user management', function () {
    $this->actingAs($this->admin)->get('/users')->assertOk();
    Livewire::test(UserManager::class)->assertOk();
});

test('rootsuperuser can access posting module', function () {
    $this->actingAs($this->root)->get('/posting')->assertOk();
    Livewire::test(Posting::class)->assertOk();
});
```

### 4.4 Feature Tests

| Test | Purpose |
|------|---------|
| `DashboardTotalsTest` | Verify dashboard aggregates |
| `ModuleTest` | Module page accessibility |
| `HttpTest` | HTTP response codes |
| `ProfileTest` | Profile CRUD |
| `UiConsistencyTest` | UI component rendering |
| `UiStateTest` | UI state management |
| `ValidationTest` | Form validation rules |

## 5. Writing Tests

### 5.1 Pest Syntax

```php
// Basic test
test('something works', function () {
    expect(true)->toBeTrue();
});

// With setup
beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
});

test('admin can access dashboard', function () {
    $this->actingAs($this->admin)->get('/dashboard')->assertOk();
});
```

### 5.2 Livewire Testing

```php
use App\Livewire\COAWorkspace;
use Livewire\Livewire;

test('COA workspace renders', function () {
    $this->actingAs($this->admin);
    Livewire::test(COAWorkspace::class)->assertOk();
});
```

### 5.3 Database Setup

```php
// RefreshDatabase trait ensures clean state
// No manual truncate needed

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->operator = User::factory()->create(['usertype' => 'operator', 'status' => 1]);
    $this->bod = User::factory()->create(['usertype' => 'bod', 'status' => 1]);
    $this->root = User::factory()->create(['usertype' => 'rootsuperuser', 'status' => 1]);
});
```

## 6. Running Tests

```bash
# All tests
composer test

# With coverage
composer test-coverage

# Specific suite
./vendor/bin/pest --testsuite=Feature
./vendor/bin/pest --testsuite=Unit

# Specific file
./vendor/bin/pest tests/Feature/AuthorizationTest.php

# Filter by name
./vendor/bin/pest --filter="admin can access"
```

## 7. Test Conventions

| Convention | Rule |
|-----------|------|
| **Naming** | `test('descriptive snake_case sentence')` |
| **One assertion per concept** | Each test verifies one behavior |
| **User setup** | Use `beforeEach()` for role-based user creation |
| **Livewire** | Use `Livewire::test(Component::class)` |
| **Auth** | Use `$this->actingAs($user)` |
| **Assertions** | `assertOk()`, `assertForbidden()`, `assertRedirect()` |
| **Database** | `RefreshDatabase` trait; no manual cleanup |

## 8. Test Data

### 8.1 User Factories

```php
User::factory()->create(['usertype' => 'rootsuperuser', 'status' => 1]);
User::factory()->create(['usertype' => 'admin', 'status' => 1]);
User::factory()->create(['usertype' => 'operator', 'status' => 1]);
User::factory()->create(['usertype' => 'bod', 'status' => 1]);
User::factory()->create(['usertype' => 'admin', 'status' => 0]); // inactive
```

### 8.2 Seed Data (for integration tests)

```bash
php artisan db:seed           # Run DatabaseSeeder
php artisan db:seed --class=JurnalCoaSeeder  # Seed 100 COAs + journals
```

## 9. Coverage Targets

| Category | Target | Notes |
|----------|--------|-------|
| Authentication | 100% | All auth flows covered |
| Authorization | 100% | All 4 roles × all protected routes |
| Livewire components | 80%+ | At least render test per component |
| Unit tests | Growing | Model methods, helpers |

## 10. CI Integration

```yaml
# GitHub Actions
- name: Run Tests
  run: composer test

- name: Run Lint
  run: composer lint

- name: Run Static Analysis
  run: composer analyse

- name: Full Check
  run: composer check  # lint + analyse + test
```
