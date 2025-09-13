<script>
    // ===== MAINTENANCE DRAWER FUNCTIONS =====
    
    /**
     * Open maintenance drawer and add URL parameter
     */
    function addMaintenance() {
        // Open the maintenance drawer via URL parameter
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('open_drawer', 'form');
        window.history.pushState({}, '', currentUrl);

        // Trigger the drawer opening
        const drawerToggle = document.getElementById('maintenance-drawer');
        if (drawerToggle) {
            drawerToggle.checked = true;
        }
    }

    /**
     * Open filter drawer and add URL parameter
     */
    function openFilterDrawer() {
        // Open the filter drawer via URL parameter
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('open_drawer', 'filter');
        window.history.pushState({}, '', currentUrl);

        // Trigger the drawer opening
        const filterDrawerToggle = document.getElementById('filter-drawer');
        if (filterDrawerToggle) {
            filterDrawerToggle.checked = true;
        }
    }

    /**
     * Check URL parameters and open appropriate drawer automatically
     */
    function checkUrlParamsAndOpenDrawer() {
        const urlParams = new URLSearchParams(window.location.search);
        const openDrawer = urlParams.get('open_drawer');

        if (openDrawer === 'form') {
            // Open maintenance form drawer
            const drawerToggle = document.getElementById('maintenance-drawer');
            if (drawerToggle) {
                drawerToggle.checked = true;
            }
        } else if (openDrawer === 'filter') {
            // Open filter drawer
            const filterDrawerToggle = document.getElementById('filter-drawer');
            if (filterDrawerToggle) {
                filterDrawerToggle.checked = true;
            }
        }
    }

    /**
     * Clean URL parameters when drawer is closed
     */
    function cleanUrlParams() {
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('open_drawer');
        window.history.replaceState({}, '', newUrl);
    }

    /**
     * Clear all filter form inputs
     */
    function clearFilters() {
        const filterForm = document.querySelector('#filter-drawer form');
        if (filterForm) {
            // Reset all form inputs
            filterForm.reset();
            
            // Optionally trigger filter application with empty values
            console.log('Filters cleared');
        }
    }

    // ===== INITIALIZATION FUNCTIONS =====
    
    /**
     * Initialize drawer event listeners
     */
    function initializeDrawerListeners() {
        // Initialize maintenance drawer listeners
        const drawerToggle = document.getElementById('maintenance-drawer');
        if (drawerToggle && !drawerToggle.hasAttribute('data-listener-attached')) {
            drawerToggle.addEventListener('change', function() {
                if (!this.checked) {
                    cleanUrlParams();
                }
            });
            drawerToggle.setAttribute('data-listener-attached', 'true');
        }

        // Initialize filter drawer listeners
        const filterDrawerToggle = document.getElementById('filter-drawer');
        if (filterDrawerToggle && !filterDrawerToggle.hasAttribute('data-filter-listener-attached')) {
            filterDrawerToggle.addEventListener('change', function() {
                if (!this.checked) {
                    cleanUrlParams();
                }
            });
            filterDrawerToggle.setAttribute('data-filter-listener-attached', 'true');
        }
    }

    /**
     * Initialize all drawer functionality
     */
    function initializeDrawers() {
        // Check URL parameters and open appropriate drawer
        checkUrlParamsAndOpenDrawer();
        
        // Initialize event listeners
        initializeDrawerListeners();
    }

    /**
     * Initialize on DOM ready
     */
    document.addEventListener('DOMContentLoaded', initializeDrawers);

    /**
     * MutationObserver for dynamic content loading
     */
    if (typeof MutationObserver !== 'undefined') {
        const drawerObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    initializeDrawerListeners();
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            drawerObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    }
</script>