<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Banned Stores';
$user = $data['admin'] ?? [];

require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'control_panel_sidebar.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; 
?>

<!-- Modern Page Header Container -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white; border: 1px solid rgba(44, 62, 80, 0.05) !important;">
    <div class="card-body py-3 px-4">
        <div class="d-sm-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">System Restrictions</h1>
                <p class="text-muted smaller mb-0">Registry of restricted business entities.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                    <i class="fas fa-ban mr-2 text-danger small"></i>
                    <span class="smaller font-weight-bold text-dark">Access Control</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Banned</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['total_banned_stores'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ban fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Permanent</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['permanently_banned'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-lock fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Temporary</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['temporarily_banned'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Expired</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['expired_bans'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-end fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-gray-600">Ban Type:</label>
                    <select class="form-control form-control-sm shadow-sm" id="filterBanType" onchange="filterBannedStores()">
                        <option value="all">All Banned Stores</option>
                        <option value="permanent">Permanent Bans</option>
                        <option value="temporary">Temporary Bans</option>
                        <option value="expired">Expired Bans</option>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-gray-600">Search Store:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control shadow-sm" id="searchStore" placeholder="Search by name..." onkeyup="filterBannedStores()">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white shadow-sm border-left-0"><i class="fas fa-search fa-sm"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-right">
                <button type="button" class="btn btn-secondary btn-sm shadow-sm" onclick="resetFilters()">
                    <i class="fas fa-redo fa-sm"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Banned Stores List -->
<div id="bannedStoresList">
<?php if (!empty($data['banned_stores'])): ?>
    <?php foreach ($data['banned_stores'] as $store): ?>
        <?php
        $isExpired = false;
        $isPermanent = false;
        $isTemporary = false;
        
        if (empty($store['banned_until']) || empty($store['ban_duration_days'])) {
            $isPermanent = true;
            $banType = 'permanent';
            $statusBadgeClass = 'badge-danger';
            $statusText = 'Permanent';
        } elseif (strtotime($store['banned_until']) <= time()) {
            $isExpired = true;
            $banType = 'expired';
            $statusBadgeClass = 'badge-secondary';
            $statusText = 'Expired';
        } else {
            $isTemporary = true;
            $banType = 'temporary';
            $statusBadgeClass = 'badge-warning';
            $statusText = 'Temporary';
        }
        ?>
        <div class="card shadow mb-3 border-left-danger store-card" data-ban-type="<?= $banType ?>" data-store-name="<?= strtolower(htmlspecialchars($store['store_name'])) ?>">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="font-weight-bold text-gray-800 mb-0"><?= htmlspecialchars($store['store_name'] ?? 'Unknown Store') ?></h5>
                                <div class="small text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?= htmlspecialchars($store['address'] ?? '') ?>, <?= htmlspecialchars($store['city'] ?? '') ?>
                                </div>
                            </div>
                            <span class="badge <?= $statusBadgeClass ?> px-3 py-2"><?= $statusText ?> Ban</span>
                        </div>

                        <div class="bg-light p-3 rounded mb-3 border-left-danger" style="border-left-width: 4px;">
                            <h6 class="text-xs font-weight-bold text-danger text-uppercase mb-2"><i class="fas fa-info-circle mr-1"></i> Ban Details</h6>
                            <div class="row small text-gray-700">
                                <div class="col-md-6">
                                    <div class="mb-1"><strong>Banned At:</strong> <?= $store['banned_at'] ? date('M d, Y H:i', strtotime($store['banned_at'])) : 'N/A' ?></div>
                                    <div class="mb-1"><strong>Duration:</strong> <?= $isPermanent ? 'Permanent' : ($store['ban_duration_days'] . ' days') ?></div>
                                    <div class="mb-1"><strong>Valid Until:</strong> <?= $isPermanent ? 'Never' : date('M d, Y H:i', strtotime($store['banned_until'])) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1"><strong>Reason:</strong> <?= htmlspecialchars($store['ban_reason'] ?? 'No reason provided') ?></div>
                                    <div class="mb-1"><strong>Banned By:</strong> <?= htmlspecialchars($store['banned_by_name'] ?? 'Unknown') ?></div>
                                    <?php if ($store['admin_email']): ?>
                                        <div class="mb-1"><strong>Owner:</strong> <?= htmlspecialchars($store['admin_name'] ?? 'Admin') ?> (<?= htmlspecialchars($store['admin_email']) ?>)</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group-vertical">
                            <button class="btn btn-sm btn-outline-success mb-2 shadow-sm" onclick="unbanStore(<?= $store['id'] ?>, '<?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?>')">
                                <i class="fas fa-check-circle fa-sm mr-1"></i> Unban Store
                            </button>
                            <?php if ($store['admin_email']): ?>
                            <button class="btn btn-sm btn-outline-primary shadow-sm" onclick="unbanAdmin('<?= htmlspecialchars($store['admin_email'], ENT_QUOTES) ?>', '<?= htmlspecialchars($store['admin_name'] ?? 'Admin', ENT_QUOTES) ?>')">
                                <i class="fas fa-user-check fa-sm mr-1"></i> Unban Admin
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="text-center py-5 shadow-sm bg-white rounded">
        <i class="fas fa-check-circle fa-4x text-success mb-3" style="opacity: 0.2;"></i>
        <h5 class="text-gray-500">No Banned Stores</h5>
        <p class="text-gray-500 small mb-0">The system has no banned stores at the moment.</p>
    </div>
<?php endif; ?>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
function filterBannedStores() {
    const type = $('#filterBanType').val();
    const search = $('#searchStore').val().toLowerCase();
    
    $('.store-card').each(function() {
        const cardType = $(this).data('ban-type');
        const cardName = $(this).data('store-name');
        const show = (type === 'all' || type === cardType) && cardName.includes(search);
        $(this).toggle(show);
    });
}

function resetFilters() {
    $('#filterBanType').val('all');
    $('#searchStore').val('');
    filterBannedStores();
}

function unbanStore(id, name) {
    if (confirm(`Unban store "${name}"? Administrative account must be unbanned separately.`)) {
        fetch('<?= BASE_URL ?>control-panel/unbanStore', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `store_id=${id}`
        }).then(() => location.reload());
    }
}

function unbanAdmin(email, name) {
    if (confirm(`Unban admin account "${name}" (${email})?`)) {
        fetch('<?= BASE_URL ?>control-panel/unbanAdmin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `admin_email=${encodeURIComponent(email)}`
        }).then(() => location.reload());
    }
}
</script>

