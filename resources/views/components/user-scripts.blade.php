<script>
    function deleteUser(userId) {
        // Close all dropdowns first
        const dropdownTriggers = document.querySelectorAll('.dropdown [tabindex="0"][role="button"]');
        dropdownTriggers.forEach(trigger => {
            trigger.blur();
        });
        
        // Small delay to ensure dropdown closes before showing confirm dialog
        setTimeout(() => {
            if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                // Buat form untuk delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dashboard/users/${userId}`;

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
</script>