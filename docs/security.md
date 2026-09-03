# Security — DAPENSE

> Authentication, authorization, RBAC, and threat model.

## 1. Authentication

### 1.1 Laravel Breeze

| Feature | Implementation |
|---------|---------------|
| Scaffolding | Laravel Breeze 2.x |
| Driver | Session-based (database) |
| Password hashing | Bcrypt (12 rounds) |
| Email verification | Required (`MustVerifyEmail` on User model) |
| Password reset | Email-based token flow |
| Session lifetime | 120 minutes |
| Session encryption | Enabled (`SESSION_ENCRYPT=true`) |

### 1.2 Auth Routes

| Route | Method | Purpose |
|-------|--------|---------|
| `/login` | GET/POST | Login form + authenticate |
| `/register` | GET/POST | Registration form + create user |
| `/logout` | GET/POST | Destroy session |
| `/forgot-password` | GET/POST | Password reset request |
| `/reset-password/{token}` | GET/POST | Password reset form |
| `/verify-email` | GET | Email verification prompt |
| `/verify-email/{id}/{hash}` | GET | Email verification confirm |
| `/confirm-password` | GET/POST | Password confirmation |
| `/demo-login` | GET | Auto-create demo user (rootsuperuser) |

### 1.3 User Model

```php
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, LogsActivity, Notifiable;

    protected $fillable = ['name', 'email', 'usertype', 'status', 'password', 'image'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isActive(): bool
    {
        return $this->status == 1;
    }
}
```

## 2. Role-Based Access Control (RBAC)

### 2.1 Role Hierarchy

| Role | Description | Privileges |
|------|-------------|-----------|
| `rootsuperuser` | System administrator | Full access + user delete + posting |
| `admin` | Administrative user | Full access except user delete |
| `operator` | Operational user | Transactions, reports, COA, periods |
| `bod` | Board of Directors | Read-only dashboards + reports |

### 2.2 Authorization Layers

DAPENSE uses **three layers** of authorization:

```
┌─────────────────────────────────────────────┐
│ Layer 1: Middleware (Route-level)            │
│   CheckRole → usertype matches route prefix │
├─────────────────────────────────────────────┤
│ Layer 2: Policies (Model-level)             │
│   8 Policies → view/create/update/delete    │
├─────────────────────────────────────────────┤
│ Layer 3: Gates (Feature-level)              │
│   4 Gates → export/import/post/manage-users │
└─────────────────────────────────────────────┘
```

### 2.3 Layer 1: CheckRole Middleware

```php
// bootstrap/app.php
$middleware->alias([
    'role' => CheckRole::class,
]);

// Usage in routes
Route::middleware(['auth', 'role:rootsuperuser'])->prefix('rootsuperuser')->group(function () {
    // Only rootsuperuser can access
});
```

**Implementation:**
```php
class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (Auth::user()->usertype !== $role) {
            return redirect('/');
        }
        return $next($request);
    }
}
```

### 2.4 Layer 2: Policies (8 Policies)

| Policy | Model | Key Permissions |
|--------|-------|----------------|
| `JournalPolicy` | `Jurnaling` | viewAny/view: all roles; create/update/delete: root, admin, operator; post/rekap: root, admin |
| `UserPolicy` | `User` | viewAny/create/update: root, admin; delete: root only (not self) |
| `LedgerPolicy` | `NeracaSaldo` | viewAny/view: all roles |
| `PeriodePolicy` | `Periode` | viewAny/create/update/delete: root, admin, operator |
| `SaldoAwalPolicy` | `SaldoAwal` | viewAny/create/update/delete: root, admin, operator |
| `OtorisatorPolicy` | `Otorisator` | viewAny/create/update/delete: root, admin, operator |
| `ReportPolicy` | — | viewAny: all roles |
| `SettingPolicy` | — | viewAny: root, admin, operator |

### 2.5 Layer 3: Gates (4 Gates)

```php
// AuthServiceProvider.php
Gate::define('export-journal', fn(User $user) =>
    in_array($user->usertype, ['rootsuperuser', 'admin', 'operator', 'bod'])
);

Gate::define('import-data', fn(User $user) =>
    in_array($user->usertype, ['rootsuperuser', 'admin', 'operator'])
);

Gate::define('post-journal', fn(User $user) =>
    in_array($user->usertype, ['rootsuperuser', 'admin'])
);

Gate::define('manage-users', fn(User $user) =>
    in_array($user->usertype, ['rootsuperuser', 'admin'])
);
```

### 2.6 Layer 4: Livewire HasRole Trait

```php
trait HasRole
{
    public function canAccess(string $feature): bool
    {
        $permissions = [
            'dashboard' => ['rootsuperuser', 'admin', 'operator', 'bod'],
            'master-data' => ['rootsuperuser', 'admin', 'operator'],
            // ... 15 features total
        ];
        return in_array($this->role(), $permissions[$feature] ?? []);
    }
}
```

### 2.7 Permission Matrix

