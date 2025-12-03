<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<?php
// Data is now coming from the controller (database)
// All calculations are done in the controller
$monthlySalesData = $monthlySalesData ?? [];
$totalRevenue = $totalRevenue ?? 0;
$totalOrders = $totalOrders ?? 0;
$totalExpenses = $totalExpenses ?? 0;
$totalProfit = $totalProfit ?? 0;
$highestIncome = $highestIncome ?? 0;
$highestIncomeMonth = $highestIncomeMonth ?? 'N/A';
$currentMonthRevenue = $currentMonthRevenue ?? 0;
$currentMonthOrders = $currentMonthOrders ?? 0;
$growthPercentage = $growthPercentage ?? 0;

// Format current month revenue for display
$currentMonthRevenueFormatted = number_format($currentMonthRevenue / 1000, 0) . 'K';
$totalRevenueFormatted = number_format($totalRevenue / 1000000, 2) . 'M';
$totalExpensesFormatted = number_format($totalExpenses / 1000, 0) . 'K';
?>

<style>
/* Clickable Card Styles */
.clickable-card {
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.clickable-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25) !important;
}

.clickable-card:hover .card-body {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,249,252,0.95) 100%);
}

.clickable-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.clickable-card:hover::after {
    left: 100%;
}

.clickable-card .card-body {
    transition: all 0.3s ease;
}

.clickable-card:hover .fa-2x {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

.clickable-card:active {
    transform: translateY(-2px);
}

/* Year Search Styles */
#yearSearch {
    font-size: 1rem;
    font-weight: 600;
    border: 2px solid #4e73df;
    transition: all 0.3s ease;
}

#yearSearch:focus {
    border-color: #2e59d9;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.input-group-append .btn-primary {
    border-radius: 0 0.35rem 0.35rem 0;
    border: 2px solid #4e73df;
    border-left: none;
    transition: all 0.3s ease;
}

.input-group-append .btn-primary:hover {
    background: linear-gradient(180deg, #2e59d9 10%, #4e73df 100%);
    transform: scale(1.05);
}

/* Table Enhancements */
#monthlyTable {
    font-size: 1rem;
    border-collapse: separate;
    border-spacing: 0;
}

#monthlyTable thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#monthlyTable tbody tr {
    transition: all 0.3s ease;
    cursor: default;
}

#monthlyTable tbody tr:hover {
    background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%) !important;
    transform: translateX(5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

#monthlyTable tbody td {
    vertical-align: middle;
    border-color: #e3e6f0;
}

#monthlyTable tfoot th {
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
}

/* Badge Animations */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.1);
}

/* Responsive Table */
@media (max-width: 768px) {
    #monthlyTable {
        font-size: 0.85rem;
    }
    
    #monthlyTable thead th,
    #monthlyTable tbody td,
    #monthlyTable tfoot th {
        padding: 0.5rem !important;
    }
}

/* Chart Container */
.chart-area {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fc 100%);
    border-radius: 10px;
    padding: 15px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .input-group {
        width: 100% !important;
        margin-bottom: 10px;
    }
}

/* Booking row hover effect */
.booking-row:hover {
    background-color: #e3f2fd !important;
    transition: background-color 0.2s ease;
}

/* Item details animation */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.item-details-row {
    animation: slideDown 0.3s ease-out;
}

/* Button styling */
.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

/* Booking details card enhancement */
.booking-details-row .card {
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.booking-details-row .card-header {
    font-size: 1rem;
    font-weight: 600;
}

/* Modal Enhancements - FULL VISIBILITY */
.modal-xl {
    max-width: 1200px;
    width: 95%;
}

/* Ensure modal can scroll and is fully visible */
.modal-dialog-scrollable {
    max-height: calc(100vh - 4rem);
}

.modal-dialog-scrollable .modal-content {
    max-height: calc(100vh - 4rem);
    overflow: hidden;
}

.modal-dialog-scrollable .modal-body {
    overflow-y: auto;
}

.booking-modal-row {
    transition: all 0.3s ease;
}

.booking-modal-row:hover {
    background: linear-gradient(90deg, #e3f2fd 0%, #f8f9fc 100%) !important;
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#modalBookingsTable thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Badge enhancements */
.badge-pill {
    padding: 0.4em 0.8em;
}

.badge-lg {
    font-size: 1.1rem;
    padding: 0.5rem 1rem;
}

/* Card hover effects in modals */
#itemDetailsBody .card {
    transition: transform 0.3s ease;
}

#itemDetailsBody .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

/* Month row hover effect */
#monthlyTable tbody tr:not(.booking-details-row):not(.item-details-row):hover {
    background: linear-gradient(90deg, #e3f2fd 0%, #f8f9fc 100%) !important;
    transform: translateX(10px);
    box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
    transition: all 0.3s ease;
}


/* Modal table styling */
#modalBookingsTable tbody tr {
    border-left: 3px solid transparent;
}

#modalBookingsTable tbody tr:hover {
    border-left-color: #4e73df;
}

/* Smooth modal transitions */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    opacity: 0;
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}

/* Fix nested modal z-index and backdrop issues - AGGRESSIVE FIX */
body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
}

