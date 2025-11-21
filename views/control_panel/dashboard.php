<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Control Panel Dashboard' ?> - UphoCare</title>
    
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
        
        .stats-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card .card-body {
            padding: 25px;
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .bg-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-success-gradient {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
        }
        
        .bg-danger-gradient {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        
        .bg-info-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
                        <i class="fas fa-tachometer-alt"></i> Dashboard Overview
                    </h1>
                    <p class="page-subtitle">Monitor login activities and system access</p>
                </div>
                <div class="top-navbar-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($data['admin']['fullname'] ?? 'Admin') ?></div>
                            <div style="font-size: 12px; color: #7f8c8d;">Admin</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
        <!-- Statistics - Today -->
        <h5 class="mb-3"><i class="fas fa-calendar-day"></i> Today's Statistics</h5>
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary-gradient text-white me-3">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Logins Today</div>
                                <div class="h3 mb-0"><?= $data['stats']['today']['total'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success-gradient text-white me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Successful Today</div>
                                <div class="h3 mb-0"><?= $data['stats']['today']['successful'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-danger-gradient text-white me-3">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Failed Today</div>
                                <div class="h3 mb-0"><?= $data['stats']['today']['failed'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info-gradient text-white me-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <div class="text-muted small">This Week</div>
                                <div class="h3 mb-0"><?= $data['stats']['week']['total'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics by User Type -->
        <h5 class="mb-3"><i class="fas fa-users"></i> Login Statistics by User Type</h5>
        <div class="row mb-4">
            <?php foreach ($data['stats']['by_type'] as $typeStats): ?>
                <div class="col-md-4 mb-3">
                    <div class="card table-card">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-3">
                                <?php if ($typeStats['user_type'] === 'customer'): ?>
                                    <span class="badge badge-customer"><i class="fas fa-user"></i> Customer</span>
                                <?php elseif ($typeStats['user_type'] === 'admin'): ?>
                                    <span class="badge badge-admin"><i class="fas fa-user-shield"></i> Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-control"><i class="fas fa-shield-alt"></i> Control Panel</span>
                                <?php endif; ?>
                            </h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Logins:</span>
                                <strong><?= $typeStats['total'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success">Successful:</span>
                                <strong class="text-success"><?= $typeStats['successful'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-danger">Failed:</span>
                                <strong class="text-danger"><?= $typeStats['failed'] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Recent Login Activity -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card table-card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Recent Login Activity</h5>
                            <a href="<?= BASE_URL ?>/control-panel/loginLogs" class="btn btn-primary btn-sm">
                                <i class="fas fa-list"></i> View All Logs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="loginTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Type</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                        <th>Failure Reason</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['recent_logins'] as $log): ?>
                                        <tr>
                                            <td><?= $log['id'] ?></td>
                                            <td>
                                                <?php if ($log['user_type'] === 'customer'): ?>
                                                    <span class="badge badge-customer">Customer</span>
                                                <?php elseif ($log['user_type'] === 'admin'): ?>
                                                    <span class="badge badge-admin">Admin</span>
                                                <?php else: ?>
                                                    <span class="badge badge-control">Control Panel</span>
                                                <?php endif; ?>
                                            </td>
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
                                            <td><?= htmlspecialchars($log['failure_reason'] ?? '-') ?></td>
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
            $('#loginTable').DataTable({
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

