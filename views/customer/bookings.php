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
    font-size: 0.75rem;
}

/* Booking Status Badges */
.badge-pending {
    background-color: #ffeaa7;
    color: #d63031;
}

.badge-approved, .badge-accepted, .badge-confirmed {
    background-color: #A0522D;
    color: white;
}

.badge-in-progress, .badge-ongoing {
    background-color: #8B4513;
    color: white;
}

.badge-completed {
    background-color: #55efc4;
    color: #00b894;
}

.badge-for-pickup {
    background-color: #74b9ff;
    color: #0984e3;
}

.badge-rejected, .badge-declined {
    background-color: #fab1a0;
    color: #d63031;
}

.badge-cancelled {
    background-color: #95a5a6;
    color: white;
}

.btn-action {
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
                        <option value="approved" <?php echo ($currentStatus === 'approved') ? 'selected' : ''; ?>>Approved / Accepted</option>
                        <option value="in_progress" <?php echo ($currentStatus === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="completed" <?php echo ($currentStatus === 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="rejected" <?php echo ($currentStatus === 'rejected') ? 'selected' : ''; ?>>Rejected / Declined</option>
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
                        <th>RESERVATION ID</th>
                        <th>SERVICE</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th>AMOUNT</th>
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
                                'accepted' => '#8B4513',
                                'confirmed' => '#8B4513',
                                'in_progress' => '#654321',
                                'ongoing' => '#654321',
                                'completed' => '#00b894',
                                'for_pickup' => '#74b9ff',
                                'rejected' => '#e74c3c',
                                'declined' => '#e74c3c',
                                'cancelled' => '#95a5a6'
                            ];
                            $flagColor = $flagColors[$booking['status']] ?? '#8B4513';
                            ?>
                            <i class="fas fa-flag" style="color: <?php echo $flagColor; ?>;" title="Priority: <?php echo ucfirst($booking['status']); ?>"></i>
                        </td>
                        <td>
                            <strong style="color: #8B4513; font-family: monospace; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($booking['booking_number'] ?? 'RV-' . str_pad($booking['id'], 5, '0', STR_PAD_LEFT)); ?>
                            </strong>
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
                            <div style="font-weight: 600;"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></div>
                            <div class="small text-muted">
                                <i class="far fa-clock fa-sm mr-1"></i>
                                <?php echo date('h:i A', strtotime($booking['created_at'])); ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            // Status mapping based on new booking status flow
                            $statusConfig = [
                                'pending' => ['class' => 'badge-warning', 'icon' => 'clock', 'text' => 'Pending'],
                                'approved' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Approved / Accepted'],
                                'accepted' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Approved / Accepted'],
                                'in_progress' => ['class' => 'badge-primary', 'icon' => 'spinner', 'text' => 'In Progress'],
                                'ongoing' => ['class' => 'badge-primary', 'icon' => 'spinner', 'text' => 'In Progress'],
                                'completed' => ['class' => 'badge-info', 'icon' => 'check-double', 'text' => 'Completed'],
                                'for_pickup' => ['class' => 'badge-info', 'icon' => 'box', 'text' => 'For Pickup'],
                                'rejected' => ['class' => 'badge-danger', 'icon' => 'times-circle', 'text' => 'Rejected / Declined'],
                                'declined' => ['class' => 'badge-danger', 'icon' => 'times-circle', 'text' => 'Rejected / Declined'],
                                'cancelled' => ['class' => 'badge-secondary', 'icon' => 'ban', 'text' => 'Cancelled'],
                                // Legacy statuses (for backward compatibility)
                                'confirmed' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Approved / Accepted']
                            ];
                            
                            $status = $booking['status'] ?? 'pending';
                            $config = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'icon' => 'circle', 'text' => ucwords(str_replace('_', ' ', $status))];
                            
                            // Show payment status if completed
                            if ($status === 'completed') {
                                $paymentStatus = $booking['payment_status'] ?? 'unpaid';
                                if ($paymentStatus === 'paid') {
                                    $config['text'] = 'Completed (Paid)';
                                    $config['class'] = 'badge-success';
                                } else {
                                    $config['text'] = 'Completed (Unpaid)';
                                    $config['class'] = 'badge-warning';
                                }
                            }
                            ?>
                            <span class="badge badge-status <?php echo $config['class']; ?>">
                                <i class="fas fa-<?php echo $config['icon']; ?> mr-1"></i>
                                <?php echo $config['text']; ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 1.05rem; color: #2c3e50;">
                                ₱<?php echo number_format($booking['total_amount'], 2); ?>
                            </div>
                            <div class="small">
                                <?php
                                $paymentBadge = $booking['payment_status'] === 'paid' ? 'success' : 
                                               ($booking['payment_status'] === 'partial' ? 'warning' : 'danger');
                                $paymentIcon = $booking['payment_status'] === 'paid' ? 'check' : 
                                              ($booking['payment_status'] === 'partial' ? 'exclamation' : 'times');
                                ?>
                                <span class="badge badge-<?php echo $paymentBadge; ?>" style="font-size: 0.7rem;">
                                    <i class="fas fa-<?php echo $paymentIcon; ?> mr-1"></i>
                                    <?php echo ucfirst($booking['payment_status']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" 
                                        class="btn btn-sm btn-info btn-action" 
                                        onclick="viewReservationDetails(<?php echo $booking['id']; ?>)" 
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($booking['status'] === 'pending'): ?>
                                <button type="button" 
                                        class="btn btn-sm btn-danger btn-action" 
                                        onclick="confirmCancelReservation(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars($booking['booking_number'] ?? 'RV-' . str_pad($booking['id'], 5, '0', STR_PAD_LEFT)); ?>')" 
                                        title="Cancel">
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
                                <th>RESERVATION #</th>
                                <th>ITEM NAME</th>
                                <th>TYPE</th>
                                <th>URGENCY</th>
                                <th>STATUS</th>
                                <th>DATE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($repairReservations as $reservation): ?>
                            <tr>
                                <td>
                                    <strong style="color: #8B4513; font-family: monospace;">
                                        <?php echo htmlspecialchars($reservation['booking_number'] ?? 'Pending...'); ?>
                                    </strong>
                                </td>
                                <td><strong><?php echo htmlspecialchars($reservation['item_name']); ?></strong></td>
                                <td><span class="badge badge-secondary"><?php echo ucfirst($reservation['item_type']); ?></span></td>
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
                                    <?php if ($reservation['status'] === 'approved' && !empty($reservation['booking_number'])): ?>
                                    <a href="<?php echo BASE_URL; ?>customer/viewRepairReservation/<?php echo $reservation['id']; ?>" 
                                       class="btn btn-sm btn-info btn-action" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success btn-action" 
                                            onclick="viewRepairReceipt(<?php echo $reservation['id']; ?>)" 
                                            title="View Receipt">
                                        <i class="fas fa-receipt"></i> Receipt
                                    </button>
                                    <?php elseif ($reservation['status'] === 'pending'): ?>
                                    <a href="<?php echo BASE_URL; ?>customer/viewRepairReservation/<?php echo $reservation['id']; ?>" 
                                       class="btn btn-sm btn-info btn-action" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <span class="text-muted ml-2">Pending Assignment</span>
                                    <?php else: ?>
                                    <a href="<?php echo BASE_URL; ?>customer/viewRepairReservation/<?php echo $reservation['id']; ?>" 
                                       class="btn btn-sm btn-info btn-action" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php endif; ?>
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
                    Do you want to cancel reservation <strong id="cancelReservationNumber"></strong>? This action cannot be undone.
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
        console.log('Processing bulk action for IDs:', selectedIds);
        
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

function confirmCancelReservation(reservationId, reservationNumber) {
    reservationToCancel = reservationId;
    var el = document.getElementById('cancelReservationNumber');
    if (el) el.textContent = reservationNumber;
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
    document.getElementById('receipt_booking_number').textContent = item.booking_number || 'N/A';
    document.getElementById('receipt_item_name').textContent = item.item_name;
    document.getElementById('receipt_item_type').textContent = item.item_type ? item.item_type.charAt(0).toUpperCase() + item.item_type.slice(1) : 'N/A';
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
        if (status === 'paid') statusClass = 'badge-success';
        else if (status === 'partial') statusClass = 'badge-warning';
        return '<span class="badge ' + statusClass + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
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
    setText('bd_booking_number', b.booking_number || ('RV-' + String(b.id).padStart(5, '0')));
    setText('bd_created_at', formatDate(b.created_at));
    setText('bd_service_name', b.service_name || 'N/A');
    setText('bd_category', b.category_name || 'General');
    setText('bd_service_type', b.service_type || '—');
    setText('bd_item_description', b.item_description || '—');
    setText('bd_item_type', b.item_type || '—');
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
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Booking Number:</strong></p>
                        <p class="text-primary" style="font-size: 1.25rem; font-weight: 700;" id="receipt_booking_number">-</p>
                    </div>
                    <div class="col-md-6 text-right">
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
                            <th>Item Type:</th>
                            <td id="receipt_item_type">-</td>
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
                    <strong>Note:</strong> Please keep this receipt for your records. Your booking number is your reference for this reservation.
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
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Reservation Number:</strong></p>
                        <p class="text-primary" style="font-size: 1.25rem; font-weight: 700; color: #8B4513;" id="bd_booking_number">-</p>
                    </div>
                    <div class="col-md-6 text-right">
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
                            <th>Item Type:</th>
                            <td id="bd_item_type">-</td>
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

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

