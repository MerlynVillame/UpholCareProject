<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<?php
$ratings = $ratings ?? [];
$adminStore = $adminStore ?? null;
$adminStoreLocationId = $adminStoreLocationId ?? null;
$totalRatings = $totalRatings ?? 0;
$averageRating = $averageRating ?? 0;
$ratingCounts = $ratingCounts ?? [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$yearlyData = $yearlyData ?? [];
$tableExists = $tableExists ?? false;
$error = $error ?? null;
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-star text-warning"></i> Store Ratings
        </h1>
        <?php if ($adminStore): ?>
            <small class="text-muted">
                <i class="fas fa-store"></i> <?php echo htmlspecialchars($adminStore['store_name']); ?>
                <?php if (!empty($adminStore['city'])): ?>
                    - <?php echo htmlspecialchars($adminStore['city']); ?>
                <?php endif; ?>
            </small>
        <?php elseif (!$adminStoreLocationId): ?>
            <div class="alert alert-warning mt-2">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>No store location found.</strong> 
                <br>Your account (<?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?>) is not linked to any store location.
                <br>Please ensure your store location exists and is linked to your admin account, or contact support.
            </div>
        <?php endif; ?>
    </div>
    <div>
        <span class="text-primary-admin font-weight-bold">Total Ratings: <?php echo $totalRatings; ?></span>
        <?php if ($averageRating > 0): ?>
            <span class="text-success font-weight-bold ml-2">Average: <?php echo $averageRating; ?>/5.0</span>
        <?php endif; ?>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> Error: <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if (!$tableExists): ?>
    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i> The store ratings table does not exist yet. 
        Ratings will appear here once customers start rating stores.
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Average Rating Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Average Rating
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $averageRating > 0 ? number_format($averageRating, 1) : '0.0'; ?>/5.0
                        </div>
                        <div class="mt-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $averageRating): ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php elseif ($i - 0.5 <= $averageRating): ?>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-gray-300"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Ratings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Ratings
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $totalRatings; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5-Star Ratings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            5-Star Ratings
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $ratingCounts[5]; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Ratings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary-admin text-uppercase mb-1">
                            Active Ratings
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $activeRatings = array_filter($ratings, function($rating) {
                                return $rating['status'] === 'active';
                            });
                            echo count($activeRatings);
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rating Distribution -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Rating Distribution
                </h6>
            </div>
            <div class="card-body">
                <?php for ($star = 5; $star >= 1; $star--): ?>
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            <div class="mr-2" style="width: 60px;">
                                <strong><?php echo $star; ?> Star</strong>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 25px;">
                                    <?php 
                                    $percentage = $totalRatings > 0 ? ($ratingCounts[$star] / $totalRatings) * 100 : 0;
                                    ?>
                                    <div class="progress-bar 
                                        <?php 
                                        if ($star >= 4) echo 'bg-success';
                                        elseif ($star == 3) echo 'bg-warning';
                                        else echo 'bg-danger';
                                        ?>" 
                                        role="progressbar" 
                                        style="width: <?php echo $percentage; ?>%"
                                        aria-valuenow="<?php echo $percentage; ?>" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        <?php echo $ratingCounts[$star]; ?> (<?php echo number_format($percentage, 1); ?>%)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<!-- Yearly Ratings Chart -->
<?php if (!empty($yearlyData)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line"></i> Ratings Over the Years
                </h6>
            </div>
            <div class="card-body">
                <canvas id="yearlyRatingsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter"></i> Filters
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="ratingFilter">Filter by Rating:</label>
                        <select class="form-control" id="ratingFilter">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="statusFilter">Filter by Status:</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ratings Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> All Store Ratings
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($ratings)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No ratings found.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="ratingsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Store</th>
                                    <th>Customer</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ratings as $rating): ?>
                                    <tr data-store-id="<?php echo $rating['store_id']; ?>"
                                        data-rating-value="<?php echo (int)$rating['rating']; ?>"
                                        data-status="<?php echo $rating['status']; ?>">
                                        <td><?php echo $rating['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($rating['store_name'] ?? 'N/A'); ?></strong><br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($rating['city'] ?? ''); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($rating['customer_name'] ?? 'Anonymous'); ?></strong><br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($rating['customer_email'] ?? ''); ?>
                                            </small>
                                            <?php if (!empty($rating['customer_phone'])): ?>
                                                <br><small class="text-muted">
                                                    <?php echo htmlspecialchars($rating['customer_phone']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= $rating['rating']): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-gray-300"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                                <span class="ml-2 font-weight-bold"><?php echo number_format($rating['rating'], 1); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($rating['review_text'])): ?>
                                                <div class="review-text" style="max-width: 300px;">
                                                    <?php echo nl2br(htmlspecialchars($rating['review_text'])); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted"><em>No review text</em></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($rating['status'] === 'active'): ?>
                                                <span class="text-success font-weight-bold">Active</span>
                                            <?php else: ?>
                                                <span class="text-secondary font-weight-bold">Hidden</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $date = new DateTime($rating['created_at']);
                                            echo $date->format('M d, Y');
                                            ?><br>
                                            <small class="text-muted">
                                                <?php echo $date->format('h:i A'); ?>
                                            </small>
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

<style>
.review-text {
    word-wrap: break-word;
    white-space: pre-wrap;
}

.table td {
    vertical-align: middle;
}

.progress {
    border-radius: 0.35rem;
}

#yearlyRatingsChart {
    max-height: 400px;
}
</style>

<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Yearly Ratings Chart
    <?php if (!empty($yearlyData)): ?>
    const yearlyCtx = document.getElementById('yearlyRatingsChart');
    if (yearlyCtx) {
        const yearlyData = <?php echo json_encode($yearlyData); ?>;
        
        const years = yearlyData.map(item => item.year);
        const totalRatings = yearlyData.map(item => parseInt(item.total_ratings));
        const averageRatings = yearlyData.map(item => parseFloat(item.average_rating).toFixed(1));
        const fiveStar = yearlyData.map(item => parseInt(item.five_star));
        const fourStar = yearlyData.map(item => parseInt(item.four_star));
        const threeStar = yearlyData.map(item => parseInt(item.three_star));
        const twoStar = yearlyData.map(item => parseInt(item.two_star));
        const oneStar = yearlyData.map(item => parseInt(item.one_star));
        
        new Chart(yearlyCtx, {
            type: 'line',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Total Ratings',
                        data: totalRatings,
                        borderColor: '#2196F3',
                        backgroundColor: 'rgba(33, 150, 243, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Average Rating',
                        data: averageRatings,
                        borderColor: 'rgb(255, 193, 7)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                        borderDash: [5, 5]
                    },
                    {
                        label: '5 Stars',
                        data: fiveStar,
                        borderColor: 'rgb(40, 167, 69)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: '4 Stars',
                        data: fourStar,
                        borderColor: 'rgb(0, 123, 255)',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: '3 Stars',
                        data: threeStar,
                        borderColor: 'rgb(255, 193, 7)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: '2 Stars',
                        data: twoStar,
                        borderColor: 'rgb(255, 152, 0)',
                        backgroundColor: 'rgba(255, 152, 0, 0.1)',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: '1 Star',
                        data: oneStar,
                        borderColor: 'rgb(220, 53, 69)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.label === 'Average Rating') {
                                    label += context.parsed.y + '/5.0';
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Year'
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Ratings'
                        },
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Average Rating'
                        },
                        beginAtZero: true,
                        max: 5,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(1) + '/5';
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
    
    // Table filtering
    const ratingFilter = document.getElementById('ratingFilter');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('ratingsTable');
    
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    
    function filterTable() {
        const selectedRating = ratingFilter ? ratingFilter.value : '';
        const selectedStatus = statusFilter ? statusFilter.value : '';
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const ratingValue = row.getAttribute('data-rating-value');
            const status = row.getAttribute('data-status');
            
            let show = true;
            
            if (selectedRating && ratingValue !== selectedRating) {
                show = false;
            }
            
            if (selectedStatus && status !== selectedStatus) {
                show = false;
            }
            
            if (show) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show message if no rows visible
        const tbody = table.querySelector('tbody');
        let noResultsMsg = tbody.querySelector('.no-results-msg');
        
        if (visibleCount === 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('tr');
                noResultsMsg.className = 'no-results-msg';
                noResultsMsg.innerHTML = '<td colspan="7" class="text-center py-4"><em class="text-muted">No ratings match the selected filters.</em></td>';
                tbody.appendChild(noResultsMsg);
            }
        } else {
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }
    
    if (ratingFilter) {
        ratingFilter.addEventListener('change', filterTable);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

