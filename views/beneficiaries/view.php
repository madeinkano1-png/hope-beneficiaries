<?php
// View beneficiary details
$additionalCSS = ['/assets/css/beneficiary-view.css'];
?>

<div class="row">
    <!-- Main Information -->
    <div class="col-lg-8">
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
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">NIDHH:</td>
                                <td><?php echo htmlspecialchars($beneficiary['nidhh']); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Household No:</td>
                                <td><?php echo htmlspecialchars($beneficiary['HouseHoldNo'] ?? 'Not provided'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    <span class="badge status-<?php echo strtolower($beneficiary['TrancheStatus']); ?> fs-6">
                                        <?php echo $beneficiary['TrancheStatus']; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Amount:</td>
                                <td class="fw-bold text-primary">
                                    ₦<?php echo number_format($beneficiary['TotalAmount'], 2); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Created:</td>
                                <td><?php echo date('M j, Y g:i A', strtotime($beneficiary['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Updated:</td>
                                <td><?php echo date('M j, Y g:i A', strtotime($beneficiary['updated_at'])); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Created By:</td>
                                <td><?php echo htmlspecialchars($beneficiary['created_by'] ?? 'System'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Updated By:</td>
                                <td><?php echo htmlspecialchars($beneficiary['updated_by'] ?? 'System'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($beneficiary['HAddress']): ?>
                    <div class="mt-3">
                        <h6 class="fw-bold">Household Address:</h6>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($beneficiary['HAddress'])); ?></p>
                    </div>
                <?php endif; ?>
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
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="location-item">
                                <i class="bi bi-building display-6 text-primary"></i>
                                <h6 class="mt-2">State</h6>
                                <p class="fw-bold"><?php echo htmlspecialchars($beneficiary['State']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="location-item">
                                <i class="bi bi-geo display-6 text-info"></i>
                                <h6 class="mt-2">LGA</h6>
                                <p class="fw-bold"><?php echo htmlspecialchars($beneficiary['LGA']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="location-item">
                                <i class="bi bi-signpost display-6 text-warning"></i>
                                <h6 class="mt-2">Ward</h6>
                                <p class="fw-bold"><?php echo htmlspecialchars($beneficiary['Ward']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="location-item">
                                <i class="bi bi-house display-6 text-success"></i>
                                <h6 class="mt-2">Community</h6>
                                <p class="fw-bold"><?php echo htmlspecialchars($beneficiary['Community']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tranche Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cash-stack me-2"></i>
                    Tranche Payment Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- First Tranche -->
                    <div class="col-md-4 mb-4">
                        <div class="tranche-card">
                            <div class="tranche-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-1-circle me-2"></i>
                                    First Tranche
                                    <?php if ($beneficiary['FirstTranchePaymentDate']): ?>
                                        <span class="badge bg-success ms-2">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary ms-2">Pending</span>
                                    <?php endif; ?>
                                </h6>
                            </div>
                            <div class="tranche-body">
                                <?php if ($beneficiary['FirstTrancheRecipient'] || $beneficiary['FirstTrancheAccountNumber']): ?>
                                    <table class="table table-sm table-borderless">
                                        <?php if ($beneficiary['FirstTrancheRecipient']): ?>
                                            <tr><td class="fw-bold">Recipient:</td><td><?php echo htmlspecialchars($beneficiary['FirstTrancheRecipient']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTrancheAccountNumber']): ?>
                                            <tr><td class="fw-bold">Account:</td><td><?php echo htmlspecialchars($beneficiary['FirstTrancheAccountNumber']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTrancheBankName']): ?>
                                            <tr><td class="fw-bold">Bank:</td><td><?php echo htmlspecialchars($beneficiary['FirstTrancheBankName']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTranchePaymentDate']): ?>
                                            <tr><td class="fw-bold">Payment Date:</td><td><?php echo date('M j, Y', strtotime($beneficiary['FirstTranchePaymentDate'])); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTranchePhone']): ?>
                                            <tr><td class="fw-bold">Phone:</td><td><?php echo htmlspecialchars($beneficiary['FirstTranchePhone']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTrancheGender']): ?>
                                            <tr><td class="fw-bold">Gender:</td><td><?php echo htmlspecialchars($beneficiary['FirstTrancheGender']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTrancheAge']): ?>
                                            <tr><td class="fw-bold">Age:</td><td><?php echo $beneficiary['FirstTrancheAge']; ?> years</td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTrancheIDType']): ?>
                                            <tr><td class="fw-bold">ID Type:</td><td><?php echo htmlspecialchars($beneficiary['FirstTrancheIDType']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['FirstTrancheAmount']): ?>
                                            <tr><td class="fw-bold">Amount:</td><td class="fw-bold text-success">₦<?php echo number_format($beneficiary['FirstTrancheAmount'], 2); ?></td></tr>
                                        <?php endif; ?>
                                    </table>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-dash-circle display-4"></i>
                                        <p class="mt-2">No information provided</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Second Tranche -->
                    <div class="col-md-4 mb-4">
                        <div class="tranche-card">
                            <div class="tranche-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-2-circle me-2"></i>
                                    Second Tranche
                                    <?php if ($beneficiary['SecondTranchePaymentDate']): ?>
                                        <span class="badge bg-success ms-2">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary ms-2">Pending</span>
                                    <?php endif; ?>
                                </h6>
                            </div>
                            <div class="tranche-body">
                                <?php if ($beneficiary['SecondTrancheRecipient'] || $beneficiary['SecondTrancheAccountNumber']): ?>
                                    <table class="table table-sm table-borderless">
                                        <?php if ($beneficiary['SecondTrancheRecipient']): ?>
                                            <tr><td class="fw-bold">Recipient:</td><td><?php echo htmlspecialchars($beneficiary['SecondTrancheRecipient']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTrancheAccountNumber']): ?>
                                            <tr><td class="fw-bold">Account:</td><td><?php echo htmlspecialchars($beneficiary['SecondTrancheAccountNumber']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTrancheBankName']): ?>
                                            <tr><td class="fw-bold">Bank:</td><td><?php echo htmlspecialchars($beneficiary['SecondTrancheBankName']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTranchePaymentDate']): ?>
                                            <tr><td class="fw-bold">Payment Date:</td><td><?php echo date('M j, Y', strtotime($beneficiary['SecondTranchePaymentDate'])); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTranchePhone']): ?>
                                            <tr><td class="fw-bold">Phone:</td><td><?php echo htmlspecialchars($beneficiary['SecondTranchePhone']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTrancheGender']): ?>
                                            <tr><td class="fw-bold">Gender:</td><td><?php echo htmlspecialchars($beneficiary['SecondTrancheGender']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTrancheAge']): ?>
                                            <tr><td class="fw-bold">Age:</td><td><?php echo $beneficiary['SecondTrancheAge']; ?> years</td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTrancheIDType']): ?>
                                            <tr><td class="fw-bold">ID Type:</td><td><?php echo htmlspecialchars($beneficiary['SecondTrancheIDType']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['SecondTrancheAmount']): ?>
                                            <tr><td class="fw-bold">Amount:</td><td class="fw-bold text-success">₦<?php echo number_format($beneficiary['SecondTrancheAmount'], 2); ?></td></tr>
                                        <?php endif; ?>
                                    </table>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-dash-circle display-4"></i>
                                        <p class="mt-2">No information provided</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Third Tranche -->
                    <div class="col-md-4 mb-4">
                        <div class="tranche-card">
                            <div class="tranche-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-3-circle me-2"></i>
                                    Third Tranche
                                    <?php if ($beneficiary['ThirdTranchePaymentDate']): ?>
                                        <span class="badge bg-success ms-2">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary ms-2">Pending</span>
                                    <?php endif; ?>
                                </h6>
                            </div>
                            <div class="tranche-body">
                                <?php if ($beneficiary['ThirdTrancheRecipient'] || $beneficiary['ThirdTrancheAccountNumber']): ?>
                                    <table class="table table-sm table-borderless">
                                        <?php if ($beneficiary['ThirdTrancheRecipient']): ?>
                                            <tr><td class="fw-bold">Recipient:</td><td><?php echo htmlspecialchars($beneficiary['ThirdTrancheRecipient']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTrancheAccountNumber']): ?>
                                            <tr><td class="fw-bold">Account:</td><td><?php echo htmlspecialchars($beneficiary['ThirdTrancheAccountNumber']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTrancheBankName']): ?>
                                            <tr><td class="fw-bold">Bank:</td><td><?php echo htmlspecialchars($beneficiary['ThirdTrancheBankName']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTranchePaymentDate']): ?>
                                            <tr><td class="fw-bold">Payment Date:</td><td><?php echo date('M j, Y', strtotime($beneficiary['ThirdTranchePaymentDate'])); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTranchePhone']): ?>
                                            <tr><td class="fw-bold">Phone:</td><td><?php echo htmlspecialchars($beneficiary['ThirdTranchePhone']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTrancheGender']): ?>
                                            <tr><td class="fw-bold">Gender:</td><td><?php echo htmlspecialchars($beneficiary['ThirdTrancheGender']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTrancheAge']): ?>
                                            <tr><td class="fw-bold">Age:</td><td><?php echo $beneficiary['ThirdTrancheAge']; ?> years</td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTrancheIDType']): ?>
                                            <tr><td class="fw-bold">ID Type:</td><td><?php echo htmlspecialchars($beneficiary['ThirdTrancheIDType']); ?></td></tr>
                                        <?php endif; ?>
                                        <?php if ($beneficiary['ThirdTrancheAmount']): ?>
                                            <tr><td class="fw-bold">Amount:</td><td class="fw-bold text-success">₦<?php echo number_format($beneficiary['ThirdTrancheAmount'], 2); ?></td></tr>
                                        <?php endif; ?>
                                    </table>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-dash-circle display-4"></i>
                                        <p class="mt-2">No information provided</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (hasPermission('edit_beneficiaries')): ?>
                        <a href="/beneficiaries/edit?nidhh=<?php echo urlencode($beneficiary['nidhh']); ?>" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i>
                            Edit Beneficiary
                        </a>
                    <?php endif; ?>
                    
                    <a href="/beneficiaries?search=<?php echo urlencode($beneficiary['nidhh']); ?>" class="btn btn-outline-info">
                        <i class="bi bi-search me-1"></i>
                        Find Similar
                    </a>
                    
                    <?php if (hasPermission('export_csv')): ?>
                        <a href="/beneficiaries?export=csv&search=<?php echo urlencode($beneficiary['nidhh']); ?>" class="btn btn-outline-success">
                            <i class="bi bi-download me-1"></i>
                            Export Record
                        </a>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('delete_beneficiaries')): ?>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteBeneficiary('<?php echo htmlspecialchars($beneficiary['nidhh']); ?>')">
                            <i class="bi bi-trash me-1"></i>
                            Delete Beneficiary
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Payment Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Payment Summary
                </h5>
            </div>
            <div class="card-body">
                <?php
                $paidTranches = 0;
                $totalTranches = 3;
                $paidAmount = 0;
                
                if ($beneficiary['FirstTranchePaymentDate']) {
                    $paidTranches++;
                    $paidAmount += floatval($beneficiary['FirstTrancheAmount'] ?? 0);
                }
                if ($beneficiary['SecondTranchePaymentDate']) {
                    $paidTranches++;
                    $paidAmount += floatval($beneficiary['SecondTrancheAmount'] ?? 0);
                }
                if ($beneficiary['ThirdTranchePaymentDate']) {
                    $paidTranches++;
                    $paidAmount += floatval($beneficiary['ThirdTrancheAmount'] ?? 0);
                }
                
                $completionRate = ($paidTranches / $totalTranches) * 100;
                ?>
                
                <div class="text-center mb-3">
                    <div class="progress-circle">
                        <div class="progress-text">
                            <span class="h4"><?php echo $paidTranches; ?>/<?php echo $totalTranches; ?></span>
                            <small class="d-block text-muted">Tranches Paid</small>
                        </div>
                    </div>
                </div>
                
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?php echo $completionRate; ?>%" 
                         aria-valuenow="<?php echo $completionRate; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                        <?php echo round($completionRate, 1); ?>%
                    </div>
                </div>
                
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="fw-bold">Total Amount:</td>
                        <td class="text-end fw-bold">₦<?php echo number_format($beneficiary['TotalAmount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-success">Paid Amount:</td>
                        <td class="text-end fw-bold text-success">₦<?php echo number_format($paidAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-warning">Pending Amount:</td>
                        <td class="text-end fw-bold text-warning">₦<?php echo number_format($beneficiary['TotalAmount'] - $paidAmount, 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Audit Trail -->
        <?php if (!empty($auditLogs) && hasPermission('view_audit_logs')): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Recent Changes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach (array_slice($auditLogs, 0, 5) as $log): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?php echo ucfirst($log['action']); ?></h6>
                                    <p class="text-muted small mb-1">
                                        by <?php echo htmlspecialchars($log['username'] ?? 'System'); ?>
                                    </p>
                                    <p class="text-muted small">
                                        <?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($auditLogs) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="/audit-logs?nidhh=<?php echo urlencode($beneficiary['nidhh']); ?>" class="btn btn-sm btn-outline-primary">
                                View All Changes
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this beneficiary?</p>
                <p class="text-muted small">
                    <strong>NIDHH:</strong> <?php echo htmlspecialchars($beneficiary['nidhh']); ?>
                </p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    This action cannot be undone. All tranche information will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="/beneficiaries/delete" style="display: inline;">
                    <input type="hidden" name="nidhh" value="<?php echo htmlspecialchars($beneficiary['nidhh']); ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        Delete Beneficiary
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteBeneficiary(nidhh) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
