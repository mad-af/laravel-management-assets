@extends('layouts.dashboard')

@section('title', 'User Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">User Management</h1>
                <p class="mt-1 text-base-content/70">Kelola data pengguna sistem.</p>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-primary btn-sm" onclick="openCreateDrawer()">
                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                    Tambah User
                </button>
                <button class="btn btn-outline btn-sm">
                    <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                    Export
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Users Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Daftar Pengguna</h2>

                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>
                                <label>
                                    <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <th>
                                    <label>
                                        <input type="checkbox" class="checkbox" />
                                    </label>
                                </th>
                                <td>
                                    <div class="flex gap-3 items-center">
                                        <x-avatar initials="{{ substr($user->name, 0, 2) }}" size="md" placeholder="true" />
                                        <div>
                                            <div class="font-bold">{{ $user->name }}</div>
                                            <div class="text-sm opacity-50">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success badge-sm">Verified</span>
                                    @else
                                        <span class="badge badge-warning badge-sm">Unverified</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li><a href="{{ route('users.show', $user) }}"><i data-lucide="eye"
                                                        class="mr-2 w-4 h-4"></i>Lihat</a></li>
                                            <li><a
                                                    onclick="openEditDrawer({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"><i
                                                        data-lucide="edit" class="mr-2 w-4 h-4"></i>Edit</a></li>
                                            <li>
                                                <a onclick="deleteUser({{ $user->id }})" class="text-error"><i
                                                        data-lucide="trash-2" class="mr-2 w-4 h-4"></i>Hapus</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="users" class="mb-2 w-12 h-12 text-base-content/50"></i>
                                        <p class="text-base-content/70">Belum ada user yang terdaftar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create User Drawer -->
    <div class="drawer drawer-end">
        <input id="create-drawer" type="checkbox" class="drawer-toggle" />
        <div class="z-50 drawer-side">
            <label for="create-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <div class="p-4 w-80 min-h-full menu bg-base-100 text-base-content">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Tambah User Baru</h3>
                    <label for="create-drawer" class="btn btn-sm btn-circle btn-ghost">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </label>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="form-control">
                        <label class="label" for="create_name">
                            <span class="label-text">Nama Lengkap</span>
                        </label>
                        <input type="text" id="create_name" name="name" class="input input-bordered"
                            value="{{ old('name') }}" required>
                    </div>

                    <div class="form-control">
                        <label class="label" for="create_email">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" id="create_email" name="email" class="input input-bordered"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="form-control">
                        <label class="label" for="create_password">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" id="create_password" name="password" class="input input-bordered" required>
                    </div>

                    <div class="form-control">
                        <label class="label" for="create_password_confirmation">
                            <span class="label-text">Konfirmasi Password</span>
                        </label>
                        <input type="password" id="create_password_confirmation" name="password_confirmation"
                            class="input input-bordered" required>
                    </div>

                    <div class="flex gap-2 pt-4">
                        <button type="submit" class="flex-1 btn btn-primary">
                            <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                            Simpan
                        </button>
                        <label for="create-drawer" class="btn btn-ghost">
                            Batal
                        </label>
                    </div>
                </form>
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
                        <input type="text" id="edit_name" name="name" class="input input-bordered" required>
                    </div>

                    <div class="form-control">
                        <label class="label" for="edit_email">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" id="edit_email" name="email" class="input input-bordered" required>
                    </div>

                    <div class="form-control">
                        <label class="label" for="edit_password">
                            <span class="label-text">Password Baru</span>
                            <span class="label-text-alt">Kosongkan jika tidak ingin mengubah</span>
                        </label>
                        <input type="password" id="edit_password" name="password" class="input input-bordered">
                    </div>

                    <div class="form-control">
                        <label class="label" for="edit_password_confirmation">
                            <span class="label-text">Konfirmasi Password Baru</span>
                        </label>
                        <input type="password" id="edit_password_confirmation" name="password_confirmation"
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
        function openCreateDrawer() {
            document.getElementById('create-drawer').checked = true;
        }

        function openEditDrawer(userId, userName, userEmail) {
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            document.getElementById('edit-form').action = `/dashboard/users/${userId}`;
            document.getElementById('edit-drawer').checked = true;
        }

        function deleteUser(userId) {
            if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                // Buat form untuk delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dashboard/users/${userId}`;

                // Tambahkan CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Tambahkan method DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                // Submit form
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection