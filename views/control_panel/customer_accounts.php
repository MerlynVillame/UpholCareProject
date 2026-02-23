<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Customer Accounts';
$user = $data['admin'] ?? [];

require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'control_panel_sidebar.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; 
?>

<!-- Modern Page Header Container -->
<div class="card border-0 module-card mb-4" style="background: white;">
    <div class="card-body py-3 px-4">
        <div class="d-sm-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">Customer Accounts</h1>
                <p class="text-muted smaller mb-0">Management of user directory and verifications.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                    <i class="fas fa-users mr-2 text-info small"></i>
                    <span class="smaller font-weight-bold text-dark">User Directory</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>control-panel/customerAccounts" class="form-inline">
            <div class="form-group mr-3">
                <label for="status" class="mr-2 small font-weight-bold text-gray-600">Status:</label>
                <select name="status" id="status" class="form-control form-control-sm">
                    <option value="all" <?= $data['filter_status'] === 'all' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="active" <?= $data['filter_status'] === 'active' ? 'selected' : '' ?>>Active (Approved)</option>
                    <option value="inactive" <?= $data['filter_status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Pending)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-search fa-sm"></i> Filter
            </button>
            <a href="<?= BASE_URL ?>control-panel/customerAccounts" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo fa-sm"></i> Reset
            </a>
        </form>
    </div>
</div>

<!-- Customer Accounts Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users mr-2"></i> 
            Customer Accounts (<?= count($data['customers']) ?> Total)
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="customerAccountsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['customers'])): ?>
                        <?php foreach ($data['customers'] as $index => $customer): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($customer['fullname']) ?></strong></td>
                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                <td><?= htmlspecialchars($customer['username']) ?></td>
                                <td><?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($customer['status'] === 'active'): ?>
                                        <span class="text-success small font-weight-bold">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="text-warning small font-weight-bold">
                                            <i class="fas fa-clock"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if ($customer['status'] === 'inactive'): ?>
                                            <a href="<?= BASE_URL ?>control-panel/approveCustomer/<?= $customer['id'] ?>" 
                                               class="btn btn-sm btn-success"
                                               onclick="return confirm('Approve this customer account?')">
                                                <i class="fas fa-check fa-sm"></i> Approve
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="rejectCustomer(<?= $customer['id'] ?>)">
                                                <i class="fas fa-times fa-sm"></i> Reject
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="rejectCustomer(<?= $customer['id'] ?>)">
                                                <i class="fas fa-ban fa-sm"></i> Deactivate
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
    function rejectCustomer(id) {
        confirm('Confirm rejection/deactivation?').then((confirmed) => {
            if (confirmed) {
                prompt('Enter reason:').then((reason) => {
                    if (reason && reason.trim()) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?= BASE_URL ?>control-panel/rejectCustomer/' + id;
                        
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'reason';
                        input.value = reason.trim();
                        
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        // DataTable initialization handled by footer.php
    });
</script>

