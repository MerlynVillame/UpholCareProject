<?php
// Get current page from URL or set default
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
$isSuperAdmin = isset($data['is_super_admin']) ? $data['is_super_admin'] : false;
$pendingCustomersCount = isset($data['pending_customers_count']) ? $data['pending_customers_count'] : 0;
$pendingAdminCount = isset($data['stats']['pending_admin_registrations']) ? $data['stats']['pending_admin_registrations'] : 0;

// Determine active menu item based on URL
$activeDashboard = (strpos($currentUrl, 'dashboard') !== false || strpos($currentUrl, 'superAdminDashboard') !== false) ? 'active' : '';
$activeLoginLogs = (strpos($currentUrl, 'loginLogs') !== false) ? 'active' : '';
$activeAdminRegistrations = (strpos($currentUrl, 'adminRegistrations') !== false) ? 'active' : '';
$activeAdminAccounts = (strpos($currentUrl, 'adminAccounts') !== false) ? 'active' : '';
$activeCustomerAccounts = (strpos($currentUrl, 'customerAccounts') !== false) ? 'active' : '';
$activeStoreRatings = (strpos($currentUrl, 'storeRatings') !== false) ? 'active' : '';
$activeBannedStores = (strpos($currentUrl, 'bannedStores') !== false) ? 'active' : '';
$activeComplianceReports = (strpos($currentUrl, 'complianceReports') !== false) ? 'active' : '';
$activeBusinessRegistrations = (strpos($currentUrl, 'businessRegistrations') !== false) ? 'active' : '';
?>

<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar" style="background-color: #FFFFFF !important;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>control-panel/<?php echo $isSuperAdmin ? 'superAdminDashboard' : 'dashboard'; ?>">
        <div class="sidebar-brand-icon">
            <img src="<?php echo BASE_URL; ?>assets/images/logo2.png" alt="UpholCare Logo" style="height: 40px; width: auto;">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo $activeDashboard; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/<?php echo $isSuperAdmin ? 'superAdminDashboard' : 'dashboard'; ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Nav Item - Login Logs -->
    <li class="nav-item <?php echo $activeLoginLogs; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/loginLogs">
            <i class="fas fa-fw fa-history"></i>
            <span>Login Logs</span></a>
    </li>

    <?php if ($isSuperAdmin): ?>
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        User Management
    </div>

    <!-- Nav Item - Admin Registrations (Consolidated) -->
    <li class="nav-item <?php echo $activeAdminAccounts; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/adminAccounts">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Admin Registrations</span>
            <?php if ($pendingAdminCount > 0): ?>
                <span class="badge badge-danger"><?php echo $pendingAdminCount; ?></span>
            <?php endif; ?>
        </a>
    </li>

    <!-- Nav Item - Customer Accounts -->
    <li class="nav-item <?php echo $activeCustomerAccounts; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/customerAccounts">
            <i class="fas fa-fw fa-users"></i>
            <span>Customer Accounts</span>
            <?php if ($pendingCustomersCount > 0): ?>
                <span class="badge badge-danger"><?php echo $pendingCustomersCount; ?></span>
            <?php endif; ?>
        </a>
    </li>

    <!-- Nav Item - Business Registrations -->
    <li class="nav-item <?php echo $activeBusinessRegistrations; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/businessRegistrations">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>Business Registrations</span>
            <?php 
            $pendingBusinesses = isset($data['pending_businesses_count']) ? $data['pending_businesses_count'] : 0;
            if ($pendingBusinesses > 0): ?>
                <span class="badge badge-danger"><?php echo $pendingBusinesses; ?></span>
            <?php endif; ?>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Store Management
    </div>

    <!-- Nav Item - Store Ratings -->
    <li class="nav-item <?php echo $activeStoreRatings; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/storeRatings">
            <i class="fas fa-fw fa-star"></i>
            <span>Store Ratings</span></a>
    </li>

    <!-- Nav Item - Compliance Reports -->
    <li class="nav-item <?php echo $activeComplianceReports; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/complianceReports">
            <i class="fas fa-fw fa-file-contract"></i>
            <span>Compliance Reports</span>
            <?php 
            $pendingReports = isset($data['stats']['pending_compliance_reports']) ? $data['stats']['pending_compliance_reports'] : (isset($data['stats']['pending']) ? $data['stats']['pending'] : 0);
            if ($pendingReports > 0): ?>
                <span class="badge badge-danger"><?php echo $pendingReports; ?></span>
            <?php endif; ?>
        </a>
    </li>

    <!-- Nav Item - Banned Stores -->
    <li class="nav-item <?php echo $activeBannedStores; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>control-panel/bannedStores">
            <i class="fas fa-fw fa-ban"></i>
            <span>Banned Stores</span></a>
    </li>
    <?php endif; ?>

</ul>

<style>
/* Sidebar styling from admin_sidebar.php */
.sidebar {
    background-color: #FFFFFF !important;
    background-image: none !important;
    position: fixed !important;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
    width: 14rem;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    padding-top: 1rem;
    /* Hide scrollbar but keep functionality */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

/* Hide scrollbar for Chrome, Safari and Opera */
.sidebar::-webkit-scrollbar {
    display: none;
}

.sidebar .nav-item .nav-link {
    color: #333333 !important;
    font-weight: 500;
    padding: 0.85rem 1rem !important;
    display: flex;
    align-items: center;
}

.sidebar .nav-item .nav-link i {
    color: #2C3E50 !important;
    margin-right: 0.75rem;
    width: 1.25rem;
    text-align: center;
}

.sidebar .nav-item.active .nav-link {
    background-color: rgba(44, 62, 80, 0.05) !important;
    color: #2C3E50 !important;
    font-weight: 700;
    border-left: 4px solid #3498DB;
}

.sidebar-heading {
    color: #2C3E50 !important;
    font-weight: 800 !important;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 1px;
    padding: 1.5rem 1rem 0.5rem 1rem !important;
    opacity: 0.8;
}

.sidebar .badge {
    margin-left: auto;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 0.7rem;
}

/* Sidebar Divider */
.sidebar-divider {
    margin: 1rem 1rem !important;
}

/* Fix for fixed sidebar, header, and footer layout */
#content-wrapper {
    margin-left: 14rem;
    width: calc(100% - 14rem);
    min-height: 100vh;
    background-color: #f4f7f6;
    padding-top: 4.375rem; /* Space for fixed topbar */
    padding-bottom: 4rem; /* Space for fixed footer */
    display: flex;
    flex-direction: column;
}

#content {
    flex: 1 0 auto;
}

@media (max-width: 768px) {
    .sidebar {
        width: 6.5rem;
    }
    #content-wrapper {
        margin-left: 6.5rem;
        width: calc(100% - 6.5rem);
    }
    .sidebar .nav-item .nav-link span {
        display: none;
    }
    .sidebar .sidebar-brand-text {
        display: none;
    }
    .sidebar-heading {
        display: none;
    }
    /* Adjust fixed header and footer width for mobile */
    .topbar, footer.sticky-footer {
        width: calc(100% - 6.5rem) !important;
    }
}
</style>
