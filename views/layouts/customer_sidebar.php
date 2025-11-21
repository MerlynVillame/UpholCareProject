<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: linear-gradient(180deg, #1a252f 0%, #2c3e50 100%);">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>customer/dashboard" style="height: 80px; background: rgba(0,0,0,0.2);">
        <div class="sidebar-brand-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 12px; border-radius: 12px;">
            <i class="fas fa-couch fa-lg" style="color: white;"></i>
        </div>
        <div class="sidebar-brand-text mx-2" style="font-weight: 700; font-size: 1.2rem; line-height: 1.3;">
            <?php echo APP_NAME; ?><br>
            <small style="font-size: 0.65rem; font-weight: 400; opacity: 0.8;">Customer Portal</small>
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

    <!-- Nav Item - Payments -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'payment') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/payments">
            <i class="fas fa-fw fa-credit-card"></i>
            <span>Payments</span>
            
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading" style="font-size: 0.65rem; color: #8895a7; letter-spacing: 0.5px;">
        SERVICES & INFO
    </div>

    <!-- Nav Item - Services Catalog -->
    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'service') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo BASE_URL; ?>customer/servicesCatalog">
            <i class="fas fa-fw fa-tools"></i>
            <span>Services Catalog</span>
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
.sidebar {
    border-right: 3px solid #4e73df;
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

.sidebar .nav-item.active .nav-link {
    background-color: rgba(255, 255, 255, 0.1);
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

/* Ensure iframes are properly positioned beside sidebar */
iframe {
    position: relative !important;
    z-index: 1 !important;
    background: white;
    width: 100%;
    display: block;
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
        padding-left: 15px;
        padding-right: 15px;
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

