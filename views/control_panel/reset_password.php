<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= APP_DESC; ?>">
    <meta name="author" content="">

    <title>Set New Password - Control Panel - UphoCare</title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .login-container {
            background-image: url('<?= BASE_URL; ?>assets/images/1.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-color: #1F4E79;
            min-height: 100vh;
            position: relative;
        }

        .login-container::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.5) 0%, rgba(22, 33, 62, 0.45) 50%, rgba(15, 52, 96, 0.5) 100%);
            z-index: 0;
        }

        .login-container::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: 0;
        }

        .login-container .container {
            position: relative;
            z-index: 1;
        }
        .card-login {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
        }
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-section h1 {
            color: #5a5c69;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .logo-section p {
            color: #858796;
            font-size: 0.9rem;
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

        @media (max-width: 768px) {
            .login-container {
                background-attachment: scroll;
            }
        }
    </style>
</head>

<body class="login-container">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card card-login o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block" style="background: linear-gradient(135deg, #2C3E50 0%, #0F3C5F 100%); display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-white p-5">
                                    <i class="fas fa-lock-open fa-5x mb-4"></i>
                                    <h2 class="font-weight-bold">New Password</h2>
                                    <p class="lead">Super Admin Control Panel</p>
                                    <p>Please enter the reset code sent to your email and set your new account password.</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="logo-section text-center mb-4">
                                        <h1 class="h3 font-weight-bold" style="color: #2C3E50;">UphoCare</h1>
                                        <p class="text-muted">Secure Your Account</p>
                                    </div>

                                    <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php endif; ?>

                                    <form class="user" method="POST" action="<?= BASE_URL ?>control-panel/processResetPassword">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                                        
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user"
                                                id="reset_code" name="reset_code" placeholder="Enter Reset Code" required autofocus>
                                        </div>

                                        <div class="form-group">
                                            <div class="password-wrapper">
                                                <input type="password" class="form-control form-control-user"
                                                    id="new_password" name="new_password" placeholder="New Password" required>
                                                <button type="button" class="password-toggle" onclick="togglePassword('new_password', 'togglePasswordIcon1')">
                                                    <i class="fas fa-eye" id="togglePasswordIcon1"></i>
                                                </button>
                                            </div>
                                            <small class="form-text text-muted pl-3">Minimum 6 characters</small>
                                        </div>

                                        <div class="form-group">
                                            <div class="password-wrapper">
                                                <input type="password" class="form-control form-control-user"
                                                    id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
                                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'togglePasswordIcon2')">
                                                    <i class="fas fa-eye" id="togglePasswordIcon2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block" style="background: linear-gradient(135deg, #3498DB 0%, #2C3E50 100%); border: none;">
                                            <i class="fas fa-check"></i> Reset Password
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="<?= BASE_URL ?>control-panel/login">Back to Login</a>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a class="text-muted small" href="<?= BASE_URL ?>">
                                            <i class="fas fa-arrow-left"></i> Back to Main Site
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

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


