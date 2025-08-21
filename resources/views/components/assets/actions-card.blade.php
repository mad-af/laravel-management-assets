@props(['asset', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="mb-4 text-lg card-title">Quick Actions</h3>

        <div class="space-y-3">
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline btn-block">
                <i data-lucide="edit" class="mr-2 w-4 h-4"></i>
                Edit Asset
            </a>

            <button class="btn btn-outline btn-warning btn-block" onclick="updateStatus('maintenance')">
                <i data-lucide="settings" class="mr-2 w-4 h-4"></i>
                Mark as Maintenance
            </button>

            <form method="POST" action="{{ route('assets.destroy', $asset) }}"
                onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-error btn-block">
                    <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                    Delete Asset
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function updateStatus(status) {
        if (confirm(`Are you sure you want to mark this asset as ${status}?`)) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("assets.update-status", $asset) }}';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';

            const statusField = document.createElement('input');
            statusField.type = 'hidden';
            statusField.name = 'status';
            statusField.value = status;

            form.appendChild(csrfToken);
            form.appendChild(methodField);
            form.appendChild(statusField);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>