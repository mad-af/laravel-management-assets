# Info Grid Component Usage

Komponen `<x-info-grid>` adalah komponen Blade yang general untuk menampilkan informasi dalam format grid dengan label dan value.

## Props

- `title` (optional): Judul card
- `icon` (optional): Icon untuk card (menggunakan heroicon)
- `items` (array): Array berisi data yang akan ditampilkan
- `description` (optional): Deskripsi tambahan di bawah grid
- `columns` (optional): Class CSS untuk mengatur kolom grid (default: 'md:grid-cols-2')

## Format Item

Setiap item dalam array `items` dapat memiliki properti:

- `label` (required): Label untuk field
- `value` (required): Value yang akan ditampilkan
- `class` (optional): CSS class tambahan untuk value
- `mono` (optional): Boolean, jika true akan menggunakan font monospace
- `badge` (optional): Boolean, jika true akan menampilkan sebagai badge
- `badge_class` (optional): CSS class untuk badge

## Contoh Penggunaan

### Basic Usage
```blade
<x-info-grid 
    title="User Information"
    :items="[
        ['label' => 'Name', 'value' => $user->name, 'class' => 'font-medium'],
        ['label' => 'Email', 'value' => $user->email],
        ['label' => 'Role', 'value' => $user->role, 'badge' => true, 'badge_class' => 'badge-primary']
    ]"
/>
```

### With Icon and Description
```blade
<x-info-grid 
    title="Asset Details"
    icon="heroicon-o-cube"
    :items="[
        ['label' => 'Asset Code', 'value' => $asset->code, 'mono' => true],
        ['label' => 'Status', 'value' => $asset->status, 'badge' => true]
    ]"
    :description="$asset->notes"
/>
```

### Custom Grid Columns
```blade
<x-info-grid 
    title="Contact Information"
    columns="md:grid-cols-3"
    :items="[
        ['label' => 'Phone', 'value' => $contact->phone],
        ['label' => 'Email', 'value' => $contact->email],
        ['label' => 'Address', 'value' => $contact->address]
    ]"
/>
```

### With Additional Content (Slot)
```blade
<x-info-grid 
    title="Product Details"
    :items="$productItems"
>
    <div class="mt-4">
        <button class="btn btn-primary">Edit Product</button>
    </div>
</x-info-grid>
```

## Keuntungan

1. **Reusable**: Dapat digunakan di berbagai tempat dengan data yang berbeda
2. **Flexible**: Mendukung berbagai tipe tampilan (text, badge, monospace)
3. **Consistent**: Menggunakan style yang konsisten dengan design system
4. **Maintainable**: Perubahan style cukup dilakukan di satu tempat
5. **Extensible**: Dapat ditambahkan slot untuk konten tambahan