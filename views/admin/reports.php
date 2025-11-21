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
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-2 text-gray-800">Sales & Revenue Report</h1>
    <div>
        <button class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fas fa-download"></i> Export PDF
        </button>
        <button class="btn btn-success btn-sm" onclick="refreshData()">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>

<p class="mb-4">Monthly sales performance and income analysis</p>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 clickable-card" onclick="scrollToSection('monthly-breakdown')" title="Click to view revenue details">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($totalRevenue, 0); ?></div>
                        <div class="text-xs text-muted">Year to date</div>
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
                            Total Profit</div>
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
                            Highest Income</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($highestIncome, 0); ?></div>
                        <div class="text-xs text-muted"><?php echo $highestIncomeMonth; ?></div>
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
                            Total Orders</div>
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
                <h6 class="m-0 font-weight-bold text-primary">Revenue & Profit Trend</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Monthly Summary</h6>
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
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Monthly Breakdown</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="monthlyTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                        <th>Expenses</th>
                        <th>Profit</th>
                        <th>Profit Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthlySalesData as $data): 
                        $profitMargin = ($data['revenue'] > 0) ? (($data['profit'] / $data['revenue']) * 100) : 0;
                    ?>
                    <tr>
                        <td><?php echo $data['month']; ?></td>
                        <td><?php echo $data['orders']; ?></td>
                        <td>₱<?php echo number_format($data['revenue'], 2); ?></td>
                        <td>₱<?php echo number_format($data['expenses'], 2); ?></td>
                        <td>₱<?php echo number_format($data['profit'], 2); ?></td>
                        <td><?php echo number_format($profitMargin, 2); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th><?php echo number_format($totalOrders); ?></th>
                        <th>₱<?php echo number_format($totalRevenue, 2); ?></th>
                        <th>₱<?php echo number_format($totalExpenses, 2); ?></th>
                        <th>₱<?php echo number_format($totalProfit, 2); ?></th>
                        <th><?php echo number_format(($totalRevenue > 0) ? (($totalProfit / $totalRevenue) * 100) : 0, 2); ?>%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
// Chart.js configuration
var ctxRevenue = document.getElementById('revenueChart');
var chartMonths = <?php echo $chartMonths ?? json_encode([]); ?>;
var chartRevenues = <?php echo $chartRevenues ?? json_encode([]); ?>;
var chartProfits = <?php echo $chartProfits ?? json_encode([]); ?>;
var chartExpenses = <?php echo $chartExpenses ?? json_encode([]); ?>;

// Revenue Line Chart
var revenueChart = new Chart(ctxRevenue, {
    type: 'line',
    data: {
        labels: chartMonths,
        datasets: [{
            label: 'Revenue',
            data: chartRevenues,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.3
        }, {
            label: 'Profit',
            data: chartProfits,
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            tension: 0.3
        }, {
            label: 'Expenses',
            data: chartExpenses,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString();
                    }
                }
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

// Initialize DataTable
$(document).ready(function() {
    $('#monthlyTable').DataTable({
        "order": [[ 0, "asc" ]],
        "pageLength": 12
    });
});
</script>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

