<script>
    // Filter table function
    function filterTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const table = document.getElementById('companiesTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) { // Skip header row
            const row = rows[i];
            const companyName = row.getAttribute('data-company-name')?.toLowerCase() || '';
            const status = row.getAttribute('data-status')?.toLowerCase() || '';
            const cells = row.getElementsByTagName('td');
            
            let textContent = '';
            for (let j = 0; j < cells.length - 1; j++) { // Skip actions column
                textContent += cells[j].textContent.toLowerCase() + ' ';
            }

            const matchesSearch = textContent.includes(searchInput) || companyName.includes(searchInput);
            const matchesStatus = statusFilter === '' || status === statusFilter;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    // Activate company function
    function activateCompany(companyId) {
        if (confirm('Are you sure you want to activate this company?')) {
            fetch(`/companies/${companyId}/activate`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to activate company'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while activating the company');
            });
        }
    }

    // Deactivate company function
    function deactivateCompany(companyId) {
        if (confirm('Are you sure you want to deactivate this company? This will affect all related data.')) {
            fetch(`/companies/${companyId}/deactivate`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to deactivate company'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deactivating the company');
            });
        }
    }

    // Initialize search on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners for real-time filtering
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        
        if (searchInput) {
            searchInput.addEventListener('input', filterTable);
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', filterTable);
        }
    });
</script>