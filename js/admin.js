/**
 * Admin Panel JavaScript
 * Handles admin-specific functionality
 */

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    initializeAdmin();
});

/**
 * Initialize admin features
 */
function initializeAdmin() {
    // Set active menu item
    setActiveMenuItem();
    
    // Initialize event listeners
    initializeEventListeners();
    
    // Initialize table sorting (if applicable)
    initializeTableSorting();
}

/**
 * Set active menu item based on current page
 */
function setActiveMenuItem() {
    const currentPage = window.location.pathname.split('/').pop() || 'dashboard.php';
    const menuItems = document.querySelectorAll('.admin-sidebar a');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href').split('/').pop();
        if (href === currentPage || (currentPage === '' && href === 'dashboard.php')) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

/**
 * Initialize event listeners for admin panel
 */
function initializeEventListeners() {
    // Delete confirmation
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('.admin-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateAdminForm(this)) {
                e.preventDefault();
            }
        });
    });
    
    // Modal functionality
    initializeModals();
    
    // Status update dropdowns
    initializeStatusUpdates();
}

/**
 * Validate admin form
 */
function validateAdminForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (field.value.trim() === '') {
            field.style.borderColor = '#dc3545';
            field.focus();
            isValid = false;
        } else {
            field.style.borderColor = '#ddd';
        }
    });
    
    if (!isValid) {
        showAlert('Please fill all required fields', 'danger');
    }
    
    return isValid;
}

/**
 * Initialize modal functionality
 */
function initializeModals() {
    const modals = document.querySelectorAll('.modal');
    const closeBtns = document.querySelectorAll('.close-btn');
    
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
            }
        });
    });
    
    // Close modal when clicking outside
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });
}

/**
 * Open modal
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
}

/**
 * Close modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}

/**
 * Initialize status update dropdowns
 */
function initializeStatusUpdates() {
    const statusSelects = document.querySelectorAll('select[name="status"]');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Find the form and submit it
            const form = this.closest('form');
            if (form && form.name.includes('status')) {
                form.submit();
            }
        });
    });
}

/**
 * Initialize table sorting
 */
function initializeTableSorting() {
    const tableHeaders = document.querySelectorAll('th[data-sortable="true"]');
    
    tableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(this);
        });
    });
}

/**
 * Sort table by column
 */
function sortTable(header) {
    const table = header.closest('table');
    const tbody = table.querySelector('tbody');
    const columnIndex = Array.from(header.parentElement.children).indexOf(header);
    const isNumeric = header.dataset.type === 'numeric';
    
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const isAscending = header.classList.contains('sort-asc');
    
    rows.sort((a, b) => {
        const aValue = a.children[columnIndex].textContent.trim();
        const bValue = b.children[columnIndex].textContent.trim();
        
        if (isNumeric) {
            return isAscending ? 
                parseFloat(bValue) - parseFloat(aValue) : 
                parseFloat(aValue) - parseFloat(bValue);
        } else {
            return isAscending ? 
                bValue.localeCompare(aValue) : 
                aValue.localeCompare(bValue);
        }
    });
    
    // Update sort indicators
    table.querySelectorAll('th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });
    
    header.classList.toggle('sort-asc', !isAscending);
    header.classList.toggle('sort-desc', isAscending);
    
    // Reorder rows
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.alert-container');
    
    if (alertContainer) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

/**
 * Delete item with confirmation
 */
function deleteItem(itemId, itemType = 'item') {
    if (confirm(`Are you sure you want to delete this ${itemType}?`)) {
        // This will be handled by the server
        return true;
    }
    return false;
}

/**
 * Bulk delete items
 */
function bulkDelete(checkboxSelector = 'input[name="selected_items[]"]') {
    const checkboxes = document.querySelectorAll(checkboxSelector + ':checked');
    
    if (checkboxes.length === 0) {
        showAlert('Please select at least one item', 'warning');
        return false;
    }
    
    if (confirm(`Are you sure you want to delete ${checkboxes.length} item(s)?`)) {
        const form = document.querySelector('form[name="bulk-action"]');
        if (form) {
            form.submit();
        }
        return true;
    }
    return false;
}

/**
 * Search functionality
 */
function searchTable(searchInputId, tableId) {
    const searchInput = document.getElementById(searchInputId);
    const table = document.getElementById(tableId);
    
    if (!searchInput || !table) return;
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        let visibleRows = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show "no results" message if needed
        if (visibleRows === 0) {
            const noResultsRow = table.querySelector('.no-results');
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    });
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, fileName = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(col => {
            rowData.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
        });
        
        csv.push(rowData.join(','));
    });
    
    downloadCSV(csv.join('\n'), fileName);
}

/**
 * Download CSV file
 */
function downloadCSV(csv, fileName) {
    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    link.setAttribute('download', fileName);
    link.click();
}

/**
 * Format date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.dataset.tooltip;
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            tooltip.style.cssText = `
                position: absolute;
                background: #333;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 4px;
                font-size: 0.85rem;
                z-index: 1000;
                white-space: nowrap;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
}

/**
 * Confirmation dialog
 */
function confirmAction(message = 'Are you sure?', onConfirm, onCancel) {
    if (confirm(message)) {
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    } else {
        if (typeof onCancel === 'function') {
            onCancel();
        }
    }
}

/**
 * Disable button while processing
 */
function disableButton(buttonSelector, loadingText = 'Processing...') {
    const button = document.querySelector(buttonSelector);
    if (button) {
        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.textContent = loadingText;
    }
}

/**
 * Enable button after processing
 */
function enableButton(buttonSelector) {
    const button = document.querySelector(buttonSelector);
    if (button) {
        button.disabled = false;
        button.textContent = button.dataset.originalText || 'Submit';
    }
}

// Export functions for use in HTML
window.openModal = openModal;
window.closeModal = closeModal;
window.deleteItem = deleteItem;
window.bulkDelete = bulkDelete;
window.searchTable = searchTable;
window.exportTableToCSV = exportTableToCSV;
window.confirmAction = confirmAction;
window.formatDate = formatDate;
window.showAlert = showAlert;
