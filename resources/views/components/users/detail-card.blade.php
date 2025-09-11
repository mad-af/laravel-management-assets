@props(['user', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="flex gap-2 items-center mb-6 card-title text-base-content">
            <i data-lucide="info" class="w-5 h-5"></i>
            User Information
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-base-content/70">Name</label>
                <div class="flex gap-4 items-center mt-1">
                    <x-avatar initials="{{ substr($user->name, 0, 2) }}" size="sm" placeholder="true" />
                    <p class="text-base-content">{{ $user->name }}</p>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Email</label>
                <p class="mt-1 text-base-content">{{ $user->email }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Role</label>
                <p class="mt-1">
                    @if($user->role === App\Enums\UserRole::ADMIN)
                    <span class="badge badge-error">Admin</span>
                @elseif($user->role === App\Enums\UserRole::STAFF)
                    <span class="badge badge-warning">Staff</span>
                @elseif($user->role === App\Enums\UserRole::AUDITOR)
                    <span class="badge badge-info">Auditor</span>
                @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Email Status</label>
                <p class="mt-1">
                    @if($user->email_verified_at)
                        <span class="badge badge-success badge-sm">Verified</span>
                    @else
                        <span class="badge badge-warning badge-sm">Unverified</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Created At</label>
                <p class="mt-1 text-base-content">{{ $user->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Last Updated</label>
                <p class="mt-1 text-base-content">{{ $user->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>