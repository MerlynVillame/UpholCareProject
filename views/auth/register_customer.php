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
        .register-container {
            background-image: url('<?php echo BASE_URL; ?>assets/images/1.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-color: #1F4E79;
            min-height: 100vh;
            padding: 2rem 0;
            position: relative;
        }

        .register-container::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.5) 0%, rgba(22, 33, 62, 0.45) 50%, rgba(15, 52, 96, 0.5) 100%);
            z-index: 0;
        }

        .register-container::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: 0;
        }

        .register-container .container {
            position: relative;
            z-index: 1;
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

        @media (max-width: 768px) {
            .register-container {
                background-attachment: scroll;
            }
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

                            <form class="user" method="POST" action="<?php echo BASE_URL; ?>auth/processRegisterCustomer" enctype="multipart/form-data">
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
                                    <input type="tel" class="form-control form-control-user" id="phone" 
                                        name="phone" placeholder="Phone Number (11 digits)"
                                        pattern="[0-9]{11}"
                                        minlength="11"
                                        maxlength="11"
                                        title="Phone number must be exactly 11 digits">
                                    <small class="form-text text-muted">Enter exactly 11 digits (e.g., 09123456789)</small>
                                </div>

                                <!-- Customer ID Upload -->
                                <div class="form-group">
                                    <label for="customer_id_image" class="form-label text-gray-700 font-weight-bold">
                                        <i class="fas fa-id-card text-success"></i> Valid Customer ID (Image) *
                                    </label>
                                    <input type="file" class="form-control" 
                                        id="customer_id_image" name="customer_id_image" 
                                        accept="image/jpeg,image/png,image/webp,image/gif" required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Upload a clear photo of your valid government-issued ID (UMID, PhilSys, Driver's License, Passport, etc.). Accepted formats: JPG, PNG, WEBP. Max size: 5MB.
                                    </small>
                                    <div id="customerIdPreview" class="mt-2" style="display: none;">
                                        <img id="customerIdPreviewImg" src="#" alt="ID Preview" style="max-height: 120px; border-radius: 8px; border: 2px solid #27ae60;">
                                        <div class="small text-success mt-1"><i class="fas fa-check-circle"></i> <span id="customerIdFileName"></span></div>
                                    </div>
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

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

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
        
        // Phone number validation
        document.getElementById('phone').addEventListener('input', function(e) {
            const phoneValue = e.target.value;
            const phoneField = e.target;
            
            // Remove any non-digit characters
            const cleanedValue = phoneValue.replace(/\D/g, '');
            
            // Update the field with cleaned value
            if (cleanedValue !== phoneValue) {
                phoneField.value = cleanedValue;
            }
            
            // Check if it's exactly 11 digits
            if (cleanedValue.length > 0 && cleanedValue.length !== 11) {
                phoneField.setCustomValidity('Phone number must be exactly 11 digits');
                phoneField.classList.add('is-invalid');
            } else {
                phoneField.setCustomValidity('');
                phoneField.classList.remove('is-invalid');
            }
        });
        
        // Customer ID image preview and validation
        document.getElementById('customer_id_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('customerIdPreview');
            const previewImg = document.getElementById('customerIdPreviewImg');
            const fileNameSpan = document.getElementById('customerIdFileName');
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only image files are allowed (JPG, PNG, WEBP, GIF). Please select a valid image.');
                    e.target.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB. Please upload a smaller image.');
                    e.target.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                // Show image preview
                const reader = new FileReader();
                reader.onload = function(ev) {
                    previewImg.src = ev.target.result;
                    fileNameSpan.textContent = file.name;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
        
        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const phoneField = document.getElementById('phone');
            const phoneValue = phoneField.value.trim();
            
            if (phoneValue && phoneValue.length !== 11) {
                e.preventDefault();
                phoneField.setCustomValidity('Phone number must be exactly 11 digits');
                phoneField.reportValidity();
                return false;
            }
        });
    </script>

</body>

</html>

