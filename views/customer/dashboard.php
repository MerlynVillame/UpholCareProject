<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.dashboard-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-card {
    border-radius: 1rem;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.stat-card:hover {
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
    transform: translateY(-5px);
}

.stat-card-modern {
    position: relative;
    background: white;
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card-modern.card-primary::before {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);
}

.stat-card-modern.card-warning::before {
    background: linear-gradient(90deg, #f6c23e 0%, #ffa502 100%);
}

.stat-card-modern.card-info::before {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);
}

.stat-card-modern.card-success::before {
    background: linear-gradient(90deg, #1cc88a 0%, #00b894 100%);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
}

.stat-icon-primary {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);
}

        .stat-card.active { background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-blue) 100%); }
        .stat-card.pending { background: linear-gradient(135deg, var(--accent-orange) 0%, #d35400 100%); }
        .stat-card.completed { background: linear-gradient(135deg, var(--success-green) 0%, #2ecc71 100%); }

.stat-value {
    font-size: 2.25rem;
    font-weight: 800;
    color: #2c3e50;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #858796;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.stat-change.positive {
    color: #1cc88a;
}

.stat-change.negative {
    color: #e74c3c;
}

/* Booking Detail Modal Styles */
.booking-detail-card {
    border-radius: 0.5rem;
    overflow: hidden;
}

.detail-section {
    padding: 1.25rem;
    border-bottom: 1px solid #e3e6f0;
}

.detail-section:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.7rem;
    color: #858796;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 0.95rem;
    color: #2c3e50;
    font-weight: 500;
}

.progress-timeline {
    position: relative;
    padding-left: 20px;
}

.progress-timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 5px;
    bottom: 5px;
    width: 2px;
    background: #e3e6f0;
}
.btn-new-booking {
    background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%);
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: var(--br-modern);
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.4);
}

.btn-new-booking:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(33, 150, 243, 0.5);
    color: white;
}

.btn-reserve-customer {
    background: var(--uphol-orange) !important;
    border-color: var(--uphol-orange) !important;
    border-radius: var(--br-modern) !important;
}

.btn-reserve-customer:hover {
    background: #e67e22 !important;
    border-color: #e67e22 !important;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card {
    animation: fadeIn 0.5s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Override Bootstrap primary colors with brown */
.btn-primary,
.btn-info {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%) !important;
    border-color: #1F4E79 !important;
    color: white !important;
}

.btn-primary:hover,
.btn-info:hover {
    background: var(--uphol-navy) !important;
    border-color: var(--uphol-navy) !important;
    color: white !important;
}

.text-primary {
    color: var(--uphol-blue) !important;
}

/* Ensure stat card icons use brand colors */
.stat-icon-primary {
    background: var(--uphol-blue) !important;
}
.stat-icon-warning {
    background: var(--uphol-orange) !important;
}
.stat-icon-success {
    background: var(--uphol-green) !important;
}
.stat-icon-info {
    background: var(--uphol-navy) !important;
}
.card-primary::before {
    background: var(--uphol-blue) !important;
}
.card-warning::before {
    background: var(--uphol-orange) !important;
}
.card-success::before {
    background: var(--uphol-green) !important;
}
.card-info::before {
    background: var(--uphol-navy) !important;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="dashboard-title mb-2">Dashboard</h1>
        <p class="text-muted mb-0">Welcome back, <strong><?php echo htmlspecialchars($user['name']); ?></strong>!</p>
    </div>
    <button type="button" class="btn btn-new-booking shadow-sm" onclick="openReservationModal()">
        <i class="fas fa-tools mr-2"></i>Repair Reservation
    </button>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Bookings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-primary shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-icon stat-icon-primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $totalBookings; ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up mr-1"></i>12% from last month
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Bookings Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-warning shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Pending</div>
                    <div class="stat-icon stat-icon-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $pendingBookings; ?></div>
                <div class="stat-change">
                    <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i>Awaiting confirmation
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-info shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-icon stat-icon-info">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $inProgressBookings; ?></div>
                <div class="stat-change">
                    <i class="fas fa-circle mr-1" style="font-size: 0.5rem; color: #1F4E79;"></i>Currently working
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-modern card-success shadow h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-label">Completed</div>
                    <div class="stat-icon stat-icon-success">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
                <div class="stat-value mb-2"><?php echo $completedBookings; ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-check-circle mr-1"></i>Successfully finished
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- My Recent Bookings -->
    <div class="col-lg-12 mb-4">
        <div class="card stat-card stat-card-modern card-primary shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: white; border-bottom: 1px solid #e3e6f0;">
                    <h6 class="m-0 font-weight-bold" style="color: #2c3e50;">
                        <i class="fas fa-list-alt mr-2" style="color: #1F4E79;"></i>My Recent Bookings
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentBookings)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $booking): ?>
                                <tr>
                                    <td><strong class="text-primary"><?php echo htmlspecialchars($booking['booking_number'] ?? 'N/A'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['service_name'] ?? 'Unknown Service'); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'badge-' . str_replace('_', '-', $booking['status'] ?? 'pending');
                                        $statusText = ucwords(str_replace('_', ' ', $booking['status'] ?? 'pending'));
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-info"
                                                onclick="viewBookingDetails(<?php echo $booking['id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No bookings yet</h5>
                        <p class="text-muted mb-3">Start by creating your first booking</p>
                        <button type="button" onclick="openReservationModal()" class="btn btn-reserve-customer shadow-sm">
                            <i class="fas fa-calendar-plus fa-sm text-white-50 mr-2"></i> New Repair Reservation
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content overflow-hidden border-0" style="border-radius: 0.8rem;">
            <div class="p-0 border-0" id="bookingDetailsModalBody">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <p class="text-muted">Fetching booking details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content overflow-hidden border-0" style="border-radius: 1rem; box-shadow: 0 1rem 3rem rgba(0,0,0,0.2);">
            <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); color: white;">
                <h5 class="modal-title font-weight-bold" id="reservationModalLabel">
                    <i class="fas fa-tools mr-2"></i>Create Repair Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="reservationModalBody">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <p class="text-muted">Loading reservation form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Open Reservation Modal
 */
