<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<script>
// Add customer-profile class to body for CSS targeting
document.body.classList.add('customer-profile');
</script>

<style>
.welcome-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
    padding: 1rem 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid rgba(227, 230, 240, 0.6);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.welcome-text {
    color: #0F3C5F;
    font-weight: 700;
    font-size: 1.15rem;
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0;
}
</style>

<!-- Page Heading -->
<div class="welcome-container shadow-sm">
    <div>
        <div class="welcome-text">
            <i class="fas fa-user-circle mr-2" style="color: #0F3C5F;"></i>
            My Profile
        </div>
        <p class="mb-0 text-muted small">Manage your account information.</p>
    </div>
    <div>
        <!-- Single Mode Toggle Button -->
        <button type="button" class="btn btn-outline-primary" id="modeToggleBtn" onclick="toggleMode()" style="border-radius: 50px; font-weight: 600; padding: 0.5rem 1.5rem; font-size: 0.85rem;">
            <i class="fas fa-home mr-1"></i> <span id="modeToggleText">Local Mode</span>
        </button>
    </div>
</div>

<!-- Mode Status Indicator -->
<div class="alert alert-info" id="modeStatusAlert">
    <i class="fas fa-info-circle"></i>
    <span id="modeStatusText">You are currently in <strong>Local Mode</strong> - managing personal bookings and services.</span>
</div>

