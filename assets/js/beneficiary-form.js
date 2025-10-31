/**
 * Beneficiary Form JavaScript
 * Handles form validation, preview, and submission
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeBeneficiaryForm();
});

/**
 * Initialize beneficiary form functionality
 */
function initializeBeneficiaryForm() {
    const form = document.getElementById('beneficiaryForm');
    if (!form) return;
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize amount calculations
    initializeAmountCalculations();
    
    // Initialize form submission
    form.addEventListener('submit', handleFormSubmission);
    
    // Initialize NIDHH uniqueness check
    initializeNidhhValidation();
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const form = document.getElementById('beneficiaryForm');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
    
    // Phone number validation
    const phoneInputs = form.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', validatePhoneNumber);
    });
    
    // Age validation
    const ageInputs = form.querySelectorAll('input[type="number"][max="120"]');
    ageInputs.forEach(input => {
        input.addEventListener('input', validateAge);
    });
    
    // Amount validation
    const amountInputs = form.querySelectorAll('input[step="0.01"]');
    amountInputs.forEach(input => {
        input.addEventListener('input', validateAmount);
    });
}

/**
 * Initialize amount calculations
 */
function initializeAmountCalculations() {
    const amountInputs = document.querySelectorAll('[name$="TrancheAmount"]');
    
    amountInputs.forEach(input => {
        input.addEventListener('input', calculateTotalAmount);
    });
}

/**
 * Initialize NIDHH uniqueness validation
 */
function initializeNidhhValidation() {
    const nidhhInput = document.getElementById('nidhh');
    if (!nidhhInput) return;
    
    let validationTimeout;
    
    nidhhInput.addEventListener('input', function() {
        clearTimeout(validationTimeout);
        
        const nidhh = this.value.trim();
        if (nidhh.length >= 3) {
            validationTimeout = setTimeout(() => {
                checkNidhhUniqueness(nidhh);
            }, 500);
        }
    });
}

/**
 * Check NIDHH uniqueness
 */
function checkNidhhUniqueness(nidhh) {
    const nidhhInput = document.getElementById('nidhh');
    
    // For now, we'll skip the AJAX check and rely on server-side validation
    // In a full implementation, you'd make an AJAX call to check uniqueness
    console.log('Would check NIDHH uniqueness for:', nidhh);
}

/**
 * Validate individual field
 */
function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    // NIDHH length validation
    if (field.name === 'nidhh' && value.length > 50) {
        showFieldError(field, 'NIDHH cannot exceed 50 characters');
        return false;
    }
    
    clearFieldError(field);
    return true;
}

/**
 * Validate phone number
 */
function validatePhoneNumber(event) {
    const field = event.target;
    const value = field.value.trim();
    
    if (value && !/^[0-9]{7,15}$/.test(value)) {
        showFieldError(field, 'Phone number must be 7-15 digits');
        return false;
    }
    
    clearFieldError(field);
    return true;
}

/**
 * Validate age
 */
function validateAge(event) {
    const field = event.target;
    const value = parseInt(field.value);
    
    if (field.value && (isNaN(value) || value < 0 || value > 120)) {
        showFieldError(field, 'Age must be between 0 and 120 years');
        return false;
    }
    
    clearFieldError(field);
    return true;
}

/**
 * Validate amount
 */
function validateAmount(event) {
    const field = event.target;
    const value = parseFloat(field.value);
    
    if (field.value && (isNaN(value) || value < 0)) {
        showFieldError(field, 'Amount must be a positive number');
        return false;
    }
    
    clearFieldError(field);
    return true;
}

/**
 * Calculate total amount from tranche amounts
 */
