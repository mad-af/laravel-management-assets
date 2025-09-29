

**1) Enum untuk kunci session**

```php
// app/Support/SessionKey.php
namespace App\Support;

enum SessionKey: string
{
    case BranchId = 'ctx.branch_id';   // pakai dot biar rapi namespace
    case Theme    = 'ctx.theme';
}
```

**2) Helper kecil (opsional, biar pemanggilan pendek)**

```php
// app/Support/session_helpers.php
use App\Support\SessionKey;

function session_get(SessionKey $key, $default = null) {
    return session($key->value, $default);
}

function session_put(SessionKey $key, $value): void {
    session([$key->value => $value]);
}
```

Daftarkan `session_helpers.php` via `composer.json` → autoload `"files"`.

**Pemakaian di mana pun (Livewire, controller, dsb.)**

```php
use App\Support\SessionKey;

// get
$branchId = session_get(SessionKey::BranchId) ?? auth()->user()->current_branch_id;

// set
session_put(SessionKey::BranchId, $this->branch_id);
```

> Keuntungan: satu sumber kebenaran nama kunci (via Enum), auto-complete IDE, minim typo.

