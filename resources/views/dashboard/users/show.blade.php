@extends('layouts.dashboard')

@section('title', 'Detail User')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div class="flex gap-4 items-center">
            <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">
                <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-base-content">Detail User</h1>
                <p class="mt-1 text-base-content/70">Informasi lengkap pengguna sistem.</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-warning btn-sm" onclick="openEditDrawer({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')">
                <i data-lucide="edit" class="mr-2 w-4 h-4"></i>
                Edit
            </button>
            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error btn-sm">
                    <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- User Info Card -->
        <div class="lg:col-span-2">
            <div class="shadow-xl card bg-base-100">
                <div class="card-body">
                    <h2 class="mb-6 text-lg font-semibold card-title">Informasi User</h2>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="form-control">
                            <label class="label">
                                <span class="font-semibold label-text">Profil</span>
                            </label>
                            <div class="p-4 rounded-lg bg-base-200">
                                <div class="flex gap-4 items-center">
                                    <x-avatar initials="{{ substr($user->name, 0, 2) }}" size="lg" placeholder="true" />
                                    <div>
                                        <div class="text-lg font-bold">{{ $user->name }}</div>
                                        <div class="text-sm opacity-70">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="font-semibold label-text">Email</span>
                            </label>
                            <div class="p-3 rounded-lg bg-base-200">
                                {{ $user->email }}
                            </div>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="font-semibold label-text">Status Email</span>
                            </label>
                            <div class="p-3 rounded-lg bg-base-200">
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">
                                        <i data-lucide="check-circle" class="mr-1 w-4 h-4"></i>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i data-lucide="clock" class="mr-1 w-4 h-4"></i>
                                        Belum Terverifikasi
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="font-semibold label-text">Tanggal Dibuat</span>
                            </label>
                            <div class="p-3 rounded-lg bg-base-200">
                                {{ $user->created_at->format('d F Y, H:i') }} WIB
                            </div>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="font-semibold label-text">Terakhir Diperbarui</span>
                            </label>
                            <div class="p-3 rounded-lg bg-base-200">
                                {{ $user->updated_at->format('d F Y, H:i') }} WIB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Card -->
        <div class="lg:col-span-1">
            <div class="shadow-xl card bg-base-100">
                <div class="card-body">
                    <h2 class="mb-4 text-lg font-semibold card-title">Aksi Cepat</h2>
                    
                    <div class="space-y-3">
                        <button class="justify-start w-full btn btn-warning" onclick="openEditDrawer({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')">
                            <i data-lucide="edit" class="mr-2 w-4 h-4"></i>
                            Edit User
                        </button>
                        
                        <button class="justify-start w-full btn btn-info" onclick="copyToClipboard('{{ $user->email }}')">
                            <i data-lucide="copy" class="mr-2 w-4 h-4"></i>
                            Copy Email
                        </button>
                        
                        <div class="divider"></div>
                        
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="justify-start w-full btn btn-error">
                                <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                                Hapus User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Drawer -->
<div class="drawer drawer-end">
    <input id="edit-drawer" type="checkbox" class="drawer-toggle" />
    <div class="z-50 drawer-side">
        <label for="edit-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="p-4 w-80 min-h-full menu bg-base-100 text-base-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">Edit User</h3>
                <label for="edit-drawer" class="btn btn-sm btn-circle btn-ghost">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </label>
            </div>
            
            <form id="edit-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div class="form-control">
                    <label class="label" for="edit_name">
                        <span class="label-text">Nama Lengkap</span>
                    </label>
                    <input type="text" 
                           id="edit_name" 
                           name="name" 
                           class="input input-bordered" 
                           required>
                </div>

                <div class="form-control">
                    <label class="label" for="edit_email">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" 
                           id="edit_email" 
                           name="email" 
                           class="input input-bordered" 
                           required>
                </div>

                <div class="form-control">
                    <label class="label" for="edit_password">
                        <span class="label-text">Password Baru</span>
                        <span class="label-text-alt">Kosongkan jika tidak ingin mengubah</span>
                    </label>
                    <input type="password" 
                           id="edit_password" 
                           name="password" 
                           class="input input-bordered">
                </div>

                <div class="form-control">
                    <label class="label" for="edit_password_confirmation">
                        <span class="label-text">Konfirmasi Password Baru</span>
                    </label>
                    <input type="password" 
                           id="edit_password_confirmation" 
                           name="password_confirmation" 
                           class="input input-bordered">
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-1 btn btn-primary">
                        <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                        Update
                    </button>
                    <label for="edit-drawer" class="btn btn-ghost">
                        Batal
                    </label>
                </div>
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

function openEditDrawer(userId, userName, userEmail) {
    document.getElementById('edit_name').value = userName;
    document.getElementById('edit_email').value = userEmail;
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_password_confirmation').value = '';
    document.getElementById('edit-form').action = `/dashboard/users/${userId}`;
    document.getElementById('edit-drawer').checked = true;
}
</script>
@endsection