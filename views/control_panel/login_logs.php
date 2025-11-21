<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Login Logs' ?> - UphoCare</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
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
        
        .filter-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .table-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .badge-success-custom {
            background-color: #28a745;
            color: white;
        }
        
        .badge-danger-custom {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-customer {
            background-color: #17a2b8;
        }
        
        .badge-admin {
            background-color: #6610f2;
        }
        
        .badge-control {
            background-color: #e83e8c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content-wrapper {
                margin-left: 0;
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
                        <i class="fas fa-history"></i> Login Logs
                    </h1>
                    <p class="page-subtitle">View and filter all login activities</p>
                </div>
                <div class="top-navbar-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($data['admin']['fullname'] ?? 'Admin') ?></div>
                            <div style="font-size: 12px; color: #7f8c8d;">
                                <?= isset($data['is_super_admin']) && $data['is_super_admin'] ? 'Super Admin' : 'Admin' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
        
        <!-- Filters -->
        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" action="<?= BASE_URL ?>/control-panel/loginLogs">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label"><i class="fas fa-user-tag"></i> User Type</label>
                            <select name="type" class="form-select">
                                <option value="all" <?= $data['filter_type'] === 'all' ? 'selected' : '' ?>>All Types</option>
                                <option value="customer" <?= $data['filter_type'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                <option value="admin" <?= $data['filter_type'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="control_panel" <?= $data['filter_type'] === 'control_panel' ? 'selected' : '' ?>>Control Panel</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label"><i class="fas fa-check-circle"></i> Status</label>
                            <select name="status" class="form-select">
                                <option value="all" <?= $data['filter_status'] === 'all' ? 'selected' : '' ?>>All Status</option>
                                <option value="success" <?= $data['filter_status'] === 'success' ? 'selected' : '' ?>>Successful</option>
                                <option value="failed" <?= $data['filter_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3 mb-md-0">
                            <label class="form-label"><i class="fas fa-list-ol"></i> Limit</label>
                            <select name="limit" class="form-select">
                                <option value="50" <?= $data['filter_limit'] === 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $data['filter_limit'] === 100 ? 'selected' : '' ?>>100</option>
                                <option value="250" <?= $data['filter_limit'] === 250 ? 'selected' : '' ?>>250</option>
                                <option value="500" <?= $data['filter_limit'] === 500 ? 'selected' : '' ?>>500</option>
                                <option value="1000" <?= $data['filter_limit'] === 1000 ? 'selected' : '' ?>>1000</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Login Logs Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card table-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-database"></i> 
                            Login Records (<?= count($data['login_logs']) ?> entries)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="loginLogsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Type</th>
                                        <th>User ID</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                        <th>Failure Reason</th>
                                        <th>Login Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['login_logs'] as $log): ?>
                                        <tr>
                                            <td><?= $log['id'] ?></td>
                                            <td>
                                                <?php if ($log['user_type'] === 'customer'): ?>
                                                    <span class="badge badge-customer">
                                                        <i class="fas fa-user"></i> Customer
                                                    </span>
                                                <?php elseif ($log['user_type'] === 'admin'): ?>
                                                    <span class="badge badge-admin">
                                                        <i class="fas fa-user-shield"></i> Admin
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-control">
                                                        <i class="fas fa-shield-alt"></i> Control
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $log['user_id'] ?? '-' ?></td>
                                            <td><?= htmlspecialchars($log['email']) ?></td>
                                            <td><?= htmlspecialchars($log['fullname'] ?? 'N/A') ?></td>
                                            <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                                            <td>
                                                <?php if ($log['login_status'] === 'success'): ?>
                                                    <span class="badge badge-success-custom">
                                                        <i class="fas fa-check"></i> Success
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger-custom">
                                                        <i class="fas fa-times"></i> Failed
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($log['failure_reason']): ?>
                                                    <span class="text-danger">
                                                        <?= htmlspecialchars($log['failure_reason']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y H:i:s', strtotime($log['login_time'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#loginLogsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                language: {
                    search: "Search logs:"
                }
            });
        });
    </script>
</body>
</html>

