<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-check mr-2"></i>All Bookings
        </h1>
        <div>
            <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#bookingNumbersModal">
                <i class="fas fa-ticket-alt mr-1"></i> Manage Booking Numbers
            </button>
            <!-- <a href="<?php echo BASE_URL; ?>admin/repairItems" class="btn btn-sm btn-success">
                <i class="fas fa-tools mr-1"></i> Repair Items
            </a> -->
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="active-tab" data-toggle="tab" href="#activeBookings" role="tab" aria-controls="activeBookings" aria-selected="true">
                        <i class="fas fa-clock mr-2"></i>Active Bookings
                        <span class="badge badge-warning ml-2" id="activeCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="completed-tab" data-toggle="tab" href="#completedBookings" role="tab" aria-controls="completedBookings" aria-selected="false">
                        <i class="fas fa-check-circle mr-2"></i>Completed Bookings
                        <span class="badge badge-success ml-2" id="completedCount">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Booking Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="bookingTabsContent">
                        <!-- Active Bookings Tab -->
                        <div class="tab-pane fade show active" id="activeBookings" role="tabpanel" aria-labelledby="active-tab">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="activeBookingsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Booking #</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $activeCount = 0;
                                        $completedCount = 0;
                                        if (!empty($bookings)): 
                                            foreach ($bookings as $booking): 
                                                // Check if booking is completed (status = completed AND payment = paid)
                                                $isCompleted = ($booking['status'] === 'completed' && $booking['payment_status'] === 'paid');
                                                if ($isCompleted) {
                                                    $completedCount++;
                                                    continue; // Skip completed bookings in active tab
                                                }
                                                $activeCount++;
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($booking['booking_number']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="customer-info">
                                                        <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['email']); ?></small>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['phone']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="service-info">
                                                        <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['service_type']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($booking['category_name']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="amount">₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Status mapping based on new booking status flow
                                                    $statusConfig = [
                                                        'pending' => ['class' => 'badge-warning', 'text' => 'Pending'],
                                                        'approved' => ['class' => 'badge-success', 'text' => 'Approved / Accepted'],
                                                        'accepted' => ['class' => 'badge-success', 'text' => 'Approved / Accepted'],
                                                        'in_progress' => ['class' => 'badge-primary', 'text' => 'In Progress'],
                                                        'ongoing' => ['class' => 'badge-primary', 'text' => 'In Progress'],
                                                        'completed' => ['class' => 'badge-info', 'text' => 'Completed'],
                                                        'for_pickup' => ['class' => 'badge-info', 'text' => 'For Pickup'],
                                                        'rejected' => ['class' => 'badge-danger', 'text' => 'Rejected / Declined'],
                                                        'declined' => ['class' => 'badge-danger', 'text' => 'Rejected / Declined'],
                                                        'cancelled' => ['class' => 'badge-secondary', 'text' => 'Cancelled'],
                                                        'confirmed' => ['class' => 'badge-success', 'text' => 'Approved / Accepted'] // Legacy
                                                    ];
                                                    
                                                    $status = $booking['status'] ?? 'pending';
                                                    $config = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'text' => ucwords(str_replace('_', ' ', $status))];
                                                    
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
                                                    <span class="badge <?php echo $config['class']; ?>">
                                                        <?php echo $config['text']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Payment status mapping
                                                    $paymentConfig = [
                                                        'unpaid' => ['class' => 'badge-danger', 'text' => 'Unpaid'],
                                                        'paid' => ['class' => 'badge-success', 'text' => 'Paid'],
                                                        'refunded' => ['class' => 'badge-info', 'text' => 'Refunded'],
                                                        'failed' => ['class' => 'badge-warning', 'text' => 'Failed']
                                                    ];
                                                    
                                                    $paymentStatus = $booking['payment_status'] ?? 'unpaid';
                                                    $paymentConfigItem = $paymentConfig[$paymentStatus] ?? ['class' => 'badge-secondary', 'text' => ucfirst($paymentStatus)];
                                                    ?>
                                                    <span class="badge <?php echo $paymentConfigItem['class']; ?>">
                                                        <?php echo $paymentConfigItem['text']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="date-info"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($booking['status'] === 'pending'): ?>
                                                            <button type="button" class="btn btn-sm btn-success" 
                                                                    onclick="acceptReservation(<?php echo $booking['id']; ?>)"
                                                                    title="Approve / Accept Reservation">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" 
                                                                    onclick="rejectReservation(<?php echo $booking['id']; ?>)"
                                                                    title="Reject / Decline Reservation">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="updateStatus(<?php echo $booking['id']; ?>, '<?php echo $booking['status']; ?>')"
                                                                title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                onclick="viewDetails(<?php echo $booking['id']; ?>)"
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="checkBookingCompliance(<?php echo $booking['id']; ?>, <?php echo $booking['user_id']; ?>)"
                                                                title="Check Booking Compliance">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php 
                                            endforeach; 
                                        endif; 
                                        ?>
                                        <?php if ($activeCount === 0): ?>
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                                                    <br><span class="text-muted">No active bookings found</span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Completed Bookings Tab -->
                        <div class="tab-pane fade" id="completedBookings" role="tabpanel" aria-labelledby="completed-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="completedBookingsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Booking #</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Completed Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($bookings)): 
                                            foreach ($bookings as $booking): 
                                                // Only show completed bookings (status = completed AND payment = paid)
                                                $isCompleted = ($booking['status'] === 'completed' && $booking['payment_status'] === 'paid');
                                                if (!$isCompleted) {
                                                    continue; // Skip non-completed bookings
                                                }
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($booking['booking_number']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="customer-info">
                                                        <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['email']); ?></small>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['phone']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="service-info">
                                                        <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['service_type']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($booking['category_name']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="amount">₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle mr-1"></i>Completed
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check mr-1"></i>Paid
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="date-info">
                                                        <?php 
                                                        $completedDate = !empty($booking['updated_at']) ? $booking['updated_at'] : $booking['created_at'];
                                                        echo date('M d, Y', strtotime($completedDate)); 
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="generateReceipt(<?php echo $booking['id']; ?>)"
                                                            title="Generate Receipt">
                                                        <i class="fas fa-receipt mr-1"></i>Receipt
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            onclick="viewDetails(<?php echo $booking['id']; ?>)"
                                                            title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php 
                                            endforeach; 
                                        endif; 
                                        ?>
                                        <?php if ($completedCount === 0): ?>
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                                                    <br><span class="text-muted">No completed bookings found</span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Booking Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="<?php echo BASE_URL; ?>admin/updateBookingStatus">
                <div class="modal-body">
                    <input type="hidden" name="booking_id" id="booking_id">
                    <div class="form-group">
                        <label for="status">Booking Status</label>
                        <select class="form-control" name="status" id="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved / Accepted</option>
                            <option value="in_progress">In Progress / Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="for_pickup">For Pickup</option>
                            <option value="rejected">Rejected / Declined</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_status">Payment Status</label>
                        <select class="form-control" name="payment_status" id="payment_status">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="refunded">Refunded</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> A booking is considered fully completed when status is "Completed" and payment is "Paid". 
                        Once completed, no further transactions can be made.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="receiptModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Payment Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="receiptContent">
                <!-- Receipt content will be loaded here -->
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Loading receipt...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()">
                    <i class="fas fa-print mr-1"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle mr-2"></i>Reject Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject_booking_id">
                <div class="form-group">
                    <label for="reject_reason">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reject_reason" rows="4" 
                              placeholder="Please provide a reason for rejecting this reservation..." required></textarea>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The customer will be notified of the rejection.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejection()">
                    <i class="fas fa-times mr-1"></i> Reject Reservation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Numbers Management Modal -->
<div class="modal fade" id="bookingNumbersModal" tabindex="-1" role="dialog" aria-labelledby="bookingNumbersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingNumbersModalLabel">
                    <i class="fas fa-ticket-alt mr-2"></i>Booking Numbers Management
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add New Booking Numbers Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-plus mr-2"></i>Add New Booking Numbers
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="addBookingNumbersForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="prefix">Prefix</label>
                                        <input type="text" class="form-control" id="prefix" name="prefix" value="BKG-" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo date('Ymd'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_number">Start Number</label>
                                        <input type="number" class="form-control" id="start_number" name="start_number" value="1" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="count">Count</label>
                                        <input type="number" class="form-control" id="count" name="count" value="10" min="1" max="100" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-plus mr-1"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Booking Numbers List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list mr-2"></i>Available Booking Numbers
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="bookingNumbersTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Booking Number</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="bookingNumbersTableBody">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="refreshBookingNumbers()">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Accept Reservation Modal (Auto-Assign Booking Number) -->
<div class="modal fade" id="acceptReservationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i>Accept Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="accept_booking_id">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> A booking number will be automatically assigned to this reservation. The customer will be notified via email and in-app notification that their reservation has been confirmed.
                </div>
                <div class="form-group">
                    <label for="accept_admin_notes">Admin Notes (Optional)</label>
                    <textarea class="form-control" id="accept_admin_notes" rows="4" 
                              placeholder="Add any notes about this reservation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmAcceptReservation()">
                    <i class="fas fa-check mr-1"></i> Accept Reservation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Booking Number Modal -->
<div class="modal fade" id="assignBookingNumberModal" tabindex="-1" role="dialog" aria-labelledby="assignBookingNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="assignBookingNumberModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>Assign Booking Number to Customer
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignBookingNumberForm">
                    <input type="hidden" id="assign_booking_number_id">
                    <div class="form-group">
                        <label for="customer_select">Select Customer</label>
                        <select class="form-control" id="customer_select" name="customer_id" required>
                            <option value="">Choose a customer...</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="booking_number_display">Booking Number</label>
                        <input type="text" class="form-control" id="booking_number_display" readonly>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> This booking number will be assigned to the selected customer and they can use it to make reservations.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmAssignBookingNumber()">
                    <i class="fas fa-check mr-1"></i> Assign Booking Number
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Compliance Check Modal -->
<div class="modal fade" id="bookingComplianceModal" tabindex="-1" role="dialog" aria-labelledby="bookingComplianceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="bookingComplianceModalLabel">
                    <i class="fas fa-clipboard-check mr-2"></i>Booking Compliance Check
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Customer Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user mr-2"></i>Customer Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <span id="compliance_customer_name">-</span></p>
                                <p><strong>Email:</strong> <span id="compliance_customer_email">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <span id="compliance_customer_phone">-</span></p>
                                <p><strong>Registration Date:</strong> <span id="compliance_customer_reg_date">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Number Assignment -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-ticket-alt mr-2"></i>Booking Number Assignment
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="booking_number_assignment">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                </div>

                <!-- Reservation Requirements Check -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list-check mr-2"></i>Reservation Requirements Compliance
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="compliance_checklist">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                </div>

                <!-- Current Booking Details -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-check mr-2"></i>Current Booking Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="current_booking_details">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="approveCompliance()" id="approveComplianceBtn" style="display: none;">
                    <i class="fas fa-check mr-1"></i> Approve Compliance
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectCompliance()" id="rejectComplianceBtn" style="display: none;">
                    <i class="fas fa-times mr-1"></i> Reject Compliance
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for jQuery to be loaded (it's in footer)
(function() {
    function initAdminBookings() {
        if (typeof jQuery === 'undefined') {
            // jQuery not loaded yet, wait a bit more
            setTimeout(initAdminBookings, 100);
            return;
        }
        
        // jQuery is loaded, initialize
        jQuery(document).ready(function($) {
            // Update counts
            updateBookingCounts();
            
            // DataTable initialization for active bookings
            if ($.fn.DataTable) {
                $('#activeBookingsTable').DataTable({
                    "order": [[ 7, "desc" ]], // Sort by date descending
                    "pageLength": 25,
                    "responsive": true,
                    "language": {
                        "search": "Search active bookings:",
                        "lengthMenu": "Show _MENU_ bookings per page",
                        "info": "Showing _START_ to _END_ of _TOTAL_ bookings",
                        "infoEmpty": "No active bookings found",
                        "infoFiltered": "(filtered from _MAX_ total bookings)"
                    }
                });
                
                // DataTable initialization for completed bookings
                $('#completedBookingsTable').DataTable({
                    "order": [[ 7, "desc" ]], // Sort by date descending
                    "pageLength": 25,
                    "responsive": true,
                    "language": {
                        "search": "Search completed bookings:",
                        "lengthMenu": "Show _MENU_ bookings per page",
                        "info": "Showing _START_ to _END_ of _TOTAL_ bookings",
                        "infoEmpty": "No completed bookings found",
                        "infoFiltered": "(filtered from _MAX_ total bookings)"
                    }
                });
            }
            
            // Update counts when tab changes
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                updateBookingCounts();
            });

            // Initialize booking numbers table when modal is shown
            $('#bookingNumbersModal').on('shown.bs.modal', function() {
                loadBookingNumbers();
                loadCustomers();
            });

            // Handle add booking numbers form
            $('#addBookingNumbersForm').on('submit', function(e) {
                e.preventDefault();
                addBookingNumbers();
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminBookings);
    } else {
        initAdminBookings();
    }
})();

// Load booking numbers via AJAX
function loadBookingNumbers() {
    fetch('<?php echo BASE_URL; ?>admin/getBookingNumbers')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('bookingNumbersTableBody');
                tbody.innerHTML = '';
                
                if (data.bookingNumbers.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No booking numbers found</td></tr>';
                    return;
                }
                
                data.bookingNumbers.forEach(bookingNumber => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${bookingNumber.id}</td>
                        <td><span class="badge badge-info">${bookingNumber.booking_number}</span></td>
                        <td>${new Date(bookingNumber.created_at).toLocaleDateString()}</td>
                        <td><span class="badge badge-success">Available</span></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success" 
                                    onclick="assignBookingNumber(${bookingNumber.id}, '${bookingNumber.booking_number}')"
                                    title="Assign to Customer">
                                <i class="fas fa-user-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="deleteBookingNumber(${bookingNumber.id})"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                showAlert('danger', 'Failed to load booking numbers: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while loading booking numbers.');
        });
}

// Load customers for assignment
function loadCustomers() {
    fetch('<?php echo BASE_URL; ?>admin/getCustomers')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('customer_select');
                select.innerHTML = '<option value="">Choose a customer...</option>';
                
                data.customers.forEach(customer => {
                    const option = document.createElement('option');
                    option.value = customer.id;
                    option.textContent = `${customer.fullname} (${customer.email})`;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading customers:', error);
        });
}

// Add new booking numbers
function addBookingNumbers() {
    const formData = new FormData(document.getElementById('addBookingNumbersForm'));
    
    // Show loading state
    const button = document.querySelector('#addBookingNumbersForm button[type="submit"]');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Adding...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/addBookingNumbers', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            loadBookingNumbers(); // Refresh the list
            document.getElementById('addBookingNumbersForm').reset();
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while adding booking numbers.');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Assign booking number to customer
function assignBookingNumber(bookingNumberId, bookingNumber) {
    document.getElementById('assign_booking_number_id').value = bookingNumberId;
    document.getElementById('booking_number_display').value = bookingNumber;
    // Use vanilla JS or ensure jQuery is loaded
    if (typeof jQuery !== 'undefined') {
        jQuery('#assignBookingNumberModal').modal('show');
    } else {
        // Fallback to Bootstrap's native modal API
        const modal = new bootstrap.Modal(document.getElementById('assignBookingNumberModal'));
        modal.show();
    }
}

// Confirm assignment
function confirmAssignBookingNumber() {
    const bookingNumberId = document.getElementById('assign_booking_number_id').value;
    const customerId = document.getElementById('customer_select').value;
    
    if (!customerId) {
        alert('Please select a customer.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Assigning...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/assignBookingNumber', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_number_id=${bookingNumberId}&customer_id=${customerId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof jQuery !== 'undefined') {
                jQuery('#assignBookingNumberModal').modal('hide');
            } else {
                const modalEl = document.getElementById('assignBookingNumberModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            showAlert('success', data.message);
            loadBookingNumbers(); // Refresh the list
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while assigning the booking number.');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Delete booking number
function deleteBookingNumber(bookingNumberId) {
    if (!confirm('Are you sure you want to delete this booking number?')) {
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>admin/deleteBookingNumber', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_number_id=${bookingNumberId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            loadBookingNumbers(); // Refresh the list
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while deleting the booking number.');
    });
}

// Refresh booking numbers
function refreshBookingNumbers() {
    loadBookingNumbers();
}

// Check booking compliance
function checkBookingCompliance(bookingId, customerId) {
    // Show modal
    if (typeof jQuery !== 'undefined') {
        jQuery('#bookingComplianceModal').modal('show');
    } else {
        const modalEl = document.getElementById('bookingComplianceModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
    
    // Load compliance data
    loadBookingComplianceData(bookingId, customerId);
}

// Load booking compliance data
function loadBookingComplianceData(bookingId, customerId) {
    // Show loading state
    showComplianceLoading();
    
    fetch(`<?php echo BASE_URL; ?>admin/getBookingCompliance?booking_id=${bookingId}&customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateComplianceModal(data);
            } else {
                showComplianceError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showComplianceError('An error occurred while loading compliance data.');
        });
}

// Show loading state in compliance modal
function showComplianceLoading() {
    document.getElementById('compliance_customer_name').textContent = 'Loading...';
    document.getElementById('compliance_customer_email').textContent = 'Loading...';
    document.getElementById('compliance_customer_phone').textContent = 'Loading...';
    document.getElementById('compliance_customer_reg_date').textContent = 'Loading...';
    
    document.getElementById('booking_number_assignment').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    document.getElementById('compliance_checklist').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    document.getElementById('current_booking_details').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    // Hide action buttons
    document.getElementById('approveComplianceBtn').style.display = 'none';
    document.getElementById('rejectComplianceBtn').style.display = 'none';
}

// Populate compliance modal with data
function populateComplianceModal(data) {
    // Customer information
    document.getElementById('compliance_customer_name').textContent = data.customer.fullname;
    document.getElementById('compliance_customer_email').textContent = data.customer.email;
    document.getElementById('compliance_customer_phone').textContent = data.customer.phone;
    document.getElementById('compliance_customer_reg_date').textContent = new Date(data.customer.created_at).toLocaleDateString();
    
    // Booking number assignment
    const assignmentHtml = data.bookingNumber ? `
        <div class="alert alert-success">
            <h6><i class="fas fa-check-circle mr-2"></i>Booking Number Assigned</h6>
            <p><strong>Booking Number:</strong> <span class="badge badge-info">${data.bookingNumber.booking_number}</span></p>
            <p><strong>Assigned Date:</strong> ${new Date(data.bookingNumber.assigned_at).toLocaleDateString()}</p>
            <p><strong>Assigned By:</strong> ${data.bookingNumber.assigned_by_admin}</p>
            <p><strong>Status:</strong> <span class="badge badge-success">Active</span></p>
        </div>
    ` : `
        <div class="alert alert-warning">
            <h6><i class="fas fa-exclamation-triangle mr-2"></i>No Booking Number Assigned</h6>
            <p>This customer does not have an assigned booking number. They cannot make reservations without one.</p>
            <button type="button" class="btn btn-sm btn-primary" onclick="assignBookingNumberFromCompliance(${data.customer.id})">
                <i class="fas fa-plus mr-1"></i> Assign Booking Number
            </button>
        </div>
    `;
    document.getElementById('booking_number_assignment').innerHTML = assignmentHtml;
    
    // Compliance checklist
    const complianceHtml = `
        <div class="row">
            <div class="col-md-6">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance1" ${data.bookingNumber ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance1">
                        <strong>Valid Booking Number</strong>
                        <br><small class="text-muted">Customer has an assigned booking number</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance2" ${data.customer.email ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance2">
                        <strong>Valid Email Address</strong>
                        <br><small class="text-muted">Customer has a valid email for notifications</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance3" ${data.customer.phone ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance3">
                        <strong>Contact Information</strong>
                        <br><small class="text-muted">Customer has phone number for contact</small>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance4" ${data.booking ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance4">
                        <strong>Valid Service Selection</strong>
                        <br><small class="text-muted">Customer has selected a valid service</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance5" ${data.booking && data.booking.booking_date ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance5">
                        <strong>Booking Date Provided</strong>
                        <br><small class="text-muted">Customer has specified a booking date</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance6" ${data.booking && data.booking.notes ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance6">
                        <strong>Additional Information</strong>
                        <br><small class="text-muted">Customer has provided additional notes</small>
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <h6>Compliance Score: <span class="badge ${data.complianceScore >= 80 ? 'badge-success' : data.complianceScore >= 60 ? 'badge-warning' : 'badge-danger'}">${data.complianceScore}%</span></h6>
            <p class="text-muted">${data.complianceScore >= 80 ? 'Customer meets all requirements' : data.complianceScore >= 60 ? 'Customer meets most requirements' : 'Customer needs to complete requirements'}</p>
        </div>
    `;
    document.getElementById('compliance_checklist').innerHTML = complianceHtml;
    
    // Current booking details
    const bookingHtml = data.booking ? `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Service:</strong> ${data.booking.service_name}</p>
                <p><strong>Category:</strong> <span class="badge badge-secondary">${data.booking.category_name}</span></p>
                <p><strong>Amount:</strong> <span class="amount">₱${parseFloat(data.booking.total_amount).toFixed(2)}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Booking Date:</strong> ${new Date(data.booking.booking_date).toLocaleDateString()}</p>
                <p><strong>Status:</strong> <span class="badge ${getStatusBadgeClass(data.booking.status)}">${data.booking.status}</span></p>
                <p><strong>Payment:</strong> <span class="badge ${getPaymentBadgeClass(data.booking.payment_status)}">${data.booking.payment_status}</span></p>
            </div>
        </div>
        ${data.booking.notes ? `<hr><p><strong>Notes:</strong><br>${data.booking.notes}</p>` : ''}
    ` : `
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            No current booking found for this customer.
        </div>
    `;
    document.getElementById('current_booking_details').innerHTML = bookingHtml;
    
    // Show action buttons if booking number is assigned
    if (data.bookingNumber) {
        document.getElementById('approveComplianceBtn').style.display = 'inline-block';
        document.getElementById('rejectComplianceBtn').style.display = 'inline-block';
    }
}

// Show compliance error
function showComplianceError(message) {
    document.getElementById('booking_number_assignment').innerHTML = `<div class="alert alert-danger">${message}</div>`;
    document.getElementById('compliance_checklist').innerHTML = `<div class="alert alert-danger">${message}</div>`;
    document.getElementById('current_booking_details').innerHTML = `<div class="alert alert-danger">${message}</div>`;
}

// Get status badge class
function getStatusBadgeClass(status) {
    switch(status) {
        case 'pending': return 'badge-warning';
        case 'confirmed': return 'badge-info';
        case 'in_progress': return 'badge-primary';
        case 'completed': return 'badge-success';
        case 'cancelled': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

// Get payment badge class
function getPaymentBadgeClass(status) {
    switch(status) {
        case 'paid': return 'badge-success';
        case 'partial': return 'badge-warning';
        case 'unpaid': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

// Assign booking number from compliance modal
function assignBookingNumberFromCompliance(customerId) {
    if (typeof jQuery !== 'undefined') {
        jQuery('#bookingComplianceModal').modal('hide');
        jQuery('#bookingNumbersModal').modal('show');
    } else {
        const complianceModal = document.getElementById('bookingComplianceModal');
        const numbersModal = document.getElementById('bookingNumbersModal');
        if (complianceModal) {
            const modal1 = bootstrap.Modal.getInstance(complianceModal);
            if (modal1) modal1.hide();
        }
        if (numbersModal) {
            const modal2 = new bootstrap.Modal(numbersModal);
            modal2.show();
        }
    }
    // You can add logic here to pre-select the customer
}

// Approve compliance
function approveCompliance() {
    if (!confirm('Are you sure you want to approve this customer\'s compliance?')) {
        return;
    }
    
    // Implementation for approving compliance
    showAlert('success', 'Customer compliance approved successfully.');
    if (typeof jQuery !== 'undefined') {
        jQuery('#bookingComplianceModal').modal('hide');
    } else {
        const modalEl = document.getElementById('bookingComplianceModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    }
}

// Reject compliance
function rejectCompliance() {
    if (!confirm('Are you sure you want to reject this customer\'s compliance?')) {
        return;
    }
    
    // Implementation for rejecting compliance
    showAlert('danger', 'Customer compliance rejected.');
    if (typeof jQuery !== 'undefined') {
        jQuery('#bookingComplianceModal').modal('hide');
    } else {
        const modalEl = document.getElementById('bookingComplianceModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    }
}

// Update booking counts
function updateBookingCounts() {
    const activeRows = document.querySelectorAll('#activeBookingsTable tbody tr:not(.empty-state)').length;
    const completedRows = document.querySelectorAll('#completedBookingsTable tbody tr:not(.empty-state)').length;
    
    document.getElementById('activeCount').textContent = activeRows;
    document.getElementById('completedCount').textContent = completedRows;
}

function updateStatus(bookingId, currentStatus) {
    document.getElementById('booking_id').value = bookingId;
    document.getElementById('status').value = currentStatus;
    
    // Load current payment status
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                document.getElementById('payment_status').value = data.booking.payment_status || 'unpaid';
            }
        })
        .catch(error => {
            console.error('Error loading booking details:', error);
        });
    
    if (typeof jQuery !== 'undefined') {
        jQuery('#statusModal').modal('show');
    } else {
        const modalEl = document.getElementById('statusModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
}

// Generate receipt for completed booking
function generateReceipt(bookingId) {
    // Show loading state
    document.getElementById('receiptContent').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Generating receipt...</p>
        </div>
    `;
    
    if (typeof jQuery !== 'undefined') {
        jQuery('#receiptModal').modal('show');
    } else {
        const modalEl = document.getElementById('receiptModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
    
    // Load booking details and generate receipt
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                const booking = data.booking;
                const receiptHtml = `
                    <div class="receipt-container p-4" style="background: #f8f9fc; border-radius: 10px;">
                        <!-- Company Header -->
                        <div class="text-center mb-4 pb-3 border-bottom">
                            <h3 class="mb-1" style="color: #4e73df; font-weight: 700;"><?php echo APP_NAME; ?></h3>
                            <p class="text-muted mb-0">Upholstery Services</p>
                            <p class="text-muted small mb-0">Official Payment Receipt</p>
                        </div>

                        <!-- Receipt Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Receipt Number:</strong> <span class="text-primary">RCP-${String(bookingId).padStart(6, '0')}</span></p>
                                <p class="mb-2"><strong>Booking ID:</strong> <span class="text-primary">${booking.booking_number || 'N/A'}</span></p>
                                <p class="mb-2"><strong>Service:</strong> ${booking.service_name || 'N/A'}</p>
                                <p class="mb-2"><strong>Date:</strong> ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Customer:</strong> ${booking.customer_name || 'N/A'}</p>
                                <p class="mb-2"><strong>Email:</strong> ${booking.email || 'N/A'}</p>
                                <p class="mb-2"><strong>Phone:</strong> ${booking.phone || 'N/A'}</p>
                                <p class="mb-2"><strong>Payment Method:</strong> <span class="badge badge-info">Cash on Delivery</span></p>
                            </div>
                        </div>

                        <!-- Payment Breakdown -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered bg-white">
                                <thead style="background: #e7e7e7;">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Service: ${booking.service_name || 'N/A'}</td>
                                        <td class="text-right">₱${parseFloat(booking.total_amount || 0).toFixed(2)}</td>
                                    </tr>
                                </tbody>
                                <tfoot style="background: #4e73df; color: white;">
                                    <tr>
                                        <th>TOTAL PAID</th>
                                        <th class="text-right">₱${parseFloat(booking.total_amount || 0).toFixed(2)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Status Message -->
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>Payment Confirmed!</strong> This receipt confirms that the service has been completed and payment has been received in full.
                        </div>

                        <!-- Footer Note -->
                        <div class="text-center text-muted small">
                            <p class="mb-1">Thank you for choosing <?php echo APP_NAME; ?>!</p>
                            <p class="mb-0">This transaction is now complete. No further actions are required.</p>
                        </div>
                    </div>
                `;
                document.getElementById('receiptContent').innerHTML = receiptHtml;
            } else {
                document.getElementById('receiptContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Failed to load booking details. Please try again.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('receiptContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    An error occurred while generating the receipt.
                </div>
            `;
        });
}

// Print receipt
function printReceipt() {
    const receiptContent = document.querySelector('.receipt-container');
    if (!receiptContent) return;
    
    const printWindow = window.open('', '_blank');
    const printContent = receiptContent.innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .receipt-container { background: white; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
                table th, table td { padding: 0.75rem; border: 1px solid #ddd; }
                table th { background-color: #f8f9fc; font-weight: 600; }
                .badge { padding: 0.25rem 0.5rem; border-radius: 4px; }
                .badge-info { background-color: #3498db; color: white; }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 250);
}

function viewDetails(bookingId) {
    // Implement booking details view
    alert('Booking details for ID: ' + bookingId);
}

// Accept reservation
function acceptReservation(bookingId) {
    document.getElementById('accept_booking_id').value = bookingId;
    document.getElementById('accept_admin_notes').value = '';
    
    if (typeof jQuery !== 'undefined') {
        jQuery('#acceptReservationModal').modal('show');
    } else {
        const modalEl = document.getElementById('acceptReservationModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
}

// Confirm accept reservation
function confirmAcceptReservation() {
    const bookingId = document.getElementById('accept_booking_id').value;
    const adminNotes = document.getElementById('accept_admin_notes').value.trim();
    
    if (!bookingId) {
        alert('Booking ID is missing.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Accepting...';
    button.disabled = true;
    
    // Send request without booking_number_id - it will be auto-assigned
    fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&admin_notes=${encodeURIComponent(adminNotes)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            if (typeof jQuery !== 'undefined') {
                jQuery('#acceptReservationModal').modal('hide');
            } else {
                const modalEl = document.getElementById('acceptReservationModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            
            // Show success message
            showAlert('success', data.message + (data.booking_number ? ' Booking Number: ' + data.booking_number : ''));
            
            // Reload page after a short delay to show updated booking number and status
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while accepting the reservation.');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Reject reservation
function rejectReservation(bookingId) {
    document.getElementById('reject_booking_id').value = bookingId;
    document.getElementById('reject_reason').value = '';
    if (typeof jQuery !== 'undefined') {
        jQuery('#rejectModal').modal('show');
    } else {
        const modalEl = document.getElementById('rejectModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
}

// Confirm rejection
function confirmRejection() {
    const bookingId = document.getElementById('reject_booking_id').value;
    const reason = document.getElementById('reject_reason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/rejectReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking_id=' + bookingId + '&reason=' + encodeURIComponent(reason)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            if (typeof jQuery !== 'undefined') {
                jQuery('#rejectModal').modal('hide');
            } else {
                const modalEl = document.getElementById('rejectModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            
            // Show success message
            showAlert('success', data.message);
            
            // Update the row status
            const row = document.querySelector(`button[onclick*="${bookingId}"]`).closest('tr');
            const statusCell = row.querySelector('td:nth-child(6)'); // Status column
            statusCell.innerHTML = '<span class="badge badge-danger">Cancelled</span>';
            
            // Remove accept/reject buttons
            const actionCell = row.querySelector('td:last-child');
            actionCell.innerHTML = `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="updateStatus(${bookingId}, 'cancelled')"
                            title="Update Status">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" 
                            onclick="viewDetails(${bookingId})"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            `;
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while rejecting the reservation.');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Show alert message
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Insert at the top of the card body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = cardBody.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
