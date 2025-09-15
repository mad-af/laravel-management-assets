<div>
    <!-- Dropdown Button -->
    <button class="p-1 btn btn-ghost btn-sm hover:bg-base-200" popovertarget="dropdown-menu-{{ $model->id }}"
        style="anchor-name: --anchor-{{ $model->id }}">
        <x-icon name="o-ellipsis-vertical" class="w-4 h-4" />
    </button>

    <!-- Dropdown Menu -->
    <ul class="shadow-sm dropdown dropdown-end menu rounded-box bg-base-100" popover
        id="dropdown-menu-{{ $model->id }}" style="position-anchor: --anchor-{{ $model->id }};">
        @if($this->hasAction('edit'))
            <li>
                <button wire:click="edit" class="flex gap-2 items-center p-2 text-sm rounded">
                    <x-icon name="o-pencil" class="w-4 h-4" />
                    Edit
                </button>
            </li>
        @endif

        @if($this->hasAction('delete'))
            <li>
                <button wire:click="delete" wire:confirm="{{ $confirmMessage }}"
                    class="flex gap-2 items-center p-2 text-sm rounded text-error">
                    <x-icon name="o-trash" class="w-4 h-4" />
                    Delete
                </button>
            </li>
        @endif

        @if($this->hasAction('view'))
            <li>
                <button wire:click="view" class="flex gap-2 items-center p-2 text-sm rounded hover:bg-base-200">
                    <x-icon name="o-eye" class="w-4 h-4" />
                    View
                </button>
            </li>
        @endif

        @if($this->hasAction('duplicate'))
            <li>
                <button wire:click="duplicate" class="flex gap-2 items-center p-2 text-sm rounded hover:bg-base-200">
                    <x-icon name="o-document-duplicate" class="w-4 h-4" />
                    Duplicate
                </button>
            </li>
        @endif
    </ul>
</div>