@props(['users', 'class' => ''])

<div class="overflow-x-auto {{ $class }}">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-email="{{ $user->email }}">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role === App\Enums\UserRole::ADMIN)
                            <span class="badge badge-error badge-sm">Admin</span>
                        @elseif($user->role === App\Enums\UserRole::STAFF)
                            <span class="badge badge-warning badge-sm">Staff</span>
                        @elseif($user->role === App\Enums\UserRole::AUDITOR)
                            <span class="badge badge-info badge-sm">Auditor</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <button class="btn btn-sm btn-ghost" popovertarget="user-dropdown-{{ $user->id }}"
                            style="anchor-name: --user-anchor-{{ $user->id }}">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>
                        <ul class="w-52 shadow-lg dropdown dropdown-left dropdown-center menu rounded-box bg-base-100"
                            popover id="user-dropdown-{{ $user->id }}"
                            style="position-anchor: --user-anchor-{{ $user->id }}">
                            <li>
                                <a href="{{ route('users.show', $user) }}" onclick="document.getElementById('user-dropdown-{{ $user->id }}').hidePopover()">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('users.edit', $user) }}" onclick="document.getElementById('user-dropdown-{{ $user->id }}').hidePopover()">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </a>
                            </li>
                            <li>
                                <a onclick="deleteUser({{ $user->id }}); document.getElementById('user-dropdown-{{ $user->id }}').hidePopover()" class="text-error">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    Delete
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-muted">
                        <i data-lucide="users" class="block mx-auto mb-3 w-12 h-12"></i>
                        No users found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>