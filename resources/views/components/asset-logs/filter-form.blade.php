<div class="mb-6 shadow-xl card bg-base-100">
    <div class="card-body">
        <form method="GET" action="{{ route('asset-logs.index') }}">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Asset</span>
                    </label>
                    <select name="asset_id" class="select select-bordered">
                        <option value="">Semua Asset</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->name }} ({{ $asset->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Action</span>
                    </label>
                    <select name="action" class="select select-bordered">
                        <option value="">Semua Action</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">User</span>
                    </label>
                    <select name="user_id" class="select select-bordered">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Dari Tanggal</span>
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input input-bordered">
                </div>
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Sampai Tanggal</span>
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input input-bordered">
                </div>
            </div>
            <div class="flex gap-2 justify-end pt-4">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i data-lucide="filter" class="mr-2 w-4 h-4"></i>
                    Filter
                </button>
                <a href="{{ route('asset-logs.index') }}" class="btn btn-ghost btn-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>