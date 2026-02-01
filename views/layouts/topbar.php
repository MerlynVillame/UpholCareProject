<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Sidebar Backdrop (Mobile) -->
        <div class="sidebar-backdrop"></div>

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

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
                        <button class="btn btn-primary" type="button">
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
                                    echo BASE_URL . 'startbootstrap-sb-admin-2-gh-pages/img/undraw_profile.svg';
                                }
                            ?>"
                            alt="Profile"
                            onerror="this.src='<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/img/undraw_profile.svg'">
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
                        <a class="dropdown-item" href="<?php 
                            $sessionUser = $_SESSION['user'] ?? null;
                            $displayUser = $sessionUser ?: ($user ?? []);
                            $userRole = $displayUser['role'] ?? 'customer';
                            echo BASE_URL . ($userRole === 'admin' ? 'admin' : 'customer'); ?>/profile">
                            <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                            Settings
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                            Activity Log
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
        <div class="container-fluid">

