<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Login - UniLeave</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: 'Inter', sans-serif;
        }
        
        .login-container {
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-wrap: wrap;
        }
        
        /* Left Panel - Image Side */
        .login-image {
            flex: 1;
            min-width: 280px;
            background-image: url('<?php echo e(asset('images/university-bg.png')); ?>');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .login-image .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30,58,95,0.7) 0%, rgba(46,90,138,0.7) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            color: white;
        }
        
        .login-image h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .login-image p {
            opacity: 0.9;
            line-height: 1.5;
        }
        
        .login-image .stats {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .login-image .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .login-image .stat-label {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        /* Right Panel - Form Side */
        .login-form {
            flex: 1;
            min-width: 320px;
            padding: 2.5rem;
            background: white;
        }
        
        .login-form h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1E3A5F;
        }
        
        .login-form p {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        
        .input-group-text {
            background: transparent;
            border-right: none;
            color: #9ca3af;
        }
        
        .form-control {
            border-left: none;
            padding: 0.625rem 0.75rem;
        }
        
        .form-control:focus {
            border-color: #1E3A5F;
            box-shadow: none;
        }
        
        .input-group:focus-within {
            border-color: #1E3A5F;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
            border-radius: 0.5rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: #1E3A5F;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-login:hover {
            background: #2E5A8A;
            transform: translateY(-1px);
        }
        
        .form-check {
            margin: 1rem 0;
        }
        
        .form-check-label {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .register-link a {
            color: #1E3A5F;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .alert-danger {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            color: #DC2626;
        }
        
        @media (max-width: 768px) {
            .login-image {
                display: none;
            }
            .login-form {
                min-width: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Image Side -->
        <div class="login-image">
            <div class="overlay">
                <h2>UniLeave</h2>
                <p>University Leave Management System - simplifying leave requests, approvals, and tracking for academic and administrative staff.</p>
                <div class="stats">
                    <div>
                        <div class="stat-number">5000+</div>
                        <div class="stat-label">Staff Members</div>
                    </div>
                    <div>
                        <div class="stat-number">11</div>
                        <div class="stat-label">Leave Types</div>
                    </div>
                    <div>
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Access</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Form Side -->
        <div class="login-form">
            <h3>Welcome Back</h3>
            <p>Please login to your account</p>
            
            <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <i class="ti ti-exclamation-circle me-1"></i> <?php echo e(session('error')); ?>

            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>
                
                <!-- CHANGED: Field name from 'staff_id' to 'email' -->
                <div class="mb-3">
                    <label class="form-label">Email or Staff ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-user"></i></span>
                        <input type="text" 
                               name="email" 
                               class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Enter your Email or Staff ID"
                               value="<?php echo e(old('email')); ?>"
                               required>
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-lock"></i></span>
                        <input type="password" 
                               name="password" 
                               class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Enter your password"
                               required>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="ti ti-login me-1"></i> Sign In
                </button>
                
                <div class="register-link">
                    Don't have an account? <a href="<?php echo e(route('register')); ?>">Create an account</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\wamp64\www\unileave\resources\views/auth/login.blade.php ENDPATH**/ ?>