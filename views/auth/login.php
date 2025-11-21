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
    <link href="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .login-container {
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            min-height: 100vh;
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
                            <div class="col-lg-6 d-none d-lg-block" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-white p-5">
                                    <i class="fas fa-couch fa-5x mb-4"></i>
                                    <h2 class="font-weight-bold"><?php echo APP_NAME; ?></h2>
                                    <p class="lead"><?php echo APP_DESC; ?></p>
                                    <p>Vehicle • Bedding • Furniture Covers</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="logo-section">
                                        <h1><?php echo APP_NAME; ?></h1>
                                        <p>Welcome back! Please login to your account.</p>
                                    </div>

                                    <?php if (isset($error) && !empty($error)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['registration_email']) && !empty($_SESSION['registration_email'])): ?>
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <i class="fas fa-user-shield"></i> <strong>Registration Submitted:</strong> 
                                        <p class="mb-2">Your registration has been submitted. Your email: <strong><?php echo htmlspecialchars($_SESSION['registration_email']); ?></strong></p>
                                        <p class="mb-2">The <strong>Super Admin</strong> will send you a verification code. Visit the verification page to see your code once it's been sent.</p>
                                        <a href="<?php echo BASE_URL; ?>auth/verifyCode?email=<?php echo urlencode($_SESSION['registration_email']); ?>" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-key"></i> Go to Verification Page
                                        </a>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php 
                                    $temp_email = $_SESSION['registration_email'];
                                    unset($_SESSION['registration_email']); 
                                    unset($_SESSION['verification_code_sent']);
                                    unset($_SESSION['verification_code']);
                                    ?>
                                    <?php endif; ?>

                                    <?php if (isset($success) && !empty($success)): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php endif; ?>

                                    <form class="user" method="POST" action="<?php echo BASE_URL; ?>auth/processLogin">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="email" name="email" placeholder="Enter Email Address..." required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <div class="password-wrapper">
                                                <input type="password" class="form-control form-control-user"
                                                    id="password" name="password" placeholder="Password" required>
                                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-sign-in-alt"></i> Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="<?php echo BASE_URL; ?>auth/forgotPassword">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="<?php echo BASE_URL; ?>auth/roleSelection">Create an Account!</a>
                                    </div>
                                    
                                    <!-- Demo Credentials -->
                                    <div class="mt-4 p-3" style="background-color: #f8f9fc; border-radius: 0.5rem;">
                                        <small class="text-muted d-block mb-2"><strong>Login with your email address</strong></small>
                                        <small class="text-muted d-block">Use the email address you registered with</small>
                                        <hr class="my-2">
                                        <small class="text-muted d-block"><strong>Or register as:</strong></small>
                                        <small class="text-muted d-block">• Customer - to book repair services</small>
                                        <small class="text-muted d-block">• Admin - to manage bookings and system</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js"></script>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById('togglePasswordIcon');
            
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

