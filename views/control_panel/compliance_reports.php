<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Compliance Reports' ?> - UphoCare</title>
    
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
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 5px 0 0 0;
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
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }

        .stats-card .card-body {
            padding: 25px;
        }

        .report-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .report-card.pending {
            border-left-color: #f39c12;
        }

        .report-card.reviewed {
            border-left-color: #3498db;
        }

        .report-card.resolved {
            border-left-color: #27ae60;
        }

        .report-card.dismissed {
            border-left-color: #95a5a6;
        }

        .report-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .report-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .store-name {
            color: #667eea;
            font-weight: 600;
            font-size: 16px;
        }

        .store-address {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.reviewed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.resolved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.dismissed {
            background: #e2e3e5;
            color: #383d41;
        }

        .issue-types {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 15px 0;
        }

        .issue-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .issue-badge.safety {
            background: #fee;
            color: #e74c3c;
        }

        .issue-badge.hygiene {
            background: #fff3cd;
            color: #f39c12;
        }

        .issue-badge.quality {
            background: #d1ecf1;
            color: #3498db;
        }

        .issue-badge.service {
            background: #e7f3ff;
            color: #2980b9;
        }

        .issue-badge.pricing {
            background: #d4edda;
            color: #27ae60;
        }

        .issue-badge.other {
            background: #e2e3e5;
            color: #7f8c8d;
        }

        .report-description {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            color: #495057;
            line-height: 1.6;
        }

        .report-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .report-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-review {
            background: #3498db;
            color: white;
        }

        .btn-review:hover {
            background: #2980b9;
        }

        .btn-resolve {
            background: #27ae60;
            color: white;
        }

        .btn-resolve:hover {
            background: #229954;
        }

        .btn-dismiss {
            background: #95a5a6;
            color: white;
        }

        .btn-dismiss:hover {
            background: #7f8c8d;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .no-reports {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .no-reports i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <?php require_once ROOT . DS . 'views' . DS . 'control_panel' . DS . 'layouts' . DS . 'sidebar.php'; ?>

    <div class="main-content-wrapper">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div>
                <h1 class="page-title">
                    <i class="fas <?= $data['page_icon'] ?? 'fa-clipboard-check' ?>"></i>
                    <?= $data['page_title'] ?? 'Compliance Reports' ?>
                </h1>
                <p class="page-subtitle"><?= $data['page_subtitle'] ?? 'Review store compliance reports submitted by customers' ?></p>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Total Reports</div>
                                    <div class="h4 mb-0"><?= $data['stats']['total'] ?? 0 ?></div>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Pending</div>
                                    <div class="h4 mb-0 text-warning"><?= $data['stats']['pending'] ?? 0 ?></div>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Reviewed</div>
                                    <div class="h4 mb-0 text-info"><?= $data['stats']['reviewed'] ?? 0 ?></div>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Resolved</div>
                                    <div class="h4 mb-0 text-success"><?= $data['stats']['resolved'] ?? 0 ?></div>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="<?= BASE_URL ?>control-panel/complianceReports" class="row align-items-end">
                    <div class="col-md-4">
                        <label for="statusFilter" class="form-label">Filter by Status:</label>
                        <select class="form-control" id="statusFilter" name="status" onchange="this.form.submit()">
                            <option value="all" <?= ($data['filter_status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="pending" <?= ($data['filter_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="reviewed" <?= ($data['filter_status'] ?? '') === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                            <option value="resolved" <?= ($data['filter_status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="dismissed" <?= ($data['filter_status'] ?? '') === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="typeFilter" class="form-label">Filter by Issue Type:</label>
                        <select class="form-control" id="typeFilter" name="type" onchange="this.form.submit()">
                            <option value="all" <?= ($data['filter_type'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Types</option>
                            <option value="safety" <?= ($data['filter_type'] ?? '') === 'safety' ? 'selected' : '' ?>>Safety</option>
                            <option value="hygiene" <?= ($data['filter_type'] ?? '') === 'hygiene' ? 'selected' : '' ?>>Hygiene</option>
                            <option value="quality" <?= ($data['filter_type'] ?? '') === 'quality' ? 'selected' : '' ?>>Quality</option>
                            <option value="service" <?= ($data['filter_type'] ?? '') === 'service' ? 'selected' : '' ?>>Service</option>
                            <option value="pricing" <?= ($data['filter_type'] ?? '') === 'pricing' ? 'selected' : '' ?>>Pricing</option>
                            <option value="other" <?= ($data['filter_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="<?= BASE_URL ?>control-panel/complianceReports" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Reports List -->
            <?php if (empty($data['reports'])): ?>
                <div class="no-reports">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>No Compliance Reports</h4>
                    <p>There are no compliance reports to display.</p>
                </div>
            <?php else: ?>
                <?php foreach ($data['reports'] as $report): ?>
                    <div class="report-card <?= $report['status'] ?>">
                        <div class="report-header">
                            <div>
                                <div class="report-title">
                                    <i class="fas fa-store text-primary"></i> 
                                    <span class="store-name"><?= htmlspecialchars($report['store_name'] ?? 'Unknown Store') ?></span>
                                </div>
                                <div class="store-address">
                                    <?= htmlspecialchars($report['address'] ?? '') ?>, <?= htmlspecialchars($report['city'] ?? '') ?>, <?= htmlspecialchars($report['province'] ?? '') ?>
                                </div>
                            </div>
                            <div>
                                <span class="status-badge <?= $report['status'] ?>">
                                    <?= ucfirst($report['status']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="issue-types">
                            <?php 
                            $issueTypes = is_array($report['issue_types']) ? $report['issue_types'] : json_decode($report['issue_types'] ?? '[]', true);
                            $issueLabels = [
                                'safety' => 'Safety',
                                'hygiene' => 'Hygiene',
                                'quality' => 'Quality',
                                'service' => 'Service',
                                'pricing' => 'Pricing',
                                'other' => 'Other'
                            ];
                            foreach ($issueTypes as $type): 
                            ?>
                                <span class="issue-badge <?= $type ?>">
                                    <i class="fas fa-<?= $type === 'safety' ? 'shield-alt' : ($type === 'hygiene' ? 'soap' : ($type === 'quality' ? 'award' : ($type === 'service' ? 'user-tie' : ($type === 'pricing' ? 'dollar-sign' : 'ellipsis-h')))) ?>"></i>
                                    <?= $issueLabels[$type] ?? ucfirst($type) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <div class="report-description">
                            <strong>Description:</strong><br>
                            <?= nl2br(htmlspecialchars($report['description'] ?? 'No description provided')) ?>
                        </div>

                        <div class="report-info">
                            <div class="info-item">
                                <div class="info-label">Reported By</div>
                                <div class="info-value"><?= htmlspecialchars($report['customer_name'] ?? 'Unknown') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($report['customer_email'] ?? '') ?></small>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Report Type</div>
                                <div class="info-value"><?= ucfirst($report['report_type'] ?? 'other') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Reported Date</div>
                                <div class="info-value"><?= date('M d, Y H:i', strtotime($report['created_at'] ?? 'now')) ?></div>
                            </div>
                            <?php if ($report['reviewed_by_name']): ?>
                            <div class="info-item">
                                <div class="info-label">Reviewed By</div>
                                <div class="info-value"><?= htmlspecialchars($report['reviewed_by_name']) ?></div>
                                <small class="text-muted"><?= $report['reviewed_at'] ? date('M d, Y', strtotime($report['reviewed_at'])) : '' ?></small>
                            </div>
                            <?php endif; ?>
                            <?php if ($report['admin_notes']): ?>
                            <div class="info-item">
                                <div class="info-label">Admin Notes</div>
                                <div class="info-value"><?= nl2br(htmlspecialchars($report['admin_notes'])) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="report-actions">
                            <?php if ($report['status'] === 'pending'): ?>
                                <button class="btn btn-action btn-review" onclick="updateReportStatus(<?= $report['id'] ?>, 'reviewed')">
                                    <i class="fas fa-eye"></i> Mark as Reviewed
                                </button>
                                <button class="btn btn-action btn-resolve" onclick="updateReportStatus(<?= $report['id'] ?>, 'resolved')">
                                    <i class="fas fa-check-circle"></i> Mark as Resolved
                                </button>
                                <button class="btn btn-action btn-dismiss" onclick="updateReportStatus(<?= $report['id'] ?>, 'dismissed')">
                                    <i class="fas fa-times-circle"></i> Dismiss
                                </button>
                            <?php elseif ($report['status'] === 'reviewed'): ?>
                                <button class="btn btn-action btn-resolve" onclick="updateReportStatus(<?= $report['id'] ?>, 'resolved')">
                                    <i class="fas fa-check-circle"></i> Mark as Resolved
                                </button>
                                <button class="btn btn-action btn-dismiss" onclick="updateReportStatus(<?= $report['id'] ?>, 'dismissed')">
                                    <i class="fas fa-times-circle"></i> Dismiss
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-secondary btn-sm" onclick="viewReportDetails(<?= $report['id'] ?>)">
                                <i class="fas fa-info-circle"></i> View Details
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Update Report Status Modal -->
    <div class="modal fade" id="updateReportStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateReportStatusTitle">Update Report Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="update_report_id">
                    <input type="hidden" id="update_report_status">
                    <div class="form-group">
                        <label for="admin_notes">Admin Notes (Optional):</label>
                        <textarea class="form-control" id="admin_notes" rows="4" 
                                  placeholder="Add any notes about this report..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateReportStatus()">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function updateReportStatus(reportId, status) {
        document.getElementById('update_report_id').value = reportId;
        document.getElementById('update_report_status').value = status;
        document.getElementById('admin_notes').value = '';
        
        const statusLabels = {
            'reviewed': 'Mark as Reviewed',
            'resolved': 'Mark as Resolved',
            'dismissed': 'Dismiss Report'
        };
        
        document.getElementById('updateReportStatusTitle').textContent = statusLabels[status] || 'Update Report Status';
        
        $('#updateReportStatusModal').modal('show');
    }
    
    function confirmUpdateReportStatus() {
        const reportId = document.getElementById('update_report_id').value;
        const status = document.getElementById('update_report_status').value;
        const adminNotes = document.getElementById('admin_notes').value.trim();
        
        // Show loading state
        const button = event.target;
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        button.disabled = true;
        
        fetch('<?= BASE_URL ?>control-panel/updateComplianceReportStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `report_id=${reportId}&status=${status}&admin_notes=${encodeURIComponent(adminNotes)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#updateReportStatusModal').modal('hide');
                alert(data.message || 'Report status updated successfully.');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update report status'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the report status.');
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
    
    function viewReportDetails(reportId) {
        // For now, just show an alert. Can be expanded to show a detailed modal.
        alert('Report ID: ' + reportId + '\nDetailed view coming soon.');
    }
    </script>
</body>
</html>