/* First modal (Monthly Bookings) - VISIBLE IN CONTENT AREA BESIDE SIDEBAR */
#monthBookingsModal {
    position: fixed !important;
    top: 0 !important;
    left: 224px !important; /* Account for sidebar width */
    width: calc(100% - 224px) !important; /* Full width minus sidebar */
    height: 100vh !important;
    z-index: 1050 !important;
    background: rgba(0, 0, 0, 0.5) !important;
    overflow-y: auto !important;
    padding: 1rem !important;
    display: none;
}

/* Adjust for mobile/small screens */
@media (max-width: 768px) {
    #monthBookingsModal {
        left: 0 !important;
        width: 100% !important;
    }
}

#monthBookingsModal.show {
    display: block !important;
}

#monthBookingsModal .modal-dialog {
    position: relative !important;
    z-index: 1051 !important;
    max-height: 95vh !important;
    margin: 2rem auto !important;
    width: 90% !important;
    max-width: 1200px !important;
    display: flex !important;
    flex-direction: column !important;
}

#monthBookingsModal .modal-content {
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.5) !important;
    max-height: 90vh !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    position: relative !important;
}

#monthBookingsModal .modal-header {
    flex-shrink: 0 !important;
    position: relative !important;
    z-index: 1 !important;
}

#monthBookingsModal .modal-body {
    overflow-y: auto !important;
    flex: 1 1 auto !important;
    min-height: 0 !important;
    position: relative !important;
    z-index: 1 !important;
}

#monthBookingsModal .modal-footer {
    flex-shrink: 0 !important;
    position: relative !important;
    z-index: 1 !important;
}

/* Second modal (Item Details) - MUCH HIGHER Z-INDEX */
#itemDetailsModal {
    z-index: 9999 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    overflow: auto !important;
}

#itemDetailsModal.show {
    display: block !important;
}

#itemDetailsModal .modal-dialog {
    z-index: 10000 !important;
    pointer-events: auto !important;
    position: relative !important;
}

#itemDetailsModal .modal-content {
    z-index: 10001 !important;
    pointer-events: auto !important;
    position: relative !important;
}

/* Force all modal backdrops to NEVER BLOCK - CRITICAL FIX */
.modal-backdrop {
    z-index: 1040 !important;
    pointer-events: none !important; /* Backdrop never blocks clicks */
}

.modal-backdrop.show {
    pointer-events: none !important; /* Even when visible, don't block */
}

.modal-backdrop + .modal-backdrop {
    z-index: 9998 !important;
    pointer-events: none !important;
}

/* When item details modal is open, its backdrop should be high */
#itemDetailsModal ~ .modal-backdrop,
#itemDetailsModal + .modal-backdrop {
    z-index: 9998 !important;
}

/* Make ALL modal content clickable - FORCE IT */
.modal-content {
    position: relative !important;
    z-index: 1 !important;
    pointer-events: auto !important;
}

.modal-dialog {
    pointer-events: auto !important;
}

.modal-body {
    pointer-events: auto !important;
}

.modal-header {
    pointer-events: auto !important;
}

.modal-footer {
    pointer-events: auto !important;
}

/* Ensure ALL buttons in modals are clickable - FORCE IT */
.modal .btn,
.modal button,
.modal .close {
    pointer-events: auto !important;
    cursor: pointer !important;
    position: relative !important;
    z-index: 1 !important;
}

/* Make sure cards in item details modal are clickable */
#itemDetailsBody .card,
#itemDetailsBody .card-body,
#itemDetailsBody .card-header {
    pointer-events: auto !important;
}

/* Fix modal overflow for nested modals */
.modal.show {
    overflow-y: auto !important;
}

/* Ensure everything in modals is always clickable */
#monthBookingsModal .btn,
#monthBookingsModal button,
#monthBookingsModal table tr,
#monthBookingsModal a,
#monthBookingsModal .close {
    cursor: pointer !important;
}

/* Nested modal still gets backdrop */
#itemDetailsModal {
    background: rgba(0, 0, 0, 0.7) !important;
}

