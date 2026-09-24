@props(['asset-loan', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="mb-6 card-title text-base-content">
            <i data-lucide="zap" class="w-5 h-5"></i>
            Aksi Cepat
        </h3>

        <div class="space-y-3">
            @if ($assetLoan->isActive())
                <a href="{{ route('asset-loans.index', ['action' => 'return', 'asset_loan_id' => $assetLoan->id, 'asset_id' => $assetLoan->asset_id]) }}"
                    class="justify-start w-full btn btn-success">
                    <i data-lucide="corner-down-left" class="mr-2 w-4 h-4"></i>
                    Kembalikan Aset
                </a>

                <a href="{{ route('asset-loans.index', ['action' => 'edit', 'asset_loan_id' => $assetLoan->id, 'asset_id' => $assetLoan->asset_id]) }}"
                    class="justify-start w-full btn">
                    <i data-lucide="edit-3" class="mr-2 w-4 h-4"></i>
                    Edit Pinjaman
                </a>
            @endif

            <a href="{{ route('asset-loans.index') }}" class="justify-start w-full btn btn-ghost">
                <i data-lucide="list" class="mr-2 w-4 h-4"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
