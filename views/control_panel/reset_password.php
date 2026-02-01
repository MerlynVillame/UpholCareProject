<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Control Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reset-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        
        .reset-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .reset-header {
            background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .reset-header i {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .reset-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 28px;
        }
        
        .reset-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .reset-body {
            padding: 40px 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #1F4E79;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }
        
        .input-group-text {
            background: white;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(139, 69, 19, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .password-wrapper {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #858796;
            cursor: pointer;
            padding: 5px 10px;
            z-index: 10;
        }
        .password-toggle:hover {
            color: #5a5c69;
        }
        .password-toggle:focus {
            outline: none;
        }
        .input-group.password-wrapper {
            position: relative;
        }
        .input-group.password-wrapper .form-control {
            padding-right: 45px;
        }
        .input-group.password-wrapper .password-toggle {
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <i class="fas fa-lock-open"></i>
                <h2>Set New Password</h2>
                <p>Control Panel</p>
            </div>
            
            <div class="reset-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= BASE_URL ?>control-panel/processResetPassword" method="POST">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                    
                    <div class="mb-3">
                        <label for="reset_code" class="form-label">
                            <i class="fas fa-key"></i> Reset Code
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <input type="text" class="form-control" id="reset_code" name="reset_code" 
                                   placeholder="Enter the reset code" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-lock"></i> New Password
                        </label>
                        <div class="input-group password-wrapper">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   placeholder="Enter new password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('new_password', 'togglePasswordIcon1')">
                                <i class="fas fa-eye" id="togglePasswordIcon1"></i>
                            </button>
                        </div>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock"></i> Confirm New Password
                        </label>
                        <div class="input-group password-wrapper">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Confirm new password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'togglePasswordIcon2')">
                                <i class="fas fa-eye" id="togglePasswordIcon2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-reset">
                        <i class="fas fa-check"></i> Reset Password
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>control-panel/login" style="color: #666; text-decoration: none; font-size: 14px;">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

