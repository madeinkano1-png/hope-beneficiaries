<?php
$pageTitle = "Login - " . APP_NAME;
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm mt-5">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-house-heart-fill text-primary" style="font-size: 3rem;"></i>
                    <h2 class="mt-2"><?php echo APP_NAME; ?></h2>
                    <p class="text-muted">Please sign in to your account</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/login">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person me-1"></i>
                            Username or Email
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               required 
                               autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-1"></i>
                            Password
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Sign In
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="/reset-password" class="text-decoration-none">
                        <i class="bi bi-question-circle me-1"></i>
                        Forgot your password?
                    </a>
                </div>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Default admin credentials:<br>
                        Username: <strong>admin</strong><br>
                        Password: <strong>admin123</strong>
                    </small>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                © <?php echo date('Y'); ?> HoPE Beneficiaries Management System
            </small>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/app.php';
?>
