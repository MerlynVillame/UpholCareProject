<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Business Booking History</h1>
        <p class="mb-0">Complete history of your business transactions and orders.</p>
    </div>
    <div>
        
        <a href="<?php echo BASE_URL; ?>customer/profile" class="btn btn-outline-primary">
            <i class="fas fa-user"></i> Back to Profile
        </a>
    </div>
</div>

<!-- Business Mode Notice -->
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i>
    <strong>Business History:</strong> This shows all your business bookings processed by admin. 
    Business bookings have priority processing and direct admin oversight.
</div>

<!-- Business Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Business Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($bookings); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Business Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ₱<?php echo number_format(array_sum(array_column($bookings, 'total_amount')), 2); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Completed Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count(array_filter($bookings, function($booking) { return $booking['status'] === 'completed'; })); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Admin Processed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count(array_filter($bookings, function($booking) { return in_array($booking['status'], ['admin_review', 'confirmed', 'in_progress', 'completed']); })); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Business Booking History Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">
            <i class="fas fa-history"></i> Business Booking History
        </h6>
    </div>
    <div class="card-body">
        <?php if (!empty($bookings)): ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="businessHistoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Booking #</th>
                            <th>Project Name</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Admin Notes</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-info">
                                        <?php echo htmlspecialchars($booking['booking_number']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['item_description']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($booking['service_name']); ?>
                                    <?php if (!empty($booking['service_type'])): ?>
                                        <br><span class="badge badge-secondary"><?php echo htmlspecialchars($booking['service_type']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-success">₱<?php echo number_format($booking['total_amount'], 2); ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($booking['status']) {
                                        case 'admin_review': 
                                            $statusClass = 'badge-warning'; 
                                            $statusText = 'Admin Review';
                                            break;
                                        case 'pending': 
                                            $statusClass = 'badge-warning'; 
                                            $statusText = 'Pending';
                                            break;
                                        case 'confirmed': 
                                            $statusClass = 'badge-info'; 
                                            $statusText = 'Confirmed';
                                            break;
                                        case 'in_progress': 
                                            $statusClass = 'badge-primary'; 
                                            $statusText = 'In Progress';
                                            break;
                                        case 'completed': 
                                            $statusClass = 'badge-success'; 
                                            $statusText = 'Completed';
                                            break;
                                        case 'cancelled': 
                                            $statusClass = 'badge-danger'; 
                                            $statusText = 'Cancelled';
                                            break;
                                        default: 
                                            $statusClass = 'badge-secondary'; 
                                            $statusText = ucfirst($booking['status']);
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                    <?php if ($booking['status'] === 'admin_review'): ?>
                                        <br><small class="text-muted">Under Admin Review</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($booking['admin_notes'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['admin_notes']); ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">No notes yet</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></strong>
                                    <br><small class="text-muted"><?php echo date('h:i A', strtotime($booking['created_at'])); ?></small>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $booking['id']; ?>" 
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($booking['status'] === 'completed'): ?>
                                        <a href="<?php echo BASE_URL; ?>customer/downloadInvoice/<?php echo $booking['id']; ?>" 
                                           class="btn btn-sm btn-success" title="Download Invoice">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Business Booking History</h5>
                <p class="text-muted">Start by creating your first business booking.</p>
                <a href="<?php echo BASE_URL; ?>customer/newBooking?mode=business" class="btn btn-success">
                    <i class="fas fa-plus"></i> Create Business Booking
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#businessHistoryTable').DataTable({
        "order": [[ 6, "desc" ]], // Sort by date descending
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": 7 } // Disable sorting on action column
        ]
    });
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
