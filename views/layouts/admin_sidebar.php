<?php
$current_uri = $_SERVER['REQUEST_URI'] ?? '';
$page = 'dashboard'; // Default

if (strpos($current_uri, 'admin/allBookings') !== false) $page = 'allBookings';
elseif (strpos($current_uri, 'admin/archivedBookings') !== false) $page = 'archivedBookings';
elseif (strpos($current_uri, 'admin/inventory') !== false) $page = 'inventory';
elseif (strpos($current_uri, 'admin/services') !== false) $page = 'services';
elseif (strpos($current_uri, 'admin/storeRatings') !== false) $page = 'storeRatings';
elseif (strpos($current_uri, 'admin/dailySchedule') !== false) $page = 'dailySchedule';
elseif (strpos($current_uri, 'admin/reports') !== false) $page = 'reports';
elseif (strpos($current_uri, 'admin/dashboard') !== false) $page = 'dashboard';
?>
<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar" style="background-color: #FFFFFF !important;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>admin/dashboard">
        <div class="sidebar-brand-icon">
            <img src="<?php echo BASE_URL; ?>assets/images/logo2.png" alt="UpholCare Logo" style="height: 40px; width: auto;">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>

    <!-- Nav Item - Reservations -->
    <li class="nav-item <?php echo $page === 'allBookings' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/allBookings">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>All Bookings</span></a>
    </li>

    <!-- Nav Item - Archived Bookings -->
    <li class="nav-item <?php echo $page === 'archivedBookings' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/archivedBookings">
            <i class="fas fa-fw fa-archive"></i>
            <span>Archived Bookings</span></a>
    </li>

    <!-- Nav Item - Inventory -->
    <li class="nav-item <?php echo $page === 'inventory' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/inventory">
            <i class="fas fa-fw fa-box"></i>
            <span>Inventory</span></a>
    </li>

    <!-- Nav Item - Services -->
    <li class="nav-item <?php echo $page === 'services' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/services">
            <i class="fas fa-fw fa-tools"></i>
            <span>Services</span></a>
    </li>

    <!-- Nav Item - Store Ratings -->
    <li class="nav-item <?php echo $page === 'storeRatings' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/storeRatings">
            <i class="fas fa-fw fa-star"></i>
            <span>Store Ratings</span></a>
    </li>

    <!-- Nav Item - Logistics Management -->
    <li class="nav-item <?php echo $page === 'dailySchedule' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/dailySchedule">
            <i class="fas fa-fw fa-truck"></i>
            <span>Logistics Management</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Reports
    </div>

    <!-- Nav Item - Reports -->
    <li class="nav-item <?php echo $page === 'reports' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/reports">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Reports</span></a>
    </li>

    <!-- Divider
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <!-- <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div> --> 
</ul>
<!-- End of Sidebar -->

<style>
/* Admin Sidebar - Fixed positioning */
.sidebar {
    background-color: #FFFFFF !important;
    background-image: none !important;
    position: fixed !important;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 999;
    width: 14rem; /* Standard SB Admin 2 sidebar width */
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    -webkit-overflow-scrolling: touch;
}

/* Sidebar Text & Design Enhancements */
.sidebar .nav-item .nav-link {
    color: #333333 !important;
    font-weight: 500;
    transition: all 0.2s ease;
}

.sidebar .nav-item .nav-link i {
    color: #0F3C5F !important;
    transition: all 0.2s ease;
}

.sidebar .nav-item .nav-link:hover {
    background-color: rgba(15, 60, 95, 0.05);
    color: #0F3C5F !important;
    padding-left: 1.25rem;
}

.sidebar .nav-item .nav-link:hover i {
    color: #0F3C5F !important;
}

.sidebar .nav-item.active .nav-link {
    background-color: rgba(15, 60, 95, 0.1) !important;
    color: #0F3C5F !important;
    font-weight: 700;
    border-left: 4px solid #0F3C5F;
}

.sidebar .nav-item.active .nav-link i {
    color: #0F3C5F !important;
}

