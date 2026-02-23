<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Super Admin Dashboard';
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
                <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">Super Admin Dashboard</h1>
                <p class="text-muted smaller mb-0">System governance and administrative oversight.</p>
            </div>
            <div class="d-none d-md-block">
                
            </div>
        </div>
    </div>
</div>


<!-- Modern Statistics Row -->
<div class="row">
    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; border-top: 4px solid #3498DB !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-light-blue mr-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted tracking-wider mb-1">Total Customers</div>
                        <div class="h4 mb-0 font-weight-bold text-dark"><?= number_format($data['stats']['total_customers'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Admins -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; border-top: 4px solid #2ECC71 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-light-success mr-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-shield text-success"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted tracking-wider mb-1">Active Admins</div>
                        <div class="h4 mb-0 font-weight-bold text-dark"><?= number_format($data['stats']['total_active_admins'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Admins -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; border-top: 4px solid #E74C3C !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-light-danger mr-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-clock text-danger"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted tracking-wider mb-1">Pending Admins</div>
                        <div class="h4 mb-0 font-weight-bold text-dark"><?= number_format($data['stats']['pending_admin_registrations'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Items Multi-Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; border-top: 4px solid #F1C40F !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stats-icon bg-light-warning mr-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-tasks text-warning"></i>
                    </div>
                    <div class="text-xs font-weight-bold text-uppercase text-muted tracking-wider">Queue Status</div>
                </div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="small">
                        <div class="mb-1"><span class="font-weight-bold text-dark"><?= $data['stats']['pending_business_registrations'] ?? 0 ?></span> <span class="text-muted">Businesses</span></div>
                        <div><span class="font-weight-bold text-dark"><?= $data['stats']['pending_customer_accounts'] ?? 0 ?></span> <span class="text-muted">Customers</span></div>
                    </div>
                    <a href="<?= BASE_URL ?>control-panel/businessRegistrations" class="btn btn-outline-warning btn-sm border-0 bg-light-warning px-3 font-weight-bold" style="font-size: 0.7rem; border-radius: 8px;">Action</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Registrations Minimalism -->
<?php if (!empty($data['pending_admins'])): ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header py-3 bg-white border-0 d-flex align-items-center text-dark">
        <div class="bg-primary-soft p-2 rounded mr-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-user-clock text-primary small"></i>
        </div>
        <h6 class="m-0 font-weight-bold">Pending Admin Registrations</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" width="100%" cellspacing="0">
                <thead class="bg-light text-gray-600" style="font-size: 0.75rem;">
                    <tr>
                        <th class="border-0 px-4">FULL NAME</th>
                        <th class="border-0">EMAIL ADDRESS</th>
                        <th class="border-0">USERNAME</th>
                        <th class="border-0">DATE REGISTERED</th>
                        <th class="border-0 text-center px-4">ACTION</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php foreach ($data['pending_admins'] as $reg): ?>
                        <tr>
                            <td class="px-4 py-3 align-middle font-weight-bold text-dark"><?= htmlspecialchars($reg['fullname']) ?></td>
                            <td class="py-3 align-middle"><?= htmlspecialchars($reg['email']) ?></td>
                            <td class="py-3 align-middle"><code class="bg-light px-2 rounded"><?= htmlspecialchars($reg['username']) ?></code></td>
                            <td class="py-3 align-middle text-muted"><?= date('M d, Y', strtotime($reg['created_at'])) ?></td>
                            <td class="text-center px-4 py-3 align-middle">
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>control-panel/approveAdmin/<?= $reg['id'] ?>" 
                                       class="btn btn-sm btn-success rounded-circle shadow-sm mr-2"
                                       style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"
                                       onclick="return confirm('Approve this admin registration?')">
                                        <i class="fas fa-check fa-xs"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger rounded-circle shadow-sm" 
                                            style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"
                                            onclick="rejectAdmin(<?= $reg['id'] ?>)">
                                        <i class="fas fa-times fa-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
function rejectAdmin(id) {
    const reason = prompt('Enter rejection reason:');
    if (reason) {
        $('<form method="POST" action="<?= BASE_URL ?>control-panel/rejectAdmin/' + id + '">' +
          '<input type="hidden" name="reason" value="' + reason + '">' +
          '</form>').appendTo('body').submit();
    }
}
</script>

