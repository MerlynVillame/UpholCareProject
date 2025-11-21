<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Admin Accounts' ?> - UphoCare</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .main-content-wrapper {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: all 0.3s ease;
            min-height: 100vh;
            position: relative;
        }

        .top-navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            margin: 0;
            margin-bottom: 30px;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 999;
            left: 0;
            right: 0;
        }

        .top-navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .page-subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 5px 0 0 0;
        }

        .top-navbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .content-area {
            padding: 0 30px 30px 30px;
            width: 100%;
            max-width: 100%;
        }
        
        .table-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .main-content-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .content-area {
                padding: 0 15px 15px 15px;
            }

            .top-navbar {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include ROOT . DS . 'views' . DS . 'control_panel' . DS . 'layouts' . DS . 'sidebar.php'; ?>
    
    <div class="main-content-wrapper">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="top-navbar-content">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-shield"></i> Admin Accounts
                    </h1>
                    <p class="page-subtitle">Monitor all admin accounts</p>
                </div>
                <div class="top-navbar-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($data['admin']['fullname'] ?? 'Admin') ?></div>
                            <div style="font-size: 12px; color: #7f8c8d;">Super Admin</div>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>control-panel/logout" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>control-panel/superAdminDashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admin Accounts</li>
                </ol>
            </nav>
            
            <!-- Tabs for Active Admins and Pending Admins -->
            <div class="card table-card mb-3">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= (!isset($data['view_type']) || $data['view_type'] === 'active') ? 'active' : '' ?>" 
                                    id="active-admins-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#active-admins" 
                                    type="button" 
                                    role="tab"
                                    onclick="window.location.href='<?= BASE_URL ?>control-panel/adminAccounts?view=active'">
                                <i class="fas fa-user-shield"></i> Active Admins
                                <span class="badge bg-primary"><?= $data['active_count'] ?? 0 ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= (isset($data['view_type']) && $data['view_type'] === 'pending') ? 'active' : '' ?>" 
                                    id="pending-admins-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#pending-admins" 
                                    type="button" 
                                    role="tab"
                                    onclick="window.location.href='<?= BASE_URL ?>control-panel/adminAccounts?view=pending'">
                                <i class="fas fa-clock"></i> Pending Admins
                                <span class="badge bg-warning text-dark"><?= $data['pending_count'] ?? 0 ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Filter Section (only for Active Admins) -->
            <?php if (!isset($data['view_type']) || $data['view_type'] === 'active'): ?>
            <div class="filter-section">
                <h5 class="mb-3"><i class="fas fa-filter"></i> Filters</h5>
                <form method="GET" action="<?= BASE_URL ?>control-panel/adminAccounts" class="row g-3">
                    <input type="hidden" name="view" value="active">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" <?= $data['filter_status'] === 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="active" <?= $data['filter_status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $data['filter_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="<?= BASE_URL ?>control-panel/adminAccounts?view=active" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Active Admins Tab -->
                <div class="tab-pane fade <?= (!isset($data['view_type']) || $data['view_type'] === 'active') ? 'show active' : '' ?>" 
                     id="active-admins" 
                     role="tabpanel">
                    <div class="card table-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-user-shield"></i> Active Admin Accounts 
                                <span class="badge bg-primary"><?= count($data['admins'] ?? []) ?> Total</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($data['admins'])): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No admin accounts found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
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
                                            <?php foreach ($data['admins'] as $index => $admin): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($admin['fullname']) ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?= ucfirst($admin['role']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($admin['status'] === 'active'): ?>
                                                            <span class="badge badge-status bg-success">
                                                                <i class="fas fa-check-circle"></i> Active
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge badge-status bg-secondary">
                                                                <i class="fas fa-times-circle"></i> Inactive
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($admin['last_login']): ?>
                                                            <?= date('M d, Y H:i', strtotime($admin['last_login'])) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Never</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('M d, Y H:i', strtotime($admin['created_at'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Admins Tab -->
                <div class="tab-pane fade <?= (isset($data['view_type']) && $data['view_type'] === 'pending') ? 'show active' : '' ?>" 
                     id="pending-admins" 
                     role="tabpanel">
                    <div class="card table-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-clock"></i> Pending Admin Accounts 
                                <span class="badge bg-warning text-dark"><?= count($data['pending_admins'] ?? []) ?> Pending</span>
                            </h5>
                            <p class="text-muted mb-0 mt-2">
                                <small><i class="fas fa-info-circle"></i> These are admin accounts that are pending and waiting for your acceptance. Use <strong>Accept</strong> to approve and generate a verification code, or <strong>Reject</strong> to decline the registration.</small>
                            </p>
                        </div>
                        <div class="card-body">
                            <?php if (empty($data['pending_admins'])): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p class="text-muted">No pending admin accounts. All registrations have been processed.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
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
                                            <?php foreach ($data['pending_admins'] as $index => $reg): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($reg['fullname']) ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($reg['email']) ?></td>
                                                    <td><?= htmlspecialchars($reg['username'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($reg['phone'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if ($reg['registration_status'] === 'pending_verification'): ?>
                                                            <span class="badge badge-status bg-info text-white">
                                                                <i class="fas fa-envelope"></i> Code Sent - Waiting for Verification
                                                            </span>
                                                        <?php elseif ($reg['registration_status'] === 'pending'): ?>
                                                            <span class="badge badge-status bg-warning text-dark">
                                                                <i class="fas fa-clock"></i> Pending - Waiting for Acceptance
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('M d, Y H:i', strtotime($reg['created_at'])) ?></td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                                            <!-- View Details Button -->
                                                            <button class="btn btn-sm btn-info" 
                                                                    onclick="viewAdminDetails(<?= htmlspecialchars(json_encode($reg), ENT_QUOTES) ?>)"
                                                                    title="View Details">
                                                                <i class="fas fa-eye"></i> View
                                                            </button>
                                                            
                                                            <?php if ($reg['registration_status'] === 'pending'): ?>
                                                                <!-- Accept Account -->
                                                                <a href="<?= BASE_URL ?>control-panel/approveAdmin/<?= $reg['id'] ?>" 
                                                                   class="btn btn-sm btn-success"
                                                                   onclick="return confirm('Accept this admin account? A verification code will be automatically generated so the admin can complete their registration and log in.')"
                                                                   title="Accept this admin account">
                                                                    <i class="fas fa-check-circle"></i> Accept
                                                                </a>
                                                                
                                                                <!-- Reject Account -->
                                                                <button class="btn btn-sm btn-danger" 
                                                                        onclick="rejectAdmin(<?= $reg['id'] ?>)"
                                                                        title="Reject this admin account">
                                                                    <i class="fas fa-times-circle"></i> Reject
                                                                </button>
                                                                
                                                            <?php elseif ($reg['registration_status'] === 'pending_verification'): ?>
                                                                <!-- View Verification Code -->
                                                                <?php if ($reg['verification_code_sent_at']): ?>
                                                                    <button class="btn btn-sm btn-warning" 
                                                                            onclick="viewVerificationCode(<?= $reg['id'] ?>, '<?= htmlspecialchars($reg['email']) ?>')"
                                                                            title="View Verification Code">
                                                                        <i class="fas fa-key"></i> View Code
                                                                    </button>
                                                                <?php endif; ?>
                                                                
                                                                <!-- Resend Code -->
                                                                <?php if ($reg['verification_code_sent_at']): ?>
                                                                    <a href="<?= BASE_URL ?>control-panel/sendVerificationCode/<?= $reg['id'] ?>" 
                                                                       class="btn btn-sm btn-outline-primary"
                                                                       onclick="return confirm('Resend verification code to <?= htmlspecialchars($reg['email']) ?>?')">
                                                                        <i class="fas fa-redo"></i> Resend
                                                                    </a>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewDetailsModalLabel">
                        <i class="fas fa-user-shield"></i> Admin Registration Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Verification Code Modal -->
    <div class="modal fade" id="viewCodeModal" tabindex="-1" aria-labelledby="viewCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="viewCodeModalLabel">
                        <i class="fas fa-key"></i> Verification Code
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewCodeContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading verification code...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function rejectAdmin(id) {
            const reason = prompt('Enter rejection reason:');
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
        
        function viewAdminDetails(reg) {
            const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
            const content = document.getElementById('viewDetailsContent');
            
            const statusBadges = {
                'pending_verification': '<span class="badge bg-info">Waiting for Verification</span>',
                'pending': '<span class="badge bg-warning text-dark">Pending Approval</span>',
                'approved': '<span class="badge bg-success">Approved</span>',
                'rejected': '<span class="badge bg-danger">Rejected</span>'
            };
            
            const statusBadge = statusBadges[reg.registration_status] || '<span class="badge bg-secondary">Unknown</span>';
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-user"></i> Full Name:</strong>
                        <p class="mb-0">${reg.fullname || 'N/A'}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-envelope"></i> Email:</strong>
                        <p class="mb-0">${reg.email || 'N/A'}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-user-circle"></i> Username:</strong>
                        <p class="mb-0">${reg.username || 'N/A'}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-phone"></i> Phone:</strong>
                        <p class="mb-0">${reg.phone || 'N/A'}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-info-circle"></i> Status:</strong>
                        <p class="mb-0">${statusBadge}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-calendar"></i> Registered On:</strong>
                        <p class="mb-0">${reg.created_at ? new Date(reg.created_at).toLocaleString() : 'N/A'}</p>
                    </div>
                    ${reg.approved_at ? `
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-check-circle"></i> Approved On:</strong>
                        <p class="mb-0">${new Date(reg.approved_at).toLocaleString()}</p>
                    </div>
                    ` : ''}
                    ${reg.verification_code_sent_at ? `
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-key"></i> Code Sent On:</strong>
                        <p class="mb-0">${new Date(reg.verification_code_sent_at).toLocaleString()}</p>
                    </div>
                    ` : ''}
                    ${reg.verification_code_verified_at ? `
                    <div class="col-md-6 mb-3">
                        <strong><i class="fas fa-check"></i> Code Verified On:</strong>
                        <p class="mb-0">${new Date(reg.verification_code_verified_at).toLocaleString()}</p>
                    </div>
                    ` : ''}
                    ${reg.rejection_reason ? `
                    <div class="col-12 mb-3">
                        <strong><i class="fas fa-times-circle"></i> Rejection Reason:</strong>
                        <p class="mb-0 alert alert-danger">${reg.rejection_reason}</p>
                    </div>
                    ` : ''}
                </div>
            `;
            
            modal.show();
        }
        
        function viewVerificationCode(id, email) {
            const modal = new bootstrap.Modal(document.getElementById('viewCodeModal'));
            const content = document.getElementById('viewCodeContent');
            
            content.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading verification code...</p>
                </div>
            `;
            
            modal.show();
            
            fetch('<?= BASE_URL ?>control-panel/getVerificationCode/' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const sentDate = data.sent_at ? new Date(data.sent_at).toLocaleString() : 'N/A';
                        content.innerHTML = `
                            <div class="text-center">
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Admin Name:</strong> ${data.fullname}</p>
                                <hr>
                                <div class="alert alert-warning">
                                    <h4 class="alert-heading"><i class="fas fa-key"></i> Verification Code</h4>
                                    <div style="font-size: 2.5rem; font-weight: bold; letter-spacing: 10px; color: #856404; font-family: 'Courier New', monospace; margin: 20px 0;">
                                        ${data.code}
                                    </div>
                                    <p class="mb-0"><small>Code sent on: ${sentDate}</small></p>
                                </div>
                                <p class="text-muted">
                                    <small>This code can be viewed by the admin on the verification page.</small>
                                </p>
                            </div>
                        `;
                    } else {
                        content.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to load verification code. Please try again.
                        </div>
                    `;
                    console.error('Error:', error);
                });
        }
    </script>
</body>
</html>