/* Ensure close button is always on top and clickable */
.modal-header .close {
    z-index: 10002 !important;
    pointer-events: auto !important;
    cursor: pointer !important;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-2 text-gray-800">Sales & Revenue Report</h1>
    <div class="d-flex align-items-center">
        <button class="btn btn-secondary btn-sm mr-2" onclick="window.print()">
            <i class="fas fa-download"></i> Export PDF
        </button>
        <button class="btn btn-success btn-sm" onclick="refreshData()">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>

<!-- YEAR SEARCH - PROMINENT -->
<div class="card shadow-lg mb-4" style="border-left: 5px solid #4e73df;">
    <div class="card-body" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-2" style="color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-calendar-alt mr-2" style="color: #4e73df;"></i>
                    Search by Year
                </h5>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-info-circle mr-1"></i>
                    Enter a year (2010-2025) to view historical data
                </p>
            </div>
            <div class="col-md-6">
                <div class="input-group" style="max-width: 400px; margin-left: auto;">
                    <input type="number" 
                           class="form-control form-control-lg" 
                           id="yearSearch" 
                           placeholder="Enter year (e.g., 2011, 2020, 2025)" 
                           value="<?php echo $selectedYear ?? date('Y'); ?>"
                           min="2000" 
                           max="<?php echo date('Y') + 5; ?>"
                           style="border: 2px solid #4e73df; font-size: 1.1rem; font-weight: 600;">
                    <div class="input-group-append">
                        <button class="btn btn-primary btn-lg" type="button" onclick="searchByYear()" style="padding: 0.5rem 2rem;">
                            <i class="fas fa-search mr-2"></i> Search Year
                        </button>
                    </div>
                </div>
                <small class="text-muted d-block mt-2 text-right">
                    Press Enter or click Search button
                </small>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mb-4" style="border-left: 5px solid #4e73df;">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h6 class="mb-0">
                <i class="fas fa-calendar-check mr-2"></i>
                <strong>Currently Viewing: Year <?php echo $selectedYear ?? date('Y'); ?></strong>
            </h6>
            <small class="text-muted">
                <i class="fas fa-check-circle text-success mr-1"></i>
                Based on <strong>completed bookings only</strong> (status = completed & payment = paid)
            </small>
        </div>
        <div class="col-md-6 text-right">
            <small class="text-muted">
                <i class="fas fa-database mr-1"></i><strong>Available Years:</strong> 
                <?php 
                $years = $availableYears ?? [date('Y')];
                if (empty($years)) {
                    echo '<span class="badge badge-warning">No data yet - Please seed test data</span>';
                } else {
                    echo implode(', ', $years); 
                }
                ?>
            </small>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 clickable-card" onclick="scrollToSection('monthly-breakdown')" title="Click to view revenue details">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Revenue (<?php echo $selectedYear ?? date('Y'); ?>)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($totalRevenue, 0); ?></div>
                        <div class="text-xs text-muted">Yearly Total</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4" id="profit-section">
        <div class="card border-left-success shadow h-100 py-2 clickable-card" onclick="scrollToSection('monthly-breakdown')" title="Click to view profit breakdown">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Profit (<?php echo $selectedYear ?? date('Y'); ?>)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($totalProfit, 0); ?></div>
                        <div class="text-xs text-muted">Net earnings</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 clickable-card" onclick="scrollToSection('monthly-breakdown')" title="Click to view monthly breakdown">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Highest Monthly Income</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($highestIncome, 0); ?></div>
                        <div class="text-xs text-muted"><?php echo $highestIncomeMonth; ?> <?php echo $selectedYear ?? date('Y'); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 clickable-card" onclick="scrollToSection('monthly-breakdown')" title="Click to view orders breakdown">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Orders (<?php echo $selectedYear ?? date('Y'); ?>)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalOrders); ?></div>
                        <div class="text-xs text-muted">Completed</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-2"></i>Yearly Income Trend - <?php echo $selectedYear ?? date('Y'); ?>
                    </h6>
                    <small class="text-muted">Monthly revenue, profit and expenses comparison</small>
                </div>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area" style="position: relative; height: 350px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Yearly Summary - <?php echo $selectedYear ?? date('Y'); ?>
                    </h6>
                    <small class="text-muted">Total breakdown</small>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="monthlySummaryChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Revenue
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Profit
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> Expenses
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Data Table -->
<div class="card shadow mb-4" id="monthly-breakdown">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-table mr-2"></i>Monthly Breakdown - Year <?php echo $selectedYear ?? date('Y'); ?>
            </h5>
                <div>
                <span class="badge badge-light" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <i class="fas fa-calendar-alt mr-2"></i>12 Months Complete View
                </span>
                </div>
            </div>
                                        </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="monthlyTable" width="100%" cellspacing="0" data-skip-datatable="true">
                <thead style="background: linear-gradient(135deg, #f8f9fc 0%, #e7e9ed 100%); position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="padding: 1rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: #2c3e50; border-bottom: 3px solid #4e73df;">
                            <i class="fas fa-calendar mr-2" style="color: #4e73df;"></i>Month
                        </th>
                        <th class="text-center" style="padding: 1rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: #2c3e50; border-bottom: 3px solid #4e73df;">
                            <i class="fas fa-shopping-cart mr-2" style="color: #4e73df;"></i>Orders
                        </th>
                        <th class="text-right" style="padding: 1rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: #2c3e50; border-bottom: 3px solid #4e73df;">
                            <i class="fas fa-money-bill-wave mr-2" style="color: #1cc88a;"></i>Revenue
                        </th>
                        <th class="text-right" style="padding: 1rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: #2c3e50; border-bottom: 3px solid #4e73df;">
                            <i class="fas fa-chart-line mr-2" style="color: #e74a3b;"></i>Expenses
                        </th>
                        <th class="text-right" style="padding: 1rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: #2c3e50; border-bottom: 3px solid #4e73df;">
                            <i class="fas fa-coins mr-2" style="color: #36b9cc;"></i>Profit
                        </th>
                        <th class="text-center" style="padding: 1rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: #2c3e50; border-bottom: 3px solid #4e73df;">
                            <i class="fas fa-percentage mr-2" style="color: #f6c23e;"></i>Margin %
                        </th>
                            </tr>
                        </thead>
                <tbody id="monthlyTableBody">
                            <?php 
                    if (empty($monthlySalesData)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3" style="display: block;"></i>
                                <h5 class="text-muted">No Data Available for Year <?php echo $selectedYear ?? date('Y'); ?></h5>
                                <p class="text-muted">Please seed test data or check if there are bookings for this year.</p>
                                <a href="<?php echo BASE_URL; ?>database/seed_2024_2025_data.php" class="btn btn-primary mt-3">
                                    <i class="fas fa-bolt mr-2"></i>Quick Seed 2024-2025 Data
                                </a>
                            </td>
                        </tr>
                    <?php else:
                        foreach ($monthlySalesData as $index => $data): 
                            $profitMargin = ($data['revenue'] > 0) ? (($data['profit'] / $data['revenue']) * 100) : 0;
                            $rowStyle = ($index % 2 == 0) ? 'background: #ffffff;' : 'background: #f8f9fc;';
                            $hasData = $data['orders'] > 0;
                    ?>
                    <tr style="<?php echo $rowStyle; ?> transition: all 0.2s ease; cursor: <?php echo $hasData ? 'pointer' : 'default'; ?>;" 
                        data-month-index="<?php echo $index; ?>"
                        <?php if ($hasData): ?>
                        onclick="openMonthModal(<?php echo $index; ?>, '<?php echo $data['month']; ?>', <?php echo $data['orders']; ?>, <?php echo $data['revenue']; ?>)"
                        title="Click to view all bookings for <?php echo $data['month']; ?>"
                        <?php endif; ?>>
                        <td class="font-weight-bold" style="padding: 0.75rem; font-size: 1rem; color: <?php echo $hasData ? '#4e73df' : '#858796'; ?>;">
                            <i class="far fa-calendar-alt mr-2"></i><?php echo $data['month']; ?>
                        </td>
                        <td class="text-center font-weight-bold" style="padding: 0.75rem; font-size: 1rem; color: <?php echo $hasData ? '#2c3e50' : '#858796'; ?>;">
                            <?php echo $hasData ? number_format($data['orders']) : '0'; ?>
                        </td>
                        <td class="text-right font-weight-bold" style="padding: 0.75rem; font-size: 1rem; color: <?php echo $hasData ? '#1cc88a' : '#858796'; ?>;">
                            ₱<?php echo number_format($data['revenue'], 2); ?>
                        </td>
                        <td class="text-right" style="padding: 0.75rem; font-size: 1rem; color: <?php echo $hasData ? '#e74a3b' : '#858796'; ?>;">
                            ₱<?php echo number_format($data['expenses'], 2); ?>
                        </td>
                        <td class="text-right font-weight-bold" style="padding: 0.75rem; font-size: 1rem; color: <?php echo $hasData ? '#36b9cc' : '#858796'; ?>;">
                            ₱<?php echo number_format($data['profit'], 2); ?>
                        </td>
                        <td class="text-center" style="padding: 0.75rem;">
                            <?php if ($hasData): ?>
                                <span class="badge" style="font-size: 0.9rem; padding: 0.5rem 1rem; background: <?php 
                                    echo $profitMargin > 70 ? 'linear-gradient(135deg, #1cc88a 0%, #17a673 100%)' : 
                                        ($profitMargin > 60 ? 'linear-gradient(135deg, #f6c23e 0%, #dda20a 100%)' : 
                                        'linear-gradient(135deg, #858796 0%, #6c757d 100%)'); 
                                ?>; color: white; font-weight: 700;">
                                    <?php echo number_format($profitMargin, 2); ?>%
                                    </span>
                                    <?php else: ?>
                                <span class="badge badge-secondary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">0.00%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                    <?php 
                    // Store booking details in a hidden script tag for JavaScript access
                    if ($hasData && !empty($data['booking_details'])): 
                    ?>
                    <script type="application/json" id="month-data-<?php echo $index; ?>">
                    <?php
                        $bookings = explode(';;', $data['booking_details']);
                        $bookingsArray = [];
                        foreach ($bookings as $booking) {
                            if (empty(trim($booking))) continue;
                            $parts = [];
                            foreach (explode('|', $booking) as $part) {
                                $colonPos = strpos($part, ':');
                                if ($colonPos !== false) {
                                    $key = substr($part, 0, $colonPos);
                                    $value = substr($part, $colonPos + 1);
                                    $parts[$key] = $value;
                                }
                            }
                            $bookingsArray[] = $parts;
                        }
                        echo json_encode($bookingsArray);
                    ?>
                    </script>
                    <?php endif; ?>
                            <?php 
                                endforeach;
                    endif; 
                    ?>
                </tbody>
                <?php if (!empty($monthlySalesData)): ?>
                <tfoot style="background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); color: white; position: sticky; bottom: 0;">
                    <tr>
                        <th style="padding: 1.25rem; font-size: 1.1rem; font-weight: 700;">
                            <i class="fas fa-calculator mr-2"></i>YEARLY TOTAL
                        </th>
                        <th class="text-center" style="padding: 1.25rem; font-size: 1.1rem; font-weight: 700;">
                            <?php echo number_format($totalOrders); ?>
                        </th>
                        <th class="text-right" style="padding: 1.25rem; font-size: 1.1rem; font-weight: 700; color: #1cc88a;">
                            ₱<?php echo number_format($totalRevenue, 2); ?>
                        </th>
                        <th class="text-right" style="padding: 1.25rem; font-size: 1.1rem; font-weight: 700; color: #e74a3b;">
                            ₱<?php echo number_format($totalExpenses, 2); ?>
                        </th>
                        <th class="text-right" style="padding: 1.25rem; font-size: 1.1rem; font-weight: 700; color: #36b9cc;">
                            ₱<?php echo number_format($totalProfit, 2); ?>
                        </th>
                        <th class="text-center" style="padding: 1.25rem;">
                            <span class="badge badge-light" style="font-size: 1rem; padding: 0.6rem 1.2rem; font-weight: 700; color: #2c3e50;">
                                <?php echo number_format(($totalRevenue > 0) ? (($totalProfit / $totalRevenue) * 100) : 0, 2); ?>%
                            </span>
                        </th>
                            </tr>
                </tfoot>
                <?php endif; ?>
                    </table>
        </div>
    </div>
</div>


<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" crossorigin="anonymous"></script>

<script>
// Prevent any caching issues and clear service workers
(function() {
    // Clear caches
    if ('caches' in window) {
        caches.keys().then(function(names) {
            for (let name of names) {
                caches.delete(name);
            }
        }).catch(function(err) {
            console.warn('Cache clearing failed:', err);
        });
    }
    
    // Unregister service workers
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for (let registration of registrations) {
                registration.unregister();
            }
        }).catch(function(err) {
            console.warn('Service worker unregistration failed:', err);
        });
    }
})();
</script>

