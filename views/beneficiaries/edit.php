<?php
// Edit beneficiary form
$additionalJS = ['/assets/js/beneficiary-form.js'];
?>

<form method="POST" action="/beneficiaries/edit?nidhh=<?php echo urlencode($beneficiary['nidhh']); ?>" id="beneficiaryForm" novalidate>
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
                           value="<?php echo htmlspecialchars($beneficiary['nidhh']); ?>" 
                           maxlength="50" required readonly>
                    <div class="form-text">NIDHH cannot be changed after creation</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="HouseHoldNo" class="form-label">Household Number</label>
                    <input type="text" class="form-control" id="HouseHoldNo" name="HouseHoldNo" 
                           value="<?php echo htmlspecialchars($beneficiary['HouseHoldNo'] ?? ''); ?>">
                    <div class="form-text">Internal household reference number</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="HAddress" class="form-label">Household Address</label>
                <textarea class="form-control" id="HAddress" name="HAddress" rows="3"><?php echo htmlspecialchars($beneficiary['HAddress'] ?? ''); ?></textarea>
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
                           value="<?php echo htmlspecialchars($beneficiary['State']); ?>" required>
                    <div class="invalid-feedback">State is required.</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="LGA" class="form-label">LGA <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="LGA" name="LGA" 
                           value="<?php echo htmlspecialchars($beneficiary['LGA']); ?>" required>
                    <div class="invalid-feedback">LGA is required.</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Ward" class="form-label">Ward <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Ward" name="Ward" 
                           value="<?php echo htmlspecialchars($beneficiary['Ward']); ?>" required>
                    <div class="invalid-feedback">Ward is required.</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="Community" class="form-label">Community <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Community" name="Community" 
                           value="<?php echo htmlspecialchars($beneficiary['Community']); ?>" required>
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
                <?php if ($beneficiary['FirstTranchePaymentDate']): ?>
                    <span class="badge bg-success ms-2">Paid</span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheRecipient" class="form-label">Recipient Name</label>
                    <input type="text" class="form-control" id="FirstTrancheRecipient" name="FirstTrancheRecipient" 
                           value="<?php echo htmlspecialchars($beneficiary['FirstTrancheRecipient'] ?? ''); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheAccountNumber" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="FirstTrancheAccountNumber" name="FirstTrancheAccountNumber" 
                           value="<?php echo htmlspecialchars($beneficiary['FirstTrancheAccountNumber'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheBankName" class="form-label">Bank Name</label>
                    <select class="form-select" id="FirstTrancheBankName" name="FirstTrancheBankName">
                        <option value="">Select Bank</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?php echo htmlspecialchars($bank['name']); ?>" 
                                    <?php echo ($beneficiary['FirstTrancheBankName'] ?? '') === $bank['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bank['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="FirstTranchePaymentDate" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="FirstTranchePaymentDate" name="FirstTranchePaymentDate" 
                           value="<?php echo $beneficiary['FirstTranchePaymentDate'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="FirstTranchePhone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="FirstTranchePhone" name="FirstTranchePhone" 
                           value="<?php echo htmlspecialchars($beneficiary['FirstTranchePhone'] ?? ''); ?>"
                           pattern="[0-9]{7,15}">
                    <div class="form-text">7-15 digits</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="FirstTrancheGender" class="form-label">Gender</label>
                    <select class="form-select" id="FirstTrancheGender" name="FirstTrancheGender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($beneficiary['FirstTrancheGender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($beneficiary['FirstTrancheGender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($beneficiary['FirstTrancheGender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        <option value="Unknown" <?php echo ($beneficiary['FirstTrancheGender'] ?? '') === 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="FirstTrancheAge" class="form-label">Age</label>
                    <input type="number" class="form-control" id="FirstTrancheAge" name="FirstTrancheAge" 
                           value="<?php echo $beneficiary['FirstTrancheAge'] ?? ''; ?>"
                           min="0" max="120">
                    <div class="form-text">0-120 years</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheIDType" class="form-label">ID Type</label>
                    <select class="form-select" id="FirstTrancheIDType" name="FirstTrancheIDType">
                        <option value="">Select ID Type</option>
                        <option value="NIN" <?php echo ($beneficiary['FirstTrancheIDType'] ?? '') === 'NIN' ? 'selected' : ''; ?>>NIN</option>
                        <option value="BVN" <?php echo ($beneficiary['FirstTrancheIDType'] ?? '') === 'BVN' ? 'selected' : ''; ?>>BVN</option>
                        <option value="Voters Card" <?php echo ($beneficiary['FirstTrancheIDType'] ?? '') === 'Voters Card' ? 'selected' : ''; ?>>Voters Card</option>
                        <option value="Drivers License" <?php echo ($beneficiary['FirstTrancheIDType'] ?? '') === 'Drivers License' ? 'selected' : ''; ?>>Drivers License</option>
                        <option value="International Passport" <?php echo ($beneficiary['FirstTrancheIDType'] ?? '') === 'International Passport' ? 'selected' : ''; ?>>International Passport</option>
                        <option value="Other" <?php echo ($beneficiary['FirstTrancheIDType'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="FirstTrancheAmount" class="form-label">Amount (₦)</label>
                    <input type="number" class="form-control" id="FirstTrancheAmount" name="FirstTrancheAmount" 
                           value="<?php echo $beneficiary['FirstTrancheAmount'] ?? ''; ?>"
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
                <?php if ($beneficiary['SecondTranchePaymentDate']): ?>
                    <span class="badge bg-success ms-2">Paid</span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheRecipient" class="form-label">Recipient Name</label>
                    <input type="text" class="form-control" id="SecondTrancheRecipient" name="SecondTrancheRecipient" 
                           value="<?php echo htmlspecialchars($beneficiary['SecondTrancheRecipient'] ?? ''); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheAccountNumber" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="SecondTrancheAccountNumber" name="SecondTrancheAccountNumber" 
                           value="<?php echo htmlspecialchars($beneficiary['SecondTrancheAccountNumber'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheBankName" class="form-label">Bank Name</label>
                    <select class="form-select" id="SecondTrancheBankName" name="SecondTrancheBankName">
                        <option value="">Select Bank</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?php echo htmlspecialchars($bank['name']); ?>" 
                                    <?php echo ($beneficiary['SecondTrancheBankName'] ?? '') === $bank['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bank['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="SecondTranchePaymentDate" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="SecondTranchePaymentDate" name="SecondTranchePaymentDate" 
                           value="<?php echo $beneficiary['SecondTranchePaymentDate'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="SecondTranchePhone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="SecondTranchePhone" name="SecondTranchePhone" 
                           value="<?php echo htmlspecialchars($beneficiary['SecondTranchePhone'] ?? ''); ?>"
                           pattern="[0-9]{7,15}">
                    <div class="form-text">7-15 digits</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="SecondTrancheGender" class="form-label">Gender</label>
                    <select class="form-select" id="SecondTrancheGender" name="SecondTrancheGender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($beneficiary['SecondTrancheGender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($beneficiary['SecondTrancheGender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($beneficiary['SecondTrancheGender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        <option value="Unknown" <?php echo ($beneficiary['SecondTrancheGender'] ?? '') === 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="SecondTrancheAge" class="form-label">Age</label>
                    <input type="number" class="form-control" id="SecondTrancheAge" name="SecondTrancheAge" 
                           value="<?php echo $beneficiary['SecondTrancheAge'] ?? ''; ?>"
                           min="0" max="120">
                    <div class="form-text">0-120 years</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheIDType" class="form-label">ID Type</label>
                    <select class="form-select" id="SecondTrancheIDType" name="SecondTrancheIDType">
                        <option value="">Select ID Type</option>
                        <option value="NIN" <?php echo ($beneficiary['SecondTrancheIDType'] ?? '') === 'NIN' ? 'selected' : ''; ?>>NIN</option>
                        <option value="BVN" <?php echo ($beneficiary['SecondTrancheIDType'] ?? '') === 'BVN' ? 'selected' : ''; ?>>BVN</option>
                        <option value="Voters Card" <?php echo ($beneficiary['SecondTrancheIDType'] ?? '') === 'Voters Card' ? 'selected' : ''; ?>>Voters Card</option>
                        <option value="Drivers License" <?php echo ($beneficiary['SecondTrancheIDType'] ?? '') === 'Drivers License' ? 'selected' : ''; ?>>Drivers License</option>
                        <option value="International Passport" <?php echo ($beneficiary['SecondTrancheIDType'] ?? '') === 'International Passport' ? 'selected' : ''; ?>>International Passport</option>
                        <option value="Other" <?php echo ($beneficiary['SecondTrancheIDType'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="SecondTrancheAmount" class="form-label">Amount (₦)</label>
                    <input type="number" class="form-control" id="SecondTrancheAmount" name="SecondTrancheAmount" 
                           value="<?php echo $beneficiary['SecondTrancheAmount'] ?? ''; ?>"
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
                <?php if ($beneficiary['ThirdTranchePaymentDate']): ?>
                    <span class="badge bg-success ms-2">Paid</span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheRecipient" class="form-label">Recipient Name</label>
                    <input type="text" class="form-control" id="ThirdTrancheRecipient" name="ThirdTrancheRecipient" 
                           value="<?php echo htmlspecialchars($beneficiary['ThirdTrancheRecipient'] ?? ''); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheAccountNumber" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="ThirdTrancheAccountNumber" name="ThirdTrancheAccountNumber" 
                           value="<?php echo htmlspecialchars($beneficiary['ThirdTrancheAccountNumber'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheBankName" class="form-label">Bank Name</label>
                    <select class="form-select" id="ThirdTrancheBankName" name="ThirdTrancheBankName">
                        <option value="">Select Bank</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?php echo htmlspecialchars($bank['name']); ?>" 
                                    <?php echo ($beneficiary['ThirdTrancheBankName'] ?? '') === $bank['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bank['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ThirdTranchePaymentDate" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="ThirdTranchePaymentDate" name="ThirdTranchePaymentDate" 
                           value="<?php echo $beneficiary['ThirdTranchePaymentDate'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="ThirdTranchePhone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="ThirdTranchePhone" name="ThirdTranchePhone" 
                           value="<?php echo htmlspecialchars($beneficiary['ThirdTranchePhone'] ?? ''); ?>"
                           pattern="[0-9]{7,15}">
                    <div class="form-text">7-15 digits</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="ThirdTrancheGender" class="form-label">Gender</label>
                    <select class="form-select" id="ThirdTrancheGender" name="ThirdTrancheGender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($beneficiary['ThirdTrancheGender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($beneficiary['ThirdTrancheGender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($beneficiary['ThirdTrancheGender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        <option value="Unknown" <?php echo ($beneficiary['ThirdTrancheGender'] ?? '') === 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="ThirdTrancheAge" class="form-label">Age</label>
                    <input type="number" class="form-control" id="ThirdTrancheAge" name="ThirdTrancheAge" 
                           value="<?php echo $beneficiary['ThirdTrancheAge'] ?? ''; ?>"
                           min="0" max="120">
                    <div class="form-text">0-120 years</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheIDType" class="form-label">ID Type</label>
                    <select class="form-select" id="ThirdTrancheIDType" name="ThirdTrancheIDType">
                        <option value="">Select ID Type</option>
                        <option value="NIN" <?php echo ($beneficiary['ThirdTrancheIDType'] ?? '') === 'NIN' ? 'selected' : ''; ?>>NIN</option>
                        <option value="BVN" <?php echo ($beneficiary['ThirdTrancheIDType'] ?? '') === 'BVN' ? 'selected' : ''; ?>>BVN</option>
                        <option value="Voters Card" <?php echo ($beneficiary['ThirdTrancheIDType'] ?? '') === 'Voters Card' ? 'selected' : ''; ?>>Voters Card</option>
                        <option value="Drivers License" <?php echo ($beneficiary['ThirdTrancheIDType'] ?? '') === 'Drivers License' ? 'selected' : ''; ?>>Drivers License</option>
                        <option value="International Passport" <?php echo ($beneficiary['ThirdTrancheIDType'] ?? '') === 'International Passport' ? 'selected' : ''; ?>>International Passport</option>
                        <option value="Other" <?php echo ($beneficiary['ThirdTrancheIDType'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ThirdTrancheAmount" class="form-label">Amount (₦)</label>
                    <input type="number" class="form-control" id="ThirdTrancheAmount" name="ThirdTrancheAmount" 
                           value="<?php echo $beneficiary['ThirdTrancheAmount'] ?? ''; ?>"
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
                        Update Beneficiary
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
                    Preview Beneficiary Changes
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
                    Update Beneficiary
                </button>
            </div>
        </div>
    </div>
</div>
