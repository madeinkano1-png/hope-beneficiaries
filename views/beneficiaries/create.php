<?php
// Create beneficiary form
$additionalJS = ['/assets/js/beneficiary-form.js'];
?>

<form method="POST" action="/beneficiaries/create" id="beneficiaryForm" novalidate>
    <!-- Basic Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Basic Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nidhh" class="form-label">NIDHH <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nidhh" name="nidhh" 
                           value="<?php echo htmlspecialchars($_POST['nidhh'] ?? ''); ?>" 
                           maxlength="50" required>
                    <div class="invalid-feedback">NIDHH is required and must be unique.</div>
                    <div class="form-text">National Identification for Household Head (max 50 characters)</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="HouseHoldNo" class="form-label">Household Number</label>
                    <input type="text" class="form-control" id="HouseHoldNo" name="HouseHoldNo" 
                           value="<?php echo htmlspecialchars($_POST['HouseHoldNo'] ?? ''); ?>">
                    <div class="form-text">Internal household reference number</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="HAddress" class="form-label">Household Address</label>
                <textarea class="form-control" id="HAddress" name="HAddress" rows="3"><?php echo htmlspecialchars($_POST['HAddress'] ?? ''); ?></textarea>
                <div class="form-text">Complete household address</div>
            </div>
        </div>
    </div>
    
    <!-- Location Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-geo-alt me-2"></i>
                Location Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="State" class="form-label">State <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="State" name="State" 
                           value="<?php echo htmlspecialchars($_POST['State'] ?? ''); ?>" required>
                    <div class="invalid-feedback">State is required.</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="LGA" class="form-label">LGA <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="LGA" name="LGA" 
                           value="<?php echo htmlspecialchars($_POST['LGA'] ?? ''); ?>" required>
                    <div class="invalid-feedback">LGA is required.</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Ward" class="form-label">Ward <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Ward" name="Ward" 
                           value="<?php echo htmlspecialchars($_POST['Ward'] ?? ''); ?>" required>
                    <div class="invalid-feedback">Ward is required.</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="Community" class="form-label">Community <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Community" name="Community" 
                           value="<?php echo htmlspecialchars($_POST['Community'] ?? ''); ?>" required>
                    <div class="invalid-feedback">Community is required.</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- First Tranche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-1-circle me-2"></i>
                First Tranche Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheRecipient" class="form-label">Recipient Name</label>
                    <input type="text" class="form-control" id="FirstTrancheRecipient" name="FirstTrancheRecipient" 
                           value="<?php echo htmlspecialchars($_POST['FirstTrancheRecipient'] ?? ''); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheAccountNumber" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="FirstTrancheAccountNumber" name="FirstTrancheAccountNumber" 
                           value="<?php echo htmlspecialchars($_POST['FirstTrancheAccountNumber'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheBankName" class="form-label">Bank Name</label>
                    <select class="form-select" id="FirstTrancheBankName" name="FirstTrancheBankName">
                        <option value="">Select Bank</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?php echo htmlspecialchars($bank['name']); ?>" 
                                    <?php echo ($_POST['FirstTrancheBankName'] ?? '') === $bank['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bank['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="FirstTranchePaymentDate" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="FirstTranchePaymentDate" name="FirstTranchePaymentDate" 
                           value="<?php echo htmlspecialchars($_POST['FirstTranchePaymentDate'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="FirstTranchePhone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="FirstTranchePhone" name="FirstTranchePhone" 
                           value="<?php echo htmlspecialchars($_POST['FirstTranchePhone'] ?? ''); ?>"
                           pattern="[0-9]{7,15}">
                    <div class="form-text">7-15 digits</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="FirstTrancheGender" class="form-label">Gender</label>
                    <select class="form-select" id="FirstTrancheGender" name="FirstTrancheGender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($_POST['FirstTrancheGender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($_POST['FirstTrancheGender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($_POST['FirstTrancheGender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        <option value="Unknown" <?php echo ($_POST['FirstTrancheGender'] ?? '') === 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="FirstTrancheAge" class="form-label">Age</label>
                    <input type="number" class="form-control" id="FirstTrancheAge" name="FirstTrancheAge" 
                           value="<?php echo htmlspecialchars($_POST['FirstTrancheAge'] ?? ''); ?>"
                           min="0" max="120">
                    <div class="form-text">0-120 years</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheIDType" class="form-label">ID Type</label>
                    <select class="form-select" id="FirstTrancheIDType" name="FirstTrancheIDType">
                        <option value="">Select ID Type</option>
                        <option value="NIN" <?php echo ($_POST['FirstTrancheIDType'] ?? '') === 'NIN' ? 'selected' : ''; ?>>NIN</option>
                        <option value="BVN" <?php echo ($_POST['FirstTrancheIDType'] ?? '') === 'BVN' ? 'selected' : ''; ?>>BVN</option>
                        <option value="Voters Card" <?php echo ($_POST['FirstTrancheIDType'] ?? '') === 'Voters Card' ? 'selected' : ''; ?>>Voters Card</option>
                        <option value="Drivers License" <?php echo ($_POST['FirstTrancheIDType'] ?? '') === 'Drivers License' ? 'selected' : ''; ?>>Drivers License</option>
                        <option value="International Passport" <?php echo ($_POST['FirstTrancheIDType'] ?? '') === 'International Passport' ? 'selected' : ''; ?>>International Passport</option>
                        <option value="Other" <?php echo ($_POST['FirstTrancheIDType'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheAmount" class="form-label">Amount (₦)</label>
                    <input type="number" class="form-control" id="FirstTrancheAmount" name="FirstTrancheAmount" 
                           value="<?php echo htmlspecialchars($_POST['FirstTrancheAmount'] ?? ''); ?>"
                           min="0" step="0.01">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Second Tranche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-2-circle me-2"></i>
                Second Tranche Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheRecipient" class="form-label">Recipient Name</label>
                    <input type="text" class="form-control" id="SecondTrancheRecipient" name="SecondTrancheRecipient" 
                           value="<?php echo htmlspecialchars($_POST['SecondTrancheRecipient'] ?? ''); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheAccountNumber" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="SecondTrancheAccountNumber" name="SecondTrancheAccountNumber" 
                           value="<?php echo htmlspecialchars($_POST['SecondTrancheAccountNumber'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheBankName" class="form-label">Bank Name</label>
                    <select class="form-select" id="SecondTrancheBankName" name="SecondTrancheBankName">
                        <option value="">Select Bank</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?php echo htmlspecialchars($bank['name']); ?>" 
                                    <?php echo ($_POST['SecondTrancheBankName'] ?? '') === $bank['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bank['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="SecondTranchePaymentDate" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="SecondTranchePaymentDate" name="SecondTranchePaymentDate" 
                           value="<?php echo htmlspecialchars($_POST['SecondTranchePaymentDate'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="SecondTranchePhone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="SecondTranchePhone" name="SecondTranchePhone" 
                           value="<?php echo htmlspecialchars($_POST['SecondTranchePhone'] ?? ''); ?>"
                           pattern="[0-9]{7,15}">
                    <div class="form-text">7-15 digits</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="SecondTrancheGender" class="form-label">Gender</label>
                    <select class="form-select" id="SecondTrancheGender" name="SecondTrancheGender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($_POST['SecondTrancheGender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($_POST['SecondTrancheGender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($_POST['SecondTrancheGender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        <option value="Unknown" <?php echo ($_POST['SecondTrancheGender'] ?? '') === 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="SecondTrancheAge" class="form-label">Age</label>
                    <input type="number" class="form-control" id="SecondTrancheAge" name="SecondTrancheAge" 
                           value="<?php echo htmlspecialchars($_POST['SecondTrancheAge'] ?? ''); ?>"
                           min="0" max="120">
                    <div class="form-text">0-120 years</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheIDType" class="form-label">ID Type</label>
                    <select class="form-select" id="SecondTrancheIDType" name="SecondTrancheIDType">
                        <option value="">Select ID Type</option>
                        <option value="NIN" <?php echo ($_POST['SecondTrancheIDType'] ?? '') === 'NIN' ? 'selected' : ''; ?>>NIN</option>
                        <option value="BVN" <?php echo ($_POST['SecondTrancheIDType'] ?? '') === 'BVN' ? 'selected' : ''; ?>>BVN</option>
                        <option value="Voters Card" <?php echo ($_POST['SecondTrancheIDType'] ?? '') === 'Voters Card' ? 'selected' : ''; ?>>Voters Card</option>
                        <option value="Drivers License" <?php echo ($_POST['SecondTrancheIDType'] ?? '') === 'Drivers License' ? 'selected' : ''; ?>>Drivers License</option>
                        <option value="International Passport" <?php echo ($_POST['SecondTrancheIDType'] ?? '') === 'International Passport' ? 'selected' : ''; ?>>International Passport</option>
                        <option value="Other" <?php echo ($_POST['SecondTrancheIDType'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheAmount" class="form-label">Amount (₦)</label>
                    <input type="number" class="form-control" id="SecondTrancheAmount" name="SecondTrancheAmount" 
                           value="<?php echo htmlspecialchars($_POST['SecondTrancheAmount'] ?? ''); ?>"
                           min="0" step="0.01">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Third Tranche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-3-circle me-2"></i>
                Third Tranche Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheRecipient" class="form-label">Recipient Name</label>
                    <input type="text" class="form-control" id="ThirdTrancheRecipient" name="ThirdTrancheRecipient" 
                           value="<?php echo htmlspecialchars($_POST['ThirdTrancheRecipient'] ?? ''); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheAccountNumber" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="ThirdTrancheAccountNumber" name="ThirdTrancheAccountNumber" 
                           value="<?php echo htmlspecialchars($_POST['ThirdTrancheAccountNumber'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheBankName" class="form-label">Bank Name</label>
                    <select class="form-select" id="ThirdTrancheBankName" name="ThirdTrancheBankName">
                        <option value="">Select Bank</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?php echo htmlspecialchars($bank['name']); ?>" 
                                    <?php echo ($_POST['ThirdTrancheBankName'] ?? '') === $bank['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bank['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ThirdTranchePaymentDate" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="ThirdTranchePaymentDate" name="ThirdTranchePaymentDate" 
                           value="<?php echo htmlspecialchars($_POST['ThirdTranchePaymentDate'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="ThirdTranchePhone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="ThirdTranchePhone" name="ThirdTranchePhone" 
                           value="<?php echo htmlspecialchars($_POST['ThirdTranchePhone'] ?? ''); ?>"
                           pattern="[0-9]{7,15}">
                    <div class="form-text">7-15 digits</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="ThirdTrancheGender" class="form-label">Gender</label>
                    <select class="form-select" id="ThirdTrancheGender" name="ThirdTrancheGender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($_POST['ThirdTrancheGender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($_POST['ThirdTrancheGender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($_POST['ThirdTrancheGender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        <option value="Unknown" <?php echo ($_POST['ThirdTrancheGender'] ?? '') === 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="ThirdTrancheAge" class="form-label">Age</label>
                    <input type="number" class="form-control" id="ThirdTrancheAge" name="ThirdTrancheAge" 
                           value="<?php echo htmlspecialchars($_POST['ThirdTrancheAge'] ?? ''); ?>"
                           min="0" max="120">
                    <div class="form-text">0-120 years</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheIDType" class="form-label">ID Type</label>
                    <select class="form-select" id="ThirdTrancheIDType" name="ThirdTrancheIDType">
                        <option value="">Select ID Type</option>
                        <option value="NIN" <?php echo ($_POST['ThirdTrancheIDType'] ?? '') === 'NIN' ? 'selected' : ''; ?>>NIN</option>
                        <option value="BVN" <?php echo ($_POST['ThirdTrancheIDType'] ?? '') === 'BVN' ? 'selected' : ''; ?>>BVN</option>
                        <option value="Voters Card" <?php echo ($_POST['ThirdTrancheIDType'] ?? '') === 'Voters Card' ? 'selected' : ''; ?>>Voters Card</option>
                        <option value="Drivers License" <?php echo ($_POST['ThirdTrancheIDType'] ?? '') === 'Drivers License' ? 'selected' : ''; ?>>Drivers License</option>
                        <option value="International Passport" <?php echo ($_POST['ThirdTrancheIDType'] ?? '') === 'International Passport' ? 'selected' : ''; ?>>International Passport</option>
                        <option value="Other" <?php echo ($_POST['ThirdTrancheIDType'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheAmount" class="form-label">Amount (₦)</label>
                    <input type="number" class="form-control" id="ThirdTrancheAmount" name="ThirdTrancheAmount" 
                           value="<?php echo htmlspecialchars($_POST['ThirdTrancheAmount'] ?? ''); ?>"
                           min="0" step="0.01">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Form Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="/beneficiaries" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Cancel
                </a>
                
                <div>
                    <button type="button" class="btn btn-outline-primary me-2" onclick="previewBeneficiary()">
                        <i class="bi bi-eye me-1"></i>
                        Preview
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>
                        Create Beneficiary
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>
                    Preview Beneficiary
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">
                    <i class="bi bi-check-lg me-1"></i>
                    Create Beneficiary
                </button>
            </div>
        </div>
    </div>
</div>
