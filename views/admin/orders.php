<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<?php
// Sample reservation data - Replace with database queries in production
$reservationsData = [
    [
        'id' => 1,
        'customer_name' => 'Maria Santos',
        'customer_phone' => '0917-234-5678',
        'customer_email' => 'maria.santos@email.com',
        'furniture_type' => 'Sofa',
        'furniture_details' => '3-seater leather sofa, beige',
        'pickup_date' => '2024-10-25',
        'pickup_location' => 'Makati, Manila',
        'total_amount' => 5500,
        'status' => 'pending',
        'created_date' => '2024-10-15'
    ],
    [
        'id' => 2,
        'customer_name' => 'Juan Dela Cruz',
        'customer_phone' => '0918-345-6789',
        'customer_email' => 'juan.delacruz@email.com',
        'furniture_type' => 'Bedroom Set',
        'furniture_details' => 'Complete bedroom set - bed, nightstands, dresser',
        'pickup_date' => '2024-10-20',
        'pickup_location' => 'Quezon City',
        'total_amount' => 8200,
        'status' => 'completed',
        'created_date' => '2024-10-10'
    ],
    [
        'id' => 3,
        'customer_name' => 'Ana Garcia',
        'customer_phone' => '0919-456-7890',
        'customer_email' => 'ana.garcia@email.com',
        'furniture_type' => 'Armchair',
        'furniture_details' => 'Modern armchair with ottoman, gray fabric',
        'pickup_date' => '2024-10-28',
        'pickup_location' => 'Pasig, Manila',
        'total_amount' => 3200,
        'status' => 'pending',
        'created_date' => '2024-10-16'
    ],
    [
        'id' => 4,
        'customer_name' => 'Pedro Reyes',
        'customer_phone' => '0920-567-8901',
        'customer_email' => 'pedro.reyes@email.com',
        'furniture_type' => 'Dining Chairs (6)',
        'furniture_details' => 'Set of 6 wooden dining chairs',
        'pickup_date' => '2024-10-22',
        'pickup_location' => 'Mandaluyong',
        'total_amount' => 6800,
        'status' => 'pending',
        'created_date' => '2024-10-12'
    ],
    [
        'id' => 5,
        'customer_name' => 'Rosa Fernandez',
        'customer_phone' => '0921-678-9012',
        'customer_email' => 'rosa.fernandez@email.com',
        'furniture_type' => 'Ottoman',
        'furniture_details' => 'Storage ottoman, brown leather',
        'pickup_date' => '2024-10-18',
        'pickup_location' => 'Taguig',
        'total_amount' => 2100,
        'status' => 'completed',
        'created_date' => '2024-10-08'
    ],
    [
        'id' => 6,
        'customer_name' => 'Carlo Mendez',
        'customer_phone' => '0922-789-0123',
        'customer_email' => 'carlo.mendez@email.com',
        'furniture_type' => 'Sectional Couch',
        'furniture_details' => 'Large L-shaped sectional, gray fabric',
        'pickup_date' => '2024-11-02',
        'pickup_location' => 'Las Piñas',
        'total_amount' => 9500,
        'status' => 'pending',
        'created_date' => '2024-10-17'
    ]
];

// Calculate statistics
$totalReservations = count($reservationsData);
$completedCount = 0;
$pendingCount = 0;
$totalRevenue = 0;

foreach ($reservationsData as $res) {
    $totalRevenue += $res['total_amount'];
    if ($res['status'] === 'completed') {
        $completedCount++;
    } elseif ($res['status'] === 'pending') {
        $pendingCount++;
    }
}

// Get filter parameters
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 'all';

// Filter reservations based on search and status
$filteredReservations = $reservationsData;

if (!empty($searchTerm)) {
    $searchLower = strtolower($searchTerm);
    $filteredReservations = array_filter($filteredReservations, function($res) use ($searchLower) {
        return strpos(strtolower($res['customer_name']), $searchLower) !== false ||
               strpos($res['customer_phone'], $searchLower) !== false ||
               strpos(strtolower($res['furniture_type']), $searchLower) !== false;
    });
}

if ($statusFilter !== 'all') {
    $filteredReservations = array_filter($filteredReservations, function($res) use ($statusFilter) {
        return $res['status'] === $statusFilter;
    });
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Furniture Reservations</h1>
        <p class="mb-0 text-muted">Manage customer pickup reservations and schedules</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Reservations</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalReservations; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completedCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingCount; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-start fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($totalRevenue, 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-8 mb-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by customer name, phone, or furniture type..." 
                           value="<?php echo $searchTerm; ?>">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="<?php echo BASE_URL; ?>admin/orders" class="btn btn-sm btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Reservations Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Reservations List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Furniture Type</th>
                        <th>Details</th>
                        <th>Pickup Date</th>
                        <th>Location</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($filteredReservations) > 0): ?>
                        <?php foreach ($filteredReservations as $reservation): ?>
                            <tr>
                                <td><?php echo $reservation['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($reservation['customer_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($reservation['customer_phone']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['furniture_type']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['furniture_details']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($reservation['pickup_date'])); ?></td>
                                <td><?php echo htmlspecialchars($reservation['pickup_location']); ?></td>
                                <td><strong>₱<?php echo number_format($reservation['total_amount'], 2); ?></strong></td>
                                <td>
                                    <?php
                                    $badgeClass = '';
                                    switch($reservation['status']) {
                                        case 'completed':
                                            $badgeClass = 'badge-success';
                                            break;
                                        case 'pending':
                                            $badgeClass = 'badge-warning';
                                            break;
                                        case 'cancelled':
                                            $badgeClass = 'badge-danger';
                                            break;
                                        default:
                                            $badgeClass = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                        </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-success" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($reservation['status'] === 'pending'): ?>
                                        <a href="#" class="btn btn-sm btn-primary" title="Mark Complete">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                        </td>
                    </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <p>No reservations found.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

