// Auto-filter functionality for tables
function initAutoFilter() {
    const statusFilter = document.getElementById('status');
    const topicFilter = document.getElementById('topic');
    const searchInput = document.getElementById('search');
    const tableBody = document.querySelector('.table tbody');
    const tableRows = tableBody ? Array.from(tableBody.querySelectorAll('tr:not(.no-results-row)')) : [];
    
    if (!tableBody || tableRows.length === 0) return;
    
    function filterTable() {
        const statusValue = statusFilter ? statusFilter.value.toLowerCase() : '';
        const topicValue = topicFilter ? topicFilter.value.toLowerCase() : '';
        const searchValue = searchInput ? searchInput.value.toLowerCase() : '';
        
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(5)'); // Status column
            const topicCell = row.querySelector('td:nth-child(3)'); // Topic column
            const nameCell = row.querySelector('td:nth-child(2)'); // Name column
            const idCell = row.querySelector('td:nth-child(1)'); // ID column
            
            const statusText = statusCell ? statusCell.textContent.toLowerCase() : '';
            const topicText = topicCell ? topicCell.textContent.toLowerCase() : '';
            const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
            const idText = idCell ? idCell.textContent.toLowerCase() : '';
            
            const statusMatch = !statusValue || statusText.includes(statusValue);
            const topicMatch = !topicValue || topicText.includes(topicValue);
            const searchMatch = !searchValue || 
                nameText.includes(searchValue) || 
                idText.includes(searchValue) ||
                topicText.includes(searchValue) ||
                statusText.includes(searchValue);
            
            if (statusMatch && topicMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update count and show/hide no results message
        updateResultsCount(visibleCount);
        showNoResultsMessage(visibleCount === 0);
    }
    
    function updateResultsCount(count) {
        const countElement = document.querySelector('.results-count');
        if (countElement) {
            countElement.textContent = count + (count === 1 ? ' ticket found' : ' tickets found');
        }
    }
    
    function showNoResultsMessage(show) {
        let noResultsRow = tableBody.querySelector('.no-results-row');
        
        if (show) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = '<td colspan="9" class="text-center text-muted py-4"><i class="fas fa-search me-2"></i>No tickets match your filters</td>';
                tableBody.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    }
    
    // Add event listeners
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    if (topicFilter) topicFilter.addEventListener('change', filterTable);
    if (searchInput) searchInput.addEventListener('input', filterTable);
    
    // Clear filters function
    window.clearFilters = function() {
        if (statusFilter) statusFilter.value = '';
        if (topicFilter) topicFilter.value = '';
        if (searchInput) searchInput.value = '';
        filterTable();
    };
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initAutoFilter);
