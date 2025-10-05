<?php 
// Determine requested module with basic sanitization and default
$rawModule = isset($_GET['m']) && $_GET['m'] !== '' ? $_GET['m'] : 'dashboard';
$module = strtolower(preg_replace('/[^a-z_]/i', '', $rawModule));

// Whitelisted route map to avoid arbitrary file includes
$routeMap = [
  'services'  => 'views/cus_services.php',
  'booking'   => 'views/cus_booking.php',
  'payment'   => 'views/cus_payment.php',
  'quotation' => 'views/quotation.php',
  'history'   => 'views/history.php',
  // 'materials' => 'views/materials.php',
  'search'    => 'views/search.php',
  'profile'   => 'views/profile.php',
  'dashboard' => 'views/cus_dashboard.php',
];
?>

<?php include("views/header.php"); ?>

<body>
  <div class="wrapper">
    
    <!-- Sidebar -->
    <?php include("views/side_bar.php"); ?>

    <!-- Main Panel -->
    <div class="main-panel">
      
      <!-- Top Menu -->
      <?php include("views/top_menu.php"); ?>
      <div class="content">
        <div class="page-inner">
          <?php
          // Derive a human-friendly page title for the header
          $pageTitles = [
            'dashboard' => 'Dashboard',
            'services' => 'Services',
            'booking' => 'Bookings',
            'payment' => 'Payments',
            'quotation' => 'Quotations',
            'history' => 'History',
            'materials' => 'Materials',
            'search' => 'Search Results',
            'shop' => 'Shop',
            'profile' => 'Profile',
          ];
          $currentTitle = isset($pageTitles[$module]) ? $pageTitles[$module] : 'Dashboard';
          ?>
          <!-- Page Header -->
          <div class="page-header bg-white border-bottom">
            <div class="container-fluid py-3 px-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="mb-1"><?= htmlspecialchars($currentTitle) ?></h3>
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="index.php?m=dashboard">Home</a></li>
                      <?php if ($module !== 'dashboard'): ?>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($currentTitle) ?></li>
                      <?php endif; ?>
                    </ol>
                  </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <button class="btn btn-outline-secondary btn-sm" onclick="window.history.back()" title="Go Back">
                    <i class="fas fa-arrow-left"></i>
                  </button>
                  <button class="btn btn-outline-primary btn-sm" onclick="location.reload()" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Page Content -->
          <main class="container-fluid py-3 px-4">
            <?php 
            // Safely include the mapped file or fall back to dashboard
            $target = isset($routeMap[$module]) ? $routeMap[$module] : $routeMap['dashboard'];
            if (!is_string($target) || $target === '' || !file_exists($target)) {
              $target = $routeMap['dashboard'];
            }
            require_once $target;
            ?>
          </main>
        </div>
      </div>

      <!-- Footer -->
      <?php include("views/footer.php"); ?>
    </div>
  </div>
</body>
</html>