| Feature | rootsuperuser | admin | operator | bod |
|---------|:------------:|:-----:|:--------:|:---:|
| dashboard | ✓ | ✓ | ✓ | ✓ |
| master-data | ✓ | ✓ | ✓ | ✗ |
| transactions | ✓ | ✓ | ✓ | ✗ |
| reports | ✓ | ✓ | ✓ | ✓ |
| finance | ✓ | ✓ | ✓ | ✓ |
| administration | ✓ | ✓ | ✗ | ✗ |
| settings | ✓ | ✓ | ✓ | ✗ |
| jurnal-entry | ✓ | ✓ | ✓ | ✗ |
| jurnaling | ✓ | ✓ | ✓ | ✗ |
| bukubesar | ✓ | ✓ | ✓ | ✓ |
| neracasaldo | ✓ | ✓ | ✓ | ✓ |
| posting | ✓ | ✓ | ✗ | ✗ |
| otorisator | ✓ | ✓ | ✓ | ✗ |
| users | ✓ | ✓ | ✗ | ✗ |
| saldoawal | ✓ | ✓ | ✓ | ✗ |

## 3. Security Headers

### 3.1 Middleware (Global)

```php
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), interest-cohort=()');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; ...");
        return $response;
    }
}
```

### 3.2 Nginx Headers

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "0" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), interest-cohort=()" always;
server_tokens off;
```

### 3.3 Bfcache Prevention

```php
class PreventBfcache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}
```

## 4. Threat Model

### 4.1 Mitigated Threats

| Threat | Mitigation | Layer |
|--------|-----------|-------|
| **SQL Injection** | Eloquent ORM + parameterized queries | Data |
| **XSS (Cross-Site Scripting)** | Blade `{{ }}` auto-escaping + CSP header | Transport |
| **CSRF** | Laravel CSRF token middleware | Middleware |
| **Session Hijacking** | Encrypted sessions, database driver | Session |
| **Brute Force** | Throttle middleware on auth routes | Middleware |
| **Privilege Escalation** | 4-layer RBAC (middleware + policy + gate + trait) | Authorization |
| **Clickjacking** | `X-Frame-Options: SAMEORIGIN` | Transport |
| **MIME Sniffing** | `X-Content-Type-Options: nosniff` | Transport |
| **Back/Forward Cache** | `PreventBfcache` middleware | Middleware |
| **Sensitive File Access** | Nginx deny rules for `.env`, `.git`, `artisan` | Transport |
| **Container Escape** | `read_only: true`, `cap_drop: ALL`, `no-new-privileges` | Infrastructure |
| **Backup Theft** | AES-256-CBC encryption with PBKDF2 | Infrastructure |
| **Data Leakage** | Activity logging (Spatie) for audit trail | Domain |

### 4.2 Open Risks

| Risk | Status | Notes |
|------|--------|-------|
| Rate limiting (global) | Partial | Only export/import/posting throttled; no global rate limit |
| 2FA/MFA | Not implemented | Consider for production |
| API token auth | Not implemented | Session-only |
| Content Security Policy | Implemented | `'unsafe-inline'` and `'unsafe-eval'` in script-src |
| HTTPS enforcement | Not configured | HSTS header commented out in nginx.conf |
| Database encryption at rest | Not configured | Depends on hosting provider |
| Password complexity rules | Not enforced | Only min length via Breeze defaults |

## 5. Docker Security

### 5.1 Container Hardening

```yaml
# docker-compose.yml
services:
  app:
    read_only: true
    cap_drop:
      - ALL
    cap_add:
      - NET_BIND_SERVICE
      - SETGID
      - SETUID
      - CHOWN
      - DAC_OVERRIDE
    security_opt:
      - no-new-privileges:true
    tmpfs:
      - /var/www/html/storage/framework/views
      - /var/www/html/storage/framework/cache
      - /var/www/html/storage/framework/sessions
      - /var/www/html/storage/logs
      - /var/www/html/bootstrap/cache
      - /var/log/nginx
      - /var/lib/nginx
      - /var/lib/redis
      - /tmp
```

### 5.2 Network Isolation

```yaml
networks:
  internal:
    name: dapense-internal
# No external network — MySQL is not exposed to host
```

### 5.3 Nginx Security

```nginx
# Deny hidden files
location ~ /\. {
    deny all;
}

# Deny sensitive files
location ~ (\.env|\.git|composer\.json|composer\.lock|artisan) {
    deny all;
}

# Limit request size
client_max_body_size 20M;
```

## 6. Backup Security

| Component | Implementation |
|-----------|---------------|
| Encryption | AES-256-CBC with PBKDF2 key derivation |
| Key storage | `BACKUP_ENCRYPTION_KEY` environment variable |
| Compression | gzip before encryption |
| Retention | 30-day automatic cleanup |
| Script | `docker/backup.sh` |

```bash
# Backup pipeline
mysqldump --single-transaction ... | gzip | openssl enc -aes-256-cbc -salt -pbkdf2 -out backup.sql.enc
```

## 7. Audit Trail

| Feature | Implementation |
|---------|---------------|
| Package | Spatie Laravel Activity Log 4.x |
| Logged models | User (name, email, usertype, status changes) |
| Log options | `logOnlyDirty()`, `dontSubmitEmptyLogs()` |
| Storage | `activity_log` table |
| Batch tracking | `batch_uuid` column for grouped operations |

## 8. Recommendations for Production

| Priority | Recommendation |
|----------|---------------|
| High | Enable HTTPS + HSTS header |
| High | Add global rate limiting (not just export/import) |
| High | Enforce password complexity rules |
| Medium | Implement 2FA for admin/root roles |
| Medium | Add Content Security Policy reporting endpoint |
| Medium | Enable database encryption at rest |
| Low | Add API token authentication for future integrations |
| Low | Implement RBAC audit logging |
