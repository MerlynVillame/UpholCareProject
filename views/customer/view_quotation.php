<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
}

.quotation-detail-card {
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
    .quotation-detail-card, .quotation-detail-card * { visibility: visible; }
    .quotation-detail-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; }
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

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-info {
    background-color: #8B4513;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-secondary {
    background-color: #6c757d;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h1 class="page-title mb-2">Quotation Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/quotations">Quotations</a></li>
                <li class="breadcrumb-item active">Quotation Details</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>customer/quotations" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Quotations
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

<?php if ($quotation): ?>
<!-- Quotation Details Card -->
<div class="card quotation-detail-card">
    <div style="height: 4px; background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);"></div>
    
    <!-- Quotation Header -->
    <div class="detail-section" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-2" style="color: white;">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Quotation Details
                </h3>
                <p class="mb-0" style="opacity: 0.9;">
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('F d, Y h:i A', strtotime($quotation['created_at'])); ?>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <?php
                $statusClass = 'badge-secondary';
                $statusText = ucfirst($quotation['status']);
                
                switch ($quotation['status']) {
                    case 'draft':
                        $statusClass = 'badge-secondary';
                        $statusText = 'Draft';
                        break;
                    case 'sent':
                        $statusClass = 'badge-info';
                        $statusText = 'Sent';
                        break;
                    case 'accepted':
                        $statusClass = 'badge-success';
                        $statusText = 'Accepted';
                        break;
                    case 'rejected':
                        $statusClass = 'badge-danger';
                        $statusText = 'Rejected';
                        break;
                }
                ?>
                <span class="badge <?php echo $statusClass; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <?php echo $statusText; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Quotation Number Section (Prominently Displayed) -->
    <div class="detail-section text-center" style="background: #f8f9fc; border-left: 4px solid #8B4513;">
        <div class="mb-2">
            <small class="text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fas fa-file-invoice mr-1"></i>Quotation Number
            </small>
        </div>
        <h2 class="mb-0" style="color: #8B4513; font-weight: 700; font-family: monospace; font-size: 2rem;">
            <?php echo htmlspecialchars($quotation['quotation_number'] ?? 'N/A'); ?>
        </h2>
        <?php if ($quotation['valid_until']): ?>
        <p class="mt-2 mb-0" style="color: #5a5c69; font-size: 0.9rem;">
            <i class="fas fa-calendar-alt mr-1"></i>Valid until: <?php echo date('F d, Y', strtotime($quotation['valid_until'])); ?>
        </p>
        <?php endif; ?>
    </div>

    <!-- Quotation Amount Section -->
    <?php if ($quotation['total_amount']): ?>
    <div class="detail-section text-center" style="background: linear-gradient(135deg, rgba(139, 69, 19, 0.1) 0%, rgba(160, 82, 45, 0.1) 100%);">
        <div class="mb-2">
            <small class="text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                Total Amount
            </small>
        </div>
        <h2 class="mb-0" style="color: #8B4513; font-weight: 700; font-size: 2.5rem;">
            ₱<?php echo number_format($quotation['total_amount'], 2); ?>
        </h2>
    </div>
    <?php endif; ?>

    <!-- Booking Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-calendar-check mr-2"></i>Booking Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Booking Number</div>
                <div class="detail-value">
                    <strong style="color: #8B4513;">
                        <?php echo htmlspecialchars($quotation['booking_number'] ?? 'N/A'); ?>
                    </strong>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Booking Date</div>
                <div class="detail-value">
                    <?php echo $quotation['booking_date'] ? date('F d, Y', strtotime($quotation['booking_date'])) : 'N/A'; ?>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Booking Status</div>
                <div class="detail-value">
                    <?php
                    $bookingStatusClass = [
                        'pending' => 'badge-warning',
                        'confirmed' => 'badge-info',
                        'in_progress' => 'badge-primary',
                        'completed' => 'badge-success',
                        'cancelled' => 'badge-danger'
                    ][$quotation['booking_status'] ?? 'pending'] ?? 'badge-secondary';
                    ?>
                    <span class="badge <?php echo $bookingStatusClass; ?>">
                        <?php echo ucwords(str_replace('_', ' ', $quotation['booking_status'] ?? 'Pending')); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-tools mr-2"></i>Service Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Service Name</div>
                <div class="detail-value"><?php echo htmlspecialchars($quotation['service_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Service Type</div>
                <div class="detail-value"><?php echo htmlspecialchars($quotation['service_type'] ?? 'N/A'); ?></div>
            </div>
            <?php if (!empty($quotation['service_description'])): ?>
            <div class="col-md-12 mb-3">
                <div class="detail-label">Service Description</div>
                <div class="detail-value"><?php echo htmlspecialchars($quotation['service_description']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quotation Notes -->
    <?php if (!empty($quotation['notes'])): ?>
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-sticky-note mr-2"></i>Notes
        </h5>
        <div class="detail-value">
            <?php echo nl2br(htmlspecialchars($quotation['notes'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Customer Information -->
    <div class="detail-section">
        <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-user mr-2"></i>Customer Information
        </h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="detail-label">Name</div>
                <div class="detail-value"><?php echo htmlspecialchars($quotation['customer_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Email</div>
                <div class="detail-value"><?php echo htmlspecialchars($quotation['customer_email'] ?? 'N/A'); ?></div>
            </div>
            <?php if (!empty($quotation['customer_phone'])): ?>
            <div class="col-md-6 mb-3">
                <div class="detail-label">Phone</div>
                <div class="detail-value"><?php echo htmlspecialchars($quotation['customer_phone']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="detail-section text-right no-print" style="background: #f8f9fc;">
        <?php if ($quotation['status'] === 'sent'): ?>
            <button type="button" class="btn btn-primary btn-lg" onclick="acceptQuotation(<?php echo $quotation['id']; ?>)">
                <i class="fas fa-check mr-2"></i> Accept Quotation
            </button>
        <?php elseif ($quotation['status'] === 'accepted'): ?>
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle mr-2"></i>This quotation has been accepted.
            </div>
        <?php elseif ($quotation['status'] === 'rejected'): ?>
            <div class="alert alert-danger mb-0">
                <i class="fas fa-times-circle mr-2"></i>This quotation has been rejected.
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle mr-2"></i>This quotation is being prepared. Please wait for it to be sent.
            </div>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary ml-2" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print Quotation
        </button>
    </div>
</div>

<?php else: ?>
<!-- Quotation Not Found -->
<div class="card quotation-detail-card">
    <div class="card-body text-center py-5">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h4>Quotation Not Found</h4>
        <p class="text-muted">The quotation you're looking for doesn't exist or you don't have permission to view it.</p>
        <a href="<?php echo BASE_URL; ?>customer/quotations" class="btn btn-primary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Quotations
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Success/Error Alert (for AJAX) -->
<div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<script>
function acceptQuotation(quotationId) {
    if (!confirm('Are you sure you want to accept this quotation? This action cannot be undone.')) {
        return;
    }
    
    // Show loading state
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Accepting...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>customer/acceptQuotation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'quotation_id=' + quotationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // Reload page after 1 second
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('danger', data.message);
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while accepting the quotation.');
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.minWidth = '300px';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    alertContainer.appendChild(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

