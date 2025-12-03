<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
}

.breadcrumb-custom {
    background: transparent;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb-custom .breadcrumb-item a {
    color: #8B4513;
    text-decoration: none;
}

.btn-new-booking {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: 0.35rem;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-new-booking:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.filter-dropdown {
    min-width: 200px;
    border-radius: 0.35rem;
    border-color: #d1d3e2;
}

.btn-business-mode {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    border: none;
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 0.35rem;
    font-weight: 500;
}

.btn-client-mode {
    background-color: #9b59b6;
    border: none;
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 0.35rem;
    font-weight: 500;
}

.booking-card {
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

.booking-card .card-header {
    background-color: white;
    border-bottom: 1px solid #e3e6f0;
    padding: 1.5rem;
}

.table-bookings {
    margin-bottom: 0;
}

.table-bookings thead th {
    border-top: none;
    border-bottom: 2px solid #e3e6f0;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #858796;
    letter-spacing: 0.5px;
    padding: 1rem;
}

.table-bookings tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e3e6f0;
}

.badge-status {
    padding: 0.5rem 0.875rem;
    border-radius: 0.35rem;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    white-space: nowrap;
    min-width: 100px;
    justify-content: center;
}

/* Booking Status Badges - Enhanced Visibility with Clear Text */
.badge-pending, .badge-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
    border: 1px solid #ff9800 !important;
}

.badge-approved, .badge-accepted, .badge-confirmed, .badge-success {
    background-color: #28a745 !important;
    color: #fff !important;
    border: 1px solid #1e7e34 !important;
}

.badge-in-progress, .badge-ongoing, .badge-primary {
    background-color: #007bff !important;
    color: #fff !important;
    border: 1px solid #0056b3 !important;
}

.badge-in-queue, .badge-info {
    background-color: #17a2b8 !important;
    color: #fff !important;
    border: 1px solid #117a8b !important;
}

.badge-under-repair {
    background-color: #6f42c1 !important;
    color: #fff !important;
    border: 1px solid #5a32a3 !important;
}

.badge-for-quality-check {
    background-color: #20c997 !important;
    color: #fff !important;
    border: 1px solid #1aa179 !important;
}

.badge-ready-for-pickup, .badge-for-pickup {
    background-color: #17a2b8 !important;
    color: #fff !important;
    border: 1px solid #117a8b !important;
}

.badge-out-for-delivery {
    background-color: #ffc107 !important;
    color: #000 !important;
    border: 1px solid #ff9800 !important;
}

.badge-completed {
    background-color: #28a745 !important;
    color: #fff !important;
    border: 1px solid #1e7e34 !important;
}

.badge-rejected, .badge-declined, .badge-danger {
    background-color: #dc3545 !important;
    color: #fff !important;
    border: 1px solid #c82333 !important;
}

.badge-cancelled, .badge-secondary {
    background-color: #6c757d !important;
    color: #fff !important;
    border: 1px solid #545b62 !important;
}

.btn-action {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem !important;
    line-height: 1.2 !important;
    min-width: 28px !important;
    height: 28px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.btn-action i {
    font-size: 0.75rem !important;
    margin: 0 !important;
}
    padding: 0.375rem 0.75rem;
    font-size: 0.85rem;
    border-radius: 0.25rem;
}

.no-bookings {
    text-align: center;
    padding: 3rem 1rem;
    color: #858796;
}

.no-bookings i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d3e2;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="page-title mb-2">Reservations</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reservations</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-primary" onclick="window.location.href='<?php echo BASE_URL; ?>customer/newBooking'">
            <i class="fas fa-arrow-left mr-1"></i>
        </button>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Reservations Card -->
<div class="card booking-card">
    <!-- Colored top bar -->
    <div style="height: 4px; background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);"></div>
    
    <div class="card-header">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #2c3e50;">
                    <i class="fas fa-calendar-check mr-2" style="color: #8B4513;"></i>My Reservations
                </h3>
            </div>
            <!-- <div class="col-md-6 text-md-right">
                <button class="btn btn-new-booking" onclick="window.location.href='<?php echo BASE_URL; ?>customer/newBooking'" style="margin-right: 0.5rem;">
                    <i class="fas fa-plus mr-2"></i>New Reservation
                </button> -->
                <button class="btn btn-new-booking" onclick="window.location.href='<?php echo BASE_URL; ?>customer/newRepairReservation'" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);">
                    <i class="fas fa-tools mr-2"></i>Repair Reservation
                </button>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <div class="d-flex align-items-center">
                    <label for="statusFilterDropdown" style="margin-right: 12px; font-weight: 600; color: #5a5c69; white-space: nowrap;">
                        Filter by Status:
                    </label>
                    <select id="statusFilterDropdown" class="form-control filter-dropdown" onchange="filterBookings(this.value)">
                        <option value="all" <?php echo ($currentStatus === 'all') ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo ($currentStatus === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo ($currentStatus === 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="in_queue" <?php echo ($currentStatus === 'in_queue') ? 'selected' : ''; ?>>In Queue</option>
                        <option value="under_repair" <?php echo ($currentStatus === 'under_repair') ? 'selected' : ''; ?>>Under Repair</option>
                        <option value="for_quality_check" <?php echo ($currentStatus === 'for_quality_check') ? 'selected' : ''; ?>>For Quality Check</option>
                        <option value="ready_for_pickup" <?php echo ($currentStatus === 'ready_for_pickup') ? 'selected' : ''; ?>>Ready for Pickup</option>
                        <option value="out_for_delivery" <?php echo ($currentStatus === 'out_for_delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
                        <option value="completed" <?php echo ($currentStatus === 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo ($currentStatus === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <label style="margin-left: 20px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                        <input type="checkbox" id="selectAllReservations" style="margin-right: 6px; vertical-align: middle;" />
                        Select All
                    </label>
                </div>
            </div>
            <div class="col-md-6 text-md-right">
                <!-- Action dropdown removed as requested -->
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($bookings)): ?>
        <div class="table-responsive">
            <table class="table table-bookings" id="bookingsDataTable">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th style="width: 40px;"></th>
                        <th>SERVICE</th>
                        <th>SERVICE OPTION</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $index => $booking): ?>
                    <tr class="booking-row" data-booking-id="<?php echo $booking['id']; ?>">
                        <td>
                            <input type="checkbox" class="booking-checkbox" value="<?php echo $booking['id']; ?>">
                        </td>
                        <td>
                            <?php 
                            // Priority flag based on status
                            $flagColors = [
                                'pending' => '#A0522D',
                                'approved' => '#8B4513',
                                'in_queue' => '#6c757d',
                                'under_repair' => '#654321',
                                'for_quality_check' => '#17a2b8',
                                'ready_for_pickup' => '#74b9ff',
                                'out_for_delivery' => '#f39c12',
                                'completed' => '#00b894',
                                'cancelled' => '#95a5a6',
                                // Legacy statuses (for backward compatibility)
                                'accepted' => '#8B4513',
                                'confirmed' => '#8B4513',
                                'in_progress' => '#654321',
                                'ongoing' => '#654321',
                                'for_pickup' => '#74b9ff',
                                'rejected' => '#e74c3c',
                                'declined' => '#e74c3c'
                            ];
                            $flagColor = $flagColors[$booking['status']] ?? '#8B4513';
                            ?>
                            <i class="fas fa-flag" style="color: <?php echo $flagColor; ?>;" title="Priority: <?php echo ucfirst($booking['status']); ?>"></i>
                        </td>
                        <td>
                            <div>
                                <strong style="color: #2c3e50;"><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                                <div class="small text-muted">
                                    <i class="fas fa-tag fa-sm mr-1"></i>
                                    <?php echo htmlspecialchars($booking['category_name'] ?? 'General'); ?>
                                    <?php if (!empty($booking['service_type'])): ?>
                                    <span class="badge badge-info ml-1" style="font-size: 0.7rem;">
                                        <?php echo htmlspecialchars($booking['service_type']); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            // Service Option mapping with icons and colors
                            $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                            $serviceOptionConfig = [
                                'pickup' => ['class' => 'badge-primary', 'icon' => 'truck-loading', 'text' => 'Pick Up'],
                                'delivery' => ['class' => 'badge-info', 'icon' => 'truck', 'text' => 'Delivery'],
                                'both' => ['class' => 'badge-success', 'icon' => 'exchange-alt', 'text' => 'Both'],
                                'walk_in' => ['class' => 'badge-warning', 'icon' => 'walking', 'text' => 'Walk In']
                            ];
                            $optionConfig = $serviceOptionConfig[$serviceOption] ?? ['class' => 'badge-secondary', 'icon' => 'question', 'text' => ucfirst($serviceOption)];
                            ?>
                            <span class="badge <?php echo $optionConfig['class']; ?>" style="font-weight: 600; font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                                <i class="fas fa-<?php echo $optionConfig['icon']; ?> mr-1"></i>
                                <?php echo htmlspecialchars($optionConfig['text']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></div>
                            <div class="small text-muted">
                                <i class="far fa-clock fa-sm mr-1"></i>
                                <?php echo date('h:i A', strtotime($booking['created_at'])); ?>
                            </div>
                        </td>
                        <td class="status-cell" data-booking-status="<?php echo htmlspecialchars($booking['status'] ?? 'pending'); ?>">
                            <?php
                            // Status mapping based on new booking status flow including PICKUP workflow
                            $statusConfig = [
                                'pending' => ['class' => 'badge-warning', 'icon' => 'clock', 'text' => 'Pending'],
                                // PICKUP workflow statuses
                                'for_pickup' => ['class' => 'badge-info', 'icon' => 'truck-loading', 'text' => 'For Pickup'],
                                'picked_up' => ['class' => 'badge-info', 'icon' => 'box-open', 'text' => 'Picked Up'],
                                'for_inspection' => ['class' => 'badge-info', 'icon' => 'search', 'text' => 'For Inspection'],
                                // 'for_quotation' status removed
                                // Work in progress statuses
                                'approved' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Approved'],
                                'in_queue' => ['class' => 'badge-info', 'icon' => 'list', 'text' => 'In Queue'],
                                'in_progress' => ['class' => 'badge-primary', 'icon' => 'spinner', 'text' => 'In Progress'],
                                'under_repair' => ['class' => 'badge-primary', 'icon' => 'tools', 'text' => 'Under Repair'],
                                'for_quality_check' => ['class' => 'badge-info', 'icon' => 'search', 'text' => 'For Quality Check'],
                                // Completion statuses
                                'ready_for_pickup' => ['class' => 'badge-success', 'icon' => 'box', 'text' => 'Ready for Pickup'],
                                'out_for_delivery' => ['class' => 'badge-warning', 'icon' => 'truck', 'text' => 'Out for Delivery'],
                                'completed' => ['class' => 'badge-success', 'icon' => 'check-double', 'text' => 'Completed'],
                                'delivered_and_paid' => ['class' => 'badge-success', 'icon' => 'check-double', 'text' => 'Delivered and Paid'],
                                'paid' => ['class' => 'badge-success', 'icon' => 'money-bill-wave', 'text' => 'Paid'],
                                'closed' => ['class' => 'badge-dark', 'icon' => 'archive', 'text' => 'Closed'],
                                'cancelled' => ['class' => 'badge-secondary', 'icon' => 'ban', 'text' => 'Cancelled'],
                                // Legacy statuses (for backward compatibility)
                                'accepted' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Approved'],
                                'confirmed' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Approved'],
                                'ongoing' => ['class' => 'badge-primary', 'icon' => 'spinner', 'text' => 'Under Repair'],
                                'for_pickup' => ['class' => 'badge-success', 'icon' => 'box', 'text' => 'Ready for Pickup'],
                                'rejected' => ['class' => 'badge-danger', 'icon' => 'times-circle', 'text' => 'Rejected'],
                                'declined' => ['class' => 'badge-danger', 'icon' => 'times-circle', 'text' => 'Declined'],
                                'admin_review' => ['class' => 'badge-warning', 'icon' => 'eye', 'text' => 'Admin Review']
                            ];
                            
                            // Get status and handle NULL/empty values
                            $status = trim(strtolower($booking['status'] ?? ''));
                            
                            // If status is empty, NULL, or whitespace, default to 'pending'
                            if (empty($status) || $status === '' || $status === null) {
                                $status = 'pending';
                            }
                            
                            // Normalize status (handle case variations)
                            $status = strtolower(trim($status));
                            
                            // Get config or use default
                            $config = $statusConfig[$status] ?? null;
                            
                            // If status not found in config, try to create a readable label
                            if (!$config) {
                                // Try to format unknown status
                                $formattedStatus = ucwords(str_replace(['_', '-'], ' ', $status));
                                $config = [
                                    'class' => 'badge-secondary',
                                    'icon' => 'circle',
                                    'text' => $formattedStatus ?: 'Pending'
                                ];
                            }
                            
                            // Show payment status if completed
                            if ($status === 'completed') {
                                $paymentStatus = strtolower(trim($booking['payment_status'] ?? 'unpaid'));
                                if (in_array($paymentStatus, ['paid', 'paid_full_cash'])) {
                                    // Full cash paid before repair
                                    $config['text'] = 'Completed (Paid)';
                                    $config['class'] = 'badge-success';
                                } else {
                                    // COD - unpaid until payment received
                                    $config['text'] = 'Completed (Unpaid)';
                                    $config['class'] = 'badge-warning';
                                }
                            } elseif ($status === 'delivered_and_paid') {
                                // Delivered and Paid status (COD after payment received)
                                $config['text'] = 'Delivered and Paid';
                                $config['class'] = 'badge-success';
                            }
                            
                            // Ensure text is never empty
                            if (empty($config['text'])) {
                                $config['text'] = 'Pending';
                                $config['class'] = 'badge-warning';
                                $config['icon'] = 'clock';
                            }
                            ?>
                            <span class="badge badge-status <?php echo htmlspecialchars($config['class']); ?>" style="font-size: 0.85rem !important; font-weight: 600 !important; padding: 0.5rem 0.875rem !important; display: inline-flex !important; align-items: center !important; white-space: nowrap !important;">
                                <i class="fas fa-<?php echo htmlspecialchars($config['icon']); ?> mr-1"></i>
                                <strong><?php echo htmlspecialchars($config['text']); ?></strong>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group" style="flex-wrap: wrap; gap: 3px;">
                                <?php
                                $status = strtolower($booking['status'] ?? 'pending');
                                $bookingId = $booking['id'];
                                $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                                
                                // Always show View Details button
                                ?>
                                <button type="button" 
                                        class="btn btn-sm btn-info btn-action" 
                                        onclick="viewReservationDetails(<?php echo $bookingId; ?>)" 
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <?php
                                // Show Update button for pending, approved, in_queue, and under_repair
                                if (in_array($status, ['pending', 'approved', 'in_queue', 'under_repair'])): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-warning btn-action" 
                                            onclick="openUpdateBookingModal(<?php echo $bookingId; ?>)" 
                                            title="Update Booking">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <?php
                                // Show Cancel button only for pending and approved
                                if (in_array($status, ['pending', 'approved'])): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger btn-action" 
                                            onclick="confirmCancelReservation(<?php echo $bookingId; ?>)" 
                                            title="Cancel Request">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-bookings">
            <i class="fas fa-calendar-times"></i>
            <h5>No reservations yet</h5>
            <p class="text-muted">Start by creating your first reservation</p>
            <a href="<?php echo BASE_URL; ?>customer/newBooking" class="btn btn-new-booking">
                <i class="fas fa-plus mr-2"></i>Create New Reservation
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Repair Reservations Section -->
        <?php if (!empty($repairReservations)): ?>
        <div class="card booking-card mt-4">
            <div style="height: 4px; background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);"></div>
            <div class="card-header">
                <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #2c3e50;">
                    <i class="fas fa-tools mr-2" style="color: #8B4513;"></i>Repair Reservations
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bookings">
                        <thead>
                            <tr>
                                <th>ITEM NAME</th>
                                <th>URGENCY</th>
                                <th>STATUS</th>
                                <th>DATE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($repairReservations as $reservation): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($reservation['item_name']); ?></strong></td>
                                <td>
                                    <?php
                                    $urgencyClass = [
                                        'low' => 'badge-secondary',
                                        'normal' => 'badge-info',
                                        'high' => 'badge-warning',
                                        'urgent' => 'badge-danger'
                                    ][$reservation['urgency']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $urgencyClass; ?>"><?php echo ucfirst($reservation['urgency']); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'pending' => 'badge-warning',
                                        'approved' => 'badge-success',
                                        'in_progress' => 'badge-primary',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-danger'
                                    ][$reservation['status']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($reservation['status']); ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($reservation['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group" style="flex-wrap: wrap; gap: 3px;">
                                        <?php
                                        $repairStatus = strtolower($reservation['status'] ?? 'pending');
                                        $repairId = $reservation['id'];
                                        $repairPaymentStatus = strtolower($reservation['payment_status'] ?? 'unpaid');
                                        ?>
                                        
                                        <!-- Always show View Details -->
                                        <a href="<?php echo BASE_URL; ?>customer/viewRepairReservation/<?php echo $repairId; ?>" 
                                           class="btn btn-sm btn-info btn-action" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php
                                        // 1️⃣ Pending / Submitted Repair Reservations
                                        if (in_array($repairStatus, ['pending', 'submitted'])): ?>
                                            <a href="<?php echo BASE_URL; ?>customer/editRepairReservation/<?php echo $repairId; ?>" 
                                               class="btn btn-sm btn-primary btn-action" 
                                               title="Edit Reservation">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>customer/rescheduleRepair/<?php echo $repairId; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Reschedule">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-secondary btn-action" 
                                                    onclick="uploadPhotos(<?php echo $repairId; ?>)" 
                                                    title="Upload Photos">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger btn-action" 
                                                    onclick="confirmCancelRepair(<?php echo $repairId; ?>)" 
                                                    title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        
                                        <?php
                                        // 2️⃣ Approved / Accepted Repair Reservations
                                        elseif (in_array($repairStatus, ['approved', 'accepted', 'confirmed'])): ?>
                                            <?php if ($repairPaymentStatus !== 'paid'): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-success btn-action" 
                                                    onclick="payNow(<?php echo $repairId; ?>)" 
                                                    title="Pay Now">
                                                <i class="fas fa-credit-card"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning btn-action" 
                                                    onclick="requestReschedule(<?php echo $repairId; ?>)" 
                                                    title="Request Reschedule">
                                                <i class="fas fa-calendar-check"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-secondary btn-action" 
                                                    onclick="uploadPhotos(<?php echo $repairId; ?>)" 
                                                    title="Upload Photos">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger btn-action" 
                                                    onclick="requestCancel(<?php echo $repairId; ?>)" 
                                                    title="Request Cancel">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        
                                        <?php
                                        // 3️⃣ Ongoing / In Progress Repair
                                        elseif (in_array($repairStatus, ['in_progress', 'ongoing'])): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary btn-action" 
                                                    onclick="trackProgress(<?php echo $repairId; ?>)" 
                                                    title="Track Progress">
                                                <i class="fas fa-tasks"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-info btn-action" 
                                                    onclick="messageStore(<?php echo $repairId; ?>)" 
                                                    title="Message Store">
                                                <i class="fas fa-comments"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-secondary btn-action" 
                                                    onclick="uploadPhotos(<?php echo $repairId; ?>)" 
                                                    title="Upload Photos">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                        
                                        <?php
                                        // 4️⃣ & 5️⃣ Completed Repair
                                        elseif ($repairStatus === 'completed'): ?>
                                            <?php if ($repairPaymentStatus === 'paid'): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-success btn-action" 
                                                        onclick="viewRepairReceipt(<?php echo $repairId; ?>)" 
                                                        title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary btn-action" 
                                                        onclick="bookAgain(<?php echo $repairId; ?>)" 
                                                        title="Book Again">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning btn-action" 
                                                        onclick="rateService(<?php echo $repairId; ?>)" 
                                                        title="Rate & Feedback">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-success btn-action" 
                                                        onclick="payNow(<?php echo $repairId; ?>)" 
                                                        title="Pay Now">
                                                    <i class="fas fa-credit-card"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary btn-action" 
                                                        onclick="viewRepairReceipt(<?php echo $repairId; ?>)" 
                                                        title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-info btn-action" 
                                                        onclick="bookAgain(<?php echo $repairId; ?>)" 
                                                        title="Book Again">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning btn-action" 
                                                        onclick="rateService(<?php echo $repairId; ?>)" 
                                                        title="Rate Service">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            <?php endif; ?>
                                        
                                        <?php
                                        // 6️⃣ Cancelled Repair
                                        elseif ($repairStatus === 'cancelled'): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary btn-action" 
                                                    onclick="bookAgain(<?php echo $repairId; ?>)" 
                                                    title="Book Again">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Cancel Reservation Confirmation Modal -->
<div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-labelledby="cancelReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="cancelReservationModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Cancel Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-times-circle" style="font-size: 4rem; color: #e74c3c;"></i>
                </div>
                <h5 style="color: #2c3e50; font-weight: 700;">Are you sure?</h5>
                <p class="text-muted mb-4">
                    Do you want to cancel this reservation? This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-arrow-left mr-1"></i>No, Keep It
                </button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-check mr-1"></i>Yes, Cancel Reservation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update Booking Modal -->
<div class="modal fade" id="updateBookingModal" tabindex="-1" aria-labelledby="updateBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #8B4513 0%, #654321 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="updateBookingModalLabel">
                    <i class="fas fa-edit mr-2"></i>Update Booking Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4">
                <form id="updateBookingForm">
                    <input type="hidden" id="update_booking_id" name="booking_id">
                    
                    <div class="form-group">
                        <label for="update_service_option"><strong>Service Option</strong></label>
                        <select class="form-control" id="update_service_option" name="service_option" required>
                            <option value="pickup">Pickup</option>
                            <option value="delivery">Delivery</option>
                            <option value="both">Both (Pickup & Delivery)</option>
                            <option value="walk_in">Walk In</option>
                        </select>
                        <small class="form-text text-muted">Select how you want to receive the service</small>
                    </div>
                    
                    <div class="form-group" id="update_pickup_date_group">
                        <label for="update_pickup_date"><strong>Pickup Date</strong></label>
                        <input type="date" class="form-control" id="update_pickup_date" name="pickup_date">
                        <small class="form-text text-muted">Preferred date for pickup</small>
                    </div>
                    
                    <div class="form-group" id="update_delivery_date_group">
                        <label for="update_delivery_date"><strong>Delivery Date</strong></label>
                        <input type="date" class="form-control" id="update_delivery_date" name="delivery_date">
                        <small class="form-text text-muted">Preferred date for delivery</small>
                    </div>
                    
                    <div class="form-group" id="update_pickup_address_group" style="display: none;">
                        <label for="update_pickup_address"><strong>Pickup Address</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="update_pickup_address" name="pickup_address" rows="3" placeholder="Enter your pickup address"></textarea>
                        <small class="form-text text-muted">Where should we pick up your item?</small>
                    </div>
                    
                    <div class="form-group" id="update_delivery_address_group" style="display: none;">
                        <label for="update_delivery_address"><strong>Delivery Address</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="update_delivery_address" name="delivery_address" rows="3" placeholder="Enter your delivery address"></textarea>
                        <small class="form-text text-muted">Where should we deliver your item?</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="update_booking_submit_btn" onclick="submitUpdateBooking()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-save mr-1"></i>Update Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle" style="font-size: 5rem; color: #28a745;"></i>
                </div>
                <h4 class="mb-3" style="color: #2c3e50; font-weight: 700;">Success!</h4>
                <p class="text-muted mb-4" id="successMessage">
                    Your action has been completed successfully.
                </p>
                <button type="button" class="btn btn-success" data-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 2rem; font-weight: 600;">
                    <i class="fas fa-thumbs-up mr-2"></i>Got It!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Confirmation Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="bulkActionModalLabel">
                    <i class="fas fa-tasks mr-2"></i>Confirm Bulk Action
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-question-circle" style="font-size: 4rem; color: #8B4513;"></i>
                </div>
                <p class="text-muted mb-4" id="bulkActionMessage">
                    Are you sure you want to perform this action on <strong id="bulkActionCount">0</strong> selected reservation(s)?
                </p>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmBulkActionBtn" style="border-radius: 8px; padding: 0.5rem 1.5rem; background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); border: none;">
                    <i class="fas fa-check mr-1"></i>Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced checkbox styling */
.booking-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.booking-row:hover {
    background-color: rgba(139, 69, 19, 0.05);
}

.booking-row.selected {
    background-color: rgba(139, 69, 19, 0.1);
}

/* Modal animations */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}

/* Fallback styles if Bootstrap JS isn't present */
.modal { z-index: 1055; }
.modal.show { display: block; }
.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); }

/* Print styles for booking details modal */
@media print {
    body * { visibility: hidden; }
    #bookingDetailsModal, #bookingDetailsModal * { visibility: visible; }
    #bookingDetailsModal {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        border: none;
    }
    #bookingDetailsModal .modal-dialog {
        max-width: 100%;
        margin: 0;
    }
    #bookingDetailsModal .modal-header .close,
    #bookingDetailsModal .modal-footer {
        display: none !important;
    }
}

