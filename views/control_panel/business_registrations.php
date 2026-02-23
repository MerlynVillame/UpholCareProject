<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Business Registrations';
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
                <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">Business Registrations</h1>
                <p class="text-muted smaller mb-0">Validation of corporate entities and credentials.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                    <i class="fas fa-briefcase mr-2 text-info small"></i>
                    <span class="smaller font-weight-bold text-dark">Entity Verification</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Context Tile -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #2C3E50 0%, #1A252F 100%); color: white;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="bg-white-soft p-3 rounded-circle mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-briefcase text-white"></i>
            </div>
            <div>
                <h6 class="font-weight-bold mb-1">Corporate Validation Flow</h6>
                <p class="mb-0 opacity-75 smaller">Audit and verify corporate credentials. Ensure all business permits are authenticated before granting operational access.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-white-soft { background: rgba(255,255,255,0.1); }
</style>

<!-- Modern Filter Section -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body py-3 px-4">
        <form method="GET" action="<?= BASE_URL ?>control-panel/businessRegistrations" class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-0 text-muted font-weight-bold px-0 mr-2">Lifecycle Stage:</span>
                    </div>
                    <select name="status" id="status" class="form-control border-0 bg-light rounded" style="font-weight: 600;">
                        <option value="all" <?= ($data['filter_status'] === null || $data['filter_status'] === 'all') ? 'selected' : '' ?>>All Entities</option>
                        <option value="pending" <?= $data['filter_status'] === 'pending' ? 'selected' : '' ?>>Awaiting Triage</option>
                        <option value="approved" <?= $data['filter_status'] === 'approved' ? 'selected' : '' ?>>Verified Active</option>
                        <option value="rejected" <?= $data['filter_status'] === 'rejected' ? 'selected' : '' ?>>Access Denied</option>
                    </select>
                </div>
            </div>
            <div class="col-md-auto ml-auto">
                <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">
                    Apply Focus
                </button>
                <a href="<?= BASE_URL ?>control-panel/businessRegistrations" class="btn btn-light btn-sm px-3 ml-2 border" style="border-radius: 8px;">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Modern Data Explorer -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header py-3 bg-white border-0 d-flex align-items-center">
        <div class="bg-primary-soft p-2 rounded mr-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-briefcase text-primary small"></i>
        </div>
        <h6 class="m-0 font-weight-bold text-dark">Business Registry <span class="text-muted font-weight-normal ml-2 small">(<?= count($data['registrations']) ?> entries)</span></h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="businessRegistrationsTable" width="100%" cellspacing="0">
                <thead class="bg-light text-gray-600" style="font-size: 0.7rem;">
                    <tr>
                        <th class="border-0 px-4">#</th>
                        <th class="border-0 font-weight-bold text-uppercase">Identification</th>
                        <th class="border-0 font-weight-bold text-uppercase">Authorized Rep</th>
                        <th class="border-0 font-weight-bold text-uppercase text-center">Category</th>
                        <th class="border-0 font-weight-bold text-uppercase">Lifecycle Stage</th>
                        <th class="border-0 font-weight-bold text-uppercase text-center">Submitted</th>
                        <th class="border-0 text-center px-4 font-weight-bold text-uppercase">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php if (!empty($data['registrations'])): ?>
                        <?php foreach ($data['registrations'] as $index => $reg): 
                            $status = $reg['status'] ?? 'pending';
                        ?>
                            <tr>
                                <td class="px-4 py-3 align-middle text-muted small"><?= $index + 1 ?></td>
                                <td class="py-3 align-middle">
                                    <div class="font-weight-bold text-dark"><?= htmlspecialchars($reg['business_name']) ?></div>
                                    <div class="smaller text-muted mt-1"><?= htmlspecialchars($reg['business_email'] ?? 'N/A') ?></div>
                                </td>
                                <td class="py-3 align-middle">
                                    <div class="text-dark font-weight-bold smaller"><?= htmlspecialchars($reg['owner_name']) ?></div>
                                </td>
                                <td class="py-3 align-middle text-center">
                                    <span class="badge badge-pill bg-light text-muted border px-2 font-weight-bold smaller"><?= htmlspecialchars($reg['business_type_name'] ?? 'Other') ?></span>
                                </td>
                                <td class="py-3 align-middle">
                                    <?php if ($status === 'pending'): ?>
                                        <div class="d-flex align-items-center text-warning">
                                            <div class="indicator-dot bg-warning pulse-warning mr-2" style="width: 7px; height: 7px;"></div>
                                            <span class="font-weight-bold smaller text-uppercase">Under Triage</span>
                                        </div>
                                    <?php elseif ($status === 'approved'): ?>
                                        <div class="d-flex align-items-center text-success">
                                            <i class="fas fa-check-circle mr-2 small"></i>
                                            <span class="font-weight-bold smaller text-uppercase">Verified Active</span>
                                        </div>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <div class="d-flex align-items-center text-danger">
                                            <i class="fas fa-times-circle mr-2 small"></i>
                                            <span class="font-weight-bold smaller text-uppercase">Access Denied</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 align-middle text-center text-muted smaller">
                                    <?= date('M d, Y', strtotime($reg['created_at'])) ?>
                                </td>
                                <td class="text-center px-4 py-3 align-middle">
                                    <div class="btn-group shadow-sm" style="border-radius: 20px; overflow: hidden;">
                                        <button class="btn btn-sm btn-light border px-3" onclick='viewBusinessDetails(<?= json_encode($reg) ?>)' title="Inspect Details">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </button>
                                        
                                        <?php if (!empty($reg['permit_file'])): ?>
                                            <a href="<?= BASE_URL . $reg['permit_file'] ?>" target="_blank" class="btn btn-sm btn-light border-left px-3 text-info" title="View Permit">
                                                <i class="fas fa-file-pdf fa-xs"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($status === 'pending'): ?>
                                            <a href="<?= BASE_URL ?>control-panel/approveBusiness/<?= $reg['id'] ?>" 
                                               class="btn btn-sm btn-success px-3 border-left" 
                                               onclick="return confirm('Authorize this business entity?')" title="Authorize Access">
                                                <i class="fas fa-check fa-xs"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger px-3 border-left" 
                                                    onclick="rejectBusiness(<?= $reg['id'] ?>)" title="Deny Request">
                                                <i class="fas fa-times fa-xs"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-light mb-3"></i>
                                <p class="text-muted mb-0">No business entities match the current lens.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Business Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="detailsContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Business Registration</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_reason">Reason for Rejection</label>
                        <textarea class="form-control" name="reason" id="reject_reason" rows="3" required placeholder="Explain why this registration is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
function viewBusinessDetails(data) {
    const content = `
        <div class="mb-3">
            <label class="small text-muted mb-0">Business Name</label>
            <div class="font-weight-bold">${data.business_name}</div>
        </div>
        <div class="mb-3">
            <label class="small text-muted mb-0">Owner / Submitter</label>
            <div class="font-weight-bold">${data.owner_name}</div>
        </div>
        <div class="mb-3">
            <label class="small text-muted mb-0">Business Type</label>
            <div class="font-weight-bold">${data.business_type_name || 'Other'}</div>
        </div>
        <div class="mb-3">
            <label class="small text-muted mb-0">Business Address</label>
            <div class="font-weight-bold">${data.business_address}</div>
        </div>
        ${data.rejected_reason ? `
        <div class="alert alert-danger px-2 py-1 small">
            <strong>Rejection Reason:</strong><br>${data.rejected_reason}
        </div>
        ` : ''}
    `;
    $('#detailsContent').html(content);
    $('#viewDetailsModal').modal('show');
}

function rejectBusiness(id) {
    $('#rejectForm').attr('action', '<?= BASE_URL ?>control-panel/rejectBusiness/' + id);
    $('#rejectModal').modal('show');
}
</script>
