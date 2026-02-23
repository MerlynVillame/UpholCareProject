<?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
    .logistic-table thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #4e73df;
        border-top: none;
        padding: 12px 15px;
        vertical-align: middle;
    }
    .logistic-table tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #5a5c69;
        font-size: 0.85rem;
        border-bottom: 1px solid #e3e6f0;
    }
    .logistic-table tbody tr:hover {
        background-color: #f8f9fc;
    }
    .customer-id {
        font-size: 0.75rem;
        color: #858796;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        border-radius: 4px;
    }
    .badge-status {
        padding: 0.5em 0.8em;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .location-box {
        max-width: 200px;
    }
</style>

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">📋 Daily Logistic Schedule</h1>
                <div class="d-flex align-items-center">
                    <?php 
                        $isBusiness = ($mode === 'business');
                        $toggleMode = $isBusiness ? 'local' : 'business';
                        $btnClass = $isBusiness ? 'btn-success' : 'btn-primary';
                        $btnIcon = $isBusiness ? 'fa-home' : 'fa-briefcase';
                        $btnText = $isBusiness ? 'Switch to Local' : 'Switch to Business';
                    ?>
                    <a href="?mode=<?php echo $toggleMode; ?>&date=<?php echo $date; ?>" class="btn btn-sm <?php echo $btnClass; ?> shadow-sm px-3 font-weight-bold mr-2">
                        <i class="fas <?php echo $btnIcon; ?> mr-1"></i> <?php echo $btnText; ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/logisticAvailability" class="btn btn-sm btn-outline-primary shadow-sm mr-2">
                        <i class="fas fa-cog fa-sm"></i> Manage Capacity
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pickup Capacity</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo (int)($counts['count_pickup'] ?? 0); ?> / <?php echo (int)($capacity['max_pickup'] ?? 0); ?>
                                    </div>
                                    <div class="progress progress-sm mr-2 mt-2">
                                        <?php $perc = (($capacity['max_pickup'] ?? 0) > 0) ? min(100, (($counts['count_pickup'] ?? 0) / ($capacity['max_pickup'] ?? 0)) * 100) : 0; ?>
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $perc; ?>%"></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Delivery Capacity</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo (int)($counts['count_delivery'] ?? 0); ?> / <?php echo (int)($capacity['max_delivery'] ?? 0); ?>
                                    </div>
                                    <div class="progress progress-sm mr-2 mt-2">
                                        <?php $perc = (($capacity['max_delivery'] ?? 0) > 0) ? min(100, (($counts['count_delivery'] ?? 0) / ($capacity['max_delivery'] ?? 0)) * 100) : 0; ?>
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $perc; ?>%"></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-truck fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Tasks</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($bookings); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Card -->
            <div class="card module-card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-truck-loading mr-2"></i> All Active Logistic Reservations</h6>
                    <small class="text-muted">Filtered by: <b>All Dates</b> | Highlighting: <b><?php echo date('M d, Y', strtotime($date)); ?></b></small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table logistic-table mb-0">
                            <thead class="bg-primary-soft text-primary font-weight-bold">
                                <tr>
                                    <th>Logistic Date</th>
                                    <th>Customer Info</th>
                                    <th>Service Type</th>
                                    <th>Logistics</th>
                                    <th>Location/Map</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bookings)): ?>
                                    <?php foreach ($bookings as $booking): 
                                        $bookingDate = $booking['pickup_date'] ?: $booking['delivery_date'] ?: $booking['booking_date'];
                                        $isToday = ($bookingDate == $date);
                                    ?>
                                        <tr class="<?php echo $isToday ? 'bg-today-highlight' : ''; ?>">
                                            <td>
                                                <div class="font-weight-bold <?php echo $isToday ? 'text-primary' : 'text-dark'; ?>">
                                                    <?php echo date('M d, Y', strtotime($bookingDate)); ?>
                                                </div>
                                                <div class="small text-muted">Created: <?php echo date('m/d/y', strtotime($booking['created_at'])); ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="font-weight-bold text-dark mr-2"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                                                </div>
                                                <div class="customer-id">ID: #<?php echo $booking['id']; ?></div>
                                            </td>
                                            <td>
                                                <div class="text-dark font-weight-600"><?php echo htmlspecialchars($booking['service_name'] ?: 'Custom Repair'); ?></div>
                                            </td>
                                            <td>
                                                <span class="text-info font-weight-bold text-uppercase">
                                                     <?php echo strtoupper($booking['service_option']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="location-box">
                                                    <?php 
                                                    $address = $booking['pickup_address'] ?: $booking['delivery_address'];
                                                    if ($address): 
                                                    ?>
                                                        <div class="small text-dark font-weight-bold text-truncate" title="<?php echo htmlspecialchars($address); ?>">
                                                            
                                                            <?php echo htmlspecialchars($address); ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted small italic">No address Provided</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                $rawStatus = $booking['status'] ?? 'pending_schedule';
                                                $status = strtolower(trim($rawStatus));
                                                if (empty($status)) $status = 'pending_schedule';
                                                
                                                $statusClass = 'badge-secondary';
                                                $statusText = str_replace('_', ' ', $status);
                                                
                                                if ($status == 'pending' || $status == 'pending_schedule') $statusClass = 'badge-warning';
                                                if ($status == 'scheduled') $statusClass = 'badge-success';
                                                if ($status == 'reschedule_requested') $statusClass = 'badge-danger';
                                                if ($status == 'completed') $statusClass = 'badge-primary';
                                                if ($status == 'picked_up') $statusClass = 'badge-info';
                                                ?>
                                                <span class="font-weight-bold text-uppercase <?php echo str_replace('badge-', 'text-', $statusClass); ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group shadow-sm">
                                                    <?php if ($status == 'pending' || $status == 'pending_schedule' || $status == 'reschedule_requested'): ?>
                                                        <button class="btn btn-xs btn-success approve-btn" data-id="<?php echo $booking['id']; ?>" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-xs btn-info reschedule-btn" data-id="<?php echo $booking['id']; ?>" data-type="<?php echo $booking['service_option']; ?>" title="Reschedule">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </button>
                                                        <button class="btn btn-xs btn-danger reject-btn" data-id="<?php echo $booking['id']; ?>" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php elseif ($status == 'scheduled' || $status == 'picked_up' || $status == 'to_inspect' || $status == 'for_pickup'): ?>
                                                        <button class="btn btn-xs btn-primary complete-btn" data-id="<?php echo $booking['id']; ?>">
                                                            <i class="fas fa-check-double mr-1"></i> DONE
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <style>
                .bg-today-highlight {
                    background-color: rgba(78, 115, 223, 0.08);
                    border-left: 4px solid #4e73df;
                }
                .bg-primary-soft {
                    background-color: rgba(78, 115, 223, 0.05);
                }
                .font-weight-600 {
                    font-weight: 600;
                }
            </style>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Reschedule Logistic Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rescheduleForm">
                    <input type="hidden" id="reschedule-id">
                    <input type="hidden" id="reschedule-type">
                    <div class="form-group">
                        <label for="new_date">Select New Date</label>
                        <input type="date" id="new_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-reschedule">Submit Reschedule</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Approve Button (Now opens Review Modal)
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            loadReviewBookingModal(id);
        });
    });

    // Load Review Booking Modal (AJAX)
    function loadReviewBookingModal(bookingId) {
        // Show loading modal
        const modalHtml = `
            <div class="modal fade" id="reviewBookingModal" tabindex="-1" role="dialog" data-backdrop="static">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-info text-white py-3">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-eye mr-2"></i>Review Booking Details (Before Approval)
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body bg-light" id="review-modal-content">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Fetching repair reservation details...</p>
                            </div>
                        </div>
                        <div class="modal-footer bg-white border-top-0">
                            <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">CLOSE</button>
                            <button type="button" class="btn btn-success px-4 font-weight-bold" id="confirm-approve-btn" data-id="${bookingId}">
                                <i class="fas fa-check-circle mr-1"></i> CONFIRM & APPROVE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Clean up previous modal if any
        $('#reviewBookingModal').remove();
        $('body').append(modalHtml);
        $('#reviewBookingModal').modal('show');

        // Fetch details
        fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                displayReviewBookingDetails(data.booking);
            } else {
                $('#review-modal-content').html('<div class="alert alert-danger">Error: ' + (data.message || 'Could not load details') + '</div>');
            }
        })
        .catch(err => {
            $('#review-modal-content').html('<div class="alert alert-danger">Error connecting to server.</div>');
        });
    }

    // Display Review Details in Modal
    function displayReviewBookingDetails(booking) {

        const dateToDisplay = booking.pickup_date || booking.delivery_date || booking.booking_date;
        const formattedDate = dateToDisplay ? new Date(dateToDisplay).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : 'N/A';

        const contentHtml = `
            <div class="row">
                <!-- Customer Info -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden">
                        <div class="card-header bg-dark text-white py-3">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-user-circle mr-2 text-info"></i> Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block Uppercase font-weight-bold">FULL NAME</small>
                                <div class="h5 font-weight-bold text-primary mb-0">${booking.customer_name || 'N/A'}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block Uppercase font-weight-bold">EMAIL ADDRESS</small>
                                <div class="text-dark">${booking.email || 'N/A'}</div>
                            </div>
                            <div>
                                <small class="text-muted d-block Uppercase font-weight-bold">PHONE NUMBER</small>
                                <div class="text-dark font-weight-600">${booking.phone || 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Details -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden">
                        <div class="card-header bg-success text-white py-3">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-tools mr-2 text-white"></i> Service Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block Uppercase font-weight-bold">SERVICE NAME</small>
                                <div class="h6 font-weight-bold text-dark mb-0">${booking.service_name || 'N/A'}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block Uppercase font-weight-bold">CATEGORY</small>
                                <span class="badge badge-info px-3 py-2 mt-1 shadow-xs">${booking.category_name || 'N/A'}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block Uppercase font-weight-bold">SERVICE TYPE</small>
                                <div class="text-dark">${booking.service_type || 'Repair'}</div>
                            </div>
                            <div>
                                <small class="text-muted d-block Uppercase font-weight-bold">ITEM DESCRIPTION</small>
                                <div class="text-dark italic small border-left pl-2 mt-1">${booking.item_description || 'No description provided'}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color & Fabric (Conditional) -->
                ${booking.selected_color_id ? `
                <div class="col-md-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                        <div class="card-header bg-info text-white py-3">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-palette mr-2"></i> Selected Color & Fabric</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <small class="text-muted d-block Uppercase font-weight-bold">COLOR NAME</small>
                                    <div class="h6 font-weight-bold text-dark mb-0">
                                        ${booking.color_name || 'N/A'}
                                        ${booking.color_hex ? `<span class="ml-2 border shadow-sm" style="display:inline-block; width:15px; height:15px; border-radius:50%; background-color:${booking.color_hex}; vertical-align: middle;"></span>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0 border-left border-right">
                                    <small class="text-muted d-block Uppercase font-weight-bold">FABRIC TYPE</small>
                                    <span class="badge badge-pill badge-primary px-3 text-uppercase">${booking.inventory_type || 'Standard'}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block Uppercase font-weight-bold">PRICE PER METER</small>
                                    <div class="h6 font-weight-bold text-primary mb-0">₱${parseFloat(booking.inventory_price_per_meter || 0).toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}

                <!-- Booking Info -->
                <div class="col-md-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                        <div class="card-header bg-warning py-3">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-calendar-check mr-2"></i> Booking Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 mb-2 mb-md-0 border-right">
                                    <small class="text-muted d-block Uppercase font-weight-bold">BOOKING ID</small>
                                    <span class="badge badge-dark px-3 text-uppercase">#${booking.id}</span>
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0 border-right">
                                    <small class="text-muted d-block Uppercase font-weight-bold">SCHEDULED DATE</small>
                                    <div class="h6 font-weight-bold text-danger mb-0">${formattedDate}</div>
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0 border-right">
                                    <small class="text-muted d-block Uppercase font-weight-bold">STATUS</small>
                                    <span class="badge badge-${getStatusBadgeClass(booking.status)} text-uppercase px-2">${booking.status ? booking.status.replace('_', ' ') : 'N/A'}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block Uppercase font-weight-bold">PAYMENT</small>
                                    <span class="badge badge-${getPaymentBadgeClass(booking.payment_status)} px-2">${getPaymentStatusText(booking.payment_status)}</span>
                                </div>
                            </div>

                            <hr class="my-3">
                            
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0 text-left">
                                    <small class="text-muted d-block Uppercase font-weight-bold"><i class="fas fa-truck mr-1 text-primary"></i> SERVICE OPTION</small>
                                    <span class="h6 font-weight-bold text-primary mb-0">${getServiceOptionText(booking.service_option)}</span>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted font-italic">${getServiceOptionDetails(booking)}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes (Conditional) -->
                ${booking.notes ? `
                <div class="col-md-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden border-left-warning" style="border-left: 5px solid #ffc107 !important;">
                        <div class="card-body py-3">
                            <small class="text-muted d-block Uppercase font-weight-bold mb-1"><i class="fas fa-sticky-note mr-1"></i> CUSTOMER NOTES</small>
                            <p class="text-dark mb-0 font-italic">${booking.notes}</p>
                        </div>
                    </div>
                </div>
                ` : ''}

            </div>
        `;
        $('#review-modal-content').html(contentHtml);

        // Bind Confirm Button
        $('#confirm-approve-btn').off('click').on('click', function() {
            const bId = $(this).attr('data-id');
            const originalText = $(this).html();
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> APPROVING...');
            
            processAction('approveLogisticRequest/' + bId);
        });
    }

    // Reject Button
    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Reject this request? it will be cancelled.')) {
                processAction('rejectLogisticRequest/' + id);
            }
        });
    });

    // Complete Button
    document.querySelectorAll('.complete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Mark this logistic task as completed?')) {
                processAction('completeLogisticRequest/' + id);
            }
        });
    });

    // Reschedule Button (Open Modal)
    document.querySelectorAll('.reschedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('reschedule-id').value = this.getAttribute('data-id');
            document.getElementById('reschedule-type').value = this.getAttribute('data-type');
            $('#rescheduleModal').modal('show');
        });
    });

    // Confirm Reschedule
    document.getElementById('confirm-reschedule').addEventListener('click', function() {
        const id = document.getElementById('reschedule-id').value;
        const type = document.getElementById('reschedule-type').value;
        const newDate = document.getElementById('new_date').value;
        
        if (!newDate) {
            alert('Please select a new date');
            return;
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('type', type);
        formData.append('new_date', newDate);

        fetch('<?php echo BASE_URL; ?>admin/rescheduleLogisticRequest', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#rescheduleModal').modal('hide');
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });

    function getStatusBadgeClass(status) {
        const statusClasses = {
            'pending': 'warning',
            'for_dropoff': 'warning',
            'for_pickup': 'info',
            'picked_up': 'primary',
            'to_inspect': 'warning',
            'for_inspect': 'warning',
            'for_inspection': 'warning',
            'inspect_completed': 'success',
            'inspection_completed_waiting_approval': 'info',
            'preview_receipt_sent': 'info',
            'for_repair': 'info',
            'under_repair': 'primary',
            'repair_completed': 'success',
            'repair_completed_ready_to_deliver': 'success',
            'for_quality_check': 'info',
            'ready_for_pickup': 'success',
            'out_for_delivery': 'primary',
            'completed': 'success',
            'delivered_and_paid': 'success',
            'paid': 'success',
            'cancelled': 'danger'
        };
        return statusClasses[status] || 'secondary';
    }

    function getPaymentBadgeClass(status) {
        const paymentClasses = {
            'unpaid': 'danger',
            'paid': 'success',
            'paid_full_cash': 'success',
            'paid_on_delivery_cod': 'success',
            'refunded': 'info',
            'failed': 'warning',
            'cancelled': 'danger'
        };
        return paymentClasses[status] || 'secondary';
    }

    function getPaymentStatusText(status) {
        const paymentTexts = {
            'unpaid': 'Unpaid',
            'paid': 'Paid',
            'paid_full_cash': 'Full Paid (Cash)',
            'paid_on_delivery_cod': 'Paid on Delivery (COD)',
            'refunded': 'Refunded',
            'failed': 'Failed',
            'cancelled': 'Cancelled'
        };
        return paymentTexts[status] || status || 'Unpaid';
    }

    function getServiceOptionText(option) {
        const optionMap = {
            'pickup': 'Pick Up',
            'delivery': 'Delivery Service',
            'both': 'Both (Pick Up & Delivery)',
            'walk_in': 'Walk In'
        };
        return optionMap[option] || option || 'Not specified';
    }

    function getServiceOptionDetails(booking) {
        const serviceOption = booking.service_option || 'pickup';
        let details = '';
        if (serviceOption === 'pickup' || serviceOption === 'both') {
            details += `Pickup Address: ${booking.pickup_address || 'Not provided'} | Date: ${booking.pickup_date ? new Date(booking.pickup_date).toLocaleDateString() : 'Not set'} | Distance: ${booking.distance_km ? parseFloat(booking.distance_km).toFixed(2) : '0.00'} km`;
        }
        if (serviceOption === 'delivery' || serviceOption === 'both') {
            if (details) details += ' || ';
            details += `Delivery Address: ${booking.delivery_address || 'Not provided'} | Date: ${booking.delivery_date ? new Date(booking.delivery_date).toLocaleDateString() : 'Not set'}`;
        }
        return details || 'No additional logistics details provided';
    }

    function processAction(endpoint) {
        fetch('<?php echo BASE_URL; ?>admin/' + endpoint)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
                // Re-enable approve btn if failed
                $('#confirm-approve-btn').prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> CONFIRM & APPROVE');
            }
        });
    }
});
</script>
