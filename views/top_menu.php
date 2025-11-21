<?php
// topmenu.php
?>
<div class="main-header">
  <nav class="navbar navbar-expand-lg border-bottom bg-white shadow-sm">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
      
      <!-- Mobile Menu Toggle -->
      <button class="btn btn-outline-secondary d-lg-none me-3" id="mobile-menu-toggle" type="button">
        <i class="fas fa-bars"></i>
      </button>
      
      <!-- Search -->
      <form class="flex-grow-1 me-3" role="search" action="index.php" method="get">
        <input type="hidden" name="m" value="search" />
        <div class="input-group">
          <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
          <input type="text" name="q" class="form-control border-0 bg-light" placeholder="Search..." aria-label="Search" />
        </div>
      </form>
      
      <!-- Right side -->
      <ul class="navbar-nav d-flex flex-row align-items-center gap-2 ms-auto">
        <!-- New Booking Button -->
        <li class="nav-item">
          <button class="btn btn-primary" id="newBookingBtn" data-bs-toggle="modal" data-bs-target="#newBookingModal">
            + New Booking
          </button>
        </li>
        <!-- Notification / Cart Button -->
        <li class="nav-item dropdown">
          <button class="btn btn-outline-primary position-relative" id="notifyBtn" data-bs-toggle="dropdown">
            <i class="fas fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifyCount"><?= $notifyCount ?? 0 ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end p-0" style="min-width: 320px">
            <li class="px-3 py-2 fw-semibold">Notifications</li>
            <li><hr class="dropdown-divider m-0"/></li>
            <li><a class="dropdown-item d-flex align-items-start gap-2 py-2" href="#"><i class="fas fa-check-circle text-success mt-1"></i><span>Your booking #1022 was completed.</span></a></li>
            <li><a class="dropdown-item d-flex align-items-start gap-2 py-2" href="#"><i class="fas fa-receipt text-primary mt-1"></i><span>Quotation #Q-77 is ready.</span></a></li>
            <li><a class="dropdown-item d-flex align-items-start gap-2 py-2" href="#"><i class="fas fa-truck text-warning mt-1"></i><span>Materials restocked.</span></a></li>
            <li><hr class="dropdown-divider m-0"/></li>
            <li><a class="dropdown-item text-center" href="#">View all</a></li>
          </ul>
        </li>
        <!-- User Dropdown -->
        <li class="nav-item topbar-user dropdown hidden-caret">
          <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#">
            <div class="avatar-sm">
              <img src="views/assets/img/jm_denis.jpg" alt="..." class="avatar-img rounded-circle" />
            </div>
            <span class="profile-username"><span class="op-7">Hi,</span><span class="fw-bold"> Hizrian</span></span>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    <img src="views/assets/img/jm_denis.jpg" alt="profile" class="avatar-img rounded" />
                  </div>
                  <div class="u-text">
                    <h4>Hizrian</h4>
                    <p class="text-muted">hello@example.com</p>
                    <a href="#" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                  </div>
                </div>
              </li>
              <li>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="index.php?m=profile">My Profile</a>
                <a class="dropdown-item" href="index.php?m=payment">My Balance</a>
                <a class="dropdown-item" href="index.php?m=history">Service History</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="index.php?m=profile">Account Setting</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Logout</a>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</div>
