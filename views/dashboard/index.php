<?php
// Dashboard view with statistics and charts
$additionalCSS = ['/assets/css/dashboard.css'];
$additionalJS = [
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js',
    '/assets/js/dashboard.js'
];
?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?php echo number_format($stats['total_beneficiaries']); ?></div>
                    <div class="stat-label">Total Beneficiaries</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?php echo number_format($stats['tranche_status']['Completed']); ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number"><?php echo number_format($totalPending); ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-clock"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-number">₦<?php echo number_format($stats['total_disbursed'], 2); ?></div>
                    <div class="stat-label">Total Disbursed</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-currency-exchange"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Indicators -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Completion Rate
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Overall Progress</span>
                    <span class="fw-bold"><?php echo $completionRate; ?>%</span>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: <?php echo $completionRate; ?>%"
                         aria-valuenow="<?php echo $completionRate; ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-4">
                        <div class="text-muted small">Not Started</div>
                        <div class="fw-bold"><?php echo $stats['tranche_status']['NotStarted']; ?></div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Partial</div>
                        <div class="fw-bold text-warning"><?php echo $stats['tranche_status']['Partial']; ?></div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Completed</div>
                        <div class="fw-bold text-success"><?php echo $stats['tranche_status']['Completed']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gender-ambiguous me-2"></i>
                    Gender Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="genderChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    Disbursement Trend (Last 6 Months)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="disbursementChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Tranche Status
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- State Distribution -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-geo-alt me-2"></i>
                    Top 10 States by Beneficiaries
                </h5>
            </div>
            <div class="card-body">
                <canvas id="stateChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Recent Activity
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentBeneficiaries)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentBeneficiaries as $beneficiary): ?>
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($beneficiary['nidhh']); ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo htmlspecialchars($beneficiary['State']); ?>, 
                                            <?php echo htmlspecialchars($beneficiary['LGA']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y', strtotime($beneficiary['created_at'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge status-<?php echo strtolower($beneficiary['TrancheStatus']); ?>">
                                        <?php echo $beneficiary['TrancheStatus']; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/beneficiaries" class="btn btn-outline-primary btn-sm">
                            View All Beneficiaries
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                        <p>No beneficiaries found</p>
                        <a href="/import" class="btn btn-primary btn-sm">
                            <i class="bi bi-upload me-1"></i>
                            Import CSV
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="/beneficiaries/create" class="btn btn-outline-primary w-100">
                            <i class="bi bi-person-plus d-block mb-2" style="font-size: 2rem;"></i>
                            Add Beneficiary
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/import" class="btn btn-outline-success w-100">
                            <i class="bi bi-upload d-block mb-2" style="font-size: 2rem;"></i>
                            Import CSV
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/beneficiaries?export=csv" class="btn btn-outline-info w-100">
                            <i class="bi bi-download d-block mb-2" style="font-size: 2rem;"></i>
                            Export Data
                        </a>
                    </div>
                    <?php if (hasPermission('view_audit_logs')): ?>
                        <div class="col-md-3 mb-3">
                            <a href="/audit-logs" class="btn btn-outline-warning w-100">
                                <i class="bi bi-journal-text d-block mb-2" style="font-size: 2rem;"></i>
                                Audit Logs
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data for JavaScript -->
<script>
window.chartData = <?php echo json_encode($chartData); ?>;
</script>
