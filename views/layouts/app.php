<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/app.css" rel="stylesheet">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php include 'views/components/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php if (isLoggedIn()): ?>
                <?php include 'views/components/sidebar.php'; ?>
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <?php else: ?>
                <main class="col-12">
            <?php endif; ?>
            
            <?php if (isset($pageHeader)): ?>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $pageHeader; ?></h1>
                    <?php if (isset($pageActions)): ?>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <?php echo $pageActions; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash_warning'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['flash_warning']; unset($_SESSION['flash_warning']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash_info'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['flash_info']; unset($_SESSION['flash_info']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Main Content -->
            <?php echo $content ?? ''; ?>
            
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