.sidebar-heading {
    color: #0F3C5F !important;
    font-weight: 800 !important;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
    padding-top: 1rem;
    padding-bottom: 0.5rem;
    opacity: 0.9;
}

.sidebar .sidebar-brand-text {
    color: #000000 !important;
}

/* Custom scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Content wrapper should be beside sidebar, not under it */
#content-wrapper {
    position: relative;
    z-index: 1;
    margin-left: 14rem; /* Match sidebar width - 14rem = 224px */
    transition: margin-left 0.3s ease;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* When sidebar is toggled, adjust content margin */
body.sidebar-toggled #content-wrapper {
    margin-left: 6.5rem; /* Collapsed sidebar width */
}

/* Ensure content area is properly positioned */
#content {
    position: relative;
    z-index: 1;
    flex: 1;
    padding-bottom: 60px; /* Space for fixed footer */
}

/* Topbar - Fixed positioning */
.topbar {
    position: fixed !important;
    top: 0;
    left: 14rem; /* Start after sidebar */
    right: 0;
    z-index: 998 !important;
    transition: left 0.3s ease;
    margin-bottom: 0 !important;
    height: auto !important;
    min-height: 56px !important;
}

/* Ensure topbar doesn't cover content */
.topbar + .container-fluid {
    margin-top: 80px !important;
    padding-top: 1rem !important;
}

/* When sidebar is toggled, adjust topbar position */
body.sidebar-toggled .topbar {
    left: 6.5rem; /* Collapsed sidebar width */
}

/* Container fluid should account for topbar */
.container-fluid {
    margin-top: 80px; /* Space for fixed topbar */
    padding-top: 1rem !important; /* Add padding to ensure titles are visible */
    overflow: visible !important;
}

/* Ensure first child of container-fluid is fully visible */
.container-fluid > *:first-child {
    margin-top: 0 !important;
    padding-top: 0 !important;
    visibility: visible !important;
    overflow: visible !important;
}

/* Ensure page heading divs are fully visible */
.container-fluid > .d-sm-flex,
.container-fluid > .d-flex {
    margin-top: 0 !important;
    padding-top: 0 !important;
    visibility: visible !important;
    overflow: visible !important;
}

/* Ensure h1, h2, h3 in page headings are fully visible */
.container-fluid .d-sm-flex h1,
.container-fluid .d-sm-flex .h1,
.container-fluid .d-sm-flex h2,
.container-fluid .d-sm-flex .h2,
.container-fluid .d-sm-flex h3,
.container-fluid .d-sm-flex .h3,
.container-fluid .d-flex h1,
.container-fluid .d-flex .h1,
.container-fluid .d-flex h2,
.container-fluid .d-flex .h2,
.container-fluid .d-flex h3,
.container-fluid .d-flex .h3 {
    margin-top: 0 !important;
    padding-top: 0 !important;
    visibility: visible !important;
    overflow: visible !important;
    text-overflow: clip !important;
    white-space: normal !important;
    line-height: 1.2 !important;
}

/* Ensure page headings are fully visible and not cut off */
.container-fluid > .d-sm-flex,
.container-fluid > h1,
.container-fluid > .h1,
.container-fluid > h2,
.container-fluid > .h2,
.container-fluid > h3,
.container-fluid > .h3,
.container-fluid > .page-title,
.container-fluid > .dashboard-title,
.container-fluid h1,
.container-fluid .h1,
.container-fluid h2,
.container-fluid .h2,
.container-fluid h3,
.container-fluid .h3,
.container-fluid .page-title,
.container-fluid .dashboard-title {
    margin-top: 0 !important;
    padding-top: 0 !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 1 !important;
    overflow: visible !important;
    text-overflow: clip !important;
    white-space: normal !important;
}

