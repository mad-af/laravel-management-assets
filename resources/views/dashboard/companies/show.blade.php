@extends('layouts.dashboard')

@section('title', 'Company Details')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Company Details</h1>
                <p class="mt-1 text-base-content/70">Detail informasi perusahaan {{ $company->name }}.</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary btn-sm">
                    <i data-lucide="edit" class="mr-2 w-4 h-4"></i>
                    Edit
                </a>
                <a href="{{ route('companies.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Company Information -->
            <div class="lg:col-span-2">
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Informasi Perusahaan</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold">Nama Perusahaan</span>
                                </label>
                                <p class="text-base-content">{{ $company->name }}</p>
                            </div>

                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold">Kode Perusahaan</span>
                                </label>
                                <span class="badge badge-outline">{{ $company->code }}</span>
                            </div>

                            @if($company->tax_id)
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Tax ID / NPWP</span>
                                    </label>
                                    <p class="text-base-content">{{ $company->tax_id }}</p>
                                </div>
                            @endif

                            @if($company->phone)
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Telepon</span>
                                    </label>
                                    <p class="text-base-content">{{ $company->phone }}</p>
                                </div>
                            @endif

                            @if($company->email)
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Email</span>
                                    </label>
                                    <p class="text-base-content">
                                        <a href="mailto:{{ $company->email }}" class="link link-primary">{{ $company->email }}</a>
                                    </p>
                                </div>
                            @endif

                            @if($company->website)
                                <div>
                                    <label class="label">
                                        <span class="label-text font-semibold">Website</span>
                                    </label>
                                    <p class="text-base-content">
                                        <a href="{{ $company->website }}" target="_blank" class="link link-primary">{{ $company->website }}</a>
                                    </p>
                                </div>
                            @endif

                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold">Status</span>
                                </label>
                                @if($company->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-error">Inactive</span>
                                @endif
                            </div>

                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold">Dibuat</span>
                                </label>
                                <p class="text-base-content">{{ $company->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if($company->address)
                            <div class="mt-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Alamat</span>
                                </label>
                                <p class="text-base-content whitespace-pre-line">{{ $company->address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Company Logo & Statistics -->
            <div class="space-y-6">
                <!-- Company Logo -->
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body text-center">
                        <h3 class="card-title justify-center mb-4">Logo Perusahaan</h3>
                        @if($company->image)
                            <div class="avatar mx-auto">
                                <div class="w-32 h-32 rounded">
                                    <img src="{{ asset('storage/' . $company->image) }}" alt="{{ $company->name }}" />
                                </div>
                            </div>
                        @else
                            <div class="avatar placeholder mx-auto">
                                <div class="bg-neutral text-neutral-content rounded w-32 h-32">
                                    <span class="text-2xl">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-base-content/50 mt-2">Belum ada logo</p>
                        @endif
                    </div>
                </div>

                <!-- Statistics -->
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Statistik</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-base-content/70">Total Users</span>
                                <span class="badge badge-info">{{ $company->users->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-base-content/70">Total Assets</span>
                                <span class="badge badge-success">{{ $company->assets->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-base-content/70">Categories</span>
                                <span class="badge badge-warning">{{ $company->categories->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-base-content/70">Locations</span>
                                <span class="badge badge-secondary">{{ $company->locations->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Data Tabs -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <div role="tablist" class="tabs tabs-bordered">
                    <input type="radio" name="company_tabs" role="tab" class="tab" aria-label="Users" checked />
                    <div role="tabpanel" class="tab-content p-6">
                        <h3 class="text-lg font-semibold mb-4">Users ({{ $company->users->count() }})</h3>
                        @if($company->users->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($company->users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <span class="badge badge-outline">{{ $user->role->value }}</span>
                                                </td>
                                                <td>
                                                    @if($user->email_verified_at)
                                                        <span class="badge badge-success">Verified</span>
                                                    @else
                                                        <span class="badge badge-warning">Unverified</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-base-content/50 text-center py-8">Belum ada user untuk perusahaan ini.</p>
                        @endif
                    </div>

                    <input type="radio" name="company_tabs" role="tab" class="tab" aria-label="Assets" />
                    <div role="tabpanel" class="tab-content p-6">
                        <h3 class="text-lg font-semibold mb-4">Assets ({{ $company->assets->count() }})</h3>
                        @if($company->assets->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Kategori</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($company->assets->take(10) as $asset)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-outline">{{ $asset->code }}</span>
                                                </td>
                                                <td>{{ $asset->name }}</td>
                                                <td>{{ $asset->category->name ?? '-' }}</td>
                                                <td>{{ $asset->location->name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $asset->status->value === 'active' ? 'success' : 'warning' }}">
                                                        {{ $asset->status->value }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($company->assets->count() > 10)
                                <p class="text-sm text-base-content/50 mt-4">Menampilkan 10 dari {{ $company->assets->count() }} assets.</p>
                            @endif
                        @else
                            <p class="text-base-content/50 text-center py-8">Belum ada asset untuk perusahaan ini.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection