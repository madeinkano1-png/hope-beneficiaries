<?php
$currentPath = $_SERVER['REQUEST_URI'];
$currentPath = explode('?', $currentPath)[0]; // Remove query parameters
$currentPath = trim($currentPath, '/');

function isActive($path, $currentPath) {
    if ($path === '' && ($currentPath === '' || $currentPath === 'dashboard')) {
        return 'active';
    }
    return strpos($currentPath, $path) === 0 ? 'active' : '';
}
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('', $currentPath); ?>" href="/dashboard">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('beneficiaries', $currentPath); ?>" href="/beneficiaries">
                    <i class="bi bi-people"></i>
                    Beneficiaries
                </a>
            </li>
            
            <?php if (hasPermission('import_csv')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('import', $currentPath); ?>" href="/import">
                        <i class="bi bi-upload"></i>
                        Import CSV
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <?php if (hasPermission('manage_users') || hasPermission('view_audit_logs')): ?>
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Administration</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <?php if (hasPermission('manage_users')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('users', $currentPath); ?>" href="/users">
                            <i class="bi bi-person-gear"></i>
                            Manage Users
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if (hasPermission('view_audit_logs')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('audit-logs', $currentPath); ?>" href="/audit-logs">
                            <i class="bi bi-journal-text"></i>
                            Audit Logs
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if (hasPermission('system_settings')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('settings', $currentPath); ?>" href="/settings">
                            <i class="bi bi-sliders"></i>
                            System Settings
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Quick Actions</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <?php if (hasPermission('create_beneficiaries')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/beneficiaries/create">
                        <i class="bi bi-person-plus"></i>
                        Add Beneficiary
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if (hasPermission('export_csv')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/beneficiaries?export=csv">
                        <i class="bi bi-download"></i>
                        Export Data
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link" href="/import/template">
                    <i class="bi bi-file-earmark-text"></i>
                    CSV Template
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Reports</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="/reports/summary">
                    <i class="bi bi-bar-chart"></i>
                    Summary Report
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/reports/disbursement">
                    <i class="bi bi-currency-exchange"></i>
                    Disbursement Report
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/reports/demographics">
                    <i class="bi bi-pie-chart"></i>
                    Demographics
                </a>
            </li>
        </ul>
        
        <!-- System Info -->
        <div class="px-3 mt-4">
            <div class="card bg-light border-0">
                <div class="card-body p-3">
                    <h6 class="card-title text-muted small mb-2">System Info</h6>
                    <div class="small text-muted">
                        <div class="d-flex justify-content-between">
                            <span>Version:</span>
                            <span><?php echo APP_VERSION; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Environment:</span>
                            <span class="badge bg-<?php echo APP_ENV === 'production' ? 'success' : 'warning'; ?> badge-sm">
                                <?php echo ucfirst(APP_ENV); ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>User:</span>
                            <span><?php echo getCurrentUser()['role']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
