<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
}

.booking-detail-card {
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

.detail-section {
    padding: 1.5rem;
    border-bottom: 1px solid #e3e6f0;
}

.detail-section:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.detail-value {
    color: #2c3e50;
    font-size: 1rem;
}

/* Print only the receipt/details card */
@media print {
    body * { visibility: hidden; }
    .booking-detail-card, .booking-detail-card * { visibility: visible; }
    .booking-detail-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; }
    .no-print { display: none !important; }
}

/* Override Bootstrap primary colors with brown */
.btn-primary {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%) !important;
    border-color: #1F4E79 !important;
    color: white !important;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1F4E79 0%, #4CAF50 50%, #0F3C5F 100%) !important;
    border-color: #4CAF50 !important;
    color: white !important;
}

.text-primary {
    color: #1F4E79 !important;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h1 class="page-title mb-2">Booking Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/bookings">Bookings</a></li>
                <li class="breadcrumb-item active">Booking Details</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Bookings
        </a>
    </div>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($booking): ?>
<!-- Booking Details Card -->
<div class="card booking-detail-card">
    <div style="height: 4px; background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);"></div>
    
    <!-- Booking Header -->
    <div class="detail-section" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%); color: white;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-2" style="color: white;">
                    <i class="fas fa-calendar-check mr-2"></i>Booking Details
                </h3>
                <p class="mb-0" style="opacity: 0.9;">
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('F d, Y h:i A', strtotime($booking['created_at'])); ?>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <?php
                $statusConfig = [
                    'pending' => ['class' => 'badge-warning', 'text' => 'Pending'],
                    'approved' => ['class' => 'badge-success', 'text' => 'Approved'],
                    'in_queue' => ['class' => 'badge-info', 'text' => 'In Queue'],
                    'under_repair' => ['class' => 'badge-primary', 'text' => 'Under Repair'],
                    'for_quality_check' => ['class' => 'badge-info', 'text' => 'For Quality Check'],
                    'ready_for_pickup' => ['class' => 'badge-success', 'text' => 'Ready for Pickup'],
                    'out_for_delivery' => ['class' => 'badge-warning', 'text' => 'Out for Delivery'],
                    'completed' => ['class' => 'badge-success', 'text' => 'Completed'],
                    'cancelled' => ['class' => 'badge-secondary', 'text' => 'Cancelled'],
                    // Legacy statuses (for backward compatibility)
                    'confirmed' => ['class' => 'badge-success', 'text' => 'Approved'],
                    'in_progress' => ['class' => 'badge-primary', 'text' => 'Under Repair'],
                    'ongoing' => ['class' => 'badge-primary', 'text' => 'Under Repair']
                ];
                $status = $booking['status'] ?? 'pending';
                $config = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'text' => ucwords(str_replace('_', ' ', $status))];
                ?>
                <span class="badge <?php echo $config['class']; ?>" id="booking_status_badge" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <i class="fas fa-<?php 
                        $statusIcons = [
                            'pending' => 'clock',
                            'approved' => 'check-circle',
                            'in_queue' => 'list',
                            'under_repair' => 'tools',
                            'for_quality_check' => 'search',
                            'ready_for_pickup' => 'box',
                            'out_for_delivery' => 'truck',
                            'completed' => 'check-double',
                            'cancelled' => 'ban'
                        ];
                        echo $statusIcons[$status] ?? 'circle';
                    ?> mr-1"></i>
                    <?php echo $config['text']; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Booking Number Section (Prominently Displayed) -->
    <div class="detail-section text-center" style="background: #f8f9fc; border-left: 4px solid #1F4E79;">
        <div class="mb-2">
            <small class="text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fas fa-ticket-alt mr-1"></i>Booking Number Assigned by Admin
            </small>
        </div>
        <h2 class="mb-0" style="color: #1F4E79; font-weight: 700; font-family: monospace; font-size: 2rem;">
            <?php if (!empty($booking['booking_number'])): ?>
                <?php echo htmlspecialchars($booking['booking_number']); ?>
            <?php else: ?>
                <span class="text-muted" style="font-size: 1.25rem;">Pending Assignment</span>
                <br>
                <small style="font-size: 0.9rem; font-weight: normal;">Admin will assign your booking number soon</small>
            <?php endif; ?>
        </h2>
        <?php if (!empty($booking['booking_number'])): ?>
        <p class="mt-2 mb-0" style="color: #5a5c69; font-size: 0.9rem;">
            <i class="fas fa-info-circle mr-1"></i>Please keep this booking number for your records
        </p>
        <?php endif; ?>
    </div>

    <!-- Service Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-tools mr-2"></i>Service Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Service Name</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['service_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Service Category</div>
                <div class="detail-value">
                    <span class="badge badge-secondary"><?php echo htmlspecialchars($booking['category_name'] ?? 'General'); ?></span>
                </div>
            </div>
            <?php if (!empty($booking['service_type'])): ?>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Service Type</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['service_type']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($booking['service_description'])): ?>
            <div class="col-md-12 mb-3">
                <div class="detail-label">Service Description</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['service_description']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Details -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-info-circle mr-2"></i>Booking Details
        </h5>
        <div class="row">
            <?php if (!empty($booking['item_description'])): ?>
            <div class="col-md-12 mb-3">
                <div class="detail-label">Item Description</div>
                <div class="detail-value"><?php echo nl2br(htmlspecialchars($booking['item_description'])); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($booking['pickup_date'])): ?>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Pickup Date</div>
                <div class="detail-value"><?php echo date('F d, Y', strtotime($booking['pickup_date'])); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($booking['notes'])): ?>
            <div class="col-md-12 mb-3">
                <div class="detail-label">Additional Notes</div>
                <div class="detail-value"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-money-bill-wave mr-2"></i>Payment Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Total Amount</div>
                <div class="detail-value" style="font-size: 1.25rem; font-weight: 700; color: #2c3e50;">
                    ₱<?php echo number_format($booking['total_amount'] ?? 0, 2); ?>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Payment Status</div>
                <div class="detail-value">
                    <?php
                    $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                    $paymentConfig = [
                        'unpaid' => ['class' => 'badge-danger', 'text' => 'Unpaid'],
                        'paid' => ['class' => 'badge-success', 'text' => 'Paid (Full Cash)'],
                        'paid_full_cash' => ['class' => 'badge-success', 'text' => 'Paid (Full Cash)'],
                        'paid_on_delivery_cod' => ['class' => 'badge-success', 'text' => 'Paid on Delivery (COD)'],
                        'partial' => ['class' => 'badge-warning', 'text' => 'Partial'],
                        'cancelled' => ['class' => 'badge-secondary', 'text' => 'Cancelled']
                    ];
                    $paymentInfo = $paymentConfig[$paymentStatus] ?? ['class' => 'badge-secondary', 'text' => ucfirst($paymentStatus)];
                    ?>
                    <span class="badge <?php echo $paymentInfo['class']; ?>">
                        <?php echo $paymentInfo['text']; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-user mr-2"></i>Customer Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Name</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></div>
            </div>            <div class="col-md-6 mb-3">
                <div class="detail-label">Email</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?></div>
            </div>
            <?php if (!empty($booking['phone'])): ?>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Phone</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['phone']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Repair Progress Section -->
    <?php if (in_array(strtolower($booking['status'] ?? ''), ['approved', 'in_queue', 'under_repair', 'for_quality_check', 'ready_for_pickup'])): ?>
    <div class="detail-section" style="background: #f8f9fc; border-left: 4px solid #4e73df;">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-tasks mr-2"></i>Repair Progress
        </h5>
        <div id="customer_progress_history" class="progress-timeline">
            <div class="text-center text-muted py-3">
                <i class="fas fa-spinner fa-spin"></i> Loading progress updates...
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="detail-section no-print" style="background: #f8f9fc;">
        <div class="d-flex flex-wrap justify-content-end gap-2">
            <?php
            $status = strtolower($booking['status'] ?? 'pending');
            $bookingId = $booking['id'] ?? 0;
            $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
            ?>
            
            <!-- Always show View Details (already on this page, but can add print) -->
            <button type="button" class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print Details
            </button>
            
            <!-- Cancel and Delete buttons (show for appropriate statuses) -->
            <?php if (in_array($status, ['pending', 'approved', 'in_queue'])): ?>
                <button type="button" class="btn btn-danger" onclick="confirmCancelBooking()">
                    <i class="fas fa-times mr-1"></i> Cancel Booking
                </button>
            <?php endif; ?>
            
            <?php if ($status === 'pending' || $status === 'cancelled'): ?>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteBooking()">
                    <i class="fas fa-trash mr-1"></i> Delete Booking
                </button>
            <?php endif; ?>
            
            <?php
            // 1️⃣ Pending Bookings - ✔ View details, ✔ Cancel (if allowed), ❌ Cannot edit or make payment yet
            if ($status === 'pending'): ?>
                <!-- Cancel button already shown above -->
            
            <?php
            // 2️⃣ Approved Bookings - ✔ View estimated date, ✔ Chat/message, ✔ Upload photos, ✔ Update service option
            elseif ($status === 'approved'): ?>
                <button type="button" class="btn btn-warning" onclick="updateServiceOption()">
                    <i class="fas fa-edit mr-1"></i> Update Service Option
                </button>
                <button type="button" class="btn btn-info" onclick="viewEstimatedDate()">
                    <i class="fas fa-calendar-check mr-1"></i> View Estimated Date
                </button>
                <a href="<?php echo BASE_URL; ?>customer/messageStore/<?php echo $bookingId; ?>" class="btn btn-primary">
                    <i class="fas fa-comments mr-1"></i> Message Shop
                </a>
                <a href="<?php echo BASE_URL; ?>customer/uploadPhotos/<?php echo $bookingId; ?>" class="btn btn-secondary">
                    <i class="fas fa-camera mr-1"></i> Upload Photos
                </a>
            
            <?php
            // 3️⃣ In Queue - ✔ Track jobs ahead, ✔ Monitor, ✔ Update service option
            elseif ($status === 'in_queue'): ?>
                <button type="button" class="btn btn-warning" onclick="updateServiceOption()">
                    <i class="fas fa-edit mr-1"></i> Update Service Option
                </button>
                <a href="<?php echo BASE_URL; ?>customer/trackQueuePosition/<?php echo $bookingId; ?>" class="btn btn-primary">
                    <i class="fas fa-list-ol mr-1"></i> Track Queue Position
                </a>
                <a href="<?php echo BASE_URL; ?>customer/trackProgress/<?php echo $bookingId; ?>" class="btn btn-info">
                    <i class="fas fa-eye mr-1"></i> Monitor Progress
                </a>
            
            <?php
            // 4️⃣ Under Repair - ✔ Track progress, ✔ View photos, ✔ Send reminders, ❌ Cannot cancel
            elseif ($status === 'under_repair'): ?>
                <a href="<?php echo BASE_URL; ?>customer/trackProgress/<?php echo $bookingId; ?>" class="btn btn-primary">
                    <i class="fas fa-tasks mr-1"></i> Track Progress
                </a>
                <a href="<?php echo BASE_URL; ?>customer/viewProgressPhotos/<?php echo $bookingId; ?>" class="btn btn-info">
                    <i class="fas fa-images mr-1"></i> View Progress Photos
                </a>
                <a href="<?php echo BASE_URL; ?>customer/messageStore/<?php echo $bookingId; ?>" class="btn btn-warning">
                    <i class="fas fa-comments mr-1"></i> Send Reminder/Question
                </a>
            
            <?php
            // 5️⃣ For Quality Check - ✔ See almost done, ✔ Prepare for pickup, ✔ Arrange pickup
            elseif ($status === 'for_quality_check'): ?>
                <button type="button" class="btn btn-success" onclick="viewQualityStatus()">
                    <i class="fas fa-check-circle mr-1"></i> View Quality Status
                </button>
                <button type="button" class="btn btn-info" onclick="preparePickup()">
                    <i class="fas fa-box mr-1"></i> Prepare for Pickup
                </button>
                <a href="<?php echo BASE_URL; ?>customer/arrangePickup/<?php echo $bookingId; ?>" class="btn btn-secondary">
                    <i class="fas fa-user-friends mr-1"></i> Arrange Pickup
                </a>
            
            <?php
            // 6️⃣ Ready for Pickup - ✔ Pay cash, ✔ View instructions, ✔ View amount, ✔ Generate QR
            elseif ($status === 'ready_for_pickup'): ?>
                <?php if (!in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod'])): ?>
                <a href="<?php echo BASE_URL; ?>customer/payment/<?php echo $bookingId; ?>" class="btn btn-success">
                    <i class="fas fa-money-bill-wave mr-1"></i> Pay in Cash
                </a>
                <?php endif; ?>
                <button type="button" class="btn btn-info" onclick="viewPickupInstructions()">
                    <i class="fas fa-info-circle mr-1"></i> Pickup Instructions
                </button>
                <button type="button" class="btn btn-primary" onclick="viewTotalAmount()">
                    <i class="fas fa-dollar-sign mr-1"></i> View Total Amount
                </button>
                <a href="<?php echo BASE_URL; ?>customer/generatePickupCode/<?php echo $bookingId; ?>" class="btn btn-secondary">
                    <i class="fas fa-qrcode mr-1"></i> Generate Pickup Code
                </a>
            
            <?php
            // 7️⃣ Out for Delivery - ✔ Track rider, ✔ Prepare cash, ✔ Contact rider, ✔ View amount, ❌ No reschedule
            elseif ($status === 'out_for_delivery'): ?>
                <a href="<?php echo BASE_URL; ?>customer/trackDelivery/<?php echo $bookingId; ?>" class="btn btn-primary">
                    <i class="fas fa-map-marker-alt mr-1"></i> Track Rider
                </a>
                <button type="button" class="btn btn-success" onclick="prepareCash()">
                    <i class="fas fa-money-bill mr-1"></i> Prepare Cash
                </button>
                <a href="<?php echo BASE_URL; ?>customer/contactRider/<?php echo $bookingId; ?>" class="btn btn-info">
                    <i class="fas fa-phone mr-1"></i> Contact Rider/Shop
                </a>
                <button type="button" class="btn btn-warning" onclick="viewTotalAmount()">
                    <i class="fas fa-dollar-sign mr-1"></i> View Total Amount
                </button>
            
            <?php
            // 8️⃣ Completed - ✔ Download receipt, ✔ Rate service, ✔ View photos, ✔ Book again
            elseif ($status === 'completed'): ?>
                <a href="<?php echo BASE_URL; ?>customer/downloadReceipt/<?php echo $bookingId; ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download mr-1"></i> Download Receipt
                </a>
                <a href="<?php echo BASE_URL; ?>customer/rateService/<?php echo $bookingId; ?>" class="btn btn-warning">
                    <i class="fas fa-star mr-1"></i> Rate Service
                </a>
                <a href="<?php echo BASE_URL; ?>customer/viewBeforeAfterPhotos/<?php echo $bookingId; ?>" class="btn btn-info">
                    <i class="fas fa-images mr-1"></i> View Before/After Photos
                </a>
                <a href="<?php echo BASE_URL; ?>customer/bookAgain/<?php echo $bookingId; ?>" class="btn btn-success">
                    <i class="fas fa-redo mr-1"></i> Book Again
                </a>
            
            <?php
            // 9️⃣ Cancelled - ✔ View reason, ✔ Submit new booking, ✔ Request refund
            elseif ($status === 'cancelled'): ?>
                <button type="button" class="btn btn-info" onclick="viewCancellationReason()">
                    <i class="fas fa-info-circle mr-1"></i> View Cancellation Reason
                </button>
                <a href="<?php echo BASE_URL; ?>customer/bookAgain/<?php echo $bookingId; ?>" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-1"></i> Submit New Booking
                </a>
                <button type="button" class="btn btn-warning" onclick="requestRefund()">
                    <i class="fas fa-undo mr-1"></i> Request Refund
                </button>
            
            <?php
            // Legacy statuses (for backward compatibility)
            elseif (in_array($status, ['accepted', 'confirmed'])): ?>
                <button type="button" class="btn btn-info" onclick="viewEstimatedDate()">
                    <i class="fas fa-calendar-check mr-1"></i> View Estimated Date
                </button>
                <a href="<?php echo BASE_URL; ?>customer/messageStore/<?php echo $bookingId; ?>" class="btn btn-primary">
                    <i class="fas fa-comments mr-1"></i> Message Shop
                </a>
                <a href="<?php echo BASE_URL; ?>customer/uploadPhotos/<?php echo $bookingId; ?>" class="btn btn-secondary">
                    <i class="fas fa-camera mr-1"></i> Upload Photos
                </a>
            
            elseif (in_array($status, ['in_progress', 'ongoing'])): ?>
                <a href="<?php echo BASE_URL; ?>customer/trackProgress/<?php echo $bookingId; ?>" class="btn btn-primary">
                    <i class="fas fa-tasks mr-1"></i> Track Progress
                </a>
                <a href="<?php echo BASE_URL; ?>customer/viewProgressPhotos/<?php echo $bookingId; ?>" class="btn btn-info">
                    <i class="fas fa-images mr-1"></i> View Progress Photos
                </a>
                <a href="<?php echo BASE_URL; ?>customer/messageStore/<?php echo $bookingId; ?>" class="btn btn-warning">
                    <i class="fas fa-comments mr-1"></i> Send Reminder/Question
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Booking Not Found -->
<div class="card booking-detail-card">
    <div class="card-body text-center py-5">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h4>Booking Not Found</h4>
        <p class="text-muted">The booking you're looking for doesn't exist or you don't have permission to view it.</p>
        <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-primary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Bookings
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Update Service Option Modal -->
<div class="modal fade" id="updateServiceOptionModal" tabindex="-1" role="dialog" aria-labelledby="updateServiceOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%); color: white;">
                <h5 class="modal-title" id="updateServiceOptionModalLabel">
                    <i class="fas fa-edit mr-2"></i>Update Service Option & Address
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateServiceOptionForm">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id'] ?? 0; ?>">
                    
                    <div class="form-group">
                        <label for="update_service_option" class="form-label">
                            Service Option <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="update_service_option" name="service_option" required onchange="handleServiceOptionChange()">
                            <option value="">-- Select Service Option --</option>
                            <option value="pickup">Pick Up</option>
                            <option value="delivery">Delivery Service</option>
                            <option value="both">Both (Pick Up & Delivery)</option>
                            <option value="walk_in">Walk In</option>
                        </select>
                        <small class="form-text text-muted">
                            Select how you want to receive the service.
                        </small>
                    </div>
                    
                    <div class="form-group" id="update_pickup_address_group" style="display: none;">
                        <label for="update_pickup_address" class="form-label">
                            Pickup Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="update_pickup_address" name="pickup_address" rows="3"
                                  placeholder="Enter your complete pickup address..."></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-map-marker-alt"></i> 
                            Provide your complete address where we will pick up your item.
                        </small>
                    </div>
                    
                    <div class="form-group" id="update_delivery_address_group" style="display: none;">
                        <label for="update_delivery_address" class="form-label">
                            Delivery Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="update_delivery_address" name="delivery_address" rows="3"
                                  placeholder="Enter your complete delivery address..."></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-map-marker-alt"></i> 
                            Your account address is pre-filled. You can modify it if needed.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitServiceOptionUpdate" onclick="submitServiceOptionUpdate()">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmCancelBooking() {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/cancelBooking/<?php echo $booking['id'] ?? 0; ?>';
    }
}

