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
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
    border-color: #A0522D !important;
    color: white !important;
}

.text-primary {
    color: #8B4513 !important;
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
    <div style="height: 4px; background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);"></div>
    
    <!-- Booking Header -->
    <div class="detail-section" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white;">
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
                $statusClass = [
                    'pending' => 'badge-warning',
                    'confirmed' => 'badge-info',
                    'in_progress' => 'badge-primary',
                    'completed' => 'badge-success',
                    'cancelled' => 'badge-danger'
                ][$booking['status']] ?? 'badge-secondary';
                ?>
                <span class="badge <?php echo $statusClass; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <?php echo ucwords(str_replace('_', ' ', $booking['status'])); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Booking Number Section (Prominently Displayed) -->
    <div class="detail-section text-center" style="background: #f8f9fc; border-left: 4px solid #8B4513;">
        <div class="mb-2">
            <small class="text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fas fa-ticket-alt mr-1"></i>Booking Number Assigned by Admin
            </small>
        </div>
        <h2 class="mb-0" style="color: #8B4513; font-weight: 700; font-family: monospace; font-size: 2rem;">
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
            
            <?php if (!empty($booking['item_type'])): ?>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Item Type</div>
                <div class="detail-value"><?php echo htmlspecialchars($booking['item_type']); ?></div>
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
                    $paymentClass = [
                        'paid' => 'badge-success',
                        'partial' => 'badge-warning',
                        'unpaid' => 'badge-danger'
                    ][$booking['payment_status'] ?? 'unpaid'] ?? 'badge-secondary';
                    ?>
                    <span class="badge <?php echo $paymentClass; ?>">
                        <?php echo ucfirst($booking['payment_status'] ?? 'Unpaid'); ?>
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
            </div>
            <div class="col-md-6 mb-3">
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

    <!-- Actions -->
    <div class="detail-section text-right no-print" style="background: #f8f9fc;">
        <?php if ($booking['status'] === 'pending'): ?>
        <button type="button" class="btn btn-danger" onclick="confirmCancelBooking()">
            <i class="fas fa-times mr-1"></i> Cancel Booking
        </button>
        <?php endif; ?>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print Receipt
        </button>
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

<script>
function confirmCancelBooking() {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/cancelBooking/<?php echo $booking['id'] ?? 0; ?>';
    }
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

