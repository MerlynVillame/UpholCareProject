<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Sidebar Backdrop (Mobile) -->
        <div class="sidebar-backdrop"></div>

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar shadow-sm">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" type="button" aria-label="Toggle sidebar" style="color: #5a5c69; font-size: 1.25rem; padding: 0.5rem; z-index: 1051;">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Topbar Search -->
            <form
                class="d-none d-md-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                        aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" style="background-color: #2C3E50; border-color: #2C3E50;">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">

                <!-- Nav Item - Search Dropdown (Visible Only on Mobile) -->
                <li class="nav-item dropdown no-arrow d-md-none">
                    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-search fa-fw"></i>
                    </a>
                    <!-- Dropdown - Messages -->
                    <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                        aria-labelledby="searchDropdown">
                        <form class="form-inline mr-auto w-100 navbar-search">
                            <div class="input-group">
                                <input type="text" class="form-control bg-light border-0 small"
                                    placeholder="Search for..." aria-label="Search"
                                    aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>

                <!-- Nav Item - Alerts -->
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle notification-bell-link" href="#" id="alertsDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell fa-fw fa-lg"></i>
                        <!-- Counter - Alerts -->
                        <span class="badge badge-danger badge-counter" id="notificationBadge">0</span>
                    </a>
                    <!-- Dropdown - Alerts -->
                    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="alertsDropdown" id="notificationsDropdown">
                        <h6 class="dropdown-header">
                            Notifications
                        </h6>
                        <div id="notificationsList">
                            <div class="dropdown-item text-center">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <a class="dropdown-item text-center small text-gray-500" href="#" id="showAllNotifications" style="display: none;">Show All Notifications</a>
                    </div>
                </li>



                <div class="topbar-divider d-none d-md-block"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-md-inline text-gray-600 small"><?php 
                            // Get user from session first (most up-to-date), then fall back to $user from view
                            $sessionUser = $_SESSION['user'] ?? null;
                            $displayUser = $sessionUser ?: ($user ?? []);
                            $displayName = $displayUser['fullname'] ?? $displayUser['name'] ?? 'User';
                            echo htmlspecialchars($displayName); 
                        ?></span>
                        <img class="img-profile rounded-circle" id="topbarProfileImage"
                            src="<?php 
                                // Get user from session first (most up-to-date), then fall back to $user from view
                                $sessionUser = $_SESSION['user'] ?? null;
                                $displayUser = $sessionUser ?: ($user ?? []);
                                
                                // Get profile image from user data or use default
                                $profileImage = $displayUser['profile_image'] ?? null;
                                if ($profileImage && file_exists(ROOT . DS . $profileImage)) {
                                    echo BASE_URL . $profileImage . '?t=' . time();
                                } else {
                                    echo BASE_URL . 'assets/images/default-avatar.svg';
                                }
                            ?>"
                            alt="Profile"
                            onerror="this.src='<?php echo BASE_URL; ?>assets/images/default-avatar.svg'">
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="<?php 
                            $sessionUser = $_SESSION['user'] ?? null;
                            $displayUser = $sessionUser ?: ($user ?? []);
                            $userRole = $displayUser['role'] ?? 'customer';
                            echo BASE_URL . ($userRole === 'admin' ? 'admin' : 'customer'); ?>/profile">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid pb-4 dashboard-main">

        <?php 
        // Super Admin Specific Notification Modal
        $isSuperAdmin = isset($_SESSION['control_panel_admin']) && (($_SESSION['control_panel_admin']['role'] ?? '') === 'super_admin' || ($_SESSION['control_panel_admin']['is_super_admin'] ?? false));
        $pendingSystemItems = $data['pending_count'] ?? 0;
        
        if ($isSuperAdmin): 
        ?>
        <!-- Super Admin Review Modal -->
        <div class="modal fade" id="superAdminReviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                    <div class="modal-header border-0 bg-warning text-dark py-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 mr-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-shield-alt text-warning fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold m-0" id="reviewModalLabel">System Review Required</h5>
                                <p class="small m-0 opacity-75">Critical actions awaiting your approval</p>
                            </div>
                        </div>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4 bg-light-soft">
                        <div class="text-center mb-4">
                            <div class="h3 font-weight-bold text-gray-800"><?= $pendingSystemItems ?></div>
                            <div class="text-xs text-uppercase font-weight-bold text-muted tracking-widest">Total Pending Items</div>
                        </div>

                        <div class="list-group list-group-flush rounded shadow-sm overflow-hidden">
                            <?php if (($data['stats']['pending_admin_registrations'] ?? 0) > 0): ?>
                                <a href="<?= BASE_URL ?>control-panel/adminRegistrations" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0 mb-1">
                                    <div class="icon-circle bg-primary-soft text-primary mr-3">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-700 text-dark small mb-0">Admin Registrations</div>
                                        <div class="text-muted smaller"><?= $data['stats']['pending_admin_registrations'] ?> requests to verify</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 fa-xs"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (($data['pending_customers_count'] ?? 0) > 0): ?>
                                <a href="<?= BASE_URL ?>control-panel/customerAccounts?status=inactive" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0">
                                    <div class="icon-circle bg-info-soft text-info mr-3">
                                        <i class="fas fa-users-cog"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-700 text-dark small mb-0">Customer Approvals</div>
                                        <div class="text-muted smaller"><?= $data['pending_customers_count'] ?> accounts need review</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 fa-xs"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // If there are pending items, inject a high-priority notification into the list
                const pendingCount = <?= (int)$pendingSystemItems ?>;
                if (pendingCount > 0) {
                    // Update badge
                    const badge = $('#notificationBadge');
                    let currentCount = parseInt(badge.text()) || 0;
                    badge.text(currentCount + 1).removeClass('hide-badge');
                    $('#alertsDropdown').addClass('has-notifications');

                    // Prepend Critical System Alert to notifications list
                    const criticalAlert = `
                        <a class="dropdown-item d-flex align-items-center bg-warning-soft notification-item" href="#" data-toggle="modal" data-target="#superAdminReviewModal">
                            <div class="mr-3">
                                <div class="icon-circle bg-warning">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-warning font-weight-bold">CRITICAL REVIEW</div>
                                <span class="font-weight-bold">System Attention Required</span>
                                <div class="small text-gray-600">You have ${pendingCount} registration(s) awaiting approval.</div>
                            </div>
                        </a>
                    `;
                    
                    // Since notifications load via AJAX, we need to wait or intercept
                    $(document).ajaxComplete(function(event, xhr, settings) {
                        if (settings.url.indexOf('getNotifications') !== -1) {
                            if ($('#notificationsList .bg-warning-soft').length === 0) {
                                $('#notificationsList').prepend(criticalAlert);
                            }
                        }
                    });
                }
            });
        </script>
        
        <style>
            .bg-warning-soft { background-color: rgba(246, 194, 62, 0.08); }
            .bg-primary-soft { background-color: rgba(78, 115, 223, 0.1); }
            .bg-info-soft { background-color: rgba(54, 185, 204, 0.1); }
        </style>
        <?php endif; ?>

