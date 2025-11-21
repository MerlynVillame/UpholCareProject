<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
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
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Quotations</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Quotations</li>
            </ol>
        </nav>
    </div>
    <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i>Request Quotation from Booking
    </a>
</div>

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

<!-- Quotations List -->
<div class="card shadow mb-4">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white;">
        <h6 class="m-0 font-weight-bold">My Quotations</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($quotations)): ?>
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Quotation ID</th>
                        <th>Booking Number</th>
                        <th>Service</th>
                        <th>Date Requested</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Valid Until</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotations as $quotation): ?>
                    <tr>
                        <td><strong class="text-primary"><?php echo htmlspecialchars($quotation['quotation_number'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($quotation['booking_number'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($quotation['service_name'] ?? 'N/A'); ?></td>
                        <td><?php echo date('M d, Y', strtotime($quotation['created_at'])); ?></td>
                        <td class="font-weight-bold">
                            <?php if ($quotation['total_amount']): ?>
                                ₱<?php echo number_format($quotation['total_amount'], 2); ?>
                            <?php else: ?>
                                <span class="text-muted">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
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
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </td>
                        <td>
                            <?php if ($quotation['valid_until']): ?>
                                <?php echo date('M d, Y', strtotime($quotation['valid_until'])); ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info mr-1" onclick="viewQuotation(<?php echo $quotation['id']; ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <?php if ($quotation['status'] === 'sent'): ?>
                                <button class="btn btn-sm btn-primary" onclick="acceptQuotation(<?php echo $quotation['id']; ?>)">
                                    <i class="fas fa-check"></i> Accept
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-file-invoice fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
            <h5 class="text-muted">No quotations yet</h5>
            <p class="text-muted mb-3">Request a quotation from one of your bookings to get started</p>
            <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-primary">
                <i class="fas fa-calendar-check mr-2"></i>View My Bookings
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Success/Error Alert (for AJAX) -->
<div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<script>
function viewQuotation(quotationId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/viewQuotation?id=' + quotationId;
}

function acceptQuotation(quotationId) {
    if (!confirm('Are you sure you want to accept this quotation? This action cannot be undone.')) {
        return;
    }
    
    // Show loading state
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Accepting...';
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
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    alertContainer.appendChild(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

