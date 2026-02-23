<?php 
$bookings = $data['businessBookings'] ?? [];
$businessStats = $data['businessStats'] ?? [];
?>

<!-- Business History Modal -->
<div class="modal fade" id="businessHistoryModal" tabindex="-1" role="dialog" aria-labelledby="businessHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title font-weight-bold" id="businessHistoryModalLabel">
                    <i class="fas fa-history mr-2"></i> Business Booking History
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Business Statistics inside Modal -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-left-success shadow-sm h-100 py-2">
                            <div class="card-body py-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo count($bookings); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow-sm h-100 py-2">
                            <div class="card-body py-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revenue</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $businessStats['totalRevenueFormatted'] ?? '₱0.00'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-peso-sign fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning shadow-sm h-100 py-2">
                            <div class="card-body py-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Completed</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            <?php echo count(array_filter($bookings, function($b) { return $b['status'] === 'completed'; })); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow-sm h-100 py-2">
                            <div class="card-body py-2">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $businessStats['activeProjects'] ?? '0'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-lg text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Table -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="businessHistoryModalTable" width="100%" cellspacing="0">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="border-top-0">Booking #</th>
                                        <th class="border-top-0">Project</th>
                                        <th class="border-top-0">Service</th>
                                        <th class="border-top-0">Amount</th>
                                        <th class="border-top-0">Status</th>
                                        <th class="border-top-0">Date</th>
                                        <th class="border-top-0 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($bookings)): ?>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td class="align-middle">
                                                    <span class="small font-weight-bold text-primary">#<?php echo htmlspecialchars($booking['booking_number']); ?></span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="small font-weight-bold text-dark"><?php echo htmlspecialchars($booking['item_description'] ?? 'N/A'); ?></span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="small text-muted"><?php echo htmlspecialchars($booking['service_name']); ?></span>
                                                    <?php if (!empty($booking['service_type'])): ?>
                                                        <br><span class="badge badge-light border small"><?php echo htmlspecialchars($booking['service_type']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="small font-weight-bold text-success">₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                                                </td>
                                                <td class="align-middle">
                                                    <?php
                                                    $statusClass = 'badge-secondary';
                                                    switch($booking['status']) {
                                                        case 'admin_review': $statusClass = 'badge-warning'; break;
                                                        case 'pending': $statusClass = 'badge-warning'; break;
                                                        case 'confirmed': $statusClass = 'badge-info'; break;
                                                        case 'in_progress': $statusClass = 'badge-primary'; break;
                                                        case 'completed': $statusClass = 'badge-success'; break;
                                                        case 'cancelled': $statusClass = 'badge-danger'; break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?> small px-2 py-1">
                                                        <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="small text-muted"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $booking['id']; ?>" class="btn btn-sm btn-circle btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted small">No business history found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize DataTable when modal is shown
$('#businessHistoryModal').on('shown.bs.modal', function () {
    if (!$.fn.DataTable.isDataTable('#businessHistoryModalTable')) {
        $('#businessHistoryModalTable').DataTable({
            "order": [[ 5, "desc" ]],
            "pageLength": 10,
            "dom": "<'row mb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "language": {
                "search": "",
                "searchPlaceholder": "Search history..."
            }
        });
        
        // Style adjustments for the search input
        $('.dataTables_filter input').addClass('form-control form-control-sm border-0 shadow-sm px-3').css('border-radius', '20px');
    }
});
</script>
