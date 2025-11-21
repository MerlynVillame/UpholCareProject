<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">My Orders</h1>
<p class="mb-4">View and track all your repair and restoration orders.</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Orders List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Service</th>
                        <th>Item Description</th>
                        <th>Status</th>
                        <th>Pickup Date</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ORD-105</td>
                        <td>Car Seat Cover Repair</td>
                        <td>Toyota Vios front seat covers</td>
                        <td><span class="badge badge-warning">Pending</span></td>
                        <td>2024-01-15</td>
                        <td>₱3,500</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>ORD-098</td>
                        <td>Mattress Cover Repair</td>
                        <td>Queen size mattress cover</td>
                        <td><span class="badge badge-info">In Progress</span></td>
                        <td>2024-01-12</td>
                        <td>₱2,500</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>ORD-087</td>
                        <td>Sofa Reupholstering</td>
                        <td>3-seater sofa</td>
                        <td><span class="badge badge-success">Completed</span></td>
                        <td>2024-01-08</td>
                        <td>₱8,500</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
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

