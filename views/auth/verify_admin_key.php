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
    </style>
</head>

<body class="verify-container">

    <div class="container">
        <div class="card card-verify o-hidden border-0 shadow-lg my-5">
            <div class="verify-header text-center">
                <i class="fas fa-key fa-3x mb-3"></i>
                <h2 class="font-weight-bold">Admin Key Verification</h2>
                <p class="mb-0">Step 2 of 2: Verify your admin key</p>
            </div>
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <p class="text-muted">
                        <i class="fas fa-envelope text-primary"></i> Registration email: <strong><?php echo htmlspecialchars($email); ?></strong>
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-shield-alt"></i> <strong>Security Required:</strong> Please enter your admin verification key to complete your registration.
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

                <form class="user" method="POST" action="<?php echo BASE_URL; ?>auth/processVerifyAdminKey">
                    <div class="form-group">
                        <label class="form-label text-gray-700">
                            <i class="fas fa-key text-warning"></i> Admin Verification Key *
                        </label>
                        <input type="text" class="form-control form-control-user" id="admin_key" 
                            name="admin_key" placeholder="Enter your admin verification key" required autofocus>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Contact the system administrator to obtain your admin verification key.
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-danger btn-user btn-block">
                        <i class="fas fa-check-circle"></i> Verify Key & Complete Registration
                    </button>
                </form>
                
                <hr>
                <div class="text-center">
                    <a class="small" href="<?php echo BASE_URL; ?>auth/registerAdmin">
                        <i class="fas fa-arrow-left"></i> Back to Registration
                    </a>
                </div>
                <div class="text-center mt-2">
                    <a class="small" href="<?php echo BASE_URL; ?>auth/login?tab=admin">
                        Already verified? Login here
                    </a>
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

</body>

</html>

