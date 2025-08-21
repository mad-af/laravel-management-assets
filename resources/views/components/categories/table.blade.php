@props(['categories', 'class' => ''])

<div class="overflow-x-auto {{ $class }}">
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
                    <td class="min-w-0">{{ $categorie->id }}</td>
                    <td class="min-w-0">{{ $categorie->name }}</td>
                    <td class="min-w-0">
                        @if($categorie->is_active)
                            <span class="text-xs whitespace-nowrap badge badge-success">Active</span>
                        @else
                            <span class="text-xs whitespace-nowrap badge badge-error">Inactive</span>
                        @endif
                    </td>
                    <td class="min-w-0">{{ $categorie->created_at->format('d M Y') }}</td>
                    <td class="min-w-0">
                        <button class="btn btn-sm btn-ghost" popovertarget="category-dropdown-{{ $categorie->id }}"
                            style="anchor-name: --category-anchor-{{ $categorie->id }}">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>
                        <ul class="w-52 border shadow-lg dropdown dropdown-left dropdown-center menu rounded-box bg-base-100"
                            popover id="category-dropdown-{{ $categorie->id }}"
                            style="position-anchor: --category-anchor-{{ $categorie->id }}">
                            @if($categorie->is_active)
                                <li>
                                    <a href="{{ route('categories.show', $categorie) }}">
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
                                    <a onclick="deleteCategory('{{ $categorie->id }}')" class="text-error">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Deactivate
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a onclick="activateCategory('{{ $categorie->id }}')" class="text-success">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Activate
                                    </a>
                                </li>
                            @endif
                        </ul>
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