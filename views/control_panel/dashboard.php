<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Control Panel Dashboard';
$user = $data['admin'] ?? [];

require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'control_panel_sidebar.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; 
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0" style="color: #0F3C5F; font-weight: 700;">Dashboard Overview</h1>
    <p class="text-muted mb-0">Monitor login activities and system access</p>
</div>

<!-- Statistics Row -->
<div class="row">
    <!-- Total Logins Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Logins Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['today']['total'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Successful Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Successful Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['today']['successful'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Failed Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['today']['failed'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Week -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            This Week</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['week']['total'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics by User Type -->
<h5 class="h6 mb-3 font-weight-bold text-gray-800"><i class="fas fa-users mr-2"></i>Login Statistics by User Type</h5>
<div class="row mb-4">
    <?php foreach ($data['stats']['by_type'] as $typeStats): ?>
        <div class="col-md-4 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="text-uppercase mb-3 font-weight-bold">
                        <?php if ($typeStats['user_type'] === 'customer'): ?>
                            <span class="text-info"><i class="fas fa-user mr-1"></i> Customer</span>
                        <?php elseif ($typeStats['user_type'] === 'admin'): ?>
                            <span class="text-primary"><i class="fas fa-user-shield mr-1"></i> Admin</span>
                        <?php else: ?>
                            <span class="text-danger"><i class="fas fa-shield-alt mr-1"></i> Control Panel</span>
                        <?php endif; ?>
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-gray-600">Total Logins:</span>
                        <strong class="text-gray-800"><?= $typeStats['total'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-success">Successful:</span>
                        <strong class="text-success"><?= $typeStats['successful'] ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-danger">Failed:</span>
                        <strong class="text-danger"><?= $typeStats['failed'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Recent Login Activity -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Recent Login Activity</h6>
                <a href="<?= BASE_URL ?>control-panel/loginLogs" class="btn btn-primary btn-sm">
                    <i class="fas fa-list fa-sm mr-1"></i> View All Logs
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="loginTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Type</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Failure Reason</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['recent_logins'] as $log): ?>
                                <tr>
                                    <td><?= $log['id'] ?></td>
                                    <td>
                                        <?php 
                                        $uRole = $log['user_role'] ?? '';
                                        $aRole = $log['admin_role'] ?? '';
                                        
                                        if ($log['user_type'] === 'customer' || $uRole === 'customer'): ?>
                                            <span class="badge badge-info">Customer</span>
                                        <?php elseif ($log['user_type'] === 'admin' || $uRole === 'admin'): ?>
                                            <span class="badge badge-primary">Admin</span>
                                        <?php elseif ($aRole === 'super_admin'): ?>
                                            <span class="badge badge-danger">Super Admin</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Control Panel</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($log['email']) ?></td>
                                    <td><?= htmlspecialchars($log['fullname'] ?? 'N/A') ?></td>
                                    <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                                    <td>
                                        <?php if ($log['login_status'] === 'success'): ?>
                                            <span class="text-success small font-weight-bold">
                                                <i class="fas fa-check-circle"></i> Success
                                            </span>
                                        <?php else: ?>
                                            <span class="text-danger small font-weight-bold">
                                                <i class="fas fa-times-circle"></i> Failed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?= htmlspecialchars($log['failure_reason'] ?? '-') ?></small></td>
                                    <td><?= date('M d, Y H:i', strtotime($log['login_time'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
    $(document).ready(function() {
        // DataTable initialization is handled in footer.php for any table with id loginTable
        // But we can add specific options here if needed once it's initialized
    });
</script>


