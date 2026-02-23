<?php
/**
 * Admin Controller
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';
require_once ROOT . DS . 'helpers' . DS . 'FeeCalculator.php';
require_once ROOT . DS . 'core' . DS . 'AutoStatusUpdateService.php';

class AdminController extends Controller {
    
    private $bookingModel;
    
    public function __construct() {
        // SECURITY: Require admin role and verify role integrity for all admin pages
        $this->requireAdmin();
        $this->verifyRoleIntegrity();
        $this->bookingModel = $this->model('Booking');
    }
    
    /**
     * Admin Dashboard
     */
    public function dashboard() {
        // Run automatic status updates based on pickup_date
        try {
            $updateStats = AutoStatusUpdateService::run();
            // Log update statistics for debugging
            if ($updateStats['checked'] > 0 || $updateStats['updated'] > 0) {
                error_log("AutoStatusUpdate in dashboard: Checked: {$updateStats['checked']}, Updated: {$updateStats['updated']}, Errors: {$updateStats['errors']}");
            }
        } catch (Exception $e) {
            error_log("AutoStatusUpdate error in dashboard: " . $e->getMessage());
            error_log("AutoStatusUpdate stack trace: " . $e->getTraceAsString());
        }
        
        // Get booking statistics
        $totalBookings = $this->bookingModel->getTotalBookings();
        $pendingBookings = $this->bookingModel->getBookingCountByStatus(null, 'pending');
        $completedBookings = $this->bookingModel->getBookingCountByStatus(null, 'completed');
        $totalRevenue = $this->bookingModel->getTotalRevenue();
        
        // Get recent bookings
        $recentBookings = $this->bookingModel->getRecentBookings(null, 10);
        
        $data = [
            'title' => 'Admin Dashboard - ' . APP_NAME,
            'user' => $this->currentUser(),
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'completedBookings' => $completedBookings,
            'totalRevenue' => $totalRevenue,
            'recentBookings' => $recentBookings
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    /**
     * Manage Orders
     */
    public function orders() {
        $data = [
            'title' => 'Manage Orders - ' . APP_NAME,
            'user' => $this->currentUser()
        ];
        
        $this->view('admin/orders', $data);
    }



    /**
     * Archive Booking
     */
    public function archiveBooking($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->bookingModel->archive($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to archive booking']);
            }
            exit;
        }
    }

    /**
     * Archived Bookings View
     */
    public function archivedBookings() {
        $bookings = $this->bookingModel->getArchivedBookings();
        
        $data = [
            'title' => 'Archived Bookings - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $bookings
        ];
        
        $this->view('admin/archived_bookings', $data);
    }
    
    /**
     * Reports
     */
    public function reports($year = null) {
        $db = Database::getInstance()->getConnection();
        
        // Get year from parameter or use current year
        $selectedYear = $year ? (int)$year : (int)date('Y');
        $currentYear = (int)date('Y');
        
        // Get monthly sales data from database
        $monthlySalesData = [];
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = sprintf('%04d-%02d-01', $selectedYear, $month);
            $monthEnd = sprintf('%04d-%02d-%d', $selectedYear, $month, date('t', strtotime($monthStart)));
            
            // Get completed/paid and delivered/paid bookings for this month
            // Include both statuses: 'completed' (with paid status) and 'delivered_and_paid'
            // Use updated_at for completion date, fallback to created_at
            // Join with users and services tables to get customer name and service details
            $sql = "SELECT 
                        COUNT(*) as orders,
                        COALESCE(SUM(b.total_amount), 0) as revenue,
                        GROUP_CONCAT(
                            CONCAT(
                                'ID:', b.id, 
                                '|Amount:', b.total_amount, 
                                '|Date:', COALESCE(b.updated_at, b.created_at),
                                '|Customer:', COALESCE(u.fullname, 'Unknown'),
                                '|Service:', COALESCE(s.service_name, 'N/A'),
                                '|Item:', COALESCE(b.item_description, 'N/A'),
                                '|ItemType:', COALESCE(b.item_type, 'N/A'),
                                '|ServiceType:', COALESCE(b.service_type, 'N/A'),
                                '|Notes:', COALESCE(b.notes, 'N/A'),
                                '|Status:', b.status
                            ) SEPARATOR ';;'
                        ) as booking_details
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN services s ON b.service_id = s.id
                    WHERE (
                        (b.status = 'completed' AND b.payment_status IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod'))
                        OR b.status = 'delivered_and_paid'
                    )
                    AND (
                        (b.completion_date IS NOT NULL AND DATE(b.completion_date) BETWEEN ? AND ?)
                        OR (b.completion_date IS NULL AND b.updated_at IS NOT NULL AND DATE(b.updated_at) BETWEEN ? AND ?)
                        OR (b.completion_date IS NULL AND b.updated_at IS NULL AND DATE(b.created_at) BETWEEN ? AND ?)
                    )";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$monthStart, $monthEnd, $monthStart, $monthEnd, $monthStart, $monthEnd]);
            $result = $stmt->fetch();
            
            $orders = (int)($result['orders'] ?? 0);
            $revenue = (float)($result['revenue'] ?? 0);
            $bookingDetails = $result['booking_details'] ?? '';
            
            // Calculate expenses (30% of revenue as estimated operating costs)
            $expenses = $revenue * 0.30;
            $profit = $revenue - $expenses;
            
            $monthlySalesData[] = [
                'month' => $monthNames[$month - 1],
                'orders' => $orders,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'booking_details' => $bookingDetails // Store details for drill-down
            ];
        }
        
        // Calculate totals and highest income
        $totalRevenue = 0;
        $totalOrders = 0;
        $totalExpenses = 0;
        $totalProfit = 0;
        $highestIncome = 0;
        $highestIncomeMonth = '';
        
        foreach ($monthlySalesData as $data) {
            $totalRevenue += $data['revenue'];
            $totalOrders += $data['orders'];
            $totalExpenses += $data['expenses'];
            $totalProfit += $data['profit'];
            
            if ($data['revenue'] > $highestIncome) {
                $highestIncome = $data['revenue'];
                $highestIncomeMonth = $data['month'];
            }
        }
        
        $averageRevenue = count($monthlySalesData) > 0 ? $totalRevenue / count($monthlySalesData) : 0;
        $averageProfit = count($monthlySalesData) > 0 ? $totalProfit / count($monthlySalesData) : 0;
        
        // Get current month data
        $currentMonth = (int)date('n');
        $currentMonthData = $monthlySalesData[$currentMonth - 1] ?? ['revenue' => 0, 'orders' => 0];
        
        // Get previous month for growth calculation
        $previousMonth = $currentMonth > 1 ? $currentMonth - 2 : 11;
        $previousMonthData = $monthlySalesData[$previousMonth] ?? ['revenue' => 0];
        
        $currentMonthRevenue = $currentMonthData['revenue'];
        $previousMonthRevenue = $previousMonthData['revenue'];
        $growthPercentage = $previousMonthRevenue > 0 
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
            : 0;
        
        // Prepare data for chart
        $months = array_map(function($d) { return $d['month']; }, $monthlySalesData);
        $revenues = array_map(function($d) { return $d['revenue']; }, $monthlySalesData);
        $profits = array_map(function($d) { return $d['profit']; }, $monthlySalesData);
        $expenses = array_map(function($d) { return $d['expenses']; }, $monthlySalesData);
        
        // Get available years from database (completed/paid and delivered/paid bookings only)
        $yearsSql = "SELECT DISTINCT YEAR(COALESCE(updated_at, created_at)) as year 
                     FROM bookings 
                     WHERE (
                         (status = 'completed' AND payment_status IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod'))
                         OR status = 'delivered_and_paid'
                     )
                     ORDER BY year DESC";
        $yearsStmt = $db->query($yearsSql);
        $availableYears = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Ensure current year is in the list
        if (!in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
            rsort($availableYears);
        }
        
        $data = [
            'title' => 'Reports - ' . APP_NAME,
            'user' => $this->currentUser(),
            'monthlySalesData' => $monthlySalesData,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'highestIncome' => $highestIncome,
            'highestIncomeMonth' => $highestIncomeMonth,
            'averageRevenue' => $averageRevenue,
            'averageProfit' => $averageProfit,
            'currentMonthRevenue' => $currentMonthRevenue,
            'currentMonthOrders' => $currentMonthData['orders'],
            'growthPercentage' => $growthPercentage,
            'chartMonths' => json_encode($months),
            'chartRevenues' => json_encode($revenues),
            'chartProfits' => json_encode($profits),
            'chartExpenses' => json_encode($expenses),
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears
        ];
        
        $this->view('admin/reports', $data);
    }

    /**
     * Generate & Download Report
     */
    public function generateReport() {
        $reportType = $_POST['report_type'] ?? 'sales';
        $startDate = $_POST['start_date'] ?? date('Y-m-01');
        $endDate = $_POST['end_date'] ?? date('Y-m-d');
        $format = $_POST['format'] ?? 'csv';
        $category = $_POST['category'] ?? 'all';
        $includeSummary = isset($_POST['include_summary']);
        $includeBreakdown = isset($_POST['include_breakdown']);
        
        $db = Database::getInstance()->getConnection();
        $storeId = $this->getAdminStoreLocationId();
        
        // Fetch Store Info
        $storeName = $_SESSION['store_name'] ?? 'My Shop';
        try {
            $storeStmt = $db->prepare("SELECT store_name FROM store_locations WHERE id = ?");
            $storeStmt->execute([$storeId]);
            $storeRow = $storeStmt->fetch();
            if ($storeRow && !empty($storeRow['store_name'])) $storeName = $storeRow['store_name'];
        } catch (Exception $e) {}
        
        $adminName = $_SESSION['fullname'] ?? ($_SESSION['name'] ?? 'Admin');

        // Build Query
        $sql = "SELECT b.id, u.fullname as customer, s.service_name as service, b.item_description as item, 
                       b.item_type, b.service_type, b.total_amount as amount, b.created_at as report_date, b.status, b.payment_status
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN services s ON b.service_id = s.id
                WHERE b.store_location_id = ? 
                AND DATE(b.created_at) BETWEEN ? AND ?";
        
        $params = [$storeId, $startDate, $endDate];
        
        // Refine based on report type
        if ($reportType === 'sales') {
            // Sales reports focus on revenue (completed or paid)
            $sql .= " AND (b.status IN ('completed', 'delivered_and_paid') OR b.payment_status IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod'))";
        }
        
        if ($category !== 'all') {
            $sql .= " AND b.service_type = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();
        
        // Calculate Statistics
        $totalBookings = count($bookings);
        $totalRevenue = 0;
        $paidCount = 0;
        $completedCount = 0;
        $cancelledCount = 0;
        
        foreach ($bookings as $b) {
            $totalRevenue += (float)$b['amount'];
            if ($b['status'] === 'completed' || $b['status'] === 'delivered_and_paid') $completedCount++;
            if ($b['status'] === 'cancelled') $cancelledCount++;
            if (in_array($b['payment_status'], ['paid', 'paid_full_cash', 'paid_on_delivery_cod'])) $paidCount++;
        }
        
        $unpaidCount = $totalBookings - $paidCount;
        $filename = "UphoCare_" . ucfirst($reportType) . "_Report_" . date('Ymd_His');

        if ($format === 'csv') {
            $this->exportCSV($bookings, $storeName, $startDate, $endDate, $filename);
        } elseif ($format === 'xlsx') {
            $this->exportExcel($bookings, $storeName, $startDate, $endDate, $totalBookings, $totalRevenue, $filename);
        } else {
            $this->exportPDF($bookings, $storeName, $startDate, $endDate, $totalBookings, $totalRevenue, $completedCount, $cancelledCount, $paidCount, $unpaidCount, $adminName, $reportType, $includeSummary, $includeBreakdown, $filename);
        }
    }

    private function exportCSV($bookings, $storeName, $start, $end, $filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['UphoCare Shop Report']);
        fputcsv($output, ['Shop Name:', $storeName]);
        fputcsv($output, ['Period:', "$start to $end"]);
        fputcsv($output, []);
        fputcsv($output, ['Booking ID', 'Customer', 'Service', 'Item', 'Date', 'Status', 'Payment', 'Amount']);
        foreach ($bookings as $b) {
            fputcsv($output, [
                'BK-' . $b['id'],
                $b['customer'],
                $b['service'],
                $b['item'],
                date('M d, Y', strtotime($b['report_date'])),
                ucfirst($b['status']),
                ucfirst($b['payment_status']),
                number_format($b['amount'], 2)
            ]);
        }
        fclose($output);
        exit;
    }

    private function exportExcel($bookings, $storeName, $start, $end, $totalBookings, $totalRevenue, $filename) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        echo "<html><body>";
        echo "<h2>$storeName - Sales Report</h2>";
        echo "<p>Period: $start to $end</p>";
        echo "<p>Total Bookings: $totalBookings | Total Revenue: ₱" . number_format($totalRevenue, 2) . "</p>";
        echo "<table border='1'><thead><tr style='background-color: #0F3C5F; color: white;'>
                <th>Booking ID</th><th>Customer</th><th>Service</th><th>Item</th><th>Date</th><th>Status</th><th>Amount</th>
              </tr></thead><tbody>";
        foreach ($bookings as $b) {
            echo "<tr><td>BK-{$b['id']}</td><td>{$b['customer']}</td><td>{$b['service']}</td><td>{$b['item']}</td>
                    <td>".date('M d, Y', strtotime($b['report_date']))."</td><td>".ucfirst($b['status'])."</td>
                    <td>" . number_format($b['amount'], 2) . "</td></tr>";
        }
        echo "</tbody></table></body></html>";
        exit;
    }

    private function exportPDF($bookings, $storeName, $start, $end, $totalBookings, $totalRevenue, $completed, $cancelled, $paid, $unpaid, $admin, $type, $showSummary, $showBreakdown, $filename) {
        // Use Dompdf for direct download
        if (!class_exists('Dompdf\Dompdf')) {
            die("PDF Library (Dompdf) not found. Please run 'composer install'.");
        }

        $dompdf = new \Dompdf\Dompdf();
        
        // Capture HTML content
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title><?= $storeName ?> - Report</title>
            <style>
                body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 20px; line-height: 1.4; }
                .header { border-bottom: 2px solid #0F3C5F; padding-bottom: 15px; margin-bottom: 25px; }
                .header h1 { color: #0F3C5F; margin: 0; font-size: 22px; }
                .meta { width: 100%; margin-top: 10px; font-size: 12px; color: #555; }
                .meta td { padding: 0; border: none; }
                .summary-table { width: 100%; margin-bottom: 30px; border-collapse: separate; border-spacing: 10px 0; }
                .summary-card { padding: 12px; background: #f8f9fc; border-radius: 6px; border-left: 4px solid #0F3C5F; width: 20%; }
                .summary-card h4 { margin: 0 0 5px 0; font-size: 9px; text-transform: uppercase; color: #666; }
                .summary-card p { margin: 0; font-size: 14px; font-weight: bold; color: #0F3C5F; }
                table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: auto; }
                table.data-table th { background: #0F3C5F; color: white; text-align: left; padding: 8px; font-size: 11px; border: 1px solid #0F3C5F; }
                table.data-table td { padding: 8px; border: 1px solid #eee; font-size: 10px; }
                table.data-table tr { page-break-inside: avoid; page-break-after: auto; }
                .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>UpholCare – Admin <?= ucfirst($type) ?> Report</h1>
                <table class="meta">
                    <tr>
                        <td>
                            <strong>Shop:</strong> <?= $storeName ?><br>
                            <strong>Range:</strong> <?= date('M d, Y', strtotime($start)) ?> – <?= date('M d, Y', strtotime($end)) ?>
                        </td>
                        <td style="text-align: right;">
                            <strong>Date Generated:</strong> <?= date('M d, Y H:i') ?><br>
                            <strong>Admin:</strong> <?= $admin ?>
                        </td>
                    </tr>
                </table>
            </div>

            <?php if ($showSummary): ?>
            <table class="summary-table">
                <tr>
                    <td class="summary-card"><h4>Total Bookings</h4><p><?= $totalBookings ?></p></td>
                    <td class="summary-card"><h4>Total Revenue</h4><p>PHP <?= number_format($totalRevenue, 2) ?></p></td>
                    <td class="summary-card"><h4>Completed</h4><p><?= $completed ?></p></td>
                    <td class="summary-card" style="border-left-color: #1cc88a;"><h4>Paid</h4><p><?= $paid ?></p></td>
                    <td class="summary-card" style="border-left-color: #f6c23e;"><h4>Unpaid</h4><p><?= $unpaid ?></p></td>
                </tr>
            </table>
            <?php endif; ?>

            <?php if ($showBreakdown): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="15%">ID</th>
                        <th width="25%">Customer</th>
                        <th width="20%">Service</th>
                        <th width="15%">Date</th>
                        <th width="15%">Status</th>
                        <th width="10%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td>BK-<?= $b['id'] ?></td>
                        <td><?= $b['customer'] ?></td>
                        <td><?= $b['service'] ?></td>
                        <td><?= date('M d, Y', strtotime($b['report_date'])) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $b['status'])) ?></td>
                        <td>PHP <?= number_format($b['amount'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <div class="footer">
                <p>This is a computer-generated document. Copyright &copy; <?= date('Y') ?> UpholCare.</p>
            </div>
        </body>
        </html>
        <?php
        $html = ob_get_clean();

        // Load HTML and Render
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream PDF to browser for download
        $dompdf->stream($filename . ".pdf", ["Attachment" => true]);
        exit;
    }
    
    /**
     * Store Ratings - View all customer ratings for admin's store only
     */
    public function storeRatings() {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get admin's store location ID
            $adminStoreLocationId = $this->getAdminStoreLocationId();
            
            // If no active store found, try to find any store (even if inactive) for debugging
            if (!$adminStoreLocationId && isset($_SESSION['email'])) {
                try {
                    $db = Database::getInstance()->getConnection();
                    $userEmail = $_SESSION['email'];
                    
                    // Try to find any store with this email (even if inactive)
                    $debugStmt = $db->prepare("
                        SELECT id, store_name, email, status 
                        FROM store_locations 
                        WHERE email = ? OR LOWER(email) = LOWER(?)
                        LIMIT 1
                    ");
                    $debugStmt->execute([$userEmail, $userEmail]);
                    $debugStore = $debugStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($debugStore) {
                        error_log("Found store for admin but status is: " . $debugStore['status']);
                        // If store exists but is inactive, we can still use it for ratings
                        if ($debugStore['status'] !== 'active') {
                            $adminStoreLocationId = intval($debugStore['id']);
                            error_log("Using inactive store ID: " . $adminStoreLocationId);
                        }
                    }
                } catch (Exception $e) {
                    error_log("Error in debug store lookup: " . $e->getMessage());
                }
            }
            
            if (!$adminStoreLocationId) {
                // If no store found, try to create one
                $adminStoreLocationId = $this->createDefaultStoreLocationForAdmin();
            }
            
            // Check if store_ratings table exists
            $tableExists = false;
            try {
                $checkTableStmt = $db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_ratings'
                ");
                $tableCheck = $checkTableStmt->fetch();
                $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
            } catch (Exception $e) {
                $tableExists = false;
            }
            
            $ratings = [];
            $adminStore = null;
            
            if ($tableExists && $adminStoreLocationId) {
                // Get admin's store information
                $storeStmt = $db->prepare("
                    SELECT id, store_name, city, address, email 
                    FROM store_locations 
                    WHERE id = ? AND status = 'active'
                    LIMIT 1
                ");
                $storeStmt->execute([$adminStoreLocationId]);
                $adminStore = $storeStmt->fetch(PDO::FETCH_ASSOC);
                
                // If store found but query returned empty, try without status check
                if (!$adminStore) {
                    $storeStmt2 = $db->prepare("
                        SELECT id, store_name, city, address, email 
                        FROM store_locations 
                        WHERE id = ?
                        LIMIT 1
                    ");
                    $storeStmt2->execute([$adminStoreLocationId]);
                    $adminStore = $storeStmt2->fetch(PDO::FETCH_ASSOC);
                }
                
                // Get ratings for admin's store only
                $stmt = $db->prepare("
                    SELECT 
                        sr.id,
                        sr.store_id,
                        sr.user_id,
                        sr.rating,
                        sr.review_text,
                        sr.status,
                        sr.created_at,
                        sr.updated_at,
                        u.fullname as customer_name,
                        u.email as customer_email,
                        u.phone as customer_phone,
                        sl.store_name,
                        sl.city,
                        sl.address
                    FROM store_ratings sr
                    LEFT JOIN users u ON sr.user_id = u.id
                    LEFT JOIN store_locations sl ON sr.store_id = sl.id
                    WHERE sr.store_id = ?
                    ORDER BY sr.created_at DESC
                ");
                $stmt->execute([$adminStoreLocationId]);
                $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get ratings grouped by year for the chart (admin's store only)
                $yearlyStmt = $db->prepare("
                    SELECT 
                        YEAR(sr.created_at) as year,
                        COUNT(sr.id) as total_ratings,
                        AVG(sr.rating) as average_rating,
                        SUM(CASE WHEN sr.rating = 5 THEN 1 ELSE 0 END) as five_star,
                        SUM(CASE WHEN sr.rating = 4 THEN 1 ELSE 0 END) as four_star,
                        SUM(CASE WHEN sr.rating = 3 THEN 1 ELSE 0 END) as three_star,
                        SUM(CASE WHEN sr.rating = 2 THEN 1 ELSE 0 END) as two_star,
                        SUM(CASE WHEN sr.rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM store_ratings sr
                    WHERE sr.store_id = ? AND sr.status = 'active'
                    GROUP BY YEAR(sr.created_at)
                    ORDER BY year ASC
                ");
                $yearlyStmt->execute([$adminStoreLocationId]);
                $yearlyData = $yearlyStmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $yearlyData = [];
            }
            
            // Calculate statistics
            $totalRatings = count($ratings);
            $averageRating = 0;
            if ($totalRatings > 0) {
                $sum = array_sum(array_column($ratings, 'rating'));
                $averageRating = round($sum / $totalRatings, 1);
            }
            
            // Count by rating value
            $ratingCounts = [
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0
            ];
            foreach ($ratings as $rating) {
                $ratingValue = (int)$rating['rating'];
                if (isset($ratingCounts[$ratingValue])) {
                    $ratingCounts[$ratingValue]++;
                }
            }
            
            $data = [
                'title' => 'Store Ratings - ' . APP_NAME,
                'user' => $this->currentUser(),
                'ratings' => $ratings,
                'adminStore' => $adminStore,
                'adminStoreLocationId' => $adminStoreLocationId,
                'totalRatings' => $totalRatings,
                'averageRating' => $averageRating,
                'ratingCounts' => $ratingCounts,
                'yearlyData' => $yearlyData ?? [],
                'tableExists' => $tableExists
            ];
            
            $this->view('admin/store_ratings', $data);
        } catch (Exception $e) {
            error_log("Error in storeRatings: " . $e->getMessage());
            $data = [
                'title' => 'Store Ratings - ' . APP_NAME,
                'user' => $this->currentUser(),
                'ratings' => [],
                'adminStore' => null,
                'adminStoreLocationId' => null,
                'totalRatings' => 0,
                'averageRating' => 0,
                'ratingCounts' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
                'yearlyData' => [],
                'tableExists' => false,
                'error' => $e->getMessage()
            ];
            $this->view('admin/store_ratings', $data);
        }
    }
    
    /**
     * Get admin's store location ID
     * Returns the store location ID associated with the current admin user
     */
    private function getAdminStoreLocationId() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            return null;
        }
        
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['email'];
        
        try {
            // Method 1: Check if store_locations has a user_id or admin_id column (direct relationship)
            try {
                $checkColumn = $db->query("SHOW COLUMNS FROM store_locations LIKE 'user_id'");
                if ($checkColumn->rowCount() > 0) {
                    $stmt = $db->prepare("
                        SELECT id as store_location_id
                        FROM store_locations
                        WHERE user_id = ? AND status = 'active'
                        LIMIT 1
                    ");
                    $stmt->execute([$userId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result && $result['store_location_id']) {
                        return intval($result['store_location_id']);
                    }
                }
                
                $checkAdminColumn = $db->query("SHOW COLUMNS FROM store_locations LIKE 'admin_id'");
                if ($checkAdminColumn->rowCount() > 0) {
                    $stmt = $db->prepare("
                        SELECT id as store_location_id
                        FROM store_locations
                        WHERE admin_id = ? AND status = 'active'
                        LIMIT 1
                    ");
                    $stmt->execute([$userId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result && $result['store_location_id']) {
                        return intval($result['store_location_id']);
                    }
                }
            } catch (Exception $e) {
                // Column doesn't exist, continue to other methods
                error_log("Note: user_id/admin_id column not found in store_locations: " . $e->getMessage());
            }
            
            // Method 2: Try direct email match in store_locations (most reliable)
            $stmt2 = $db->prepare("
                SELECT id as store_location_id
                FROM store_locations
                WHERE email = ? AND status = 'active'
                LIMIT 1
            ");
            $stmt2->execute([$userEmail]);
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($result2 && $result2['store_location_id']) {
                return intval($result2['store_location_id']);
            }
            
            // Method 3: Try linking via admin_registrations using user_id
            $stmt3 = $db->prepare("
                SELECT sl.id as store_location_id
                FROM store_locations sl
                INNER JOIN admin_registrations ar ON (
                    (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                     AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                    OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                        AND ar.business_name IS NOT NULL 
                        AND ar.business_name != '')
                )
                INNER JOIN users u ON ar.user_id = u.id AND u.role = 'admin'
                WHERE u.id = ? AND u.email = ? AND sl.status = 'active'
                LIMIT 1
            ");
            $stmt3->execute([$userId, $userEmail]);
            $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            if ($result3 && $result3['store_location_id']) {
                return intval($result3['store_location_id']);
            }
            
            // Method 4: Try linking via admin_registrations using email
            $stmt4 = $db->prepare("
                SELECT sl.id as store_location_id
                FROM store_locations sl
                INNER JOIN admin_registrations ar ON (
                    (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                     AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                    OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                        AND ar.business_name IS NOT NULL 
                        AND ar.business_name != '')
                )
                INNER JOIN users u ON ar.email = u.email AND u.role = 'admin'
                WHERE u.id = ? AND u.email = ? AND sl.status = 'active'
                LIMIT 1
            ");
            $stmt4->execute([$userId, $userEmail]);
            $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
            
            if ($result4 && $result4['store_location_id']) {
                return intval($result4['store_location_id']);
            }
            
            // Method 5: Try to find any store where email matches (case-insensitive)
            $stmt5 = $db->prepare("
                SELECT id as store_location_id
                FROM store_locations
                WHERE LOWER(email) = LOWER(?) AND status = 'active'
                LIMIT 1
            ");
            $stmt5->execute([$userEmail]);
            $result5 = $stmt5->fetch(PDO::FETCH_ASSOC);
            
            if ($result5 && $result5['store_location_id']) {
                return intval($result5['store_location_id']);
            }
            
            // If no store found, log for debugging with more details
            error_log("WARNING: No store location found for admin user_id: $userId, email: $userEmail");
            error_log("Attempted methods: direct user_id/admin_id, email match, admin_registrations via user_id, admin_registrations via email, case-insensitive email");
            
            // Debug: Check what stores exist
            $debugStmt = $db->query("SELECT id, store_name, email, status FROM store_locations LIMIT 10");
            $debugStores = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Available stores (first 10): " . json_encode($debugStores));
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting admin store location: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }
    
    /**
     * Create a default store location for admin if none exists
     */
    private function createDefaultStoreLocationForAdmin() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            return null;
        }
        
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['email'];
        
        try {
            // Get admin registration info
            $stmt = $db->prepare("
                SELECT ar.business_name, ar.business_address, ar.business_city, ar.business_province,
                       ar.business_latitude, ar.business_longitude, ar.phone, ar.email
                FROM admin_registrations ar
                INNER JOIN users u ON ar.user_id = u.id
                WHERE u.id = ? AND u.email = ? AND u.role = 'admin'
                LIMIT 1
            ");
            $stmt->execute([$userId, $userEmail]);
            $adminReg = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($adminReg && !empty($adminReg['business_name'])) {
                // Create store location from admin registration
                $insertStmt = $db->prepare("
                    INSERT INTO store_locations 
                    (store_name, address, city, province, latitude, longitude, phone, email, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
                ");
                $insertStmt->execute([
                    $adminReg['business_name'],
                    $adminReg['business_address'] ?? '',
                    $adminReg['business_city'] ?? 'Bohol',
                    $adminReg['business_province'] ?? 'Bohol',
                    $adminReg['business_latitude'] ?? null,
                    $adminReg['business_longitude'] ?? null,
                    $adminReg['phone'] ?? '',
                    $adminReg['email'] ?? $userEmail
                ]);
                
                return intval($db->lastInsertId());
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error creating default store location: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Inventory Management
     */
    public function inventory() {
        $inventoryModel = $this->model('Inventory');
        
        // Get admin's store location ID to filter inventory
        $adminStoreLocationId = $this->getAdminStoreLocationId();
        $inventory = $inventoryModel->getAll($adminStoreLocationId);
        
        $data = [
            'title' => 'Inventory Management - ' . APP_NAME,
            'user' => $this->currentUser(),
            'inventory' => $inventory,
            'admin_store_location_id' => $adminStoreLocationId
        ];
        
        $this->view('admin/inventory', $data);
    }
    
    /**
     * Get Inventory (AJAX) - Returns inventory data
     */
    public function getInventory() {
        // Prevent any output before JSON
        if (ob_get_level()) {
            ob_clean();
        }
        ob_start();
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
            $inventoryModel = $this->model('Inventory');
            $storeLocationId = $_GET['store_location_id'] ?? null;
            $inventory = $inventoryModel->getAll($storeLocationId);
            
            // Format for frontend
            $formatted = array_map(function($item) {
                $status = 'in-stock';
                $quantity = floatval($item['quantity'] ?? 0);
                if ($quantity === 0) {
                    $status = 'out-of-stock';
                } elseif ($quantity < 5) {
                    $status = 'low-stock';
                }
                
                // Get type from database - Check BOTH fabric_type and leather_type columns
                // The database might have either column name
                $dbType = $item['leather_type'] ?? $item['fabric_type'] ?? null;
                
                // Only default to 'standard' if it's actually null/empty/not set
                // If database has 'premium', preserve it as 'premium'
                if ($dbType === null || $dbType === '' || (!isset($item['leather_type']) && !isset($item['fabric_type']))) {
                    $normalizedType = 'standard';
                } else {
                    // Preserve the actual database value, just normalize case
                    $normalizedType = strtolower(trim($dbType));
                    // Ensure it's valid
                    if ($normalizedType !== 'standard' && $normalizedType !== 'premium') {
                        $normalizedType = 'standard'; // Fallback if invalid
                    }
                }
                
                // Debug logging - this will help identify the issue
                error_log("DEBUG getInventory - Item ID: " . ($item['id'] ?? 'N/A') . 
                         " | Code: " . ($item['color_code'] ?? 'N/A') . 
                         " | DB leather_type: " . var_export($item['leather_type'] ?? 'NOT SET', true) .
                         " | DB fabric_type: " . var_export($item['fabric_type'] ?? 'NOT SET', true) .
                         " | Selected DB type: " . var_export($dbType, true) . 
                         " | Normalized: " . $normalizedType);
                
                return [
                    'id' => $item['id'] ?? 0,
                    'code' => $item['color_code'] ?? '',
                    'name' => $item['color_name'] ?? '',
                    'color' => $item['color_hex'] ?? '#000000',
                    'color_hex' => $item['color_hex'] ?? '#000000',
                    // Return the normalized type in all fields for frontend compatibility
                    'fabric_type_raw' => $dbType, // Keep original for debugging
                    'type' => $normalizedType,
                    'fabric_type' => $normalizedType,
                    'leather_type' => $normalizedType,
                    'store_location_id' => $item['store_location_id'] ?? null,
                    'quantity' => $quantity,
                    'standard_price' => floatval($item['price_per_unit'] ?? $item['standard_price'] ?? 0),
                    'premium_price' => floatval($item['premium_price'] ?? 0),
                    'price_per_meter' => floatval($item['price_per_meter'] ?? 0),
                    'status' => $status,
                    'lastUpdated' => isset($item['updated_at']) && $item['updated_at'] ? date('M d, Y, h:i A', strtotime($item['updated_at'])) : (isset($item['created_at']) && $item['created_at'] ? date('M d, Y, h:i A', strtotime($item['created_at'])) : date('M d, Y, h:i A'))
                ];
            }, $inventory);
            
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => true,
                'data' => $formatted,
                'count' => count($formatted)
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            error_log("Error getting inventory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => false, 
                'message' => 'Error loading inventory: ' . $e->getMessage(),
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null,
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Create/Add Inventory Item (AJAX)
     */
    public function createInventory() {
        // Prevent any output before JSON
        if (ob_get_level()) {
            ob_clean();
        }
        ob_start();
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Check request method - also check for POST data in case method is overridden
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $hasPostData = !empty($_POST);
        
        // If method is GET but we have POST data, try to parse from input stream
        if ($requestMethod === 'GET' && !$hasPostData) {
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                // Try to parse as form data (multipart/form-data or application/x-www-form-urlencoded)
                parse_str($input, $parsed);
                if (!empty($parsed)) {
                    $_POST = array_merge($_POST, $parsed);
                    $hasPostData = true;
                }
            }
        }
        
        if ($requestMethod !== 'POST' && !$hasPostData) {
            http_response_code(405);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed. Expected POST, got ' . $requestMethod,
                'received_method' => $requestMethod,
                'has_post_data' => $hasPostData,
                'post_keys' => array_keys($_POST ?? []),
                'input_length' => strlen(file_get_contents('php://input') ?: '')
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        }
        
        try {
            $inventoryModel = $this->model('Inventory');
            
            // Debug: Log all incoming data
            error_log("DEBUG createInventory - REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
            error_log("DEBUG createInventory - Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
            error_log("DEBUG createInventory - All POST data: " . print_r($_POST, true));
            error_log("DEBUG createInventory - All _REQUEST data: " . print_r($_REQUEST, true));
            
            // Get POST data
            $colorCode = $_POST['color_code'] ?? '';
            $colorName = $_POST['color_name'] ?? '';
            $colorHex = $_POST['color_hex'] ?? '#000000';
            
            // Get and normalize leather type to lowercase
            // Debug: Log what we're receiving
            $leatherTypeRaw = $_POST['leather_type'] ?? $_POST['fabric_type'] ?? '';
            error_log("DEBUG createInventory - Raw leather_type from POST: " . var_export($leatherTypeRaw, true));
            error_log("DEBUG createInventory - leather_type from POST directly: " . var_export($_POST['leather_type'] ?? 'NOT SET', true));
            error_log("DEBUG createInventory - fabric_type from POST directly: " . var_export($_POST['fabric_type'] ?? 'NOT SET', true));
            error_log("DEBUG createInventory - All POST keys: " . implode(', ', array_keys($_POST)));
            
            // If empty, default to standard, otherwise normalize
            if (empty($leatherTypeRaw)) {
                $leatherType = 'standard';
                error_log("DEBUG createInventory - leather_type was empty, defaulting to 'standard'");
            } else {
                $leatherType = strtolower(trim($leatherTypeRaw));
                error_log("DEBUG createInventory - Normalized leather_type: " . $leatherType);
            }
            
            // Ensure it's either 'standard' or 'premium'
            if ($leatherType !== 'standard' && $leatherType !== 'premium') {
                error_log("DEBUG createInventory - Invalid leather_type '{$leatherType}', defaulting to 'standard'");
                $leatherType = 'standard'; // Default to standard if invalid
            }
            
            error_log("DEBUG createInventory - Final leather_type to save: " . $leatherType);
            
            $pricePerMeter = floatval($_POST['price_per_meter'] ?? 0);
            $quantity = floatval($_POST['quantity'] ?? 0);
            
            // Automatically get admin's store location ID
            $storeLocationId = $this->getAdminStoreLocationId();
            
            // If no store location found, try to create one or use a default
            if (!$storeLocationId) {
                // Try to create a default store location for this admin
                $storeLocationId = $this->createDefaultStoreLocationForAdmin();
            }
            
            // If still no store location, allow null (optional for now)
            // This allows admins to add inventory even if store location setup is incomplete
            // Store location can be assigned later
            
            $data = [
                'color_code' => $colorCode,
                'color_name' => $colorName,
                'color_hex' => $colorHex,
                'fabric_type' => $leatherType, // Save as lowercase: 'standard' or 'premium'
                'leather_type' => $leatherType, // Save as lowercase: 'standard' or 'premium'
                'price_per_unit' => 0, // Set to 0 as standard/premium prices are removed
                'premium_price' => 0, // Set to 0 as standard/premium prices are removed
                'price_per_meter' => $pricePerMeter,
                'quantity' => $quantity,
                'store_location_id' => $storeLocationId
            ];
            
            // Validate required fields
            if (empty($data['color_code']) || empty($data['color_name'])) {
                http_response_code(400);
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Color code and name are required',
                    'received_data' => [
                        'color_code' => !empty($data['color_code']),
                        'color_name' => !empty($data['color_name'])
                    ]
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }
            
            if (empty($leatherType)) {
                http_response_code(400);
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Leather type is required'
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }
            
            // Validate price per meter
            if ($pricePerMeter <= 0) {
                http_response_code(400);
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Price per meter must be greater than 0'
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }
            
            
            // Check for duplicate color name (case-insensitive)
            $existingColorName = $inventoryModel->findByColorName($colorName, $storeLocationId);
            if ($existingColorName) {
                http_response_code(400);
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'This color name already exists for this item. Please use a different color name.'
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }
            
            // Check if color code already exists
            $existing = $inventoryModel->getAll();
            foreach ($existing as $item) {
                if (strtolower($item['color_code']) === strtolower($data['color_code'])) {
                    http_response_code(400);
                    ob_clean();
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Color code already exists: ' . $data['color_code']
                    ], JSON_UNESCAPED_UNICODE);
                    ob_end_flush();
                    exit;
                }
            }
            
            $id = $inventoryModel->create($data);
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Inventory item added successfully',
                'id' => $id,
                'data' => $data
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error creating inventory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Error creating inventory item: ' . $e->getMessage(),
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Update Inventory Item (AJAX)
     */
    public function updateInventory() {
        header('Content-Type: application/json');
        ob_start();
        
        // Check request method - allow POST or GET with POST data
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $hasPostData = !empty($_POST) || !empty(file_get_contents('php://input'));
        
        // Debug logging
        error_log("DEBUG updateInventory - REQUEST_METHOD: " . $requestMethod);
        error_log("DEBUG updateInventory - Has POST data: " . ($hasPostData ? 'yes' : 'no'));
        error_log("DEBUG updateInventory - POST keys: " . implode(', ', array_keys($_POST ?? [])));
        
        // Parse JSON input if present
        if ($requestMethod === 'POST' && empty($_POST) && !empty(file_get_contents('php://input'))) {
            $input = file_get_contents('php://input');
            $jsonData = json_decode($input, true);
            if ($jsonData) {
                $_POST = array_merge($_POST, $jsonData);
            }
        }
        
        if ($requestMethod !== 'POST' && !$hasPostData) {
            http_response_code(405);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed. Expected POST, got ' . $requestMethod,
                'received_method' => $requestMethod,
                'has_post_data' => $hasPostData
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        }
        
        try {
            $inventoryModel = $this->model('Inventory');
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid inventory ID']);
                exit;
            }
            
            // Automatically get admin's store location ID
            $storeLocationId = $this->getAdminStoreLocationId();
            
            // If no store location found, try to create one or use a default
            if (!$storeLocationId) {
                // Try to create a default store location for this admin
                $storeLocationId = $this->createDefaultStoreLocationForAdmin();
            }
            
            // If still no store location, allow null (optional for now)
            // This allows admins to add inventory even if store location setup is incomplete
            // Store location can be assigned later
            
            $pricePerMeter = floatval($_POST['price_per_meter'] ?? 0);
            
            // Validate price per meter
            if ($pricePerMeter <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Price per meter must be greater than 0'
                ]);
                exit;
            }
            
            // Get and normalize leather type to lowercase
            $leatherTypeRaw = $_POST['leather_type'] ?? $_POST['fabric_type'] ?? 'standard';
            $leatherType = strtolower(trim($leatherTypeRaw));
            
            // Ensure it's either 'standard' or 'premium'
            if ($leatherType !== 'standard' && $leatherType !== 'premium') {
                $leatherType = 'standard'; // Default to standard if invalid
            }
            
            $data = [
                'color_code' => $_POST['color_code'] ?? '',
                'color_name' => $_POST['color_name'] ?? '',
                'color_hex' => $_POST['color_hex'] ?? '#000000',
                'fabric_type' => $leatherType, // Save as lowercase: 'standard' or 'premium'
                'price_per_unit' => 0, // Set to 0 as standard/premium prices are removed
                'premium_price' => 0, // Set to 0 as standard/premium prices are removed
                'price_per_meter' => $pricePerMeter,
                'quantity' => floatval($_POST['quantity'] ?? 0),
                'store_location_id' => $storeLocationId // Can be null
            ];
            
            // Check if color code already exists (excluding current item)
            $existing = $inventoryModel->getAll();
            foreach ($existing as $item) {
                if ($item['id'] != $id && strtolower($item['color_code']) === strtolower($data['color_code'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Color code already exists']);
                    exit;
                }
            }
            
            $inventoryModel->update($id, $data);
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Inventory item updated successfully',
                'id' => $id,
                'data' => $data
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error updating inventory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Error updating inventory item: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Delete Inventory Item (AJAX)
     */
    public function deleteInventory() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            $inventoryModel = $this->model('Inventory');
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid inventory ID']);
                exit;
            }
            
            // Get the item first to check quantity
            $item = $inventoryModel->getById($id);
            
            if (!$item) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Inventory item not found']);
                exit;
            }
            
            // Check if quantity is zero
            if (floatval($item['quantity'] ?? 0) == 0) {
                // Don't delete - mark as out of stock instead to preserve history
                $inventoryModel->update($id, ['status' => 'out-of-stock']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Item marked as out of stock (quantity is zero). Item not deleted to preserve inventory history.'
                ]);
            } else {
                // Has stock - allow deletion
                $inventoryModel->delete($id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Inventory item deleted successfully'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error deleting inventory: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting inventory item']);
        }
        exit;
    }
    
    /**
     * Get All Inventory (AJAX)
     */
    public function getAllInventory() {
        header('Content-Type: application/json');
        
        try {
            $inventoryModel = $this->model('Inventory');
            $inventory = $inventoryModel->getAll();
            
            // Format for frontend
            $formatted = array_map(function($item) {
                $status = 'in-stock';
                $quantity = floatval($item['quantity'] ?? 0);
                if ($quantity === 0) {
                    $status = 'out-of-stock';
                } elseif ($quantity < 5) {
                    $status = 'low-stock';
                }
                
                return [
                    'id' => $item['id'],
                    'code' => $item['color_code'],
                    'name' => $item['color_name'],
                    'color' => $item['color_hex'],
                    'color_hex' => $item['color_hex'],
                    'type' => $item['fabric_type'] ?? 'standard',
                    'quantity' => $quantity,
                    'standard_price' => floatval($item['price_per_unit'] ?? 0),
                    'premium_price' => floatval($item['premium_price'] ?? 0),
                    'price_per_meter' => floatval($item['price_per_meter'] ?? 0),
                    'status' => $status,
                    'lastUpdated' => isset($item['updated_at']) ? date('M d, Y, h:i A', strtotime($item['updated_at'])) : date('M d, Y, h:i A')
                ];
            }, $inventory);
            
            echo json_encode([
                'success' => true,
                'inventory' => $formatted
            ]);
        } catch (Exception $e) {
            error_log("Error getting inventory: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error loading inventory']);
        }
        exit;
    }
    
    /**
     * Manage Booking Numbers
     */
    public function bookingNumbers() {
        $bookingModel = $this->model('Booking');
        
        $availableNumbers = $bookingModel->getAvailableBookingNumbers();
        $usedNumbers = $bookingModel->getUsedBookingNumbers();
        
        $data = [
            'title' => 'Manage Booking Numbers - ' . APP_NAME,
            'user' => $this->currentUser(),
            'availableNumbers' => $availableNumbers,
            'usedNumbers' => $usedNumbers
        ];
        
        $this->view('admin/booking_numbers', $data);
    }
    
    /**
     * Add new booking numbers
     */
    public function addBookingNumbers() {
        // Always set JSON header to prevent HTML errors from breaking JSON
        header('Content-Type: application/json');
        
        // Only allow POST method for AJAX requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        $prefix = $_POST['prefix'] ?? 'BKG-';
        $date = $_POST['date'] ?? date('Ymd');
        $startNumber = (int)($_POST['start_number'] ?? 1);
        $count = (int)($_POST['count'] ?? 10);
        
        $added = 0;
        
        for ($i = 0; $i < $count; $i++) {
            $number = $startNumber + $i;
            $bookingNumber = $prefix . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            
            // Check if booking number already exists
            $stmt = $db->prepare("SELECT id FROM booking_numbers WHERE booking_number = ?");
            $stmt->execute([$bookingNumber]);
            
            if (!$stmt->fetch()) {
                $stmt = $db->prepare("INSERT INTO booking_numbers (booking_number) VALUES (?)");
                if ($stmt->execute([$bookingNumber])) {
                    $added++;
                }
            }
        }
        
        // Return JSON response
        echo json_encode([
            'success' => true,
            'message' => "Added {$added} new booking numbers.",
            'added' => $added
        ]);
        exit;
    }
    
    /**
     * View All Bookings
     */
    public function allBookings() {
        // Run automatic status updates based on pickup_date
        // NOTE: This service will NOT touch bookings that are already in 'to_inspect' or beyond
        try {
            $updateStats = AutoStatusUpdateService::run();
            // Log update statistics for debugging
            if ($updateStats['checked'] > 0 || $updateStats['updated'] > 0) {
                error_log("AutoStatusUpdate in allBookings: Checked: {$updateStats['checked']}, Updated: {$updateStats['updated']}, Errors: {$updateStats['errors']}");
            }
        } catch (Exception $e) {
            error_log("AutoStatusUpdate error in allBookings: " . $e->getMessage());
            error_log("AutoStatusUpdate stack trace: " . $e->getTraceAsString());
        }
        
        // CRITICAL: After AutoStatusUpdate runs, verify that 'to_inspect' statuses are preserved
        // This is a safety check to ensure no statuses were accidentally reverted
        try {
            $db = Database::getInstance()->getConnection();
            $verifyStmt = $db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'to_inspect'");
            $verifyResult = $verifyStmt->fetch();
            if ($verifyResult && $verifyResult['count'] > 0) {
                error_log("DEBUG allBookings: Found {$verifyResult['count']} bookings with 'to_inspect' status - these should be preserved");
            }
        } catch (Exception $e) {
            error_log("Error verifying to_inspect statuses: " . $e->getMessage());
        }
        
        // Get database connection
        $db = Database::getInstance()->getConnection();
        
        // Handle Mode (Local vs Business) filtering
        $mode = $_GET['mode'] ?? 'local'; // Default to local mode
        $modeCondition = "AND b.booking_type = 'personal'";
        if ($mode === 'business') {
            $modeCondition = "AND b.booking_type IN ('business', 'business_reservation', 'corporate')"; // Use corporate as alias if ever added
        } elseif ($mode === 'all') {
            $modeCondition = ""; // Show everything
        }

        // Get all bookings with customer and service details
        // CRITICAL: Use b.status directly (not COALESCE) to preserve actual status values
        // Only use COALESCE in WHERE clause for filtering, not in SELECT
        // This ensures 'to_inspect', 'for_repair', etc. are preserved correctly
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email, u.phone,
                b.status,
                COALESCE(b.payment_status, 'unpaid') as payment_status
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE (b.status IS NULL OR LOWER(b.status) NOT IN ('rejected', 'declined'))
                AND b.is_archived = 0
                {$modeCondition}
                ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $bookings = $stmt->fetchAll();
        
        // Preserve actual statuses and payment_status - only default if truly NULL or empty string
        // CRITICAL: Do NOT override valid statuses like 'to_inspect', 'for_repair', 'approved', etc.
        foreach ($bookings as &$booking) {
            // Handle status - preserve actual value from database
            $status = trim($booking['status'] ?? '');
            // Only default to 'pending' if status is truly empty/null
            // Preserve ALL valid statuses including 'to_inspect', 'for_repair', 'approved', 'in_queue', etc.
            if ($status === '' || $status === null || strtolower($status) === 'null') {
                $booking['status'] = 'pending';
                error_log("DEBUG allBookings: Booking #{$booking['id']} had NULL/empty status, defaulted to 'pending'");
            } else {
                // Preserve the actual status from database - DO NOT CHANGE IT
                $booking['status'] = $status;
                // Debug log for to_inspect status
                if (strtolower($status) === 'to_inspect') {
                    error_log("DEBUG allBookings: Booking #{$booking['id']} has status 'to_inspect' - preserving it");
                }
            }
            
            // Handle payment_status - preserve actual values from database
            $paymentStatus = trim($booking['payment_status'] ?? '');
            // Only default to 'unpaid' if payment_status is truly empty/null
            // Preserve all valid payment statuses like 'paid_full_cash', 'paid_on_delivery_cod', etc.
            if ($paymentStatus === '' || $paymentStatus === null || strtolower($paymentStatus) === 'null') {
                $booking['payment_status'] = 'unpaid';
            } else {
                // Preserve the actual payment_status from database
                $booking['payment_status'] = $paymentStatus;
            }
        }
        
        $data = [
            'title' => 'All Bookings - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $bookings,
            'mode' => $mode
        ];
        
        $this->view('admin/all_bookings', $data);
    }
    
    /**
     * Update Booking Status
     */
    public function updateBookingStatus() {
        // Start output buffering and clear any existing output
        ob_start();
        ob_clean();
        
        // Set JSON headers immediately to prevent HTML output
        if (!headers_sent()) {
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            ob_end_flush();
            exit;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        $paymentStatus = $_POST['payment_status'] ?? null;
        $adminNotes = $_POST['admin_notes'] ?? '';
        // Technician assignment removed - no longer used
        $progressType = $_POST['progress_type'] ?? null;
        $progressNotes = $_POST['progress_notes'] ?? '';
        $notifyCustomer = isset($_POST['notify_customer']) && $_POST['notify_customer'] == '1';
        $notifyCustomerProgress = isset($_POST['notify_customer_progress']) && $_POST['notify_customer_progress'] == '1';
        // Pickup/Delivery management removed - now handled by customer's service option selection
        // Initialize variables that might be used later
        $pickupDate = $_POST['pickup_date'] ?? null;
        $deliveryDate = $_POST['delivery_date'] ?? null;
        $deliveryAddress = $_POST['delivery_address'] ?? null;
        $codPayment = $_POST['cod_payment'] ?? null;
        $deliveryType = $_POST['delivery_type'] ?? null;
        $notifyCustomerDelivery = isset($_POST['notify_customer_delivery']) && $_POST['notify_customer_delivery'] == '1';
        
        // Debug logging
        error_log("Update Booking Status - Booking ID: " . $bookingId . ", New Status: " . $newStatus);
        error_log("POST data: " . print_r($_POST, true));
        
        if (!$bookingId || !$newStatus) {
            ob_clean();
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'Booking ID and status are required'
            ]);
            ob_end_flush();
            exit;
        }
        
        // Sanitize status value
        $newStatus = trim(strtolower($newStatus));
        
        // Include new statuses
        $allowedStatuses = [
            'pending', 'accepted', 'for_pickup', 'picked_up', 'to_inspect', 'for_inspection', 
            'inspect_completed', 'preview_receipt_sent',
            'for_repair', 'under_repair', 'for_quality_check', 
            'ready_for_pickup', 'out_for_delivery', 'completed', 'paid', 'closed', 
            'delivered_and_paid', 'cancelled', 'repair_completed', 'repair_completed_ready_to_deliver'
        ];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            ob_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid status value: ' . $newStatus]);
            ob_end_flush();
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $adminId = $this->currentUser()['id'];
            
            // Start transaction to ensure atomic update
            $db->beginTransaction();
            
            // Get current status for comparison FIRST
            $currentStmt = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
            $currentStmt->execute([$bookingId]);
            $currentBooking = $currentStmt->fetch();
            $currentStatus = $currentBooking['status'] ?? 'pending';
            $currentPaymentStatus = $currentBooking['payment_status'] ?? 'unpaid';
            
            // STRICT STATUS FLOW: Use StatusTransitionGuard to prevent backward movement
            // EXCEPTION: Admin can always reset to 'pending' status
            require_once ROOT . DS . 'core' . DS . 'StatusTransitionGuard.php';
            if ($newStatus !== 'pending') {
                // Only validate transitions if not resetting to pending
                try {
                    StatusTransitionGuard::validateTransition($currentStatus, $newStatus);
                } catch (Exception $e) {
                    $db->rollBack();
                    ob_clean();
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                    ob_end_flush();
                    exit;
                }
            } else {
                // Admin is resetting to pending - log this action and reset payment status
                error_log("Admin reset booking ID {$bookingId} from '{$currentStatus}' back to 'pending'");
                // When resetting to pending, also reset payment status to unpaid
                if ($paymentStatus === null) {
                    $paymentStatus = 'unpaid';
                }
            }
            
            error_log("Current status: " . $currentStatus . ", New status: " . $newStatus);
            
            // Status transition validation is now handled by StatusTransitionGuard above
            // This ensures forward-only movement and prevents backward transitions
            // Exception: Admin can always reset to 'pending'
            
            // Update booking status and payment
            $updateData = ['status' => $newStatus];
            
            // If resetting to pending, also reset payment status
            if ($newStatus === 'pending' && $paymentStatus === null) {
                $paymentStatus = 'unpaid';
            }
            
            // Set completion_date when booking is finalized (completed/paid or delivered/paid)
            // Check if completion_date column exists
            $checkCompletionDateColumn = $db->query("SHOW COLUMNS FROM bookings LIKE 'completion_date'");
            $hasCompletionDateColumn = $checkCompletionDateColumn->rowCount() > 0;
            
            if ($hasCompletionDateColumn) {
                // Set completion_date for both 'completed' (with paid) and 'delivered_and_paid' statuses
                if (($newStatus === 'completed' && $currentStatus !== 'completed') ||
                    ($newStatus === 'delivered_and_paid' && $currentStatus !== 'delivered_and_paid')) {
                    $updateData['completion_date'] = date('Y-m-d H:i:s');
                    error_log("Setting completion_date for booking ID " . $bookingId . " to " . $updateData['completion_date'] . " (status: " . $newStatus . ")");
                }
            }
            
            // Always update payment_status if provided in POST data (including 'unpaid')
            // Check both $paymentStatus variable and $_POST directly
            $finalPaymentStatus = null;
            if ($paymentStatus !== null && $paymentStatus !== '') {
                $finalPaymentStatus = $paymentStatus;
            } elseif (isset($_POST['payment_status'])) {
                // Include 'unpaid' as a valid value - don't skip it
                $finalPaymentStatus = $_POST['payment_status'];
            }
            
            // If payment_status was provided (including 'unpaid'), always include it in the update
            if ($finalPaymentStatus !== null && $finalPaymentStatus !== '') {
                $updateData['payment_status'] = $finalPaymentStatus;
                error_log("Payment status will be updated to: " . $finalPaymentStatus);
                
                // Auto-update status based on payment_status changes
                // Workflow:
                // 1. COD: status = "completed", payment_status = "unpaid" (until payment received)
                // 2. When payment_status is updated to "paid" or "paid_on_delivery_cod" and status is "completed", 
                //    automatically change status to "delivered_and_paid"
                // 3. Full Cash: status = "completed", payment_status = "paid_full_cash" (paid before repair)
                
                if (($finalPaymentStatus === 'paid' || $finalPaymentStatus === 'paid_on_delivery_cod') && 
                    ($currentStatus === 'completed' || $newStatus === 'completed' || $currentStatus === 'out_for_delivery')) {
                    // COD: If status is completed or out_for_delivery and payment is now paid, change to delivered_and_paid
                    $updateData['status'] = 'delivered_and_paid';
                    $newStatus = 'delivered_and_paid'; // Update the variable for consistency
                    error_log("Auto-updating status to 'delivered_and_paid' because payment_status is now paid (COD) and status was {$currentStatus}");
                } elseif ($finalPaymentStatus === 'paid_full_cash') {
                    // Full cash paid before repair - keep status as "completed" (already paid)
                    // Don't change to delivered_and_paid, just ensure it's completed
                    if ($newStatus !== 'completed' && $newStatus !== 'delivered_and_paid') {
                        $updateData['status'] = 'completed';
                        $newStatus = 'completed';
                    } elseif ($newStatus === 'delivered_and_paid' && $finalPaymentStatus === 'paid_full_cash') {
                        // If status is delivered_and_paid but payment is paid_full_cash, keep as completed
                        // (delivered_and_paid is only for COD)
                        $updateData['status'] = 'completed';
                        $newStatus = 'completed';
                    }
                    error_log("Payment is paid_full_cash - keeping status as 'completed' (not delivered_and_paid)");
                } elseif ($finalPaymentStatus === 'unpaid' && ($newStatus === 'delivered_and_paid' || $currentStatus === 'delivered_and_paid')) {
                    // If payment is unpaid but status is delivered_and_paid, revert to completed
                    $updateData['status'] = 'completed';
                    $newStatus = 'completed';
                    error_log("Payment is unpaid - reverting status from 'delivered_and_paid' to 'completed'");
                }
            } else {
                error_log("No payment_status provided in update request - keeping existing value");
            }
            
            // Add admin notes if provided
            if ($adminNotes) {
                $stmt = $db->prepare("SELECT `notes` FROM `bookings` WHERE `id` = ?");
                $stmt->execute([$bookingId]);
                $booking = $stmt->fetch();
                $existingNotes = $booking['notes'] ?? '';
                $updateData['notes'] = $existingNotes ? $existingNotes . "\n\n[Admin: " . date('Y-m-d H:i') . "] " . $adminNotes : "[Admin: " . date('Y-m-d H:i') . "] " . $adminNotes;
            }
            
            // Update pickup/delivery information
            if ($pickupDate) {
                $updateData['pickup_date'] = date('Y-m-d H:i:s', strtotime($pickupDate));
            }
            if ($deliveryDate) {
                $updateData['delivery_date'] = date('Y-m-d H:i:s', strtotime($deliveryDate));
            }
            if ($deliveryAddress) {
                $updateData['delivery_address'] = $deliveryAddress;
            }
            if ($codPayment) {
                $updateData['payment_status'] = 'paid_on_delivery_cod';
            }
            
            // Recalculate fees if delivery type or distance changes
            // Get current booking to check if we need to recalculate
            $currentBookingStmt = $db->prepare("SELECT `total_amount`, `distance_km` FROM `bookings` WHERE `id` = ?");
            $currentBookingStmt->execute([$bookingId]);
            $currentBooking = $currentBookingStmt->fetch();
            
            $baseServicePrice = floatval($currentBooking['total_amount'] ?? 0);
            $currentDistance = floatval($currentBooking['distance_km'] ?? 0);
            
            // Check if distance is being updated
            $newDistance = isset($_POST['distance_km']) ? floatval($_POST['distance_km']) : $currentDistance;
            
            // Determine delivery type from status or POST data
            $newDeliveryType = 'pickup'; // Default
            if ($deliveryType === 'delivery' || $newStatus === 'out_for_delivery') {
                $newDeliveryType = 'delivery';
            } elseif ($deliveryType === 'pickup' || $pickupDate) {
                $newDeliveryType = 'pickup';
            }
            
            // Recalculate fees if delivery type changed or distance was updated
            if ($baseServicePrice > 0 && ($newDistance != $currentDistance || $deliveryType)) {
                $fees = FeeCalculator::calculateFees($baseServicePrice, $newDeliveryType, $newDistance);
                
                // Update fee fields
                $updateData['labor_fee'] = $fees['labor_fee'];
                $updateData['pickup_fee'] = $fees['pickup_fee'];
                $updateData['delivery_fee'] = $fees['delivery_fee'];
                $updateData['gas_fee'] = $fees['gas_fee'];
                $updateData['travel_fee'] = $fees['travel_fee'];
                $updateData['distance_km'] = $fees['distance_km'];
                $updateData['total_additional_fees'] = $fees['total_additional_fees'];
                $updateData['grand_total'] = $fees['grand_total'];
                
                error_log("Fees recalculated: Grand Total = " . $fees['grand_total']);
            }
            
            // Use direct SQL update to ensure status is definitely changed
            // Use booking_id as primary key (not id) - use backticks for safety
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "`$field` = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            // Build and execute update query
            // Use id as primary key for db_upholcare database
            $updateQuery = "UPDATE `bookings` SET " . implode(', ', $updateFields) . ", `updated_at` = NOW() WHERE `id` = ?";
            error_log("Update query: " . $updateQuery);
            error_log("Update values: " . print_r($updateValues, true));
            error_log("Booking ID being used: " . $bookingId);
            
            $updateStmt = null;
            $result = false;
            $updateError = null;
            
            try {
                $updateStmt = $db->prepare($updateQuery);
                $result = $updateStmt->execute($updateValues);
                
                error_log("Update result: " . ($result ? 'SUCCESS' : 'FAILED'));
                if (!$result) {
                    $errorInfo = $updateStmt->errorInfo();
                    error_log("Update error: " . print_r($errorInfo, true));
                    $updateError = $errorInfo[2] ?? 'Unknown error';
                }
            } catch (PDOException $e) {
                error_log("PDO Exception in update: " . $e->getMessage());
                error_log("SQL State: " . $e->getCode());
                $updateError = $e->getMessage();
                
                // Check if it's a column not found error (shouldn't happen now, but keep as safety)
                if (strpos($e->getMessage(), "Column not found") !== false || 
                    strpos($e->getMessage(), "Unknown column") !== false ||
                    $e->getCode() == '42S22') {
                    
                    // Try with 'booking_id' instead of 'id' as fallback (for upholcare_customers database)
                    error_log("Column 'id' not found, trying fallback with 'booking_id' column...");
                    $fallbackQuery = str_replace('`id`', '`booking_id`', $updateQuery);
                    try {
                        $fallbackStmt = $db->prepare($fallbackQuery);
                        $result = $fallbackStmt->execute($updateValues);
                        if ($result) {
                            error_log("Fallback query succeeded with 'id' column");
                            $updateStmt = $fallbackStmt; // Use the fallback statement
                        } else {
                            $errorInfo = $fallbackStmt->errorInfo();
                            error_log("Fallback query also failed: " . print_r($errorInfo, true));
                            throw new Exception("Update failed with both booking_id and id: " . ($errorInfo[2] ?? 'Unknown error'));
                        }
                    } catch (Exception $e2) {
                        error_log("Fallback query exception: " . $e2->getMessage());
                        throw new Exception("Update failed: " . $e->getMessage() . " | Fallback also failed: " . $e2->getMessage());
                    }
                } else {
                    throw $e; // Re-throw if it's a different error
                }
            }
            
            if (!$result && $updateError) {
                throw new Exception("Update failed: " . $updateError);
            }
            
            if ($result) {
                // Verify the update actually happened - check both status and payment_status
                $verifyStmt = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
                $verifyStmt->execute([$bookingId]);
                $verified = $verifyStmt->fetch();
                
                error_log("Verified status after update: " . ($verified['status'] ?? 'NULL'));
                error_log("Verified payment_status after update: " . ($verified['payment_status'] ?? 'NULL'));
                
                // If status didn't update, force update with explicit WHERE clause
                if (!$verified || $verified['status'] !== $newStatus) {
                    error_log("Status mismatch detected! Expected: " . $newStatus . ", Got: " . ($verified['status'] ?? 'NULL') . ". Forcing update...");
                    
                    // Try multiple update methods
                    $forceUpdateStmt = $db->prepare("UPDATE `bookings` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                    $forceResult = $forceUpdateStmt->execute([$newStatus, $bookingId]);
                    
                    if ($forceResult) {
                        // Verify again
                        $verifyStmt2 = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
                        $verifyStmt2->execute([$bookingId]);
                        $verified2 = $verifyStmt2->fetch();
                        error_log("After force update, status is: " . ($verified2['status'] ?? 'NULL'));
                        error_log("After force update, payment_status is: " . ($verified2['payment_status'] ?? 'NULL'));
                    }
                }
                
                // If payment_status was supposed to be updated but wasn't, force update it
                if (isset($updateData['payment_status'])) {
                    $expectedPaymentStatus = $updateData['payment_status'];
                    $actualPaymentStatus = $verified['payment_status'] ?? null;
                    
                    // Handle empty strings as null
                    if ($actualPaymentStatus === '') {
                        $actualPaymentStatus = null;
                    }
                    
                    if ($actualPaymentStatus !== $expectedPaymentStatus) {
                        error_log("Payment status mismatch! Expected: " . $expectedPaymentStatus . ", Got: " . ($actualPaymentStatus ?? 'NULL/EMPTY') . ". Forcing update...");
                        $forcePaymentStmt = $db->prepare("UPDATE `bookings` SET `payment_status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                        $forcePaymentResult = $forcePaymentStmt->execute([$expectedPaymentStatus, $bookingId]);
                        if ($forcePaymentResult) {
                            error_log("Payment status force update successful");
                            // Verify the force update worked
                            $verifyPaymentStmt = $db->prepare("SELECT `payment_status` FROM `bookings` WHERE `id` = ?");
                            $verifyPaymentStmt->execute([$bookingId]);
                            $verifyPaymentResult = $verifyPaymentStmt->fetch();
                            $forceUpdatedStatus = $verifyPaymentResult['payment_status'] ?? null;
                            error_log("After force update, payment_status is: " . ($forceUpdatedStatus ?? 'NULL'));
                            // Update verified array with the forced value
                            $verified['payment_status'] = $forceUpdatedStatus ?? $expectedPaymentStatus;
                        } else {
                            $errorInfo = $forcePaymentStmt->errorInfo();
                            error_log("Payment status force update FAILED: " . print_r($errorInfo, true));
                        }
                    } else {
                        error_log("Payment status matches expected value: " . $expectedPaymentStatus);
                    }
                }
                
                // Add progress update if provided
                if ($progressType && $progressNotes) {
                    $this->addProgressUpdate($bookingId, $adminId, $progressType, $progressNotes);
                }
                
                // Send notifications if requested
                if ($notifyCustomer) {
                    $this->sendStatusUpdateNotification($bookingId, $newStatus, $paymentStatus);
                }
                
                if ($notifyCustomerProgress && $progressType && $progressNotes) {
                    $this->sendProgressUpdateNotification($bookingId, $progressType, $progressNotes);
                }
                
                if ($notifyCustomerDelivery && ($pickupDate || $deliveryDate)) {
                    $this->sendPickupDeliveryNotification($bookingId, $deliveryType, $pickupDate, $deliveryDate, $deliveryAddress);
                }
                
                // Automatically send receipt notification when transaction is completed AND payment is paid
                // Check both status and payment_status changes
                $shouldSendReceipt = false;
                
                // Get final status and payment status after update
                $finalStatus = $newStatus;
                $finalPaymentStatus = isset($updateData['payment_status']) ? strtolower(trim($updateData['payment_status'])) : strtolower(trim($currentPaymentStatus ?? 'unpaid'));
                
                // Check if transaction is completed (status is completed or delivered_and_paid)
                $isCompleted = in_array($finalStatus, ['completed', 'delivered_and_paid']);
                
                // Check if payment is paid
                $isPaid = in_array($finalPaymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']);
                
                if ($isCompleted && $isPaid) {
                    // Check if this is a new completion (status changed to completed) OR payment just became paid
                    $statusChangedToCompleted = in_array($finalStatus, ['completed', 'delivered_and_paid']) && 
                                                !in_array($currentStatus, ['completed', 'delivered_and_paid']);
                    
                    $paymentChangedToPaid = isset($updateData['payment_status']) && 
                                           in_array($finalPaymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']) &&
                                           !in_array(strtolower(trim($currentPaymentStatus ?? 'unpaid')), ['paid', 'paid_full_cash', 'paid_on_delivery_cod']);
                    
                    // Also check if status was already completed and payment just became paid
                    $wasAlreadyCompleted = in_array($currentStatus, ['completed', 'delivered_and_paid']);
                    $paymentJustBecamePaid = isset($updateData['payment_status']) && $paymentChangedToPaid;
                    
                    // Send receipt if:
                    // 1. Status just changed to completed AND payment is paid, OR
                    // 2. Status was already completed AND payment just became paid
                    if (($statusChangedToCompleted && $isPaid) || ($wasAlreadyCompleted && $paymentJustBecamePaid)) {
                        $shouldSendReceipt = true;
                    }
                }
                
                if ($shouldSendReceipt) {
                    // Automatically send receipt notification to customer
                    $this->sendReceiptNotification($bookingId);
                }
                
                // Send notification when status changes to delivered_and_paid
                if ($newStatus === 'delivered_and_paid' && $currentStatus !== 'delivered_and_paid') {
                    try {
                        require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                        $notificationService = new NotificationService();
                        
                        // Get customer details
                        $customerStmt = $db->prepare("
                            SELECT u.email, u.fullname 
                            FROM users u 
                            INNER JOIN bookings b ON u.id = b.user_id 
                            WHERE b.id = ?
                        ");
                        $customerStmt->execute([$bookingId]);
                        $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($customer && $customer['email']) {
                            $subject = "Delivery Completed - Thank You! - Booking #" . $bookingId;
                            $message = "Dear {$customer['fullname']},\n\n";
                            $message .= "🎉 Thank you for trusting UpholCare!\n\n";
                            $message .= "Your item has been successfully delivered and payment has been received.\n\n";
                            $message .= "We hope you are satisfied with our service. If you have any questions or concerns, please don't hesitate to contact us.\n\n";
                            $message .= "Your official receipt will be issued shortly. You can view it in your account or it will be sent to your email.\n\n";
                            $message .= "Thank you for choosing UpholCare!\n\n";
                            $message .= "Best regards,\nUpholCare Team";
                            
                            $notificationService->sendEmail($customer['email'], $subject, $message);
                        }
                    } catch (Exception $e) {
                        error_log("Error sending delivered_and_paid notification for booking ID {$bookingId}: " . $e->getMessage());
                        // Don't fail the whole request if notification fails
                    }
                }
                
                // Commit the transaction to save all changes
                $db->commit();
                
                // Final verification - get the actual saved status and payment status
                // Use id as primary key for db_upholcare - use backticks for safety
                $finalVerifyStmt = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
                $finalVerifyStmt->execute([$bookingId]);
                $finalStatus = $finalVerifyStmt->fetch();
                $actualStatus = $finalStatus['status'] ?? $newStatus;
                
                // CRITICAL: If we updated to inspection/repair statuses, ensure it stays that way
                // Never allow reverting from advanced statuses back to basic statuses
                $protectedStatuses = ['to_inspect', 'inspect_completed', 'preview_receipt_sent', 'under_repair', 'for_repair'];
                if (in_array($newStatus, $protectedStatuses) && $actualStatus !== $newStatus) {
                    error_log("CRITICAL: Status should be '{$newStatus}' but got '{$actualStatus}'. Forcing update to '{$newStatus}'...");
                    $forceStmt = $db->prepare("UPDATE `bookings` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                    $forceStmt->execute([$newStatus, $bookingId]);
                    $actualStatus = $newStatus;
                    error_log("Forced status to '{$newStatus}' for booking #{$bookingId}");
                }
                
                // Additional safeguard: If status is advanced, never allow it to be basic statuses
                $advancedStatuses = ['to_inspect', 'inspect_completed', 'preview_receipt_sent', 'for_repair', 'under_repair', 'completed', 'paid', 'cancelled'];
                if (in_array(strtolower($actualStatus), $advancedStatuses) && in_array($actualStatus, ['pending', 'approved'])) {
                    error_log("CRITICAL: Status is advanced but showing as 'approved'. This should not happen!");
                    // Force it back to the expected advanced status
                    if ($newStatus !== 'approved' && in_array(strtolower($newStatus), $advancedStatuses)) {
                        $forceCorrectStmt = $db->prepare("UPDATE `bookings` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                        $forceCorrectStmt->execute([$newStatus, $bookingId]);
                        $actualStatus = $newStatus;
                        error_log("Corrected status from 'approved' back to '{$newStatus}' for booking #{$bookingId}");
                    }
                }
                
                // Get payment_status - handle empty strings and null values
                $dbPaymentStatus = $finalStatus['payment_status'] ?? null;
                if ($dbPaymentStatus === '' || $dbPaymentStatus === null) {
                    // If database has empty/null, use what we tried to save, or current value
                    $actualPaymentStatus = $finalPaymentStatus ?? $currentPaymentStatus ?? 'unpaid';
                } else {
                    // Use the actual value from database
                    $actualPaymentStatus = $dbPaymentStatus;
                }
                
                // If we tried to update payment_status but it's still not correct, force update one more time
                if (isset($updateData['payment_status']) && $actualPaymentStatus !== $updateData['payment_status']) {
                    error_log("Final check: payment_status mismatch. Expected: " . $updateData['payment_status'] . ", Got: " . $actualPaymentStatus . ". Forcing final update...");
                    $finalForceStmt = $db->prepare("UPDATE `bookings` SET `payment_status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                    $finalForceResult = $finalForceStmt->execute([$updateData['payment_status'], $bookingId]);
                    if ($finalForceResult) {
                        $actualPaymentStatus = $updateData['payment_status'];
                        error_log("Final force update successful. payment_status is now: " . $actualPaymentStatus);
                    }
                }
                
                error_log("Final status returned to client: " . $actualStatus);
                error_log("Final payment status returned to client: " . $actualPaymentStatus);
                
                // CRITICAL FINAL CHECK: If we tried to update to inspection/repair statuses, verify it's actually that status
                $protectedStatuses = ['to_inspect', 'inspect_completed', 'preview_receipt_sent', 'under_repair'];
                if (in_array($newStatus, $protectedStatuses)) {
                    // Do one more verification query
                    $finalCheckStmt = $db->prepare("SELECT `status` FROM `bookings` WHERE `id` = ?");
                    $finalCheckStmt->execute([$bookingId]);
                    $finalCheck = $finalCheckStmt->fetch();
                    $finalStatusCheck = $finalCheck['status'] ?? null;
                    
                    if ($finalStatusCheck !== $newStatus) {
                        error_log("CRITICAL FINAL CHECK: Status is '{$finalStatusCheck}' but should be '{$newStatus}'. Forcing one more time...");
                        $finalForceStmt = $db->prepare("UPDATE `bookings` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                        $finalForceStmt->execute([$newStatus, $bookingId]);
                        $actualStatus = $newStatus;
                        error_log("Final force update completed. Status is now: {$newStatus}");
                    } else {
                        error_log("Final check passed: Status is correctly '{$newStatus}'");
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking updated successfully. All changes have been saved.',
                    'status' => $actualStatus,
                    'payment_status' => $actualPaymentStatus,
                    'booking_id' => $bookingId,
                    'previous_status' => $currentStatus,
                    'new_status' => $actualStatus
                ]);
                ob_end_flush();
            } else {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                ob_clean();
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
                ob_end_flush();
            }
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            ob_clean();
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            error_log("Error updating booking status: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred while updating status. Please try again.']);
            ob_end_flush();
            exit;
        }
    }
    
    /**
     * Mark Item as Received (for_dropoff → for_inspect)
     * For Delivery Service: Customer brought item to shop
     */
    public function markItemReceived() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Only allow if status is 'for_dropoff'
            if ($booking['status'] !== 'for_dropoff') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Item can only be marked as received when status is "For Drop-off"'
                ]);
                return;
            }
            
            // Update status to 'for_inspect'
            $updateStmt = $db->prepare("UPDATE bookings SET status = 'for_inspect', updated_at = NOW() WHERE id = ?");
            $result = $updateStmt->execute([$bookingId]);
            
            if ($result) {
                // Send notification to customer
                try {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    // Get customer details
                    $customerStmt = $db->prepare("SELECT u.email, u.fullname FROM users u INNER JOIN bookings b ON u.id = b.user_id WHERE b.id = ?");
                    $customerStmt->execute([$bookingId]);
                    $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($customer && $customer['email']) {
                        $subject = "Item Received - Ready for Inspection - Booking #" . $bookingId;
                        $message = "Dear {$customer['fullname']},\n\n";
                        $message .= "Your item has been received at our shop. Our team will now proceed with inspection.\n\n";
                        $message .= "We will inspect your item for:\n";
                        $message .= "• Damage assessment\n";
                        $message .= "• Material requirements\n";
                        $message .= "• Labor cost estimation\n";
                        $message .= "• Repair timeline\n\n";
                        $message .= "Once inspection is complete, you will receive a preview receipt with the estimated cost for your approval.\n\n";
                        $message .= "Thank you for choosing UpholCare!\n\n";
                        $message .= "Best regards,\nUpholCare Team";
                        
                        $notificationService->sendEmail($customer['email'], $subject, $message);
                    }
                } catch (Exception $e) {
                    error_log("Error sending notification for booking ID {$bookingId}: " . $e->getMessage());
                    // Don't fail the whole request if notification fails
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Item marked as received. Status changed to "For Inspect". Customer has been notified.',
                    'new_status' => 'for_inspect'
                ]);
            } else {
                throw new Exception('Database update failed');
            }
        } catch (Exception $e) {
            error_log("Error marking item as received: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to mark item as received']);
        }
    }
    
    /**
     * Mark as Out for Delivery (repair_completed_ready_to_deliver → out_for_delivery)
     */
    public function markOutForDelivery() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Only allow if status is 'repair_completed_ready_to_deliver'
            if ($booking['status'] !== 'repair_completed_ready_to_deliver') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Can only mark as out for delivery when status is "Repair Completed - Ready for Delivery"'
                ]);
                return;
            }
            
            // Update status to 'out_for_delivery'
            $updateStmt = $db->prepare("UPDATE bookings SET status = 'out_for_delivery', updated_at = NOW() WHERE id = ?");
            $result = $updateStmt->execute([$bookingId]);
            
            if ($result) {
                // Send notification to customer
                try {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    // Get customer and booking details
                    $customerStmt = $db->prepare("
                        SELECT u.email, u.fullname, b.delivery_date, b.delivery_address 
                        FROM users u 
                        INNER JOIN bookings b ON u.id = b.user_id 
                        WHERE b.id = ?
                    ");
                    $customerStmt->execute([$bookingId]);
                    $data = $customerStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($data && $data['email']) {
                        $deliveryDate = !empty($data['delivery_date']) ? date('F d, Y', strtotime($data['delivery_date'])) : 'your scheduled date';
                        $deliveryAddress = !empty($data['delivery_address']) ? $data['delivery_address'] : 'your address';
                        
                        $subject = "Your Item is On Its Way - Booking #" . $bookingId;
                        $message = "Dear {$data['fullname']},\n\n";
                        $message .= "Great news! Your item repair is completed and your order is now out for delivery.\n\n";
                        $message .= "📦 Delivery Details:\n";
                        $message .= "• Scheduled Date: {$deliveryDate}\n";
                        $message .= "• Delivery Address: {$deliveryAddress}\n\n";
                        $message .= "Please prepare payment (COD) when the rider arrives.\n\n";
                        $message .= "We'll notify you once your item has been delivered.\n\n";
                        $message .= "Thank you for choosing UpholCare!\n\n";
                        $message .= "Best regards,\nUpholCare Team";
                        
                        $notificationService->sendEmail($data['email'], $subject, $message);
                    }
                } catch (Exception $e) {
                    error_log("Error sending notification for booking ID {$bookingId}: " . $e->getMessage());
                    // Don't fail the whole request if notification fails
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking marked as out for delivery. Status changed to "On Delivery". Customer has been notified.',
                    'new_status' => 'out_for_delivery'
                ]);
            } else {
                throw new Exception('Database update failed');
            }
        } catch (Exception $e) {
            error_log("Error marking as out for delivery: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to mark as out for delivery']);
        }
    }
    
    /**
     * Auto-determine next status based on current status and service option
     */
    public function autoNextStatus($currentStatus, $serviceOption) {
        // Delivery Flow: Customer brings item → Inspection → Repair → Delivery → Payment → Official Receipt
        $deliveryFlow = [
            "for_dropoff" => "for_inspect",           // Customer brought item to shop
            "for_inspect" => "to_inspect",         // Ready for inspection
            "to_inspect" => "inspection_completed_waiting_approval",   // Inspection done, waiting for customer approval
            "inspection_completed_waiting_approval" => "under_repair",    // After customer approves preview receipt
            "under_repair" => "repair_completed_ready_to_deliver",  // Repair finished, ready for delivery
            "repair_completed_ready_to_deliver" => "out_for_delivery", // Delivery scheduled, out for delivery
            "out_for_delivery" => "delivered_and_paid" // Delivered and payment received (COD)
        ];
        
        // Pickup Flow: Admin picks up → Inspection → Repair → Customer picks up → Payment
        $pickupFlow = [
            "for_pickup" => "picked_up",           // Admin picked up item
            "picked_up" => "to_inspect",           // Ready for inspection
            "to_inspect" => "inspect_completed",   // Inspection done
            "inspect_completed" => "for_repair",    // After customer approves preview receipt
            "for_repair" => "under_repair",        // Repair started
            "under_repair" => "repair_completed",  // Repair finished
            "repair_completed" => "completed"      // Customer picks up (payment before or on pickup)
        ];
        
        $serviceOption = strtolower(trim($serviceOption ?? ''));
        
        if ($serviceOption === "delivery") {
            return $deliveryFlow[$currentStatus] ?? $currentStatus;
        }
        
        if ($serviceOption === "pickup") {
            return $pickupFlow[$currentStatus] ?? $currentStatus;
        }
        
        // Both: Follows pickup flow until repair, then delivery flow
        if ($serviceOption === "both") {
            if (in_array($currentStatus, ['for_pickup', 'picked_up', 'to_inspect', 'inspect_completed', 'for_repair', 'under_repair'])) {
                return $pickupFlow[$currentStatus] ?? $currentStatus;
            } else {
                // After repair, follow delivery flow
                return $deliveryFlow[$currentStatus] ?? $currentStatus;
            }
        }
        
        return $currentStatus;
    }
    
    /**
     * Assign technician to booking
     */
    private function assignTechnicianToBooking($bookingId, $technicianId, $adminId, $notes = '') {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Create booking_technicians table if it doesn't exist
            $db->exec("
                CREATE TABLE IF NOT EXISTS booking_technicians (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    booking_id INT(11) NOT NULL,
                    technician_id INT(11) NOT NULL,
                    assigned_by_admin_id INT(11) NOT NULL,
                    notes TEXT,
                    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
                    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    completed_at TIMESTAMP NULL,
                    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
                    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE,
                    KEY idx_booking_id (booking_id),
                    KEY idx_technician_id (technician_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Deactivate previous assignments
            $stmt = $db->prepare("UPDATE booking_technicians SET status = 'cancelled' WHERE booking_id = ? AND status = 'active'");
            $stmt->execute([$bookingId]);
            
            // Assign new technician
            $stmt = $db->prepare("
                INSERT INTO booking_technicians (booking_id, technician_id, assigned_by_admin_id, notes, status)
                VALUES (?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$bookingId, $technicianId, $adminId, $notes]);
        } catch (Exception $e) {
            error_log("Error assigning technician: " . $e->getMessage());
        }
    }
    
    /**
     * Send Preview to Customer
     * Admin can send a preview image/note to customer about their purchase
     */
    public function sendPreviewToCustomer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $previewNotes = $_POST['preview_notes'] ?? '';
        $previewImage = null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get full booking details with all related information
            $checkColumn = $db->query("SHOW COLUMNS FROM bookings LIKE 'selected_color_id'");
            $hasSelectedColorId = $checkColumn->rowCount() > 0;
            
            if ($hasSelectedColorId) {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        i.color_name,
                        i.color_code,
                        i.color_hex,
                        COALESCE(i.price_per_meter, 0) as inventory_price_per_meter,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        LEFT JOIN inventory i ON b.selected_color_id = i.id
                        WHERE b.id = ?";
            } else {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        NULL as color_name,
                        NULL as color_code,
                        NULL as color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        WHERE b.id = ?";
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Handle file upload if provided
            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'previews' . DS;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExtension = pathinfo($_FILES['preview_image']['name'], PATHINFO_EXTENSION);
                $fileName = 'preview_' . $bookingId . '_' . time() . '.' . $fileExtension;
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['preview_image']['tmp_name'], $filePath)) {
                    $previewImage = 'uploads/previews/' . $fileName;
                }
            }
            
            // Update booking with preview info
            $stmt = $db->prepare("UPDATE bookings SET preview_image = ?, preview_notes = ?, preview_sent_at = NOW() WHERE id = ?");
            $stmt->execute([$previewImage, $previewNotes, $bookingId]);
            
            // Send notification to customer
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            // Create in-app notification
            $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                                VALUES (?, 'preview', 'Booking Preview Available', 'Admin has sent you a preview of your booking details and pricing.', ?, NOW())");
            $stmt->execute([$booking['user_id'], $bookingId]);
            
            // Send preview email with booking details and receipt-style pricing
            $notificationService->sendPreviewEmail(
                $booking['email'],
                $booking['customer_name'],
                $booking,
                $previewImage,
                $previewNotes
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Preview sent to customer successfully!'
            ]);
        } catch (Exception $e) {
            error_log('Error sending preview: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send preview: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Send Quotation After Inspection (for PICKUP service option)
     * This is Email #2 - sent AFTER item is picked up and inspected
     * Contains FINAL pricing based on actual measurements and damage assessment
     */
    public function sendQuotationAfterInspection() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking has selected_color_id for joining inventory table
            $checkStmt = $db->prepare("SELECT selected_color_id FROM bookings WHERE id = ?");
            $checkStmt->execute([$bookingId]);
            $checkResult = $checkStmt->fetch();
            $hasSelectedColorId = !empty($checkResult['selected_color_id']);
            
            // Get booking details with customer info
            if ($hasSelectedColorId) {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        i.color_name,
                        i.color_code,
                        i.color_hex,
                        COALESCE(i.price_per_meter, 0) as inventory_price_per_meter,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        LEFT JOIN inventory i ON b.selected_color_id = i.id
                        WHERE b.id = ?";
            } else {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        NULL as color_name,
                        NULL as color_code,
                        NULL as color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        WHERE b.id = ?";
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Verify this is a PICKUP service option booking
            $serviceOption = strtolower($booking['service_option'] ?? 'pickup');
            if ($serviceOption !== 'pickup') {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Quotation after inspection is only for PICKUP service option. This booking is: ' . $serviceOption
                ]);
                exit;
            }
            
            // Update booking - mark that quotation was sent
            $stmt = $db->prepare("UPDATE bookings SET quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$bookingId]);
            
            // Send notification to customer
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            // Create in-app notification
            $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                                VALUES (?, 'quotation', 'Final Quotation Available', 'Your item has been inspected. Final quotation with pricing is now available.', ?, NOW())");
            $stmt->execute([$booking['user_id'], $bookingId]);
            
            // Send quotation email after inspection
            $notificationService->sendQuotationAfterInspection(
                $booking['email'],
                $booking['customer_name'],
                'Booking #' . $bookingId, // Use booking ID instead of booking number
                $booking
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Final quotation sent to customer successfully! Customer will receive email with complete pricing details.'
            ]);
        } catch (Exception $e) {
            error_log('Error sending quotation after inspection: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send quotation: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Add progress update to booking
     */
    private function addProgressUpdate($bookingId, $adminId, $progressType, $notes) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Create booking_progress_logs table if it doesn't exist
            $db->exec("
                CREATE TABLE IF NOT EXISTS booking_progress_logs (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    booking_id INT(11) NOT NULL,
                    admin_id INT(11) NOT NULL,
                    progress_type VARCHAR(100) NOT NULL,
                    notes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
                    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
                    KEY idx_booking_id (booking_id),
                    KEY idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $stmt = $db->prepare("
                INSERT INTO booking_progress_logs (booking_id, admin_id, progress_type, notes)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$bookingId, $adminId, $progressType, $notes]);
        } catch (Exception $e) {
            error_log("Error adding progress update: " . $e->getMessage());
        }
    }
    
    /**
     * Send status update notification
     */
    private function sendStatusUpdateNotification($bookingId, $status, $paymentStatus) {
        // Implementation for sending status update email
        // This would use NotificationService
    }
    
    /**
     * Send progress update notification
     */
    private function sendProgressUpdateNotification($bookingId, $progressType, $notes) {
        // Implementation for sending progress update email
    }
    
    /**
     * Send pickup/delivery notification
     */
    private function sendPickupDeliveryNotification($bookingId, $deliveryType, $pickupDate, $deliveryDate, $address) {
        // Implementation for sending pickup/delivery email
    }
    
    /**
     * Automatically send receipt notification to customer when payment is completed
     */
    private function sendReceiptNotification($bookingId) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details with customer info
            $stmt = $db->prepare("
                SELECT b.*, u.id as customer_id, u.fullname as customer_name, u.email as customer_email
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ?
            ");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                error_log("Cannot send receipt notification: Booking not found for booking ID: " . $bookingId);
                return false;
            }
            
            if (empty($booking['customer_id']) || empty($booking['user_id'])) {
                error_log("Cannot send receipt notification: Customer not found for booking ID: " . $bookingId . " (user_id: " . ($booking['user_id'] ?? 'null') . ")");
                return false;
            }
            
            $customerId = $booking['customer_id'];
            $customerName = $booking['customer_name'] ?? 'Customer';
            $bookingNumber = 'Booking #' . $bookingId; // Use booking ID instead
            $grandTotal = floatval($booking['grand_total'] ?? $booking['total_amount'] ?? 0);
            
            // Check if receipt notification was already sent for this booking to avoid duplicates
            // First check if related_id column exists
            $checkColumns = $db->query("SHOW COLUMNS FROM notifications LIKE 'related_id'");
            $hasRelatedId = $checkColumns->fetch();
            
            if ($hasRelatedId) {
                $checkStmt = $db->prepare("
                    SELECT id FROM notifications 
                    WHERE user_id = ? 
                    AND related_id = ? 
                    AND related_type = 'booking' 
                    AND title = 'Payment Receipt Available'
                    LIMIT 1
                ");
                $checkStmt->execute([$customerId, $bookingId]);
            } else {
                // Fallback: check by title and message content
                $checkStmt = $db->prepare("
                    SELECT id FROM notifications 
                    WHERE user_id = ? 
                    AND title = 'Payment Receipt Available'
                    AND message LIKE ?
                    LIMIT 1
                ");
                $checkStmt->execute([$customerId, "%{$bookingNumber}%"]);
            }
            $existingNotification = $checkStmt->fetch();
            
            // Only send notification and email if it hasn't been sent before
            if (!$existingNotification) {
                // Create notification for customer
                try {
                    // Check if related_id and related_type columns exist
                    $checkColumns = $db->query("SHOW COLUMNS FROM notifications LIKE 'related_id'");
                    $hasRelatedId = $checkColumns->fetch();
                    
                    $title = 'Payment Receipt Available - Approval Required';
                    $message = "Your payment receipt for booking {$bookingNumber} (Amount: ₱" . number_format($grandTotal, 2) . ") is now available. Please review and approve the receipt to proceed with the repair. Once approved, your item will be moved to 'Under Repair' status.";
                    
                    if ($hasRelatedId) {
                        // Use extended notification structure with related_id and related_type
                        $stmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, related_id, related_type, created_at)
                            VALUES (?, 'success', ?, ?, ?, 'booking', NOW())
                        ");
                        $result = $stmt->execute([
                            $customerId,
                            $title,
                            $message,
                            $bookingId
                        ]);
                    } else {
                        // Use basic notification structure without related_id and related_type
                        $stmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, created_at)
                            VALUES (?, 'success', ?, ?, NOW())
                        ");
                        $result = $stmt->execute([
                            $customerId,
                            $title,
                            $message
                        ]);
                    }
                    
                    if (!$result) {
                        $errorInfo = $stmt->errorInfo();
                        error_log("Failed to insert notification for booking ID: " . $bookingId);
                        error_log("PDO Error Info: " . print_r($errorInfo, true));
                        throw new Exception("Failed to insert notification: " . ($errorInfo[2] ?? 'Unknown error'));
                    }
                } catch (PDOException $e) {
                    error_log("PDO Exception inserting notification: " . $e->getMessage());
                    error_log("SQL State: " . $e->getCode());
                    // Check if it's a table doesn't exist error
                    if ($e->getCode() == '42S02') {
                        error_log("ERROR: notifications table does not exist. Please create it first.");
                    } elseif (strpos($e->getMessage(), 'Unknown column') !== false) {
                        error_log("ERROR: notifications table is missing required columns. Please update the table structure.");
                    }
                    throw $e; // Re-throw to be caught by outer catch
                }
                
                // Also send email notification if email is configured
                if (!empty($booking['customer_email'])) {
                    try {
                        $notificationServicePath = ROOT . DS . 'core' . DS . 'NotificationService.php';
                        if (file_exists($notificationServicePath)) {
                            require_once $notificationServicePath;
                            $notificationService = new NotificationService();
                            
                            // Send receipt email using NotificationService
                            $notificationService->sendPaymentReceipt(
                                $booking['customer_email'],
                                $customerName,
                                $bookingNumber,
                                $booking,
                                $grandTotal
                            );
                        } else {
                            error_log("NotificationService.php not found at: " . $notificationServicePath);
                            // Don't fail the whole process if NotificationService is missing
                        }
                    } catch (Exception $e) {
                        error_log("Error sending receipt email: " . $e->getMessage());
                        error_log("Stack trace: " . $e->getTraceAsString());
                        // Don't fail the whole process if email fails
                    } catch (Error $e) {
                        error_log("Fatal error sending receipt email: " . $e->getMessage());
                        error_log("Stack trace: " . $e->getTraceAsString());
                        // Don't fail the whole process if email fails
                    }
                }
                
                error_log("Receipt notification sent successfully to customer ID: " . $customerId . " for booking ID: " . $bookingId);
            } else {
                error_log("Receipt notification already sent for booking ID: " . $bookingId . ", skipping duplicate notification and email.");
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Database error sending receipt notification: " . $e->getMessage());
            error_log("SQL Error Code: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        } catch (Exception $e) {
            error_log("Error sending receipt notification: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        } catch (Error $e) {
            error_log("Fatal error sending receipt notification: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    
    /**
     * Delete Booking (AJAX)
     */
    public function deleteBooking() {
        // Start output buffering to prevent any accidental output
        ob_start();
        
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Check request method
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($requestMethod !== 'POST') {
            ob_clean();
            http_response_code(405);
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed. Expected POST, got ' . $requestMethod,
                'received_method' => $requestMethod
            ]);
            ob_end_flush();
            exit;
        }
        
        // Get booking ID from POST data
        $bookingId = $_POST['booking_id'] ?? null;
        
        // Also check if it's in the request body (for FormData)
        if (!$bookingId && !empty(file_get_contents('php://input'))) {
            parse_str(file_get_contents('php://input'), $input);
            $bookingId = $input['booking_id'] ?? null;
        }
        
        if (!$bookingId || !is_numeric($bookingId)) {
            ob_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            ob_end_flush();
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id, status, payment_status, user_id FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                ob_clean();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                ob_end_flush();
                exit;
            }
            
            // Check if booking can be deleted
            // Allow deletion if:
            // 1. Status is pending, cancelled, declined, OR
            // 2. Status is completed but payment is unpaid (unused completed bookings)
            // 3. Payment status is unpaid or cancelled
            $status = strtolower($booking['status'] ?? '');
            $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
            
            $allowedStatuses = ['pending', 'cancelled', 'declined'];
            $restrictedStatuses = ['delivered_and_paid', 'under_repair'];
            
            // Allow deletion of completed bookings only if unpaid
            if ($status === 'completed' && !in_array($paymentStatus, ['unpaid', 'cancelled'])) {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cannot delete completed booking with payment status: ' . ucfirst($paymentStatus) . '. Only unpaid completed bookings can be deleted.'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Restrict deletion of active/processing bookings
            if (in_array($status, $restrictedStatuses)) {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cannot delete booking with status: ' . ucfirst($status) . '. Only unused bookings (pending, cancelled, rejected, completed unpaid) can be deleted.'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Restrict deletion of paid bookings (except cancelled)
            if (!in_array($paymentStatus, ['unpaid', 'cancelled']) && $status !== 'completed') {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cannot delete booking with payment status: ' . ucfirst($paymentStatus) . '. Only unpaid bookings can be deleted.'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Start transaction
            $db->beginTransaction();
            
            try {
                // Delete related records first (if any)
                // Check if related_id column exists in notifications table
                $hasRelatedId = false;
                try {
                    $checkStmt = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'notifications' 
                        AND COLUMN_NAME = 'related_id'
                    ");
                    $hasRelatedId = $checkStmt->fetch()['cnt'] > 0;
                } catch (Exception $e) {
                    // Column doesn't exist, skip notification deletion
                    error_log("related_id column not found in notifications table: " . $e->getMessage());
                }
                
                // Delete notifications related to this booking (only if related_id column exists)
                if ($hasRelatedId) {
                    try {
                        // Check if related_type column also exists
                        $checkStmt = $db->query("
                            SELECT COUNT(*) as cnt 
                            FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'notifications' 
                            AND COLUMN_NAME = 'related_type'
                        ");
                        $hasRelatedType = $checkStmt->fetch()['cnt'] > 0;
                        
                        if ($hasRelatedType) {
                            $stmt = $db->prepare("DELETE FROM notifications WHERE related_id = ? AND related_type = 'booking'");
                            $stmt->execute([$bookingId]);
                        } else {
                            $stmt = $db->prepare("DELETE FROM notifications WHERE related_id = ?");
                            $stmt->execute([$bookingId]);
                        }
                    } catch (Exception $e) {
                        // If deletion fails, log but continue
                        error_log("Error deleting notifications: " . $e->getMessage());
                    }
                } else {
                    // Try to delete by message content if related_id doesn't exist
                    try {
                        $stmt = $db->prepare("DELETE FROM notifications WHERE user_id = ? AND message LIKE ?");
                        $stmt->execute([$booking['user_id'] ?? 0, '%booking%' . $bookingId . '%']);
                    } catch (Exception $e) {
                        error_log("Error deleting notifications by message: " . $e->getMessage());
                    }
                }
                
                // Delete related quotations (if any) - must be deleted before booking due to foreign key
                try {
                    // Check if quotations table exists
                    $checkTable = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.TABLES 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'quotations'
                    ");
                    $tableExists = $checkTable->fetch()['cnt'] > 0;
                    
                    if ($tableExists) {
                        $stmt = $db->prepare("DELETE FROM quotations WHERE booking_id = ?");
                        $stmt->execute([$bookingId]);
                        error_log("Deleted quotations for booking ID: " . $bookingId);
                    }
                } catch (Exception $e) {
                    error_log("Error deleting quotations: " . $e->getMessage());
                    // Continue even if quotations deletion fails
                }
                
                // Delete related payments (if any) - must be deleted before booking due to foreign key
                try {
                    // Check if payments table exists
                    $checkTable = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.TABLES 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'payments'
                    ");
                    $tableExists = $checkTable->fetch()['cnt'] > 0;
                    
                    if ($tableExists) {
                        $stmt = $db->prepare("DELETE FROM payments WHERE booking_id = ?");
                        $stmt->execute([$bookingId]);
                        error_log("Deleted payments for booking ID: " . $bookingId);
                    }
                } catch (Exception $e) {
                    error_log("Error deleting payments: " . $e->getMessage());
                    // Continue even if payments deletion fails
                }
                
                // Delete booking
                $stmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
                $stmt->execute([$bookingId]);
                
                // Commit transaction
                $db->commit();
                
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking deleted successfully'
                ]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log("Error deleting booking: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            ob_clean();
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Error deleting booking: ' . $e->getMessage(),
                'error' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $e->getMessage() : null
            ]);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Get Booking Details (AJAX)
     */
    public function getBookingDetails($bookingId) {
        // Start output buffering to catch any unexpected output
        ob_start();
        
        try {
            // Set headers first to prevent any output before JSON
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            if (empty($bookingId) || !is_numeric($bookingId)) {
                http_response_code(400);
                ob_clean(); // Clear any output
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check if selected_color_id column exists in bookings table
            $checkColumn = $db->query("SHOW COLUMNS FROM bookings LIKE 'selected_color_id'");
            $hasSelectedColorId = $checkColumn->rowCount() > 0;
            
            // Check which type column exists in inventory table
            $checkFabricType = $db->query("SHOW COLUMNS FROM inventory LIKE 'fabric_type'");
            $hasFabricType = $checkFabricType->rowCount() > 0;
            $checkLeatherType = $db->query("SHOW COLUMNS FROM inventory LIKE 'leather_type'");
            $hasLeatherType = $checkLeatherType->rowCount() > 0;
            
            // Check if price_per_meter column exists
            $checkPricePerMeter = $db->query("SHOW COLUMNS FROM inventory LIKE 'price_per_meter'");
            $hasPricePerMeter = $checkPricePerMeter->rowCount() > 0;
            
            // Build inventory type selection based on which column exists
            $inventoryTypeSelect = '';
            if ($hasFabricType && $hasLeatherType) {
                // Both exist, use COALESCE to get either one
                $inventoryTypeSelect = "COALESCE(i.fabric_type, i.leather_type) as inventory_type";
            } elseif ($hasFabricType) {
                $inventoryTypeSelect = "i.fabric_type as inventory_type";
            } elseif ($hasLeatherType) {
                $inventoryTypeSelect = "i.leather_type as inventory_type";
            } else {
                $inventoryTypeSelect = "NULL as inventory_type";
            }
            
            // Build price selection based on which column exists
            // Try price_per_meter first, then fallback to price_per_unit
            $checkPricePerUnit = $db->query("SHOW COLUMNS FROM inventory LIKE 'price_per_unit'");
            $hasPricePerUnit = $checkPricePerUnit->rowCount() > 0;
            
            $inventoryPriceSelect = '';
            if ($hasPricePerMeter && $hasPricePerUnit) {
                // Both exist, prefer price_per_meter but fallback to price_per_unit if price_per_meter is 0
                $inventoryPriceSelect = "COALESCE(NULLIF(i.price_per_meter, 0), i.price_per_unit, 0) as inventory_price_per_meter";
            } elseif ($hasPricePerMeter) {
                $inventoryPriceSelect = "COALESCE(i.price_per_meter, 0) as inventory_price_per_meter";
            } elseif ($hasPricePerUnit) {
                $inventoryPriceSelect = "COALESCE(i.price_per_unit, 0) as inventory_price_per_meter";
            } else {
                $inventoryPriceSelect = "0 as inventory_price_per_meter";
            }
            
            // Build SQL query based on whether selected_color_id column exists
            if ($hasSelectedColorId) {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        i.color_name,
                        i.color_code,
                        i.color_hex,
                        {$inventoryPriceSelect},
                        {$inventoryTypeSelect},
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        LEFT JOIN inventory i ON b.selected_color_id = i.id
                        WHERE b.id = ?";
            } else {
                // Column doesn't exist, skip the inventory join
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        NULL as color_name,
                        NULL as color_code,
                        NULL as color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        WHERE b.id = ?";
            }
            
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                $errorInfo = $db->errorInfo();
                throw new Exception('Failed to prepare database query: ' . ($errorInfo[2] ?? 'Unknown error'));
            }
            
            $execResult = $stmt->execute([$bookingId]);
            if (!$execResult) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Failed to execute query: ' . ($errorInfo[2] ?? 'Unknown error'));
            }
            
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Preserve actual status - only default to 'pending' if truly NULL or empty
            if ($booking) {
                $status = trim($booking['status'] ?? '');
                if ($status === '' || $status === null || strtolower($status) === 'null') {
                    $booking['status'] = 'pending';
                } else {
                    // Preserve the actual status from database
                    $booking['status'] = $status;
                }
                
                // Ensure all values are JSON-serializable
                foreach ($booking as $key => $value) {
                    if (is_resource($value)) {
                        $booking[$key] = null;
                    } elseif (is_object($value)) {
                        $booking[$key] = (string)$value;
                    } elseif (is_array($value)) {
                        // Handle nested arrays
                        $booking[$key] = json_decode(json_encode($value), true);
                    }
                }
                
                ob_clean(); // Clear any unexpected output
                echo json_encode(['success' => true, 'booking' => $booking], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
            } else {
                http_response_code(404);
                ob_clean(); // Clear any unexpected output
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
            }
        } catch (PDOException $e) {
            error_log("Database error in getBookingDetails (Booking ID: {$bookingId}): " . $e->getMessage());
            error_log("PDO Error Info: " . print_r($e->errorInfo ?? [], true));
            http_response_code(500);
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => false, 
                'message' => 'Database error occurred. Please try again later.',
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error in getBookingDetails (Booking ID: {$bookingId}): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => false, 
                'message' => 'An error occurred while loading booking details.',
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            ob_end_flush();
        }
        exit;
    }
    
    /**
     * Accept Booking (Simplified - JSON only)
     */
    public function acceptBooking() {
        ob_start();
        
        // Set JSON header immediately
        if (!headers_sent()) {
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_clean();
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method. POST required.'
            ]);
            ob_end_flush();
            exit;
        }
        
        // Read JSON input
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        
        if (!$data || !isset($data['booking_id'])) {
            ob_clean();
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid data'
            ]);
            ob_end_flush();
            exit;
        }
        
        $bookingId = $data['booking_id'];
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                ob_clean();
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Booking not found'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Check if booking is already accepted or in inspection
            if (in_array($booking['status'], ['accepted', 'approved', 'for_pickup', 'for_dropoff', 'for_inspect', 'to_inspect', 'for_inspection', 'picked_up'])) {
                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'Booking is already accepted or in progress'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Determine correct status based on service option
            $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
            
            // Log for debugging
            error_log("acceptBooking: Booking ID {$bookingId}, Service Option: '{$serviceOption}', Current Status: '{$booking['status']}'");
            
            // NEW WORKFLOW: For Delivery service option
            if ($serviceOption === 'delivery') {
                // Delivery: Customer must bring item to shop → status 'for_dropoff'
                $newStatus = 'for_dropoff';
                $statusMessage = 'Booking accepted. Customer must bring item to shop. Status changed to "For Drop-off".';
            } elseif ($serviceOption === 'pickup' || $serviceOption === 'both') {
                // Pickup/Both: Admin picks up item → goes to 'for_pickup'
                $newStatus = 'for_pickup';
                $statusMessage = 'Booking accepted successfully. Status changed to "For Pick-Up".';
            } else {
                // Default to 'approved' for walk-in or other options
                $newStatus = 'approved';
                $statusMessage = 'Booking accepted successfully. Status changed to "Approved".';
            }
            
            error_log("acceptBooking: Will update booking ID {$bookingId} to status '{$newStatus}'");
            
            // Update status - Use explicit transaction to ensure data consistency
            // CRITICAL: Check if status value exists in ENUM before updating
            try {
                // First, verify the status value is valid by checking ENUM
                $checkEnumStmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
                $enumColumn = $checkEnumStmt->fetch(PDO::FETCH_ASSOC);
                $enumType = $enumColumn['Type'] ?? '';
                
                // Check if the new status is in the ENUM
                if (stripos($enumType, $newStatus) === false) {
                    error_log("acceptBooking: ✗ CRITICAL ERROR - Status '{$newStatus}' is NOT in the database ENUM!");
                    error_log("acceptBooking: Current ENUM values: {$enumType}");
                    throw new Exception("Status '{$newStatus}' is not a valid status value. Please run the database migration to add this status to the ENUM.");
                }
                
                // Start transaction to ensure atomic update
                $db->beginTransaction();
                
                // Update status with explicit WHERE clause
                $updateSql = "UPDATE bookings SET status = :status, updated_at = NOW() WHERE id = :booking_id";
                $updateStmt = $db->prepare($updateSql);
                
                if (!$updateStmt) {
                    $errorInfo = $db->errorInfo();
                    $db->rollBack();
                    throw new Exception('Failed to prepare UPDATE query: ' . ($errorInfo[2] ?? 'Unknown error'));
                }
                
                $result = $updateStmt->execute([
                    ':status' => $newStatus,
                    ':booking_id' => $bookingId
                ]);
                
                if (!$result) {
                    $errorInfo = $updateStmt->errorInfo();
                    $db->rollBack();
                    error_log("acceptBooking: ✗ UPDATE query execution failed");
                    error_log("acceptBooking: PDO Error Code: " . ($errorInfo[0] ?? 'N/A'));
                    error_log("acceptBooking: PDO Error Message: " . ($errorInfo[2] ?? 'N/A'));
                    throw new Exception('Failed to execute UPDATE query: ' . ($errorInfo[2] ?? 'Unknown error'));
                }
                
                $rowsAffected = $updateStmt->rowCount();
                error_log("acceptBooking: UPDATE query executed, rows affected: {$rowsAffected}");
                
                if ($rowsAffected === 0) {
                    $db->rollBack();
                    throw new Exception("No rows were updated. Booking ID {$bookingId} may not exist or status is already '{$newStatus}'. Please check the booking ID and current status.");
                }
                
                // CRITICAL: Verify the update BEFORE committing
                $verifyStmt = $db->prepare("SELECT status FROM bookings WHERE id = :booking_id");
                $verifyStmt->execute([':booking_id' => $bookingId]);
                $verifyData = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$verifyData) {
                    $db->rollBack();
                    throw new Exception("Booking ID {$bookingId} not found after update attempt.");
                }
                
                $actualStatus = $verifyData['status'] ?? null;
                
                if ($actualStatus !== $newStatus) {
                    $db->rollBack();
                    error_log("acceptBooking: ✗ CRITICAL - Status verification failed!");
                    error_log("acceptBooking: Expected status: '{$newStatus}'");
                    error_log("acceptBooking: Actual status: '{$actualStatus}'");
                    error_log("acceptBooking: This usually means the status value is not in the database ENUM or there was a constraint violation.");
                    throw new Exception("Status update verification failed. Expected '{$newStatus}' but got '{$actualStatus}'. The status may not be in the database ENUM. Please run the database migration.");
                }
                
                // Commit the transaction only if verification passed
                $db->commit();
                error_log("acceptBooking: ✓ Successfully updated booking ID {$bookingId} to status '{$newStatus}' and verified in database");
                
            } catch (PDOException $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                error_log("acceptBooking: ✗ PDOException: " . $e->getMessage());
                error_log("acceptBooking: PDO Error Info: " . print_r($e->errorInfo ?? [], true));
                error_log("acceptBooking: SQL State: " . ($e->errorInfo[0] ?? 'N/A'));
                
                // Check if it's an ENUM constraint violation
                if (isset($e->errorInfo[0]) && $e->errorInfo[0] === '22007' || 
                    (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1265)) {
                    throw new Exception("Invalid status value '{$newStatus}'. This status is not in the database ENUM. Please run the database migration: database/add_delivery_workflow_statuses.sql");
                }
                
                throw $e;
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                error_log("acceptBooking: ✗ Error: " . $e->getMessage());
                throw $e;
            }
            
            // Send notification to customer if delivery service
            if ($serviceOption === 'delivery') {
                try {
                    $customerStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                    $customerStmt->execute([$booking['user_id']]);
                    $customer = $customerStmt->fetch();
                    
                    if ($customer) {
                        require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                        $notificationService = new NotificationService();
                        
                        // Send email notification
                        $bookingDate = !empty($booking['booking_date']) ? date('F d, Y', strtotime($booking['booking_date'])) : 'your scheduled date';
                        $subject = "Booking Accepted - Bring Item to Shop - Booking #" . $bookingId;
                        $message = "Dear {$customer['fullname']},\n\n";
                        $message .= "Your booking has been accepted. Please bring your item to the shop on {$bookingDate}.\n\n";
                        $message .= "After you bring the item, our team will inspect it and send you a preview receipt with the estimated cost.\n\n";
                        $message .= "Thank you for choosing UpholCare!";
                        
                        $notificationService->sendEmail($customer['email'], $subject, $message);
                    }
                } catch (Exception $e) {
                    error_log("Error sending notification: " . $e->getMessage());
                    // Don't fail the whole request if notification fails
                }
            }
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => $statusMessage,
                'new_status' => $newStatus
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        } catch (PDOException $e) {
            ob_clean();
            $errorMessage = $e->getMessage();
            $errorInfo = $e->errorInfo ?? [];
            error_log("Error accepting booking (PDOException): " . $errorMessage);
            error_log("PDO Error Info: " . print_r($errorInfo, true));
            error_log("Stack trace: " . $e->getTraceAsString());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred: ' . (defined('APP_DEBUG') && APP_DEBUG ? $errorMessage : 'Please try again later'),
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? [
                    'message' => $errorMessage,
                    'code' => $e->getCode(),
                    'errorInfo' => $errorInfo
                ] : null
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        } catch (Exception $e) {
            ob_clean();
            $errorMessage = $e->getMessage();
            error_log("Error accepting booking (Exception): " . $errorMessage);
            error_log("Stack trace: " . $e->getTraceAsString());
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error occurred: ' . (defined('APP_DEBUG') && APP_DEBUG ? $errorMessage : 'Please try again later'),
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? [
                    'message' => $errorMessage,
                    'code' => $e->getCode()
                ] : null
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        }
    }
    
    /**
     * Accept Reservation (AJAX) - Legacy method
     */
    /**
     * Accept Reservation and Assign Booking Number
     */
    public function acceptReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        $bookingNumberId = $_POST['booking_number_id'] ?? null; // Optional - will auto-assign if not provided
        $adminNotes = $_POST['admin_notes'] ?? '';
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Start transaction to ensure atomic update
            $db->beginTransaction();
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Booking numbers removed - system now uses availability based on stock and capacity
            
            // First, set status to "approved" (admin has reviewed and approved)
            // Then, if service option is PICKUP, automatically update to "for_pickup"
            $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
            $newStatus = 'approved'; // Always start with "approved" after admin approval
            
            // Update booking: Set status based on service option (queue number already assigned)
            // Use direct SQL update to ensure status is definitely changed
            $updateData = [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Add admin notes if provided
            if ($adminNotes) {
                $updateData['notes'] = ($booking['notes'] ?? '') . "\n\n[Admin Notes: " . $adminNotes . "]";
            }
            
            // Update using direct SQL to ensure it works
            // CRITICAL: Use explicit status = 'approved' to prevent any default value issues
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            // Build update query with explicit status assignment
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            error_log("AcceptReservation - Update query: " . $updateQuery);
            error_log("AcceptReservation - Update values: " . print_r($updateValues, true));
            
            $updateStmt = $db->prepare($updateQuery);
            $updateResult = $updateStmt->execute($updateValues);
            
            error_log("AcceptReservation - Update result: " . ($updateResult ? 'SUCCESS' : 'FAILED'));
            
            // Immediately verify after update (before commit)
            $preCommitCheck = $db->prepare("SELECT status FROM bookings WHERE id = ?");
            $preCommitCheck->execute([$bookingId]);
            $preCommitStatus = $preCommitCheck->fetch();
            error_log("AcceptReservation - Pre-commit status: " . ($preCommitStatus['status'] ?? 'NULL'));
            
            // CRITICAL: Verify the update actually happened and commit transaction
            if ($updateResult) {
                // Commit transaction first to ensure changes are saved
                if ($db->inTransaction()) {
                    $db->commit();
                }
                
                // Verify the update actually happened
                $verifyStmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                $verifyStmt->execute([$bookingId]);
                $verified = $verifyStmt->fetch();
                
                if (!$verified || $verified['status'] !== 'approved') {
                    // Status didn't update, try again with explicit update
                    error_log("Status verification failed for booking ID: " . $bookingId . ". Attempting force update...");
                    $forceUpdateStmt = $db->prepare("UPDATE bookings SET status = 'approved', updated_at = NOW() WHERE id = ?");
                    $forceUpdateResult = $forceUpdateStmt->execute([$bookingId]);
                    
                    if ($forceUpdateResult) {
                        // Verify again after force update
                        $verifyStmt2 = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                        $verifyStmt2->execute([$bookingId]);
                        $verified2 = $verifyStmt2->fetch();
                        error_log("After force update, status is: " . ($verified2['status'] ?? 'NULL'));
                    } else {
                        // Log error for debugging
                        error_log("CRITICAL: Failed to update booking status to 'approved' for booking ID: " . $bookingId);
                    }
                }
            } else {
                // Update failed
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
                exit;
            }
            
            // After setting to "approved", check service option
            // For Pickup/Both: Auto-update to "for_pickup" (admin will pick up)
            // For Delivery: Update to "for_dropoff" (customer brings item to shop)
            if ($updateResult && $newStatus === 'approved') {
                $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                
                // If service option is Pick Up or Both, automatically update to "for_pickup"
                if ($serviceOption === 'pickup' || $serviceOption === 'both') {
                    $pickupUpdateStmt = $db->prepare("UPDATE bookings SET status = 'for_pickup', updated_at = NOW() WHERE id = ?");
                    $pickupUpdateResult = $pickupUpdateStmt->execute([$bookingId]);
                    
                    if ($pickupUpdateResult) {
                        $newStatus = 'for_pickup'; // Update for response
                        error_log("Auto-updated booking ID " . $bookingId . " from 'approved' to 'for_pickup' (Pick Up service)");
                    }
                } elseif ($serviceOption === 'delivery') {
                    // For Delivery: Update to 'for_dropoff' - customer must bring item to shop
                    $dropoffUpdateStmt = $db->prepare("UPDATE bookings SET status = 'for_dropoff', updated_at = NOW() WHERE id = ?");
                    $dropoffUpdateResult = $dropoffUpdateStmt->execute([$bookingId]);
                    
                    if ($dropoffUpdateResult) {
                        $newStatus = 'for_dropoff'; // Update for response
                        error_log("Auto-updated booking ID " . $bookingId . " from 'approved' to 'for_dropoff' (Delivery service)");
                    }
                }
            }
            
            // Get customer details for notification (only if update was successful)
            if ($updateResult) {
                // Get customer details for notification
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$booking['user_id']]);
                $customer = $stmt->fetch();
                
                // Queue numbers removed - calculate customers ahead based on creation date
                $customersAhead = 0;
                $stmt = $db->prepare("
                    SELECT COUNT(*) as customers_ahead
                    FROM bookings b
                    WHERE b.status IN ('pending', 'approved', 'in_queue', 'under_repair', 'for_quality_check', 'ready_for_pickup', 'out_for_delivery')
                    AND b.id != ?
                    AND b.created_at < (SELECT created_at FROM bookings WHERE id = ?)
                ");
                $stmt->execute([$bookingId, $bookingId]);
                $aheadResult = $stmt->fetch();
                $customersAhead = (int)($aheadResult['customers_ahead'] ?? 0);
                
                // Send notification to customer
                if ($customer) {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    // Send approval email with "ready for repair" message
                    $notificationService->sendReservationApprovalEmail(
                        $customer['email'],
                        $customer['fullname'],
                        'Booking #' . $bookingId, // Use booking ID instead
                        $booking,
                        0, // No queue position
                        $customersAhead
                    );
                    
                    // Create in-app notification for customer
                    // Check if related_id column exists in notifications table
                    $hasRelatedId = false;
                    try {
                        $checkStmt = $db->query("
                            SELECT COUNT(*) as cnt 
                            FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'notifications' 
                            AND COLUMN_NAME = 'related_id'
                        ");
                        $hasRelatedId = $checkStmt->fetch()['cnt'] > 0;
                    } catch (Exception $e) {
                        // Ignore error
                    }
                    
                    // Create notification with better message based on service option
                    $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                    $notificationMessage = "Great news! Your reservation has been approved by the admin.";
                    
                    if ($serviceOption === 'pickup' || $serviceOption === 'both') {
                        $notificationMessage .= " Your booking status is now 'For Pick Up'. We will pick up your item on the scheduled date. You will receive an email confirmation shortly.";
                    } elseif ($serviceOption === 'delivery') {
                        $bookingDate = !empty($booking['booking_date']) ? date('F d, Y', strtotime($booking['booking_date'])) : 'your scheduled date';
                        $notificationMessage .= " Your booking status is now 'Approved'. Please bring your item to the shop on {$bookingDate} for inspection. After inspection, you will receive a Preview Receipt with the estimated cost.";
                    } else {
                        $notificationMessage .= " Your booking status is now 'Approved'. You can now track your repair progress.";
                    }
                    
                    if ($hasRelatedId) {
                        $notifStmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                            VALUES (?, 'success', ?, ?, ?, NOW())
                        ");
                        $notifStmt->execute([
                            $customer['id'],
                            'Reservation Approved',
                            $notificationMessage,
                            $bookingId
                        ]);
                    } else {
                        $notifStmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, created_at) 
                            VALUES (?, 'success', ?, ?, NOW())
                        ");
                        $notifStmt->execute([
                            $customer['id'],
                            'Reservation Approved',
                            $notificationMessage
                        ]);
                    }
                }
                
                // Final verification - double check status was updated (after commit)
                // Wait a moment for database to fully commit
                usleep(100000); // 100ms delay to ensure commit is complete
                
                $finalCheckStmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                $finalCheckStmt->execute([$bookingId]);
                $finalStatus = $finalCheckStmt->fetch();
                
                $actualStatus = trim($finalStatus['status'] ?? '');
                error_log("Final status check for booking ID " . $bookingId . ": '" . $actualStatus . "'");
                
                // Ensure status is 'approved' - if not, force update one more time
                if (!$finalStatus || strtolower($actualStatus) !== 'approved') {
                    error_log("Final check failed for booking ID: " . $bookingId . ". Status is: '" . $actualStatus . "'. Performing final force update...");
                    
                    // Use explicit UPDATE with WHERE clause to ensure it works
                    $finalForceStmt = $db->prepare("UPDATE bookings SET status = 'approved', updated_at = NOW() WHERE id = ?");
                    $finalForceResult = $finalForceStmt->execute([$bookingId]);
                    
                    if ($finalForceResult) {
                        // Wait again for update to complete
                        usleep(100000); // 100ms delay
                        
                        // Verify one more time
                        $finalCheckStmt2 = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                        $finalCheckStmt2->execute([$bookingId]);
                        $finalStatus = $finalCheckStmt2->fetch();
                        $actualStatus2 = trim($finalStatus['status'] ?? '');
                        error_log("After final force update, status is: '" . $actualStatus2 . "'");
                        
                        // If still not approved, log critical error
                        if (strtolower($actualStatus2) !== 'approved') {
                            error_log("CRITICAL: Status still not 'approved' after force update for booking ID: " . $bookingId);
                        } else {
                            $actualStatus = $actualStatus2;
                        }
                    } else {
                        error_log("CRITICAL: Final force update failed for booking ID: " . $bookingId);
                    }
                    
                    // Ensure we return 'approved' in response even if verification fails
                    if (strtolower($actualStatus) !== 'approved') {
                        $actualStatus = 'approved';
                    }
                }
                
                // Get the final verified status
                $responseStatus = $actualStatus ?? ($finalStatus['status'] ?? 'approved');
                $responseStatus = trim($responseStatus);
                
                // Use the actual status (for_pickup for PICKUP, approved for others)
                $statusMessage = ($newStatus === 'for_pickup') 
                    ? 'Reservation approved! Status set to "For Pickup". Item will be collected for inspection. Customer notified via email.'
                    : 'Reservation approved successfully! Status changed to "Approved". Customer notified via email.';
                
                // Ensure response status matches the intended status
                if (strtolower($responseStatus) !== strtolower($newStatus)) {
                    error_log("WARNING: Response status '" . $responseStatus . "' does not match intended status '" . $newStatus . "' for booking ID " . $bookingId);
                    $responseStatus = $newStatus;
                }
                
                error_log("Sending response for booking ID " . $bookingId . " with status: '" . $responseStatus . "'");
                
                echo json_encode([
                    'success' => true,
                    'message' => $statusMessage,
                    'booking_id' => $bookingId,
                    'customers_ahead' => $customersAhead,
                    'status' => $responseStatus // Return actual status
                ]);
            } else {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to accept reservation']);
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Save Calculated Total Payment (Bayronon)
     * Admin calculates total after examining item and measuring fabric
     */
    public function saveCalculatedTotal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        $fabricLength = $_POST['fabric_length'] ?? null;
        $fabricWidth = $_POST['fabric_width'] ?? null;
        $fabricArea = $_POST['fabric_area'] ?? null;
        $fabricCostPerMeter = $_POST['fabric_cost_per_meter'] ?? null;
        $fabricTotal = $_POST['fabric_total'] ?? null;
        $laborFee = $_POST['labor_fee'] ?? null;
        $materialCost = $_POST['material_cost'] ?? 0;
        $serviceFees = $_POST['service_fees'] ?? 0;
        $totalAmount = $_POST['total_amount'] ?? null;
        $calculationNotes = $_POST['calculation_notes'] ?? '';
        
        if (!$bookingId || !$totalAmount) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID and total amount are required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Prepare update data
            $updateData = [
                'fabric_length' => $fabricLength,
                'fabric_width' => $fabricWidth,
                'fabric_area' => $fabricArea,
                'fabric_cost_per_meter' => $fabricCostPerMeter,
                'fabric_total' => $fabricTotal,
                'labor_fee' => $laborFee,
                'material_cost' => $materialCost,
                'service_fees' => $serviceFees,
                'total_amount' => $totalAmount,
                'calculated_total_saved' => '1',
                'calculation_notes' => $calculationNotes,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Build update query
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                // Check if column exists before adding to update
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Calculated total saved successfully. You can now approve the booking.',
                    'total_amount' => $totalAmount
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save calculated total']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Old acceptReservation method - replaced above
     */
    public function acceptReservation_OLD() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        // Get booking details with customer information
        $booking = $this->getBookingWithCustomerDetails($bookingId);
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            return;
        }
        
        if ($booking['status'] !== 'pending') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Only pending reservations can be accepted']);
            return;
        }
        
        // Update status to confirmed
        $result = $this->bookingModel->update($bookingId, [
            'status' => 'confirmed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            // Send approval notification email
            $this->sendApprovalNotification($booking);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Reservation accepted successfully. Customer has been notified.',
                'new_status' => 'confirmed'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to accept reservation']);
        }
    }
    
    /**
     * Reject Reservation (AJAX)
     */
    public function rejectReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $reason = $_POST['reason'] ?? 'No reason provided';
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        // Get booking details with customer information
        $booking = $this->getBookingWithCustomerDetails($bookingId);
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            return;
        }
        
        if ($booking['status'] !== 'pending') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Only pending reservations can be rejected']);
            return;
        }
        
        // Update status to rejected and add rejection reason
        $result = $this->bookingModel->update($bookingId, [
            'status' => 'rejected',
            'notes' => $booking['notes'] ? $booking['notes'] . "\n\nRejection Reason: " . $reason : "Rejection Reason: " . $reason,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            // Send rejection notification email
            $this->sendRejectionNotification($booking, $reason);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Reservation rejected successfully. Customer has been notified.',
                'new_status' => 'rejected'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to reject reservation']);
        }
    }
    
    /**
     * Get booking with customer details for notifications
     */
    private function getBookingWithCustomerDetails($bookingId) {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email as customer_email, u.phone
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetch();
    }
    
    /**
     * Send approval notification
     */
    private function sendApprovalNotification($booking) {
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $result = $notificationService->sendReservationApproval(
                $booking['customer_email'],
                $booking['customer_name'],
                $booking
            );
            
            if (!$result) {
                error_log("Failed to send approval notification for booking ID: " . $booking['id']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error sending approval notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send rejection notification
     */
    private function sendRejectionNotification($booking, $reason) {
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $result = $notificationService->sendReservationRejection(
                $booking['customer_email'],
                $booking['customer_name'],
                $booking,
                $reason
            );
            
            if (!$result) {
                error_log("Failed to send rejection notification for booking ID: " . $booking['id']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error sending rejection notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get Pending Reservations (AJAX)
     */
    public function getPendingReservations() {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.status = 'pending'
                ORDER BY b.created_at ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $reservations = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'reservations' => $reservations,
            'count' => count($reservations)
        ]);
    }
    
    /**
     * Get Booking Numbers (AJAX)
     */
    public function getBookingNumbers() {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Booking numbers removed - return empty array
            $bookingNumbers = [];
            
            echo json_encode([
                'success' => true,
                'bookingNumbers' => $bookingNumbers
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading booking numbers: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get Customers (AJAX)
     */
    public function getCustomers() {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT id, fullname, email, phone FROM users WHERE role = 'customer' ORDER BY fullname ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $customers = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading customers: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Assign Booking Number to Customer (AJAX)
     */
    public function assignBookingNumber() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingNumberId = $_POST['booking_number_id'] ?? null;
        $customerId = $_POST['customer_id'] ?? null;
        
        if (!$bookingNumberId || !$customerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking number ID and customer ID are required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Check if customer already has an active booking number
            $stmt = $db->prepare("SELECT id FROM customer_booking_numbers WHERE customer_id = ? AND status = 'active'");
            $stmt->execute([$customerId]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Customer already has an active booking number']);
                return;
            }
            
            // Get booking number details
            $stmt = $db->prepare("SELECT booking_number FROM booking_numbers WHERE id = ?");
            $stmt->execute([$bookingNumberId]);
            $bookingNumberData = $stmt->fetch();
            
            if (!$bookingNumberData) {
                echo json_encode(['success' => false, 'message' => 'Booking number not found']);
                return;
            }
            
            // Get customer information
            $stmt = $db->prepare("SELECT fullname, email FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
                return;
            }
            
            // Assign booking number to customer
            $stmt = $db->prepare("
                INSERT INTO customer_booking_numbers 
                (customer_id, booking_number_id, assigned_by_admin_id, status, assigned_at) 
                VALUES (?, ?, ?, 'active', NOW())
            ");
            
            $adminId = $this->currentUser()['id'];
            $result = $stmt->execute([$customerId, $bookingNumberId, $adminId]);
            
            if ($result) {
                // Get the inserted ID
                $customerBookingNumberId = $db->lastInsertId();
                
                // Get the assigned_at timestamp for this assignment
                $stmt = $db->prepare("SELECT assigned_at FROM customer_booking_numbers WHERE id = ?");
                $stmt->execute([$customerBookingNumberId]);
                $assignmentData = $stmt->fetch();
                $assignedAt = $assignmentData['assigned_at'] ?? date('Y-m-d H:i:s');
                
                // Calculate queue position (line number)
                // Count how many customers with active booking numbers were assigned before or at the same time
                // This determines their position in the queue
                $stmt = $db->prepare("
                    SELECT COUNT(*) + 1 as queue_position
                    FROM customer_booking_numbers
                    WHERE status = 'active' 
                    AND (
                        assigned_at < ? 
                        OR (assigned_at = ? AND id < ?)
                    )
                ");
                $stmt->execute([$assignedAt, $assignedAt, $customerBookingNumberId]);
                $queueResult = $stmt->fetch();
                $queuePosition = (int)($queueResult['queue_position'] ?? 1);
                
                // Calculate customers ahead
                $customersAhead = max(0, $queuePosition - 1);
                
                // Send email notification with queue position and customers ahead
                require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                $notificationService = new NotificationService();
                $emailSent = $notificationService->sendBookingNumberAssignmentEmail(
                    $customer['email'],
                    $customer['fullname'],
                    $bookingNumberData['booking_number'],
                    $queuePosition,
                    $customersAhead
                );
                
                $message = 'Booking number assigned successfully';
                if ($emailSent) {
                    $message .= ' and email notification sent';
                } else {
                    $message .= ' (email notification failed)';
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => $message,
                    'queue_position' => $queuePosition,
                    'booking_number' => $bookingNumberData['booking_number']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to assign booking number']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Delete Booking Number (AJAX)
     */
    public function deleteBookingNumber() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingNumberId = $_POST['booking_number_id'] ?? null;
        
        if (!$bookingNumberId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking number ID is required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Check if booking number is assigned to any customer
            $stmt = $db->prepare("SELECT id FROM customer_booking_numbers WHERE booking_number_id = ? AND status = 'active'");
            $stmt->execute([$bookingNumberId]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete booking number that is assigned to a customer']);
                return;
            }
            
            // Delete booking number
            $stmt = $db->prepare("DELETE FROM booking_numbers WHERE id = ?");
            $result = $stmt->execute([$bookingNumberId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Booking number deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete booking number']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Get Workers/Technicians (AJAX)
     */
    public function getWorkers() {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get all users with role 'admin' or 'technician' or 'worker'
            $stmt = $db->prepare("
                SELECT id, fullname, email, role 
                FROM users 
                WHERE role IN ('admin', 'technician', 'worker') 
                ORDER BY fullname ASC
            ");
            $stmt->execute();
            $workers = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'workers' => $workers
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading workers: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Send Receipt to Customer (AJAX)
     * Called when admin views/generates a receipt - automatically sends it to customer
     */
    public function sendReceiptToCustomer() {
        // Start output buffering to prevent any unwanted output
        ob_start();
        
        // Set headers first - MUST be before any output
        if (!headers_sent()) {
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
        
        // Check request method - also check for X-HTTP-Method-Override header (for compatibility)
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $overrideMethod = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
        $xRequestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'not set';
        
        // Log request details for debugging
        error_log("sendReceiptToCustomer called - Method: " . $requestMethod . ", URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . ", X-Requested-With: " . $xRequestedWith);
        error_log("POST data: " . print_r($_POST, true));
        error_log("php://input: " . file_get_contents('php://input'));
        
        // Allow POST or method override
        if ($requestMethod !== 'POST' && $overrideMethod !== 'POST') {
            error_log("sendReceiptToCustomer: Method not allowed - Expected POST, got " . $requestMethod);
            ob_clean();
            http_response_code(405);
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed. Expected POST, got ' . $requestMethod,
                'debug' => [
                    'request_method' => $requestMethod,
                    'http_method_override' => $overrideMethod,
                    'x_requested_with' => $xRequestedWith,
                    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
                    'has_post_data' => !empty($_POST),
                    'has_input_data' => !empty(file_get_contents('php://input'))
                ]
            ]);
            ob_end_flush();
            exit;
        }
        
        try {
            // Handle both JSON and FormData
            $bookingId = null;
            
            // First check $_POST (for FormData)
            if (!empty($_POST['booking_id'])) {
                $bookingId = $_POST['booking_id'];
            } else {
                // Try JSON input
                $rawInput = file_get_contents('php://input');
                if (!empty($rawInput)) {
                    $input = json_decode($rawInput, true);
                    if (!empty($input) && isset($input['booking_id'])) {
                        $bookingId = $input['booking_id'];
                    }
                }
            }
            
            // Final fallback to GET (shouldn't happen for POST requests)
            if (empty($bookingId)) {
                $bookingId = $_GET['booking_id'] ?? null;
            }
            
            if (empty($bookingId) || !is_numeric($bookingId)) {
                ob_clean();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check current status - DO NOT change status yet, wait for customer approval
            $checkStmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
            $checkStmt->execute([$bookingId]);
            $currentBooking = $checkStmt->fetch(PDO::FETCH_ASSOC);
            $currentStatus = $currentBooking['status'] ?? '';
            
            // Get booking details to check service option
            $bookingStmt = $db->prepare("SELECT service_option FROM bookings WHERE id = ?");
            $bookingStmt->execute([$bookingId]);
            $bookingData = $bookingStmt->fetch(PDO::FETCH_ASSOC);
            $serviceOption = strtolower(trim($bookingData['service_option'] ?? ''));
            
            // STRICT STATUS FLOW: Only send receipt if in inspection-related status
            // For Delivery: Change status to 'inspection_completed_waiting_approval' - customer must approve
            // For Other: Change status to 'inspect_completed' - customer must approve
            $newStatus = $currentStatus; // Default to current status
            
            // Validate status transition - only allow from inspection statuses
            $allowedFromStatuses = ['to_inspect', 'for_inspection', 'picked_up', 'for_inspect'];
            if (!in_array($currentStatus, $allowedFromStatuses)) {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Cannot send receipt. Booking must be in inspection status. Current status: ' . $currentStatus
                ]);
                ob_end_flush();
                exit;
            }
            
            // Check if quotation_sent column exists before updating
            $checkColumn = $db->query("SHOW COLUMNS FROM bookings LIKE 'quotation_sent'");
            $columnExists = $checkColumn->fetch();
            
            // Check if inspection_completed_waiting_approval status exists in ENUM
            $checkStatusEnum = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
            $statusColumn = $checkStatusEnum->fetch(PDO::FETCH_ASSOC);
            $hasInspectionCompletedWaitingApproval = strpos($statusColumn['Type'] ?? '', 'inspection_completed_waiting_approval') !== false;
            $hasInspectCompleted = strpos($statusColumn['Type'] ?? '', 'inspect_completed') !== false;
            
            // For Delivery service: Use 'inspection_completed_waiting_approval'
            // For Other services: Use 'inspect_completed' or fallback
            if ($serviceOption === 'delivery' && $hasInspectionCompletedWaitingApproval) {
                // Delivery service: Set status to 'inspection_completed_waiting_approval'
                if ($columnExists) {
                    $updateStmt = $db->prepare("UPDATE bookings SET status = 'inspection_completed_waiting_approval', quotation_sent = 1, quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                } else {
                    $updateStmt = $db->prepare("UPDATE bookings SET status = 'inspection_completed_waiting_approval', quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                }
                $updateStmt->execute([$bookingId]);
                $newStatus = 'inspection_completed_waiting_approval';
            } elseif ($hasInspectCompleted) {
                // Other services: Use 'inspect_completed'
                if ($columnExists) {
                    $updateStmt = $db->prepare("UPDATE bookings SET status = 'inspect_completed', quotation_sent = 1, quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                } else {
                    $updateStmt = $db->prepare("UPDATE bookings SET status = 'inspect_completed', quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                }
                $updateStmt->execute([$bookingId]);
                $newStatus = 'inspect_completed';
            } elseif ($columnExists) {
                // Fallback: If statuses don't exist, use preview_receipt_sent (backward compatibility)
                $checkPreviewStatus = strpos($statusColumn['Type'] ?? '', 'preview_receipt_sent') !== false;
                if ($checkPreviewStatus) {
                    $updateStmt = $db->prepare("UPDATE bookings SET status = 'preview_receipt_sent', quotation_sent = 1, quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                    $updateStmt->execute([$bookingId]);
                    $newStatus = 'preview_receipt_sent';
                } else {
                    // If neither status exists, mark receipt as sent but keep current status
                    $updateStmt = $db->prepare("UPDATE bookings SET quotation_sent = 1, quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                    $updateStmt->execute([$bookingId]);
                    $newStatus = $currentStatus; // Keep current status
                }
            } else {
                // Fallback: only update quotation_sent_at if quotation_sent column doesn't exist
                $updateStmt = $db->prepare("UPDATE bookings SET quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$bookingId]);
                $newStatus = $currentStatus; // Keep current status
            }
            
            // Use the existing sendReceiptNotification method
            try {
                $result = $this->sendReceiptNotification($bookingId);
                
                if ($result) {
                    ob_clean();
                    $statusMessage = $serviceOption === 'delivery' 
                        ? 'Preview receipt sent to customer successfully. Status changed to "Inspection Completed - Waiting for Customer Approval". Customer will receive a notification and can approve the receipt to proceed with repair.'
                        : 'Preview receipt sent to customer successfully. Status changed to "Inspect Completed". Customer will receive a notification and can approve the receipt to proceed with repair.';
                    echo json_encode([
                        'success' => true,
                        'message' => $statusMessage,
                        'status' => $newStatus, // Return the new status
                        'previous_status' => $currentStatus
                    ]);
                    ob_end_flush();
                } else {
                    ob_clean();
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to send receipt notification to customer. Please check error logs for details.'
                    ]);
                    ob_end_flush();
                }
            } catch (Exception $notificationError) {
                error_log("Error in sendReceiptNotification: " . $notificationError->getMessage());
                error_log("Stack trace: " . $notificationError->getTraceAsString());
                ob_clean();
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'An error occurred while sending receipt notification',
                    'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $notificationError->getMessage() : null
                ]);
                ob_end_flush();
            }
        } catch (Exception $e) {
            error_log("Error in sendReceiptToCustomer: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            ob_clean();
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while sending receipt',
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ]);
            ob_end_flush();
        }
        exit;
    }
    
    /**
     * Save Receipt (AJAX)
     * Saves leather details, meters, price per meter, and labor fee
     */
    public function saveReceipt() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = $input['booking_id'] ?? $_POST['booking_id'] ?? null;
            $leatherQuality = $input['leather_quality'] ?? $_POST['leather_quality'] ?? 'standard';
            $leatherColorId = $input['leather_color_id'] ?? $_POST['leather_color_id'] ?? null;
            $numberOfMeters = floatval($input['number_of_meters'] ?? $_POST['number_of_meters'] ?? 0);
            $pricePerMeter = floatval($input['price_per_meter'] ?? $_POST['price_per_meter'] ?? 0);
            $laborFee = floatval($input['labor_fee'] ?? $_POST['labor_fee'] ?? 0);
            $repairDays = intval($input['repair_days'] ?? $_POST['repair_days'] ?? 0);
            
            // Material fields from Materials tab
            $fabricType = $input['fabric_type'] ?? $_POST['fabric_type'] ?? null;
            $meters = floatval($input['meters'] ?? $_POST['meters'] ?? 0);
            $foamReplacement = $input['foam_replacement'] ?? $_POST['foam_replacement'] ?? null;
            $foamThickness = floatval($input['foam_thickness'] ?? $_POST['foam_thickness'] ?? 0);
            $accessories = $input['accessories'] ?? $_POST['accessories'] ?? []; // Array of selected accessories
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                exit;
            }
            
            if ($numberOfMeters <= 0 || $pricePerMeter <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Number of meters and price per meter must be greater than 0']);
                exit;
            }
            
            if ($repairDays <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Repair days must be greater than 0']);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Get color price from inventory if color is selected
            $colorPrice = 0;
            if ($leatherColorId) {
                // Check which price columns exist in inventory table
                $checkPricePerMeter = $db->query("SHOW COLUMNS FROM inventory LIKE 'price_per_meter'");
                $hasPricePerMeter = $checkPricePerMeter->rowCount() > 0;
                $checkPricePerUnit = $db->query("SHOW COLUMNS FROM inventory LIKE 'price_per_unit'");
                $hasPricePerUnit = $checkPricePerUnit->rowCount() > 0;
                
                // Build query based on which columns exist
                if ($hasPricePerMeter && $hasPricePerUnit) {
                    $priceQuery = "SELECT COALESCE(NULLIF(price_per_meter, 0), price_per_unit, 0) as price FROM inventory WHERE id = ?";
                } elseif ($hasPricePerMeter) {
                    $priceQuery = "SELECT COALESCE(price_per_meter, 0) as price FROM inventory WHERE id = ?";
                } elseif ($hasPricePerUnit) {
                    $priceQuery = "SELECT COALESCE(price_per_unit, 0) as price FROM inventory WHERE id = ?";
                } else {
                    $priceQuery = "SELECT 0 as price FROM inventory WHERE id = ?";
                }
                
                $inventoryStmt = $db->prepare($priceQuery);
                $inventoryStmt->execute([$leatherColorId]);
                $inventoryData = $inventoryStmt->fetch(PDO::FETCH_ASSOC);
                $colorPrice = floatval($inventoryData['price'] ?? 0);
            }
            
            // Calculate totals
            $fabricCost = $numberOfMeters * $pricePerMeter;
            $grandTotal = $fabricCost + $colorPrice + $laborFee;
            
            // Get leather color name for notes
            $leatherColorName = '';
            if ($leatherColorId) {
                $inventoryModel = $this->model('Inventory');
                $color = $inventoryModel->getColorById($leatherColorId);
                $leatherColorName = $color ? $color['color_name'] : 'Selected Color';
            }
            
            // Check which columns exist in bookings table
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            // Update booking with receipt data
            // Use existing columns: fabric_cost_per_meter, fabric_total (for total fabric cost), labor_fee, grand_total
            $updateData = [];
            
            // Only update selected_color_id if column exists and color is provided
            if (in_array('selected_color_id', $existingColumns) && $leatherColorId) {
                $updateData['selected_color_id'] = $leatherColorId;
            }
            
            // Save quality - check which column exists (color_type, fabric_type, or leather_type)
            if (in_array('color_type', $existingColumns)) {
                $updateData['color_type'] = $leatherQuality;
            } elseif (in_array('fabric_type', $existingColumns)) {
                $updateData['fabric_type'] = $leatherQuality;
            } elseif (in_array('leather_type', $existingColumns)) {
                $updateData['leather_type'] = $leatherQuality;
            }
            
            // Add pricing fields if they exist
            if (in_array('fabric_cost_per_meter', $existingColumns)) {
                $updateData['fabric_cost_per_meter'] = $pricePerMeter;
            }
            if (in_array('fabric_total', $existingColumns)) {
                $updateData['fabric_total'] = $fabricCost; // Total fabric cost (meters × price)
            }
            if (in_array('fabric_cost', $existingColumns)) {
                $updateData['fabric_cost'] = $fabricCost;
            }
            if (in_array('labor_fee', $existingColumns)) {
                $updateData['labor_fee'] = $laborFee;
            }
            if (in_array('grand_total', $existingColumns)) {
                $updateData['grand_total'] = $grandTotal;
            }
            if (in_array('total_amount', $existingColumns)) {
                $updateData['total_amount'] = $grandTotal;
            }
            if (in_array('color_price', $existingColumns)) {
                $updateData['color_price'] = $colorPrice;
            }
            if (in_array('number_of_meters', $existingColumns)) {
                $updateData['number_of_meters'] = $numberOfMeters;
            }
            if (in_array('price_per_meter', $existingColumns)) {
                $updateData['price_per_meter'] = $pricePerMeter;
            }
            if (in_array('calculation_notes', $existingColumns)) {
                $updateData['calculation_notes'] = "Receipt Details - Quality: " . ucfirst($leatherQuality) . ", Leather: {$leatherColorName}, Meters: {$numberOfMeters}, Price per Meter: ₱{$pricePerMeter}, Color Price: ₱{$colorPrice}, Leather Cost: ₱{$fabricCost}, Labor Fee: ₱{$laborFee}, Grand Total: ₱{$grandTotal}";
            }
            
            // Save repair_days if column exists
            if (in_array('repair_days', $existingColumns)) {
                $updateData['repair_days'] = $repairDays;
            }
            
            // Save material fields if columns exist
            if (in_array('fabric_type', $existingColumns) && $fabricType) {
                $updateData['fabric_type'] = $fabricType;
            }
            if (in_array('meters', $existingColumns) && $meters > 0) {
                $updateData['meters'] = $meters;
            }
            if (in_array('foam_replacement', $existingColumns) && $foamReplacement) {
                $updateData['foam_replacement'] = $foamReplacement;
            }
            if (in_array('foam_thickness', $existingColumns) && $foamThickness > 0) {
                $updateData['foam_thickness'] = $foamThickness;
            }
            if (in_array('accessories', $existingColumns) && is_array($accessories) && !empty($accessories)) {
                $updateData['accessories'] = json_encode($accessories); // Store as JSON
            }
            
            // Always update updated_at if column exists
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            // Build update query only with existing columns
            if (empty($updateData)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No valid columns found to update']);
                exit;
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Receipt saved successfully',
                    'grand_total' => $grandTotal,
                    'fabric_cost' => $fabricCost,
                    'labor_fee' => $laborFee
                ]);
            } else {
                $errorInfo = $updateStmt->errorInfo();
                error_log("Failed to save receipt - SQL Error: " . print_r($errorInfo, true));
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to save receipt. Database error occurred.',
                    'error' => $errorInfo[2] ?? 'Unknown database error'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error in saveReceipt: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while saving receipt: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get Receipt Preview Data (AJAX)
     * Returns JSON data for receipt preview modal
     */
    public function getReceiptPreview($bookingId) {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get complete booking details with all inspection data
            $sql = "SELECT b.*, 
                    s.service_name, 
                    s.service_type, 
                    sc.category_name,
                    u.fullname as customer_name, 
                    u.email as customer_email, 
                    u.phone as customer_phone,
                    inv.color_name,
                    inv.color_code,
                    b.color_type,
                    admin.fullname as admin_name
                    FROM bookings b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN service_categories sc ON s.category_id = sc.id
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN inventory inv ON b.selected_color_id = inv.id
                    LEFT JOIN users admin ON admin.role = 'admin' AND admin.status = 'active'
                    WHERE b.id = ?
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Check if booking is paid
            $paymentStatus = strtolower(trim($booking['payment_status'] ?? 'unpaid'));
            $isPaid = in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']) || 
                     in_array(strtolower($booking['status'] ?? ''), ['delivered_and_paid', 'completed']);
            
            if (!$isPaid) {
                echo json_encode(['success' => false, 'message' => 'Official receipt can only be generated for paid bookings']);
                exit;
            }
            
            // Calculate amounts
            $laborFee = floatval($booking['labor_fee'] ?? 0);
            $pickupFee = floatval($booking['pickup_fee'] ?? 0);
            $deliveryFee = floatval($booking['delivery_fee'] ?? 0);
            $gasFee = floatval($booking['gas_fee'] ?? 0);
            $travelFee = floatval($booking['travel_fee'] ?? 0);
            $inspectionFee = floatval($booking['inspection_fee'] ?? 0);
            $colorPrice = floatval($booking['color_price'] ?? 0);
            $numberOfMeters = floatval($booking['number_of_meters'] ?? $booking['meters'] ?? 0);
            $pricePerMeter = floatval($booking['price_per_meter'] ?? $booking['fabric_cost_per_meter'] ?? 0);
            $fabricCost = $numberOfMeters * $pricePerMeter;
            if ($fabricCost == 0 && $colorPrice > 0) {
                $fabricCost = $colorPrice;
            }
            
            // Calculate totals
            $subtotal = $laborFee + $fabricCost;
            $totalAdditionalFees = $pickupFee + $deliveryFee + $gasFee + $travelFee + $inspectionFee;
            $totalAmount = $subtotal + $totalAdditionalFees;
            $discount = floatval($booking['discount'] ?? 0);
            $totalPaid = $totalAmount - $discount;
            
            // Generate Official Receipt Number
            $receiptNumber = 'OR-' . date('Ymd') . '-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT);
            
            // Get current admin name
            $adminName = $_SESSION['user']['fullname'] ?? 'Admin';
            
            // Payment mode
            $paymentMode = 'Cash';
            if ($paymentStatus === 'paid_on_delivery_cod') {
                $paymentMode = 'Cash on Delivery (COD)';
            } elseif (strpos(strtolower($paymentStatus), 'gcash') !== false || strpos(strtolower($paymentStatus), 'bank') !== false) {
                $paymentMode = 'GCash / Bank Transfer';
            }
            
            // Dates
            $completionDate = !empty($booking['completed_at']) ? date('M d, Y', strtotime($booking['completed_at'])) : 
                             (!empty($booking['updated_at']) ? date('M d, Y', strtotime($booking['updated_at'])) : date('M d, Y'));
            $deliveryDate = !empty($booking['delivered_at']) ? date('M d, Y', strtotime($booking['delivered_at'])) : $completionDate;
            $paymentDate = !empty($booking['payment_date']) ? date('F d, Y', strtotime($booking['payment_date'])) : 
                          (!empty($booking['updated_at']) ? date('F d, Y', strtotime($booking['updated_at'])) : date('F d, Y'));
            $paymentTime = !empty($booking['payment_date']) ? date('g:i A', strtotime($booking['payment_date'])) : 
                          (!empty($booking['updated_at']) ? date('g:i A', strtotime($booking['updated_at'])) : date('g:i A'));
            
            // Item details
            $itemName = $booking['item_description'] ?? $booking['service_name'] ?? 'N/A';
            $itemIssue = $booking['damage_description'] ?? 'N/A';
            $materialUsed = '';
            if (!empty($booking['color_name'])) {
                $colorType = ($booking['color_type'] === 'premium') ? 'Premium' : 'Standard';
                $materialUsed = $booking['color_name'] . ' (' . $colorType . ')';
            }
            if (!empty($booking['foam_type'])) {
                $materialUsed .= ($materialUsed ? ' / ' : '') . 'Foam: ' . $booking['foam_type'];
            }
            if (empty($materialUsed)) {
                $materialUsed = 'N/A';
            }
            
            // Measurements
            $measurement = '';
            if (!empty($booking['measurement_height']) || !empty($booking['measurement_width'])) {
                $height = $booking['measurement_height'] ?? 0;
                $width = $booking['measurement_width'] ?? 0;
                $thickness = $booking['measurement_thickness'] ?? 0;
                if ($thickness > 0) {
                    $measurement = $height . ' x ' . $width . ' x ' . $thickness . ' inches';
                } else {
                    $measurement = $height . ' x ' . $width . ' inches';
                }
            } else {
                $measurement = $booking['measurement_custom'] ?? 'N/A';
            }
            
            // Service option
            $serviceOption = ucfirst(strtolower($booking['service_option'] ?? 'pickup'));
            if ($serviceOption === 'Both') {
                $serviceOption = 'Both (Pickup & Delivery)';
            }
            
            // Inspected by
            $inspectedBy = $adminName;
            if (!empty($booking['inspected_by'])) {
                $inspectedBy = $booking['inspected_by'];
            }
            
            // Return receipt data
            echo json_encode([
                'success' => true,
                'receipt' => [
                    'receiptNumber' => $receiptNumber,
                    'dateIssued' => date('F d, Y'),
                    'booking' => [
                        'id' => $booking['id'],
                        'bookingNumber' => '#' . $booking['id'],
                        'serviceName' => $booking['service_name'] ?? 'N/A',
                        'categoryName' => $booking['category_name'] ?? 'N/A',
                        'serviceOption' => $serviceOption,
                        'completionDate' => $completionDate,
                        'deliveryDate' => $deliveryDate,
                        'inspectedBy' => $inspectedBy
                    ],
                    'customer' => [
                        'name' => $booking['customer_name'] ?? 'N/A',
                        'email' => $booking['customer_email'] ?? 'N/A',
                        'phone' => $booking['customer_phone'] ?? 'N/A',
                        'address' => $booking['delivery_address'] ?? $booking['pickup_address'] ?? 'N/A'
                    ],
                    'item' => [
                        'name' => $itemName,
                        'issue' => $itemIssue,
                        'materialUsed' => $materialUsed,
                        'measurement' => $measurement,
                        'quantity' => 1
                    ],
                    'payment' => [
                        'laborFee' => $laborFee,
                        'fabricCost' => $fabricCost,
                        'foamCost' => floatval($booking['foam_cost'] ?? 0),
                        'miscMaterialsCost' => floatval($booking['misc_materials_cost'] ?? 0),
                        'pickupFee' => $pickupFee,
                        'deliveryFee' => $deliveryFee,
                        'gasFee' => $gasFee,
                        'travelFee' => $travelFee,
                        'inspectionFee' => $inspectionFee,
                        'subtotal' => $subtotal,
                        'totalAdditionalFees' => $totalAdditionalFees,
                        'discount' => $discount,
                        'totalAmount' => $totalAmount,
                        'totalPaid' => $totalPaid,
                        'balance' => 0,
                        'mode' => $paymentMode,
                        'referenceNumber' => $booking['payment_ref_number'] ?? '',
                        'paymentDate' => $paymentDate,
                        'paymentTime' => $paymentTime
                    ],
                    'admin' => [
                        'name' => $adminName
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            error_log("Error getting receipt preview: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Generate Official Receipt
     * Generates a complete official receipt for paid bookings
     */
    public function generateOfficialReceipt($bookingId) {
        ob_start();
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get complete booking details
            $sql = "SELECT b.*, 
                    s.service_name, 
                    s.service_type, 
                    sc.category_name,
                    u.fullname as customer_name, 
                    u.email as customer_email, 
                    u.phone as customer_phone,
                    inv.color_name,
                    inv.color_code,
                    b.color_type,
                    admin.fullname as admin_name
                    FROM bookings b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN service_categories sc ON s.category_id = sc.id
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN inventory inv ON b.selected_color_id = inv.id
                    LEFT JOIN users admin ON admin.role = 'admin' AND admin.status = 'active'
                    WHERE b.id = ?
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                $_SESSION['error'] = 'Booking not found';
                $this->redirect('admin/allBookings');
                return;
            }
            
            // Check if booking is paid
            $paymentStatus = strtolower(trim($booking['payment_status'] ?? 'unpaid'));
            $isPaid = in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']) || 
                     in_array(strtolower($booking['status'] ?? ''), ['delivered_and_paid', 'completed']);
            
            if (!$isPaid) {
                $_SESSION['error'] = 'Official receipt can only be generated for paid bookings';
                $this->redirect('admin/allBookings');
                return;
            }
            
            // Calculate amounts
            $laborFee = floatval($booking['labor_fee'] ?? 0);
            $pickupFee = floatval($booking['pickup_fee'] ?? 0);
            $deliveryFee = floatval($booking['delivery_fee'] ?? 0);
            $gasFee = floatval($booking['gas_fee'] ?? 0);
            $travelFee = floatval($booking['travel_fee'] ?? 0);
            $colorPrice = floatval($booking['color_price'] ?? 0);
            $numberOfMeters = floatval($booking['number_of_meters'] ?? $booking['meters'] ?? 0);
            $pricePerMeter = floatval($booking['price_per_meter'] ?? $booking['fabric_cost_per_meter'] ?? 0);
            $fabricCost = $numberOfMeters * $pricePerMeter;
            if ($fabricCost == 0 && $colorPrice > 0) {
                $fabricCost = $colorPrice;
            }
            
            // Calculate subtotal
            $subtotal = $fabricCost + $laborFee + $pickupFee + $deliveryFee + $gasFee + $travelFee;
            
            // Generate Official Receipt Number (OR-000XXX format)
            $receiptNumber = 'OR-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT);
            
            // Get current admin name
            $adminName = $_SESSION['user']['fullname'] ?? 'Admin';
            
            // Payment mode
            $paymentMode = 'Cash';
            if ($paymentStatus === 'paid_on_delivery_cod') {
                $paymentMode = 'Cash on Delivery (COD)';
            } elseif (strpos(strtolower($paymentStatus), 'gcash') !== false || strpos(strtolower($paymentStatus), 'bank') !== false) {
                $paymentMode = 'GCash / Bank Transfer';
            }
            
            // Payment date/time
            $paymentDate = !empty($booking['updated_at']) ? $booking['updated_at'] : date('Y-m-d H:i:s');
            $paymentDateFormatted = date('F d, Y', strtotime($paymentDate));
            $paymentTimeFormatted = date('g:i A', strtotime($paymentDate));
            
            // Generate receipt HTML
            $receiptHtml = $this->generateOfficialReceiptHTML($booking, $receiptNumber, $adminName, [
                'laborFee' => $laborFee,
                'pickupFee' => $pickupFee,
                'deliveryFee' => $deliveryFee,
                'gasFee' => $gasFee,
                'travelFee' => $travelFee,
                'fabricCost' => $fabricCost,
                'numberOfMeters' => $numberOfMeters,
                'pricePerMeter' => $pricePerMeter,
                'colorName' => $booking['color_name'] ?? 'N/A',
                'colorCode' => $booking['color_code'] ?? 'N/A',
                'colorType' => $booking['color_type'] ?? 'standard',
                'subtotal' => $subtotal,
                'totalAmount' => $subtotal,
                'totalPaid' => $subtotal,
                'balance' => 0,
                'paymentMode' => $paymentMode,
                'paymentDate' => $paymentDateFormatted,
                'paymentTime' => $paymentTimeFormatted
            ]);
            
            // Output receipt
            header('Content-Type: text/html; charset=UTF-8');
            echo $receiptHtml;
            exit;
            
        } catch (Exception $e) {
            ob_clean();
            error_log("Error generating official receipt: " . $e->getMessage());
            $_SESSION['error'] = 'Error generating receipt: ' . $e->getMessage();
            $this->redirect('admin/allBookings');
        }
    }
    
    /**
     * Generate Official Receipt HTML
     * Creates the complete official receipt HTML with all required sections
     */
    private function generateOfficialReceiptHTML($booking, $receiptNumber, $adminName, $amounts) {
        $businessName = 'UpholCare Upholstery Services';
        // TODO: Update these with actual business information
        $businessAddress = 'Complete Address'; // Update with actual business address
        $contactNumber = 'Contact Number'; // Update with actual contact number
        $email = 'Email'; // Update with actual business email
        $tinNumber = 'TIN Number'; // Update with actual TIN number
        $birPermit = 'BIR Permit Number'; // Update with actual BIR Permit number
        
        $dateIssued = date('F d, Y');
        
        // Build item/service details table
        $itemsHtml = '';
        $itemCount = 0;
        
        // Labor Fee
        if ($amounts['laborFee'] > 0) {
            $itemCount++;
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>' . htmlspecialchars($booking['service_name'] ?? 'Upholstery Service') . ' – Labor</td>';
            $itemsHtml .= '<td style="text-align: center;">1</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['laborFee'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['laborFee'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        
        // Fabric/Color
        if ($amounts['fabricCost'] > 0) {
            $itemCount++;
            $colorTypeText = ($amounts['colorType'] === 'premium') ? 'Premium' : 'Standard';
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>Selected Fabric (Color: ' . htmlspecialchars($amounts['colorName']) . ', Code: ' . htmlspecialchars($amounts['colorCode']) . ', ' . $colorTypeText . ' Type)</td>';
            $itemsHtml .= '<td style="text-align: center;">' . number_format($amounts['numberOfMeters'], 2) . ' meters</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['pricePerMeter'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['fabricCost'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        
        // Pickup Fee
        if ($amounts['pickupFee'] > 0) {
            $itemCount++;
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>Pick-Up Fee</td>';
            $itemsHtml .= '<td style="text-align: center;">1</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['pickupFee'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['pickupFee'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        
        // Delivery Fee
        if ($amounts['deliveryFee'] > 0) {
            $itemCount++;
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>Delivery Fee</td>';
            $itemsHtml .= '<td style="text-align: center;">1</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['deliveryFee'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['deliveryFee'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        
        // Gas Fee
        if ($amounts['gasFee'] > 0) {
            $itemCount++;
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>Gas Fee</td>';
            $itemsHtml .= '<td style="text-align: center;">1</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['gasFee'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['gasFee'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        
        // Travel Fee
        if ($amounts['travelFee'] > 0) {
            $itemCount++;
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>Travel Fee</td>';
            $itemsHtml .= '<td style="text-align: center;">1</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['travelFee'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['travelFee'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        
        // If no items, add a default item
        if ($itemCount === 0) {
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>' . htmlspecialchars($booking['service_name'] ?? 'Service') . '</td>';
            $itemsHtml .= '<td style="text-align: center;">1</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['totalAmount'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align: right;">₱' . number_format($amounts['totalAmount'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - ' . htmlspecialchars($receiptNumber) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Arial", "Helvetica", sans-serif; 
            background: #f5f5f5; 
            padding: 20px;
        }
        .receipt-container { 
            background: white; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 40px; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            padding-bottom: 20px; 
            border-bottom: 3px solid #2c3e50; 
        }
        .header h1 { 
            color: #2c3e50; 
            font-size: 2.5rem; 
            margin-bottom: 10px; 
            font-weight: 700; 
        }
        .header .business-name { 
            font-size: 1.5rem; 
            font-weight: 600; 
            color: #4e73df; 
            margin-bottom: 10px; 
        }
        .header .business-info { 
            font-size: 0.95rem; 
            color: #6c757d; 
            line-height: 1.6; 
            margin: 5px 0; 
        }
        .receipt-title { 
            color: #28a745; 
            font-weight: 700; 
            font-size: 1.3rem; 
            margin-top: 15px; 
            text-transform: uppercase; 
        }
        .receipt-number { 
            font-size: 1.1rem; 
            font-weight: 600; 
            color: #2c3e50; 
            margin-top: 10px; 
        }
        .section { 
            margin: 25px 0; 
        }
        .section-title { 
            font-size: 1.1rem; 
            font-weight: 600; 
            color: #2c3e50; 
            margin-bottom: 15px; 
            padding-bottom: 8px; 
            border-bottom: 2px solid #e3e6f0; 
        }
        .info-row { 
            margin: 8px 0; 
            display: flex; 
            justify-content: space-between; 
        }
        .info-label { 
            font-weight: 600; 
            color: #5a5c69; 
            min-width: 150px; 
        }
        .info-value { 
            color: #2c3e50; 
            flex: 1; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
        }
        table thead { 
            background: #4e73df; 
            color: white; 
        }
        table th { 
            padding: 12px; 
            text-align: left; 
            font-weight: 600; 
        }
        table th:nth-child(2),
        table th:nth-child(3),
        table th:nth-child(4) { 
            text-align: center; 
        }
        table td { 
            padding: 10px 12px; 
            border: 1px solid #e3e6f0; 
        }
        table td:nth-child(2),
        table td:nth-child(3),
        table td:nth-child(4) { 
            text-align: right; 
        }
        table tbody tr:nth-child(even) { 
            background: #f8f9fc; 
        }
        .summary-table { 
            margin-top: 20px; 
        }
        .summary-table td { 
            padding: 10px; 
            border: 1px solid #e3e6f0; 
        }
        .summary-table td:first-child { 
            font-weight: 600; 
            background: #f8f9fc; 
        }
        .summary-table td:last-child { 
            text-align: right; 
            font-weight: 600; 
        }
        .total-row { 
            background: #28a745 !important; 
            color: white !important; 
            font-size: 1.2rem; 
            font-weight: 700; 
        }
        .total-row td { 
            border-color: #28a745; 
        }
        .signature-section { 
            margin-top: 40px; 
            display: flex; 
            justify-content: space-between; 
        }
        .signature-box { 
            width: 30%; 
            text-align: center; 
        }
        .signature-line { 
            border-top: 2px solid #2c3e50; 
            margin-top: 50px; 
            padding-top: 5px; 
        }
        .footer-notes { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 2px solid #e3e6f0; 
            text-align: center; 
            color: #6c757d; 
            font-size: 0.9rem; 
            line-height: 1.8; 
        }
        .print-button { 
            text-align: center; 
            margin: 20px 0; 
        }
        .print-button button { 
            background: #4e73df; 
            color: white; 
            border: none; 
            padding: 12px 30px; 
            font-size: 1rem; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        .print-button button:hover { 
            background: #2e59d9; 
        }
        @media print {
            body { background: white; padding: 0; }
            .receipt-container { box-shadow: none; padding: 20px; }
            .print-button { display: none; }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>
    
    <div class="receipt-container">
        <!-- Header Section -->
        <div class="header">
            <h1>UpholCare</h1>
            <div class="business-name">Upholstery Services</div>
            <div class="business-info">
                <div>' . htmlspecialchars($businessAddress) . '</div>
                <div>Contact Number: ' . htmlspecialchars($contactNumber) . '</div>
                <div>Email: ' . htmlspecialchars($email) . '</div>
                <div>TIN Number: ' . htmlspecialchars($tinNumber) . '</div>
                <div>BIR Permit Number: ' . htmlspecialchars($birPermit) . '</div>
            </div>
            <div class="receipt-title">Official Receipt</div>
            <div class="receipt-number">Official Receipt Number: ' . htmlspecialchars($receiptNumber) . '</div>
            <div style="margin-top: 10px; font-size: 1rem;">Date Issued: ' . htmlspecialchars($dateIssued) . '</div>
        </div>
        
        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-row">
                <span class="info-label">Customer Name:</span>
                <span class="info-value">' . htmlspecialchars($booking['customer_name'] ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">' . htmlspecialchars($booking['delivery_address'] ?? $booking['pickup_address'] ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Contact Number:</span>
                <span class="info-value">' . htmlspecialchars($booking['customer_phone'] ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Booking Number:</span>
                <span class="info-value">#' . htmlspecialchars($booking['id']) . '</span>
            </div>
        </div>
        
        <!-- Item / Service Details -->
        <div class="section">
            <div class="section-title">Item / Service Details</div>
            <table>
                <thead>
                    <tr>
                        <th>Description of Service</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $itemsHtml . '
                </tbody>
            </table>
        </div>
        
        <!-- Summary of Charges -->
        <div class="section">
            <div class="section-title">Summary of Charges</div>
            <table class="summary-table">
                <tbody>
                    <tr>
                        <td>Subtotal</td>
                        <td>₱' . number_format($amounts['subtotal'], 2) . '</td>
                    </tr>
                    <tr>
                        <td>Inspection Fee (if any)</td>
                        <td>₱0.00</td>
                    </tr>
                    <tr>
                        <td>Pick-Up Fee (if applicable)</td>
                        <td>₱' . number_format($amounts['pickupFee'], 2) . '</td>
                    </tr>
                    <tr>
                        <td>Delivery Fee (if applicable)</td>
                        <td>₱' . number_format($amounts['deliveryFee'], 2) . '</td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL AMOUNT DUE</td>
                        <td>₱' . number_format($amounts['totalAmount'], 2) . '</td>
                    </tr>
                    <tr>
                        <td>TOTAL AMOUNT PAID</td>
                        <td>₱' . number_format($amounts['totalPaid'], 2) . '</td>
                    </tr>
                    <tr>
                        <td>BALANCE</td>
                        <td>₱' . number_format($amounts['balance'], 2) . '</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Payment Details -->
        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="info-row" style="margin-bottom: 15px;">
                <span class="info-label">Mode of Payment:</span>
                <span class="info-value">
                    ' . ($amounts['paymentMode'] === 'Cash' || $amounts['paymentMode'] === 'Cash on Delivery (COD)' ? '✔' : '☐') . ' Cash
                    ' . (strpos($amounts['paymentMode'], 'GCash') !== false || strpos($amounts['paymentMode'], 'Bank') !== false ? '✔' : '☐') . ' GCash / Bank Transfer
                </span>
            </div>
            ' . (strpos($amounts['paymentMode'], 'GCash') !== false || strpos($amounts['paymentMode'], 'Bank') !== false ? '
            <div class="info-row">
                <span class="info-label">Reference Number:</span>
                <span class="info-value">' . htmlspecialchars($receiptNumber) . ' (if online payment)</span>
            </div>
            ' : '') . '
            <div class="info-row">
                <span class="info-label">Date/Time of Payment:</span>
                <span class="info-value">' . htmlspecialchars($amounts['paymentDate']) . ' – ' . htmlspecialchars($amounts['paymentTime']) . '</span>
            </div>
        </div>
        
        <!-- Prepared By -->
        <div class="section">
            <div class="section-title">Prepared By</div>
            <div class="signature-section">
                <div class="signature-box">
                    <div><strong>Prepared By:</strong> ' . htmlspecialchars($adminName) . '</div>
                    <div class="signature-line">Signature</div>
                </div>
                <div class="signature-box">
                    <div><strong>Released By (Delivery Personnel):</strong></div>
                    <div class="signature-line">Signature</div>
                </div>
                <div class="signature-box">
                    <div><strong>Received By (Customer):</strong></div>
                    <div class="signature-line">Signature</div>
                    <div style="margin-top: 10px; font-size: 0.9rem;">Date Received: ____________________</div>
                </div>
            </div>
        </div>
        
        <!-- Footer Notes -->
        <div class="footer-notes">
            <p><strong>Thank you for trusting UpholCare.</strong></p>
            <p>This serves as your official receipt for full payment.</p>
            <p>Items repaired and delivered are subject to 7-day workmanship warranty.</p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            // Auto-print option (optional - can be enabled)
            // window.print();
        };
    </script>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Email Official Receipt to Customer
     */
    public function emailOfficialReceipt($bookingId) {
        header('Content-Type: application/json');
        
        try {
            // Get receipt data (reuse getReceiptPreview logic)
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT b.*, 
                    s.service_name, 
                    sc.category_name,
                    u.fullname as customer_name, 
                    u.email as customer_email, 
                    u.phone as customer_phone,
                    inv.color_name,
                    inv.color_code,
                    b.color_type
                    FROM bookings b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN service_categories sc ON s.category_id = sc.id
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN inventory inv ON b.selected_color_id = inv.id
                    WHERE b.id = ?
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Generate receipt number
            $receiptNumber = 'OR-' . date('Ymd') . '-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT);
            $adminName = $_SESSION['user']['fullname'] ?? 'Admin';
            
            // Calculate amounts
            $laborFee = floatval($booking['labor_fee'] ?? 0);
            $pickupFee = floatval($booking['pickup_fee'] ?? 0);
            $deliveryFee = floatval($booking['delivery_fee'] ?? 0);
            $gasFee = floatval($booking['gas_fee'] ?? 0);
            $travelFee = floatval($booking['travel_fee'] ?? 0);
            $inspectionFee = floatval($booking['inspection_fee'] ?? 0);
            $numberOfMeters = floatval($booking['number_of_meters'] ?? $booking['meters'] ?? 0);
            $pricePerMeter = floatval($booking['price_per_meter'] ?? $booking['fabric_cost_per_meter'] ?? 0);
            $fabricCost = $numberOfMeters * $pricePerMeter;
            if ($fabricCost == 0) {
                $fabricCost = floatval($booking['color_price'] ?? 0);
            }
            
            $subtotal = $laborFee + $fabricCost;
            $totalAdditionalFees = $pickupFee + $deliveryFee + $gasFee + $travelFee + $inspectionFee;
            $totalAmount = $subtotal + $totalAdditionalFees;
            $discount = floatval($booking['discount'] ?? 0);
            $totalPaid = $totalAmount - $discount;
            
            $paymentMode = 'Cash';
            $paymentStatus = strtolower(trim($booking['payment_status'] ?? 'unpaid'));
            if ($paymentStatus === 'paid_on_delivery_cod') {
                $paymentMode = 'Cash on Delivery (COD)';
            } elseif (strpos(strtolower($paymentStatus), 'gcash') !== false || strpos(strtolower($paymentStatus), 'bank') !== false) {
                $paymentMode = 'GCash / Bank Transfer';
            }
            
            // Generate receipt HTML
            $amounts = [
                'laborFee' => $laborFee,
                'pickupFee' => $pickupFee,
                'deliveryFee' => $deliveryFee,
                'gasFee' => $gasFee,
                'travelFee' => $travelFee,
                'inspectionFee' => $inspectionFee,
                'fabricCost' => $fabricCost,
                'numberOfMeters' => $numberOfMeters,
                'pricePerMeter' => $pricePerMeter,
                'colorName' => $booking['color_name'] ?? 'N/A',
                'colorCode' => $booking['color_code'] ?? 'N/A',
                'colorType' => $booking['color_type'] ?? 'standard',
                'subtotal' => $subtotal,
                'totalAmount' => $totalAmount,
                'totalPaid' => $totalPaid,
                'balance' => 0,
                'paymentMode' => $paymentMode,
                'paymentDate' => date('F d, Y'),
                'paymentTime' => date('g:i A')
            ];
            
            $receiptHtml = $this->generateOfficialReceiptHTML($booking, $receiptNumber, $adminName, $amounts);
            
            // Send email using NotificationService
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            // Create HTML email message
            $subject = "Official Receipt - " . $receiptNumber . " - UpholCare";
            $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #4e73df; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background: #f8f9fc; }
                    .receipt-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; }
                    .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 12px; }
                    .button { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Official Receipt - UpholCare</h2>
                    </div>
                    <div class='content'>
                        <p>Dear " . htmlspecialchars($booking['customer_name']) . ",</p>
                        <p>Thank you for your business! Please find your official receipt details below:</p>
                        
                        <div class='receipt-info'>
                            <p><strong>Receipt Number:</strong> " . htmlspecialchars($receiptNumber) . "</p>
                            <p><strong>Booking Number:</strong> #" . htmlspecialchars($booking['id']) . "</p>
                            <p><strong>Total Amount Paid:</strong> ₱" . number_format($totalPaid, 2) . "</p>
                            <p><strong>Payment Method:</strong> " . htmlspecialchars($paymentMode) . "</p>
                            <p><strong>Date Issued:</strong> " . date('F d, Y') . "</p>
                        </div>
                        
                        <p>Your official receipt has been generated and is attached to this email. Please keep this receipt for your records.</p>
                        
                        <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
                        
                        <p>Thank you for choosing UpholCare!</p>
                        
                        <p>Best regards,<br>
                        <strong>UpholCare Team</strong></p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message. Please do not reply to this email.</p>
                        <p>UpholCare - Upholstery & Furniture Repair Services</p>
                    </div>
                </div>
            </body>
            </html>";
            
            // Send email
            $emailSent = $notificationService->sendEmail(
                $booking['customer_email'],
                $subject,
                $message
            );
            
            if ($emailSent) {
                echo json_encode(['success' => true, 'message' => 'Receipt sent successfully to ' . $booking['customer_email']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send email. Please check email configuration in config/email.php']);
            }
            
        } catch (Exception $e) {
            error_log("Error emailing receipt: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Download Official Receipt as PDF
     */
    public function downloadOfficialReceiptPDF($bookingId) {
        // For now, redirect to HTML version (PDF generation requires TCPDF or similar library)
        $this->generateOfficialReceipt($bookingId);
    }
    
    /**
     * Issue Official Receipt (Save to database and mark booking)
     */
    public function issueOfficialReceipt($bookingId) {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking exists and is paid
            $sql = "SELECT b.*, u.email as customer_email, u.fullname as customer_name
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.id
                    WHERE b.id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Check if already issued (only if column exists)
            $checkReceiptColumn = $db->query("SHOW COLUMNS FROM bookings LIKE 'receipt_issued'");
            $hasReceiptColumn = $checkReceiptColumn->rowCount() > 0;
            
            if ($hasReceiptColumn && !empty($booking['receipt_issued']) && $booking['receipt_issued'] == 1) {
                echo json_encode(['success' => false, 'message' => 'Receipt already issued for this booking']);
                exit;
            }
            
            // Generate receipt number
            $receiptNumber = 'OR-' . date('Ymd') . '-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT);
            $adminId = $_SESSION['user']['id'] ?? null;
            
            // Save receipt to official_receipts table (if exists)
            $checkTable = $db->query("SHOW TABLES LIKE 'official_receipts'");
            if ($checkTable->rowCount() > 0) {
                $receiptData = [
                    'booking_id' => $bookingId,
                    'receipt_number' => $receiptNumber,
                    'issued_by' => $adminId,
                    'receipt_data' => json_encode($booking),
                    'status' => 'issued',
                    'email_sent' => 0
                ];
                
                $insertSql = "INSERT INTO official_receipts (booking_id, receipt_number, issued_by, receipt_data, status, email_sent) 
                             VALUES (?, ?, ?, ?, ?, ?)";
                $insertStmt = $db->prepare($insertSql);
                $insertStmt->execute([
                    $receiptData['booking_id'],
                    $receiptData['receipt_number'],
                    $receiptData['issued_by'],
                    $receiptData['receipt_data'],
                    $receiptData['status'],
                    $receiptData['email_sent']
                ]);
            }
            
            // Update booking to mark receipt as issued (only if columns exist)
            $checkReceiptIssued = $db->query("SHOW COLUMNS FROM bookings LIKE 'receipt_issued'");
            $checkReceiptIssuedAt = $db->query("SHOW COLUMNS FROM bookings LIKE 'receipt_issued_at'");
            $checkReceiptNumber = $db->query("SHOW COLUMNS FROM bookings LIKE 'receipt_number'");
            
            $hasReceiptIssued = $checkReceiptIssued->rowCount() > 0;
            $hasReceiptIssuedAt = $checkReceiptIssuedAt->rowCount() > 0;
            $hasReceiptNumber = $checkReceiptNumber->rowCount() > 0;
            
            if ($hasReceiptIssued || $hasReceiptIssuedAt || $hasReceiptNumber) {
                $updateFields = [];
                $updateValues = [];
                
                if ($hasReceiptIssued) {
                    $updateFields[] = "receipt_issued = 1";
                }
                if ($hasReceiptIssuedAt) {
                    $updateFields[] = "receipt_issued_at = NOW()";
                }
                if ($hasReceiptNumber) {
                    $updateFields[] = "receipt_number = ?";
                    $updateValues[] = $receiptNumber;
                }
                
                if (!empty($updateFields)) {
                    $updateValues[] = $bookingId;
                    $updateSql = "UPDATE bookings SET " . implode(", ", $updateFields) . " WHERE id = ?";
                    $updateStmt = $db->prepare($updateSql);
                    $updateStmt->execute($updateValues);
                }
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Official receipt issued successfully',
                'receiptNumber' => $receiptNumber
            ]);
            
        } catch (Exception $e) {
            error_log("Error issuing receipt: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Save Measurements (AJAX)
     * Saves item measurements during inspection
     */
    public function saveMeasurements() {
        // Start output buffering to prevent any output before JSON
        ob_start();
        header('Content-Type: application/json');
        
        // Check request method
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Method not allowed. Expected POST, got ' . $requestMethod]);
            ob_end_flush();
            exit;
        }
        
        try {
            // Handle both JSON and FormData
            $input = [];
            $rawInput = file_get_contents('php://input');
            
            // Try to parse as JSON first
            if (!empty($rawInput)) {
                $jsonInput = json_decode($rawInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonInput)) {
                    $input = $jsonInput;
                }
            }
            
            // Merge with $_POST (FormData will be here)
            $data = array_merge($input, $_POST);
            
            $bookingId = $data['booking_id'] ?? null;
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check which measurement columns exist
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            $updateData = [];
            if (in_array('measurement_height', $existingColumns)) {
                $updateData['measurement_height'] = floatval($data['height'] ?? 0);
            }
            if (in_array('measurement_width', $existingColumns)) {
                $updateData['measurement_width'] = floatval($data['width'] ?? 0);
            }
            if (in_array('measurement_thickness', $existingColumns)) {
                $updateData['measurement_thickness'] = floatval($data['thickness'] ?? 0);
            }
            if (in_array('measurement_custom', $existingColumns)) {
                $updateData['measurement_custom'] = $data['custom_measurements'] ?? '';
            }
            if (in_array('measurement_notes', $existingColumns)) {
                $updateData['measurement_notes'] = $data['notes'] ?? '';
            }
            
            if (empty($updateData)) {
                // Store in calculation_notes if no dedicated columns AND if the column exists
                if (in_array('calculation_notes', $existingColumns)) {
                    $notes = "Measurements - Height: " . ($data['height'] ?? 'N/A') . 
                             "cm, Width: " . ($data['width'] ?? 'N/A') . 
                             "cm, Thickness: " . ($data['thickness'] ?? 'N/A') . 
                             "cm. " . ($data['custom_measurements'] ?? '');
                    $updateData['calculation_notes'] = $notes;
                } else {
                    // If no columns exist at all, at least try to save something
                    // This should not happen, but handle gracefully
                    error_log("Warning: No measurement columns found in bookings table for booking ID: " . $bookingId);
                }
            }
            
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            // If no columns to update, still return success (data might be stored elsewhere or not needed)
            if (empty($updateData)) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Measurements processed (no dedicated columns found)']);
                ob_end_flush();
                exit;
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                // Double-check that the field exists before adding to update
                if (in_array($field, $existingColumns)) {
                    $updateFields[] = "$field = ?";
                    $updateValues[] = $value;
                } else {
                    error_log("Warning: Attempted to update non-existent column '$field' in bookings table");
                }
            }
            
            // If after filtering we have no valid fields, return success anyway
            if (empty($updateFields)) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Measurements processed (no valid columns to update)']);
                ob_end_flush();
                exit;
            }
            
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Measurements saved successfully']);
                ob_end_flush();
            } else {
                http_response_code(500);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Failed to save measurements']);
                ob_end_flush();
            }
        } catch (Exception $e) {
            error_log("Error in saveMeasurements: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error saving measurements: ' . $e->getMessage()]);
            ob_end_flush();
        }
        exit;
    }
    
    /**
     * Save Damages (AJAX)
     * Saves damage/defect records during inspection
     */
    public function saveDamages() {
        // Start output buffering to prevent any output before JSON
        ob_start();
        header('Content-Type: application/json');
        
        // Check request method
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Method not allowed. Expected POST, got ' . $requestMethod]);
            ob_end_flush();
            exit;
        }
        
        try {
            // Handle both JSON and FormData
            $input = [];
            $rawInput = file_get_contents('php://input');
            
            // Try to parse as JSON first
            if (!empty($rawInput)) {
                $jsonInput = json_decode($rawInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonInput)) {
                    $input = $jsonInput;
                }
            }
            
            // Merge with $_POST (FormData will be here)
            $data = array_merge($input, $_POST);
            
            $bookingId = $data['booking_id'] ?? null;
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check which damage columns exist
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            // Handle damage_types - could be array or comma-separated string
            $damageTypes = $data['damage_types'] ?? [];
            if (is_string($damageTypes)) {
                $damageTypes = explode(',', $damageTypes);
                $damageTypes = array_map('trim', $damageTypes);
                $damageTypes = array_filter($damageTypes);
            }
            
            $damageDescription = $data['description'] ?? '';
            $damageLocation = $data['location'] ?? '';
            $damageSeverity = $data['severity'] ?? 'moderate';
            
            $updateData = [];
            
            // Store damage info in calculation_notes or dedicated columns
            $damageNotes = "DAMAGES RECORDED:\n";
            $damageNotes .= "Types: " . (is_array($damageTypes) ? implode(', ', $damageTypes) : $damageTypes) . "\n";
            $damageNotes .= "Description: " . $damageDescription . "\n";
            $damageNotes .= "Location: " . $damageLocation . "\n";
            $damageNotes .= "Severity: " . ucfirst($damageSeverity);
            
            if (in_array('damage_description', $existingColumns)) {
                $updateData['damage_description'] = $damageDescription;
            }
            if (in_array('damage_location', $existingColumns)) {
                $updateData['damage_location'] = $damageLocation;
            }
            if (in_array('damage_severity', $existingColumns)) {
                $updateData['damage_severity'] = $damageSeverity;
            }
            // Save damage_types as comma-separated string or JSON
            if (in_array('damage_types', $existingColumns)) {
                if (is_array($damageTypes) && !empty($damageTypes)) {
                    $updateData['damage_types'] = implode(', ', $damageTypes);
                } elseif (is_string($damageTypes) && !empty($damageTypes)) {
                    $updateData['damage_types'] = $damageTypes;
                } else {
                    $updateData['damage_types'] = 'None';
                }
            }
            
            // Always update calculation_notes with damage info
            if (in_array('calculation_notes', $existingColumns)) {
                $existingNotes = '';
                $stmt = $db->prepare("SELECT calculation_notes FROM bookings WHERE id = ?");
                $stmt->execute([$bookingId]);
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($booking && $booking['calculation_notes']) {
                    $existingNotes = $booking['calculation_notes'] . "\n\n";
                }
                $updateData['calculation_notes'] = $existingNotes . $damageNotes;
            }
            
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            if (empty($updateData)) {
                http_response_code(500);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'No valid columns found to update']);
                ob_end_flush();
                exit;
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Damage record saved successfully']);
                ob_end_flush();
            } else {
                http_response_code(500);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Failed to save damage record']);
                ob_end_flush();
            }
        } catch (Exception $e) {
            error_log("Error in saveDamages: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error saving damages: ' . $e->getMessage()]);
            ob_end_flush();
        }
        exit;
    }
    
    /**
     * Save Materials (AJAX)
     * Saves materials/fabrics used during inspection
     */
    public function saveMaterials() {
        // Start output buffering to prevent any output before JSON
        ob_start();
        header('Content-Type: application/json');
        
        // Check request method
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Method not allowed. Expected POST, got ' . $requestMethod]);
            ob_end_flush();
            exit;
        }
        
        try {
            // Handle both JSON and FormData
            $input = [];
            $rawInput = file_get_contents('php://input');
            
            // Try to parse as JSON first
            if (!empty($rawInput)) {
                $jsonInput = json_decode($rawInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonInput)) {
                    $input = $jsonInput;
                }
            }
            
            // Merge with $_POST (FormData will be here)
            $data = array_merge($input, $_POST);
            
            $bookingId = $data['booking_id'] ?? null;
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check which material columns exist
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            // Handle field names - support both 'fabric_type' and 'material_fabric_type'
            $fabricType = $data['fabric_type'] ?? $data['material_fabric_type'] ?? '';
            $meters = floatval($data['meters'] ?? $data['material_meters'] ?? 0);
            $foamReplacement = $data['foam_replacement'] ?? $data['material_foam'] ?? 'none';
            $foamThickness = floatval($data['foam_thickness'] ?? $data['material_foam_thickness'] ?? 0);
            $accessories = $data['accessories'] ?? [];
            $notes = $data['notes'] ?? $data['material_notes'] ?? '';
            
            $updateData = [];
            
            // Store material info
            $materialNotes = "MATERIALS USED:\n";
            $materialNotes .= "Fabric Type: " . $fabricType . "\n";
            $materialNotes .= "Meters/Yards: " . $meters . "\n";
            $materialNotes .= "Foam Replacement: " . ucfirst($foamReplacement);
            if ($foamThickness > 0) {
                $materialNotes .= " (" . $foamThickness . " inches)";
            }
            $materialNotes .= "\n";
            if (is_array($accessories) && !empty($accessories)) {
                $materialNotes .= "Accessories: " . implode(', ', $accessories) . "\n";
            }
            if ($notes) {
                $materialNotes .= "Notes: " . $notes;
            }
            
            if (in_array('material_fabric_type', $existingColumns)) {
                $updateData['material_fabric_type'] = $fabricType;
            }
            if (in_array('material_meters', $existingColumns)) {
                $updateData['material_meters'] = $meters;
            }
            if (in_array('material_foam', $existingColumns)) {
                $updateData['material_foam'] = $foamReplacement;
            }
            if (in_array('material_foam_thickness', $existingColumns)) {
                $updateData['material_foam_thickness'] = $foamThickness;
            }
            if (in_array('material_notes', $existingColumns)) {
                $updateData['material_notes'] = $notes;
            }
            
            // Always update calculation_notes with material info
            if (in_array('calculation_notes', $existingColumns)) {
                $existingNotes = '';
                $stmt = $db->prepare("SELECT calculation_notes FROM bookings WHERE id = ?");
                $stmt->execute([$bookingId]);
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($booking && $booking['calculation_notes']) {
                    $existingNotes = $booking['calculation_notes'] . "\n\n";
                }
                $updateData['calculation_notes'] = $existingNotes . $materialNotes;
            }
            
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            if (empty($updateData)) {
                http_response_code(500);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'No valid columns found to update']);
                ob_end_flush();
                exit;
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Materials saved successfully']);
                ob_end_flush();
            } else {
                http_response_code(500);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Failed to save materials']);
                ob_end_flush();
            }
        } catch (Exception $e) {
            error_log("Error in saveMaterials: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error saving materials: ' . $e->getMessage()]);
            ob_end_flush();
        }
        exit;
    }
    
    /**
     * Get Booking Technician Assignment (AJAX)
     */
    public function getBookingTechnician($bookingId) {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking_technicians table exists
            $tableExists = false;
            try {
                $checkStmt = $db->query("SHOW TABLES LIKE 'booking_technicians'");
                $tableExists = $checkStmt->rowCount() > 0;
            } catch (Exception $e) {
                // Table doesn't exist
            }
            
            if ($tableExists) {
                $stmt = $db->prepare("
                    SELECT bt.technician_id, u.fullname as name
                    FROM booking_technicians bt
                    LEFT JOIN users u ON bt.technician_id = u.id
                    WHERE bt.booking_id = ? AND bt.status = 'active'
                    ORDER BY bt.assigned_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$bookingId]);
                $technician = $stmt->fetch();
                
                if ($technician) {
                    echo json_encode([
                        'success' => true,
                        'technician' => [
                            'id' => $technician['technician_id'],
                            'name' => $technician['name']
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'technician' => null
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => true,
                    'technician' => null
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading technician: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get Booking Progress History (AJAX)
     */
    public function getBookingProgress($bookingId) {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking_progress_logs table exists
            $tableExists = false;
            try {
                $checkStmt = $db->query("SHOW TABLES LIKE 'booking_progress_logs'");
                $tableExists = $checkStmt->rowCount() > 0;
            } catch (Exception $e) {
                // Table doesn't exist
            }
            
            if ($tableExists) {
                $stmt = $db->prepare("
                    SELECT bpl.*, u.fullname as admin_name
                    FROM booking_progress_logs bpl
                    LEFT JOIN users u ON bpl.admin_id = u.id
                    WHERE bpl.booking_id = ?
                    ORDER BY bpl.created_at DESC
                ");
                $stmt->execute([$bookingId]);
                $progress = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'progress' => $progress
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'progress' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading progress: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Cancel Booking (Admin)
     */
    public function cancelBooking($id) {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id, status, payment_status FROM bookings WHERE id = ?");
            $stmt->execute([$id]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                throw new Exception('Booking not found');
            }
            
            // Only allow cancellation if not already completed and paid
            $status = strtolower($booking['status'] ?? '');
            $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
            
            if (in_array($status, ['completed', 'delivered_and_paid']) && 
                in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod'])) {
                throw new Exception('Cannot cancel a completed and paid booking.');
            }
            
            // Update booking status to cancelled
            $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);
            
            $db->commit();
            
            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error cancelling booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get Booking Compliance Data (AJAX)
     */
    public function getBookingCompliance() {
        $bookingId = $_GET['booking_id'] ?? null;
        $customerId = $_GET['customer_id'] ?? null;
        
        if (!$bookingId || !$customerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID and Customer ID are required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Get customer information
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
                return;
            }
            
            // Get current booking details
            // Note: Queue number is automatically assigned when customer submits reservation
            // No need to check customer_booking_numbers table
            $stmt = $db->prepare("
                SELECT b.*, s.service_name, s.service_type, sc.category_name
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE b.id = ? AND b.user_id = ?
            ");
            $stmt->execute([$bookingId, $customerId]);
            $booking = $stmt->fetch();
            
            // Check if booking was previously rejected
            // Check booking status for 'cancelled' or 'rejected'
            $hasRejection = false;
            $rejectionReason = null;
            
            if ($booking) {
                // Check if status is cancelled or rejected
                if (in_array(strtolower($booking['status']), ['cancelled', 'rejected'])) {
                    $hasRejection = true;
                }
                
                // Check if there's a rejection reason in notes
                if ($booking['notes'] && (
                    stripos($booking['notes'], 'rejected') !== false || 
                    stripos($booking['notes'], 'rejection') !== false ||
                    stripos($booking['notes'], 'declined') !== false
                )) {
                    $hasRejection = true;
                    // Try to extract rejection reason
                    if (preg_match('/rejection.*?reason[:\s]+(.+?)(?:\n|$)/i', $booking['notes'], $matches)) {
                        $rejectionReason = trim($matches[1]);
                    }
                }
                
                // Check for rejection records in notifications or logs if tables exist
                try {
                    $rejectionStmt = $db->prepare("
                        SELECT message 
                        FROM notifications 
                        WHERE related_id = ? 
                        AND (type = 'error' OR type = 'danger' OR message LIKE '%reject%' OR message LIKE '%decline%')
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ");
                    $rejectionStmt->execute([$bookingId]);
                    $rejectionNotif = $rejectionStmt->fetch();
                    if ($rejectionNotif) {
                        $hasRejection = true;
                        if (!$rejectionReason) {
                            $rejectionReason = $rejectionNotif['message'];
                        }
                    }
                } catch (Exception $e) {
                    // Table might not exist, ignore
                }
            }
            
            // Calculate compliance score
            // Note: Queue number will be assigned by admin when approving reservation
            // Queue number is NOT a requirement for approval - admin assigns it during approval
            $complianceScore = 0;
            $totalChecks = 4; // Queue number assignment is done by admin, not a requirement
            
            // Queue number is optional - admin will assign it when approving
            // if ($booking && $booking['booking_number']) $complianceScore += 25; // Not required
            
            if ($customer['email']) $complianceScore += 25; // Valid email
            if ($customer['phone']) $complianceScore += 25; // Contact info
            if ($booking && $booking['service_id']) $complianceScore += 25; // Service selection
            if ($booking && $booking['booking_date']) $complianceScore += 25; // Booking date
            
            $complianceScore = round($complianceScore);
            
            echo json_encode([
                'success' => true,
                'customer' => $customer,
                'booking' => $booking,
                'complianceScore' => $complianceScore,
                'hasRejection' => $hasRejection,
                'rejectionReason' => $rejectionReason
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Email Notifications Management
     */
    public function emailNotifications() {
        $data = [
            'title' => 'Email Notifications - ' . APP_NAME,
            'user' => $this->currentUser()
        ];
        
        $this->view('admin/email_notifications', $data);
    }
    
    /**
     * Test Email Configuration
     */
    /**
     * Test Auto Status Update Service
     * Access via: /admin/testAutoStatusUpdate
     */
    public function testAutoStatusUpdate() {
        header('Content-Type: text/html; charset=utf-8');
        
        echo "<!DOCTYPE html><html><head><title>Test Auto Status Update</title>";
        echo "<style>body{font-family:Arial,sans-serif;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;} table{border-collapse:collapse;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;}</style>";
        echo "</head><body>";
        echo "<h1>Auto Status Update Test</h1>";
        
        try {
            echo "<h2>Running Auto Status Update Service...</h2>";
            
            $stats = AutoStatusUpdateService::run();
            
            echo "<div class='info'><strong>Statistics:</strong></div>";
            echo "<ul>";
            echo "<li><strong>Bookings Checked:</strong> {$stats['checked']}</li>";
            echo "<li><strong>Bookings Updated:</strong> {$stats['updated']}</li>";
            echo "<li><strong>Errors:</strong> {$stats['errors']}</li>";
            if (isset($stats['error_message'])) {
                echo "<li><strong>Error Message:</strong> <span class='error'>{$stats['error_message']}</span></li>";
            }
            echo "</ul>";
            
            if (!empty($stats['details'])) {
                echo "<h3>Updated Bookings:</h3>";
                echo "<table>";
                echo "<tr><th>Booking ID</th><th>Old Status</th><th>New Status</th><th>Pickup Date</th></tr>";
                foreach ($stats['details'] as $detail) {
                    echo "<tr>";
                    echo "<td>#{$detail['booking_id']}</td>";
                    echo "<td>{$detail['old_status']}</td>";
                    echo "<td class='success'><strong>{$detail['new_status']}</strong></td>";
                    echo "<td>{$detail['pickup_date']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='info'>No bookings were updated.</div>";
            }
            
            // Show current bookings that should be updated
            echo "<h3>Current Bookings Status (for debugging):</h3>";
            $db = Database::getInstance()->getConnection();
            $today = date('Y-m-d');
            
            $sql = "SELECT id, status, pickup_date, service_option 
                    FROM bookings 
                    WHERE pickup_date IS NOT NULL 
                    AND pickup_date != ''
                    AND (
                        DATE(pickup_date) <= :today
                        OR pickup_date <= :todayDateTime
                    )
                    AND status IN ('approved', 'for_pickup')
                    ORDER BY pickup_date ASC
                    LIMIT 20";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'today' => $today,
                'todayDateTime' => date('Y-m-d H:i:s')
            ]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($bookings)) {
                echo "<div class='info'>No bookings found with pickup_date <= today and status 'approved' or 'for_pickup'.</div>";
            } else {
                echo "<table>";
                echo "<tr><th>Booking ID</th><th>Status</th><th>Pickup Date</th><th>Service Option</th><th>Should Update?</th></tr>";
                foreach ($bookings as $booking) {
                    $shouldUpdate = '';
                    if ($booking['status'] === 'approved' && in_array(strtolower($booking['service_option']), ['pickup', 'both'])) {
                        $shouldUpdate = '<span class="success">Yes → for_pickup</span>';
                    } elseif ($booking['status'] === 'for_pickup') {
                        $shouldUpdate = '<span class="success">Yes → picked_up → to_inspect</span>';
                    } else {
                        $shouldUpdate = '<span class="error">No</span>';
                    }
                    
                    echo "<tr>";
                    echo "<td>#{$booking['id']}</td>";
                    echo "<td>{$booking['status']}</td>";
                    echo "<td>{$booking['pickup_date']}</td>";
                    echo "<td>{$booking['service_option']}</td>";
                    echo "<td>{$shouldUpdate}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            echo "<hr>";
            echo "<p><strong>Current Date/Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
            echo "<p><a href='javascript:location.reload()'>Refresh</a> | <a href='" . BASE_URL . "admin/allBookings'>Back to All Bookings</a></p>";
            
        } catch (Exception $e) {
            echo "<div class='error'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
        echo "</body></html>";
        exit;
    }
    
    public function testEmail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $testEmail = $_POST['test_email'] ?? null;
        
        if (!$testEmail || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid email address is required']);
            return;
        }
        
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $result = $notificationService->testEmailConfiguration();
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Test email sent successfully to ' . $testEmail
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to send test email. Check your email configuration.'
                ]);
            }
        } catch (Exception $e) {
            error_log("Test email error: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error sending test email: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get Notifications (AJAX)
     */
    public function getNotifications() {
        header('Content-Type: application/json');
        ob_start();
        
        try {
            if (!$this->isLoggedIn()) {
                http_response_code(401);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Get user ID from session directly as fallback
            $userId = null;
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            } else {
                // Try currentUser method
                $currentUser = $this->currentUser();
                if ($currentUser && isset($currentUser['id'])) {
                    $userId = $currentUser['id'];
                }
            }
            
            if (!$userId) {
                http_response_code(401);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'User not found or not logged in']);
                ob_end_flush();
                exit;
            }
            
            // Check if notifications table exists
            $checkTable = $db->query("SHOW TABLES LIKE 'notifications'");
            if ($checkTable->rowCount() === 0) {
                // Table doesn't exist, return empty notifications
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'unread_count' => 0,
                    'notifications' => []
                ]);
                ob_end_flush();
                exit;
            }
            
            // Get unread count
            $countStmt = $db->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
            $countStmt->execute([$userId]);
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            $unreadCount = $countResult['unread_count'] ?? 0;
            
            // Get recent notifications (last 10)
            $notifStmt = $db->prepare("
                SELECT id, title, message, type, is_read, created_at 
                FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $notifStmt->execute([$userId]);
            $notifications = $notifStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format notifications for display
            $formattedNotifications = [];
            foreach ($notifications as $notif) {
                $formattedNotifications[] = [
                    'id' => $notif['id'] ?? 0,
                    'title' => $notif['title'] ?? '',
                    'message' => $notif['message'] ?? '',
                    'type' => $notif['type'] ?? 'info',
                    'is_read' => (bool)($notif['is_read'] ?? 0),
                    'created_at' => $notif['created_at'] ?? date('Y-m-d H:i:s'),
                    'time_ago' => isset($notif['created_at']) ? $this->timeAgo($notif['created_at']) : 'Just now'
                ];
            }
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'unread_count' => (int)$unreadCount,
                'notifications' => $formattedNotifications
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error in getNotifications: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Error fetching notifications: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Mark notification as read (AJAX)
     */
    public function markNotificationRead() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $notificationId = $_POST['notification_id'] ?? null;
        
        if (!$notificationId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $userId = $this->currentUser()['id'];
            
            // Mark notification as read
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
            $stmt->execute([$notificationId, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Helper function to calculate time ago
     */
    private function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M d, Y', $timestamp);
        }
    }
    
    /**
     * Get Email Logs
     */
    public function getEmailLogs() {
        $logFile = ROOT . DS . 'logs' . DS . 'email_notifications.log';
        
        if (!file_exists($logFile)) {
            echo json_encode([
                'success' => true,
                'logs' => []
            ]);
            return;
        }
        
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        // Get last 50 logs
        $logs = array_slice(array_reverse($logs), 0, 50);
        
        echo json_encode([
            'success' => true,
            'logs' => $logs
        ]);
    }
    
    /**
     * Create quotation for a booking
     */
    public function createQuotation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $totalAmount = $_POST['total_amount'] ?? null;
        $validUntil = $_POST['valid_until'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        if (!$bookingId || !$totalAmount) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID and total amount are required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verify booking exists
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            $quotationModel = $this->model('Quotation');
            
            // Check if quotation already exists for this booking
            $existingQuotations = $quotationModel->getQuotationsByBookingId($bookingId);
            
            // If quotation exists, update it; otherwise create new
            if (!empty($existingQuotations)) {
                // Find draft quotation
                $draftQuotation = null;
                foreach ($existingQuotations as $quote) {
                    if ($quote['status'] === 'draft') {
                        $draftQuotation = $quote;
                        break;
                    }
                }
                
                if ($draftQuotation) {
                    // Update existing draft quotation
                    $updateData = [
                        'total_amount' => $totalAmount,
                        'notes' => $notes
                    ];
                    
                    if ($validUntil) {
                        $updateData['valid_until'] = $validUntil;
                    }
                    
                    $result = $quotationModel->update($draftQuotation['id'], $updateData);
                    $quotationId = $draftQuotation['id'];
                } else {
                    // Create new quotation
                    $quotationData = [
                        'booking_id' => $bookingId,
                        'total_amount' => $totalAmount,
                        'status' => 'draft',
                        'notes' => $notes
                    ];
                    
                    if ($validUntil) {
                        $quotationData['valid_until'] = $validUntil;
                    }
                    
                    $quotationId = $quotationModel->createQuotation($quotationData);
                    $result = $quotationId !== false;
                }
            } else {
                // Create new quotation
                $quotationData = [
                    'booking_id' => $bookingId,
                    'total_amount' => $totalAmount,
                    'status' => 'draft',
                    'notes' => $notes
                ];
                
                if ($validUntil) {
                    $quotationData['valid_until'] = $validUntil;
                }
                
                $quotationId = $quotationModel->createQuotation($quotationData);
                $result = $quotationId !== false;
            }
            
            if ($result) {
                $quotation = $quotationModel->getQuotationById($quotationId);
                echo json_encode([
                    'success' => true,
                    'message' => 'Quotation created successfully',
                    'quotation' => $quotation
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create quotation']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Send quotation to customer
     */
    public function sendQuotation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $quotationId = $_POST['quotation_id'] ?? null;
        
        if (!$quotationId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Quotation ID is required']);
            return;
        }
        
        try {
            $quotationModel = $this->model('Quotation');
            $quotation = $quotationModel->getQuotationById($quotationId);
            
            if (!$quotation) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Quotation not found']);
                return;
            }
            
            // Check if quotation has total amount
            if (!$quotation['total_amount']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quotation must have a total amount before sending']);
                return;
            }
            
            // Send quotation (change status to 'sent')
            $result = $quotationModel->sendQuotation($quotationId);
            
            if ($result) {
                // Send notification to customer (optional)
                $db = Database::getInstance()->getConnection();
                
                // Create notification for customer
                $hasRelatedId = false;
                try {
                    $checkStmt = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'notifications' 
                        AND COLUMN_NAME = 'related_id'
                    ");
                    $hasRelatedId = $checkStmt->fetch()['cnt'] > 0;
                } catch (Exception $e) {
                    // Ignore error
                }
                
                if ($hasRelatedId) {
                    $notifStmt = $db->prepare("
                        INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                        VALUES (?, 'info', ?, ?, ?, NOW())
                    ");
                    $notifStmt->execute([
                        $quotation['user_id'],
                        'New Quotation Available',
                        "A quotation has been sent for your booking. Quotation Number: " . $quotation['quotation_number'],
                        $quotationId
                    ]);
                } else {
                    $notifStmt = $db->prepare("
                        INSERT INTO notifications (user_id, type, title, message, created_at) 
                        VALUES (?, 'info', ?, ?, NOW())
                    ");
                    $notifStmt->execute([
                        $quotation['user_id'],
                        'New Quotation Available',
                        "A quotation has been sent for your booking. Quotation Number: " . $quotation['quotation_number']
                    ]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quotation sent to customer successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to send quotation']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get quotations for admin
     */
    public function quotations() {
        $status = $_GET['status'] ?? null;
        $quotationModel = $this->model('Quotation');
        $quotations = $quotationModel->getAllQuotations($status);
        
        $data = [
            'title' => 'Quotations - ' . APP_NAME,
            'user' => $this->currentUser(),
            'quotations' => $quotations,
            'currentStatus' => $status
        ];
        
        $this->view('admin/quotations', $data);
    }
    
    /**
     * Services Management
     */
    public function services() {
        $serviceModel = $this->model('Service');
        
        // Auto-migrate any legacy 'inactive' services → 'archived'
        $serviceModel->migrateInactiveToArchived();
        
        $services = $serviceModel->getAllServices();
        $categories = $serviceModel->getAllCategories();
        
        $data = [
            'title' => 'Services Management - ' . APP_NAME,
            'user' => $this->currentUser(),
            'services' => $services,
            'categories' => $categories
        ];
        
        $this->view('admin/services', $data);
    }
    
    /**
     * Create Service
     */
    public function createService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $serviceName = trim($_POST['service_name'] ?? '');
        $serviceType = trim($_POST['service_type'] ?? '');
        $categoryId = $_POST['category_id'] ?? null;
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? null;
        $status = $_POST['status'] ?? 'active';
        
        if (empty($serviceName)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service name is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $userId = $this->currentUser()['id'];
            
            // Get the admin's store_id from store_locations table
            $storeStmt = $db->prepare("SELECT id FROM store_locations WHERE admin_id = ? AND status = 'active' LIMIT 1");
            $storeStmt->execute([$userId]);
            $store = $storeStmt->fetch();
            
            if (!$store) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No active store found for this admin. Please contact support.']);
                return;
            }
            
            $storeId = $store['id'];
            
            $serviceModel = $this->model('Service');
            
            $serviceData = [
                'service_name' => $serviceName,
                'service_type' => $serviceType,
                'description' => $description,
                'price' => $price ? (float)$price : null,
                'status' => $status,
                'store_id' => $storeId  // Automatically assign to admin's store
            ];
            
            if ($categoryId) {
                $serviceData['category_id'] = (int)$categoryId;
            }
            
            $serviceId = $serviceModel->createService($serviceData);
            
            if ($serviceId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service created successfully',
                    'service_id' => $serviceId
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create service']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Update Service
     */
    public function updateService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $serviceId = $_POST['service_id'] ?? null;
        $serviceName = trim($_POST['service_name'] ?? '');
        $serviceType = trim($_POST['service_type'] ?? '');
        $categoryId = $_POST['category_id'] ?? null;
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? null;
        $status = $_POST['status'] ?? 'active';
        
        if (!$serviceId || empty($serviceName)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service ID and name are required']);
            return;
        }
        
        try {
            $serviceModel = $this->model('Service');
            
            // Verify service exists
            $service = $serviceModel->getById($serviceId);
            if (!$service) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }
            
            $updateData = [
                'service_name' => $serviceName,
                'service_type' => $serviceType,
                'description' => $description,
                'price' => $price ? (float)$price : null,
                'status' => $status
            ];
            
            if ($categoryId) {
                $updateData['category_id'] = (int)$categoryId;
            } else {
                $updateData['category_id'] = null;
            }
            
            $result = $serviceModel->updateService($serviceId, $updateData);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update service']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Archive Service (replaces Delete)
     */
    public function deleteService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $serviceId = $_POST['service_id'] ?? null;
        
        if (!$serviceId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service ID is required']);
            return;
        }
        
        try {
            $serviceModel = $this->model('Service');
            
            // Verify service exists
            $service = $serviceModel->getById($serviceId);
            if (!$service) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }
            
            // Archive the service instead of hard/soft deleting
            $result = $serviceModel->archiveService($serviceId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service "' . htmlspecialchars($service['service_name']) . '" has been moved to Archived Services.'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to archive service']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Archived Services View
     */
    public function archivedServices() {
        $serviceModel = $this->model('Service');
        $archivedServices = $serviceModel->getArchivedServices();
        
        $data = [
            'title' => 'Archived Services - ' . APP_NAME,
            'user' => $this->currentUser(),
            'archivedServices' => $archivedServices
        ];
        
        $this->view('admin/archived_services', $data);
    }
    
    /**
     * Restore Archived Service (AJAX)
     */
    public function restoreService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $serviceId = $_POST['service_id'] ?? null;
        
        if (!$serviceId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service ID is required']);
            return;
        }
        
        try {
            $serviceModel = $this->model('Service');
            
            $service = $serviceModel->getById($serviceId);
            if (!$service) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }
            
            $result = $serviceModel->restoreService($serviceId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service "' . htmlspecialchars($service['service_name']) . '" has been restored to active services.'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to restore service']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get Archived Services (AJAX)
     */
    public function getArchivedServices() {
        header('Content-Type: application/json');
        try {
            $serviceModel = $this->model('Service');
            $services = $serviceModel->getArchivedServices();
            echo json_encode(['success' => true, 'services' => $services]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Admin Profile
     */
    public function profile() {
        $userModel = $this->model('User');
        $userId = $this->currentUser()['id'];
        $userDetails = $userModel->getById($userId);
        
        // Get user's profile images with fallback
        // Check if database columns exist, if not create them
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check and add cover_image column if it doesn't exist
            $checkCover = $db->query("SHOW COLUMNS FROM users LIKE 'cover_image'");
            if ($checkCover->rowCount() == 0) {
                $db->exec("ALTER TABLE users ADD COLUMN cover_image VARCHAR(255) DEFAULT NULL");
            }
            
            // Check and add profile_image column if it doesn't exist
            $checkProfile = $db->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
            if ($checkProfile->rowCount() == 0) {
                $db->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
            }
            
            // Refresh user details after adding columns
            $userDetails = $userModel->getById($userId);
        } catch (Exception $e) {
            error_log('Error checking/adding columns: ' . $e->getMessage());
            // Continue anyway - columns might already exist
        }
        
        // Set default images
        $coverImage = BASE_URL . 'assets/images/default-cover.svg';
        $profileImage = BASE_URL . 'assets/images/default-avatar.svg';
        
        if (!empty($userDetails['cover_image'])) {
            $coverImagePath = $userDetails['cover_image'];
            // Check if file exists
            if (file_exists(ROOT . DS . $coverImagePath)) {
                $coverImage = BASE_URL . $coverImagePath;
            }
        }
        
        if (!empty($userDetails['profile_image'])) {
            $profileImagePath = $userDetails['profile_image'];
            // Check if file exists
            if (file_exists(ROOT . DS . $profileImagePath)) {
                $profileImage = BASE_URL . $profileImagePath . '?t=' . time(); // Add cache busting
            }
        }
        
        // Merge userDetails with currentUser to ensure all fields are available
        $currentUser = $this->currentUser();
        $user = array_merge($currentUser ?? [], $userDetails ?? []);
        
        // Update session with latest user data from database to ensure consistency
        $_SESSION['user'] = $userDetails;
        
        $data = [
            'title' => 'My Profile - ' . APP_NAME,
            'user' => $user, // Use full user data from database
            'userDetails' => $userDetails,
            'coverImage' => $coverImage,
            'profileImage' => $profileImage
        ];
        
        $this->view('admin/profile', $data);
    }
    
    /**
     * Update Admin Profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/profile');
        }
        
        $userId = $this->currentUser()['id'];
        $userModel = $this->model('User');
        
        // Get form data - check for both 'name' and 'fullname' columns
        $updateData = [];
        
        // Check which column exists
        try {
            $db = Database::getInstance()->getConnection();
            $checkFullname = $db->query("SHOW COLUMNS FROM users LIKE 'fullname'");
            $hasFullname = $checkFullname->rowCount() > 0;
            
            if ($hasFullname) {
                $updateData['fullname'] = trim($_POST['fullname'] ?? '');
            } else {
                $updateData['name'] = trim($_POST['fullname'] ?? trim($_POST['name'] ?? ''));
            }
        } catch (Exception $e) {
            // Default to 'name' if check fails
            $updateData['name'] = trim($_POST['fullname'] ?? trim($_POST['name'] ?? ''));
        }
        
        $updateData['email'] = trim($_POST['email'] ?? '');
        $updateData['phone'] = trim($_POST['phone'] ?? '');
        
        // Validate required fields
        if (empty($updateData['fullname'] ?? $updateData['name'] ?? '')) {
            $_SESSION['error'] = 'Full name is required';
            $this->redirect('admin/profile');
        }
        
        if (empty($updateData['email'])) {
            $_SESSION['error'] = 'Email is required';
            $this->redirect('admin/profile');
        }
        
        // Check if email is already taken by another user
        $existingUser = $userModel->findByEmail($updateData['email']);
        if ($existingUser && $existingUser['id'] != $userId) {
            $_SESSION['error'] = 'Email is already taken by another user';
            $this->redirect('admin/profile');
        }
        
        // Update user
        $result = $userModel->updateUser($userId, $updateData);
        
        if ($result) {
            // Update session with new data
            $updatedUser = $userModel->getById($userId);
            $_SESSION['user'] = $updatedUser;
            
            $_SESSION['success'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update profile. Please try again.';
        }
        
        $this->redirect('admin/profile');
    }
    
    /**
     * Change Admin Password
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/profile');
        }
        
        $userId = $this->currentUser()['id'];
        $userModel = $this->model('User');
        $user = $userModel->getById($userId);
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All password fields are required';
            $this->redirect('admin/profile');
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'Current password is incorrect';
            $this->redirect('admin/profile');
        }
        
        // Check if new password matches confirmation
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New password and confirmation do not match';
            $this->redirect('admin/profile');
        }
        
        // Check password strength (minimum 6 characters)
        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters long';
            $this->redirect('admin/profile');
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $userModel->updateUser($userId, ['password' => $hashedPassword]);
        
        if ($result) {
            $_SESSION['success'] = 'Password changed successfully!';
        } else {
            $_SESSION['error'] = 'Failed to change password. Please try again.';
        }
        
        $this->redirect('admin/profile');
    }
    
    /**
     * Upload Profile Images (AJAX)
     */
    public function uploadProfileImages() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $userId = $this->currentUser()['id'];
        $uploadDir = ROOT . DS . 'assets' . DS . 'uploads' . DS . 'profiles' . DS;
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                exit;
            }
        }
        
        $errors = [];
        $uploadedFiles = [];
        
        // Handle cover image upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $coverImage = $_FILES['cover_image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (in_array($coverImage['type'], $allowedTypes)) {
                $coverFileName = 'cover_' . $userId . '_' . time() . '.' . pathinfo($coverImage['name'], PATHINFO_EXTENSION);
                $coverPath = $uploadDir . $coverFileName;
                
                if (move_uploaded_file($coverImage['tmp_name'], $coverPath)) {
                    $uploadedFiles['cover_image'] = 'assets/uploads/profiles/' . $coverFileName;
                } else {
                    $errors[] = 'Failed to upload cover image';
                }
            } else {
                $errors[] = 'Invalid cover image format. Allowed: JPG, PNG, GIF, WEBP';
            }
        }
        
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $profileImage = $_FILES['profile_image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (in_array($profileImage['type'], $allowedTypes)) {
                $profileFileName = 'profile_' . $userId . '_' . time() . '.' . pathinfo($profileImage['name'], PATHINFO_EXTENSION);
                $profilePath = $uploadDir . $profileFileName;
                
                if (move_uploaded_file($profileImage['tmp_name'], $profilePath)) {
                    $uploadedFiles['profile_image'] = 'assets/uploads/profiles/' . $profileFileName;
                } else {
                    $errors[] = 'Failed to upload profile image';
                }
            } else {
                $errors[] = 'Invalid profile image format. Allowed: JPG, PNG, GIF, WEBP';
            }
        }
        
        if (empty($errors) && !empty($uploadedFiles)) {
            try {
                $userModel = $this->model('User');
                $updateData = [];
                
                if (isset($uploadedFiles['cover_image'])) {
                    $updateData['cover_image'] = $uploadedFiles['cover_image'];
                }
                if (isset($uploadedFiles['profile_image'])) {
                    $updateData['profile_image'] = $uploadedFiles['profile_image'];
                }
                
                if (!empty($updateData)) {
                    $result = $userModel->updateUser($userId, $updateData);
                    
                    if ($result) {
                        // Update session
                        $updatedUser = $userModel->getById($userId);
                        $_SESSION['user'] = $updatedUser;
                        
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Image uploaded successfully',
                            'files' => $uploadedFiles
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to update user profile']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'No files to upload']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => !empty($errors) ? implode(', ', $errors) : 'No files uploaded'
            ]);
        }
        exit;
    }
    /**
     * Logistic Availability Manager
     */
    public function logisticAvailability() {
        $db = Database::getInstance()->getConnection();
        $storeId = $this->getAdminStoreLocationId();
        
        if (!$storeId) {
            $_SESSION['error'] = 'Could not find your store link. Please contact support.';
            $this->redirect('admin/dashboard');
            return;
        }
        
        // Get capacity for the next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        
        $sql = "SELECT * FROM store_logistic_capacities WHERE store_id = ? AND date BETWEEN ? AND ? ORDER BY date ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$storeId, $startDate, $endDate]);
        $capacityRecords = $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
        
        // Prepare 30 days of data, filling from DB or defaults
        $availabilityData = [];
        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            if (isset($capacityRecords[$date])) {
                $availabilityData[$date] = $capacityRecords[$date];
            } else {
                $availabilityData[$date] = [
                    'date' => $date,
                    'max_pickup' => 2,
                    'max_delivery' => 2,
                    'max_inspection' => 3
                ];
            }
        }
        
        $data = [
            'title' => 'Logistic Availability Manager - ' . APP_NAME,
            'user' => $this->currentUser(),
            'availabilityData' => $availabilityData,
            'storeId' => $storeId
        ];
        
        $this->view('admin/logistic_availability', $data);
    }
    
    /**
     * Update Logistic Capacity (AJAX)
     */
    public function updateLogisticCapacity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $storeId = $this->getAdminStoreLocationId();
        $date = $_POST['date'] ?? null;
        $max_pickup = isset($_POST['max_pickup']) ? (int)$_POST['max_pickup'] : 2;
        $max_delivery = isset($_POST['max_delivery']) ? (int)$_POST['max_delivery'] : 2;
        $max_inspection = isset($_POST['max_inspection']) ? (int)$_POST['max_inspection'] : 3;
        
        if (!$storeId || !$date) {
            echo json_encode(['success' => false, 'message' => 'Missing required data']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO store_logistic_capacities (store_id, date, max_pickup, max_delivery, max_inspection) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                max_pickup = VALUES(max_pickup),
                max_delivery = VALUES(max_delivery),
                max_inspection = VALUES(max_inspection)";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$storeId, $date, $max_pickup, $max_delivery, $max_inspection]);
            echo json_encode(['success' => true, 'message' => 'Capacity updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Daily Logistic Schedule
     */
    public function dailySchedule($date = null) {
        $date = $date ?: date('Y-m-d');
        $db = Database::getInstance()->getConnection();
        $storeId = $this->getAdminStoreLocationId();
        
        // Handle Mode (Local vs Business) filtering
        $mode = $_GET['mode'] ?? 'local'; // Default to local mode
        $modeCondition = "AND b.booking_type = 'personal'";
        if ($mode === 'business') {
            $modeCondition = "AND b.booking_type IN ('business', 'business_reservation', 'corporate')";
        } elseif ($mode === 'all') {
            $modeCondition = ""; // Show everything
        }

        // Fetch ALL ACTIVE logistics bookings for this store across all dates
        $sql = "SELECT b.*, u.fullname as customer_name, s.service_name 
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN services s ON b.service_id = s.id
                WHERE b.store_location_id = ? 
                AND b.service_option IN ('pickup', 'delivery', 'both', 'pickup_and_delivery')
                AND b.status IN ('pending', 'pending_schedule', 'scheduled', 'reschedule_requested', 'to_inspect', 'for_pickup', 'picked_up')
                AND b.is_archived = 0
                {$modeCondition}
                ORDER BY COALESCE(b.pickup_date, b.delivery_date, b.booking_date) ASC, b.created_at ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$storeId]);
        $bookings = $stmt->fetchAll();
        
        // Fetch capacity for this date
        $stmtCap = $db->prepare("SELECT * FROM store_logistic_capacities WHERE store_id = ? AND date = ?");
        $stmtCap->execute([$storeId, $date]);
        $capacity = $stmtCap->fetch();
        
        if (!$capacity) {
            $capacity = ['max_pickup' => 2, 'max_delivery' => 2, 'max_inspection' => 3];
        }
        
        // Count current logistics for this date
        $stmtCount = $db->prepare("
            SELECT 
                SUM(CASE WHEN (pickup_date = ? AND service_option IN ('pickup', 'both', 'pickup_and_delivery')) THEN 1 ELSE 0 END) as count_pickup,
                SUM(CASE WHEN (delivery_date = ? AND service_option IN ('delivery', 'both', 'pickup_and_delivery')) THEN 1 ELSE 0 END) as count_delivery
            FROM bookings 
            WHERE store_location_id = ? 
            AND status IN ('pending', 'pending_schedule', 'scheduled', 'reschedule_requested', 'to_inspect', 'for_pickup', 'picked_up')
        ");
        $stmtCount->execute([$date, $date, $storeId]);
        $counts = $stmtCount->fetch();

        $data = [
            'title' => 'Daily Logistic Schedule - ' . APP_NAME,
            'user' => $this->currentUser(),
            'date' => $date,
            'bookings' => $bookings,
            'capacity' => $capacity,
            'counts' => $counts,
            'mode' => $mode
        ];
        
        $this->view('admin/daily_schedule', $data);
    }

    public function approveLogisticRequest($id) {
        header('Content-Type: application/json');
        $db = Database::getInstance()->getConnection();
        
        try {
            // 1. Fetch Booking Details with all necessary validation source data
            $sql = "SELECT b.*, s.requires_transport, s.service_name, u.fullname as customer_name, u.phone as customer_phone, u.banned_at 
                    FROM bookings b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN users u ON b.user_id = u.id
                    WHERE b.id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            $booking = $stmt->fetch();

            if (!$booking) {
                throw new Exception("Booking not found");
            }

            // 2. VALIDATION CHECKS

            // Check Blacklist / Bad History
            if ($booking['banned_at']) {
                throw new Exception("Approval Blocked: Customer is currently on the blacklist/banned.");
            }

            // Service Type Validation
            if ($booking['requires_transport'] != 1) {
                throw new Exception("Invalid Service: This service (" . ($booking['service_name'] ?: 'Custom') . ") is marked as not requiring logistics transport.");
            }

            // Address & Contact Validation
            $address = $booking['pickup_address'] ?: $booking['delivery_address'];
            if (empty(trim($address)) || empty(trim($booking['customer_phone']))) {
                throw new Exception("Incomplete Info: Complete address and contact number are required before approval.");
            }

            // Capacity Validation (Most Important)
            $logisticDate = $booking['pickup_date'] ?: $booking['delivery_date'] ?: $booking['booking_date'];
            if (!$logisticDate) $logisticDate = date('Y-m-d'); // Fallback to avoid error
            
            $serviceOpt = strtolower(trim($booking['service_option']));
            $isPickup = in_array($serviceOpt, ['pickup', 'both', 'pickup_and_delivery']);
            $type = $isPickup ? 'pickup' : 'delivery';
            
            // Get Store Capacity Settings
            $stmtCap = $db->prepare("SELECT * FROM store_logistic_capacities WHERE store_id = ? AND date = ?");
            $stmtCap->execute([$booking['store_location_id'], $logisticDate]);
            $capSettings = $stmtCap->fetch();
            
            // Default to 2 if no setting exists
            $maxAllowed = $isPickup ? ($capSettings['max_pickup'] ?? 2) : ($capSettings['max_delivery'] ?? 2);
            
            // Count current confirmed schedules for this date and type
            $sqlCount = "SELECT COUNT(*) FROM logistics_schedule WHERE logistic_date = ? AND type = ? AND status != 'cancelled'";
            $stmtCount = $db->prepare($sqlCount);
            $stmtCount->execute([$logisticDate, $type]);
            $currentCount = $stmtCount->fetchColumn();

            if ($currentCount >= $maxAllowed) {
                throw new Exception("Capacity FULL! Already have $currentCount $type" . "s scheduled for " . date('M d', strtotime($logisticDate)) . ". Limit is $maxAllowed.");
            }

            // Duplicate Booking Check
            $sqlDup = "SELECT id FROM bookings 
                       WHERE user_id = ? AND service_id = ? AND (pickup_date = ? OR delivery_date = ?) 
                       AND status = 'scheduled' AND id != ? AND is_archived = 0";
            $stmtDup = $db->prepare($sqlDup);
            $stmtDup->execute([$booking['user_id'], $booking['service_id'], $logisticDate, $logisticDate, $id]);
            if ($stmtDup->fetch()) {
                throw new Exception("Approval Flag: This customer already has a scheduled reservation for the same service on this date.");
            }

            // 3. TRANSACTIONAL APPROVAL
            $db->beginTransaction();

            // Update Booking Table
            $updateSql = "UPDATE bookings SET status = 'approved', updated_at = NOW() WHERE id = ?";
            $stmtUpdate = $db->prepare($updateSql);
            $stmtUpdate->execute([$id]);

            // Insert into Logistics Schedule Table
            $insertSql = "INSERT INTO logistics_schedule (booking_id, logistic_date, type, status) VALUES (?, ?, ?, 'scheduled')";
            $stmtInsert = $db->prepare($insertSql);
            $stmtInsert->execute([$id, $logisticDate, $type]);

            // Optional: Hard lock update or notification could go here

            $db->commit();
            echo json_encode(['success' => true, 'message' => "Request Approved! $type scheduled for " . date('M d, Y', strtotime($logisticDate))]);

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Approval Failed: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Reject Logistic Request (AJAX)
     */
    public function rejectLogisticRequest($id) {
        header('Content-Type: application/json');
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);

            // Sync with logistics_schedule if it exists
            $syncSql = "UPDATE logistics_schedule SET status = 'cancelled' WHERE booking_id = ? AND status = 'scheduled'";
            $stmtSync = $db->prepare($syncSql);
            $stmtSync->execute([$id]);

            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Request rejected/cancelled.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Mark Logistic as Completed (AJAX)
     */
    public function completeLogisticRequest($id) {
        header('Content-Type: application/json');
        $db = Database::getInstance()->getConnection();
        
        try {
            // Check what type of logistic this was
            $stmt = $db->prepare("SELECT service_option, status FROM bookings WHERE id = ?");
            $stmt->execute([$id]);
            $booking = $stmt->fetch();
            
            $nextStatus = 'completed';
            // If it was a pickup or drop-off, it moves to 'to_inspect' for the repair workflow
            if (in_array($booking['service_option'], ['pickup', 'both', 'delivery'])) {
                $nextStatus = 'to_inspect';
            }
            
            $db->beginTransaction();
            $sql = "UPDATE bookings SET status = ?, completion_date = NOW() WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$nextStatus, $id]);

            // Sync with logistics_schedule
            $syncSql = "UPDATE logistics_schedule SET status = 'completed' WHERE booking_id = ? AND status = 'scheduled'";
            $stmtSync = $db->prepare($syncSql);
            $stmtSync->execute([$id]);

            $db->commit();
            echo json_encode(['success' => true, 'message' => "Service marked as complete. Status updated to " . str_replace('_', ' ', $nextStatus)]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Reschedule Request (AJAX)
     */
    public function rescheduleLogisticRequest() {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;
        $newDate = $_POST['new_date'] ?? null;
        $type = $_POST['type'] ?? 'pickup'; // 'pickup' or 'delivery'
        
        if (!$id || !$newDate) {
            echo json_encode(['success' => false, 'message' => 'Missing data']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            $dateColumn = ($type === 'delivery') ? 'delivery_date' : 'pickup_date';
            $sql = "UPDATE bookings SET status = 'reschedule_requested', $dateColumn = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$newDate, $id]);
            echo json_encode(['success' => true, 'message' => 'Reschedule request sent.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}


