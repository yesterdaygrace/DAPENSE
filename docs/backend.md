# Backend Standards — DAPENSE

> PHP/Laravel coding conventions, patterns, and implementation guidelines.

> **Note:** The user originally requested "Go implementation standards." DAPENSE is a **Laravel 13 / PHP 8.3** project. This document covers the actual backend stack.

## 1. Language & Framework

| Component | Version | Requirement |
|-----------|---------|-------------|
| PHP | 8.3 | `^8.3` (composer.json) |
| Laravel | 13.x | `^13.0` |
| Livewire | 4.x | `^4.0` |
| Larastan | 3.x | Static analysis |

## 2. Code Style

### 2.1 Formatting

| Tool | Config | Purpose |
|------|--------|---------|
| Laravel Pint | `./vendor/bin/pint` | Code style (PSR-12 based) |
| Larastan | `./vendor/bin/phpstan analyse` | Static analysis (level 5+) |

```bash
# Check style
composer lint          # Pint --test
composer lint-fix      # Pint (auto-fix)

# Static analysis
composer analyse       # PHPStan
composer analyse-verbose  # PHPStan --debug

# Both
composer check         # lint + analyse + test
```

### 2.2 Naming Conventions

| Element | Convention | Example |
|---------|-----------|---------|
| Classes | PascalCase | `COAWorkspace`, `JournalEntryController` |
| Methods | camelCase | `exportData()`, `storePost()` |
| Variables | camelCase | `$headerCoaId`, `$periode` |
| Properties | snake_case | `kode_akun`, `tanggal_jurnal` |
| Database columns | snake_case | `header_coa_id`, `saldo_normal` |
| Routes | kebab-case | `/coa-workspace`, `/jurnal-entry` |
| Blade views | dot notation | `modules.master-data.index` |

### 2.3 PHP 8.3 Features

```php
// Match expressions (preferred over switch)
return match ($this->role()) {
    'rootsuperuser' => 'Root Superuser',
    'bod' => 'BOD',
    'operator' => 'Operator',
    default => 'Admin',
};

// Typed properties
protected string $table = 'coas';

// Readonly properties (where applicable)
readonly Periode $periode;

// Enums (for saldo_normal)
enum SaldoNormal: string {
    case Debet = 'debet';
    case Kredit = 'kredit';
}
```

## 3. Project Structure

```
app/
├── Exports/              # Maatwebsite Excel export classes
│   └── COAExport.php
├── Http/
│   ├── Controllers/      # Traditional controllers (legacy)
│   │   ├── Auth/         # Breeze auth controllers (8)
│   │   ├── Base/         # Core accounting controllers (8)
│   │   ├── Modules/      # Module controllers (2)
│   │   ├── admin/        # Admin-only controllers
│   │   └── rootsuperuser/ # Root-only controllers
│   ├── Middleware/        # 3 custom middleware
│   │   ├── CheckRole.php
│   │   ├── SecurityHeaders.php
│   │   └── PreventBfcache.php
│   └── Requests/         # Form request validation
├── Imports/              # Maatwebsite Excel import classes
│   └── COAImport.php
├── Livewire/             # Livewire components (12)
│   ├── Concerns/         # Shared traits
│   │   └── HasRole.php
│   ├── Dashboard.php
│   ├── COAWorkspace.php
│   ├── JournalEntry.php
│   ├── JurnalManager.php
│   ├── JurnalList.php
│   ├── BukuBesar.php
│   ├── NeracaSaldo.php
│   ├── SaldoAwal.php
│   ├── PeriodeManager.php
│   ├── OtorisatorManager.php
│   ├── UserManager.php
│   └── Posting.php
├── Models/               # Eloquent models (8)
│   ├── User.php
│   ├── HeaderCOA.php
│   ├── COA.php
│   ├── Jurnaling.php
│   ├── Periode.php
│   ├── SaldoAwal.php
│   ├── NeracaSaldo.php
│   └── Otorisator.php
├── Policies/             # Authorization policies (8)
│   ├── JournalPolicy.php
│   ├── LedgerPolicy.php
│   ├── OtorisatorPolicy.php
│   ├── PeriodePolicy.php
│   ├── ReportPolicy.php
│   ├── SaldoAwalPolicy.php
│   ├── SettingPolicy.php
│   └── UserPolicy.php
├── Providers/            # Service providers (2)
│   ├── AppServiceProvider.php
│   └── AuthServiceProvider.php
└── View/                 # View composers
```

## 4. Livewire Component Patterns

### 4.1 Component Structure

```php
namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use Livewire\Component;

class COAWorkspace extends Component
{
    use HasRole;

    // State properties
    public ?int $selectedHeaderId = null;
    public string $search = '';

    // Lifecycle
    public function mount(): void
    {
        $this->authorize('master-data'); // HasRole::canAccess()
    }

    // Actions
    public function save(): void
    {
        $this->validate([...]);
        // ... persist
    }

    // Render
    public function render()
    {
        return view('livewire.c-o-a-workspace');
    }
}
```

