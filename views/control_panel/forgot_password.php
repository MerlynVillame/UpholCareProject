<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Control Panel</title>
    
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
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .info-box {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-box i {
            color: #4caf50;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <i class="fas fa-key"></i>
                <h2>Reset Password</h2>
                <p>Super Admin Control Panel</p>
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
                
                <div class="info-box">
                    <p class="mb-0" style="font-size: 14px;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Super Admin Password Reset:</strong><br>
                        Enter your email address and we'll send you a password reset code. 
                        The code will be displayed on your screen after submission.
                    </p>
                </div>
                
                <form action="<?= BASE_URL ?>control-panel/processForgotPassword" method="POST">
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-at"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Enter your registered email" required autofocus>
                        </div>
                        <small class="text-muted">Enter the email you used to create your super admin account</small>
                    </div>
                    
                    <button type="submit" class="btn btn-reset">
                        <i class="fas fa-paper-plane"></i> Send Reset Code
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>control-panel/login" style="color: #666; text-decoration: none; font-size: 14px;">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="back-link">
            <a href="<?= BASE_URL ?>">
                <i class="fas fa-home"></i> Back to Main Site
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

