/**
 * Import JavaScript
 * Handles CSV import functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeFileUpload();
    initializeImportProgress();
});

/**
 * Initialize file upload functionality
 */
function initializeFileUpload() {
    const fileInput = document.getElementById('csv_file');
    const uploadArea = document.getElementById('fileUploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadForm = document.getElementById('csvUploadForm');
    
    if (!fileInput || !uploadArea) return;
    
    // File input change handler
    fileInput.addEventListener('change', function(e) {
        handleFileSelection(e.target.files[0]);
    });
    
    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelection(files[0]);
        }
    });
    
    // Form submission handler
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!fileInput.files[0]) {
                showAlert('Please select a file first.', 'warning');
                return;
            }
            
            // Show progress modal
            const progressModal = new bootstrap.Modal(document.getElementById('uploadProgressModal'));
            progressModal.show();
            
            // Submit form
            uploadForm.submit();
        });
    }
    
    /**
     * Handle file selection
     */
    function handleFileSelection(file) {
        if (!file) {
            fileInfo.classList.add('d-none');
            uploadBtn.disabled = true;
            return;
        }
        
        // Validate file type
        if (!file.name.toLowerCase().endsWith('.csv')) {
            showAlert('Please select a CSV file.', 'danger');
            fileInput.value = '';
            return;
        }
        
        // Validate file size
        const maxSize = 50 * 1024 * 1024; // 50MB
        if (file.size > maxSize) {
            showAlert('File size exceeds maximum allowed size of 50MB.', 'danger');
            fileInput.value = '';
            return;
        }
        
        // Display file info
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.classList.remove('d-none');
        uploadBtn.disabled = false;
    }
}

/**
 * Initialize import progress tracking
 */
function initializeImportProgress() {
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id');
    
    if (sessionId && window.location.pathname.includes('/import/')) {
        trackImportProgress(sessionId);
    }
}

/**
 * Track import progress
 */
function trackImportProgress(sessionId) {
    const progressInterval = setInterval(() => {
        fetch(`/import/status?session_id=${sessionId}`)
            .then(response => response.json())
            .then(data => {
                updateProgressDisplay(data);
                
                if (data.status === 'completed' || data.status === 'failed') {
                    clearInterval(progressInterval);
                    
                    if (data.status === 'completed') {
                        showAlert('Import completed successfully!', 'success');
                    } else {
                        showAlert('Import failed. Please check the error report.', 'danger');
                    }
                }
            })
            .catch(error => {
                console.error('Error tracking progress:', error);
                clearInterval(progressInterval);
            });
    }, 2000); // Check every 2 seconds
}

/**
 * Update progress display
 */
function updateProgressDisplay(data) {
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    
    if (progressBar) {
        progressBar.style.width = data.progress + '%';
        progressBar.setAttribute('aria-valuenow', data.progress);
    }
    
    if (progressText) {
        progressText.textContent = `${data.processed_rows} / ${data.total_rows} rows processed (${data.progress}%)`;
    }
}

/**
 * Preview import data
 */
function previewImport() {
    const previewModal = document.getElementById('previewModal');
    if (previewModal) {
        const modal = new bootstrap.Modal(previewModal);
        modal.show();
    }
}

/**
 * Confirm import execution
 */
function confirmImport() {
    if (confirm('Are you sure you want to import this data? This action cannot be undone.')) {
        const form = document.getElementById('executeImportForm');
        if (form) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-spinner-border spinner-border-sm me-1"></i>Importing...';
            }
            
            form.submit();
        }
    }
}

/**
 * Cancel import operation
 */
function cancelImport() {
    if (confirm('Are you sure you want to cancel this import operation?')) {
        window.location.href = '/import/cancel';
    }
}

/**
 * Download error report
 */
function downloadErrorReport(errors) {
    const csvContent = generateErrorReportCSV(errors);
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = 'import_errors_' + new Date().toISOString().slice(0, 10) + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

/**
 * Generate error report CSV
 */
function generateErrorReportCSV(errors) {
    const headers = ['Row', 'NIDHH', 'Field', 'Error'];
    const rows = [headers];
    
    errors.forEach(error => {
        rows.push([
            error.row || '',
            error.nidhh || '',
            error.field || '',
            error.error || ''
        ]);
    });
    
    return rows.map(row => 
        row.map(field => `"${field.toString().replace(/"/g, '""')}"`).join(',')
    ).join('\n');
}

/**
 * Validate CSV headers
 */
function validateCSVHeaders(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const csv = e.target.result;
            const lines = csv.split('\n');
            
            if (lines.length === 0) {
                reject('File is empty');
                return;
            }
            
            const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
            const expectedHeaders = [
                'State', 'LGA', 'Ward', 'Community', 'nidhh', 'HouseHoldNo', 'HAddress',
                'TrancheStatus', 'TotalAmount',
                'FirstTrancheRecipient', 'FirstTrancheAccountNumber', 'FirstTrancheBankName',
                'FirstTranchePaymentDate', 'FirstTranchePhone', 'FirstTrancheGender',
                'FirstTrancheAge', 'FirstTrancheIDType',
                'SecondTrancheRecipient', 'SecondTrancheAccountNumber', 'SecondTrancheBankName',
                'SecondTranchePaymentDate', 'SecondTranchePhone', 'SecondTrancheGender',
                'SecondTrancheAge', 'SecondTrancheIDType',
                'ThirdTrancheRecipient', 'ThirdTrancheAccountNumber', 'ThirdTrancheBankName',
                'ThirdTranchePaymentDate', 'ThirdTranchePhone', 'ThirdTrancheGender',
                'ThirdTrancheAge', 'ThirdTrancheIDType'
            ];
            
            const missingHeaders = expectedHeaders.filter(h => !headers.includes(h));
            const extraHeaders = headers.filter(h => !expectedHeaders.includes(h));
            
            if (missingHeaders.length > 0 || extraHeaders.length > 0) {
                let errorMsg = 'Header validation failed:\n';
                if (missingHeaders.length > 0) {
                    errorMsg += `Missing headers: ${missingHeaders.join(', ')}\n`;
                }
                if (extraHeaders.length > 0) {
                    errorMsg += `Extra headers: ${extraHeaders.join(', ')}`;
                }
                reject(errorMsg);
            } else {
                resolve(true);
            }
        };
        
        reader.onerror = function() {
            reject('Error reading file');
        };
        
        // Read first 1KB to check headers
        reader.readAsText(file.slice(0, 1024));
    });
}

/**
 * Format file size for display
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
 * Show loading spinner
 */
function showLoading(message = 'Processing...') {
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
