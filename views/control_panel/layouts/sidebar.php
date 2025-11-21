<?php
// Get current page from URL or set default
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
$isSuperAdmin = isset($data['is_super_admin']) ? $data['is_super_admin'] : false;
$pendingCount = isset($data['pending_count']) ? $data['pending_count'] : 0;
$pendingCustomersCount = isset($data['pending_customers_count']) ? $data['pending_customers_count'] : 0;
$pendingAdminCount = isset($data['stats']['pending_admin_registrations']) ? $data['stats']['pending_admin_registrations'] : 0;

// Get statistics for sidebar display
$stats = $data['stats'] ?? [];
$totalAdmins = $stats['total_active_admins'] ?? 0;
$totalCustomers = $stats['total_customers'] ?? 0;

// Determine active menu item based on URL
$activeDashboard = (strpos($currentUrl, 'dashboard') !== false || strpos($currentUrl, 'superAdminDashboard') !== false) ? 'active' : '';
$activeLoginLogs = (strpos($currentUrl, 'loginLogs') !== false) ? 'active' : '';
$activeAdminRegistrations = (strpos($currentUrl, 'adminRegistrations') !== false) ? 'active' : '';
$activeAdminAccounts = (strpos($currentUrl, 'adminAccounts') !== false) ? 'active' : '';
$activeCustomerAccounts = (strpos($currentUrl, 'customerAccounts') !== false) ? 'active' : '';
$activeStoreRatings = (strpos($currentUrl, 'storeRatings') !== false) ? 'active' : '';
$activeBannedStores = (strpos($currentUrl, 'bannedStores') !== false) ? 'active' : '';
$activeComplianceReports = (strpos($currentUrl, 'complianceReports') !== false) ? 'active' : '';
$activeRegister = (strpos($currentUrl, 'register') !== false) ? 'active' : '';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        box-shadow: 4px 0 20px rgba(0,0,0,0.3);
        z-index: 1000;
        overflow-y: auto;
        overflow-x: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .sidebar-user {
        padding: 20px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin: 10px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    .sidebar-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
    }

    .sidebar-user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        border: 3px solid rgba(255,255,255,0.2);
        position: relative;
    }

    .sidebar-user-avatar::after {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        border-radius: 50%;
        border: 2px solid rgba(102, 126, 234, 0.5);
        animation: rotate 3s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .sidebar-user-details {
        flex: 1;
        min-width: 0;
    }

    .sidebar-user-name {
        font-weight: 600;
        font-size: 15px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 4px;
    }

    .sidebar-user-role {
        font-size: 11px;
        color: rgba(255,255,255,0.7);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .sidebar-user-role .role-badge {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 600;
    }

    .sidebar-menu {
        padding: 10px 0;
        flex: 1;
    }

    .sidebar-menu-section {
        margin-bottom: 25px;
    }

    .sidebar-menu-title {
        padding: 12px 20px 8px 20px;
        color: rgba(255,255,255,0.5);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-menu-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        margin-left: 8px;
    }

    .sidebar-menu-item {
        display: flex;
        align-items: center;
        padding: 14px 20px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        position: relative;
        margin: 2px 10px;
        border-radius: 8px;
    }

    .sidebar-menu-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .sidebar-menu-item:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.1) 100%);
        color: white;
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .sidebar-menu-item:hover::before {
        transform: scaleY(1);
    }

    .sidebar-menu-item.active {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.3) 0%, rgba(118, 75, 162, 0.2) 100%);
        color: white;
        border-left-color: #667eea;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .sidebar-menu-item.active::before {
        transform: scaleY(1);
    }

    .sidebar-menu-item i {
        width: 22px;
        margin-right: 12px;
        font-size: 18px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .sidebar-menu-item:hover i {
        transform: scale(1.2);
    }

    .sidebar-menu-item span {
        flex: 1;
    }

    .sidebar-menu-item .badge {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(245, 87, 108, 0.4);
        animation: pulse-badge 2s ease-in-out infinite;
    }

    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .sidebar-footer {
        padding: 15px 20px;
        background: rgba(0,0,0,0.3);
        border-top: 1px solid rgba(255,255,255,0.1);
        margin-top: auto;
    }

    .sidebar-footer-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 8px;
        background: rgba(231, 76, 60, 0.1);
        border: 1px solid rgba(231, 76, 60, 0.3);
    }

    .sidebar-footer-link:hover {
        background: rgba(231, 76, 60, 0.2);
        color: white;
        border-color: rgba(231, 76, 60, 0.5);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }

    .sidebar-footer-link i {
        margin-right: 8px;
    }

    /* Main content wrapper */
    .main-content-wrapper {
        margin-left: 280px;
        transition: all 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            width: 280px;
        }

        .sidebar.show {
            transform: translateX(0);
            box-shadow: 4px 0 30px rgba(0,0,0,0.5);
        }

        .main-content-wrapper {
            margin-left: 0;
        }

        .sidebar-toggle {
            display: block !important;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
    }

    /* Scrollbar styling */
    .sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.2);
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #764ba2 0%, #667eea 100%);
    }

    /* Tooltip */
    .sidebar-menu-item[title]:hover::after {
        content: attr(title);
        position: absolute;
        left: 100%;
        margin-left: 10px;
        padding: 8px 12px;
        background: rgba(0,0,0,0.9);
        color: white;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 10000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
</style>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- User Info -->
    <div class="sidebar-user" style="margin-top: 20px;">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar">
                <i class="fas <?= $isSuperAdmin ? 'fa-crown' : 'fa-user-shield' ?>"></i>
            </div>
            <div class="sidebar-user-details">
                <div class="sidebar-user-name"><?= htmlspecialchars($data['admin']['fullname'] ?? 'Admin') ?></div>
                <div class="sidebar-user-role">
                    <span class="role-badge">
                        <i class="fas <?= $isSuperAdmin ? 'fa-crown' : 'fa-user-shield' ?>"></i>
                        <?= $isSuperAdmin ? 'Super Admin' : 'Admin' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="sidebar-menu">
        <!-- Main Menu -->
        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title">
                <i class="fas fa-bars"></i> Main Menu
            </div>
            <a href="<?= BASE_URL ?>control-panel/<?= $isSuperAdmin ? 'superAdminDashboard' : 'dashboard' ?>" 
               class="sidebar-menu-item <?= $activeDashboard ?>"
               title="Dashboard Overview">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= BASE_URL ?>control-panel/loginLogs" 
               class="sidebar-menu-item <?= $activeLoginLogs ?>"
               title="View Login History">
                <i class="fas fa-history"></i>
                <span>Login Logs</span>
            </a>
        </div>

        <!-- User Management (Super Admin Only) -->
        <?php if ($isSuperAdmin): ?>
        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title">
                <i class="fas fa-users-cog"></i> User Management
            </div>
            <a href="<?= BASE_URL ?>control-panel/adminRegistrations" 
               class="sidebar-menu-item <?= $activeAdminRegistrations ?>"
               title="Approve/Reject Admin Registrations">
                <i class="fas fa-user-clock"></i>
                <span>Admin Registrations</span>
                <?php if ($pendingAdminCount > 0): ?>
                    <span class="badge"><?= $pendingAdminCount ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>control-panel/adminAccounts" 
               class="sidebar-menu-item <?= $activeAdminAccounts ?>"
               title="View & Monitor All Admin Accounts">
                <i class="fas fa-user-shield"></i>
                <span>Admin Accounts</span>
            </a>
            <a href="<?= BASE_URL ?>control-panel/customerAccounts" 
               class="sidebar-menu-item <?= $activeCustomerAccounts ?>"
               title="Approve/Reject Customer Accounts">
                <i class="fas fa-users"></i>
                <span>Customer Accounts</span>
                <?php if ($pendingCustomersCount > 0): ?>
                    <span class="badge"><?= $pendingCustomersCount ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Store Management (Super Admin Only) -->
        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title">
                <i class="fas fa-store"></i> Store Management
            </div>
            <a href="<?= BASE_URL ?>control-panel/storeRatings" 
               class="sidebar-menu-item <?= $activeStoreRatings ?>"
               title="Monitor Store Performance & Ratings">
                <i class="fas fa-star-half-alt"></i>
                <span>Store Ratings</span>
            </a>
            <a href="<?= BASE_URL ?>control-panel/complianceReports" 
               class="sidebar-menu-item <?= $activeComplianceReports ?>"
               title="Review Store Compliance Reports">
                <i class="fas fa-file-contract"></i>
                <span>Compliance Reports</span>
                <?php 
                // Get pending compliance reports count from stats
                $pendingReports = isset($data['stats']['pending_compliance_reports']) ? $data['stats']['pending_compliance_reports'] : (isset($data['stats']['pending']) ? $data['stats']['pending'] : 0);
                if ($pendingReports > 0): 
                ?>
                    <span class="badge"><?= $pendingReports ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>control-panel/bannedStores" 
               class="sidebar-menu-item <?= $activeBannedStores ?>"
               title="View & Manage Banned Stores">
                <i class="fas fa-ban"></i>
                <span>Banned Stores</span>
            </a>
        </div>
        <?php endif; ?>

        <!-- System & Security (Super Admin Only) -->
        <?php if ($isSuperAdmin): ?>
        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title">
                <i class="fas fa-shield-alt"></i> System & Security
            </div>
            <a href="<?= BASE_URL ?>control-panel/register" 
               class="sidebar-menu-item <?= $activeRegister ?>"
               title="Register New Super Admin">
                <i class="fas fa-user-plus"></i>
                <span>New Super Admin</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>control-panel/logout" class="sidebar-footer-link" title="Logout from Control Panel">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="btn btn-primary sidebar-toggle d-none" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<script>
    // Sidebar toggle for mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        // Show toggle button on mobile
        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                if (sidebarToggle) {
                    sidebarToggle.classList.remove('d-none');
                }
            } else {
                if (sidebarToggle) {
                    sidebarToggle.classList.add('d-none');
                }
                sidebar.classList.remove('show');
            }
        }
        
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (sidebar && !sidebar.contains(e.target) && sidebarToggle && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Add smooth scroll behavior
        const menuItems = document.querySelectorAll('.sidebar-menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    setTimeout(() => {
                        sidebar.classList.remove('show');
                    }, 300);
                }
            });
        });
    });
</script>

