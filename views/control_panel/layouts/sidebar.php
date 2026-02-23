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
        background: #FFFFFF !important;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        z-index: 1000;
        overflow-y: auto;
        overflow-x: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #e3e6f0;
    }

    .sidebar-user {
        padding: 20px;
        background: rgba(15, 60, 95, 0.05);
        border-bottom: 1px solid rgba(15, 60, 95, 0.1);
        margin: 10px;
        border-radius: 12px;
    }

    .sidebar-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #0F3C5F;
    }

    .sidebar-user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--uphol-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.4);
        border: 3px solid rgba(255,255,255,0.2);
        position: relative;
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
        color: #5a5c69;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .sidebar-user-role .role-badge {
        background: #0F3C5F;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 600;
        color: white;
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
        color: #0F3C5F;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.7;
    }

    .sidebar-menu-item {
        display: flex;
        align-items: center;
        padding: 14px 20px;
        color: #333333;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        position: relative;
        margin: 2px 10px;
        border-radius: 12px;
    }

    .sidebar-menu-item:hover {
        background: rgba(15, 60, 95, 0.05);
        color: #0F3C5F;
        transform: translateX(5px);
    }

    .sidebar-menu-item.active {
        background: rgba(15, 60, 95, 0.1);
        color: #0F3C5F;
        border-left-color: #0F3C5F;
        font-weight: 700;
    }

    .sidebar-menu-item i {
        width: 20px;
        margin-right: 12px;
        font-size: 16px;
        text-align: center;
        color: #0F3C5F;
        transition: all 0.3s ease;
    }

    .sidebar-menu-item .badge {
        margin-left: auto;
        background: #0F3C5F;
        color: white;
        font-size: 10px;
        padding: 4px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .sidebar-footer {
        padding: 20px;
        margin-top: auto;
        border-top: 1px solid #e3e6f0;
    }

    .sidebar-footer-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        color: #dc3545;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 10px;
        font-weight: 600;
        background: rgba(220, 53, 69, 0.05);
        border: 1px solid rgba(220, 53, 69, 0.1);
    }

    .sidebar-footer-link:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2);
    }

    .sidebar-footer-link i {
        font-size: 18px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
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
            background: var(--uphol-blue);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
        }
    }
</style>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- User Info -->
    <div class="sidebar-user" style="margin-top: 20px; background: white; border: 1px solid #e3e6f0; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar" style="background: #0F3C5F; box-shadow: none; border: none;">
                <i class="fas <?= $isSuperAdmin ? 'fa-crown' : 'fa-user-shield' ?>"></i>
            </div>
            <div class="sidebar-user-details">
                <div class="sidebar-user-name" style="color: #0F3C5F;"><?= htmlspecialchars($data['admin']['fullname'] ?? 'Admin') ?></div>
                <div class="sidebar-user-role">
                    <span class="role-badge" style="background: rgba(15, 60, 95, 0.1); color: #0F3C5F; border: 1px solid rgba(15, 60, 95, 0.2);">
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

    </nav>
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
