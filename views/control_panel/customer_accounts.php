<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Customer Accounts' ?> - UphoCare</title>
    
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
                        <i class="fas fa-users"></i> Customer Accounts
                    </h1>
                    <p class="page-subtitle">Manage customer account approvals</p>
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
                    <li class="breadcrumb-item active">Customer Accounts</li>
                </ol>
            </nav>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <h5 class="mb-3"><i class="fas fa-filter"></i> Filters</h5>
                <form method="GET" action="<?= BASE_URL ?>control-panel/customerAccounts" class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" <?= $data['filter_status'] === 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="active" <?= $data['filter_status'] === 'active' ? 'selected' : '' ?>>Active (Approved)</option>
                            <option value="inactive" <?= $data['filter_status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Pending/Rejected)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="<?= BASE_URL ?>control-panel/customerAccounts" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            
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
            
            <!-- Customer Accounts Table -->
            <div class="card table-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Customer Accounts 
                        <span class="badge bg-primary"><?= count($data['customers']) ?> Total</span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($data['customers'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No customer accounts found.</p>
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
                                    <?php foreach ($data['customers'] as $index => $customer): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($customer['fullname']) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($customer['email']) ?></td>
                                            <td><?= htmlspecialchars($customer['username']) ?></td>
                                            <td><?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php if ($customer['status'] === 'active'): ?>
                                                    <span class="badge badge-status bg-success">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-status bg-warning text-dark">
                                                        <i class="fas fa-clock"></i> Inactive
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y H:i', strtotime($customer['created_at'])) ?></td>
                                            <td>
                                                <?php if ($customer['status'] === 'inactive'): ?>
                                                    <a href="<?= BASE_URL ?>control-panel/approveCustomer/<?= $customer['id'] ?>" 
                                                       class="btn btn-sm btn-success me-1"
                                                       onclick="return confirm('Approve this customer account?')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </a>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="rejectCustomer(<?= $customer['id'] ?>)">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-warning" 
                                                            onclick="rejectCustomer(<?= $customer['id'] ?>)">
                                                        <i class="fas fa-ban"></i> Deactivate
                                                    </button>
                                                <?php endif; ?>
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function rejectCustomer(id) {
            const reason = prompt('Enter rejection/deactivation reason:');
            if (reason && reason.trim()) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>control-panel/rejectCustomer/' + id;
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'reason';
                input.value = reason.trim();
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

