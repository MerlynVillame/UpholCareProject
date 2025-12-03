<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Business Bookings</h1>
        <p class="mb-0">Your business bookings are processed by admin. View status and details here.</p>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>customer/newBooking" class="btn btn-success">
            <i class="fas fa-plus"></i> New Business Booking
        </a>
        <a href="<?php echo BASE_URL; ?>customer/profile" class="btn btn-outline-primary">
            <i class="fas fa-user"></i> Back to Profile
        </a>
    </div>
</div>

<!-- Admin Processing Notice -->
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i>
    <strong>Business Mode Notice:</strong> Your business bookings are sent directly to admin for processing. 
    You will receive updates on the status of your orders. Business bookings have priority processing.
</div>

<!-- Business Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Business Bookings</div>
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
                            Pending Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count(array_filter($bookings, function($booking) { return $booking['status'] === 'pending'; })); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                            Active Projects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count(array_filter($bookings, function($booking) { return in_array($booking['status'], ['confirmed', 'in_progress']); })); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Business Bookings Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">
            <i class="fas fa-briefcase"></i> Business Bookings
        </h6>
    </div>
    <div class="card-body">
        <?php if (!empty($bookings)): ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Booking #</th>
                            <th>Project Name</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
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
                                    switch($booking['status']) {
                                        case 'pending': $statusClass = 'badge-warning'; break;
                                        case 'confirmed': $statusClass = 'badge-info'; break;
                                        case 'in_progress': $statusClass = 'badge-primary'; break;
                                        case 'completed': $statusClass = 'badge-success'; break;
                                        case 'cancelled': $statusClass = 'badge-danger'; break;
                                        default: $statusClass = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $booking['id']; ?>" 
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Business Bookings Yet</h5>
                <p class="text-muted">Start by creating your first business booking.</p>
                <a href="<?php echo BASE_URL; ?>customer/newBooking" class="btn btn-success">
                    <i class="fas fa-plus"></i> Create Business Booking
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 5, "desc" ]], // Sort by date descending
        "pageLength": 10
    });
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
