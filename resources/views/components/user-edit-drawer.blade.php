@props([
    'drawerId' => 'edit-drawer',
    'title' => 'Edit User',
    'formId' => 'edit-form'
])

<!-- Edit User Drawer -->
<div class="drawer drawer-end">
    <input id="{{ $drawerId }}" type="checkbox" class="drawer-toggle" />
    <div class="z-50 drawer-side">
        <label for="{{ $drawerId }}" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="p-4 w-80 min-h-full menu bg-base-100 text-base-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">{{ $title }}</h3>
                <label for="{{ $drawerId }}" class="btn btn-sm btn-circle btn-ghost">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </label>
            </div>

            <form id="{{ $formId }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="form-control">
                    <label class="label" for="{{ $drawerId }}_name">
                        <span class="label-text">Nama Lengkap</span>
                    </label>
                    <input type="text" id="{{ $drawerId }}_name" name="name" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label class="label" for="{{ $drawerId }}_email">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="{{ $drawerId }}_email" name="email" class="input input-bordered" required>
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-1 btn btn-primary">
                        <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                        Update
                    </button>
                    <label for="{{ $drawerId }}" class="btn btn-ghost">
                        Batal
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Listen for custom openEditDrawer events
    document.addEventListener('openEditDrawer', function(event) {
        const { userId, userName, userEmail } = event.detail;
        
        // Populate form and open drawer
        document.getElementById('{{ $drawerId }}_name').value = userName;
        document.getElementById('{{ $drawerId }}_email').value = userEmail;
        document.getElementById('{{ $formId }}').action = `/dashboard/users/${userId}`;
        document.getElementById('{{ $drawerId }}').checked = true;
    });
    
    // Check URL on page load and open drawer if edit parameter exists
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const editUserId = urlParams.get('edit');
        
        if (editUserId) {
            // Find user data from the page
            const userRow = document.querySelector(`[data-user-id="${editUserId}"]`);
            if (userRow) {
                const userName = userRow.dataset.userName;
                const userEmail = userRow.dataset.userEmail;
                
                // Open drawer
                setTimeout(() => {
                    document.getElementById('{{ $drawerId }}_name').value = userName;
                    document.getElementById('{{ $drawerId }}_email').value = userEmail;
                    document.getElementById('{{ $formId }}').action = `/dashboard/users/${editUserId}`;
                    document.getElementById('{{ $drawerId }}').checked = true;
                }, 100);
            }
        }
    });
    
    // Listen for drawer close events and update URL
    document.getElementById('{{ $drawerId }}').addEventListener('change', function(e) {
        if (!e.target.checked) {
            // Drawer is being closed, remove URL parameter
            const url = new URL(window.location);
            url.searchParams.delete('edit');
            window.history.pushState({}, '', url);
        }
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const editUserId = urlParams.get('edit');
        
        if (editUserId) {
            // Open drawer if edit parameter exists
            const userRow = document.querySelector(`[data-user-id="${editUserId}"]`);
            if (userRow) {
                const userName = userRow.dataset.userName;
                const userEmail = userRow.dataset.userEmail;
                
                document.getElementById('{{ $drawerId }}_name').value = userName;
                document.getElementById('{{ $drawerId }}_email').value = userEmail;
                document.getElementById('{{ $formId }}').action = `/dashboard/users/${editUserId}`;
                document.getElementById('{{ $drawerId }}').checked = true;
            }
        } else {
            // Close drawer if no edit parameter
            document.getElementById('{{ $drawerId }}').checked = false;
        }
    });
</script>