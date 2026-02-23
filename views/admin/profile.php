<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
        <p class="mb-0">Manage your account information.</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Profile Header with Cover Photo and Profile Image -->
<div class="card shadow mb-4">
    <div class="card-body p-0">
        <!-- Cover Photo Section -->
        <div class="cover-photo-container" id="coverPhotoContainer">
            <img src="<?php echo $coverImage; ?>" alt="Cover Photo" class="cover-photo" id="coverPhoto">
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
                            // Get profile image from userDetails or user, with cache-busting
                            $imgPath = $userDetails['profile_image'] ?? $user['profile_image'] ?? null;
                            if ($imgPath && file_exists(ROOT . DS . $imgPath)) {
                                echo BASE_URL . $imgPath . '?t=' . time();
                            } else {
                                echo BASE_URL . 'assets/images/default-avatar.svg';
                            }
                        ?>" alt="Profile Image" class="profile-image" id="profileImage" 
                        onerror="this.src='<?php echo BASE_URL; ?>assets/images/default-avatar.svg'">
                        <button type="button" class="btn btn-primary-admin btn-sm profile-image-btn" id="changeProfileBtn">
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
                        <h2 class="profile-name" id="profileDisplayName"><?php echo htmlspecialchars($user['name'] ?? $user['fullname'] ?? 'Admin'); ?></h2>
                        <p class="profile-email" id="profileDisplayEmail"><?php echo htmlspecialchars($userDetails['email'] ?? $user['email'] ?? 'No email provided'); ?></p>
                        <div class="profile-badges">
                            <span class="text-primary-admin font-weight-bold">Admin</span>
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
                <h6 class="m-0 font-weight-bold text-primary-admin">Profile Information</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>admin/updateProfile" id="profileUpdateForm">
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
                    <button type="submit" class="btn btn-primary-admin">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary-admin">Change Password</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>admin/changePassword">
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
                    <button type="submit" class="btn btn-primary-admin">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Account Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary-admin">Account Info</h6>
            </div>
            <div class="card-body">
                <p><strong>Role:</strong> <span class="text-primary-admin font-weight-bold">Admin</span></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($userDetails['email'] ?? $user['email'] ?? 'N/A'); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($userDetails['phone'] ?? $user['phone'] ?? 'N/A'); ?></p>
                <p><strong>Member Since:</strong> <?php echo !empty($userDetails['created_at']) ? date('F Y', strtotime($userDetails['created_at'])) : 'N/A'; ?></p>
                <p><strong>Status:</strong> <span class="text-success font-weight-bold">Active</span></p>
            </div>
        </div>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<script>
// Image Upload Functionality
document.addEventListener('DOMContentLoaded', function() {
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
    
    fetch('<?php echo BASE_URL; ?>admin/uploadProfileImages', {
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
                    coverPhoto.src = '<?php echo BASE_URL; ?>' + data.files.cover_image + '?t=' + new Date().getTime();
                }
            }
        } else {
            showAlert('danger', data.message || 'Failed to upload cover photo.');
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
    
    fetch('<?php echo BASE_URL; ?>admin/uploadProfileImages', {
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
                    // Reload page to get updated image from server
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
            showAlert('danger', data.message || 'Failed to upload profile photo.');
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

// Update topbar profile image
function updateTopbarProfileImage(imageUrl) {
    const topbarProfileImg = document.getElementById('topbarProfileImage');
    if (!topbarProfileImg) {
        // Fallback to class selector
        const topbarProfileImgByClass = document.querySelector('.img-profile');
        if (topbarProfileImgByClass) {
            topbarProfileImgByClass.src = imageUrl;
        }
    } else {
        topbarProfileImg.src = imageUrl;
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
}

.profile-info-section {
    padding: 20px;
    background: white;
}

.profile-image-container {
    position: relative;
    display: inline-block;
    margin-top: -80px;
    margin-left: 20px;
}

.profile-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid white;
    object-fit: cover;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: block;
    background-color: #f8f9fa;
    background-image: url('<?php echo BASE_URL; ?>assets/images/default-avatar.svg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.profile-image-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-image-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 10px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-details {
    margin-top: 20px;
}

.profile-name {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.profile-email {
    color: #6c757d;
    margin-bottom: 15px;
}

.profile-badges {
    margin-top: 10px;
}
</style>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

