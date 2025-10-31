/**
 * Beneficiaries JavaScript
 * Handles beneficiary list functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeBeneficiariesPage();
});

/**
 * Initialize beneficiaries page functionality
 */
function initializeBeneficiariesPage() {
    // Initialize location filters
    initializeLocationFilters();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize export functionality
    initializeExport();
}

/**
 * Initialize hierarchical location filters
 */
function initializeLocationFilters() {
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    const wardSelect = document.getElementById('ward');
    const communitySelect = document.getElementById('community');
    
    if (!stateSelect) return;
    
    // Store original options for reset
    window.originalOptions = {
        lgas: Array.from(lgaSelect.options).map(opt => ({ value: opt.value, text: opt.text })),
        wards: Array.from(wardSelect.options).map(opt => ({ value: opt.value, text: opt.text })),
        communities: Array.from(communitySelect.options).map(opt => ({ value: opt.value, text: opt.text }))
    };
}

/**
 * Update location filters based on selection
 */
function updateLocationFilters() {
    const state = document.getElementById('state').value;
    const lga = document.getElementById('lga').value;
    const ward = document.getElementById('ward').value;
    
    // Update LGAs based on state
    updateSelectOptions('lga', state ? `/api/locations/lgas?state=${encodeURIComponent(state)}` : null, 'lgas');
    
    // Update Wards based on state and LGA
    if (state && lga) {
        updateSelectOptions('ward', `/api/locations/wards?state=${encodeURIComponent(state)}&lga=${encodeURIComponent(lga)}`, 'wards');
    } else {
        updateSelectOptions('ward', null, 'wards');
    }
    
    // Update Communities based on state, LGA, and Ward
    if (state && lga && ward) {
        updateSelectOptions('community', `/api/locations/communities?state=${encodeURIComponent(state)}&lga=${encodeURIComponent(lga)}&ward=${encodeURIComponent(ward)}`, 'communities');
    } else {
        updateSelectOptions('community', null, 'communities');
    }
}

/**
 * Update select options via AJAX or reset to original
 */
function updateSelectOptions(selectId, url, originalKey) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    if (!url) {
        // Reset to original options
        select.innerHTML = '<option value="">All ' + selectId.toUpperCase() + 's</option>';
        if (window.originalOptions && window.originalOptions[originalKey]) {
            window.originalOptions[originalKey].forEach(option => {
                if (option.value) {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value;
                    optionElement.textContent = option.text;
                    select.appendChild(optionElement);
                }
            });
        }
        return;
    }
    
    // For now, we'll use the form submission approach
    // In a full implementation, you'd make AJAX calls to get filtered options
    console.log('Would fetch options from:', url);
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchInput = document.getElementById('search');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Auto-submit form after 500ms of no typing
            if (this.value.length >= 3 || this.value.length === 0) {
                document.getElementById('filtersForm').submit();
            }
        }, 500);
    });
    
    // Submit on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('filtersForm').submit();
        }
    });
}

/**
 * Initialize export functionality
 */
function initializeExport() {
    // Export button is handled by onclick in the template
}

/**
 * Export beneficiaries with current filters
 */
function exportBeneficiaries() {
    const form = document.getElementById('filtersForm');
    if (!form) return;
    
    // Create a temporary form for export
    const exportForm = document.createElement('form');
    exportForm.method = 'GET';
    exportForm.action = '/beneficiaries';
    exportForm.style.display = 'none';
    
    // Copy all filter values
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        exportForm.appendChild(input);
    }
    
    // Add export parameter
    const exportInput = document.createElement('input');
    exportInput.type = 'hidden';
    exportInput.name = 'export';
    exportInput.value = 'csv';
    exportForm.appendChild(exportInput);
    
    // Submit form
    document.body.appendChild(exportForm);
    exportForm.submit();
    document.body.removeChild(exportForm);
    
    // Show success message
    showAlert('Export started. Your download will begin shortly.', 'info');
}

/**
 * Change items per page
 */
function changePerPage(perPage) {
    const form = document.getElementById('filtersForm');
    if (!form) return;
    
    // Add per_page parameter
    let perPageInput = form.querySelector('input[name="per_page"]');
    if (!perPageInput) {
        perPageInput = document.createElement('input');
        perPageInput.type = 'hidden';
        perPageInput.name = 'per_page';
        form.appendChild(perPageInput);
    }
    perPageInput.value = perPage;
    
    // Reset to first page
    let pageInput = form.querySelector('input[name="page"]');
    if (!pageInput) {
        pageInput = document.createElement('input');
        pageInput.type = 'hidden';
        pageInput.name = 'page';
        form.appendChild(pageInput);
    }
    pageInput.value = '1';
    
    // Submit form
    form.submit();
}

/**
 * Delete beneficiary
 */
function deleteBeneficiary(nidhh) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Set NIDHH in modal
    document.getElementById('deleteNidhh').textContent = nidhh;
    document.getElementById('deleteNidhhInput').value = nidhh;
    
    // Show modal
    modal.show();
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.alert-container') || document.body;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.insertBefore(alert, alertContainer.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

/**
 * Format currency for display
 */
function formatCurrency(amount) {
    return '₦' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format number for display
 */
function formatNumber(number) {
    return parseInt(number).toLocaleString();
}

/**
 * Validate form before submission
 */
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Show loading spinner
 */
function showLoading(message = 'Loading...') {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary spinner-border-lg mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-white">${message}</p>
        </div>
    `;
    document.body.appendChild(spinner);
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    const spinner = document.querySelector('.spinner-overlay');
    if (spinner) {
        spinner.remove();
    }
}
