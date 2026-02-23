<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Store Ratings';
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
                <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">Store Performance</h1>
                <p class="text-muted smaller mb-0">Service quality metrics and reputation benchmarks.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                    <i class="fas fa-star text-warning mr-2 small"></i>
                    <span class="smaller font-weight-bold text-dark">Quality Assurance</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Stores</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['total_stores'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-store fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">With Ratings</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['stores_with_ratings'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Below Threshold</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['stores_below_threshold'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Rating</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($data['stats']['avg_rating'] ?? 0, 1) ?>/5.0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>control-panel/storeRatings" class="form-inline">
            <div class="form-group mr-3">
                <label class="mr-2 small font-weight-bold text-gray-600">Threshold:</label>
                <select name="threshold" class="form-control form-control-sm">
                    <option value="" <?= empty($data['rating_threshold']) ? 'selected' : '' ?>>All Stores</option>
                    <option value="1.0" <?= ($data['rating_threshold'] ?? '') == '1.0' ? 'selected' : '' ?>>Below 1.0</option>
                    <option value="2.0" <?= ($data['rating_threshold'] ?? '') == '2.0' ? 'selected' : '' ?>>Below 2.0</option>
                    <option value="3.0" <?= ($data['rating_threshold'] ?? '') == '3.0' ? 'selected' : '' ?>>Below 3.0</option>
                </select>
            </div>
            <div class="form-group mr-3">
                <input type="text" id="searchStore" class="form-control form-control-sm" placeholder="Search store name..." onkeyup="filterStores()">
            </div>
            <button type="submit" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-filter fa-sm"></i> Filter
            </button>
            <a href="<?= BASE_URL ?>control-panel/storeRatings" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo fa-sm"></i> Reset
            </a>
        </form>
    </div>
</div>

<!-- Store Cards -->
<div id="storesList">
    <?php if (!empty($data['stores'])): ?>
        <?php foreach ($data['stores'] as $store): ?>
            <?php 
            $rating = floatval($store['avg_rating'] ?? 0);
            $totalRatings = intval($store['total_ratings'] ?? 0);
            $borderClass = $rating < 2.0 ? 'border-left-danger' : ($rating < 3.0 ? 'border-left-warning' : 'border-left-primary');
            ?>
            <div class="card shadow mb-3 <?= $borderClass ?> store-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="font-weight-bold text-gray-800 mb-1 store-name"><?= htmlspecialchars($store['store_name']) ?></h5>
                            <div class="small text-gray-500 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= htmlspecialchars($store['address'] . ', ' . $store['city']) ?>
                            </div>
                            <div class="row">
                                <div class="col-auto">
                                    <div class="text-xs font-weight-bold text-uppercase text-gray-500">Avg Rating</div>
                                    <div class="h6 mb-0 font-weight-bold text-primary">
                                        <?php if ($totalRatings > 0): ?>
                                            <i class="fas fa-star text-warning"></i> <?= number_format($rating, 1) ?> (<?= $totalRatings ?>)
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-auto ml-4">
                                    <div class="text-xs font-weight-bold text-uppercase text-gray-500">Admin</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($store['admin_name'] ?? 'N/A') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-warning" onclick="removeStoreFromMap(<?= $store['id'] ?>, '<?= addslashes(htmlspecialchars($store['store_name'])) ?>')">
                                    <i class="fas fa-map-marker-times fa-sm"></i> Remove Map
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="banStore(<?= $store['id'] ?>, '<?= addslashes(htmlspecialchars($store['store_name'])) ?>', true, <?= $store['admin_id'] ?? 'null' ?>)">
                                    <i class="fas fa-ban fa-sm"></i> Ban Store & Admin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5 shadow-sm bg-white rounded">
            <i class="fas fa-store fa-3x text-gray-300 mb-3"></i>
            <p class="text-gray-500 mb-0">No stores match the criteria.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modals -->
<div class="modal fade" id="removeStoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold">Remove Store from Map</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Remove <strong id="removeStoreName"></strong> from the map?</p>
                <textarea class="form-control" id="removeReason" rows="2" placeholder="Reason..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="confirmRemoveStore()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="banStoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold">Ban Store & Admin</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger py-2 small mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Banning a store also bans the owner's admin account.
                </div>
                <div id="banStoreMessage" class="font-weight-bold mb-3"></div>
                <div class="form-group mb-2">
                    <label class="small font-weight-bold">Reason</label>
                    <textarea class="form-control" id="banReason" rows="2" placeholder="Reason..."></textarea>
                </div>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Duration (Days)</label>
                    <input type="number" class="form-control" id="banDurationDays" placeholder="Empty for permanent">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="confirmBanStore()">Ban Forever/Limited</button>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
let currentStoreId = null;

function filterStores() {
    const term = $('#searchStore').val().toLowerCase();
    $('.store-card').each(function() {
        const name = $(this).find('.store-name').text().toLowerCase();
        $(this).toggle(name.includes(term));
    });
}

function removeStoreFromMap(id, name) {
    currentStoreId = id;
    $('#removeStoreName').text(name);
    $('#removeStoreModal').modal('show');
}

function confirmRemoveStore() {
    const reason = $('#removeReason').val();
    fetch('<?= BASE_URL ?>control-panel/removeStoreFromMap', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `store_id=${currentStoreId}&reason=${encodeURIComponent(reason)}`
    }).then(() => location.reload());
}

function banStore(id, name, banAdmin, adminId) {
    currentStoreId = id;
    $('#banStoreMessage').html(`Ban ${name}?`);
    $('#banStoreModal').modal('show');
}

function confirmBanStore() {
    const reason = $('#banReason').val();
    const duration = $('#banDurationDays').val();
    let body = `store_id=${currentStoreId}&reason=${encodeURIComponent(reason)}&ban_admin=true`;
    if (duration) body += `&ban_duration_days=${duration}`;
    
    fetch('<?= BASE_URL ?>control-panel/banStore', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    }).then(() => location.reload());
}
</script>

