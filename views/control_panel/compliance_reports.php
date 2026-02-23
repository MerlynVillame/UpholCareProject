<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Compliance Reports';
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
                <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">Compliance Registry</h1>
                <p class="text-muted smaller mb-0">Review of community compliance reports.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                    <i class="fas fa-clipboard-check mr-2 text-info small"></i>
                    <span class="smaller font-weight-bold text-dark">System Integrity</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Reports</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['total'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['pending'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Reviewed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['reviewed'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Resolved</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['resolved'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>control-panel/complianceReports" class="form-inline">
            <div class="form-group mr-3">
                <label class="mr-2 small font-weight-bold text-gray-600">Status:</label>
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="all" <?= ($data['filter_status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                    <option value="pending" <?= ($data['filter_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="reviewed" <?= ($data['filter_status'] ?? '') === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                    <option value="resolved" <?= ($data['filter_status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="dismissed" <?= ($data['filter_status'] ?? '') === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
                </select>
            </div>
            <div class="form-group mr-3">
                <label class="mr-2 small font-weight-bold text-gray-600">Type:</label>
                <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="all" <?= ($data['filter_type'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Types</option>
                    <option value="safety" <?= ($data['filter_type'] ?? '') === 'safety' ? 'selected' : '' ?>>Safety</option>
                    <option value="hygiene" <?= ($data['filter_type'] ?? '') === 'hygiene' ? 'selected' : '' ?>>Hygiene</option>
                    <option value="quality" <?= ($data['filter_type'] ?? '') === 'quality' ? 'selected' : '' ?>>Quality</option>
                    <option value="service" <?= ($data['filter_type'] ?? '') === 'service' ? 'selected' : '' ?>>Service</option>
                    <option value="pricing" <?= ($data['filter_type'] ?? '') === 'pricing' ? 'selected' : '' ?>>Pricing</option>
                    <option value="other" <?= ($data['filter_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-filter fa-sm"></i> Filter
            </button>
            <a href="<?= BASE_URL ?>control-panel/complianceReports" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo fa-sm"></i> Reset
            </a>
        </form>
    </div>
</div>

<!-- Reports List -->
<?php if (!empty($data['reports'])): ?>
    <?php foreach ($data['reports'] as $report): ?>
        <?php 
        $statusClass = [
            'pending' => 'border-left-warning',
            'reviewed' => 'border-left-info',
            'resolved' => 'border-left-success',
            'dismissed' => 'border-left-secondary'
        ][$report['status']] ?? 'border-left-primary';
        
        $badgeClass = [
            'pending' => 'badge-warning',
            'reviewed' => 'badge-info',
            'resolved' => 'badge-success',
            'dismissed' => 'badge-secondary'
        ][$report['status']] ?? 'badge-primary';
        ?>
        <div class="card shadow mb-3 <?= $statusClass ?>">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="font-weight-bold text-gray-800 mb-0"><?= htmlspecialchars($report['store_name'] ?? 'Unknown Store') ?></h5>
                                <div class="small text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?= htmlspecialchars($report['address'] ?? '') ?>, <?= htmlspecialchars($report['city'] ?? '') ?>
                                </div>
                            </div>
                            <span class="badge <?= $badgeClass ?> p-2"><?= ucfirst($report['status']) ?></span>
                        </div>
                        
                        <div class="mb-2">
                            <?php 
                            $issueTypes = is_array($report['issue_types']) ? $report['issue_types'] : json_decode($report['issue_types'] ?? '[]', true);
                            foreach ($issueTypes as $type): 
                            ?>
                                <span class="badge badge-light border text-muted px-2 py-1 mr-1 small">
                                    <i class="fas fa-tag fa-xs mr-1"></i> <?= ucfirst($type) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <div class="bg-light p-2 rounded small text-gray-700 mb-3 border-left-info" style="border-left-width: 3px;">
                            <strong>Description:</strong> <?= nl2br(htmlspecialchars($report['description'] ?? 'No description')) ?>
                        </div>

                        <div class="row small text-gray-500">
                            <div class="col-md-4">
                                <strong>Reported By:</strong> <?= htmlspecialchars($report['customer_name'] ?? 'Unknown') ?>
                                <br><?= htmlspecialchars($report['customer_email'] ?? '') ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Date:</strong> <?= date('M d, Y H:i', strtotime($report['created_at'])) ?>
                            </div>
                            <?php if ($report['reviewed_by_name']): ?>
                                <div class="col-md-4">
                                    <strong>Reviewed By:</strong> <?= htmlspecialchars($report['reviewed_by_name']) ?>
                                    <br><?= date('M d, Y', strtotime($report['reviewed_at'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group-vertical">
                            <?php if ($report['status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-outline-info mb-1" onclick="updateReportStatus(<?= $report['id'] ?>, 'reviewed')">
                                    <i class="fas fa-eye fa-sm"></i> Review
                                </button>
                                <button class="btn btn-sm btn-outline-success mb-1" onclick="updateReportStatus(<?= $report['id'] ?>, 'resolved')">
                                    <i class="fas fa-check fa-sm"></i> Resolve
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateReportStatus(<?= $report['id'] ?>, 'dismissed')">
                                    <i class="fas fa-times fa-sm"></i> Dismiss
                                </button>
                            <?php elseif ($report['status'] === 'reviewed'): ?>
                                <button class="btn btn-sm btn-outline-success mb-1" onclick="updateReportStatus(<?= $report['id'] ?>, 'resolved')">
                                    <i class="fas fa-check fa-sm"></i> Resolve
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateReportStatus(<?= $report['id'] ?>, 'dismissed')">
                                    <i class="fas fa-times fa-sm"></i> Dismiss
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
        <i class="fas fa-clipboard-check fa-3x text-gray-300 mb-3"></i>
        <p class="text-gray-500 mb-0">No compliance reports found.</p>
    </div>
<?php endif; ?>

<!-- Status Update Modal -->
<div class="modal fade" id="updateReportStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="updateReportStatusTitle">Update Report Status</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="update_report_id">
                <input type="hidden" id="update_report_status">
                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-gray-600">Admin Notes (Optional)</label>
                    <textarea class="form-control" id="admin_notes" rows="3" placeholder="Add notes about this action..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="confirmUpdateReportStatus()">
                    <i class="fas fa-save fa-sm mr-1"></i> Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
function updateReportStatus(id, status) {
    $('#update_report_id').val(id);
    $('#update_report_status').val(status);
    $('#admin_notes').val('');
    
    const titles = {
        'reviewed': 'Mark as Reviewed',
        'resolved': 'Mark as Resolved',
        'dismissed': 'Dismiss Report'
    };
    $('#updateReportStatusTitle').text(titles[status] || 'Update Status');
    $('#updateReportStatusModal').modal('show');
}

function confirmUpdateReportStatus() {
    const id = $('#update_report_id').val();
    const status = $('#update_report_status').val();
    const notes = $('#admin_notes').val();
    
    fetch('<?= BASE_URL ?>control-panel/updateComplianceReportStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `report_id=${id}&status=${status}&admin_notes=${encodeURIComponent(notes)}`
    }).then(() => location.reload());
}
</script>