<script>
// Chart.js configuration
var ctxRevenue = document.getElementById('revenueChart');
var chartMonths = <?php echo $chartMonths ?? json_encode([]); ?>;
var chartRevenues = <?php echo $chartRevenues ?? json_encode([]); ?>;
var chartProfits = <?php echo $chartProfits ?? json_encode([]); ?>;
var chartExpenses = <?php echo $chartExpenses ?? json_encode([]); ?>;

// Revenue Line Chart - Yearly Income Display
var revenueChart = new Chart(ctxRevenue, {
    type: 'line',
    data: {
        labels: chartMonths,
        datasets: [{
            label: 'Revenue',
            data: chartRevenues,
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgb(78, 115, 223)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            tension: 0.4,
            fill: true
        }, {
            label: 'Profit',
            data: chartProfits,
            borderColor: 'rgb(28, 200, 138)',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgb(28, 200, 138)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            tension: 0.4,
            fill: true
        }, {
            label: 'Expenses',
            data: chartExpenses,
            borderColor: 'rgb(231, 74, 59)',
            backgroundColor: 'rgba(231, 74, 59, 0.1)',
            borderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgb(231, 74, 59)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    },
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    color: '#5a5c69'
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    color: '#5a5c69'
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 13,
                        weight: '600'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    title: function(context) {
                        return context[0].label + ' <?php echo $selectedYear ?? date("Y"); ?>';
                    },
                    label: function(context) {
                        return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            },
            title: {
                display: true,
                text: 'Monthly Income Trend for Year <?php echo $selectedYear ?? date("Y"); ?>',
                font: {
                    size: 16,
                    weight: 'bold'
                },
                padding: {
                    bottom: 20
                },
                color: '#2c3e50'
            }
        }
    }
});

