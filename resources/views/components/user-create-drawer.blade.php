@props([
    'drawerId' => 'create-drawer',
    'title' => 'Tambah User Baru',
    'action' => route('users.store'),
    'method' => 'POST'
])

<!-- Create User Drawer -->
<div class="drawer drawer-end">
    <input id="{{ $drawerId }}" type="checkbox" class="drawer-toggle" />
    <div class="z-50 drawer-side">
        <label for="{{ $drawerId }}" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="p-4 w-80 min-h-full menu bg-base-100 text-base-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">{{ $title }}</h3>
                <label for="{{ $drawerId }}" class="btn btn-sm btn-circle btn-ghost">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </label>
            </div>

            <form action="{{ $action }}" method="{{ $method }}" class="space-y-4">
                @csrf
                @if($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="form-control">
                    <label class="label" for="{{ $drawerId }}_name">
                        <span class="label-text">Nama Lengkap</span>
                    </label>
                    <input type="text" id="{{ $drawerId }}_name" name="name" class="input input-bordered"
                        value="{{ old('name') }}" required>
                </div>

                <div class="form-control">
                    <label class="label" for="{{ $drawerId }}_email">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="{{ $drawerId }}_email" name="email" class="input input-bordered"
                        value="{{ old('email') }}" required>
                </div>

                <div class="form-control">
                    <label class="label" for="{{ $drawerId }}_password">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" id="{{ $drawerId }}_password" name="password" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label class="label" for="{{ $drawerId }}_password_confirmation">
                        <span class="label-text">Konfirmasi Password</span>
                    </label>
                    <input type="password" id="{{ $drawerId }}_password_confirmation" name="password_confirmation"
                        class="input input-bordered" required>
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-1 btn btn-primary">
                        <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                        Simpan
                    </button>
                    <label for="{{ $drawerId }}" class="btn btn-ghost">
                        Batal
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCreateDrawer() {
        document.getElementById('{{ $drawerId }}').checked = true;
    }
</script>