function calculateTotalAmount() {
    const firstAmount = parseFloat(document.getElementById('FirstTrancheAmount')?.value || 0);
    const secondAmount = parseFloat(document.getElementById('SecondTrancheAmount')?.value || 0);
    const thirdAmount = parseFloat(document.getElementById('ThirdTrancheAmount')?.value || 0);
    
    const totalAmount = firstAmount + secondAmount + thirdAmount;
    
    // Display total amount somewhere (you could add a display element)
    console.log('Total Amount:', totalAmount);
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    let feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

/**
 * Clear field error
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid');
}

/**
 * Handle form submission
 */
function handleFormSubmission(event) {
    event.preventDefault();
    
    if (!validateForm()) {
        showAlert('Please correct the errors in the form before submitting.', 'danger');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-spinner-border spinner-border-sm me-1"></i>Saving...';
    
    // Submit form
    event.target.submit();
}

/**
 * Validate entire form
 */
function validateForm() {
    const form = document.getElementById('beneficiaryForm');
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!validateField({ target: field })) {
            isValid = false;
        }
    });
    
    // Validate phone numbers
    const phoneFields = form.querySelectorAll('input[type="tel"]');
    phoneFields.forEach(field => {
        if (!validatePhoneNumber({ target: field })) {
            isValid = false;
        }
    });
    
    // Validate ages
    const ageFields = form.querySelectorAll('input[type="number"][max="120"]');
    ageFields.forEach(field => {
        if (!validateAge({ target: field })) {
            isValid = false;
        }
    });
    
    // Validate amounts
    const amountFields = form.querySelectorAll('input[step="0.01"]');
    amountFields.forEach(field => {
        if (!validateAmount({ target: field })) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Preview beneficiary data
 */
function previewBeneficiary() {
    if (!validateForm()) {
        showAlert('Please correct the errors in the form before previewing.', 'danger');
        return;
    }
    
    const formData = new FormData(document.getElementById('beneficiaryForm'));
    const data = Object.fromEntries(formData.entries());
    
    const previewContent = generatePreviewContent(data);
    document.getElementById('previewContent').innerHTML = previewContent;
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

/**
 * Generate preview content
 */
function generatePreviewContent(data) {
    const formatCurrency = (amount) => {
        return amount ? '₦' + parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) : '₦0.00';
    };
    
    const formatDate = (date) => {
        return date ? new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }) : 'Not set';
    };
    
    // Calculate total amount
    const firstAmount = parseFloat(data.FirstTrancheAmount || 0);
    const secondAmount = parseFloat(data.SecondTrancheAmount || 0);
    const thirdAmount = parseFloat(data.ThirdTrancheAmount || 0);
    const totalAmount = firstAmount + secondAmount + thirdAmount;
    
    return `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>NIDHH:</strong></td><td>${data.nidhh || 'Not provided'}</td></tr>
                    <tr><td><strong>Household No:</strong></td><td>${data.HouseHoldNo || 'Not provided'}</td></tr>
                    <tr><td><strong>Address:</strong></td><td>${data.HAddress || 'Not provided'}</td></tr>
                </table>
                
                <h6><i class="bi bi-geo-alt me-2"></i>Location</h6>
                <table class="table table-sm">
                    <tr><td><strong>State:</strong></td><td>${data.State || 'Not provided'}</td></tr>
                    <tr><td><strong>LGA:</strong></td><td>${data.LGA || 'Not provided'}</td></tr>
                    <tr><td><strong>Ward:</strong></td><td>${data.Ward || 'Not provided'}</td></tr>
                    <tr><td><strong>Community:</strong></td><td>${data.Community || 'Not provided'}</td></tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h6><i class="bi bi-currency-exchange me-2"></i>Financial Summary</h6>
                <table class="table table-sm">
                    <tr><td><strong>First Tranche:</strong></td><td>${formatCurrency(data.FirstTrancheAmount)}</td></tr>
                    <tr><td><strong>Second Tranche:</strong></td><td>${formatCurrency(data.SecondTrancheAmount)}</td></tr>
                    <tr><td><strong>Third Tranche:</strong></td><td>${formatCurrency(data.ThirdTrancheAmount)}</td></tr>
                    <tr class="table-primary"><td><strong>Total Amount:</strong></td><td><strong>${formatCurrency(totalAmount)}</strong></td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6><i class="bi bi-people me-2"></i>Tranche Recipients</h6>
                <div class="row">
                    ${generateTranchePreview('First', data)}
                    ${generateTranchePreview('Second', data)}
                    ${generateTranchePreview('Third', data)}
                </div>
            </div>
        </div>
    `;
}

/**
 * Generate tranche preview
 */
function generateTranchePreview(tranche, data) {
    const recipient = data[`${tranche}TrancheRecipient`];
    const account = data[`${tranche}TrancheAccountNumber`];
    const bank = data[`${tranche}TrancheBankName`];
    const paymentDate = data[`${tranche}TranchePaymentDate`];
    const phone = data[`${tranche}TranchePhone`];
    const gender = data[`${tranche}TrancheGender`];
    const age = data[`${tranche}TrancheAge`];
    const idType = data[`${tranche}TrancheIDType`];
    
    if (!recipient && !account && !bank) {
        return `
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">${tranche} Tranche</h6>
                    </div>
                    <div class="card-body text-center text-muted">
                        <i class="bi bi-dash-circle"></i>
                        <p class="mb-0">No information provided</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    return `
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">${tranche} Tranche</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        ${recipient ? `<tr><td><strong>Recipient:</strong></td><td>${recipient}</td></tr>` : ''}
                        ${account ? `<tr><td><strong>Account:</strong></td><td>${account}</td></tr>` : ''}
                        ${bank ? `<tr><td><strong>Bank:</strong></td><td>${bank}</td></tr>` : ''}
                        ${paymentDate ? `<tr><td><strong>Payment Date:</strong></td><td>${new Date(paymentDate).toLocaleDateString()}</td></tr>` : ''}
                        ${phone ? `<tr><td><strong>Phone:</strong></td><td>${phone}</td></tr>` : ''}
                        ${gender ? `<tr><td><strong>Gender:</strong></td><td>${gender}</td></tr>` : ''}
                        ${age ? `<tr><td><strong>Age:</strong></td><td>${age} years</td></tr>` : ''}
                        ${idType ? `<tr><td><strong>ID Type:</strong></td><td>${idType}</td></tr>` : ''}
                    </table>
                </div>
            </div>
        </div>
    `;
}

/**
 * Submit form from preview modal
 */
function submitForm() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
    modal.hide();
    
    document.getElementById('beneficiaryForm').submit();
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
 * Copy data from one tranche to another
 */
function copyTrancheData(fromTranche, toTranche) {
    const fields = ['Recipient', 'AccountNumber', 'BankName', 'Phone', 'Gender', 'Age', 'IDType'];
    
    fields.forEach(field => {
        const fromField = document.getElementById(`${fromTranche}Tranche${field}`);
        const toField = document.getElementById(`${toTranche}Tranche${field}`);
        
        if (fromField && toField && fromField.value) {
            toField.value = fromField.value;
        }
    });
    
    showAlert(`${fromTranche} tranche data copied to ${toTranche} tranche.`, 'success');
}

/**
 * Clear tranche data
 */
function clearTrancheData(tranche) {
    const fields = ['Recipient', 'AccountNumber', 'BankName', 'PaymentDate', 'Phone', 'Gender', 'Age', 'IDType', 'Amount'];
    
    fields.forEach(field => {
        const fieldElement = document.getElementById(`${tranche}Tranche${field}`);
        if (fieldElement) {
            fieldElement.value = '';
            clearFieldError(fieldElement);
        }
    });
    
    calculateTotalAmount();
    showAlert(`${tranche} tranche data cleared.`, 'info');
}
