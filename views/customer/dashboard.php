<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Content starting directly as layout already provides wrappers -->
            <!-- Welcome Header Card -->
            <div class="card module-card border-bottom-0 border-top-0 border-right-0 border-left-primary mb-4 shadow-sm" style="background: white; border-radius: 12px; border-left-width: 5px !important;">
                <div class="card-body p-4">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h1 class="h3 mb-1 font-weight-bold" style="color: #0F3C5F;"><i class="fas fa-home mr-2" style="color: #0F3C5F;"></i> Welcome back, <?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?>!</h1>
                            <p class="text-muted small mb-0">Here's what's happening with your repair requests today.</p>
                        </div>
                        <button type="button" class="btn btn-new-booking shadow-sm" onclick="openReservationModal()" 
                                style="font-size: 0.85rem; padding: 0.6rem 1.5rem; border-radius: 50px; font-weight: 700;">
                            <i class="fas fa-tools mr-2"></i>Repair Reservation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="row">
                <!-- Total Bookings Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card module-card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 tracking-wider">
                                        Total Bookings</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalBookings); ?></div>
                                </div>
                                <div class="col-auto stats-icon">
                                    <div class="bg-light-blue p-3 rounded-circle">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Bookings Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card module-card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 tracking-wider">
                                        Pending Review</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800"><?php echo number_format($pendingBookings); ?></div>
                                </div>
                                <div class="col-auto stats-icon">
                                    <div class="bg-light-warning p-3 rounded-circle">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- In Progress Bookings Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card module-card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1 tracking-wider">
                                        Under Repair</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800"><?php echo number_format($inProgressBookings); ?></div>
                                </div>
                                <div class="col-auto stats-icon">
                                    <div class="bg-light-blue p-3 rounded-circle">
                                        <i class="fas fa-tools text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Spent Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card module-card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1 tracking-wider">
                                        Total Spent</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($totalSpent, 2); ?></div>
                                </div>
                                <div class="col-auto stats-icon">
                                    <div class="bg-light-success p-3 rounded-circle">
                                        <i class="fas fa-wallet text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php 
            // Feature 1: Booking Progress Tracker (Most Important)
            // Find the first active booking that isn't completed or cancelled
            $activeBooking = null;
            foreach ($recentBookings as $b) {
                if (!in_array(strtolower($b['status']), ['completed', 'cancelled'])) {
                    $activeBooking = $b;
                    break;
                }
            }

            if ($activeBooking):
                $status = strtolower($activeBooking['status']);
                $steps = [
                    'pending' => ['label' => 'Pending', 'icon' => 'fa-clock', 'percent' => 25],
                    'approved' => ['label' => 'Approved', 'icon' => 'fa-check-circle', 'percent' => 50],
                    'in_progress' => ['label' => 'Repairing', 'icon' => 'fa-tools', 'percent' => 75],
                    'completed' => ['label' => 'Ready', 'icon' => 'fa-flag-checkered', 'percent' => 100]
                ];

                // Map database status to tracker status
                $currentStep = 'pending';
                if ($status === 'approved' || $status === 'confirmed') $currentStep = 'approved';
                if ($status === 'in_progress' || $status === 'under_repair') $currentStep = 'in_progress';
                if ($status === 'completed') $currentStep = 'completed';
                
                $activePercent = $steps[$currentStep]['percent'];
            ?>
            <div class="card module-card mb-4 overflow-hidden border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="m-0 font-weight-bold text-dark">
                            <i class="fas fa-truck-fast text-primary mr-2"></i>Active Booking Tracker 
                            <span class="text-muted font-weight-normal ml-2">#<?php echo $activeBooking['booking_number']; ?></span>
                        </h6>
                        <span class="badge badge-pill bg-light-info text-info px-3 py-2 font-weight-bold">
                            <?php echo $steps[$currentStep]['label']; ?>
                        </span>
                    </div>
                    
                    <div class="progress-tracker-container mt-4 mb-2">
                        <div class="progress mb-4" style="height: 6px; border-radius: 10px; background-color: #eaecf4;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: <?php echo $activePercent; ?>%; background: linear-gradient(90deg, #0F3C5F, #3498db);" 
                                 aria-valuenow="<?php echo $activePercent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between text-center tracker-steps">
                            <?php foreach ($steps as $key => $step): 
                                $isPast = $step['percent'] <= $activePercent;
                                $isActive = $key === $currentStep;
                            ?>
                                <div class="step-item <?php echo $isPast ? 'step-completed' : ''; ?> <?php echo $isActive ? 'step-active' : ''; ?>">
                                    <div class="step-icon-wrapper mb-2 mx-auto d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                                         style="width: 35px; height: 35px; transition: all 0.3s;">
                                        <i class="fas <?php echo $step['icon']; ?> <?php echo $isPast ? 'text-white' : 'text-gray-400'; ?>" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div class="step-label text-xs font-weight-bold <?php echo $isPast ? 'text-dark' : 'text-muted'; ?>">
                                        <?php echo $step['label']; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php 
            // Feature 3: Rating Reminder (After Completed)
            // Look for the most recent completed booking
            $recentCompleted = null;
            foreach ($recentBookings as $b) {
                if (strtolower($b['status']) === 'completed') {
                    $recentCompleted = $b;
                    break;
                }
            }

            if ($recentCompleted): 
            ?>
            <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-radius: 12px; overflow: hidden;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 mr-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-star text-warning fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold m-0 text-gray-800">Rate Your Recent Service</h6>
                            <p class="text-muted smaller mb-0">Booking #<?php echo $recentCompleted['booking_number']; ?> is complete. How was it?</p>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>customer/view_booking/<?php echo $recentCompleted['id']; ?>#rating" class="btn btn-warning btn-sm rounded-pill px-4 font-weight-bold">
                        Leave Review
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="row mt-2">
                <!-- Recent Activity Table -->
                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="card module-card shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between border-0">
                            <h6 class="m-0 font-weight-bold" style="color: #0F3C5F;">Recent Activity</h6>
                            <a href="<?php echo BASE_URL; ?>customer/bookings" class="smaller font-weight-bold text-primary">View All Bookings</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-items-center mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="border-0 px-3 py-2 text-xs font-weight-bold text-muted text-uppercase tracking-wider">Booking ID</th>
                                            <th class="border-0 px-3 py-2 text-xs font-weight-bold text-muted text-uppercase tracking-wider">Service</th>
                                            <th class="border-0 px-3 py-2 text-xs font-weight-bold text-muted text-uppercase tracking-wider">Status</th>
                                            <th class="border-0 px-3 py-2 text-xs font-weight-bold text-muted text-uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentBookings)): ?>
                                            <?php foreach ($recentBookings as $booking): ?>
                                                <tr>
                                                    <td class="px-3 py-4 border-top-0">
                                                        <span class="font-weight-bold text-gray-800"><?php echo htmlspecialchars($booking['booking_number']); ?></span>
                                                    </td>
                                                    <td class="px-3 py-4 border-top-0">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bold text-gray-800"><?php echo htmlspecialchars($booking['service_name']); ?></span>
                                                            <span class="text-muted smaller"><?php echo htmlspecialchars($booking['service_type'] ?? ''); ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 border-top-0">
                                                        <?php
                                                        $status = strtolower($booking['status'] ?? 'pending');
                                                        $badgeClass = 'bg-light-warning text-warning';
                                                        $statusLabel = ucfirst($status);
                                                        
                                                        switch($status) {
                                                            case 'pending': 
                                                            case 'admin_review':
                                                                $badgeClass = 'bg-light-warning text-warning'; 
                                                                $statusLabel = 'Pending Review';
                                                                break;
                                                            case 'under_repair':
                                                            case 'in_progress':
                                                                $badgeClass = 'bg-light-blue text-info';
                                                                $statusLabel = 'In Progress';
                                                                break;
                                                            case 'completed':
                                                                $badgeClass = 'bg-light-success text-success';
                                                                $statusLabel = 'Completed';
                                                                break;
                                                            case 'cancelled':
                                                                $badgeClass = 'bg-light-danger text-danger';
                                                                $statusLabel = 'Cancelled';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge badge-pill px-3 py-2 <?php echo $badgeClass; ?> font-weight-bold" style="font-size: 0.7rem;">
                                                            <?php echo $statusLabel; ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-4 border-top-0 text-muted smaller">
                                                        <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-5 border-0">
                                                    <div class="mb-3">
                                                        <i class="fas fa-calendar-times fa-3x text-gray-200"></i>
                                                    </div>
                                                    <p class="text-muted mb-0">No recent bookings found.</p>
                                                    <a href="<?php echo BASE_URL; ?>customer/newRepairReservation" class="smaller font-weight-bold">Make your first request now</a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions / Sidebar Style -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card module-card mb-4">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="m-0 font-weight-bold" style="color: #0F3C5F;">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <a href="javascript:void(0)" onclick="openReservationModal()" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                                    <div class="bg-light-info p-2 rounded-circle mr-3">
                                        <i class="fas fa-tools text-info fa-fw"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-gray-800">Repair Reservation</div>
                                        <div class="smaller text-muted">Submit an item for restoration</div>
                                    </div>
                                </a>
                                <a href="<?php echo BASE_URL; ?>customer/fabricsCatalog" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                                    <div class="bg-light-success p-2 rounded-circle mr-3">
                                        <i class="fas fa-swatchbook text-success fa-fw"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-gray-800">Browse Fabrics</div>
                                        <div class="smaller text-muted">Explore colors and materials</div>
                                    </div>
                                </a>
                                <a href="<?php echo BASE_URL; ?>customer/storeLocations" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                                    <div class="bg-light-warning p-2 rounded-circle mr-3">
                                        <i class="fas fa-map-pin text-warning fa-fw"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-gray-800">Our Stores</div>
                                        <div class="smaller text-muted">Find the nearest UpholCare shop</div>
                                    </div>
                                </a>
                                <a href="<?php echo BASE_URL; ?>customer/profile" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                                    <div class="bg-light-danger p-2 rounded-circle mr-3">
                                        <i class="fas fa-user-gear text-danger fa-fw"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-gray-800">Account Settings</div>
                                        <div class="smaller text-muted">Manage your profile and business info</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2: Promo / Updates Card -->
                    <div class="card module-card mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
                        <div class="card-header border-0 py-3 d-flex align-items-center" style="background: #FFF9E6;">
                            <span class="mr-2">🎉</span>
                            <h6 class="m-0 font-weight-bold text-dark">Promo This Month</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="promo-item p-3 mb-2 rounded-lg" style="background-color: #f8f9fe; border-left: 4px solid #3498db;">
                                <div class="font-weight-bold text-primary small">10% OFF SOFA REPAIR</div>
                                <div class="smaller text-muted">Limited time offer for new requests</div>
                            </div>
                            <div class="promo-item p-3 rounded-lg" style="background-color: #fdfaf3; border-left: 4px solid #f1c40f;">
                                <div class="font-weight-bold text-warning small">FREE PICK-UP</div>
                                <div class="smaller text-muted">Within City limits only</div>
                            </div>
                            <button onclick="openReservationModal()" class="btn btn-primary btn-block rounded-pill mt-3 btn-sm">Book Reservation</button>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card module-card shadow-sm" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);">
                        <div class="card-body text-white">
                            <h6 class="font-weight-bold mb-3">Need Assistance?</h6>
                            <p class="smaller mb-3 opacity-75">Our team is here to help with any questions about your repair or our services.</p>
                            <a href="#" class="btn btn-light btn-sm rounded-pill px-4 text-primary font-weight-bold">Contact Support</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
    
    <!-- Repair Reservation Modal -->
    <?php require_once ROOT . DS . 'views' . DS . 'customer' . DS . 'repair_reservation_modal_wrapper.php'; ?>

<style>
.dashboard-main {
    background-color: #f8fafc !important;
    min-height: calc(100vh - 70px);
}
.dashboard-main .module-card {
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    border: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
}
.dashboard-main .module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
}
.bg-light-blue { background-color: rgba(52, 152, 219, 0.1); }
.bg-light-info { background-color: rgba(52, 115, 219, 0.1); }
.bg-light-success { background-color: rgba(46, 204, 113, 0.1); }
.bg-light-warning { background-color: rgba(241, 196, 15, 0.1); }
.bg-light-danger { background-color: rgba(231, 76, 60, 0.1); }
.opacity-75 { opacity: 0.75; }

.tracking-wider { letter-spacing: 0.05em; }
.smaller { font-size: 0.8rem; }

/* Progress Tracker Styling */
.step-item .step-icon-wrapper {
    background-color: #f8f9fc;
    border: 2px solid #eaecf4;
}
.step-item.step-completed .step-icon-wrapper {
    background-color: #0F3C5F;
    border-color: #0F3C5F;
}
.step-item.step-active .step-icon-wrapper {
    background-color: #3498db;
    border-color: #3498db;
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3) !important;
}
.btn-new-booking {
    background: #0F3C5F;
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: 50px; /* Force rounded pill */
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(15, 60, 95, 0.3);
    transition: all 0.3s ease;
}
.step-label {
    margin-top: 8px;
    letter-spacing: 0.02em;
}
.promo-item {
    transition: transform 0.2s;
}
.promo-item:hover {
    transform: scale(1.02);
}
</style>