// Monthly Summary Pie Chart
var ctxSummary = document.getElementById('monthlySummaryChart');
var summaryChart = new Chart(ctxSummary, {
    type: 'doughnut',
    data: {
        labels: ['Revenue', 'Profit', 'Expenses'],
        datasets: [{
            data: [<?php echo $totalRevenue ?? 0; ?>, <?php echo $totalProfit ?? 0; ?>, <?php echo $totalExpenses ?? 0; ?>],
            backgroundColor: ['#4e73df', '#1cc88a', '#e74a3b'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#c0392b'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(context) {
                    var label = context.label || '';
                    var value = context.parsed;
                    return label + ': ₱' + value.toLocaleString();
                }
            }
        },
        legend: {
            display: false
        },
        cutout: '80%'
    }
});

function refreshData() {
    location.reload();
}

// Scroll to section function
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        // Add highlight effect
        element.style.transition = 'box-shadow 0.3s ease';
        element.style.boxShadow = '0 0 20px rgba(78, 115, 223, 0.5)';
        setTimeout(() => {
            element.style.boxShadow = '';
        }, 2000);
    }
}

// Search by year function
function searchByYear() {
    const yearInput = document.getElementById('yearSearch');
    const year = yearInput.value.trim();
    
    // Validate year
    if (!year || year.length !== 4) {
        alert('Please enter a valid 4-digit year (e.g., 2025)');
        yearInput.focus();
        return;
    }
    
    const currentYear = new Date().getFullYear();
    const yearNum = parseInt(year);
    
    if (yearNum < 2000 || yearNum > currentYear + 5) {
        alert('Please enter a year between 2000 and ' + (currentYear + 5));
        yearInput.focus();
        return;
    }
    
    // Show loading indicator
    const searchBtn = document.querySelector('.input-group-append .btn-primary');
    if (searchBtn) {
        const originalHTML = searchBtn.innerHTML;
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        searchBtn.disabled = true;
    }
    
    // Redirect to reports page with year parameter
    window.location.href = '<?php echo BASE_URL; ?>admin/reports/' + year;
}

