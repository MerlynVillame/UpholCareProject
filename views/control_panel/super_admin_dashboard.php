<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Super Admin Dashboard' ?> - UphoCare</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
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
            background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            padding: 15px 30px;
            margin: 0;
            margin-bottom: 30px;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 999;
            left: 0;
            right: 0;
            color: white;
        }

        .top-navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.8);
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
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            border: 2px solid white;
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
            margin-bottom: 20px;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
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
            background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%);
        }
        
        .bg-success-gradient {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
        }
        
        .bg-warning-gradient {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
        }
        
        .bg-info-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .bg-danger-gradient {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        
        .bg-purple-gradient {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        }
        
        .bg-orange-gradient {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        
        .table-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .stats-card .card-body {
            padding: 25px;
        }
        
        .stats-card .h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .stats-card .small {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .stats-trend {
            font-size: 0.75rem;
            margin-top: 5px;
        }
        
        .stats-trend.up {
            color: #28a745;
        }
        
        .stats-trend.down {
            color: #dc3545;
        }
        
        .chart-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            padding: 20px;
        }
        
        .quick-action-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            background: white;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .quick-action-btn {
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 5px;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .alert-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .system-health {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .health-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .health-indicator.good {
            background: #28a745;
        }
        
        .health-indicator.warning {
            background: #ffc107;
        }
        
        .health-indicator.danger {
            background: #dc3545;
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
                        <i class="fas fa-crown"></i> Super Admin Dashboard
                    </h1>
                    <p class="page-subtitle">Manage system and admin accounts</p>
                </div>
                <div class="top-navbar-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($data['admin']['fullname'] ?? 'Admin') ?></div>
                            <div style="font-size: 12px; color: rgba(255, 255, 255, 0.8);">Super Admin</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
        <!-- System Health Alert -->
        <?php 
        $pendingCount = ($data['stats']['pending_admin_registrations'] ?? 0) + ($data['stats']['pending_customer_accounts'] ?? 0);
        if ($pendingCount > 0): 
        ?>
        <div class="alert-card alert alert-warning mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1"><strong>Action Required</strong></h5>
                    <p class="mb-0">
                        You have <strong><?= $pendingCount ?></strong> pending item(s) requiring your attention:
                        <?php if ($data['stats']['pending_admin_registrations'] > 0): ?>
                            <a href="<?= BASE_URL ?>control-panel/adminRegistrations" class="alert-link"><?= $data['stats']['pending_admin_registrations'] ?> admin registration(s)</a>
                        <?php endif; ?>
                        <?php if (($data['stats']['pending_customer_accounts'] ?? 0) > 0): ?>
                            <?php if ($data['stats']['pending_admin_registrations'] > 0): ?>, <?php endif; ?>
                            <a href="<?= BASE_URL ?>control-panel/customerAccounts?status=inactive" class="alert-link"><?= $data['stats']['pending_customer_accounts'] ?> customer account(s)</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Statistics Cards Row 1 -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary-gradient text-white me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">Total Customers</div>
                                <div class="h3 mb-0"><?= number_format($data['stats']['total_customers'] ?? 0) ?></div>
                                <?php if (($data['stats']['new_customers_today'] ?? 0) > 0): ?>
                                    <div class="stats-trend up">
                                        <i class="fas fa-arrow-up"></i> <?= $data['stats']['new_customers_today'] ?> new today
                                    </div>
                                <?php endif; ?>
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
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">Active Admins</div>
                                <div class="h3 mb-0"><?= number_format($data['stats']['total_active_admins'] ?? 0) ?></div>
                                <div class="stats-trend">
                                    <i class="fas fa-crown"></i> <?= $data['stats']['total_super_admins'] ?? 0 ?> super admin(s)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning-gradient text-white me-3">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">Total Bookings</div>
                                <div class="h3 mb-0"><?= number_format($data['stats']['total_bookings'] ?? 0) ?></div>
                                <?php if (($data['stats']['pending_bookings'] ?? 0) > 0): ?>
                                    <div class="stats-trend warning">
                                        <i class="fas fa-clock"></i> <?= $data['stats']['pending_bookings'] ?> pending
                                    </div>
                                <?php endif; ?>
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
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">Total Revenue</div>
                                <div class="h3 mb-0">₱<?= number_format($data['stats']['total_revenue'] ?? 0, 2) ?></div>
                                <div class="stats-trend">
                                    <i class="fas fa-calendar"></i> ₱<?= number_format($data['stats']['month_revenue'] ?? 0, 2) ?> this month
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards Row 2 -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-danger-gradient text-white me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">Pending Admin Registrations</div>
                                <div class="h3 mb-0"><?= number_format($data['stats']['pending_admin_registrations'] ?? 0) ?></div>
                                <div class="stats-trend">
                                    <a href="<?= BASE_URL ?>control-panel/adminRegistrations" class="text-decoration-none">
                                        <i class="fas fa-eye"></i> Review now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-purple-gradient text-white me-3">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">Pending Customer Accounts</div>
                                <div class="h3 mb-0"><?= number_format($data['stats']['pending_customer_accounts'] ?? 0) ?></div>
                                <div class="stats-trend">
                                    <a href="<?= BASE_URL ?>control-panel/customerAccounts?status=inactive" class="text-decoration-none">
                                        <i class="fas fa-eye"></i> Review now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-orange-gradient text-white me-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">Today's Revenue</div>
                                <div class="h3 mb-0">₱<?= number_format($data['stats']['today_revenue'] ?? 0, 2) ?></div>
                                <div class="stats-trend">
                                    <i class="fas fa-calendar-day"></i> <?= date('M d, Y') ?>
                                </div>
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
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small">This Month's Revenue</div>
                                <div class="h3 mb-0">₱<?= number_format($data['stats']['month_revenue'] ?? 0, 2) ?></div>
                                <div class="stats-trend">
                                    <i class="fas fa-calendar-alt"></i> <?= date('F Y') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-chart-area"></i> Revenue Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card table-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="<?= BASE_URL ?>control-panel/adminRegistrations" class="btn btn-primary-admin quick-action-btn">
                            <i class="fas fa-user-check"></i> Manage Admin Registrations
                            <?php if (($data['stats']['pending_admin_registrations'] ?? 0) > 0): ?>
                                <span class="badge bg-light text-dark"><?= $data['stats']['pending_admin_registrations'] ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?= BASE_URL ?>control-panel/adminAccounts" class="btn btn-info quick-action-btn">
                            <i class="fas fa-user-shield"></i> Monitor Admin Accounts
                        </a>
                        <a href="<?= BASE_URL ?>control-panel/customerAccounts" class="btn btn-purple quick-action-btn" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white;">
                            <i class="fas fa-users"></i> Manage Customer Accounts
                            <?php if (($data['stats']['pending_customer_accounts'] ?? 0) > 0): ?>
                                <span class="badge bg-light text-dark"><?= $data['stats']['pending_customer_accounts'] ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?= BASE_URL ?>control-panel/loginLogs" class="btn btn-secondary quick-action-btn">
                            <i class="fas fa-history"></i> Login Logs
                        </a>
                        <a href="<?= BASE_URL ?>control-panel/register" class="btn btn-warning quick-action-btn">
                            <i class="fas fa-user-plus"></i> Register Super Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending Admin Registrations -->
        <?php if (!empty($data['pending_admins'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card table-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-user-clock"></i> Pending Admin Registrations 
                            <span class="badge bg-warning"><?= count($data['pending_admins']) ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Phone</th>
                                        <th>Registered On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['pending_admins'] as $reg): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($reg['fullname']) ?></td>
                                            <td><?= htmlspecialchars($reg['email']) ?></td>
                                            <td><?= htmlspecialchars($reg['username']) ?></td>
                                            <td><?= htmlspecialchars($reg['phone'] ?? 'N/A') ?></td>
                                            <td><?= date('M d, Y', strtotime($reg['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>control-panel/approveAdmin/<?= $reg['id'] ?>" 
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('Approve this admin registration?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </a>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="rejectAdmin(<?= $reg['id'] ?>)">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Total Revenue', 'This Month', 'Today'],
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: [
                            <?= $data['stats']['total_revenue'] ?? 0 ?>,
                            <?= $data['stats']['month_revenue'] ?? 0 ?>,
                            <?= $data['stats']['today_revenue'] ?? 0 ?>
                        ],
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function rejectAdmin(id) {
            const reason = prompt('Enter rejection reason:');
            if (reason) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>control-panel/rejectAdmin/' + id;
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'reason';
                input.value = reason;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

