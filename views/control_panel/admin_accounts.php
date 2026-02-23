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
                <p class="text-muted smaller mb-0">Review and approve administrator access requests.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                    <i class="fas fa-user-clock mr-2 text-warning small"></i>
                    <span class="smaller font-weight-bold text-dark">Governance Review</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Context Tabs -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header p-0 bg-white border-0">
        <div class="d-flex nav-tabs-modern">
            <a class="flex-fill text-center py-3 px-2 text-decoration-none transition-all <?= (!isset($data['view_type']) || $data['view_type'] === 'active') ? 'active-tab bg-primary-soft border-bottom-primary' : 'text-muted opacity-75' ?>" 
               href="<?= BASE_URL ?>control-panel/adminAccounts?view=active">
                <i class="fas fa-user-shield mr-2"></i> 
                <span class="font-weight-bold small text-uppercase tracking-wider">Active Governance</span>
                <span class="badge badge-pill badge-primary ml-2 shadow-sm smaller"><?= $data['active_count'] ?? 0 ?></span>
            </a>
            <a class="flex-fill text-center py-3 px-2 text-decoration-none transition-all <?= (isset($data['view_type']) && $data['view_type'] === 'pending') ? 'active-tab bg-primary-soft border-bottom-primary' : 'text-muted opacity-75' ?>" 
               href="<?= BASE_URL ?>control-panel/adminAccounts?view=pending">
                <i class="fas fa-clock mr-2"></i> 
                <span class="font-weight-bold small text-uppercase tracking-wider">Awaiting Review</span>
                <span class="badge badge-pill badge-warning text-dark ml-2 shadow-sm smaller"><?= $data['pending_count'] ?? 0 ?></span>
            </a>
        </div>
    </div>
</div>

<style>
    .nav-tabs-modern a { border-bottom: 3px solid transparent; transition: all 0.2s ease; }
    .nav-tabs-modern a.active-tab { border-bottom-color: var(--brand-azure) !important; color: var(--brand-navy) !important; }
    .nav-tabs-modern a:hover:not(.active-tab) { background-color: rgba(44, 62, 80, 0.02); }
    .border-bottom-primary { border-bottom: 3px solid var(--brand-azure) !important; }