// Suppress console warnings for DataTable (clean console output)
if (typeof console !== 'undefined' && console.warn) {
    const originalWarn = console.warn;
    console.warn = function(...args) {
        const message = args.join(' ');
        // Filter out DataTable warnings for our custom tables
        if (message.includes('modalBookingsTable') || 
            message.includes('monthlyTable') ||
            message.includes('Resource was not cached')) {
            return; // Suppress these specific warnings
        }
        originalWarn.apply(console, args);
    };
}

// Open modal with month bookings
function openMonthModal(monthIndex, monthName, totalOrders, totalRevenue) {
    // Get booking data from hidden script tag
    const dataElement = document.getElementById('month-data-' + monthIndex);
    if (!dataElement) {
        alert('No bookings found for this month');
        return;
    }
    
    let bookingsData = [];
    try {
        bookingsData = JSON.parse(dataElement.textContent);
    } catch (e) {
        console.error('Error parsing booking data:', e);
        alert('Error loading booking data');
        return;
    }
    
    // Fix backdrop blocking issue BEFORE opening modal
    if (typeof jQuery !== 'undefined') {
        // Remove any existing backdrop that might be blocking
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    }
    
    // Update modal title and summary
    document.getElementById('modalMonthName').textContent = monthName + ' <?php echo $selectedYear ?? date('Y'); ?>';
    document.getElementById('modalTotalBookings').textContent = totalOrders;
    document.getElementById('modalTotalRevenue').textContent = '₱' + parseFloat(totalRevenue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Populate bookings table
    const tbody = document.getElementById('modalBookingsBody');
    tbody.innerHTML = '';
    
    bookingsData.forEach((booking, index) => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.className = 'booking-modal-row';
        row.setAttribute('data-booking-index', index);
        
        const dateObj = new Date(booking.Date);
        const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        row.innerHTML = `
            <td><span class="badge badge-primary badge-pill">#${booking.ID || 'N/A'}</span></td>
            <td><strong>${escapeHtml(booking.Customer || 'N/A')}</strong></td>
            <td><small class="text-muted">${escapeHtml(truncateText(booking.Service || 'N/A', 40))}</small></td>
            <td><small>${formattedDate}</small></td>
            <td class="text-right"><strong class="text-success">₱${parseFloat(booking.Amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
            <td class="text-center">
                <button class="btn btn-sm btn-info view-items-btn" data-booking-index="${index}">
                    <i class="fas fa-eye"></i> View Items
                </button>
            </td>
        `;
        
        // Add click handler for view items button
        const viewBtn = row.querySelector('.view-items-btn');
        if (viewBtn) {
            viewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                showItemDetails(index, booking);
            });
        }
        
        // Add click handler for row (but not button)
        row.onclick = function(e) {
            // Don't trigger if clicking button or icon
            if (!e.target.closest('button') && !e.target.closest('.btn')) {
                showItemDetails(index, booking);
            }
        };
        
        tbody.appendChild(row);
    });
    
    // Show the modal WITHOUT backdrop and ensure it's visible
    if (typeof jQuery !== 'undefined') {
        $('#monthBookingsModal').modal({
            backdrop: false, // NO backdrop - nothing to block clicks
            keyboard: true,
            focus: true,
            show: true
        });
        
        // Force modal to be visible and properly positioned in content area
        $('#monthBookingsModal').on('shown.bs.modal', function() {
            const $modal = $(this);
            
            // Calculate sidebar width (usually 224px or check actual width)
            const sidebarWidth = $('.sidebar').outerWidth() || 224;
            
            // Ensure modal is visible in content area (beside sidebar)
            $modal.css({
                'display': 'block',
                'position': 'fixed',
                'top': '0',
                'left': sidebarWidth + 'px',
                'width': 'calc(100% - ' + sidebarWidth + 'px)',
                'height': '100vh',
                'z-index': '1050',
                'overflow-y': 'auto',
                'padding': '1rem'
            });
            
            // Ensure dialog is centered and visible
            const $dialog = $modal.find('.modal-dialog');
            $dialog.css({
                'margin': '2rem auto',
                'max-height': 'calc(100vh - 4rem)',
                'position': 'relative',
                'width': '90%',
                'max-width': '1200px'
            });
            
            // Scroll to top of modal
            $modal.scrollTop(0);
            
            console.log('Modal positioned at left:', sidebarWidth, 'px');
        });
    }
}

