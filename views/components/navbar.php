<?php $currentUser = getCurrentUser(); ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <i class="bi bi-house-heart-fill me-2"></i>
            <?php echo APP_NAME; ?>
        </a>
        
        <?php if (isLoggedIn()): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/beneficiaries">
                            <i class="bi bi-people me-1"></i>
                            Beneficiaries
                        </a>
                    </li>
                    <?php if (hasPermission('import_csv')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/import">
                                <i class="bi bi-upload me-1"></i>
                                Import CSV
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (hasPermission('manage_users')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear me-1"></i>
                                Admin
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/users">
                                    <i class="bi bi-person-gear me-1"></i>
                                    Manage Users
                                </a></li>
                                <li><a class="dropdown-item" href="/audit-logs">
                                    <i class="bi bi-journal-text me-1"></i>
                                    Audit Logs
                                </a></li>
                                <li><a class="dropdown-item" href="/settings">
                                    <i class="bi bi-sliders me-1"></i>
                                    System Settings
                                </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($currentUser['full_name']); ?>
                            <span class="badge bg-secondary ms-1"><?php echo ucfirst($currentUser['role']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile">
                                <i class="bi bi-person me-1"></i>
                                Profile
                            </a></li>
                            <li><a class="dropdown-item" href="/change-password">
                                <i class="bi bi-key me-1"></i>
                                Change Password
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">
                                <i class="bi bi-box-arrow-right me-1"></i>
                                Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>
