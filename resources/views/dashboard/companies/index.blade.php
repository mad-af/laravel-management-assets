@extends('layouts.dashboard')

@section('title', 'Company Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Company Management</h1>
                <p class="mt-1 text-base-content/70">Kelola data perusahaan dalam sistem.</p>
            </div>
            <div>
                <a href="{{ route('companies.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                    Tambah Company
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i data-lucide="x-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Companies Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Daftar Perusahaan</h2>
                
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Nama Perusahaan</th>
                                <th>Kode</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Users</th>
                                <th>Assets</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr>
                                    <td>
                                        @if($company->image)
                                            <div class="avatar">
                                                <div class="w-12 h-12 rounded">
                                                    <img src="{{ asset('storage/' . $company->image) }}" alt="{{ $company->name }}" />
                                                </div>
                                            </div>
                                        @else
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded w-12 h-12">
                                                    <span class="text-xs">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-bold">{{ $company->name }}</div>
                                        @if($company->tax_id)
                                            <div class="text-sm opacity-50">Tax ID: {{ $company->tax_id }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-outline">{{ $company->code }}</span>
                                    </td>
                                    <td>{{ $company->email ?? '-' }}</td>
                                    <td>{{ $company->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $company->users_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $company->assets_count }}</span>
                                    </td>
                                    <td>
                                        @if($company->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-error">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown dropdown-end">
                                            <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                                                <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                            </div>
                                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                                <li>
                                                    <a href="{{ route('companies.show', $company) }}">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                        View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('companies.edit', $company) }}">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('companies.activate', $company) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full text-left">
                                                            @if($company->is_active)
                                                                <i data-lucide="pause" class="w-4 h-4"></i>
                                                                Deactivate
                                                            @else
                                                                <i data-lucide="play" class="w-4 h-4"></i>
                                                                Activate
                                                            @endif
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('companies.destroy', $company) }}" method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this company?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full text-left text-error">
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-8">
                                        <div class="text-base-content/50">
                                            <i data-lucide="building-2" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                                            <p>Belum ada data perusahaan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($companies->hasPages())
                    <div class="mt-6">
                        {{ $companies->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection