<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator - UphoCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .generator-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header i {
            font-size: 60px;
            color: #8B4513;
            margin-bottom: 15px;
        }
        .hash-output {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .btn-generate {
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(139, 69, 19, 0.4);
        }
        .alert-warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="generator-card">
        <div class="header">
            <i class="fas fa-key"></i>
            <h2>Password Hash Generator</h2>
            <p class="text-muted">Generate secure password hash for Super Admin account</p>
        </div>

        <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Security Note:</strong> Delete this file after generating your password hash!
        </div>

        <form method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Enter Your Password
                </label>
                <input type="text" class="form-control form-control-lg" id="password" name="password" 
                       placeholder="Enter password to hash" required>
                <small class="text-muted">Minimum 6 characters recommended</small>
            </div>

            <button type="submit" class="btn btn-generate w-100">
                <i class="fas fa-magic"></i> Generate Hash
            </button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])): ?>
            <hr class="my-4">
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-shield-alt"></i> Generated Password Hash
                </label>
                <div class="hash-output">
                    <?php echo password_hash($_POST['password'], PASSWORD_DEFAULT); ?>
                </div>
                <small class="text-muted">Copy this hash and use it in your SQL script</small>
            </div>

            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Success!</strong> Hash generated for password: <strong><?php echo htmlspecialchars($_POST['password']); ?></strong>
            </div>

            <div class="alert alert-info">
                <strong>Next Steps:</strong>
                <ol class="mb-0 mt-2">
                    <li>Copy the hash above</li>
                    <li>Open <code>database/create_super_admin.sql</code></li>
                    <li>Replace the password hash on line 23</li>
                    <li>Change the email on line 22</li>
                    <li>Run the SQL script in phpMyAdmin</li>
                    <li><strong>Delete this file!</strong></li>
                </ol>
            </div>
        <?php endif; ?>

        <hr class="my-4">

        <div class="text-center">
            <a href="control-panel/login" class="btn btn-outline-secondary">
                <i class="fas fa-sign-in-alt"></i> Go to Control Panel Login
            </a>
        </div>

        <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 10px;">
            <strong><i class="fas fa-info-circle"></i> Instructions:</strong>
            <ol class="mb-0 mt-2" style="font-size: 14px;">
                <li>Enter your desired super admin password above</li>
                <li>Click "Generate Hash"</li>
                <li>Copy the generated hash</li>
                <li>Use it in the SQL script to create your super admin account</li>
                <li>See <code>CREATE_SUPER_ADMIN_GUIDE.md</code> for complete instructions</li>
            </ol>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

