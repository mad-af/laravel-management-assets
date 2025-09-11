@props([
    'asset',
    'class' => ''
])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="mb-6 card-title text-base-content">
            <i data-lucide="zap" class="w-5 h-5 mr"></i>
            Aksi Cepat
        </h3>
        
        <div class="space-y-3">
            <a href="{{ route('assets.edit', $asset) }}" class="justify-start w-full btn">
                <i data-lucide="edit-3" class="mr-2 w-4 h-4"></i>
                Edit Asset
            </a>
            
            <button class="justify-start w-full btn {{ $asset->status === \App\Enums\AssetStatus::MAINTENANCE ? 'btn-disabled' : '' }}"
                onclick="updateStatusAsset('{{ \App\Enums\AssetStatus::MAINTENANCE->value }}')"
                {{ $asset->status === \App\Enums\AssetStatus::MAINTENANCE ? 'disabled' : '' }}>
                <i class="fas fa-wrench"></i>
                {{ $asset->status === \App\Enums\AssetStatus::MAINTENANCE ? 'Maintenance Unavailable' : 'Mark as Maintenance' }}
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