### 4.2 HasRole Trait

All Livewire components use the `HasRole` trait for permission checks:

```php
// Check feature access
$this->authorize('master-data'); // calls canAccess('master-data')

// Check specific role
if ($this->isRole(['rootsuperuser', 'admin'])) { ... }

// Get route prefix
$url = $this->routePrefix(); // 'rootsuperuser', 'admin', etc.
```

### 4.3 Feature Permission Matrix

| Feature | rootsuperuser | admin | operator | bod |
|---------|:------------:|:-----:|:--------:|:---:|
| dashboard | ✓ | ✓ | ✓ | ✓ |
| master-data | ✓ | ✓ | ✓ | ✗ |
| transactions | ✓ | ✓ | ✓ | ✗ |
| reports | ✓ | ✓ | ✓ | ✓ |
| finance | ✓ | ✓ | ✓ | ✓ |
| administration | ✓ | ✓ | ✗ | ✗ |
| settings | ✓ | ✓ | ✓ | ✗ |
| posting | ✓ | ✓ | ✗ | ✗ |
| users | ✓ | ✓ | ✗ | ✗ |

## 5. Model Conventions

### 5.1 Model Structure

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class COA extends Model
{
    use HasFactory;

    protected $table = 'coas';

    protected $fillable = [
        'kode_akun', 'nama_akun', 'saldo_normal', 'kategori', 'level', 'header_coa_id',
    ];

    public function headerCoa(): BelongsTo
    {
        return $this->belongsTo(HeaderCOA::class, 'header_coa_id');
    }

    public function jurnalings(): HasMany
    {
        return $this->hasMany(Jurnaling::class, 'coa_id', 'id');
    }
}
```

### 5.2 Cast Rules

| Column Type | Cast | Example |
|-------------|------|---------|
| `decimal(15,2)` | `'decimal:2'` | `debit`, `kredit` |
| `timestamp` | `'datetime'` | `email_verified_at` |
| `password` | `'hashed'` | `password` |
| `boolean` | `'boolean'` | `is_rekap` |

### 5.3 Relationship Conventions

- Always specify FK explicitly: `$this->belongsTo(COA::class, 'coa_id')`
- Use descriptive method names: `headerCoa()`, `jurnaling()`, `periode()`
- Self-references: `parent()` / `children()` for hierarchical models

## 6. Controller Conventions

### 6.1 Legacy Controller Pattern

```php
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index()
    {
        $coas = COA::with('headerCoa')->get();
        return view('coa.index', compact('coas'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'kode_akun' => 'required|string|max:50|unique:coas,kode_akun',
            'nama_akun' => 'required|string|max:255',
            // ...
        ]);
        COA::create($validated);
        return redirect()->route('coa.index')->with('success', 'COA created');
    }
}
```

### 6.2 Livewire vs Controller Decision

| Use Case | Approach |
|----------|----------|
| Reactive CRUD with filtering | Livewire component |
| Form submissions | Livewire or controller |
| Export endpoints | Controller (streaming) |
| Import endpoints | Controller (Maatwebsite) |
| Simple views | Controller |

## 7. Import/Export Patterns

### 7.1 Maatwebsite Excel

```php
// Export
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;

class COAExport implements FromCollection
{
    public function collection()
    {
        return COA::with('headerCoa')->get();
    }
}

// Import
namespace App\Imports;
use Maatwebsite\Excel\Concerns\ToModel;

class COAImport implements ToModel
{
    public function model(array $row)
    {
        return new COA([
            'kode_akun' => $row['kode_akun'],
            'nama_akun' => $row['nama_akun'],
            // ...
        ]);
    }
}
```

## 8. Validation Patterns

### 8.1 Form Request Validation

```php
// In controller
$validated = $request->validate([
    'kode_akun' => 'required|string|max:50|unique:coas,kode_akun',
    'nama_akun' => 'required|string|max:255',
    'debit' => 'required|numeric|min:0',
    'kredit' => 'required|numeric|min:0',
]);
```

### 8.2 Livewire Validation

```php
public function rules(): array
{
    return [
        'kode_akun' => 'required|string|max:50|unique:coas,kode_akun',
        'nama_akun' => 'required|string|max:255',
    ];
}
```

## 9. Error Handling

| Pattern | Implementation |
|---------|---------------|
| Model validation | Eloquent + custom rules |
| Form validation | `$request->validate()` / Livewire `validate()` |
| Authorization | Policies + Gates + HasRole trait |
| Exception rendering | JSON for `api/*` or `expectsJson()` |
| Duplicate prevention | Unique constraints + application checks |
| Activity logging | Spatie Activity Log (`LogsActivity` trait) |

## 10. Testing Standards

| Tool | Purpose |
|------|---------|
| Pest 4 | Test framework |
| RefreshDatabase | Isolated test databases |
| Livewire testing | `Livewire::test(Component::class)` |

```bash
composer test          # Run all tests
composer test-coverage # With coverage report
```

See `docs/testing.md` for full conventions.
