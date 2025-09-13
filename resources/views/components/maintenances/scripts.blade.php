<script>
// ===== MAINTENANCE DRAWER FUNCTIONS =====

// Global variables
let isEditMode = false;
let currentMaintenanceId = null;

/**
 * Open maintenance drawer in create mode and add URL parameter
 */
function addMaintenance() {
    resetForm();
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
 * Function to clean URL parameters when drawer closes
 */
function cleanUrlParams() {
    if (!document.getElementById('maintenance-drawer').checked) {
        // Reset form when drawer closes
        resetForm();
        // Remove URL parameters
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('open_drawer');
        window.history.pushState({}, '', currentUrl);
    }
}

/**
 * Function to reset form to create mode
 */
function resetForm() {
    isEditMode = false;
    currentMaintenanceId = null;
    
    // Reset form action and method
    const form = document.getElementById('maintenance-form');
    if (form) {
        form.action = '/admin/maintenances';
        const methodInput = document.getElementById('form-method');
        const idInput = document.getElementById('maintenance-id');
        if (methodInput) methodInput.value = '';
        if (idInput) idInput.value = '';
        
        // Reset form title and button
        const title = document.getElementById('drawer-title');
        const submitText = document.getElementById('submit-text');
        const submitIcon = document.getElementById('submit-icon');
        
        if (title) title.textContent = 'Add New Maintenance';
        if (submitText) submitText.textContent = 'Create Maintenance';
        if (submitIcon) submitIcon.setAttribute('name', 'o-plus');
        
        // Reset all form fields
        form.reset();
        
        // Reset select elements to default
        const assetSelect = document.getElementById('asset-id');
        const typeSelect = document.getElementById('maintenance-type');
        const prioritySelect = document.getElementById('maintenance-priority');
        const statusSelect = document.getElementById('maintenance-status');
        const assignedSelect = document.getElementById('maintenance-assigned-to');
        
        if (assetSelect) assetSelect.selectedIndex = 0;
        if (typeSelect) typeSelect.selectedIndex = 0;
        if (prioritySelect) prioritySelect.selectedIndex = 0;
        if (statusSelect) statusSelect.value = 'open';
        if (assignedSelect) assignedSelect.selectedIndex = 0;
    }
}

/**
 * Function to open drawer in edit mode
 */
function openEditDrawer(maintenanceId) {
    isEditMode = true;
    currentMaintenanceId = maintenanceId;
    
    // Fetch maintenance data
    fetch(`/admin/maintenances/${maintenanceId}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateForm(data.maintenance);
            // Open drawer
            document.getElementById('maintenance-drawer').checked = true;
        } else {
            alert('Error loading maintenance data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading maintenance data');
    });
}

/**
 * Function to populate form with maintenance data
 */
function populateForm(maintenance) {
    // Update form for edit mode
    const form = document.getElementById('maintenance-form');
    if (form) {
        form.action = `/admin/maintenances/${maintenance.id}`;
        const methodInput = document.getElementById('form-method');
        const idInput = document.getElementById('maintenance-id');
        
        if (methodInput) methodInput.value = 'PUT';
        if (idInput) idInput.value = maintenance.id;
        
        // Update title and button
        const title = document.getElementById('drawer-title');
        const submitText = document.getElementById('submit-text');
        const submitIcon = document.getElementById('submit-icon');
        
        if (title) title.textContent = 'Edit Maintenance';
        if (submitText) submitText.textContent = 'Update Maintenance';
        if (submitIcon) submitIcon.setAttribute('name', 'o-pencil');
        
        // Populate form fields
        const fields = {
            'asset-id': maintenance.asset_id,
            'maintenance-title': maintenance.title,
            'maintenance-type': maintenance.type,
            'maintenance-priority': maintenance.priority,
            'maintenance-status': maintenance.status,
            'maintenance-description': maintenance.description,
            'maintenance-scheduled-date': maintenance.scheduled_date,
            'maintenance-assigned-to': maintenance.assigned_to,
            'maintenance-cost': maintenance.cost,
            'maintenance-notes': maintenance.notes
        };
        
        Object.keys(fields).forEach(fieldId => {
            const element = document.getElementById(fieldId);
            if (element && fields[fieldId] !== null && fields[fieldId] !== undefined) {
                element.value = fields[fieldId];
            }
        });
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

 // Initialize when page loads
 document.addEventListener('DOMContentLoaded', function() {
     checkUrlParamsAndOpenDrawer();
     
     // Add event listener for drawer toggle
     const drawerToggle = document.getElementById('maintenance-drawer');
     if (drawerToggle) {
         drawerToggle.addEventListener('change', cleanUrlParams);
     }
 });
</script>