/* Override Bootstrap primary colors with brown */
.btn-primary {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
    border-color: #A0522D !important;
    color: white !important;
}

.btn-info {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-info:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
    border-color: #A0522D !important;
    color: white !important;
}

.text-primary {
    color: #8B4513 !important;
}
</style>

<script>
// Show success/error messages
<?php if (isset($_SESSION['success'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    showNotification('success', '<?php echo addslashes($_SESSION['success']); ?>');
});
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    showNotification('error', '<?php echo addslashes($_SESSION['error']); ?>');
});
<?php unset($_SESSION['error']); endif; ?>

// Select All Reservations
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') {
        // Skip jQuery-dependent UX wiring when jQuery isn't present
        return;
    }
    $('#selectAllReservations').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.booking-checkbox').prop('checked', isChecked);
        
        // Toggle row highlighting
        if (isChecked) {
            $('.booking-row').addClass('selected');
        } else {
            $('.booking-row').removeClass('selected');
        }
    });
    
    // Individual checkbox
    $('.booking-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.booking-row').addClass('selected');
        } else {
            $(this).closest('.booking-row').removeClass('selected');
            $('#selectAllReservations').prop('checked', false);
        }
        
        // Check if all are selected
        const total = $('.booking-checkbox').length;
        const checked = $('.booking-checkbox:checked').length;
        if (total === checked && total > 0) {
            $('#selectAllReservations').prop('checked', true);
        }
    });
    
    // Bulk Action Handler
    $('#bulkActionDropdown').on('change', function() {
        const action = $(this).val();
        const selectedReservations = $('.booking-checkbox:checked');
        
        if (!action) return;
        
        if (selectedReservations.length === 0) {
            showNotification('warning', 'Please select at least one reservation');
            $(this).val('');
            return;
        }
        
        const count = selectedReservations.length;
        $('#bulkActionCount').text(count);
        
        if (action === 'cancel') {
            $('#bulkActionMessage').html(`Are you sure you want to <strong class="text-danger">CANCEL</strong> ${count} selected reservation(s)?`);
        } else if (action === 'print') {
            $('#bulkActionMessage').html(`Generate receipt for ${count} selected reservation(s)?`);
        } else {
            $('#bulkActionMessage').html(`Perform action on ${count} selected reservation(s)?`);
        }
        
        $('#bulkActionModal').modal('show');
        $(this).val('');
    });
    
    // Confirm Bulk Action
    $('#confirmBulkActionBtn').on('click', function() {
        const selectedIds = [];
        $('.booking-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        // Here you would make an AJAX call to process the bulk action
        // console.log('Processing bulk action for IDs:', selectedIds);
        
        $('#bulkActionModal').modal('hide');
        
        // Show success
        setTimeout(function() {
            $('#successMessage').text('Bulk action completed successfully!');
            $('#successModal').modal('show');
        }, 300);
    });
});