<!-- Profile Header with Cover Photo and Profile Image -->
<div class="card shadow mb-4">
    <div class="card-body p-0">
        <!-- Cover Photo Section -->
        <div class="cover-photo-container" id="coverPhotoContainer">
            <img src="<?php 
                // Get cover image from userDetails or user, with cache-busting (same as admin)
                $coverPath = $userDetails['cover_image'] ?? $user['cover_image'] ?? null;
                if ($coverPath && file_exists(ROOT . DS . $coverPath)) {
                    echo BASE_URL . $coverPath . '?t=' . time();
                } else {
                    echo BASE_URL . 'assets/images/default-cover.svg';
                }
            ?>" alt="Cover Photo" class="cover-photo" id="coverPhoto">
            <div class="cover-photo-overlay">
                <button type="button" class="btn btn-light btn-sm" id="changeCoverBtn">
                    <i class="fas fa-camera"></i> Change Cover Photo
                </button>
                <input type="file" id="cover_image" name="cover_image" accept="image/*" style="display: none;">
            </div>
            <div class="cover-photo-loading" id="coverPhotoLoading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i> Uploading...
            </div>
        </div>
        
        <!-- Profile Image and Info Section -->
        <div class="profile-info-section">
            <div class="row">
                <div class="col-md-3">
                    <div class="profile-image-container">
                        <img src="<?php 
                            // Get profile image from userDetails or user, with cache-busting (same as admin)
                            $imgPath = $userDetails['profile_image'] ?? $user['profile_image'] ?? null;
                            if ($imgPath && file_exists(ROOT . DS . $imgPath)) {
                                echo BASE_URL . $imgPath . '?t=' . time();
                            } else {
                                echo BASE_URL . 'assets/images/default-avatar.svg';
                            }
                        ?>" alt="Profile Image" class="profile-image" id="profileImage" 
                        onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>assets/images/default-avatar.svg'">
                        <button type="button" class="btn btn-primary btn-sm profile-image-btn" id="changeProfileBtn">
                            <i class="fas fa-camera"></i>
                        </button>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                        <div class="profile-image-loading" id="profileImageLoading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="profile-details">
                        <h2 class="profile-name"><?php echo htmlspecialchars($user['name'] ?? $user['fullname'] ?? 'User'); ?></h2>
                        <p class="profile-email"><?php echo htmlspecialchars($userDetails['email'] ?? $user['email'] ?? 'No email provided'); ?></p>
                        <div class="profile-badges">
                            <span class="badge badge-primary">Customer</span>
                            <span class="badge badge-success" id="modeBadge">Local Mode</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">

        <!-- Profile Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>customer/updateProfile" id="profileUpdateForm">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($userDetails['fullname'] ?? $user['fullname'] ?? $user['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userDetails['email'] ?? $user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($userDetails['phone'] ?? $user['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($userDetails['address'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>customer/changePassword">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Business Mode Content (Hidden by default) -->
    <div class="col-lg-12" id="businessModeContent" style="display: none;">
        <!-- Business Information / Registration status -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-briefcase"></i> Business Profile
                </h6>
                <?php if ($businessProfile): ?>
                    <?php 
                    $statusClass = 'badge-warning';
                    if ($businessProfile['status'] === 'approved') $statusClass = 'badge-success';
                    if ($businessProfile['status'] === 'rejected') $statusClass = 'badge-danger';
                    ?>
                    <span class="badge <?php echo $statusClass; ?> py-2 px-3">
                        Status: <?php echo ucfirst($businessProfile['status']); ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($businessProfile && $businessProfile['status'] === 'approved'): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Your business profile is approved. You can now make business reservations.
                    </div>
                <?php elseif ($businessProfile && $businessProfile['status'] === 'pending'): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> Your business registration is under review by the Super Admin.
                    </div>
                <?php elseif ($businessProfile && $businessProfile['status'] === 'rejected'): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Your registration was rejected. Reason: <?php echo htmlspecialchars($businessProfile['rejected_reason'] ?? 'Not specified'); ?>
                        <br>You can update your information and resubmit below.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Register your business to access corporate booking features and priority processing.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>customer/updateBusinessProfile" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_name">Business Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_name" name="business_name" 
                                       value="<?php echo htmlspecialchars($businessProfile['business_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_type_id">Business Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="business_type_id" name="business_type_id" required>
                                    <option value="">Select Business Type</option>
                                    <?php foreach ($businessTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>" <?php echo (isset($businessProfile['business_type_id']) && $businessProfile['business_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type['type_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="business_address">Business Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="business_address" name="business_address" rows="3" required><?php echo htmlspecialchars($businessProfile['business_address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="permit_file">Business Permit (PDF, JPG, PNG) <span class="text-danger">*</span></label>
                                <?php if (!empty($businessProfile['permit_file'])): ?>
                                    <div class="mb-2">
                                        <small class="text-success"><i class="fas fa-file-alt"></i> Current permit: <a href="<?php echo BASE_URL . $businessProfile['permit_file']; ?>" target="_blank">View File</a></small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control-file" id="permit_file" name="permit_file" <?php echo empty($businessProfile['permit_file']) ? 'required' : ''; ?> accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Upload a valid business permit or DTI registration for verification.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> <?php echo empty($businessProfile) ? 'Submit Registration' : 'Update & Resubmit'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Business Transactions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-chart-line"></i> Business Transactions
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Business Bookings</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="businessBookingsCount"><?php echo $businessStats['totalBookings'] ?? '0'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Business Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="businessRevenue"><?php echo $businessStats['totalRevenueFormatted'] ?? '₱0.00'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Orders</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingBusinessOrders"><?php echo $businessStats['pendingOrders'] ?? '0'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Active Projects</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeProjectsCount"><?php echo $businessStats['activeProjects'] ?? '0'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Business Mode Notice:</strong> In business mode, your bookings will be sent directly to the admin for processing. 
                            You will receive notifications about the status of your business orders.
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success btn-block mb-3" data-toggle="modal" data-target="#businessReservationModal">
                            <i class="fas fa-plus"></i> New Business Reservation
                            <small class="d-block">(Admin Processed)</small>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-warning btn-block mb-3" data-toggle="modal" data-target="#businessHistoryModal">
                            <i class="fas fa-history"></i> Business History
                            <small class="d-block">(Complete History)</small>
                        </button>
                    </div>
                </div>

                <!-- Recent Business Transactions -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="businessTransactionsTable">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="businessTransactionsBody">
                            <?php if (!empty($recentBusinessTransactions)): ?>
                                <?php foreach ($recentBusinessTransactions as $transaction): ?>
                                    <?php 
                                    $statusBadge = 'badge-info';
                                    if ($transaction['status'] === 'pending') $statusBadge = 'badge-warning';
                                    if (in_array($transaction['status'], ['completed', 'delivered_and_paid'])) $statusBadge = 'badge-success';
                                    if ($transaction['status'] === 'cancelled') $statusBadge = 'badge-secondary';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['business_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['service_name'] ?? 'N/A'); ?></td>
                                        <td>₱<?php echo number_format($transaction['total_amount'], 2); ?></td>
                                        <td><span class="badge <?php echo $statusBadge; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                                        <td><?php echo date('Y-m-d', strtotime($transaction['created_at'])); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $transaction['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No recent business transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Account Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Info</h6>
            </div>
            <div class="card-body">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Account Type:</strong> <span class="badge badge-info">Customer</span></p>
                <p><strong>Member Since:</strong> January 2024</p>
                <p><strong>Status:</strong> <span class="badge badge-success">Active</span></p>
            </div>
        </div>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<script>
// Business Mode Toggle Functionality
let currentMode = 'local'; // Default to local mode

// Toggle between Local and Business Mode
function toggleMode() {
    if (currentMode === 'local') {
        switchToBusinessMode();
    } else {
        switchToLocalMode();
    }
}

// Switch to Local Mode
function switchToLocalMode() {
    currentMode = 'local';
    
    // Update button
    const toggleBtn = document.getElementById('modeToggleBtn');
    const toggleText = document.getElementById('modeToggleText');
    const toggleIcon = toggleBtn.querySelector('i');
    
    toggleBtn.className = 'btn btn-outline-primary';
    toggleIcon.className = 'fas fa-home';
    toggleText.textContent = 'Local Mode';
    
    // Hide business content, show local content
    document.getElementById('businessModeContent').style.display = 'none';
    document.querySelector('.col-lg-8').style.display = 'block';
    document.querySelector('.col-lg-4').style.display = 'block';
    
    // Show sidebar in local mode
    setBusinessMode(false);
    
    // Update status message
    document.getElementById('modeStatusText').innerHTML = 'You are currently in <strong>Local Mode</strong> - managing personal bookings and services.';
    document.getElementById('modeStatusAlert').className = 'alert alert-info';
    
    // Update profile header badge
    document.getElementById('modeBadge').textContent = 'Local Mode';
    document.getElementById('modeBadge').className = 'badge badge-primary';
    
    // Update page title
    document.querySelector('h1').textContent = 'My Profile';
    document.querySelector('h1').nextElementSibling.textContent = 'Manage your account information.';
    
    // Store mode in session storage
    sessionStorage.setItem('profileMode', 'local');
    sessionStorage.setItem('businessMode', 'false');
}

// Switch to Business Mode
function switchToBusinessMode() {
    currentMode = 'business';
    
    // Update button
    const toggleBtn = document.getElementById('modeToggleBtn');
    const toggleText = document.getElementById('modeToggleText');
    const toggleIcon = toggleBtn.querySelector('i');
    
    toggleBtn.className = 'btn btn-outline-success';
    toggleIcon.className = 'fas fa-briefcase';
    toggleText.textContent = 'Business Mode';
    
    // Show business content, hide local content
    document.getElementById('businessModeContent').style.display = 'block';
    document.querySelector('.col-lg-8').style.display = 'none';
    document.querySelector('.col-lg-4').style.display = 'none';
    
    // Hide sidebar in business mode
    setBusinessMode(true);
    
    // Update status message
    document.getElementById('modeStatusText').innerHTML = 'You are currently in <strong>Business Mode</strong> - bookings go directly to admin for processing.';
    document.getElementById('modeStatusAlert').className = 'alert alert-success';
    
    // Update profile header badge
    document.getElementById('modeBadge').textContent = 'Business Mode';
    document.getElementById('modeBadge').className = 'badge badge-success';
    
    // Update page title
    document.querySelector('h1').textContent = 'Business Profile';
    document.querySelector('h1').nextElementSibling.textContent = 'Manage your business information and transactions.';
    
    // Load business data
    loadBusinessData();
    
    // Store mode in session storage
    sessionStorage.setItem('profileMode', 'business');
    sessionStorage.setItem('businessMode', 'true');
}

// Load business data
function loadBusinessData() {
    // Fetch real statistics from the server
    fetch('<?php echo BASE_URL; ?>customer/getBusinessStats')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                document.getElementById('businessBookingsCount').textContent = data.totalBookings;
                document.getElementById('businessRevenue').textContent = data.totalRevenue;
                document.getElementById('pendingBusinessOrders').textContent = data.pendingOrders;
                document.getElementById('activeProjectsCount').textContent = data.activeProjects;
            }
        })
        .catch(error => console.error('Error loading business stats:', error));
}

