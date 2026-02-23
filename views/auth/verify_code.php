<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo APP_DESC; ?>">
    <meta name="author" content="">

    <title><?php echo $title; ?></title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .verify-container {
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .card-verify {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        .verify-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem 1rem 0 0;
        }
        .verification-code-input {
            font-size: 2rem;
            letter-spacing: 0.8rem;
            text-align: center;
            font-weight: bold;
        }
        .message-box {
            border-left: 4px solid #667eea;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .message-box .alert-heading {
            color: #8B4513;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .message-box-icon {
            font-size: 2rem;
            color: #8B4513;
        }
    </style>
</head>

<body class="verify-container">

    <div class="container">
        <div class="card card-verify o-hidden border-0 shadow-lg my-5">
            <div class="verify-header text-center">
                <i class="fas fa-envelope fa-3x mb-3"></i>
                <h2 class="font-weight-bold">Email Verification</h2>
                <p class="mb-0">Enter your verification code</p>
            </div>
            <div class="card-body p-5">
                <!-- Message Box - Instructions for Email Verification -->
                <div class="alert alert-primary mb-4" role="alert" style="border-left: 4px solid #8B4513; background: linear-gradient(135deg, #f5e6d3 0%, #e8d5c4 100%); border-radius: 8px; padding: 20px; position: relative;">
                    <div class="d-flex align-items-start">
                        <div class="mr-3" style="font-size: 2rem;">
                            <i class="fas fa-envelope" style="color: #8B4513;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h5 class="alert-heading mb-3" style="color: #8B4513; font-weight: 600;">
                                <i class="fas fa-shield-alt"></i> Verification Required Before Login
                            </h5>
                            
                            <div style="background: #fff3cd; border: 2px dashed #ffc107; border-radius: 10px; padding: 25px; text-align: center; margin: 15px 0;">
                                <div style="margin-bottom: 15px;">
                                    <i class="fas fa-envelope-open text-warning" style="font-size: 2.5rem; margin-bottom: 10px;"></i>
                                    <p class="mb-2" style="color: #856404; font-weight: 600; font-size: 1.1rem; margin-bottom: 15px;">
                                        Verification Code Sent to Your Email
                                    </p>
                                </div>
                                <p class="mb-2" style="color: #856404; font-size: 0.95rem; line-height: 1.6;">
                                    After you clicked the register button, a 4-digit verification code was <strong>automatically sent</strong> to your email address (<strong><?php echo htmlspecialchars($email); ?></strong>).
                                </p>
                                <?php if (isset($code_sent_at) && $code_sent_at): ?>
                                    <p class="mt-2 mb-0" style="color: #999; font-size: 0.85rem;">
                                        <i class="fas fa-clock"></i> Code sent on: <?php echo date('F d, Y h:i A', strtotime($code_sent_at)); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <div style="background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; border-radius: 5px; margin: 15px 0;">
                                <p class="mb-2" style="color: #0c5460; font-weight: 600;">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>You Must Verify Before Login</strong>
                                </p>
                                <p class="mb-0" style="color: #0c5460; font-size: 0.9rem; line-height: 1.6;">
                                    You <strong>cannot log in</strong> until you enter the verification code from your email. The verification code is <strong>only available in your email inbox</strong>. Please check your email (including spam/junk folder) for the verification code.
                                </p>
                            </div>
                            
                            <p class="mb-0" style="color: #666; font-size: 0.9rem;">
                                <i class="fas fa-key"></i> Enter the 4-digit code from your email in the field below to verify your account and enable login.
                            </p>
                        </div>
                    </div>
                </div>

                <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <?php if (isset($success) && !empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <form class="user" method="POST" action="<?php echo BASE_URL; ?>auth/processVerifyCode">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($role ?? 'admin'); ?>">
                    
                    <div class="form-group">
                        <label class="form-label text-gray-700">
                            <i class="fas fa-key text-warning"></i> Verification Code *
                        </label>
                        <input type="text" 
                               class="form-control form-control-user verification-code-input" 
                               id="verification_code" 
                               name="verification_code" 
                               placeholder="0000" 
                               maxlength="4" 
                               pattern="[0-9]{4}" 
                               required 
                               autofocus
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Enter the 4-digit verification code that was sent to your email address.
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-danger btn-user btn-block">
                        <i class="fas fa-check-circle"></i> Verify Code
                    </button>
                </form>
                
                <hr>
                <div class="text-center">
                    <p class="small text-muted mb-3">
                        <i class="fas fa-question-circle"></i> Didn't receive the email? 
                        Please check your <strong>spam/junk folder</strong> first.
                    </p>
                    
                    <!-- Resend Verification Code Button -->
                    <form method="GET" action="<?php echo BASE_URL; ?>auth/resendVerificationCode" style="display: inline-block;">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <input type="hidden" name="role" value="<?php echo htmlspecialchars($role ?? 'admin'); ?>">
                        <button type="submit" class="btn btn-outline-primary btn-sm" style="margin: 5px;">
                            <i class="fas fa-redo"></i> Resend Verification Code
                        </button>
                    </form>
                    
                    <p class="small text-muted mt-3 mb-2" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle"></i> You can request a new verification code if you didn't receive the email. Please wait 5 minutes between requests.
                    </p>
                </div>
                <div class="text-center mt-2">
                    <p class="small text-muted mb-2">
                        <i class="fas fa-info-circle"></i> After verifying your code, you will be able to log in to the system.
                    </p>
                    <a class="small" href="<?php echo BASE_URL; ?>auth/login?tab=admin">
                        Back to Login (Login will be enabled after verification)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

</body>

</html>