</style>
    
    <div class="card-body">
        <!-- Filter Section (only for Active Admins) -->
        <?php if (!isset($data['view_type']) || $data['view_type'] === 'active'): ?>
        <div class="mb-4">
            <form method="GET" action="<?= BASE_URL ?>control-panel/adminAccounts" class="form-inline">
                <input type="hidden" name="view" value="active">
                <div class="form-group mr-3">
                    <label for="status" class="mr-2 small font-weight-bold">Status:</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="all" <?= $data['filter_status'] === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="active" <?= $data['filter_status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $data['filter_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-search fa-sm"></i> Filter
                </button>
                <a href="<?= BASE_URL ?>control-panel/adminAccounts?view=active" class="btn btn-secondary btn-sm">
                    <i class="fas fa-redo fa-sm"></i> Reset
                </a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="activeAdminsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['admins'])): ?>
                        <?php foreach ($data['admins'] as $index => $admin): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($admin['fullname']) ?></strong></td>
                                <td><?= htmlspecialchars($admin['email']) ?></td>
                                <td><span class="badge badge-info"><?= ucfirst($admin['role']) ?></span></td>
                                <td>
                                    <?php if ($admin['status'] === 'active'): ?>
                                        <span class="text-success small font-weight-bold"><i class="fas fa-check-circle"></i> Active</span>
                                    <?php else: ?>
                                        <span class="text-secondary small font-weight-bold"><i class="fas fa-times-circle"></i> Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : '<span class="text-muted">Never</span>' ?></td>
                                <td><?= date('M d, Y', strtotime($admin['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Pending Admins View -->
        <?php if (isset($data['view_type']) && $data['view_type'] === 'pending'): ?>
        <div class="alert alert-info border-left-info shadow-sm mb-4">
            <i class="fas fa-info-circle mr-2"></i>
            These are admin accounts waiting for acceptance. <strong>Accept</strong> to generate a verification code, or <strong>Reject</strong> to decline.
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="pendingAdminsTable" width="100%" cellspacing="0">
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
                    <?php if (!empty($data['pending_admins'])): ?>
                        <?php foreach ($data['pending_admins'] as $index => $reg): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($reg['fullname']) ?></strong></td>
                                <td><?= htmlspecialchars($reg['email']) ?></td>
                                <td><?= htmlspecialchars($reg['username'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($reg['phone'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($reg['registration_status'] === 'pending_verification'): ?>
                                        <span class="badge badge-info"><i class="fas fa-envelope"></i> Code Sent</span>
                                    <?php elseif ($reg['registration_status'] === 'pending'): ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> Waiting</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($reg['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-info" onclick="viewAdminDetails(<?= htmlspecialchars(json_encode($reg), ENT_QUOTES) ?>)">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </button>
                                        
                                        <?php if ($reg['registration_status'] === 'pending'): ?>
                                            <a href="<?= BASE_URL ?>control-panel/approveAdmin/<?= $reg['id'] ?>" class="btn btn-sm btn-success"
                                               onclick="return confirm('Accept this admin account? A verification code will be generated.')">
                                                <i class="fas fa-check fa-sm"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="rejectAdmin(<?= $reg['id'] ?>)">
                                                <i class="fas fa-times fa-sm"></i>
                                            </button>
                                        <?php elseif ($reg['registration_status'] === 'pending_verification'): ?>
                                            <button class="btn btn-sm btn-warning" onclick="viewVerificationCode(<?= $reg['id'] ?>, '<?= htmlspecialchars($reg['email']) ?>')">
                                                <i class="fas fa-key fa-sm"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>control-panel/sendVerificationCode/<?= $reg['id'] ?>" class="btn btn-sm btn-outline-primary"
                                               onclick="return confirm('Resend verification code to <?= htmlspecialchars($reg['email']) ?>?')">
                                                <i class="fas fa-redo fa-sm"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals -->
<!-- View/Review Admin Registration Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title font-weight-bold" id="viewDetailsModalLabel">
                    <i class="fas fa-user-shield mr-2"></i>Admin Registration Review
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="viewDetailsContent" style="max-height: 70vh; overflow-y: auto;">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- View Verification Code Modal -->
<div class="modal fade" id="viewCodeModal" tabindex="-1" role="dialog" aria-labelledby="viewCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold" id="viewCodeModalLabel"><i class="fas fa-key mr-2"></i>Verification Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewCodeContent">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-gray-600">Loading verification code...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
    function rejectAdmin(id) {
        confirm('Reject this admin account?').then((confirmed) => {
            if (confirmed) {
                prompt('Enter rejection reason:').then((reason) => {
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
                });
            }
        });
    }
    
    function viewAdminDetails(reg) {
        const content = document.getElementById('viewDetailsContent');
        const statusBadges = {
            'pending_verification': '<span class="badge badge-info px-3 py-2"><i class="fas fa-envelope mr-1"></i> Code Sent</span>',
            'pending': '<span class="badge badge-warning text-dark px-3 py-2"><i class="fas fa-clock mr-1"></i> Awaiting Triage</span>',
            'approved': '<span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> Governance Approved</span>',
            'rejected': '<span class="badge badge-danger px-3 py-2"><i class="fas fa-times-circle mr-1"></i> Access Denied</span>'
        };
        const statusBadge = statusBadges[reg.registration_status] || '<span class="badge badge-secondary">Unknown</span>';
        
        // Format the registration date
        const regDate = reg.created_at ? new Date(reg.created_at).toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'N/A';
        
        content.innerHTML = `
            <!-- Personal Information Section -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 class="mb-0 font-weight-bold text-gray-800">Personal Information</h5>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">Full Name</label>
                        <div class="font-weight-bold text-gray-900">${reg.fullname || 'N/A'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">Username</label>
                        <div class="font-weight-bold text-gray-900">${reg.username || 'N/A'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">Email Address</label>
                        <div class="font-weight-bold text-gray-900">${reg.email || 'N/A'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">Phone Number</label>
                        <div class="font-weight-bold text-gray-900">${reg.phone || 'N/A'}</div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Business Information Section -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info text-white rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-store"></i>
                    </div>
                    <h5 class="mb-0 font-weight-bold text-gray-800">Business Information</h5>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">Business Name</label>
                        <div class="font-weight-bold text-gray-900">${reg.business_name || 'N/A'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">Registered On</label>
                        <div class="font-weight-bold text-gray-900">${regDate}</div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="small text-muted mb-1">Business Address</label>
                        <div class="font-weight-bold text-gray-900">${reg.business_address || 'N/A'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">City</label>
                        <div class="font-weight-bold text-gray-900">${reg.business_city || 'N/A'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted mb-1">Province</label>
                        <div class="font-weight-bold text-gray-900">${reg.business_province || 'N/A'}</div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Requirements Verification Section -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning text-dark rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h5 class="mb-0 font-weight-bold text-gray-800">Requirements Verification</h5>
                </div>
                
                ${reg.business_permit_path ? `
                <div class="mb-3">
                    <label class="small text-muted mb-2">Submitted Business Permit (Required)</label>
                    <div class="border rounded p-3 bg-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                            <div>
                                <div class="font-weight-bold text-gray-900">${reg.business_permit_path.split('/').pop()}</div>
                                <small class="text-muted">Uploaded on ${regDate}</small>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>${reg.business_permit_path}" target="_blank" class="btn btn-sm btn-dark px-4">
                            <i class="fas fa-external-link-alt mr-2"></i>Open Permit
                        </a>
                    </div>
                </div>
                ` : '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>No business permit uploaded</div>'}
            </div>

            <!-- Status Section -->
            <div class="mb-4">
                <label class="small text-muted mb-2">Lifecycle Stage</label>
                <div>${statusBadge}</div>
            </div>

            ${reg.rejection_reason ? `
            <div class="alert alert-danger border-left-danger">
                <div class="d-flex align-items-start">
                    <i class="fas fa-times-circle fa-2x mr-3 mt-1"></i>
                    <div>
                        <strong class="d-block mb-1">Rejection Reason:</strong>
                        <p class="mb-0">${reg.rejection_reason}</p>
                    </div>
                </div>
            </div>
            ` : ''}

            ${reg.registration_status === 'pending' ? `
            <hr class="my-4">
            
            <!-- Verification Checkbox -->
            <div class="form-check mb-4 p-3 bg-light rounded">
                <input class="form-check-input" type="checkbox" id="verifyCheck_${reg.id}" style="width: 20px; height: 20px; margin-top: 2px;">
                <label class="form-check-label ml-2 font-weight-normal text-gray-800" for="verifyCheck_${reg.id}" style="line-height: 1.6;">
                    I have carefully reviewed the submitted business permit and verified that all information is correct and legitimate.
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger px-4 mr-2" onclick="rejectAdminFromModal(${reg.id})">
                    <i class="fas fa-times-circle mr-2"></i>Reject Registration
                </button>
                <button type="button" class="btn btn-success px-4" onclick="approveAdminFromModal(${reg.id})" id="approveBtn_${reg.id}" disabled>
                    <i class="fas fa-check-circle mr-2"></i>Accept & Approve
                </button>
            </div>
            ` : ''}
        `;
        
        // Add event listener for checkbox after content is loaded
        if (reg.registration_status === 'pending') {
            setTimeout(() => {
                const checkbox = document.getElementById('verifyCheck_' + reg.id);
                const approveBtn = document.getElementById('approveBtn_' + reg.id);
                if (checkbox && approveBtn) {
                    checkbox.addEventListener('change', function() {
                        approveBtn.disabled = !this.checked;
                    });
                }
            }, 100);
        }
        
        $('#viewDetailsModal').modal('show');
    }
    
    function approveAdminFromModal(id) {
        if (confirm('Accept this admin account? A verification code will be generated and sent.')) {
            window.location.href = '<?= BASE_URL ?>control-panel/approveAdmin/' + id;
        }
    }
    
    function rejectAdminFromModal(id) {
        $('#viewDetailsModal').modal('hide');
        setTimeout(() => {
            rejectAdmin(id);
        }, 300);
    }
    
    function viewVerificationCode(id, email) {
        $('#viewCodeModal').modal('show');
        const content = document.getElementById('viewCodeContent');
        
        fetch('<?= BASE_URL ?>control-panel/getVerificationCode/' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    content.innerHTML = `
                        <div class="text-center">
                            <p class="mb-1 text-gray-800">Registration code for <strong>${data.fullname}</strong></p>
                            <p class="small text-muted mb-4">${data.email}</p>
                            <div class="alert alert-warning border-left-warning py-4 mb-0">
                                <div style="font-size: 2rem; font-weight: 800; letter-spacing: 8px; color: #856404; font-family: monospace;">
                                    ${data.code}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    content.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                content.innerHTML = `<div class="alert alert-danger">Failed to load.</div>`;
            });
    }

    $(document).ready(function() {
        // DataTables are handled by footer.php safely
    });
</script>