// Initialize page based on stored mode
document.addEventListener('DOMContentLoaded', function() {
    const savedMode = sessionStorage.getItem('profileMode');
    if (savedMode === 'business') {
        switchToBusinessMode();
    } else {
        switchToLocalMode();
    }
});

// Handle business profile form submission
document.addEventListener('DOMContentLoaded', function() {
    const businessForm = document.querySelector('form[action*="updateBusinessProfile"]');
    if (businessForm) {
        businessForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Simulate form submission
            alert('Business profile updated successfully!');
            
            // You would typically send this to the server
            console.log('Business profile data:', Object.fromEntries(formData));
        });
    }
    
    // Image Upload Functionality
    // Cover Photo Upload
    const coverImageInput = document.getElementById('cover_image');
    const coverPhoto = document.getElementById('coverPhoto');
    const changeCoverBtn = document.getElementById('changeCoverBtn');
    const coverPhotoLoading = document.getElementById('coverPhotoLoading');
    
    if (changeCoverBtn && coverImageInput) {
        changeCoverBtn.addEventListener('click', function() {
            coverImageInput.click();
        });
    }
    
    if (coverImageInput) {
        coverImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select a valid image file.');
                    coverImageInput.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size must be less than 5MB.');
                    coverImageInput.value = '';
                    return;
                }
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (coverPhoto) {
                        coverPhoto.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
                
                // Upload image
                uploadCoverPhoto(file);
            }
        });
    }
    
    // Profile Photo Upload
    const profileImageInput = document.getElementById('profile_image');
    const profileImage = document.getElementById('profileImage');
    const changeProfileBtn = document.getElementById('changeProfileBtn');
    const profileImageLoading = document.getElementById('profileImageLoading');
    
    if (changeProfileBtn && profileImageInput) {
        changeProfileBtn.addEventListener('click', function() {
            profileImageInput.click();
        });
    }
    
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select a valid image file.');
                    profileImageInput.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size must be less than 5MB.');
                    profileImageInput.value = '';
                    return;
                }
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (profileImage) {
                        profileImage.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
                
                // Upload image
                uploadProfilePhoto(file);
            }
        });
    }
});

