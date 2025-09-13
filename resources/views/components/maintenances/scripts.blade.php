<script>
    function addMaintenance() {
        // Open the maintenance drawer via URL parameter
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('open_drawer', 'maintenance');
        window.history.pushState({}, '', currentUrl);

        // Trigger the drawer opening
        const drawerToggle = document.getElementById('maintenance-drawer');
        if (drawerToggle) {
            drawerToggle.checked = true;
        }
    }

    // Function to check URL parameters and open drawer automatically
    function checkUrlParamsAndOpenDrawer() {
        const urlParams = new URLSearchParams(window.location.search);
        const openDrawer = urlParams.get('open_drawer');
        const action = urlParams.get('action');

        // Open drawer if URL contains open_drawer=maintenance or action=add_maintenance
        if (openDrawer === 'maintenance' || action === 'add_maintenance') {
            const drawerToggle = document.getElementById('maintenance-drawer');
            if (drawerToggle) {
                drawerToggle.checked = true;

            }
        }
    }

    // Function to clean URL parameters when drawer is closed
    function cleanUrlParams() {
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('open_drawer');
        newUrl.searchParams.delete('action');
        window.history.replaceState({}, '', newUrl);
    }

    // Run the check when page loads
    document.addEventListener('DOMContentLoaded', function () {
        checkUrlParamsAndOpenDrawer();
        cleanUrlParams();
    });
</script>