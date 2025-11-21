<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Booking History</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item active">History</li>
            </ol>
        </nav>
    </div>
</div>

<!-- History Filter -->
<div class="row mb-4">
    <div class="col-md-3">
        <select class="form-control">
            <option>All Time</option>
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
            <option>Last 3 Months</option>
            <option>Last Year</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control">
            <option>All Statuses</option>
            <option>Completed</option>
            <option>Cancelled</option>
        </select>
    </div>
</div>

<!-- History Timeline -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Booking History</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $booking): ?>
        <div class="border-left-primary shadow mb-3 p-3" style="border-left: 4px solid;">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="text-xs text-muted">
                        <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                    </div>
                    <div class="font-weight-bold text-primary">
                        <?php echo htmlspecialchars($booking['booking_number'] ?? 'N/A'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="font-weight-bold"><?php echo htmlspecialchars($booking['service_name'] ?? 'Unknown Service'); ?></div>
                    <div class="small text-muted"><?php echo htmlspecialchars($booking['item_description'] ?? 'No description'); ?></div>
                </div>
                <div class="col-md-2">
                    <?php
                    $statusClass = 'badge-' . str_replace('_', '-', $booking['status'] ?? 'pending');
                    $statusText = ucwords(str_replace('_', ' ', $booking['status'] ?? 'pending'));
                    ?>
                    <span class="badge <?php echo $statusClass; ?>">
                        <?php echo $statusText; ?>
                    </span>
                </div>
                <div class="col-md-2">
                    <div class="font-weight-bold">₱<?php echo number_format($booking['total_amount'] ?? 0, 2); ?></div>
                    <div class="small text-muted"><?php echo ucfirst($booking['payment_status'] ?? 'unpaid'); ?></div>
                </div>
                <div class="col-md-2 text-right">
                    <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $booking['id']; ?>" 
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-500">No booking history yet</h5>
            <p class="text-muted">Your completed bookings will appear here</p>
        </div>
        <?php endif; ?>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

