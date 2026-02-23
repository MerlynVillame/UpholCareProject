<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Admin Registrations';
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
                <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">Admin Registrations</h1>
                <p class="text-muted smaller mb-0">Management of administrative membership requests.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                    <i class="fas fa-users-cog mr-2 text-info small"></i>
                    <span class="smaller font-weight-bold text-dark">Access Governance</span>
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
                <i class="fas fa-shield-alt text-white"></i>
            </div>
            <div>
                <h6 class="font-weight-bold mb-1">Registration Governance</h6>
                <p class="mb-0 opacity-75 smaller">Review and adjudicate inbound administrator requests. Approved accounts will receive a secure verification challenge.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-white-soft { background: rgba(255,255,255,0.1); }
    .opacity-75 { opacity: 0.75; }
</style>

<!-- Modern Filter Section -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body py-3 px-4">
        <form method="GET" action="<?= BASE_URL ?>control-panel/adminRegistrations" class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-0 text-muted font-weight-bold px-0 mr-2">Lifecycle Stage:</span>
                    </div>
                    <select name="status" id="status" class="form-control border-0 bg-light rounded" style="font-weight: 600;">
                        <option value="all" <?= $data['filter_status'] === 'all' ? 'selected' : '' ?>>All Registrations</option>
                        <option value="pending" <?= $data['filter_status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                        <option value="pending_verification" <?= $data['filter_status'] === 'pending_verification' ? 'selected' : '' ?>>Awaiting Verification</option>
                        <option value="approved" <?= $data['filter_status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $data['filter_status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
            </div>
            <div class="col-md-auto ml-auto">
                <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">
                    Apply Focus
                </button>
                <a href="<?= BASE_URL ?>control-panel/adminRegistrations" class="btn btn-light btn-sm px-3 ml-2 border" style="border-radius: 8px;">
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
            <i class="fas fa-users text-primary small"></i>
        </div>
        <h6 class="m-0 font-weight-bold text-dark">Administrative Candidates <span class="text-muted font-weight-normal ml-2 small">(<?= count($data['registrations']) ?> entries)</span></h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="adminRegistrationsTable" width="100%" cellspacing="0">
                <thead class="bg-light text-gray-600" style="font-size: 0.7rem;">
                    <tr>
                        <th class="border-0 px-4">#</th>
                        <th class="border-0 font-weight-bold text-uppercase">Identification</th>
                        <th class="border-0 font-weight-bold text-uppercase">Credentials</th>
                        <th class="border-0 font-weight-bold text-uppercase">Lifecycle Stage</th>
                        <th class="border-0 font-weight-bold text-uppercase">Access Flow</th>
                        <th class="border-0 text-center px-4 font-weight-bold text-uppercase">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php if (!empty($data['registrations'])): ?>
                        <?php foreach ($data['registrations'] as $index => $reg): 
                            $status = $reg['registration_status'] ?? 'pending';
                            if (empty($status)) $status = 'pending';
                        ?>
                            <tr>
                                <td class="px-4 py-3 align-middle text-muted small"><?= $index + 1 ?></td>
                                <td class="py-3 align-middle">
                                    <div class="font-weight-bold text-dark"><?= htmlspecialchars($reg['fullname']) ?></div>
                                    <div class="smaller text-muted mt-1"><?= htmlspecialchars($reg['email']) ?></div>
                                    <?php if (!empty($reg['employee_id'])): ?>
                                        <div class="mt-1">
                                            <span class="badge badge-warning text-dark" style="font-size: 0.7rem;">
                                                <i class="fas fa-id-badge mr-1"></i><?= htmlspecialchars($reg['employee_id']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 align-middle">
                                    <code class="bg-light px-2 py-1 rounded smaller text-primary"><?= htmlspecialchars($reg['username']) ?></code>
                                </td>
                                <td class="py-3 align-middle">
                                    <?php if ($status === 'pending_verification'): ?>
                                        <div class="d-flex align-items-center text-info">
                                            <i class="fas fa-envelope-open-text mr-2 small"></i>
                                            <span class="font-weight-bold smaller">Verification Sent</span>
                                        </div>
                                    <?php elseif ($status === 'approved'): ?>
                                        <div class="d-flex align-items-center text-success">
                                            <i class="fas fa-check-circle mr-2 small"></i>
                                            <span class="font-weight-bold smaller">Governance Approved</span>
                                        </div>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <div class="d-flex align-items-center text-danger">
                                            <i class="fas fa-times-circle mr-2 small"></i>
                                            <span class="font-weight-bold smaller">Access Denied</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center text-warning">
                                            <div class="spinner-grow spinner-grow-sm mr-2" style="width: 8px; height: 8px;"></div>
                                            <span class="font-weight-bold smaller">Awaiting Triage</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 align-middle">
                                    <div class="smaller text-dark font-weight-bold">Registered</div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= date('M d, Y', strtotime($reg['created_at'])) ?></div>
                                </td>
                                <td class="text-center px-4 py-3 align-middle">
                                    <div class="btn-group shadow-sm" style="border-radius: 20px; overflow: hidden;">
                                        <!-- View Details Always Available -->
                                        <button class="btn btn-sm btn-light border px-3" onclick='viewAdminDetails(<?= json_encode($reg) ?>)' title="View Full Profile">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </button>
                                        
                                        <?php if ($status === 'pending_verification'): ?>
                                            <button class="btn btn-sm btn-info px-3 border-left" onclick="viewVerificationCode(<?= $reg['id'] ?>)" title="View Code">
                                                <i class="fas fa-key fa-xs"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>control-panel/sendVerificationCode/<?= $reg['id'] ?>" 
                                               class="btn btn-sm btn-light border-left px-3 text-primary"
                                               onclick="return confirm('Resend verification code?')" title="Resend Code">
                                                <i class="fas fa-redo fa-xs"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger px-3 border-left" onclick="rejectAdmin(<?= $reg['id'] ?>)" title="Reject Account">
                                                <i class="fas fa-times fa-xs"></i>
                                            </button>
                                        <?php elseif ($status === 'pending'): ?>
                                            <button class="btn btn-sm btn-success px-3 border-left" onclick='viewAdminDetails(<?= json_encode($reg) ?>)' title="Review & Approve">
                                                <i class="fas fa-check fa-xs mr-1"></i> Review
                                            </button>
                                            <button class="btn btn-sm btn-danger px-3 border-left" onclick="rejectAdmin(<?= $reg['id'] ?>)" title="Reject Account">
                                                <i class="fas fa-times fa-xs"></i>
                                            </button>
                                        <?php elseif ($status === 'rejected' && !empty($reg['rejection_reason'])): ?>
                                            <button class="btn btn-sm btn-light border-left px-3 text-danger font-weight-bold" onclick="showRejectionReason('<?= addslashes(htmlspecialchars($reg['rejection_reason'])) ?>')" title="Show Rejection Reason">
                                                Reason
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-light mb-3"></i>
                                <p class="text-muted mb-0">The registration funnel is currently empty for this lens.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Admin Registration Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="viewDetailsContent"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewCodeModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold">Verification Code</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center" id="viewCodeContent"></div>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
function rejectAdmin(id) {
    const reason = window.prompt('Enter rejection reason:');
    if (reason && reason.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>control-panel/rejectAdmin/' + id;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'reason';
        input.value = reason.trim();
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function showRejectionReason(reason) {
    alert('Rejection Reason: ' + reason);
}

function viewAdminDetails(reg) {
    const status = reg.registration_status || 'pending';
    const baseUrl = '<?= BASE_URL ?>';
    
    let content = `
        <div class="row">
            <div class="col-12 mb-4">
                <h6 class="text-primary font-weight-bold border-bottom pb-2">
                    <i class="fas fa-user mr-2"></i>Personal Information
                </h6>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Full Name</label>
                <div class="font-weight-bold text-gray-800">${reg.fullname || 'N/A'}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Email</label>
                <div class="font-weight-bold text-gray-800">${reg.email || 'N/A'}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Username</label>
                <div class="font-weight-bold text-gray-800">${reg.username || 'N/A'}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Phone</label>
                <div class="font-weight-bold text-gray-800">${reg.phone || 'N/A'}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">
                    <i class="fas fa-id-badge text-warning mr-1"></i>Employee ID / Admin ID
                </label>
                <div class="font-weight-bold ${reg.employee_id ? 'text-primary' : 'text-danger'}">
                    ${reg.employee_id ? '<span class="badge badge-primary px-2 py-1" style="font-size:0.9rem; letter-spacing:1px;">' + reg.employee_id + '</span>' : '<span class="badge badge-danger px-2 py-1">Not Provided</span>'}
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Registration ID</label>
                <div class="font-weight-bold text-gray-800"><code>#${reg.id || 'N/A'}</code></div>
            </div>

            <div class="col-12 mb-4 mt-2">
                <h6 class="text-primary font-weight-bold border-bottom pb-2">
                    <i class="fas fa-building mr-2"></i>Business Information
                </h6>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Business Name</label>
                <div class="font-weight-bold text-gray-800">${reg.business_name || 'N/A'}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Registered On</label>
                <div class="font-weight-bold text-gray-800">${reg.created_at}</div>
            </div>
            <div class="col-12 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Business Address</label>
                <div class="font-weight-bold text-gray-800">
                    ${reg.business_address || 'N/A'}${reg.business_city ? ', ' + reg.business_city : ''}${reg.business_province ? ', ' + reg.business_province : ''}
                </div>
            </div>

            <div class="col-12 mb-4 mt-2">
                <h6 class="text-primary font-weight-bold border-bottom pb-2">
                    <i class="fas fa-file-alt mr-2"></i>Requirements Verification
                </h6>
            </div>
            <div class="col-md-12 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-2 d-block">Submitted Business Permit (Required)</label>
                ${reg.business_permit_path ? `
                    <div class="card bg-light border-left-primary shadow-sm">
                        <div class="card-body py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                                <div>
                                    <div class="font-weight-bold text-gray-800 mb-0">${reg.business_permit_filename || 'Business_Permit.pdf'}</div>
                                    <div class="small text-muted">Uploaded on ${reg.created_at}</div>
                                </div>
                            </div>
                            <a href="${baseUrl}${reg.business_permit_path}" target="_blank" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                                <i class="fas fa-external-link-alt mr-1"></i> Open Permit
                            </a>
                        </div>
                    </div>
                ` : `
                    <div class="alert alert-warning py-2 mb-0 small">
                        <i class="fas fa-exclamation-triangle mr-2"></i> No business permit file found for this registration.
                    </div>
                `}
            </div>

            <div class="col-md-12 mb-3">
                <label class="small font-weight-bold text-gray-500 mb-2 d-block"><i class="fas fa-id-card text-warning mr-1"></i> Submitted Valid ID (Required)</label>
                ${reg.valid_id_path ? `<div class="card shadow-sm" style="border-left:4px solid #ffc107;background:#fffdf6"><div class="card-body py-3"><div class="d-flex align-items-center justify-content-between mb-2"><div class="d-flex align-items-center"><i class="fas fa-id-card fa-2x text-warning mr-3"></i><div><div class="font-weight-bold text-gray-800 mb-0">${reg.valid_id_filename||'Valid_ID.jpg'}</div><div class="small text-muted">Uploaded on ${reg.created_at}</div></div></div><a href="${baseUrl}${reg.valid_id_path}" target="_blank" class="btn btn-warning btn-sm rounded-pill shadow-sm" style="color:#fff"><i class="fas fa-external-link-alt mr-1"></i> Full Size</a></div><div style="border-radius:8px;overflow:hidden;border:2px solid #ffc107;max-height:200px"><img src="${baseUrl}${reg.valid_id_path}" alt="Valid ID" style="width:100%;max-height:200px;object-fit:cover;display:block;cursor:zoom-in" onclick="window.open('${baseUrl}${reg.valid_id_path}','_blank')" title="Click to open full size"></div><div class="small text-muted mt-1"><i class="fas fa-search-plus mr-1"></i> Click image to view full size</div></div></div>` : `<div class="alert alert-danger py-2 mb-0 small"><i class="fas fa-exclamation-triangle mr-2"></i> <strong>No valid ID submitted.</strong> Consider rejecting this registration.</div>`}
            </div>

            ${status === 'pending' ? `
                <div class="col-12 mt-4 px-3 py-3 rounded bg-gray-100 border">
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="verifyRequirementsCheck" onchange="toggleApprovalButtons(this)">
                        <label class="custom-control-label font-weight-bold text-dark" for="verifyRequirementsCheck">
                            I have carefully reviewed the submitted <strong>business permit</strong> and <strong>valid ID</strong>, and verified that all information is correct and legitimate.
                        </label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-danger mr-2" onclick="rejectAdmin(${reg.id})">
                            <i class="fas fa-times-circle mr-1"></i> Reject Registration
                        </button>
                        <a href="${baseUrl}control-panel/approveAdmin/${reg.id}" 
                           id="btnApproveInsideModal"
                           class="btn btn-success disabled"
                           style="pointer-events: none;"
                           onclick="return confirm('Accept this admin account after verifying requirements?')">
                            <i class="fas fa-check-circle mr-1"></i> Accept & Approve
                        </a>
                    </div>
                </div>
            ` : `
                <div class="col-12 mt-2">
                    <label class="small font-weight-bold text-gray-500 mb-0">Current Status</label>
                    <div class="font-weight-bold ${status === 'approved' ? 'text-success' : (status === 'rejected' ? 'text-danger' : 'text-info')}">
                        ${status.toUpperCase()}
                    </div>
                </div>
            `}

            ${reg.rejection_reason ? `
            <div class="col-12 mt-3">
                <label class="small font-weight-bold text-gray-500 mb-0">Rejection Reason</label>
                <div class="alert alert-danger py-2 mt-1 small">${reg.rejection_reason}</div>
            </div>
            ` : ''}
        </div>
    `;
    $('#viewDetailsContent').html(content);
    $('#viewDetailsModal').modal('show');
}

function toggleApprovalButtons(checkbox) {
    const btnApprove = document.getElementById('btnApproveInsideModal');
    if (checkbox.checked) {
        btnApprove.classList.remove('disabled');
        btnApprove.style.pointerEvents = 'auto';
    } else {
        btnApprove.classList.add('disabled');
        btnApprove.style.pointerEvents = 'none';
    }
}

function viewVerificationCode(id) {
    $('#viewCodeContent').html('<div class="spinner-border spinner-border-sm text-primary"></div>');
    $('#viewCodeModal').modal('show');
    
    fetch('<?= BASE_URL ?>control-panel/getVerificationCode/' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#viewCodeContent').html(`
                    <div class="h3 font-weight-bold text-primary tracking-widest my-3" style="letter-spacing: 5px;">
                        ${data.code}
                    </div>
                    <p class="small text-muted mb-0">Sent to: ${data.email}</p>
                `);
            } else {
                $('#viewCodeContent').html(`<div class="text-danger small">${data.message}</div>`);
            }
        });
}
</script>