// Upload Cover Photo
function uploadCoverPhoto(file) {
    const formData = new FormData();
    formData.append('cover_image', file);
    
    const coverPhotoLoading = document.getElementById('coverPhotoLoading');
    const changeCoverBtn = document.getElementById('changeCoverBtn');
    
    // Show loading
    if (coverPhotoLoading) {
        coverPhotoLoading.style.display = 'block';
    }
    if (changeCoverBtn) {
        changeCoverBtn.disabled = true;
        changeCoverBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    }
    
    fetch('<?php echo BASE_URL; ?>customer/uploadProfileImages', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Cover photo uploaded successfully!');
            // Update image src if server returned new path
                if (data.files && data.files.cover_image) {
                const coverPhoto = document.getElementById('coverPhoto');
                if (coverPhoto) {
                    const newImageUrl = '<?php echo BASE_URL; ?>' + data.files.cover_image + '?t=' + new Date().getTime();
                    coverPhoto.src = newImageUrl;
                    coverPhoto.onerror = null; // Remove error handler to allow new image
                    // Force reload after a short delay to ensure image is visible
                    setTimeout(() => {
                        coverPhoto.style.display = 'block';
                        coverPhoto.style.visibility = 'visible';
                        coverPhoto.style.opacity = '1';
                    }, 100);
                }
            } else {
                // Reload page to get updated image from server (same as admin)
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            showAlert('danger', data.message || 'Failed to upload cover photo.');
            // Revert preview on error after delay
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while uploading the cover photo.');
        setTimeout(() => {
            location.reload();
        }, 2000);
    })
    .finally(() => {
        // Hide loading
        if (coverPhotoLoading) {
            coverPhotoLoading.style.display = 'none';
        }
        if (changeCoverBtn) {
            changeCoverBtn.disabled = false;
            changeCoverBtn.innerHTML = '<i class="fas fa-camera"></i> Change Cover Photo';
        }
        // Reset file input
        const coverImageInput = document.getElementById('cover_image');
        if (coverImageInput) {
            coverImageInput.value = '';
        }
    });
}

