<script>
    function deleteAsset(assetId) {
        // Close all dropdowns first
        const dropdownTriggers = document.querySelectorAll('.dropdown [tabindex="0"][role="button"]');
        dropdownTriggers.forEach(trigger => {
            trigger.blur();
        });
        
        // Small delay to ensure dropdown closes before showing confirm dialog
        setTimeout(() => {
            if (confirm('Apakah Anda yakin ingin menghapus asset ini?')) {
                // Buat form untuk delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dashboard/assets/${assetId}`;

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
        }, 100);
    }

    function updateStatusAsset(status) {
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