// Show item details in nested modal
function showItemDetails(index, booking) {
    // Prevent backdrop issues with nested modals
    if (typeof jQuery !== 'undefined') {
        // Temporarily hide the first modal's backdrop
        $('#monthBookingsModal').css('overflow', 'hidden');
    }
    
    const itemDetailsBody = document.getElementById('itemDetailsBody');
    
    itemDetailsBody.innerHTML = `
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="alert alert-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="fas fa-receipt mr-2"></i>Booking #${booking.ID || 'N/A'}</strong>
                        </div>
                        <div>
                            <span class="badge badge-success badge-lg">₱${parseFloat(booking.Amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="fas fa-tools mr-2"></i>Service Details</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="font-weight-bold" style="width: 40%;">Service Name:</td>
                                <td>${escapeHtml(booking.Service || 'N/A')}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Service Type:</td>
                                <td>${escapeHtml(booking.ServiceType || 'N/A')}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Customer:</td>
                                <td>${escapeHtml(booking.Customer || 'N/A')}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white">
                        <strong><i class="fas fa-box mr-2"></i>Item Details</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="font-weight-bold" style="width: 40%;">Item Type:</td>
                                <td>${escapeHtml(booking.ItemType || 'N/A')}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Description:</td>
                                <td>${escapeHtml(booking.Item || 'N/A')}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Date:</td>
                                <td>${new Date(booking.Date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        ${(booking.Notes && booking.Notes !== 'N/A') ? `
        <div class="row">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <strong><i class="fas fa-sticky-note mr-2"></i>Additional Notes</strong>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${escapeHtml(booking.Notes).replace(/\n/g, '<br>')}</p>
                    </div>
                </div>
            </div>
        </div>
        ` : ''}
    `;
    
    // Show the item details modal with AGGRESSIVE z-index and clickability fixes
    if (typeof jQuery !== 'undefined') {
        // Remove any existing event handlers to prevent duplicates
        $('#itemDetailsModal').off('shown.bs.modal hidden.bs.modal');
        
        // Show the modal
        $('#itemDetailsModal').modal({
            backdrop: true,
            keyboard: true,
            focus: true,
            show: true
        });
        
        // AGGRESSIVE FIX: Ensure modal is on top and clickable
        $('#itemDetailsModal').on('shown.bs.modal', function() {
            const $modal = $(this);
            const $dialog = $modal.find('.modal-dialog');
            const $content = $modal.find('.modal-content');
            
            // Force very high z-index
            $modal.css({
                'z-index': '9999',
                'pointer-events': 'none',
                'display': 'block',
                'position': 'fixed'
            });
            
            $dialog.css({
                'z-index': '10000',
                'pointer-events': 'auto',
                'position': 'relative'
            });
            
            $content.css({
                'z-index': '10001',
                'pointer-events': 'auto',
                'position': 'relative'
            });
            
            // Fix backdrop z-index
            $('.modal-backdrop').each(function(index) {
                if (index === 0) {
                    $(this).css('z-index', '1040');
                } else {
                    $(this).css('z-index', '9998');
                }
            });
            
            // Ensure all buttons are clickable
            $modal.find('.btn, button, .close').css({
                'pointer-events': 'auto',
                'cursor': 'pointer',
                'z-index': '1',
                'position': 'relative'
            });
            
            // Make sure modal is appended to body
            $modal.appendTo('body');
        });
        
        // Restore when nested modal closes
        $('#itemDetailsModal').on('hidden.bs.modal', function() {
            $('#monthBookingsModal').css('overflow', 'auto');
            $('.modal-backdrop:first').css('z-index', '1040');
        });
    }
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Helper function to truncate text
function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Allow Enter key to trigger search
document.addEventListener('DOMContentLoaded', function() {
    const yearInput = document.getElementById('yearSearch');
    if (yearInput) {
        yearInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchByYear();
            }
        });
        
        // Auto-focus on year input
        yearInput.focus();
    }
    
    // Prevent DataTable initialization on custom tables
    const monthlyTable = document.getElementById('monthlyTable');
    if (monthlyTable) {
        monthlyTable.setAttribute('data-no-datatable', 'true');
        if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable && jQuery.fn.DataTable.isDataTable('#monthlyTable')) {
            jQuery('#monthlyTable').DataTable().destroy();
        }
    }
    
    // Prevent DataTable on modal table
    const modalTable = document.getElementById('modalBookingsTable');
    if (modalTable) {
        modalTable.setAttribute('data-no-datatable', 'true');
        if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable && jQuery.fn.DataTable.isDataTable('#modalBookingsTable')) {
            jQuery('#modalBookingsTable').DataTable().destroy();
        }
    }
    
    // Add hover effect to main month rows only (not detail rows)
    const tableRows = document.querySelectorAll('#monthlyTable tbody tr:not(.booking-details-row):not(.item-details-row)');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            if (!this.classList.contains('booking-details-row') && !this.classList.contains('item-details-row')) {
                this.style.backgroundColor = '#f8f9fc';
                this.style.transition = 'all 0.2s ease';
            }
        });
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('booking-details-row') && !this.classList.contains('item-details-row')) {
                this.style.backgroundColor = '';
            }
        });
    });
});
</script>

