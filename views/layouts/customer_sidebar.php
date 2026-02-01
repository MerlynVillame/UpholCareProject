<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>customer/dashboard" style="height: 80px;">
        <div class="sidebar-brand-icon" style="background: transparent; padding: 0; border-radius: 0;">
            <img src="<?php echo BASE_URL; ?>assets/images/logo2.png" alt="UpholCare Logo" style="height: 50px; width: auto;">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/dashboard">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading" style="font-size: 0.65rem; color: #8895a7; letter-spacing: 0.5px;">
        BOOKING MANAGEMENT
    </div>

    <!-- Nav Item - All Bookings -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'booking') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/bookings">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>All Bookings</span>
            
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading" style="font-size: 0.65rem; color: #8895a7; letter-spacing: 0.5px;">
        SERVICES & INFO
    </div>

    <!-- Nav Item - Fabric/Color Catalog -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'fabricsCatalog') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/fabricsCatalog">
            <i class="fas fa-fw fa-palette"></i>
            <span>Fabric/Color Catalog</span>
        </a>
    </li>

    <!-- Nav Item - Store Locations -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'storeLocations') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/storeLocations">
            <i class="fas fa-fw fa-map-marker-alt"></i>
            <span>Find Stores</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading" style="font-size: 0.65rem; color: #8895a7; letter-spacing: 0.5px;">
        ACCOUNT
    </div>

    <!-- Nav Item - History -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'history') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/history">
            <i class="fas fa-fw fa-history"></i>
            <span>Booking History</span>
        </a>
    </li>

    <!-- Nav Item - Profile -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'profile') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/profile">
            <i class="fas fa-fw fa-user-circle"></i>
            <span>My Profile</span>
        </a>
    </li>

    <!-- Divider -->
    <!-- <hr class="sidebar-divider d-none d-md-block"> -->

    <!-- Quick Action Button -->

    <!-- Sidebar Toggler (Sidebar) -->
    <!-- <div class="text-center d-none d-md-inline mb-3">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div> -->

</ul>
<!-- End of Sidebar -->

<style>
/* Sidebar border adjustments */
.sidebar,
#accordionSidebar {
    border-right: 3px solid var(--uphol-orange);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 999;
    width: 14rem; /* Ensure sidebar has fixed width on desktop */
}

/* Business mode - hide sidebar (only on profile page) */
.customer-profile .business-mode .sidebar {
    display: none !important;
}

.customer-profile .business-mode #content-wrapper {
    margin-left: 0 !important;
    width: 100% !important;
}

/* Local mode - ensure sidebar is always visible */
.sidebar {
    display: block !important;
}

/* Ensure sidebar is visible in local mode on profile page */
.customer-profile:not(.business-mode) .sidebar {
    display: block !important;
    visibility: visible !important;
}

/* Ensure sidebar is always visible on non-profile pages */
body:not(.customer-profile) .sidebar {
    display: block !important;
    visibility: visible !important;
}

/* Business mode indicator */
.business-mode-indicator {
    position: fixed;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    z-index: 9999;
    display: none;
    box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
}

.business-mode .business-mode-indicator {
    display: block;
}

.sidebar .nav-item.active .nav-link,
#accordionSidebar .nav-item.active .nav-link {
    background-color: rgba(255, 255, 255, 0.2) !important;
    opacity: 1 !important;
    color: #ffffff !important;
}

/* Ensure sidebar brand is not transparent */
.sidebar-brand {
    background: #0d1117 !important;
    background-color: #0d1117 !important;
    opacity: 1 !important;
}

/* Ensure all sidebar elements are fully opaque */
.sidebar,
.sidebar *,
#accordionSidebar,
#accordionSidebar * {
    opacity: 1 !important;
}

/* Override any transparent backgrounds - use solid colors */
.sidebar,
#accordionSidebar {
    background-color: var(--uphol-navy) !important;
    opacity: 1 !important;
}

/* Ensure nav items are not transparent */
.sidebar .nav-item,
#accordionSidebar .nav-item {
    opacity: 1 !important;
    background: transparent !important;
}

.sidebar .nav-link,
#accordionSidebar .nav-link {
    opacity: 1 !important;
    color: rgba(255, 255, 255, 0.8) !important;
}

.sidebar .nav-link:hover,
#accordionSidebar .nav-link:hover {
    opacity: 1 !important;
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.1) !important;
}

