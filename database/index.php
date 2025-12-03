<?php
/**
 * Database Test Data Management Dashboard
 * Quick access to seeding and removal tools
 */

require_once __DIR__ . '/../config/database.php';

// Get statistics
$db = Database::getInstance()->getConnection();

$testRecordsStmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE notes = 'TEST_DATA_DO_NOT_DELETE'");
$testRecordsStmt->execute();
$testRecords = $testRecordsStmt->fetch(PDO::FETCH_ASSOC);
$testRecordCount = $testRecords['count'];

$totalRecordsStmt = $db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed' AND payment_status = 'paid'");
$totalRecords = $totalRecordsStmt->fetch(PDO::FETCH_ASSOC);
$totalRecordCount = $totalRecords['count'];

// Get test records by year
$yearStatsStmt = $db->prepare("
    SELECT YEAR(updated_at) as year, COUNT(*) as count 
    FROM bookings 
    WHERE notes = 'TEST_DATA_DO_NOT_DELETE'
    GROUP BY YEAR(updated_at)
    ORDER BY year DESC
");
$yearStatsStmt->execute();
$yearStats = $yearStatsStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Test Data Management - UphoCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .stat-card.test-records .icon { color: #ffc107; }
        .stat-card.total-records .icon { color: #28a745; }
        .stat-card.percentage .icon { color: #17a2b8; }
        
        .stat-card h3 {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .action-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .action-card h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .action-card p {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .action-card ul {
            color: #6c757d;
            margin: 15px 0 20px 20px;
            line-height: 1.8;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
            color: white;
        }
        
        .btn-success:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(86, 171, 47, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        
        .btn-danger:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(235, 51, 73, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: scale(1.05);
        }
        
        .year-breakdown {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .year-breakdown h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        .year-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .year-item:last-child {
            border-bottom: none;
        }
        
        .year-item:hover {
            background: #f8f9fa;
        }
        
        .year-label {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .year-count {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 20px;
        }
        
        .footer-links a {
            margin: 0 10px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
        }
        
        .alert-info {
            background: #d1ecf1;
            border: 2px solid #17a2b8;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-database"></i> Database Test Data Management</h1>
            <p>Manage test booking records for yearly reports testing</p>
        </div>
        
        <?php if ($testRecordCount > 0): ?>
        <div class="alert alert-warning">
            <strong><i class="fas fa-exclamation-triangle"></i> Test Data Active:</strong> 
            There are currently <?php echo number_format($testRecordCount); ?> test records in the database.
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <strong><i class="fas fa-info-circle"></i> No Test Data:</strong> 
            Database contains no test records. Click "Seed Test Data" to add sample bookings.
        </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card test-records">
                <div class="icon"><i class="fas fa-vial"></i></div>
                <h3>Test Records</h3>
                <div class="number"><?php echo number_format($testRecordCount); ?></div>
            </div>
            
            <div class="stat-card total-records">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h3>Total Completed</h3>
                <div class="number"><?php echo number_format($totalRecordCount); ?></div>
            </div>
            
            <div class="stat-card percentage">
                <div class="icon"><i class="fas fa-percentage"></i></div>
                <h3>Test Data %</h3>
                <div class="number">
                    <?php 
                    $percentage = $totalRecordCount > 0 ? ($testRecordCount / $totalRecordCount) * 100 : 0;
                    echo number_format($percentage, 1); 
                    ?>%
                </div>
            </div>
        </div>
        
        <?php if (!empty($yearStats)): ?>
        <!-- Year Breakdown -->
        <div class="year-breakdown">
            <h2><i class="fas fa-calendar-alt"></i> Test Records by Year</h2>
            <?php foreach ($yearStats as $stat): ?>
            <div class="year-item">
                <span class="year-label">
                    <i class="fas fa-calendar"></i> Year <?php echo $stat['year']; ?>
                </span>
                <span class="year-count"><?php echo number_format($stat['count']); ?> bookings</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Actions -->
        <div class="actions-grid">
            <div class="action-card">
                <h2><i class="fas fa-bolt"></i> Quick Seed (2024-2025)</h2>
                <p>Creates sample bookings for <strong>2024-2025 ONLY</strong> - Fast setup!</p>
                <ul>
                    <li>5-7 bookings per month</li>
                    <li>~140 total records (2 years)</li>
                    <li>Perfect for quick testing</li>
                    <li>See monthly graphs immediately</li>
                    <li>Takes ~5 seconds</li>
                </ul>
                <a href="seed_2024_2025_data.php" class="btn btn-success">
                    <i class="fas fa-bolt"></i> Quick Seed (Recommended)
                </a>
            </div>
            
            <div class="action-card">
                <h2><i class="fas fa-seedling"></i> Full Historical Data</h2>
                <p>Creates complete dataset for <strong>2010-2025</strong> (15 years).</p>
                <ul>
                    <li>3-8 bookings per month (increases over time)</li>
                    <li>~600-900 total records</li>
                    <li>4% annual inflation simulation</li>
                    <li>Business growth simulation</li>
                    <li>Takes ~30-60 seconds</li>
                </ul>
                <a href="seed_yearly_test_data.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Seed Full History
                </a>
            </div>
            
            <div class="action-card">
                <h2><i class="fas fa-trash-alt"></i> Remove Test Data</h2>
                <p>Safely removes all test booking records.</p>
                <ul>
                    <li>Confirmation required</li>
                    <li>Only deletes test records</li>
                    <li>Cannot be undone</li>
                    <li>Preserves real data</li>
                </ul>
                <a href="remove_test_data.php" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Remove Test Data
                </a>
            </div>
            
            <div class="action-card">
                <h2><i class="fas fa-chart-line"></i> View Reports</h2>
                <p>Test the yearly reports with search functionality.</p>
                <ul>
                    <li>Search by year</li>
                    <li>View line graphs</li>
                    <li>Monthly breakdown</li>
                    <li>Profit margins</li>
                </ul>
                <a href="../admin/reports" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i> Go to Reports
                </a>
            </div>
            
            <div class="action-card">
                <h2><i class="fas fa-book"></i> Documentation</h2>
                <p>View detailed instructions and information.</p>
                <ul>
                    <li>Usage instructions</li>
                    <li>Test data details</li>
                    <li>Troubleshooting guide</li>
                    <li>Safety features</li>
                </ul>
                <a href="README_TEST_DATA.md" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-file-alt"></i> Read Documentation
                </a>
            </div>
        </div>
        
        <!-- Footer Links -->
        <div class="footer-links">
            <a href="../admin/dashboard" class="btn btn-secondary">
                <i class="fas fa-home"></i> Admin Dashboard
            </a>
            <a href="../admin/bookings" class="btn btn-secondary">
                <i class="fas fa-clipboard-list"></i> All Bookings
            </a>
        </div>
    </div>
</body>
</html>

