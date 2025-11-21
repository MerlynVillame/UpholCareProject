<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.dashboard-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-card {
    border-radius: 1rem;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.stat-card:hover {
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
    transform: translateY(-5px);
}

.stat-card-modern {
    position: relative;
    background: white;
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card-modern.card-primary::before {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
}

.stat-card-modern.card-warning::before {
    background: linear-gradient(90deg, #f6c23e 0%, #ffa502 100%);
}

.stat-card-modern.card-info::before {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
}

.stat-card-modern.card-success::before {
    background: linear-gradient(90deg, #1cc88a 0%, #00b894 100%);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
}

.stat-icon-primary {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
}

.stat-icon-warning {
    background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
}

.stat-icon-info {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
}

.stat-icon-success {
    background: linear-gradient(135deg, #1cc88a 0%, #00b894 100%);
}

.stat-value {
    font-size: 2.25rem;
    font-weight: 800;
    color: #2c3e50;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #858796;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.stat-change.positive {
    color: #1cc88a;
}

.stat-change.negative {
    color: #e74c3c;
}

.btn-new-booking {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(139, 69, 19, 0.4);
}

.btn-new-booking:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 69, 19, 0.5);
    color: white;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card {
    animation: fadeIn 0.5s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Override Bootstrap primary colors with brown */
.btn-primary,
.btn-info {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-primary:hover,
.btn-info:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
    border-color: #A0522D !important;
    color: white !important;
}

.text-primary {
    color: #8B4513 !important;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="dashboard-title mb-2">Dashboard</h1>
        <p class="text-muted mb-0">Welcome back, <strong><?php echo htmlspecialchars($user['name']); ?></strong>!</p>
    </div>
    <a href="<?php echo BASE_URL; ?>customer/newRepairReservation" class="btn btn-new-booking">
        <i class="fas fa-tools mr-2"></i>Repair Reservation
    </a>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Bookings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-primary shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-icon stat-icon-primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $totalBookings; ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up mr-1"></i>12% from last month
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Bookings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-warning shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Pending</div>
                    <div class="stat-icon stat-icon-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $pendingBookings; ?></div>
                <div class="stat-change">
                    <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i>Awaiting confirmation
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-info shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-icon stat-icon-info">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $inProgressBookings; ?></div>
                <div class="stat-change">
                    <i class="fas fa-circle mr-1" style="font-size: 0.5rem; color: #8B4513;"></i>Currently working
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-success shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Completed</div>
                    <div class="stat-icon stat-icon-success">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $completedBookings; ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-check-circle mr-1"></i>Successfully finished
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- My Recent Bookings -->
    <div class="col-lg-8 mb-4">
        <div class="card stat-card stat-card-modern card-primary shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: white; border-bottom: 1px solid #e3e6f0;">
                <h6 class="m-0 font-weight-bold" style="color: #2c3e50;">
                    <i class="fas fa-list-alt mr-2" style="color: #8B4513;"></i>My Recent Bookings
                </h6>
                <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-sm btn-new-booking" style="padding: 0.4rem 1rem;">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recentBookings)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td><strong class="text-primary"><?php echo htmlspecialchars($booking['booking_number'] ?? 'N/A'); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['service_name'] ?? 'Unknown Service'); ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'badge-' . str_replace('_', '-', $booking['status'] ?? 'pending');
                                    $statusText = ucwords(str_replace('_', ' ', $booking['status'] ?? 'pending'));
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $booking['id']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No bookings yet</h5>
                    <p class="text-muted mb-3">Start by creating your first booking</p>
                    <a href="<?php echo BASE_URL; ?>customer/newRepairReservation" class="btn btn-new-booking">
                        <i class="fas fa-tools mr-2"></i>Repair Reservation
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Our Services -->
    <div class="col-lg-4 mb-4">
        <div class="card stat-card stat-card-modern card-success shadow mb-4">
            <div class="card-header py-3" style="background: white; border-bottom: 1px solid #e3e6f0;">
                <h6 class="m-0 font-weight-bold" style="color: #2c3e50;">
                    <i class="fas fa-concierge-bell mr-2" style="color: #1cc88a;"></i>Our Services
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3 p-2" style="background: #f8f9fc; border-radius: 0.5rem;">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); 
                                    border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-car"></i>
                        </div>
                        <span class="font-weight-bold ml-2">Vehicle Covers</span>
                    </div>
                    <p class="small text-muted mb-0">Repair and restoration services</p>
                </div>
                <div class="mb-3 p-2" style="background: #f8f9fc; border-radius: 0.5rem;">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); 
                                    border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-bed"></i>
                        </div>
                        <span class="font-weight-bold ml-2">Bedding</span>
                    </div>
                    <p class="small text-muted mb-0">Professional restoration</p>
                </div>
                <div class="mb-0 p-2" style="background: #f8f9fc; border-radius: 0.5rem;">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); 
                                    border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-couch"></i>
                        </div>
                        <span class="font-weight-bold ml-2">Furniture</span>
                    </div>
                    <p class="small text-muted mb-0">Expert reupholstering</p>
                </div>
            </div>
        </div>
    </div>

</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