function confirmDeleteBooking() {
    if (confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/deleteBooking/<?php echo $booking['id'] ?? 0; ?>';
    }
}

function updateServiceOption() {
    // Open the update service option modal
    const modal = new bootstrap.Modal(document.getElementById('updateServiceOptionModal'));
    modal.show();
    
    // Load current booking data
    const bookingId = <?php echo $booking['id'] ?? 0; ?>;
    const currentServiceOption = '<?php echo $booking['service_option'] ?? ''; ?>';
    const currentPickupAddress = `<?php echo addslashes($booking['pickup_address'] ?? ''); ?>`;
    const currentDeliveryAddress = `<?php echo addslashes($booking['delivery_address'] ?? ''); ?>`;
    const userAddress = `<?php echo addslashes($userAddress ?? ''); ?>`;
    
    // Set current values
    document.getElementById('update_service_option').value = currentServiceOption;
    document.getElementById('update_pickup_address').value = currentPickupAddress;
    document.getElementById('update_delivery_address').value = currentDeliveryAddress || userAddress;
    
    // Show/hide address fields based on current selection
    handleServiceOptionChange();
}

function handleServiceOptionChange() {
    const serviceOption = document.getElementById('update_service_option').value;
    const pickupGroup = document.getElementById('update_pickup_address_group');
    const deliveryGroup = document.getElementById('update_delivery_address_group');
    const userAddress = `<?php echo addslashes($userAddress ?? ''); ?>`;
    
    // Hide all groups first
    if (pickupGroup) pickupGroup.style.display = 'none';
    if (deliveryGroup) deliveryGroup.style.display = 'none';
    
    // Show relevant groups based on service option
    if (serviceOption === 'pickup' || serviceOption === 'both') {
        if (pickupGroup) pickupGroup.style.display = 'block';
    }
    
    if (serviceOption === 'delivery' || serviceOption === 'both') {
        if (deliveryGroup) deliveryGroup.style.display = 'block';
        // Auto-fill delivery address from user's account if empty
        const deliveryAddressField = document.getElementById('update_delivery_address');
        if (deliveryAddressField && !deliveryAddressField.value && userAddress) {
            deliveryAddressField.value = userAddress;
        }
    }
}