/* Ensure breadcrumbs are visible */
.container-fluid .breadcrumb {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* Footer - Fixed positioning */
.sticky-footer {
    position: fixed !important;
    bottom: 0;
    left: 14rem; /* Start after sidebar */
    right: 0;
    z-index: 997 !important;
    transition: left 0.3s ease;
    height: 60px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

/* When sidebar is toggled, adjust footer position */
body.sidebar-toggled .sticky-footer {
    left: 6.5rem; /* Collapsed sidebar width */
}

/* Ensure iframes are properly positioned beside sidebar and fully visible */
iframe {
    position: relative !important;
    z-index: 1 !important;
    background: white;
    width: 100%;
    display: block;
    margin-top: 0 !important;
    padding-top: 0 !important;
    top: 0 !important;
    visibility: visible !important;
    opacity: 1 !important;
    vertical-align: top !important;
}

/* Ensure iframe containers don't cut off the top */
iframe[src*="maps"],
iframe[src*="map"],
iframe[src*="google"],
iframe[src*="embed"] {
    margin-top: 0 !important;
    padding-top: 0 !important;
    top: 0 !important;
    position: relative !important;
    display: block !important;
    visibility: visible !important;
    vertical-align: top !important;
}

/* Ensure iframe wrapper divs don't hide the top */
div[style*="border-radius"] iframe,
.card-body iframe,
.card iframe {
    margin-top: 0 !important;
    padding-top: 0 !important;
    top: 0 !important;
}

/* Responsive: On mobile, sidebar should be overlay */
@media (max-width: 991.98px) {
    /* Sidebar overlay on mobile */
    .sidebar {
        position: fixed !important;
        top: 0;
        left: -14rem; /* Hidden by default */
        height: 100vh;
        width: 14rem !important;
        z-index: 1050;
        transition: left 0.3s ease;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
    }
    
    /* Show sidebar when toggled */
    body.sidebar-toggled .sidebar {
        left: 0;
    }
    
    /* Overlay backdrop when sidebar is open */
    .sidebar-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1040;
    }
    
    body.sidebar-toggled .sidebar-backdrop {
        display: block;
    }
    
    /* Content wrapper adjustments */
    #content-wrapper {
        margin-left: 0 !important;
        width: 100% !important;
    }
    
    /* Topbar adjustments */
    .topbar {
        position: fixed !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1030 !important;
    }
    
    /* Container fluid */
    .container-fluid {
        margin-top: 56px !important; /* Topbar height on mobile */
        padding-top: 0.5rem !important; /* Smaller padding on mobile */
        padding-left: 15px;
        padding-right: 15px;
    }
    
    /* Ensure page titles are fully visible on mobile */
    .container-fluid h1,
    .container-fluid .h1,
    .container-fluid h2,
    .container-fluid .h2,
    .container-fluid h3,
    .container-fluid .h3,
    .container-fluid .page-title {
        margin-top: 0 !important;
        padding-top: 0 !important;
        line-height: 1.2 !important;
    }
    
    /* Ensure iframes are fully visible on mobile */
    iframe {
        margin-top: 0 !important;
        padding-top: 0 !important;
        top: 0 !important;
    }
    
    /* Footer */
    .sticky-footer {
        position: relative !important;
        left: 0 !important;
        margin-left: 0 !important;
    }
    
    #content {
        padding-bottom: 1rem !important;
    }
    
    /* Sidebar brand adjustments */
    .sidebar-brand {
        height: 56px !important; /* Match topbar height */
        padding: 0.5rem;
    }
    
    .sidebar-brand-text {
        font-size: 0.9rem;
    }
    
    /* Nav items */
    .sidebar .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .sidebar .nav-link i {
        font-size: 1rem;
        width: 1.5rem;
    }
    
    /* Sidebar toggler */
    #sidebarToggle {
        display: block !important;
    }
    
    /* Ensure sidebar toggle button in topbar is visible */
    #sidebarToggleTop {
        display: block !important;
    }
}

/* Small mobile devices */
@media (max-width: 575.98px) {
    .sidebar {
        width: 16rem !important; /* Slightly wider on small screens */
    }
    
    .sidebar-brand-text {
        font-size: 0.85rem;
    }
    
    .sidebar .nav-link {
        padding: 0.65rem 0.75rem;
        font-size: 0.85rem;
    }
}
</style>
