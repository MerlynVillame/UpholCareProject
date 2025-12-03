<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#generateReportModal"><i
            class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Bookings</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalBookings; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo number_format($totalRevenue, 2); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Bookings
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $pendingBookings; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Completed Bookings</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completedBookings; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->

<div class="row">

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dropdown Header:</div>
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Service Distribution</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dropdown Header:</div>
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Vehicle Covers
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Bedding
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Furniture
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Pending Reservations -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-clock mr-2"></i>Pending Reservations
                </h6>
                <a href="<?php echo BASE_URL; ?>admin/allBookings" class="btn btn-sm btn-warning">Manage All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="pendingTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Booking #</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Get pending bookings for dashboard
                            $pendingBookings = [];
                            if (isset($recentBookings)) {
                                $pendingBookings = array_filter($recentBookings, function($booking) {
                                    return $booking['status'] === 'pending';
                                });
                                $pendingBookings = array_slice($pendingBookings, 0, 5); // Show only 5 most recent
                            }
                            ?>
                            <?php if (!empty($pendingBookings)): ?>
                                <?php foreach ($pendingBookings as $booking): ?>
                                    <tr>
                                        <td><span class="badge badge-warning"><?php echo htmlspecialchars($booking['booking_number']); ?></span></td>
                                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                        <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="acceptReservation(<?php echo $booking['id']; ?>)"
                                                        title="Accept">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="rejectReservation(<?php echo $booking['id']; ?>)"
                                                        title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <br>No pending reservations
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                <a href="<?php echo BASE_URL; ?>admin/allBookings" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Booking #</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentBookings)): ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($booking['booking_number']); ?></span></td>
                                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch($booking['status']) {
                                                case 'pending': $statusClass = 'badge-warning'; break;
                                                case 'confirmed': $statusClass = 'badge-info'; break;
                                                case 'in_progress': $statusClass = 'badge-primary'; break;
                                                case 'completed': $statusClass = 'badge-success'; break;
                                                case 'cancelled': $statusClass = 'badge-danger'; break;
                                                default: $statusClass = 'badge-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($booking['status']); ?></span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                        <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>admin/allBookings" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-calendar-times mr-2"></i>No recent bookings
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #4e73df; color: white; border: none;">
                <h5 class="modal-title" style="color: white; font-weight: 600;">
                    <i class="fas fa-file-download"></i> Generate Report
                </h5>
                <button type="button" class="close" style="color: white; opacity: 0.8;" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    <!-- Report Type -->
                    <div class="form-group">
                        <label><strong>Report Type <span class="text-danger">*</span></strong></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" id="salesReport" name="reportType" class="custom-control-input" value="sales" checked>
                                    <label class="custom-control-label" for="salesReport">
                                        <i class="fas fa-chart-line text-primary"></i> Sales Report
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" id="revenueReport" name="reportType" class="custom-control-input" value="revenue">
                                    <label class="custom-control-label" for="revenueReport">
                                        <i class="fas fa-dollar-sign text-success"></i> Revenue Report
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" id="bookingReport" name="reportType" class="custom-control-input" value="booking">
                                    <label class="custom-control-label" for="bookingReport">
                                        <i class="fas fa-calendar-check text-info"></i> Booking Report
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Date Range -->
                    <div class="form-group">
                        <label><strong>Date Range <span class="text-danger">*</span></strong></label>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="small">From Date</label>
                                <input type="date" class="form-control" id="fromDate" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small">To Date</label>
                                <input type="date" class="form-control" id="toDate" required>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Date Options -->
                    <div class="form-group">
                        <label class="small text-muted">Quick Select:</label>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('today')">Today</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('week')">This Week</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('month')">This Month</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('quarter')">This Quarter</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('year')">This Year</button>
                        </div>
                    </div>

                    <hr>

                    <!-- Report Category/Filter -->
                    <div class="form-group">
                        <label><strong>Category/Filter</strong></label>
                        <select class="form-control" id="reportCategory">
                            <option value="all">All Categories</option>
                            <option value="vehicle">Vehicle Covers</option>
                            <option value="bedding">Bedding</option>
                            <option value="furniture">Furniture</option>
                        </select>
                    </div>

                    <!-- Additional Options -->
                    <div class="form-group">
                        <label><strong>Additional Options</strong></label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="includeCharts" checked>
                            <label class="custom-control-label" for="includeCharts">Include Charts and Graphs</label>
                        </div>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" id="includeDetails" checked>
                            <label class="custom-control-label" for="includeDetails">Include Detailed Breakdown</label>
                        </div>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" id="includeSummary">
                            <label class="custom-control-label" for="includeSummary">Include Summary Statistics</label>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div class="form-group">
                        <label><strong>Export Format <span class="text-danger">*</span></strong></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" id="pdfFormat" name="exportFormat" class="custom-control-input" value="pdf" checked>
                                    <label class="custom-control-label" for="pdfFormat">
                                        <i class="fas fa-file-pdf text-danger"></i> PDF
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" id="excelFormat" name="exportFormat" class="custom-control-input" value="excel">
                                    <label class="custom-control-label" for="excelFormat">
                                        <i class="fas fa-file-excel text-success"></i> Excel
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" id="csvFormat" name="exportFormat" class="custom-control-input" value="csv">
                                    <label class="custom-control-label" for="csvFormat">
                                        <i class="fas fa-file-csv text-info"></i> CSV
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Report Preview -->
                <div id="reportPreview" class="mt-4" style="display: none;">
                    <hr>
                    <h6 class="font-weight-bold">Preview:</h6>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <span id="previewText">Select options to preview report details</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="previewReport()">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button type="button" class="btn btn-primary" onclick="generateReport()">
                    <i class="fas fa-download"></i> Generate & Download
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
// Revenue Overview Chart (Area Chart)
var ctxArea = document.getElementById("myAreaChart");
var myAreaChart = new Chart(ctxArea, {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Revenue",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [15000, 18000, 25000, 22000, 28000, 32000, 35000, 30000, 28000, 33000, 38000, 42000],
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineColor: "rgb(234, 236, 244)",
                    zeroLineBorderDash: [2]
                }
            }
        },
        plugins: {
        legend: {
            display: false
        },
            tooltip: {
            backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
            titleMarginBottom: 10,
                titleColor: '#6e707e',
                titleFont: {
                    size: 14
                },
            borderColor: '#dddfeb',
            borderWidth: 1,
                padding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                    label: function(context) {
                        var datasetLabel = context.dataset.label || '';
                        return datasetLabel + ': ₱' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Service Distribution Chart (Pie Chart)
var ctxPie = document.getElementById("myPieChart");
var myPieChart = new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: ["Vehicle Covers", "Bedding", "Furniture"],
        datasets: [{
            data: [45, 30, 25],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
            backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
                padding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                    label: function(context) {
                        var datasetLabel = context.label || '';
                        var value = context.parsed || context.raw;
                    return datasetLabel + ': ' + value + '%';
                }
            }
        },
        legend: {
            display: false
            }
        },
        cutout: '80%'
    }
});