function submitServiceOptionUpdate() {
    const form = document.getElementById('updateServiceOptionForm');
    const formData = new FormData(form);
    const bookingId = <?php echo $booking['id'] ?? 0; ?>;
    
    // Validate required fields
    const serviceOption = formData.get('service_option');
    if (!serviceOption) {
        alert('Please select a service option.');
        return;
    }
    
    if ((serviceOption === 'pickup' || serviceOption === 'both') && !formData.get('pickup_address')) {
        alert('Please provide pickup address.');
        return;
    }
    
    if ((serviceOption === 'delivery' || serviceOption === 'both') && !formData.get('delivery_address')) {
        alert('Please provide delivery address.');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitServiceOptionUpdate');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';
    
    // Submit via AJAX
    fetch('<?php echo BASE_URL; ?>customer/updateServiceOption', {
        method: 'POST',
        body: formData,
        cache: 'no-cache'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Service option and address updated successfully!');
            // Reload page to show updated information
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update service option.'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// 2️⃣ Approved Actions
function viewEstimatedDate() {
    // Show estimated date in booking details (already on page)
    alert('Estimated completion date is shown in the booking details above.');
}

// 5️⃣ For Quality Check Actions
function viewQualityStatus() {
    alert('Quality check is in progress. Your item will be ready for pickup soon!');
}

function preparePickup() {
    alert('Please prepare to pick up your item. Check the pickup instructions below.');
}

// 6️⃣ Ready for Pickup Actions
function viewPickupInstructions() {
    alert('Pickup instructions:\n- Bring a valid ID\n- Come during business hours\n- Have exact cash ready');
}

function viewTotalAmount() {
    // Amount is already displayed on the page
    alert('Total amount: ₱<?php echo number_format($booking['total_amount'] ?? 0, 2); ?>');
}

// 7️⃣ Out for Delivery Actions
function prepareCash() {
    alert('Please prepare exact cash: ₱<?php echo number_format($booking['total_amount'] ?? 0, 2); ?>\n\nThe delivery rider will collect payment upon delivery.');
}

// 9️⃣ Cancelled Actions
function viewCancellationReason() {
    alert('Cancellation reason: Please check the booking details or contact the store for more information.');
}

function requestRefund() {
    if (confirm('Request a refund for this cancelled booking? The store will review your request.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/requestRefund/<?php echo $booking['id'] ?? 0; ?>';
    }
}

// Load progress history for customer
function loadCustomerProgressHistory(bookingId) {
    const progressDiv = document.getElementById('customer_progress_history');
    if (!progressDiv) return;
    
    fetch('<?php echo BASE_URL; ?>customer/getBookingProgress/' + bookingId, {
        method: 'GET',
        cache: 'no-cache',
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.progress && data.progress.length > 0) {
                progressDiv.innerHTML = data.progress.map((update, index) => {
                    const progressType = update.progress_type || 'Update';
                    const progressTypeText = progressType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    const date = new Date(update.created_at);
                    const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                    
                    return `
                        <div class="progress-item mb-3" style="border-left: 3px solid #4e73df; padding-left: 1rem;">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0" style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-check-circle mr-2" style="color: #4e73df;"></i>${progressTypeText}
                                </h6>
                                <small class="text-muted">${formattedDate}</small>
                            </div>
                            ${update.notes ? `<p class="mb-1" style="color: #5a5c69;">${update.notes}</p>` : ''}
                            <small class="text-muted">
                                <i class="fas fa-user-tie mr-1"></i>Updated by: ${update.admin_name || 'Admin'}
                            </small>
                        </div>
                    `;
                }).join('');
            } else {
                progressDiv.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        No progress updates yet. Admin will update you as work progresses.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading progress:', error);
            progressDiv.innerHTML = `
                <div class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Unable to load progress updates. Please refresh the page.
                </div>
            `;
        });
}

// Load progress and status when page loads
document.addEventListener('DOMContentLoaded', function() {
    const bookingId = <?php echo $booking['id'] ?? 0; ?>;
    if (bookingId) {
        loadCustomerProgressHistory(bookingId);
        // Refresh immediately on page load
        refreshBookingStatus(bookingId);
        
        // Auto-refresh status and progress every 10 seconds for faster updates
        setInterval(function() {
            refreshBookingStatus(bookingId);
            loadCustomerProgressHistory(bookingId);
        }, 10000); // Refresh every 10 seconds for faster status updates
        
        // Also refresh when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshBookingStatus(bookingId);
                loadCustomerProgressHistory(bookingId);
            }
        });
        
        // Refresh when window gains focus
        window.addEventListener('focus', function() {
            refreshBookingStatus(bookingId);
        });
    }
});

// Refresh booking status to show latest updates from admin
function refreshBookingStatus(bookingId) {
    // Add cache-busting parameter to ensure fresh data
    const timestamp = new Date().getTime();
    fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + bookingId + '?t=' + timestamp, {
        method: 'GET',
        cache: 'no-cache',
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                const booking = data.data;
                const status = booking.status || 'pending';
                const currentStatus = document.getElementById('booking_status_badge')?.textContent?.trim() || '';
                
                // Only update if status has changed
                if (currentStatus && !currentStatus.includes(status.replace(/_/g, ' '))) {
                    console.log('Status changed from', currentStatus, 'to', status);
                    
                    // Update status badge
                    const statusConfig = {
                        'pending': {class: 'badge-warning', text: 'Pending'},
                        'approved': {class: 'badge-success', text: 'Approved'},
                        'in_queue': {class: 'badge-info', text: 'In Queue'},
                        'under_repair': {class: 'badge-primary', text: 'Under Repair'},
                        'for_quality_check': {class: 'badge-info', text: 'For Quality Check'},
                        'ready_for_pickup': {class: 'badge-success', text: 'Ready for Pickup'},
                        'out_for_delivery': {class: 'badge-warning', text: 'Out for Delivery'},
                        'completed': {class: 'badge-success', text: 'Completed'},
                        'cancelled': {class: 'badge-secondary', text: 'Cancelled'}
                    };
                    
                    const config = statusConfig[status] || {class: 'badge-secondary', text: ucwords(status.replace(/_/g, ' '))};
                    const statusBadge = document.getElementById('booking_status_badge');
                    if (statusBadge) {
                        const statusIcons = {
                            'pending': 'clock',
                            'approved': 'check-circle',
                            'in_queue': 'list',
                            'under_repair': 'tools',
                            'for_quality_check': 'search',
                            'ready_for_pickup': 'box',
                            'out_for_delivery': 'truck',
                            'completed': 'check-double',
                            'cancelled': 'ban'
                        };
                        const icon = statusIcons[status] || 'circle';
                        statusBadge.className = 'badge ' + config.class;
                        statusBadge.innerHTML = `<i class="fas fa-${icon} mr-1"></i>${config.text}`;
                        
                        // Show notification if status changed to approved
                        if (status === 'approved' && currentStatus.includes('Pending')) {
                            // Show a subtle notification
                            const notification = document.createElement('div');
                            notification.className = 'alert alert-success alert-dismissible fade show';
                            notification.style.position = 'fixed';
                            notification.style.top = '20px';
                            notification.style.right = '20px';
                            notification.style.zIndex = '9999';
                            notification.innerHTML = `
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Booking Approved!</strong> Your reservation has been approved by the admin.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            `;
                            document.body.appendChild(notification);
                            setTimeout(() => {
                                notification.remove();
                            }, 5000);
                        }
                    }
                    
                    // Show/hide progress section based on status
                    const progressSection = document.querySelector('#customer_progress_history')?.closest('.detail-section');
                    const shouldShowProgress = ['approved', 'in_queue', 'under_repair', 'for_quality_check', 'ready_for_pickup'].includes(status);
                    if (progressSection) {
                        if (shouldShowProgress && progressSection.style.display === 'none') {
                            progressSection.style.display = 'block';
                            loadCustomerProgressHistory(bookingId);
                        } else if (!shouldShowProgress) {
                            progressSection.style.display = 'none';
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing status:', error);
        });
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

