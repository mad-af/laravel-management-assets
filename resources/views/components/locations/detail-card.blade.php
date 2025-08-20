@props([
    'location',
    'class' => ''
])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h2 class="mb-6 text-lg font-semibold card-title">Informasi Location</h2>
        
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Profil</span>
                </label>
                <div class="p-4 rounded-lg bg-base-200">
                    <div class="flex gap-4 items-center">
                        <x-avatar initials="{{ substr($location->name, 0, 2) }}" size="lg" placeholder="true" />
                        <div>
                            <div class="text-lg font-bold">{{ $location->name }}</div>
                            <div class="text-sm opacity-70">ID: {{ $location->id }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Status</span>
                </label>
                <div class="p-3 rounded-lg bg-base-200">
                    @if($location->is_active)
                        <span class="badge badge-success">
                            <i data-lucide="check-circle" class="mr-1 w-4 h-4"></i>
                            Aktif
                        </span>
                    @else
                        <span class="badge badge-error">
                            <i data-lucide="x-circle" class="mr-1 w-4 h-4"></i>
                            Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Tanggal Dibuat</span>
                </label>
                <div class="p-3 rounded-lg bg-base-200">
                    {{ $location->created_at->format('d F Y, H:i') }} WIB
                </div>
            </div>
            
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Terakhir Diperbarui</span>
                </label>
                <div class="p-3 rounded-lg bg-base-200">
                    {{ $location->updated_at->format('d F Y, H:i') }} WIB
                </div>
            </div>
        </div>
    </div>
</div>