// Confirm Cancel Reservation Function
let reservationToCancel = null;

function confirmCancelReservation(reservationId) {
    reservationToCancel = reservationId;
    if (typeof jQuery !== 'undefined') {
        jQuery('#cancelReservationModal').modal('show');
    } else {
        const modalEl = document.getElementById('cancelReservationModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }
}

// Confirm Cancel Button
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('confirmCancelBtn');
    if (btn) {
        btn.addEventListener('click', function() {
            if (reservationToCancel) {
                window.location.href = '<?php echo BASE_URL; ?>customer/cancelBooking/' + reservationToCancel;
            }
        });
    }
});

// View Repair Receipt
function viewRepairReceipt(repairItemId) {
    fetch('<?php echo BASE_URL; ?>customer/getRepairReservationDetails/' + repairItemId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                populateReceiptModal(item);
                if (typeof jQuery !== 'undefined') {
                    jQuery('#repairReceiptModal').modal('show');
                } else {
                    const modalEl = document.getElementById('repairReceiptModal');
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                }
            } else {
                showNotification('error', data.message || 'Failed to load receipt details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred while loading the receipt.');
        });
}

// Populate Receipt Modal
function populateReceiptModal(item) {
    // Booking number removed - no longer displayed
    document.getElementById('receipt_item_name').textContent = item.item_name;
    document.getElementById('receipt_description').textContent = item.item_description;
    document.getElementById('receipt_urgency').textContent = item.urgency ? item.urgency.charAt(0).toUpperCase() + item.urgency.slice(1) : 'Normal';
    document.getElementById('receipt_status').textContent = item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : 'Pending';
    document.getElementById('receipt_estimated_cost').textContent = item.estimated_cost ? '₱' + parseFloat(item.estimated_cost).toFixed(2) : 'To be determined';
    document.getElementById('receipt_date').textContent = new Date(item.created_at).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    document.getElementById('receipt_customer_name').textContent = item.customer_name || 'N/A';
    document.getElementById('receipt_customer_email').textContent = item.email || 'N/A';
    document.getElementById('receipt_customer_phone').textContent = item.phone || 'N/A';
}