/* Ensure dividers are visible */
.sidebar-divider {
    opacity: 1 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

/* Ensure headings are visible */
.sidebar-heading {
    opacity: 1 !important;
    color: rgba(255, 255, 255, 0.6) !important;
}

.sidebar .nav-link {
    padding: 0.875rem 1rem;
    position: relative;
}

.sidebar .nav-link .badge-counter {
    position: absolute;
    right: 15px;
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Ensure sidebar content can scroll if needed */
.sidebar {
    -webkit-overflow-scrolling: touch;
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

/* Adjust container-fluid to ensure page titles are visible */
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

/* On mobile, reduce margin to show more content */
@media (max-width: 991.98px) {
    .container-fluid {
        margin-top: 56px !important; /* Topbar height on mobile */
        padding-top: 0.5rem !important; /* Smaller padding on mobile */
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
    overflow: visible !important;
}

/* Ensure content area doesn't cut off iframes */
#content {
    position: relative;
    z-index: 1;
    flex: 1;
    padding-bottom: 60px; /* Space for fixed footer */
    overflow: visible !important;
    padding-top: 0 !important;
}

/* When sidebar is toggled, adjust content margin */
body.sidebar-toggled #content-wrapper {
    margin-left: 6.5rem; /* Collapsed sidebar width */
}

/* Ensure iframes in content area are fully visible at the top */
#content iframe,
.container-fluid iframe,
.card iframe,
.card-body iframe {
    margin-top: 0 !important;
    padding-top: 0 !important;
    top: 0 !important;
    position: relative !important;
    display: block !important;
    visibility: visible !important;
    vertical-align: top !important;
}

/* Ensure iframes in content area are fully visible at the top */
#content iframe,
.container-fluid iframe,
.card iframe,
.card-body iframe {
    margin-top: 0 !important;
    padding-top: 0 !important;
    top: 0 !important;
    position: relative !important;
    display: block !important;
    visibility: visible !important;
    vertical-align: top !important;
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

/* Ensure modals and overlays are above sidebar */
.modal {
    z-index: 1050 !important;
}

.modal-backdrop {
    z-index: 1040 !important;
}

/* Responsive: On mobile, sidebar should be overlay */
@media (max-width: 991.98px) {
    /* Sidebar overlay on mobile */
    .sidebar,
    #accordionSidebar {
        position: fixed !important;
        top: 0 !important;
        left: -14rem; /* Hidden by default */
        height: 100vh !important;
        width: 14rem !important;
        z-index: 1060 !important;
        transition: left 0.3s ease, transform 0.3s ease !important;
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.4) !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        transform: translateX(0) !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        background: linear-gradient(180deg, #1a252f 0%, #2c3e50 100%) !important;
        background-color: #1a252f !important;
    }
    
    /* Show sidebar when toggled - ensure fully opaque */
    body.sidebar-toggled .sidebar,
    body.sidebar-toggled #accordionSidebar,
    .sidebar.toggled,
    #accordionSidebar.toggled,
    body.sidebar-toggled .sidebar.toggled,
    body.sidebar-toggled #accordionSidebar.toggled {
        left: 0 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        transform: translateX(0) !important;
        z-index: 1060 !important;
        background: linear-gradient(180deg, #1a252f 0%, #2c3e50 100%) !important;
        background-color: #1a252f !important;
    }
    
    /* Force all sidebar content to be opaque on mobile */
    @media (max-width: 991.98px) {
        body.sidebar-toggled .sidebar *,
        body.sidebar-toggled #accordionSidebar * {
            opacity: 1 !important;
        }
        
        body.sidebar-toggled .sidebar-brand {
            background: #0d1117 !important;
            background-color: #0d1117 !important;
            opacity: 1 !important;
        }
    }
    
    /* Overlay backdrop when sidebar is open */
    .sidebar-backdrop {
        display: none !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        z-index: 1050 !important;
        opacity: 1 !important;
        transition: opacity 0.3s ease !important;
    }
    
    body.sidebar-toggled .sidebar-backdrop {
        display: block !important;
        opacity: 1 !important;
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
        z-index: 1020 !important;
    }
    
    /* When sidebar is open, it should be above topbar */
    body.sidebar-toggled .sidebar,
    body.sidebar-toggled #accordionSidebar {
        z-index: 1060 !important;
        position: fixed !important;
    }
    
    body.sidebar-toggled .sidebar-backdrop {
        z-index: 1050 !important;
        position: fixed !important;
    }
    
    /* Ensure sidebar is always on top when toggled */
    @media (max-width: 991.98px) {
        body.sidebar-toggled .sidebar,
        body.sidebar-toggled #accordionSidebar {
            z-index: 9999 !important;
        }
        
        body.sidebar-toggled .sidebar-backdrop {
            z-index: 9998 !important;
        }
        
        /* Ensure topbar doesn't cover sidebar */
        body.sidebar-toggled .topbar {
            z-index: 1020 !important;
        }
    }
    
    /* Container fluid */
    .container-fluid {
        margin-top: 56px !important; /* Topbar height on mobile */
        padding-top: 0.5rem !important; /* Smaller padding on mobile */
        padding-left: 15px;
        padding-right: 15px;
        overflow: visible !important;
    }
    
    /* Ensure page titles are fully visible on mobile */
    .container-fluid > .d-sm-flex:first-child,
    .container-fluid > h1:first-child,
    .container-fluid > .h1:first-child,
    .container-fluid > h3:first-child,
    .container-fluid > .h3:first-child {
        margin-top: 0 !important;
        padding-top: 0 !important;
        visibility: visible !important;
        overflow: visible !important;
        text-overflow: clip !important;
        white-space: normal !important;
        line-height: 1.2 !important;
    }
    
    /* Ensure breadcrumbs are visible */
    .container-fluid .breadcrumb {
        margin-top: 0.25rem !important;
        padding-top: 0 !important;
        visibility: visible !important;
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
    
    .sidebar-brand-text small {
        font-size: 0.6rem;
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
        visibility: visible !important;
        opacity: 1 !important;
        z-index: 10000 !important;
        position: relative !important;
        pointer-events: auto !important;
        background-color: transparent !important;
        border: none !important;
        color: #5a5c69 !important;
        padding: 0.5rem !important;
        margin-right: 0.5rem !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        min-width: 40px !important;
        min-height: 40px !important;
        touch-action: manipulation !important;
    }
    
    #sidebarToggleTop:hover {
        color: #1F4E79 !important;
        background-color: rgba(139, 69, 19, 0.1) !important;
    }
    
    #sidebarToggleTop:active {
        transform: scale(0.95);
        background-color: rgba(139, 69, 19, 0.2) !important;
    }
    
    #sidebarToggleTop:focus {
        outline: 2px solid #1F4E79 !important;
        outline-offset: 2px !important;
    }
    
    #sidebarToggleTop i {
        pointer-events: none;
        font-size: 1.25rem !important;
    }
    
    /* Ensure button is always clickable even when sidebar is open */
    body.sidebar-toggled #sidebarToggleTop {
        z-index: 10001 !important;
        position: relative !important;
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