// DataTable initialization - will run after jQuery is loaded from footer
(function() {
    function initDataTables() {
        if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
            jQuery(document).ready(function($) {
                if ($('#dataTable').length) {
    $('#dataTable').DataTable();
                }
                if ($('#pendingTable').length) {
    $('#pendingTable').DataTable({
        "paging": false,
        "searching": false,
        "info": false
    });
                }
    
    // Set default date range to current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
                const fromDateEl = document.getElementById('fromDate');
                const toDateEl = document.getElementById('toDate');
                if (fromDateEl) fromDateEl.valueAsDate = firstDay;
                if (toDateEl) toDateEl.valueAsDate = lastDay;
            });
        } else {
            // jQuery not loaded yet, wait a bit and try again
            setTimeout(initDataTables, 50);
        }
    }
    
    // Start initialization when DOM is ready or immediately if DOM already ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDataTables);
    } else {
        // DOM already loaded, but jQuery might not be (footer loads it)
        initDataTables();
    }
})();

// Set date range based on quick select
function setDateRange(range) {
    const today = new Date();
    let fromDate, toDate;
    
    switch(range) {
        case 'today':
            fromDate = new Date(today);
            toDate = new Date(today);
            break;
        case 'week':
            const firstDayOfWeek = new Date(today);
            firstDayOfWeek.setDate(today.getDate() - today.getDay());
            fromDate = firstDayOfWeek;
            toDate = new Date(today);
            break;
        case 'month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            fromDate = new Date(today.getFullYear(), quarter * 3, 1);
            toDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
            break;
        case 'year':
            fromDate = new Date(today.getFullYear(), 0, 1);
            toDate = new Date(today.getFullYear(), 11, 31);
            break;
    }
    
    document.getElementById('fromDate').valueAsDate = fromDate;
    document.getElementById('toDate').valueAsDate = toDate;
}

