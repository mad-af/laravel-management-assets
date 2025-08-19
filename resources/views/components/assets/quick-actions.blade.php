@props([
    'asset',
    'class' => ''
])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h2 class="mb-4 text-lg font-semibold card-title">Aksi Cepat</h2>
        
        <div class="space-y-3">
            <a href="{{ route('assets.edit', $asset) }}" class="justify-start w-full btn">
                <i data-lucide="edit-3" class="mr-2 w-4 h-4"></i>
                Edit Asset
            </a>
            
            <button class="justify-start w-full btn" onclick="copyToClipboard('{{ $asset->email }}')">
                <i data-lucide="copy" class="mr-2 w-4 h-4"></i>
                Copy Email
            </button>
            
            <div class="divider"></div>
            
            <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus asset ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="justify-start w-full btn text-error">
                    <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                    Hapus Asset
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