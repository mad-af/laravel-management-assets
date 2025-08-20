@props(['categories', 'class' => ''])

<div class="table-responsive {{ $class }}">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $categorie)
                <tr data-categorie-id="{{ $categorie->id }}" data-categorie-name="{{ $categorie->name }}">
                    <td>{{ $categorie->id }}</td>
                    <td>{{ $categorie->name }}</td>
                    <td>
                        @if($categorie->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-error">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $categorie->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-sm btn-ghost">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                            </div>
                            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                                @if($categorie->is_active)
                                    <li>
                                        <a href="{{ route('categories.show', $categorie) }}"
                                            onclick="document.activeElement.blur()">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                            View
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('categories.edit', $categorie) }}">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a onclick="deleteCategory('{{ $categorie->id }}'); document.activeElement.blur()"
                                            class="text-error">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Deactivate
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a onclick="activateCategory('{{ $categorie->id }}'); document.activeElement.blur()"
                                            class="text-success">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            Activate
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-muted">
                        <i data-lucide="folder" class="block mx-auto mb-3 w-12 h-12"></i>
                        No categories found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>