// Preview report
function previewReport() {
    const reportType = document.querySelector('input[name="reportType"]:checked').value;
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;
    const category = document.getElementById('reportCategory').value;
    const includeCharts = document.getElementById('includeCharts').checked;
    const includeDetails = document.getElementById('includeDetails').checked;
    const includeSummary = document.getElementById('includeSummary').checked;
    const format = document.querySelector('input[name="exportFormat"]:checked').value;
    
    if (!fromDate || !toDate) {
        alert('Please select a date range');
        return;
    }
    
    let previewText = `Report Type: ${reportType.charAt(0).toUpperCase() + reportType.slice(1)} Report<br>`;
    previewText += `Date Range: ${fromDate} to ${toDate}<br>`;
    previewText += `Category: ${category === 'all' ? 'All Categories' : category}<br>`;
    previewText += `Export Format: ${format.toUpperCase()}<br>`;
    previewText += `<hr>`;
    previewText += `<strong>Options:</strong><br>`;
    previewText += `- Include Charts: ${includeCharts ? 'Yes' : 'No'}<br>`;
    previewText += `- Include Details: ${includeDetails ? 'Yes' : 'No'}<br>`;
    previewText += `- Include Summary: ${includeSummary ? 'Yes' : 'No'}<br>`;
    
    document.getElementById('previewText').innerHTML = previewText;
    document.getElementById('reportPreview').style.display = 'block';
}

// Generate and download report
function generateReport() {
    const reportType = document.querySelector('input[name="reportType"]:checked').value;
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;
    const category = document.getElementById('reportCategory').value;
    const format = document.querySelector('input[name="exportFormat"]:checked').value;
    
    if (!fromDate || !toDate) {
        alert('Please select a date range');
        return;
    }
    
    // Show loading
    alert('Generating report... Please wait.');
    
    // In a real application, this would make an AJAX call to generate the report
    // For now, we'll simulate the download
    console.log('Report Generation Parameters:');
    console.log({
        type: reportType,
        fromDate: fromDate,
        toDate: toDate,
        category: category,
        format: format
    });
    
    // Simulate report generation
    setTimeout(() => {
        const fileName = `${reportType}_report_${fromDate}_${toDate}.${format}`;
        alert(`Report generated successfully!\n\nFile: ${fileName}\n\nThis would download the actual report file.`);
        
        // Close modal after generation
        $('#generateReportModal').modal('hide');
    }, 1500);
}

// Accept reservation
function acceptReservation(bookingId) {
    if (!confirm('Are you sure you want to accept this reservation?')) {
        return;
    }
    
    // Show loading state
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking_id=' + bookingId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            
            // Remove the row from pending table
            const row = button.closest('tr');
            row.remove();
            
            // Update pending count in dashboard
            updatePendingCount();
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while accepting the reservation.');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Reject reservation
function rejectReservation(bookingId) {
    const reason = prompt('Please provide a reason for rejecting this reservation:');
    if (!reason) {
        return;
    }
    
    // Show loading state
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/rejectReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking_id=' + bookingId + '&reason=' + encodeURIComponent(reason)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            
            // Remove the row from pending table
            const row = button.closest('tr');
            row.remove();
            
            // Update pending count in dashboard
            updatePendingCount();
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while rejecting the reservation.');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Show alert message
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Insert at the top of the page
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Update pending count in dashboard
function updatePendingCount() {
    // This would ideally make an AJAX call to get updated counts
    // For now, we'll just refresh the page after a short delay
    setTimeout(() => {
        location.reload();
    }, 2000);
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

