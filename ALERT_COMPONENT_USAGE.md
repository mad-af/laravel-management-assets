# Alert Component Usage

Komponen Alert Livewire untuk menampilkan notifikasi pop-up di kanan atas halaman.

## Fitur

- ✅ Pop-up alert di kanan atas
- ✅ 4 tipe alert: success, error, warning, info
- ✅ Auto-hide dengan delay yang dapat dikustomisasi
- ✅ Animasi smooth masuk dan keluar
- ✅ Tombol close manual
- ✅ Multiple alerts sekaligus
- ✅ Responsive design

## Instalasi

Komponen sudah otomatis tersedia di semua halaman dashboard karena sudah ditambahkan ke `layouts/dashboard.blade.php`.

## Cara Penggunaan

### 1. Menggunakan Trait WithAlert (Recommended)

Tambahkan trait `WithAlert` ke Livewire component Anda:

```php
<?php

namespace App\Livewire\YourComponent;

use App\Traits\WithAlert;
use Livewire\Component;

class YourComponent extends Component
{
    use WithAlert;

    public function saveData()
    {
        // Your logic here...
        
        // Show success alert
        $this->showSuccessAlert('Data berhasil disimpan!', 'Berhasil');
        
        // Or show error alert
        $this->showErrorAlert('Terjadi kesalahan!', 'Error');
        
        // Or show warning alert
        $this->showWarningAlert('Peringatan: Data akan dihapus!', 'Peringatan');
        
        // Or show info alert
        $this->showInfoAlert('Informasi penting', 'Info');
    }
}
```

### 2. Menggunakan Dispatch Event Langsung

```php
// Di dalam method Livewire component
$this->dispatch('showAlert', 'success', 'Pesan sukses', 'Judul Opsional');
$this->dispatch('showAlert', 'error', 'Pesan error');
$this->dispatch('showAlert', 'warning', 'Pesan warning');
$this->dispatch('showAlert', 'info', 'Pesan info');
```

### 3. Dari JavaScript/Alpine.js

```javascript
// Menggunakan Livewire dispatch
Livewire.dispatch('showAlert', {
    type: 'success',
    message: 'Operasi berhasil!',
    title: 'Sukses'
});

// Atau menggunakan $dispatch Alpine.js
$dispatch('showAlert', {
    type: 'error',
    message: 'Terjadi kesalahan!'
});
```

### 4. Dari Controller (via Session)

Untuk menampilkan alert setelah redirect, Anda bisa menggunakan session flash:

```php
// Di Controller
public function store(Request $request)
{
    // Your logic here...
    
    return redirect()->route('dashboard')
        ->with('alert', [
            'type' => 'success',
            'message' => 'Data berhasil disimpan!',
            'title' => 'Berhasil'
        ]);
}
```

Kemudian di blade template, tambahkan script untuk menangkap session:

```blade
@if(session('alert'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.dispatch('showAlert', 
                '{{ session('alert.type') }}', 
                '{{ session('alert.message') }}', 
                '{{ session('alert.title') }}'
            );
        });
    </script>
@endif
```

## Kustomisasi

### Mengubah Auto-hide Delay

```blade
<!-- Di layout atau halaman tertentu -->
<livewire:alert :auto-hide="true" :hide-delay="3000" />
```

### Menonaktifkan Auto-hide

```blade
<livewire:alert :auto-hide="false" />
```

## Tipe Alert

| Tipe | Class CSS | Icon | Warna |
|------|-----------|------|-------|
| `success` | `alert-success` | `check-circle` | Hijau |
| `error` | `alert-error` | `x-circle` | Merah |
| `warning` | `alert-warning` | `alert-triangle` | Kuning |
| `info` | `alert-info` | `info` | Biru |

## Event Listeners

Komponen Alert mendengarkan event berikut:

- `showAlert` - Menampilkan alert baru
- `hideAlert` - Menyembunyikan alert tertentu
- `clearAlerts` - Menghapus semua alert

## Contoh Implementasi

Lihat contoh implementasi di `app/Livewire/Assets/Table.php` method `deleteAsset()`.

## Styling

Komponen menggunakan DaisyUI classes dan Tailwind CSS. Alert akan muncul di posisi `fixed top-4 right-4` dengan z-index 50.

## Dependencies

- Livewire 3.x
- Alpine.js
- DaisyUI
- Lucide Icons