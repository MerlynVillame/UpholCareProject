<div class="sidebar" data-background-color="dark" id="sidebar">
  <div class="sidebar-logo">
    <a href="index.php" class="logo" onclick="alert('Logo clicked!')">
      <img src="views/assets/img/kaiadmin/logo_light.svg" alt="UpholCare Logo" class="navbar-brand" id="sidebar-logo"/>
      <span class="logo-text" id="logo-text">UpholCare</span>
    </a>
    <button class="sidebar-toggle-btn" id="sidebar-toggle" title="Toggle Sidebar">
      <i class="fas fa-bars"></i>
    </button>
  </div>
  
  
  <div class="sidebar-wrapper">
    <div class="sidebar-content">
      <!-- Quick Access Section -->
      <div class="sidebar-section">
        <ul class="nav nav-primary quick-access">
          <li class="nav-item">
            <a href="index.php?m=dashboard" data-tooltip="Dashboard">
              <i class="fas fa-home"></i>
              <span class="nav-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?m=booking" data-tooltip="Bookings">
              <i class="fas fa-calendar-check"></i>
              <span class="nav-text">Bookings</span>
              <span class="badge badge-warning" id="booking-badge" onclick="alert('Booking badge clicked!')">2</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?m=payment" data-tooltip="Payments">
              <i class="fas fa-credit-card"></i>
              <span class="nav-text">Payments</span>
              <span class="badge badge-danger" id="payment-badge" onclick="alert('Payment badge clicked!')">1</span>
            </a>
          </li>
        </ul>
      </div>
      
      <!-- Main Navigation Section -->
      <div class="sidebar-section">
        <div class="section-title clickable" onclick="alert('Services clicked!'); window.location.href='index.php?m=services'" data-tooltip="View All Services">
          <i class="fas fa-th-large"></i>
          <span class="title-text">Services & Products</span>
        </div>
        <ul class="nav nav-primary">
          <li class="nav-item">
            <a href="index.php?m=services" data-tooltip="Services">
              <i class="fas fa-couch"></i>
              <span class="nav-text">Services</span>
            </a>
          </li>
        </ul>
      </div>
      
      <!-- Management Section -->
      <div class="sidebar-section">
        <div class="section-title clickable" onclick="alert('Management clicked!'); showManagementMenu()" data-tooltip="Management Options">
          <i class="fas fa-cog"></i>
          <span class="title-text">Management</span>
        </div>
        <ul class="nav nav-primary">
          <li class="nav-item">
            <a href="index.php?m=quotation" data-tooltip="Quotations">
              <i class="fas fa-file-invoice"></i>
              <span class="nav-text">Quotations</span>
              <span class="badge badge-warning" id="quote-badge" onclick="alert('Quote badge clicked!')">1</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?m=history" data-tooltip="History">
              <i class="fas fa-history"></i>
              <span class="nav-text">History</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?m=profile" data-tooltip="Profile">
              <i class="fas fa-user"></i>
              <span class="nav-text">Profile</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
  
  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    <div class="user-info clickable" onclick="alert('Profile clicked!'); window.location.href='index.php?m=profile'" data-tooltip="View Profile">
      <div class="user-avatar">
        <img src="views/assets/img/jm_denis.jpg" alt="User" class="avatar-img">
      </div>
      <div class="user-details" id="user-details">
        <div class="user-name">John Doe</div>
        <div class="user-role">Customer</div>
      </div>
    </div>
    <div class="sidebar-actions">
      <button class="action-btn" title="Settings" onclick="window.location.href='index.php?m=profile'">
        <i class="fas fa-cog"></i>
      </button>
      <button class="action-btn" title="Help" onclick="showHelp()">
        <i class="fas fa-question-circle"></i>
      </button>
      <button class="action-btn" title="Logout" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </div>
</div>

<!-- Mobile Overlay -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<script>
// Quick test to verify clicks are working
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sidebar DOM loaded');
    
    // Test sidebar positioning and visibility
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        const rect = sidebar.getBoundingClientRect();
        console.log('Sidebar position:', {
            top: rect.top,
            left: rect.left,
            width: rect.width,
            height: rect.height,
            zIndex: window.getComputedStyle(sidebar).zIndex
        });
        
        // Test if sidebar is clickable
        sidebar.addEventListener('click', function(e) {
            console.log('Click detected on sidebar:', e.target);
            console.log('Click coordinates:', e.clientX, e.clientY);
        });
    }
    
    // Test badge clicks specifically
    const badges = document.querySelectorAll('.badge');
    console.log('Found badges:', badges.length);
    badges.forEach((badge, index) => {
        badge.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Badge', index, 'clicked');
        });
    });
    
    // Test if main panel is overlapping
    const mainPanel = document.querySelector('.main-panel');
    if (mainPanel) {
        const rect = mainPanel.getBoundingClientRect();
        console.log('Main panel position:', {
            left: rect.left,
            width: rect.width,
            zIndex: window.getComputedStyle(mainPanel).zIndex
        });
    }
});
</script>
