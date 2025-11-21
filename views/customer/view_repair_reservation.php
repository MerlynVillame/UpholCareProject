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
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h1 class="page-title mb-2">Repair Reservation Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/bookings">Bookings</a></li>
                <li class="breadcrumb-item active">Repair Reservation Details</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Bookings
        </a>
    </div>
</div>

<?php if ($repairItem): ?>
<!-- Repair Reservation Details Card -->
<div class="card booking-detail-card">
    <div style="height: 4px; background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);"></div>
    
    <!-- Reservation Header -->
    <div class="detail-section" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-2" style="color: white;">
                    <i class="fas fa-tools mr-2"></i>Repair Reservation Details
                </h3>
                <p class="mb-0" style="opacity: 0.9;">
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('F d, Y h:i A', strtotime($repairItem['created_at'])); ?>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <?php
                $statusClass = [
                    'pending' => 'badge-warning',
                    'approved' => 'badge-success',
                    'in_progress' => 'badge-primary',
                    'completed' => 'badge-success',
                    'cancelled' => 'badge-danger',
                    'quoted' => 'badge-info'
                ][$repairItem['status']] ?? 'badge-secondary';
                ?>
                <span class="badge <?php echo $statusClass; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <?php echo ucwords(str_replace('_', ' ', $repairItem['status'])); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Booking Number Section (Prominently Displayed) -->
    <div class="detail-section text-center" style="background: #f8f9fc; border-left: 4px solid #f5576c;">
        <div class="mb-2">
            <small class="text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fas fa-ticket-alt mr-1"></i>Booking Number Assigned by Admin
            </small>
        </div>
        <h2 class="mb-0" style="color: #f5576c; font-weight: 700; font-family: monospace; font-size: 2rem;">
            <?php if (!empty($repairItem['booking_number'])): ?>
                <?php echo htmlspecialchars($repairItem['booking_number']); ?>
            <?php else: ?>
                <span class="text-muted" style="font-size: 1.25rem;">Pending Assignment</span>
                <br>
                <small style="font-size: 0.9rem; font-weight: normal;">Admin will review your reservation and assign a booking number</small>
            <?php endif; ?>
        </h2>
        <?php if (!empty($repairItem['booking_number'])): ?>
        <p class="mt-2 mb-0" style="color: #5a5c69; font-size: 0.9rem;">
            <i class="fas fa-info-circle mr-1"></i>Please keep this booking number for your records. You can view your receipt anytime.
        </p>
        <?php endif; ?>
    </div>

    <!-- Item Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-box mr-2"></i>Item Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Item Name</div>
                <div class="detail-value"><?php echo htmlspecialchars($repairItem['item_name']); ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Item Type</div>
                <div class="detail-value">
                    <span class="badge badge-secondary"><?php echo ucfirst($repairItem['item_type']); ?></span>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="detail-label">Description</div>
                <div class="detail-value"><?php echo nl2br(htmlspecialchars($repairItem['item_description'])); ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Urgency Level</div>
                <div class="detail-value">
                    <?php
                    $urgencyClass = [
                        'low' => 'badge-secondary',
                        'normal' => 'badge-info',
                        'high' => 'badge-warning',
                        'urgent' => 'badge-danger'
                    ][$repairItem['urgency']] ?? 'badge-secondary';
                    ?>
                    <span class="badge <?php echo $urgencyClass; ?>"><?php echo ucfirst($repairItem['urgency']); ?></span>
                </div>
            </div>
            <?php if (!empty($repairItem['estimated_cost'])): ?>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Estimated Cost</div>
                <div class="detail-value" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50;">
                    ₱<?php echo number_format($repairItem['estimated_cost'], 2); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-user mr-2"></i>Your Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Name</div>
                <div class="detail-value"><?php echo htmlspecialchars($repairItem['customer_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Email</div>
                <div class="detail-value"><?php echo htmlspecialchars($repairItem['email'] ?? 'N/A'); ?></div>
            </div>
            <?php if (!empty($repairItem['phone'])): ?>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Phone</div>
                <div class="detail-value"><?php echo htmlspecialchars($repairItem['phone']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($repairItem['admin_notes'])): ?>
    <!-- Admin Notes -->
    <div class="detail-section" style="background: #fff3cd; border-left: 4px solid #ffc107;">
        <h5 class="mb-3" style="color: #856404; font-weight: 700;">
            <i class="fas fa-comment-alt mr-2"></i>Admin Notes
        </h5>
        <div class="detail-value" style="color: #856404;">
            <?php echo nl2br(htmlspecialchars($repairItem['admin_notes'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="detail-section text-right no-print" style="background: #f8f9fc;">
        <?php if ($repairItem['status'] === 'approved' && !empty($repairItem['booking_number'])): ?>
        <button type="button" class="btn btn-info" onclick="viewReceipt()">
            <i class="fas fa-receipt mr-1"></i> View Receipt
        </button>
        <?php endif; ?>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print Details
        </button>
    </div>
</div>

<script>
function viewReceipt() {
    const repairItemId = <?php echo $repairItem['id']; ?>;
    viewRepairReceipt(repairItemId);
}
</script>

<?php else: ?>
<!-- Reservation Not Found -->
<div class="card booking-detail-card">
    <div class="card-body text-center py-5">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h4>Reservation Not Found</h4>
        <p class="text-muted">The repair reservation you're looking for doesn't exist or you don't have permission to view it.</p>
        <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-primary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Bookings
        </a>
    </div>
</div>
<?php endif; ?>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

