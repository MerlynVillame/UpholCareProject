<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Manage Users</h1>
<p class="mb-4">View and manage all system users.</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        <a href="#" class="btn btn-primary-admin btn-sm" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-plus"></i> Add New User
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $usr): ?>
                        <tr>
                            <td><?php echo $usr['id']; ?></td>
                            <td><?php echo htmlspecialchars($usr['username']); ?></td>
                            <td><?php echo htmlspecialchars($usr['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($usr['email']); ?></td>
                            <td><?php echo htmlspecialchars($usr['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($usr['role'] === 'admin'): ?>
                                    <span class="text-danger font-weight-bold">Admin</span>
                                <?php else: ?>
                                    <span class="text-info font-weight-bold">Customer</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usr['status'] === 'active'): ?>
                                    <span class="text-success font-weight-bold">Active</span>
                                <?php else: ?>
                                    <span class="text-secondary font-weight-bold">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info text-white" style="background-color: var(--uphol-blue); border-color: var(--uphol-blue);"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-success" style="background-color: var(--uphol-green); border-color: var(--uphol-green);"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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

