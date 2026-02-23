<?php 
// Prepare data for layouts
$title = $data['title'] ?? 'Login Logs';
$user = $data['admin'] ?? [];

require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'control_panel_sidebar.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; 
?>


    <!-- Modern Page Header Container -->
    <div class="card border-0 module-card mb-4" style="background: white;">
        <div class="card-body py-3 px-4">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h4 mb-0 font-weight-bold" style="color: #2C3E50;">Access Registry</h1>
                    <p class="text-muted smaller mb-0">Audit trail of system access attempts.</p>
                </div>
                <div class="d-none d-md-block">
                    <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                        <div class="indicator-dot bg-success mr-2 shadow-sm pulse-success" style="width: 7px; height: 7px;"></div>
                        <span class="smaller font-weight-bold text-dark">Live Monitor <span class="mx-1">|</span> <?= date('H:i') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sleek Minimalist Search Bar -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 12px; border-left: 5px solid #0F3C5F !important;">
        <div class="card-body py-3 px-4">
            <form method="GET" action="<?= BASE_URL ?>control-panel/loginLogs" class="row align-items-center">
                <div class="col-md-6 d-flex align-items-center mb-3 mb-md-0">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px;">
                        <i class="fas fa-user-shield text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-weight-bold" style="color: #2C3E50;">Search by Name</h6>
                        <p class="text-muted smaller mb-0">
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex">
                        <div class="input-group shadow-sm">
                            <input type="text" name="search" class="form-control border-0 bg-light" 
                                   placeholder="Type name or email here..." 
                                   value="<?= htmlspecialchars($data['filter_search'] ?? '') ?>"
                                   style="border-radius: 10px 0 0 10px; height: 48px; font-weight: 500;">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-navy-modern px-4" style="border-radius: 0 10px 10px 0; height: 48px;">
                                    <i class="fas fa-search mr-2"></i> Search
                                </button>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>control-panel/loginLogs" class="btn btn-white border shadow-sm ml-2 d-flex align-items-center justify-content-center" 
                           style="width: 48px; height: 48px; border-radius: 10px;" title="Reset View">
                            <i class="fas fa-sync-alt text-muted"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Registry Table -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 12px; border: 1px solid rgba(0,0,0,0.05) !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-items-center" id="professionalLoginLogsTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-muted border-bottom">
                        <tr>
                            <th class="border-0 small text-uppercase font-weight-bold px-4 py-3">Subject</th>
                            <th class="border-0 small text-uppercase font-weight-bold py-3">User Access</th>
                            <th class="border-0 small text-uppercase font-weight-bold py-3">Security Level</th>
                            <th class="border-0 small text-uppercase font-weight-bold px-4 py-3">Activity Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <?php 
                        // Logic to minimize table clutter: Count occurrences per user/day/status
                        $logCounts = [];
                        foreach ($data['login_logs'] as $l) {
                            $dateKey = date('Y-m-d', strtotime($l['login_time']));
                            $userKey = $l['email'] . '_' . $dateKey . '_' . $l['login_status'];
                            $logCounts[$userKey] = ($logCounts[$userKey] ?? 0) + 1;
                        }

                        $shownKeys = [];
                        foreach ($data['login_logs'] as $log): 
                            $dateKey = date('Y-m-d', strtotime($log['login_time']));
                            $userKey = $log['email'] . '_' . $dateKey . '_' . $log['login_status'];
                            $isDuplicate = in_array($userKey, $shownKeys);
                            $shownKeys[] = $userKey;
                            
                            // Determine if we should show this row (collapsed view logic)
                            // For audit trails, we usually want to see ALL, but the user asked to minimize
                            // So let's add a badge to the first occurrence and allow users to toggle?
                            // For now, let's just add the counts to the first row of that group.
                        ?>
                            <tr class="hover-row-modern transition-300 <?= $isDuplicate ? 'repeated-log-row d-none bg-light-soft' : 'primary-log-row' ?>" 
                                data-group="<?= htmlspecialchars($userKey) ?>">
                                <td class="px-4 py-4 border-bottom-soft" style="background: #ffffff;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-capsule mr-3">
                                            <div class="avatar-base bg-light text-secondary font-weight-900 rounded-lg d-flex align-items-center justify-content-center border" style="width: 44px; height: 44px;">
                                                <?= strtoupper(substr($log['fullname'] ?? 'S', 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-700 text-dark small mb-0 d-flex align-items-center">
                                                <?= htmlspecialchars($log['fullname'] ?? 'System User') ?>
                                                <?php if (!$isDuplicate && $logCounts[$userKey] > 1): ?>
                                                    <a href="javascript:void(0)" class="toggle-log-group text-info ml-2 font-weight-bold" 
                                                       data-target="<?= htmlspecialchars($userKey) ?>" style="font-size: 0.65rem; text-decoration: none;">
                                                        <i class="fas fa-history mr-1"></i> <?= $logCounts[$userKey] ?> attempts today
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted font-weight-500" style="font-size: 0.72rem;">
                                                <?= htmlspecialchars($log['email']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 border-bottom-soft align-middle">
                                    <?php 
                                    // Clean labeling instead of button-like badges
                                    $cpRole = $log['admin_role'] ?? '';
                                    $userRole = $log['user_role'] ?? '';
                                    
                                    if ($log['user_type'] === 'customer' || $userRole === 'customer') {
                                        echo '<div class="status-pill-modern text-muted"><i class="fas fa-user-circle mr-2 opacity-50"></i>Customer</div>';
                                    } elseif ($log['user_type'] === 'admin' || $userRole === 'admin') {
                                        echo '<div class="status-pill-modern border-left pl-3" style="border-color: #3498DB !important;"><i class="fas fa-store-alt mr-2 text-info"></i>Store Admin</div>';
                                    } elseif ($cpRole === 'super_admin') {
                                        echo '<div class="status-pill-modern border-left pl-3" style="border-color: #e74c3c !important;"><i class="fas fa-crown mr-2 text-danger"></i>Super Admin</div>';
                                    } elseif ($cpRole === 'admin') {
                                        echo '<div class="status-pill-modern border-left pl-3" style="border-color: #2C3E50 !important;"><i class="fas fa-user-shield mr-2 text-dark"></i>System Admin</div>';
                                    } else {
                                        echo '<div class="status-pill-modern text-muted"><i class="fas fa-id-badge mr-2 opacity-50"></i>Member</div>';
                                    }
                                    ?>
                                </td>
                                <td class="py-4 border-bottom-soft align-middle" style="background: #ffffff;">
                                    <?php if ($log['login_status'] === 'success'): ?>
                                        <div class="text-success-modern d-flex align-items-center">
                                            <span class="small font-weight-700 tracking-wider text-uppercase text-success"><i class="fas fa-check-circle mr-1"></i> Authorized</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex flex-column">
                                            <div class="text-danger-modern d-flex align-items-center mb-1">
                                                <span class="small font-weight-700 tracking-wider text-uppercase text-danger"><i class="fas fa-times-circle mr-1"></i> Denied</span>
                                            </div>
                                            <span class="text-muted smaller font-weight-500 pl-3">
                                                <?= htmlspecialchars($log['failure_reason'] ?? 'Invalid attempts') ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 border-bottom-soft align-middle" style="background: rgba(44, 62, 80, 0.02);" data-order="<?= strtotime($log['login_time']) ?>">
                                    <div class="timeline-unit">
                                        <div class="font-weight-700 text-dark small mb-0">
                                            <?= date('M d, Y', strtotime($log['login_time'])) ?>
                                        </div>
                                        <div class="text-muted smaller font-weight-600">
                                            at <?= date('H:i:s', strtotime($log['login_time'])) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<style>
    /* Professional Typography & Color Matrix */
    .bg-light-soft { background-color: #F8FAFC; }
    .border-bottom-soft { border-bottom: 1px solid #F1F5F9; }
    .text-navy { color: #0F3C5F; }
    .transition-300 { transition: all 0.3s ease; }
    .font-weight-900 { font-weight: 900; }
    .truncate-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Modern Table States */
    .hover-row-modern:hover {
        background-color: #FBFDFF;
        transform: scale(1.002);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    /* Custom Input Design */
    .select-professional {
        background-color: #F8FAFC !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 10px !important;
        height: 44px !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        transition: all 0.2s;
    }
    .select-professional:focus {
        border-color: #0F3C5F !important;
        box-shadow: 0 0 0 3px rgba(15, 60, 95, 0.1) !important;
    }

    /* Hide Sort Indicators Encircled by User */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after {
        display: none !important;
    }

    #professionalLoginLogsTable thead th {
        cursor: pointer;
        border-bottom: 2px solid #edf2f7;
        color: #64748b;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 1.25rem 1rem;
        transition: all 0.2s;
    }

    #professionalLoginLogsTable thead th:hover {
        background-color: #f8fafc;
        color: #0F3C5F;
    }

    /* Fixed Row Hover Effects */
    #professionalLoginLogsTable tbody tr {
        transition: all 0.2s ease;
    }
    
    #professionalLoginLogsTable tbody tr:hover {
        background-color: rgba(15, 60, 95, 0.02);
        box-shadow: inset 4px 0 0 #0F3C5F;
    }

    #professionalLoginLogsTable td {
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .btn-navy-modern {
        background-color: #2C3E50;
        color: white;
        border-radius: 10px;
        height: 48px;
        font-weight: 700;
        border: none;
        transition: all 0.3s;
    }
    .btn-navy-modern:hover {
        background-color: #34495E;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
    }
    .btn-light-modern {
        background-color: #fff;
        border-radius: 10px;
        height: 44px;
        font-weight: 700;
        color: #64748b;
    }

    /* Status Pill Logic - label style instead of button style */
    .status-pill-modern {
        display: inline-flex;
        align-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
    }
    .text-success { color: #2ecc71 !important; }
    .text-danger { color: #e74c3c !important; }
    .text-info { color: #3498DB !important; }
    .text-dark { color: #2C3E50 !important; }

    /* Security Indicators */
    .indicator-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    @keyframes pulse-danger {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
    .pulse-danger { animation: pulse-danger 1.5s infinite; }

    /* Interactive UI Elements */
    .hover-lift:hover { transform: translateY(-2px); transition: 0.2s; }

    /* DataTables Overrides */
    #professionalLoginLogsTable_wrapper .dataTables_length {
        display: none !important; /* Force hide length menu as requested */
    }
    #professionalLoginLogsTable_wrapper .dataTables_filter {
        padding: 1.5rem 1.5rem 1rem;
    }
    #professionalLoginLogsTable_wrapper .dataTables_filter input {
        border-radius: 10px;
        border: 1px solid #E2E8F0;
        padding: 0.5rem 1rem;
        margin-left: 0.5rem;
    }
    #professionalLoginLogsTable_wrapper .dataTables_info {
        padding: 1.5rem;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
    }
    #professionalLoginLogsTable_wrapper .dataTables_paginate {
        padding: 1rem 1.5rem;
    }
</style>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#professionalLoginLogsTable')) {
            $('#professionalLoginLogsTable').DataTable().destroy();
        }
        
        var table = $('#professionalLoginLogsTable').DataTable({
            "lengthChange": false, 
            "pageLength": 10,
            "order": [[3, "desc"]], // Sort by timeline DESC (forces recent first)
            "ordering": true,
            "columnDefs": [
                { "type": "num", "targets": 3 }, // Force Timeline to use numeric data-order for sorting
                { "orderable": false, "targets": [0, 1, 2] } // Only Timeline is sortable to keep it clean
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Filter results...",
                "paginate": {
                    "previous": '<i class="fas fa-chevron-left small"></i>',
                    "next": '<i class="fas fa-chevron-right small"></i>'
                }
            },
            "dom": "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm border-0');
            }
        });

        // Toggle repeated logs
        $(document).on('click', '.toggle-log-group', function(e) {
            e.preventDefault();
            var groupKey = $(this).data('target');
            var groupRows = $('.repeated-log-row[data-group="' + groupKey + '"]');
            
            if (groupRows.hasClass('d-none')) {
                groupRows.removeClass('d-none');
                $(this).html('<i class="fas fa-compress-alt mr-1"></i> Hide attempts');
            } else {
                groupRows.addClass('d-none');
                $(this).html('<i class="fas fa-history mr-1"></i> ' + $(this).text().split(' ')[0] + ' attempts today');
            }
        });
    });
</script>