function openReservationModal() {
    // Show modal
    $('#reservationModal').modal('show');
    
    // Reset body with spinner
    document.getElementById('reservationModalBody').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <p class="text-muted">Loading reservation form...</p>
        </div>
    `;
    
    // Fetch partial content
    fetch('<?php echo BASE_URL; ?>customer/newRepairReservationPartial')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            document.getElementById('reservationModalBody').innerHTML = html;
            
            // Re-execute scripts in the loaded HTML
            const doc = document.getElementById('reservationModalBody');
            const scripts = doc.getElementsByTagName('script');
            for (let i = 0; i < scripts.length; i++) {
                // Execute script content
                try {
                    const scriptVar = scripts[i].innerText || scripts[i].textContent;
                    const newScript = document.createElement('script');
                    newScript.text = scriptVar;
                    document.body.appendChild(newScript).parentNode.removeChild(newScript);
                } catch (e) {
                    console.error('Error executing script:', e);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reservationModalBody').innerHTML = `
                <div class="alert alert-danger m-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Error loading form. Please try again.
                </div>
            `;
        });
}

/**
 * View Booking Details in Modal
 */
function viewBookingDetails(id) {
    // Reset modal content
    $('#bookingDetailsModalBody').html(`
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <p class="text-muted">Fetching booking details...</p>
        </div>
    `);
    
    // Show modal
    $('#bookingDetailsModal').modal('show');
    
    // Fetch partial content
    fetch('<?php echo BASE_URL; ?>customer/viewBookingPartial/' + id)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            $('#bookingDetailsModalBody').html(html);
            
            // Wait for partial to be rendered then load progress if element exists
            setTimeout(() => {
                const progressContainer = document.getElementById('modal_progress_history');
                if (progressContainer) {
                    loadModalProgressHistory(id);
                }
            }, 100);
        })
        .catch(error => {
            console.error('Error:', error);
            $('#bookingDetailsModalBody').html(`
                <div class="alert alert-danger m-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Error loading booking details. Please try again or refresh the page.
                </div>
                <div class="text-right p-3">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            `);
        });
}

/**
 * Load Progress History into Modal
 */
function loadModalProgressHistory(bookingId) {
    const container = document.getElementById('modal_progress_history');
    const section = document.getElementById('modal_progress_section');
    
    if (!container) return;
    
    fetch('<?php echo BASE_URL; ?>customer/getBookingProgress/' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.progress && data.progress.length > 0) {
                if (section) section.style.display = 'block';
                
                let html = '';
                data.progress.forEach(item => {
                    const date = new Date(item.created_at).toLocaleString();
                    html += `
                        <div class="progress-item mb-3 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge badge-info">${(item.status || item.progress_type || 'Update').replace(/_/g, ' ').toUpperCase()}</span>
                                <small class="text-muted">${date}</small>
                            </div>
                            <div class="small">${item.description || item.remarks || 'No description provided'}</div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="text-center text-muted py-2 small">No progress records found yet.</div>';
            }
        })
        .catch(error => {
            console.error('Error loading progress:', error);
            container.innerHTML = '<div class="text-center text-danger py-2 small">Failed to load progress.</div>';
        });
}

/**
 * Confirm and Handle Booking Cancellation
 */
function confirmCancelBooking(bookingId) {
    // Use the custom dialog system from footer if available, otherwise fallback
    const message = 'Are you sure you want to cancel this booking? This action cannot be undone.';
    
    if (window.confirm) {
        window.confirm(message).then(confirmed => {
            if (confirmed) {
                window.location.href = '<?php echo BASE_URL; ?>customer/cancelBooking/' + bookingId;
            }
        });
    } else if (confirm(message)) {
        window.location.href = '<?php echo BASE_URL; ?>customer/cancelBooking/' + bookingId;
    }
}
</script>

