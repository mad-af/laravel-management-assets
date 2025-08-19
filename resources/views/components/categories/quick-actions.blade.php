@props([
    'categorie',
    'class' => ''
])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h2 class="mb-4 text-lg font-semibold card-title">Aksi Cepat</h2>
        
        <div class="space-y-3">
            <a href="{{ route('categories.edit', $categorie) }}" class="justify-start w-full btn">
                <i data-lucide="edit-3" class="mr-2 w-4 h-4"></i>
                Edit Category
            </a>
            
            <button class="justify-start w-full btn" onclick="copyToClipboard('{{ $categorie->email }}')">
                <i data-lucide="copy" class="mr-2 w-4 h-4"></i>
                Copy Email
            </button>
            
            <div class="divider"></div>
            
            <form action="{{ route('categories.destroy', $categorie) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus categorie ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="justify-start w-full btn text-error">
                    <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                    Hapus Category
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Email berhasil disalin!');
    });
}
</script>