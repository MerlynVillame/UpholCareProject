<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-archive mr-2"></i>Archived Bookings
        </h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Archived Bookings List
                    </h6>
                </div>
                <div class="card-body">
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
                        <table class="table table-bordered table-striped" id="archivedBookingsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Booking #</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <!-- <th>Actions</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bookings)): ?>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <span class="text-primary-admin font-weight-bold">Booking #<?php echo htmlspecialchars($booking['id']); ?></span>
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
                                                <span><!-- Category: --><?php echo htmlspecialchars($booking['category_name']); ?></span>
                                            </td>
                                            <td>
                                                <span style="color: #6c757d; font-weight: bold;">
                                                    <i class="fas fa-archive mr-1"></i>Archived
                                                </span>
                                            </td>
                                            <td>
                                                <span class="date-info"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
                                            </td>
                                            <!-- 
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete permanently?')) window.location.href='<?php echo BASE_URL; ?>admin/deleteBooking/<?php echo $booking['id']; ?>'">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td> 
                                            -->
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="empty-state">
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-archive fa-3x text-gray-300 mb-3"></i>
                                            <br><span class="text-muted">No archived bookings found</span>
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
<!-- /.container-fluid -->

<!-- DataTables -->
<link href="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#archivedBookingsTable').DataTable({
        "order": [[ 5, "desc" ]] // Sort by Date column descending
    });
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
