<script>
    // Asset logs scripts - mainly for UI interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips for truncated text
        const truncatedElements = document.querySelectorAll('.truncate[title]');
        truncatedElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                // You can add tooltip library here if needed
            });
        });
        
        // Auto-close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) {
                    const trigger = dropdown.querySelector('[tabindex="0"][role="button"]');
                    if (trigger) {
                        trigger.blur();
                    }
                }
            });
        });
        
        // Handle export functionality
        const exportBtn = document.getElementById('export-logs');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                // Get current filter parameters
                const urlParams = new URLSearchParams(window.location.search);
                const exportUrl = new URL('{{ route("asset-logs.export") }}', window.location.origin);
                
                // Add current filters to export URL
                urlParams.forEach((value, key) => {
                    exportUrl.searchParams.append(key, value);
                });
                
                // Trigger download
                window.location.href = exportUrl.toString();
            });
        }
    });
</script>