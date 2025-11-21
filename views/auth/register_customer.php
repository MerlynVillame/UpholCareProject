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
        .register-container {
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .card-register {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
        }
        .customer-header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
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

<body class="register-container">

    <div class="container">

        <div class="card card-register o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block customer-header" style="display: flex; align-items: center; justify-content: center;">
                        <div class="text-center text-white p-5">
                            <i class="fas fa-user fa-5x mb-4"></i>
                            <h2 class="font-weight-bold">Customer Registration</h2>
                            <p class="lead">Create your account and start booking services</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create Customer Account!</h1>
                                <p class="text-muted mb-4">Register as a customer to book repair services.</p>
                            </div>

                            <?php if (isset($error) && !empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php endif; ?>

                            <form class="user" method="POST" action="<?php echo BASE_URL; ?>auth/processRegisterCustomer">
                                <input type="hidden" name="role" value="customer">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="full_name" 
                                        name="full_name" placeholder="Full Name" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="email" 
                                        name="email" placeholder="Email Address *" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="phone" 
                                        name="phone" placeholder="Phone Number">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <div class="password-wrapper">
                                            <input type="password" class="form-control form-control-user"
                                                id="password" name="password" placeholder="Password" required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="password-wrapper">
                                            <input type="password" class="form-control form-control-user"
                                                id="confirm_password" name="confirm_password" placeholder="Repeat Password" required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                                <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-user btn-block">
                                    <i class="fas fa-user-plus"></i> Register Customer Account
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?php echo BASE_URL; ?>auth/login?tab=customer">Already have an account? Login!</a>
                            </div>
                            <div class="text-center mt-2">
                                <a class="small" href="<?php echo BASE_URL; ?>auth/registerAdmin">Register as Admin instead</a>
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
            const iconId = fieldId === 'password' ? 'togglePasswordIcon' : 'toggleConfirmPasswordIcon';
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