// Upload Profile Photo
function uploadProfilePhoto(file) {
    const formData = new FormData();
    formData.append('profile_image', file);
    
    const profileImageLoading = document.getElementById('profileImageLoading');
    const changeProfileBtn = document.getElementById('changeProfileBtn');
    
    // Show loading
    if (profileImageLoading) {
        profileImageLoading.style.display = 'block';
    }
    if (changeProfileBtn) {
        changeProfileBtn.disabled = true;
        changeProfileBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    fetch('<?php echo BASE_URL; ?>customer/uploadProfileImages', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Profile photo uploaded successfully!');
            // Update image src if server returned new path
            if (data.files && data.files.profile_image) {
                const profileImage = document.getElementById('profileImage');
                if (profileImage) {
                    const newImageUrl = '<?php echo BASE_URL; ?>' + data.files.profile_image + '?t=' + new Date().getTime();
                    profileImage.src = newImageUrl;
                    profileImage.onerror = null; // Remove error handler to allow new image
                    // Update topbar profile image
                    updateTopbarProfileImage(newImageUrl);
                    // Force reload after a short delay to ensure image is visible
                    setTimeout(() => {
                        profileImage.style.display = 'block';
                        profileImage.style.visibility = 'visible';
                        profileImage.style.opacity = '1';
                    }, 100);
                }
            } else {
                // Reload page to get updated image from server (same as admin)
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            showAlert('danger', data.message || 'Failed to upload profile photo.');
            // Revert preview on error after delay
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while uploading the profile photo.');
        setTimeout(() => {
            location.reload();
        }, 2000);
    })
    .finally(() => {
        // Hide loading
        if (profileImageLoading) {
            profileImageLoading.style.display = 'none';
        }
        if (changeProfileBtn) {
            changeProfileBtn.disabled = false;
            changeProfileBtn.innerHTML = '<i class="fas fa-camera"></i>';
        }
        // Reset file input
        const profileImageInput = document.getElementById('profile_image');
        if (profileImageInput) {
            profileImageInput.value = '';
        }
    });
}

// Update topbar profile image (same as admin)
function updateTopbarProfileImage(imageUrl) {
    const topbarProfileImg = document.getElementById('topbarProfileImage');
    if (topbarProfileImg) {
        topbarProfileImg.src = imageUrl;
        topbarProfileImg.onerror = null;
    }
}

// Show Alert Function
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show custom-alert`;
    alert.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" onclick="this.parentElement.remove()">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Handle profile form submission - update display immediately
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileUpdateForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            // Form will submit normally and page will reload
            // The session will be updated on the server side
            // After reload, the topbar and profile page will show updated info
        });
    }
    
    // Update topbar profile image when profile image is uploaded
    // This is already handled in uploadProfilePhoto function
});

</script>

<style>

/* Profile Header Styles */
.cover-photo-container {
    position: relative;
    height: 200px;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
}

.cover-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.cover-photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.cover-photo-container:hover .cover-photo-overlay {
    opacity: 1;
}

.cover-photo-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    z-index: 10;
}

.profile-info-section {
    padding: 20px;
    background: #fff;
    border-radius: 0 0 8px 8px;
}

.profile-image-container {
    position: relative;
    display: inline-block;
}

.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.profile-image-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
    z-index: 5;
}

.profile-image-container:hover .profile-image-btn {
    opacity: 1;
}

.profile-image-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 12px;
    border-radius: 50%;
    z-index: 10;
    font-size: 0.8rem;
}

.profile-image-container {
    position: relative;
}

/* Default images fallback */
#coverPhoto {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
    min-height: 200px;
}

#profileImage {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
    border: 4px solid #fff;
}

.profile-details {
    padding-left: 20px;
}

.profile-name {
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.profile-email {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 15px;
}

.profile-badges .badge {
    margin-right: 8px;
    font-size: 0.8rem;
    padding: 6px 12px;
}

/* Business Mode Profile Styles */
.business-mode .profile-name {
    color: #28a745;
}

.business-mode .profile-badges .badge-success {
    background-color: #28a745;
}

/* Responsive Design */
@media (max-width: 768px) {
    .profile-info-section .row {
        text-align: center;
    }
    
    .profile-details {
        padding-left: 0;
        margin-top: 15px;
    }
    
    .profile-image {
        width: 100px;
        height: 100px;
    }
    
    .cover-photo-container {
        height: 150px;
    }
}
</style>

<?php require_once ROOT . DS . 'views' . DS . 'customer' . DS . 'modals' . DS . 'business_reservation_modal.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'customer' . DS . 'modals' . DS . 'business_history_modal.php'; ?>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

