
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.btn-primary {
    background: var(--uphol-blue);
    border-color: var(--uphol-blue);
    color: white !important;
}
.btn-primary:hover {
    background: var(--uphol-navy);
    border-color: var(--uphol-navy);
    color: white !important;
}
.card-header {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}
.archive-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(108,117,125,0.12);
    color: #6c757d;
    font-weight: 600;
    font-size: 12px;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid rgba(108,117,125,0.3);
}
.restore-btn {
    background: linear-gradient(135deg, #0F3C5F, #1a6fad);
    border: none;
    color: white;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-size: 13px;
}
.restore-btn:hover {
    background: linear-gradient(135deg, #1a6fad, #0F3C5F);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(15,60,95,0.3);
}
.empty-archive {
    text-align: center;
    padding: 60px 20px;
    color: #adb5bd;
}
.empty-archive i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
    opacity: 0.4;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Archived Services</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/services">Services</a></li>
                <li class="breadcrumb-item active">Archived Services</li>
            </ol>
        </nav>
    </div>
    <a href="<?php echo BASE_URL; ?>admin/services" class="btn btn-primary-admin">
        <i class="fas fa-tools mr-2"></i>Back to Services
    </a>
</div>

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

<!-- Info Banner -->
<div class="alert" style="background: linear-gradient(135deg,#fff3cd,#ffeeba); border-left: 4px solid #ffc107; border-radius: 8px; margin-bottom: 24px;">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle mr-3" style="color:#856404; font-size:20px;"></i>
        <div>
            <strong style="color:#856404;">Archived Services</strong>
            <p class="mb-0" style="color:#6d5208; font-size:13px;">
                Services listed here have been removed from the active catalog. They cannot be booked by customers. You can restore any service to make it active again.
            </p>
        </div>
    </div>
</div>

<!-- Archived Services Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-archive mr-2"></i>Archived Services
            <span class="badge badge-light ml-2" style="font-size:13px;"><?php echo count($archivedServices); ?></span>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Service Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($archivedServices)): ?>
                        <?php foreach ($archivedServices as $service): ?>
                        <tr>
                            <td><?php echo $service['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($service['service_name']); ?></strong></td>
                            <td>
                                <?php if (!empty($service['category_name'])): ?>
                                    <span class="text-muted"><?php echo htmlspecialchars($service['category_name']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">No Category</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($service['service_type'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($service['price']): ?>
                                    <strong class="text-muted">₱<?php echo number_format($service['price'], 2); ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">Not Set</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="archive-badge">
                                    <i class="fas fa-archive"></i> Archived
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($service['created_at'])); ?></td>
                            <td>
                                <button type="button" class="restore-btn" onclick="restoreService(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars($service['service_name']); ?>')" title="Restore to Active">
                                    <i class="fas fa-undo mr-1"></i>Restore
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-archive">
                                    <i class="fas fa-archive"></i>
                                    <h5 style="color:#adb5bd; font-weight:600;">No Archived Services</h5>
                                    <p style="font-size:14px;">When you remove a service from the active list, it will appear here.</p>
                                    <a href="<?php echo BASE_URL; ?>admin/services" class="btn btn-primary-admin mt-2">
                                        <i class="fas fa-tools mr-2"></i>Go to Services
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<script>
function restoreService(serviceId, serviceName) {
    if (!confirm('Restore "' + serviceName + '" back to active services?\n\nIt will become visible in the service catalog again.')) {
        return;
    }

    const formData = new FormData();
    formData.append('service_id', serviceId);

    fetch('<?php echo BASE_URL; ?>admin/restoreService', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message + ' Redirecting...');
            setTimeout(() => {
                window.location.href = '<?php echo BASE_URL; ?>admin/services';
            }, 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while restoring the service.');
    });
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.cssText = 'min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" onclick="this.parentElement.remove()">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    alertContainer.appendChild(alert);

    setTimeout(() => {
        if (alert.parentNode) alert.remove();
    }, 5000);
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