<!-- Monthly Bookings Modal -->
<div class="modal fade" id="monthBookingsModal" tabindex="-1" role="dialog" aria-labelledby="monthBookingsModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="true" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050; overflow-y: auto;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-height: 95vh; margin: 2rem auto; position: relative;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
                <h5 class="modal-title" id="monthBookingsModalLabel">
                    <i class="fas fa-calendar-alt mr-2"></i><span id="modalMonthName">Month</span> - Completed Bookings
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: #f8f9fc;">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-left-primary shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bookings</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="modalTotalBookings">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="modalTotalRevenue">₱0.00</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bookings List -->
                <div class="card shadow">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list mr-2"></i>All Completed Bookings
                        </h6>
                        <small class="text-muted">Click on any booking to view detailed item information</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="modalBookingsTable" data-skip-datatable="true" data-no-datatable="true">
                                <thead style="background: linear-gradient(135deg, #f8f9fc 0%, #e7e9ed 100%);">
                                    <tr>
                                        <th style="width: 10%;">Booking ID</th>
                                        <th style="width: 20%;">Customer</th>
                                        <th style="width: 25%;">Service</th>
                                        <th style="width: 15%;">Date</th>
                                        <th style="width: 15%;" class="text-right">Amount</th>
                                        <th style="width: 15%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="modalBookingsBody">
                                    <!-- Bookings will be inserted here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <small class="text-muted mr-auto">
                    <i class="fas fa-check-circle text-success mr-1"></i>
                    All bookings shown have status "completed" and payment "paid"
                </small>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Item Details Modal (nested) -->
<div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="itemDetailsModalLabel" aria-hidden="true" data-backdrop="true" data-keyboard="true" style="z-index: 9999 !important; pointer-events: none;">
    <div class="modal-dialog modal-lg" role="document" style="z-index: 10000 !important; pointer-events: auto !important;">
        <div class="modal-content" style="z-index: 10001 !important; pointer-events: auto !important;">
            <div class="modal-header bg-info text-white" style="pointer-events: auto !important; z-index: 1 !important;">
                <h5 class="modal-title" id="itemDetailsModalLabel">
                    <i class="fas fa-shopping-bag mr-2"></i>Purchased Items & Services
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="z-index: 10002 !important; pointer-events: auto !important; cursor: pointer !important;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="itemDetailsBody" style="pointer-events: auto !important;">
                <!-- Item details will be inserted here -->
            </div>
            <div class="modal-footer" style="pointer-events: auto !important;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="pointer-events: auto !important; cursor: pointer !important; z-index: 1 !important;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Additional Insights Section -->
<?php if (!empty($monthlySalesData) && $totalRevenue > 0): ?>
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); color: white;">
                <h5 class="m-0 font-weight-bold">
                    <i class="fas fa-lightbulb mr-2"></i>Financial Insights - Year <?php echo $selectedYear ?? date('Y'); ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Average Monthly Revenue -->
                    <div class="col-md-3 mb-3">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #4e73df15 0%, #224abe15 100%); border-radius: 10px; border-left: 4px solid #4e73df;">
                            <div class="text-muted small mb-2">
                                <i class="fas fa-chart-bar"></i> AVG MONTHLY REVENUE
                            </div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #4e73df;">
                                ₱<?php echo number_format($averageRevenue ?? 0, 2); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Average Monthly Profit -->
                    <div class="col-md-3 mb-3">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #1cc88a15 0%, #17a67315 100%); border-radius: 10px; border-left: 4px solid #1cc88a;">
                            <div class="text-muted small mb-2">
                                <i class="fas fa-coins"></i> AVG MONTHLY PROFIT
                            </div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #1cc88a;">
                                ₱<?php echo number_format($averageProfit ?? 0, 2); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Average Orders Per Month -->
                    <div class="col-md-3 mb-3">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #f6c23e15 0%, #dda20a15 100%); border-radius: 10px; border-left: 4px solid #f6c23e;">
                            <div class="text-muted small mb-2">
                                <i class="fas fa-shopping-cart"></i> AVG ORDERS/MONTH
                            </div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #f6c23e;">
                                <?php echo number_format($totalOrders / 12, 1); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overall Profit Margin -->
                    <div class="col-md-3 mb-3">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #36b9cc15 0%, #25839115 100%); border-radius: 10px; border-left: 4px solid #36b9cc;">
                            <div class="text-muted small mb-2">
                                <i class="fas fa-percentage"></i> PROFIT MARGIN
                            </div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #36b9cc;">
                                <?php echo number_format(($totalRevenue > 0) ? (($totalProfit / $totalRevenue) * 100) : 0, 2); ?>%
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Key Performance Indicators -->
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-chart-pie mr-2"></i>Key Performance Indicators
                        </h6>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="padding: 1rem; background: #f8f9fc; border-radius: 8px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fas fa-trophy text-warning mr-2"></i>Best Month
                                </span>
                                <span class="font-weight-bold text-primary"><?php echo $highestIncomeMonth; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="padding: 1rem; background: #f8f9fc; border-radius: 8px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fas fa-arrow-up text-success mr-2"></i>Total Transactions
                                </span>
                                <span class="font-weight-bold text-success"><?php echo number_format($totalOrders); ?> bookings</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="padding: 1rem; background: #f8f9fc; border-radius: 8px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fas fa-calculator text-info mr-2"></i>Expense Ratio
                                </span>
                                <span class="font-weight-bold text-danger">30.00%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-muted">
                            <i class="fas fa-info-circle mr-2"></i>Need to add more data?
                        </h6>
                        <small class="text-muted">Generate test data for different years to compare trends</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="<?php echo BASE_URL; ?>database/" class="btn btn-primary">
                            <i class="fas fa-database mr-2"></i>Data Management
                        </a>
                        <a href="<?php echo BASE_URL; ?>database/seed_2024_2025_data.php" class="btn btn-success">
                            <i class="fas fa-bolt mr-2"></i>Quick Seed Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