// View Reservation Details (Modal)
function viewReservationDetails(reservationId) {
    // Show loading state in modal
    const modalEl = document.getElementById('bookingDetailsModal');
    if (!modalEl) {
        console.error('Reservation details modal not found!');
        alert('Modal element not found. Please refresh the page.');
        return;
    }
    
    // Ensure modal is attached to <body> to avoid stacking/overflow issues
    try {
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
    } catch (_) {}

    // Show modal immediately with loading state (robust to missing libs)
    try {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal === 'function') {
            jQuery('#bookingDetailsModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            // Fallback: force visible
            document.body.classList.add('modal-open');
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.style.zIndex = '1055';
            // Add basic backdrop
            let backdrop = document.getElementById('temp-modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'temp-modal-backdrop';
                backdrop.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1050;';
                document.body.appendChild(backdrop);
            }
        }
    } catch (e) {
        console.error('Modal show error:', e);
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.style.zIndex = '1055';
    }
    
    // Set loading message
    const modalBody = modalEl.querySelector('.modal-body');
    if (modalBody) {
        const originalContent = modalBody.innerHTML;
        modalBody.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x mb-3" style="color: #8B4513;"></i><p>Loading reservation details...</p></div>';
        
        // Fetch reservation details
        fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + reservationId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Restore modal body content first, then populate
                    modalBody.innerHTML = originalContent;
                    populateBookingDetailsModal(data.data);
                } else {
                    modalBody.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed to load reservation details.') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = '<div class="alert alert-danger">An error occurred while loading reservation details.</div>';
            });
    } else {
        // Fallback if modal body not found
        fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + reservationId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateBookingDetailsModal(data.data);
                    if (typeof jQuery !== 'undefined') {
                        jQuery('#bookingDetailsModal').modal('show');
                    } else {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                } else {
                    alert(data.message || 'Failed to load reservation details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading reservation details.');
            });
    }
}

