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
    </style>
</head>

<body class="register-container">

    <div class="container">

        <div class="card card-register o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); display: flex; align-items: center; justify-content: center;">
                        <div class="text-center text-white p-5">
                            <i class="fas fa-user-plus fa-5x mb-4"></i>
                            <h2 class="font-weight-bold">Join Us!</h2>
                            <p class="lead">Create your account and start managing your repairs</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                                <p class="text-muted mb-4">Register as a customer to book services or as an admin to manage the system.</p>
                            </div>

                            <?php if (isset($error) && !empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php endif; ?>

                            <form class="user" method="POST" action="<?php echo BASE_URL; ?>auth/processRegister">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="full_name" 
                                        name="full_name" placeholder="Full Name" required>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="username" 
                                            name="username" placeholder="Username" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="email" class="form-control form-control-user" id="email" 
                                            name="email" placeholder="Email Address" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="phone" 
                                        name="phone" placeholder="Phone Number">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-gray-700">Account Type</label>
                                    <select class="form-control form-control-user" id="role" name="role" required onchange="toggleAdminKeyField()">
                                        <option value="">Select Account Type</option>
                                        <option value="customer">Customer - Book repair services</option>
                                        <option value="admin">Admin - Manage bookings and system</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Default: Customer account. Admin registration requires a special key.
                                    </small>
                                </div>
                                
                                <!-- Admin Key Field (Hidden by default) -->
                                <div class="form-group" id="adminKeyField" style="display: none;">
                                    <label class="form-label text-gray-700">
                                        <i class="fas fa-key text-warning"></i> Admin Verification Key
                                    </label>
                                    <input type="text" class="form-control form-control-user" id="admin_key" 
                                        name="admin_key" placeholder="Enter admin verification key">
                                    <small class="form-text text-danger">
                                        <i class="fas fa-shield-alt"></i> 
                                        Admin accounts require a special verification key. Contact system administrator to obtain this key.
                                    </small>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            id="password" name="password" placeholder="Password" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            id="confirm_password" name="confirm_password" placeholder="Repeat Password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?php echo BASE_URL; ?>auth/login">Already have an account? Login!</a>
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
        // Toggle admin key field
        function toggleAdminKeyField() {
            const role = document.getElementById('role').value;
            const adminKeyField = document.getElementById('adminKeyField');
            const adminKeyInput = document.getElementById('admin_key');
            
            if (role === 'admin') {
                adminKeyField.style.display = 'block';
                adminKeyInput.required = true;
            } else {
                adminKeyField.style.display = 'none';
                adminKeyInput.required = false;
                adminKeyInput.value = '';
            }
        }
    </script>

</body>

</html>

