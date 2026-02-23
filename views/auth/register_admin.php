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
        .admin-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
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
                    <div class="col-lg-5 d-none d-lg-block admin-header" style="display: flex; align-items: center; justify-content: center;">
                        <div class="text-center text-white p-5">
                            <i class="fas fa-user-shield fa-5x mb-4"></i>
                            <h2 class="font-weight-bold">Admin Registration</h2>
                            <p class="lead">Create an admin account to manage the system</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create Admin Account!</h1>
                                <p class="text-muted mb-4">Register as an admin to manage bookings and system settings.</p>
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle"></i> <strong>Registration Process:</strong> After submitting your registration, your account will be pending. The <strong>Super Admin</strong> will send you a verification code. Once you receive the code from the Super Admin, visit the verification page to enter it and complete your registration. After verification, you'll wait for final approval from the Super Admin.
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

                            <form class="user" method="POST" action="<?php echo BASE_URL; ?>auth/processRegisterAdmin" enctype="multipart/form-data">
                                <input type="hidden" name="role" value="admin">
                                
                                <h5 class="text-gray-700 mb-3"><i class="fas fa-user"></i> Personal Information</h5>
                                <hr class="mb-4">
                                
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user <?php echo (!empty($field_errors) && empty($form_data['full_name']) ? 'is-invalid' : ''); ?>" 
                                        id="full_name" name="full_name" 
                                        placeholder="Full Name *" 
                                        value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>" 
                                        required>
                                    <?php if (!empty($field_errors) && empty($form_data['full_name'])): ?>
                                        <div class="invalid-feedback">Full name is required</div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user <?php echo (!empty($field_errors) && (empty($form_data['email']) || !filter_var($form_data['email'] ?? '', FILTER_VALIDATE_EMAIL) || (isset($field_errors) && in_array('Email already exists', $field_errors))) ? 'is-invalid' : ''); ?>" 
                                        id="email" name="email" 
                                        placeholder="Email Address *" 
                                        value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" 
                                        required>
                                    <?php if (!empty($field_errors)): ?>
                                        <?php if (empty($form_data['email'])): ?>
                                            <div class="invalid-feedback">Email is required</div>
                                        <?php elseif (!filter_var($form_data['email'] ?? '', FILTER_VALIDATE_EMAIL)): ?>
                                            <div class="invalid-feedback">Invalid email format</div>
                                        <?php elseif (in_array('Email already exists', $field_errors)): ?>
                                            <div class="invalid-feedback">Email already exists</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <input type="tel" class="form-control form-control-user" 
                                        id="phone" name="phone" 
                                        placeholder="Phone Number (11 digits)" 
                                        pattern="[0-9]{11}"
                                        minlength="11"
                                        maxlength="11"
                                        title="Phone number must be exactly 11 digits"
                                        value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>">
                                    <small class="form-text text-muted">Enter exactly 11 digits (e.g., 09123456789)</small>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user <?php echo (!empty($field_errors) && empty($form_data['employee_id']) ? 'is-invalid' : ''); ?>" 
                                        id="employee_id" name="employee_id" 
                                        placeholder="Employee ID / Admin ID *" 
                                        value="<?php echo htmlspecialchars($form_data['employee_id'] ?? ''); ?>"
                                        required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-id-badge"></i> Enter your Employee ID or Admin ID for identity verification.
                                    </small>
                                    <?php if (!empty($field_errors) && empty($form_data['employee_id'])): ?>
                                        <div class="invalid-feedback d-block">Employee ID / Admin ID is required</div>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="text-gray-700 mb-3 mt-4"><i class="fas fa-store"></i> Business Information</h5>
                                <hr class="mb-4">
                                
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user <?php echo (!empty($field_errors) && empty($form_data['business_name']) ? 'is-invalid' : ''); ?>" 
                                        id="business_name" name="business_name" 
                                        placeholder="Business/Store Name *" 
                                        value="<?php echo htmlspecialchars($form_data['business_name'] ?? ''); ?>" 
                                        required>
                                    <small class="form-text text-muted">The name of your upholstery business or shop</small>
                                    <?php if (!empty($field_errors) && empty($form_data['business_name'])): ?>
                                        <div class="invalid-feedback">Business name is required</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <textarea class="form-control form-control-user <?php echo (!empty($field_errors) && empty($form_data['business_address']) ? 'is-invalid' : ''); ?>" 
                                        id="business_address" name="business_address" 
                                        rows="3" placeholder="Complete Business Address *" 
                                        required><?php echo htmlspecialchars($form_data['business_address'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Enter your full business address (Street, Barangay, City, Province)</small>
                                    <?php if (!empty($field_errors) && empty($form_data['business_address'])): ?>
                                        <div class="invalid-feedback">Business address is required</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user <?php echo (!empty($field_errors) && empty($form_data['business_city']) ? 'is-invalid' : ''); ?>" 
                                            id="business_city" name="business_city" 
                                            placeholder="City *" 
                                            value="<?php echo htmlspecialchars($form_data['business_city'] ?? 'Bohol'); ?>" 
                                            required>
                                        <?php if (!empty($field_errors) && empty($form_data['business_city'])): ?>
                                            <div class="invalid-feedback">Business city is required</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user <?php echo (!empty($field_errors) && empty($form_data['business_province']) ? 'is-invalid' : ''); ?>" 
                                            id="business_province" name="business_province" 
                                            placeholder="Province *" 
                                            value="<?php echo htmlspecialchars($form_data['business_province'] ?? 'Bohol'); ?>" 
                                            required>
                                        <?php if (!empty($field_errors) && empty($form_data['business_province'])): ?>
                                            <div class="invalid-feedback">Business province is required</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="business_permit" class="form-label text-gray-700">
                                        <i class="fas fa-file-pdf text-danger"></i> Business Permit (PDF Only) *
                                    </label>
                                    <input type="file" class="form-control form-control-user <?php echo (!empty($field_errors) && (in_array('Business permit (PDF) is required', $field_errors) || in_array('Business permit must be a PDF file', $field_errors) || in_array('Business permit file size must not exceed 5MB', $field_errors) || in_array('Invalid file type. Only PDF files are allowed.', $field_errors)) ? 'is-invalid' : ''); ?>" 
                                        id="business_permit" name="business_permit" accept=".pdf" required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Please upload your business permit in PDF format. Maximum file size: 5MB.
                                    </small>
                                    <?php if (!empty($field_errors)): ?>
                                        <?php if (in_array('Business permit (PDF) is required', $field_errors)): ?>
                                            <div class="invalid-feedback d-block">Business permit is required</div>
                                        <?php elseif (in_array('Business permit must be a PDF file', $field_errors) || in_array('Invalid file type. Only PDF files are allowed.', $field_errors)): ?>
                                            <div class="invalid-feedback d-block">Only PDF files are allowed</div>
                                        <?php elseif (in_array('Business permit file size must not exceed 5MB', $field_errors)): ?>
                                            <div class="invalid-feedback d-block">File size must not exceed 5MB</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($form_data['business_permit_filename'])): ?>
                                        <div class="mt-2 text-info">
                                            <i class="fas fa-file-pdf text-danger"></i> 
                                            <span>Previously selected: <?php echo htmlspecialchars($form_data['business_permit_filename']); ?></span>
                                            <small class="text-muted">(Please select the file again)</small>
                                        </div>
                                    <?php endif; ?>
                                    <div id="filePreview" class="mt-2" style="display: none;">
                                        <i class="fas fa-file-pdf text-danger"></i> 
                                        <span id="fileName"></span>
                                    </div>
                                </div>
                                
                                <h5 class="text-gray-700 mb-3 mt-4"><i class="fas fa-lock"></i> Account Password</h5>
                                <hr class="mb-4">
                                
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <div class="password-wrapper">
                                            <input type="password" class="form-control form-control-user <?php echo (!empty($field_errors) && (in_array('Password is required', $field_errors) || in_array('Password must be at least 6 characters', $field_errors)) ? 'is-invalid' : ''); ?>"
                                                id="password" name="password" placeholder="Password *" required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </button>
                                            <?php if (!empty($field_errors)): ?>
                                                <?php if (in_array('Password is required', $field_errors)): ?>
                                                    <div class="invalid-feedback d-block">Password is required</div>
                                                <?php elseif (in_array('Password must be at least 6 characters', $field_errors)): ?>
                                                    <div class="invalid-feedback d-block">Password must be at least 6 characters</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="password-wrapper">
                                            <input type="password" class="form-control form-control-user <?php echo (!empty($field_errors) && in_array('Passwords do not match', $field_errors) ? 'is-invalid' : ''); ?>"
                                                id="confirm_password" name="confirm_password" placeholder="Repeat Password *" required>
                                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                                <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                            </button>
                                            <?php if (!empty($field_errors) && in_array('Passwords do not match', $field_errors)): ?>
                                                <div class="invalid-feedback d-block">Passwords do not match</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input <?php echo (!empty($field_errors) && in_array('You must agree to the terms and conditions', $field_errors) ? 'is-invalid' : ''); ?>" 
                                            id="agree_terms" name="agree_terms" 
                                            <?php echo (isset($form_data['agree_terms']) && $form_data['agree_terms'] ? 'checked' : ''); ?> 
                                            required>
                                        <label class="custom-control-label" for="agree_terms">
                                            I agree to the terms and conditions and understand that my account will be pending approval after key verification.
                                        </label>
                                        <?php if (!empty($field_errors) && in_array('You must agree to the terms and conditions', $field_errors)): ?>
                                            <div class="invalid-feedback d-block">You must agree to the terms and conditions</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-danger btn-user btn-block">
                                    <i class="fas fa-user-shield"></i> Register & Verify Admin Key
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?php echo BASE_URL; ?>auth/login?tab=admin">Already have an account? Login!</a>
                            </div>
                            <div class="text-center mt-2">
                                <a class="small" href="<?php echo BASE_URL; ?>auth/registerCustomer">Register as Customer instead</a>
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
        
        // File upload validation
        document.getElementById('business_permit').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            
            if (file) {
                // Check file type
                if (file.type !== 'application/pdf') {
                    alert('Only PDF files are allowed. Please select a PDF file.');
                    e.target.value = '';
                    filePreview.style.display = 'none';
                    return;
                }
                
                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB. Please upload a smaller file.');
                    e.target.value = '';
                    filePreview.style.display = 'none';
                    return;
                }
                
                // Show file preview
                fileName.textContent = file.name;
                filePreview.style.display = 'block';
            } else {
                filePreview.style.display = 'none';
            }
        });
        
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