function populateBookingDetailsModal(b) {
    function setText(id, value) {
        var el = document.getElementById(id);
        if (el) el.textContent = value;
        else console.warn('Element not found:', id);
    }
    
    function setHTML(id, value) {
        var el = document.getElementById(id);
        if (el) el.innerHTML = value;
        else console.warn('Element not found:', id);
    }
    
    // Format status with badge
    function formatStatus(status) {
        status = (status || 'pending').replace(/_/g, ' ');
        var statusClass = 'badge-success';
        if (status.toLowerCase() === 'pending') statusClass = 'badge-warning';
        else if (status.toLowerCase() === 'cancelled') statusClass = 'badge-danger';
        else if (status.toLowerCase().includes('progress')) statusClass = 'badge-primary';
        return '<span class="badge ' + statusClass + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    }
    
    // Format payment status with badge
    function formatPaymentStatus(status) {
        status = (status || 'unpaid').toLowerCase();
        var statusClass = 'badge-danger';
        var statusText = 'Unpaid';
        
        if (status === 'paid' || status === 'paid_full_cash') {
            statusClass = 'badge-success';
            statusText = 'Paid (Full Cash)';
        } else if (status === 'paid_on_delivery_cod') {
            statusClass = 'badge-success';
            statusText = 'Paid on Delivery (COD)';
        } else if (status === 'partial') {
            statusClass = 'badge-warning';
            statusText = 'Partial';
        } else if (status === 'cancelled') {
            statusClass = 'badge-secondary';
            statusText = 'Cancelled';
        }
        
        return '<span class="badge ' + statusClass + '">' + statusText + '</span>';
    }
    
    // Format dates
    function formatDate(dateString) {
        if (!dateString) return '—';
        try {
            var date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch(e) {
            return dateString;
        }
    }
    
    // Populate all fields
    // Booking number removed - no longer displayed
    setText('bd_created_at', formatDate(b.created_at));
    setText('bd_service_name', b.service_name || 'N/A');
    setText('bd_category', b.category_name || 'General');
    setText('bd_service_type', b.service_type || '—');
    setText('bd_item_description', b.item_description || '—');
    setText('bd_pickup_date', b.pickup_date ? formatDate(b.pickup_date) : '—');
    setHTML('bd_status', formatStatus(b.status));
    setText('bd_total_amount', '₱' + (parseFloat(b.total_amount || 0).toFixed(2)));
    setHTML('bd_payment_status', formatPaymentStatus(b.payment_status));
    setText('bd_customer_name', b.customer_name || 'N/A');
    setText('bd_customer_email', b.email || 'N/A');
    setText('bd_customer_phone', b.phone || 'N/A');
}

// Print reservation receipt (only modal content)
function printBookingReceipt() {
    const modalContent = document.getElementById('bookingDetailsModal');
    if (!modalContent) return;
    
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const printContent = modalContent.innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reservation Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .modal-content { background: white; }
                .modal-header { background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white; padding: 1.5rem; border-radius: 8px 8px 0 0; }
                .modal-body { padding: 2rem; }
                .modal-footer { display: none; }
                .close { display: none; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
                table th, table td { padding: 0.75rem; border: 1px solid #ddd; }
                table th { background-color: #f8f9fc; font-weight: 600; }
                hr { border-color: #e3e6f0; margin: 1.5rem 0; }
                .badge { padding: 0.25rem 0.5rem; border-radius: 4px; }
                .badge-success { background-color: #28a745; color: white; }
                .badge-info { background-color: #8B4513; color: white; }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for content to load, then print
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 250);
}

</script>

<!-- Repair Receipt Modal -->
<div class="modal fade" id="repairReceiptModal" tabindex="-1" aria-labelledby="repairReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="repairReceiptModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Repair Reservation Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <!-- Receipt Header -->
                <div class="text-center mb-4">
                    <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 0.5rem;">UphoCare</h4>
                    <p class="text-muted mb-0">Repair & Restoration Services</p>
                </div>
                
                <hr style="border-color: #e3e6f0; margin: 1.5rem 0;">
                
                <!-- Receipt Details -->
                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <p class="mb-2"><strong>Date:</strong></p>
                        <p id="receipt_date">-</p>
                    </div>
                </div>
                
                <hr style="border-color: #e3e6f0; margin: 1.5rem 0;">
                
                <!-- Item Details -->
                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Item Information</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Item Name:</th>
                            <td id="receipt_item_name">-</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td id="receipt_description">-</td>
                        </tr>
                        <tr>
                            <th>Urgency:</th>
                            <td id="receipt_urgency">-</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge badge-success" id="receipt_status">-</span></td>
                        </tr>
                        <tr>
                            <th>Estimated Cost:</th>
                            <td id="receipt_estimated_cost">-</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Customer Information -->
                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem; margin-top: 1.5rem;">Customer Information</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td id="receipt_customer_name">-</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td id="receipt_customer_email">-</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td id="receipt_customer_phone">-</td>
                        </tr>
                    </table>
                </div>
                
                <hr style="border-color: #e3e6f0; margin: 1.5rem 0;">
                
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> Please keep this receipt for your records.
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 2rem 2rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-print mr-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="bookingDetailsModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Reservation Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center mb-4">
                    <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 0.25rem;">UphoCare</h4>
                    <p class="text-muted mb-0">Reservation Receipt</p>
                </div>

                <hr style="border-color: #e3e6f0; margin: 1.25rem 0;">

                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <p class="mb-2"><strong>Date:</strong></p>
                        <p id="bd_created_at">-</p>
                    </div>
                </div>

                <hr style="border-color: #e3e6f0; margin: 1.25rem 0;">

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Service Information</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Service:</th>
                            <td id="bd_service_name">-</td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td id="bd_category">-</td>
                        </tr>
                        <tr>
                            <th>Service Type:</th>
                            <td id="bd_service_type">-</td>
                        </tr>
                    </table>
                </div>

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Reservation Details</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Item Description:</th>
                            <td id="bd_item_description">-</td>
                        </tr>
                        <tr>
                            <th>Pickup Date:</th>
                            <td id="bd_pickup_date">-</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge badge-success" id="bd_status">-</span></td>
                        </tr>
                    </table>
                </div>

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Payment</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Total Amount:</strong></p>
                        <p id="bd_total_amount">₱0.00</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Payment Status:</strong></p>
                        <p><span class="badge badge-info" id="bd_payment_status">Unpaid</span></p>
                    </div>
                </div>

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Customer</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td id="bd_customer_name">-</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td id="bd_customer_email">-</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td id="bd_customer_phone">-</td>
                        </tr>
                    </table>
                </div>

                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> Please keep this receipt for your records. Your reservation number is your reference.
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 2rem 2rem; background: #f8f9fc;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printBookingReceipt()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-print mr-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================
// Customer Booking Actions Based on Status
// ============================================

// 1️⃣ Pending Actions
// Already handled by confirmCancelReservation function above

// 2️⃣ Approved Actions
function viewEstimatedDate(bookingId) {
    viewReservationDetails(bookingId); // Show in details modal
}

function messageStore(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/messageStore/' + bookingId;
}

function uploadPhotos(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/uploadPhotos/' + bookingId;
}

// 3️⃣ In Queue Actions
function trackQueuePosition(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/trackQueuePosition/' + bookingId;
}

function trackProgress(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/trackProgress/' + bookingId;
}

// 4️⃣ Under Repair Actions
function viewProgressPhotos(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/viewProgressPhotos/' + bookingId;
}

// 5️⃣ For Quality Check Actions
function viewQualityStatus(bookingId) {
    viewReservationDetails(bookingId);
}

function preparePickup(bookingId) {
    viewReservationDetails(bookingId);
}

function arrangePickup(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/arrangePickup/' + bookingId;
}

// 6️⃣ Ready for Pickup Actions
function payNow(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/payment/' + bookingId;
}

function viewPickupInstructions(bookingId) {
    viewReservationDetails(bookingId);
}

function viewTotalAmount(bookingId) {
    viewReservationDetails(bookingId);
}

function generatePickupCode(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/generatePickupCode/' + bookingId;
}

// 7️⃣ Out for Delivery Actions
function trackDelivery(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/trackDelivery/' + bookingId;
}

function prepareCash(bookingId) {
    viewReservationDetails(bookingId);
}

function contactRider(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/contactRider/' + bookingId;
}

// 8️⃣ Completed Actions
function downloadReceipt(bookingId) {
    window.open('<?php echo BASE_URL; ?>customer/downloadReceipt/' + bookingId, '_blank');
}

function rateService(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/rateService/' + bookingId;
}

function viewBeforeAfterPhotos(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/viewBeforeAfterPhotos/' + bookingId;
}

function bookAgain(bookingId) {
    if (confirm('Create a new reservation based on this booking?')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/bookAgain/' + bookingId;
    }
}

// 9️⃣ Cancelled Actions
function viewCancellationReason(bookingId) {
    viewReservationDetails(bookingId);
}

function requestRefund(bookingId) {
    if (confirm('Request a refund for this cancelled booking? The store will review your request.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/requestRefund/' + bookingId;
    }
}

// Repair Reservation Specific Actions
function confirmCancelRepair(repairId) {
    if (confirm('Are you sure you want to cancel this repair reservation? This action cannot be undone.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/cancelRepairReservation/' + repairId;
    }
}
// Auto-refresh booking statuses every 30 seconds to show admin updates
let statusRefreshInterval = null;

function refreshBookingStatuses() {
    // Get all booking rows
    const bookingRows = document.querySelectorAll('.booking-row[data-booking-id]');
    if (bookingRows.length === 0) return;
    
    // Get all booking IDs
    const bookingIds = Array.from(bookingRows).map(row => row.getAttribute('data-booking-id'));
    
    // Refresh each booking's status
    bookingIds.forEach(bookingId => {
        // Add cache-busting timestamp to ensure fresh data
        const timestamp = new Date().getTime();
        fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + bookingId + '?t=' + timestamp, {
            method: 'GET',
            cache: 'default',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    const booking = data.data;
                    // Preserve the actual status from server - only default to 'pending' if truly empty/null
                    let status = (booking.status || '').toString().trim();
                    
                    // Only default to 'pending' if status is truly empty/null/undefined
                    if (!status || status === 'null' || status === 'undefined' || status === '') {
                        status = 'pending';
                    }
                    
                    // Convert to lowercase for comparison, but preserve original for display
                    const statusLower = status.toLowerCase();
                    
                    // Find the row
                    const row = document.querySelector(`.booking-row[data-booking-id="${bookingId}"]`);
                    if (!row) return;
                    
                    // Find status cell using data attribute (most reliable)
                    let statusCell = row.querySelector('td.status-cell[data-booking-status]');
                    
                    // Fallback: find by badge-status class
                    if (!statusCell) {
                        const allCells = row.querySelectorAll('td');
                        for (let i = 0; i < allCells.length; i++) {
                            const cell = allCells[i];
                            const cellText = cell.textContent.trim();
                            
                            // Check if this cell contains a badge with badge-status class
                            const statusBadge = cell.querySelector('.badge-status');
                            if (statusBadge) {
                                statusCell = cell;
                                break;
                            }
                        }
                    }
                    
                    // Fallback: if badge-status not found, try finding by badge class and status keywords
                    if (!statusCell) {
                        const allCells = row.querySelectorAll('td');
                        for (let i = 0; i < allCells.length; i++) {
                            const cell = allCells[i];
                            const cellText = cell.textContent.trim();
                            
                            const badge = cell.querySelector('.badge');
                            if (badge) {
                                const badgeText = badge.textContent.toLowerCase();
                                // Check if badge contains status-related keywords
                                if (badgeText.includes('pending') || 
                                    badgeText.includes('approved') || 
                                    badgeText.includes('under repair') ||
                                    badgeText.includes('in queue') ||
                                    badgeText.includes('completed') ||
                                    badgeText.includes('cancelled') ||
                                    badgeText.includes('ready') ||
                                    badgeText.includes('delivery') ||
                                    badgeText.includes('quality')) {
                                    statusCell = cell;
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Final fallback: use nth-child(6) - the STATUS column (6th column in the table)
                    // Table structure: Checkbox(1), Flag(2), Service(3), Service Option(4), Date(5), STATUS(6), Actions(7)
                    if (!statusCell) {
                        const candidateCell = row.querySelector('td:nth-child(6)');
                        if (candidateCell) {
                            statusCell = candidateCell;
                        } else {
                            // Fallback: find status cell by searching all cells
                            const allCells = Array.from(row.querySelectorAll('td'));
                            for (let i = 0; i < allCells.length; i++) {
                                const cell = allCells[i];
                                const cellText = cell.textContent.trim();
                                // Check if this cell contains status badge
                                if (cell.querySelector('.badge-status')) {
                                    statusCell = cell;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if (!statusCell) {
                        console.error('Status cell not found for booking:', bookingId, 'Row:', row);
                        return;
                    }
                    
                    // Get current displayed status from the status cell
                    const currentStatusText = statusCell.textContent.trim().toLowerCase();
                    const currentStatusAttr = statusCell.getAttribute('data-booking-status') || '';
                    
                    // Normalize status for comparison (handle variations like 'approved', 'Approved', 'APPROVED')
                    const normalizedStatus = statusLower;
                    const normalizedCurrent = currentStatusAttr.toLowerCase();
                    
                    // Check if status actually changed
                    // Compare both the displayed text and the data attribute
                    const statusMatches = (
                        currentStatusText.includes(normalizedStatus) || 
                        normalizedCurrent === normalizedStatus
                    );
                    
                    // Only update if status actually changed
                    // CRITICAL LOGIC: Prevent approved status from being reset to pending
                    
                    // If UI already shows approved, NEVER override it with pending from server
                    // This protects against server returning stale/cached data
                    if ((currentStatusText.includes('approved') || normalizedCurrent === 'approved') && normalizedStatus === 'pending') {
                        // Don't override approved with pending (preserve approved status)
                        return;
                    }
                    
                    // If server says approved but UI shows pending, always update
                    if (normalizedStatus === 'approved' && (currentStatusText.includes('pending') || normalizedCurrent === 'pending')) {
                        // Force update from pending to approved - don't return
                    } else if (statusMatches && normalizedStatus !== 'pending') {
                        // Status matches and it's not pending, skip update to prevent loops
                        return;
                    } else if (normalizedStatus === 'pending' && currentStatusText.includes('pending') && normalizedCurrent === 'pending') {
                        // Both are pending, skip update
                        return;
                    }
                    
                    // Status mapping - use normalized status (lowercase) for lookup
                    const statusConfig = {
                        'pending': {class: 'badge-warning', icon: 'clock', text: 'Pending'},
                        'approved': {class: 'badge-success', icon: 'check-circle', text: 'Approved'},
                        'Approved': {class: 'badge-success', icon: 'check-circle', text: 'Approved'}, // Handle uppercase
                        'APPROVED': {class: 'badge-success', icon: 'check-circle', text: 'Approved'}, // Handle all caps
                        'in_queue': {class: 'badge-info', icon: 'list', text: 'In Queue'},
                        'under_repair': {class: 'badge-primary', icon: 'tools', text: 'Under Repair'},
                        'for_quality_check': {class: 'badge-info', icon: 'search', text: 'For Quality Check'},
                        'ready_for_pickup': {class: 'badge-success', icon: 'box', text: 'Ready for Pickup'},
                        'out_for_delivery': {class: 'badge-warning', icon: 'truck', text: 'Out for Delivery'},
                        'completed': {class: 'badge-success', icon: 'check-double', text: 'Completed'},
                        'cancelled': {class: 'badge-secondary', icon: 'ban', text: 'Cancelled'},
                        'accepted': {class: 'badge-success', icon: 'check-circle', text: 'Approved'},
                        'confirmed': {class: 'badge-success', icon: 'check-circle', text: 'Approved'},
                        'in_progress': {class: 'badge-primary', icon: 'spinner', text: 'Under Repair'},
                        'ongoing': {class: 'badge-primary', icon: 'spinner', text: 'Under Repair'},
                        'admin_review': {class: 'badge-warning', icon: 'eye', text: 'Admin Review'}
                    };
                    
                    // Get config or create default - use normalized status for lookup
                    let config = statusConfig[status] || statusConfig[statusLower];
                    if (!config) {
                        // Try to format unknown status
                        const formattedStatus = status.split(/[_-]/).map(word => 
                            word.charAt(0).toUpperCase() + word.slice(1)
                        ).join(' ');
                        config = {
                            class: 'badge-secondary',
                            icon: 'circle',
                            text: formattedStatus || 'Pending'
                        };
                    }
                    
                    // Ensure "Approved" status shows clearly (handle any case variation)
                    if (normalizedStatus === 'approved') {
                        config = {class: 'badge-success', icon: 'check-circle', text: 'Approved'};
                    }
                    
                    // Ensure text is never empty
                    if (!config.text || config.text.trim() === '') {
                        config.text = 'Pending';
                        config.class = 'badge-warning';
                        config.icon = 'clock';
                    }
                    
                    // Ensure status cell has the correct class and attributes for future detection
                    statusCell.classList.add('status-cell');
                    // Store normalized status in data attribute for reliable comparison
                    statusCell.setAttribute('data-booking-status', normalizedStatus);
                    
                    // Update status badge with visible text
                    statusCell.innerHTML = `
                        <span class="badge badge-status ${config.class}" style="font-size: 0.85rem !important; font-weight: 600 !important; padding: 0.5rem 0.875rem !important; display: inline-flex !important; align-items: center !important; white-space: nowrap !important;">
                            <i class="fas fa-${config.icon} mr-1"></i>
                            <strong>${config.text}</strong>
                        </span>
                    `;
                }
            })
            .catch(error => {
                // Only log actual errors, not cache-related warnings
                if (error.message && 
                    !error.message.includes('cache') && 
                    !error.message.includes('Content unavailable') &&
                    !error.message.includes('Failed to fetch')) {
                    console.error('Error refreshing booking status for booking ID ' + bookingId + ':', error);
                }
                // Silently handle network/cache-related errors - they're not critical
            });
    });
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Refresh immediately on page load to get latest status
    refreshBookingStatuses();
    
    // Refresh statuses every 8 seconds for faster updates (reduced from 10s)
    // Only set ONE interval to prevent duplicate calls
    if (!statusRefreshInterval) {
        statusRefreshInterval = setInterval(refreshBookingStatuses, 8000);
    }
    
    // Also refresh when page becomes visible (user switches back to tab)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshBookingStatuses();
        }
    });
    
    // Refresh when window gains focus
    window.addEventListener('focus', function() {
        refreshBookingStatuses();
    });
    
    // Listen for storage events (cross-tab communication)
    window.addEventListener('storage', function(e) {
        if (e.key === 'booking_status_updated') {
            const bookingId = e.newValue;
            if (bookingId) {
                // Refresh specific booking status
                if (typeof refreshSingleBookingStatus === 'function') {
                    refreshSingleBookingStatus(bookingId);
                } else {
                    refreshBookingStatuses();
                }
            }
        }
    });
});

// Clean up interval when page unloads
window.addEventListener('beforeunload', function() {
    if (statusRefreshInterval) {
        clearInterval(statusRefreshInterval);
    }
});

// Update Booking Modal Functions
function openUpdateBookingModal(bookingId) {
    if (!bookingId) {
        alert('Invalid booking ID');
        return;
    }
    
    // Fetch booking details
    fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const booking = data.data;
                populateUpdateModal(booking);
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery('#updateBookingModal').modal('show');
                } else {
                    const modalEl = document.getElementById('updateBookingModal');
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                }
            } else {
                alert('Error loading booking details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading booking details. Please try again.');
        });
}

function populateUpdateModal(booking) {
    // Populate service option
    const serviceOption = booking.service_option || 'pickup';
    document.getElementById('update_service_option').value = serviceOption;
    
    // Populate dates
    if (booking.pickup_date) {
        const pickupDate = new Date(booking.pickup_date);
        document.getElementById('update_pickup_date').value = pickupDate.toISOString().split('T')[0];
    }
    if (booking.delivery_date) {
        const deliveryDate = new Date(booking.delivery_date);
        document.getElementById('update_delivery_date').value = deliveryDate.toISOString().split('T')[0];
    }
    
    // Populate addresses
    document.getElementById('update_pickup_address').value = booking.pickup_address || '';
    document.getElementById('update_delivery_address').value = booking.delivery_address || '';
    
    // Store booking ID
    document.getElementById('update_booking_id').value = booking.id;
    
    // Show/hide address fields based on service option
    toggleAddressFields(serviceOption);
}

function toggleAddressFields(serviceOption) {
    const pickupGroup = document.getElementById('update_pickup_address_group');
    const deliveryGroup = document.getElementById('update_delivery_address_group');
    
    if (serviceOption === 'pickup' || serviceOption === 'both') {
        pickupGroup.style.display = 'block';
    } else {
        pickupGroup.style.display = 'none';
    }
    
    if (serviceOption === 'delivery' || serviceOption === 'both') {
        deliveryGroup.style.display = 'block';
    } else {
        deliveryGroup.style.display = 'none';
    }
}

// Listen for service option change
document.addEventListener('DOMContentLoaded', function() {
    const serviceOptionSelect = document.getElementById('update_service_option');
    if (serviceOptionSelect) {
        serviceOptionSelect.addEventListener('change', function() {
            toggleAddressFields(this.value);
        });
    }
});

function submitUpdateBooking() {
    const bookingId = document.getElementById('update_booking_id').value;
    const serviceOption = document.getElementById('update_service_option').value;
    const pickupDate = document.getElementById('update_pickup_date').value;
    const deliveryDate = document.getElementById('update_delivery_date').value;
    const pickupAddress = document.getElementById('update_pickup_address').value;
    const deliveryAddress = document.getElementById('update_delivery_address').value;
    
    if (!bookingId) {
        alert('Invalid booking ID');
        return;
    }
    
    // Validate required fields based on service option
    if ((serviceOption === 'pickup' || serviceOption === 'both') && !pickupAddress.trim()) {
        alert('Pickup address is required');
        return;
    }
    
    if ((serviceOption === 'delivery' || serviceOption === 'both') && !deliveryAddress.trim()) {
        alert('Delivery address is required');
        return;
    }
    
    // Show loading
    const submitBtn = document.getElementById('update_booking_submit_btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    // Prepare form data
    const formData = new URLSearchParams();
    formData.append('booking_id', bookingId);
    formData.append('service_option', serviceOption);
    if (pickupDate) formData.append('pickup_date', pickupDate);
    if (deliveryDate) formData.append('delivery_date', deliveryDate);
    if (pickupAddress) formData.append('pickup_address', pickupAddress);
    if (deliveryAddress) formData.append('delivery_address', deliveryAddress);
    
    // Submit update
    fetch('<?php echo BASE_URL; ?>customer/updateBooking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking updated successfully!');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#updateBookingModal').modal('hide');
            } else {
                const modalEl = document.getElementById('updateBookingModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            location.reload(); // Reload to show updated data
        } else {
            alert('Error: ' + (data.message || 'Failed to update booking'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the booking. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

