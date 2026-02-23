<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.dashboard-container {
    background-color: #f8fafc;
    /* Removed min-height to fix forced scrolling */
    padding: 1.5rem;
    padding-top: 1.5rem !important; /* Ensure content isn't cut off */
    margin-top: -1rem !important; /* Reduce gap from navbar safely */
}
.btn-new-booking {
    background: #0F3C5F;
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: var(--br-modern);
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(15, 60, 95, 0.3);
}

.btn-new-booking:hover {
    background: #1F4E79;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 60, 95, 0.4);
    color: white;
}

.breadcrumb-modern {
    background: transparent;
    padding: 0;
    margin-bottom: 0.5rem;
}

/* ... existing CSS ... */

.empty-state-modern {
    padding: 4rem 2rem;
    text-align: center;
    background: #ffffff;
    border-radius: 16px;
    border: 2px dashed #e2e8f0;
    /* Removed min-height and fixed height as requested */
}

/* ... other CSS ... */
.welcome-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
    padding: 1rem 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid rgba(227, 230, 240, 0.6);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.welcome-text {
    color: #0F3C5F;
    font-weight: 700;
    font-size: 1.15rem;
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0;
}

/* Search and Filter Section - Aligned with Catalog Design */
.search-filter-section {
    background: white;
    padding: 1rem 1.25rem;
    border-radius: 1.25rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    border: 1px solid rgba(227, 230, 240, 0.6);
    margin-bottom: 1.5rem;
}

.search-box {
    position: relative;
    margin-bottom: 1rem;
    width: 100%;
    max-width: 550px;
}

.search-box input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 1.5px solid #e3e6f0;
    border-radius: 50px;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    background: #f8f9fc;
}

.search-box input:focus {
    background: white;
    border-color: #0F3C5F;
    outline: none;
    box-shadow: 0 4px 15px rgba(15, 60, 95, 0.1);
}

.search-box i {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #0F3C5F;
    font-size: 0.95rem;
}

.custom-select-modern {
    padding: 0.75rem 2.5rem 0.75rem 1.25rem;
    border: 1.5px solid #e3e6f0;
    border-radius: 50px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #5a5c69;
    background: #f8f9fc url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat right 1rem center/10px 10px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    min-width: 200px;
}

.custom-select-modern:focus {
    background-color: white;
    border-color: #0F3C5F;
    outline: none;
    box-shadow: 0 4px 15px rgba(15, 60, 95, 0.1);
}

.filter-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    width: 100%;
}

@media (max-width: 768px) {
    .filter-wrapper {
        flex-direction: column;
        align-items: stretch;
    }
    .search-box {
        max-width: none;
    }
}
</style>

<div class="container-fluid dashboard-container">
    <!-- Page Header -->
    <div class="welcome-container shadow-sm">
        <div class="welcome-text">
            <i class="fas fa-calendar-check mr-2" style="color: #0F3C5F;"></i>
            My Bookings
        </div>
        <div class="d-flex align-items-center">
            <button class="action-btn-dashboard mr-3" onclick="location.reload()" title="Refresh List" style="background: rgba(15, 60, 95, 0.05); border: none; border-radius: 50%; width: 35px; height: 35px; color: #0F3C5F; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn btn-new-booking shadow-sm" onclick="openReservationModal()" 
                    style="font-size: 0.85rem; padding: 0.6rem 1.5rem; border-radius: 50px; font-weight: 700;">
                <i class="fas fa-tools mr-2"></i>Repair Reservation
            </button>
        </div>
    </div>

    <?php
    $hasServices = !empty($bookings);
    $hasRepairs = !empty($repairReservations);
    $hasAnyBookings = $hasServices || $hasRepairs;
    ?>

    <?php if ($hasAnyBookings): ?>
        <!-- Filter Section -->
        <div class="search-filter-section shadow-sm border-0">
            <div class="filter-wrapper">
                <div class="search-box mb-0">
                    <i class="fas fa-search"></i>
                    <input type="text" id="bookingSearchInput" placeholder="Search by service name or reference #..." onkeyup="performBookingsFilter()">
                </div>
                
                <div class="dropdown-container">
                    <select id="statusFilterSelect" class="custom-select-modern" onchange="updateStatusFilter(this.value)">
                        <option value="all">All Reservations</option>
                        <?php
                        // Combine statuses from both arrays for unique filter list
                        $uniqueStatuses = [];
                        
                        if ($hasServices) {
                            foreach ($bookings as $booking) {
                                $status = strtolower(trim($booking['status'] ?? 'pending'));
                                if (!in_array($status, $uniqueStatuses)) $uniqueStatuses[] = $status;
                            }
                        }
                        
                        if ($hasRepairs) {
                            foreach ($repairReservations as $reservation) {
                                $status = strtolower(trim($reservation['status'] ?? 'pending'));
                                if (!in_array($status, $uniqueStatuses)) $uniqueStatuses[] = $status;
                            }
                        }
                        
                        sort($uniqueStatuses);
                        $statusDisplayNames = [
                            'pending' => 'Pending',
                            'pending_schedule' => 'Pending Schedule Request',
                            'scheduled' => 'Scheduled',
                            'reschedule_requested' => 'Reschedule Requested',
                            'for_pickup' => 'For Pickup',
                            'picked_up' => 'Picked Up',
                            'to_inspect' => 'To Inspect',
                            'for_inspection' => 'For Inspection',
                            'for_repair' => 'For Repair',
                            'under_repair' => 'Under Repair',
                            'for_quality_check' => 'For Quality Check',
                            'ready_for_pickup' => 'Ready for Pickup',
                            'out_for_delivery' => 'Out for Delivery',
                            'completed' => 'Completed',
                            'paid' => 'Paid',
                            'closed' => 'Closed',
                            'cancelled' => 'Cancelled',
                            'approved' => 'Approved',
                            'confirmed' => 'Confirmed',
                            'accepted' => 'Accepted',
                            'in_progress' => 'In Progress',
                            'ongoing' => 'Ongoing'
                        ];
                        foreach ($uniqueStatuses as $status): 
                            $val = strtolower($status);
                            $displayName = $statusDisplayNames[$val] ?? ucfirst(str_replace('_', ' ', $val));
                        ?>
                            <option value="<?php echo $val; ?>"><?php echo $displayName; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <?php if ($hasServices): ?>
            <div class="services-table-wrapper">
                <div class="table-responsive bg-white rounded-lg shadow-sm border mb-5">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark">
                            <tr>
                                <th class="py-3 pl-4 border-bottom-0" style="width: 5%; color: #e9eeffff;">ID</th>
                                <th class="py-3 border-bottom-0" style="width: 25%; color: #e9eeffff;">Service Info</th>
                                <th class="py-3 border-bottom-0" style="width: 20%; color: #e9eeffff;">Service Type</th>
                                <th class="py-3 border-bottom-0" style="width: 20%; color: #e9eeffff;">Date & Time</th>
                                <th class="py-3 border-bottom-0" style="width: 15%; color: #e9eeffff;">Status</th>
                                <th class="py-3 text-right pr-4 border-bottom-0" style="width: 15%; color: #e9eeffff;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="servicesContainer">
                        <?php foreach ($bookings as $booking): 
                            $status = strtolower(trim($booking['status'] ?? 'pending'));
                            $bookingId = $booking['id'];
                            
                            // Map status to pill classes
                            $pillClass = 'pending';
                            if (in_array($status, ['approved', 'confirmed', 'accepted', 'scheduled'])) $pillClass = 'approved';
                            if (in_array($status, ['under_repair', 'ongoing', 'for_repair'])) $pillClass = 'in_progress';
                            if (in_array($status, ['completed', 'paid', 'delivered_and_paid'])) $pillClass = 'completed';
                            if (in_array($status, ['cancelled', 'rejected', 'declined', 'reschedule_requested'])) $pillClass = 'cancelled';
                            
                            // Icon for service option
                            $opt = strtolower($booking['service_option'] ?? 'pickup');
                            $optIcon = ['pickup' => 'truck-loading', 'delivery' => 'truck', 'both' => 'exchange-alt', 'walk_in' => 'walking'][$opt] ?? 'info-circle';
                        ?>
                        <tr class="booking-row booking-card-item" data-id="<?php echo $bookingId; ?>" data-booking-id="<?php echo $bookingId; ?>" data-status="<?php echo $status; ?>">
                            <td class="pl-4 align-middle">
                                <span class="text-muted font-weight-bold">#<?php echo $bookingId; ?></span>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark" style="font-size: 1rem;"><?php echo htmlspecialchars($booking['service_name']); ?></div>
                                <div class="small text-muted">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    <?php echo htmlspecialchars($booking['category_name'] ?? 'General'); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center text-secondary">
                                    <i class="fas fa-<?php echo $optIcon; ?> mr-2"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $opt)); ?>
                                </div>
                                <?php if (!empty($booking['service_type'])): ?>
                                    <div class="small text-info font-weight-bold mt-1">
                                        <?php echo htmlspecialchars($booking['service_type']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <div class="text-dark">
                                    <i class="far fa-calendar-alt mr-1 text-muted"></i>
                                    <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-clock mr-1"></i>
                                    <?php echo date('h:i A', strtotime($booking['created_at'])); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="status-pill <?php echo $pillClass; ?>">
                                    <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                </span>
                            </td>
                            <td class="text-right pr-4 align-middle">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="action-btn-dashboard action-btn-view" onclick="viewReservationDetails(<?php echo $bookingId; ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if (in_array($status, ['pending', 'ready_for_pickup', 'for_pickup'])): ?>
                                        <button class="action-btn-dashboard action-btn-edit" onclick="openUpdateBookingModal(<?php echo $bookingId; ?>)" title="Update Booking">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($status === 'pending'): ?>
                                        <button class="action-btn-dashboard action-btn-cancel" onclick="confirmCancelReservation(<?php echo $bookingId; ?>)" title="Cancel Request">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
<?php endif; ?>
        
        <!-- Repairs Section -->
        <?php if ($hasRepairs): ?>
            <h5 class="repairs-heading mb-3 font-weight-bold text-dark <?php echo $hasServices ? 'mt-5' : ''; ?>">Repair Requests</h5>
            <div class="repairs-table-wrapper">
                <div class="table-responsive bg-white rounded-lg shadow-sm border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 pl-4 border-bottom-0" style="width: 5%; color: #4e73df;">ID</th>
                            <th class="py-3 border-bottom-0" style="width: 25%; color: #4e73df;">Item Name</th>
                            <th class="py-3 border-bottom-0" style="width: 15%; color: #4e73df;">Urgency</th>
                            <th class="py-3 border-bottom-0" style="width: 25%; color: #4e73df;">Date & Time</th>
                            <th class="py-3 border-bottom-0" style="width: 15%; color: #4e73df;">Status</th>
                            <th class="py-3 text-right pr-4 border-bottom-0" style="width: 15%; color: #4e73df;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="repairsContainer">
                        <?php foreach ($repairReservations as $reservation): 
                            $rStatus = strtolower(trim($reservation['status'] ?? 'pending'));
                            $repairId = $reservation['id'];
                            $rPaymentStatus = strtolower($reservation['payment_status'] ?? 'unpaid');
                            
                            // Map status to pill classes
                            $pillClass = 'pending';
                            if (in_array($rStatus, ['approved', 'confirmed', 'accepted'])) $pillClass = 'approved';
                            if (in_array($rStatus, ['in_progress', 'ongoing', 'under_repair'])) $pillClass = 'in_progress';
                            if (in_array($rStatus, ['completed'])) $pillClass = 'completed';
                            if (in_array($rStatus, ['cancelled', 'rejected', 'declined'])) $pillClass = 'cancelled';
            
                            $urgencyCfg = [
                                'low' => ['class' => 'secondary', 'icon' => 'arrow-down'],
                                'normal' => ['class' => 'info', 'icon' => 'minus'],
                                'high' => ['class' => 'warning', 'icon' => 'arrow-up'],
                                'urgent' => ['class' => 'danger', 'icon' => 'exclamation-circle']
                            ][$reservation['urgency']] ?? ['class' => 'secondary', 'icon' => 'question-circle'];
                        ?>
                        <tr class="booking-row booking-card-item" data-id="<?php echo $repairId; ?>" data-booking-id="<?php echo $repairId; ?>" data-status="<?php echo $rStatus; ?>">
                            <td class="pl-4 align-middle">
                                <span class="text-muted font-weight-bold">#<?php echo $repairId; ?></span>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark" style="font-size: 1rem;"><?php echo htmlspecialchars($reservation['item_name']); ?></div>
                                <div class="small text-muted">
                                    <i class="fas fa-wrench mr-1"></i> Repair Request
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="booking-meta-item">
                                    <i class="fas fa-<?php echo $urgencyCfg['icon']; ?> text-<?php echo $urgencyCfg['class']; ?> mr-2"></i>
                                    <?php echo ucfirst($reservation['urgency']); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="text-dark">
                                    <i class="far fa-calendar-alt mr-1 text-muted"></i>
                                    <?php echo date('M d, Y', strtotime($reservation['created_at'])); ?>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-clock mr-1"></i>
                                    <?php echo date('h:i A', strtotime($reservation['created_at'])); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="status-pill <?php echo $pillClass; ?>">
                                    <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $rStatus)); ?>
                                </span>
                            </td>
                            <td class="text-right pr-4 align-middle">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?php echo BASE_URL; ?>customer/viewRepairReservation/<?php echo $repairId; ?>" class="action-btn-dashboard action-btn-view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($rStatus === 'pending'): ?>
                                        <a href="<?php echo BASE_URL; ?>customer/editRepairReservation/<?php echo $repairId; ?>" class="action-btn-dashboard action-btn-edit" title="Edit Reservation">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="action-btn-dashboard action-btn-cancel" onclick="confirmCancelRepair(<?php echo $repairId; ?>)" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (in_array($rStatus, ['approved', 'accepted', 'confirmed']) && $rPaymentStatus !== 'paid'): ?>
                                        <button class="action-btn-dashboard" onclick="payNow(<?php echo $repairId; ?>)" title="Pay Now" style="background-color: #dcfce7; color: #16a34a;">
                                            <i class="fas fa-credit-card"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Global Empty State (Only shows if NO bookings at all) -->
        <div class="empty-state-modern mt-4">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <h3 class="empty-state-title">No reservations found</h3>
            <p class="text-muted mb-4">You haven't made any service or repair reservations yet. Create one to get started! 🚀</p>
            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-primary px-4" onclick="openReservationModal()">
                    <i class="fas fa-plus mr-2"></i>New Reservation
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Repair Reservation Modal -->
<?php require_once ROOT . DS . 'views' . DS . 'customer' . DS . 'repair_reservation_modal_wrapper.php'; ?>

<!-- Cancel Reservation Confirmation Modal -->
<div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-labelledby="cancelReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="cancelReservationModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Cancel Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-times-circle" style="font-size: 4rem; color: #e74c3c;"></i>
                </div>
                <h5 style="color: #2c3e50; font-weight: 700;">Are you sure?</h5>
                <p class="text-muted mb-4">
                    Do you want to cancel this reservation? This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-arrow-left mr-1"></i>No, Keep It
                </button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-check mr-1"></i>Yes, Cancel Reservation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update Booking Modal -->
<div class="modal fade" id="updateBookingModal" tabindex="-1" aria-labelledby="updateBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #1F4E79 0%, #0F3C5F 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="updateBookingModalLabel">
                    <i class="fas fa-edit mr-2"></i>Update Booking Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4">
                <form id="updateBookingForm">
                    <input type="hidden" id="update_booking_id" name="booking_id">
                    
                    <div class="form-group">
                        <label for="update_service_option"><strong>Service Option</strong></label>
                        <select class="form-control" id="update_service_option" name="service_option" required>
                            <option value="pickup">Pickup</option>
                            <option value="delivery">Delivery</option>
                            <option value="both">Both (Pickup & Delivery)</option>
                            <option value="walk_in">Walk In</option>
                        </select>
                        <small class="form-text text-muted">Select how you want to receive the service</small>
                    </div>
                    
                    <div class="form-group" id="update_pickup_date_group">
                        <label for="update_pickup_date"><strong>Pickup Date</strong></label>
                        <input type="date" class="form-control" id="update_pickup_date" name="pickup_date">
                        <small class="form-text text-muted">Preferred date for pickup</small>
                    </div>
                    
                    <div class="form-group" id="update_delivery_date_group">
                        <label for="update_delivery_date"><strong>Delivery Date</strong></label>
                        <input type="date" class="form-control" id="update_delivery_date" name="delivery_date">
                        <small class="form-text text-muted">Preferred date for delivery</small>
                    </div>
                    
                    <div class="form-group" id="update_pickup_address_group" style="display: none;">
                        <label for="update_pickup_address"><strong>Pickup Address</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="update_pickup_address" name="pickup_address" rows="3" placeholder="Enter your pickup address"></textarea>
                        <small class="form-text text-muted">Where should we pick up your item?</small>
                    </div>
                    
                    <div class="form-group" id="update_delivery_address_group" style="display: none;">
                        <label for="update_delivery_address"><strong>Delivery Address</strong> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="update_delivery_address" name="delivery_address" rows="3" placeholder="Enter your delivery address"></textarea>
                        <small class="form-text text-muted">Where should we deliver your item?</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="update_booking_submit_btn" onclick="submitUpdateBooking()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-save mr-1"></i>Update Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Receipt Modal -->
<div class="modal fade" id="previewReceiptModal" tabindex="-1" aria-labelledby="previewReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 800px; margin-left: 250px;">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="previewReceiptModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Preview Receipt - Review Before Approval
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Please review the preview receipt below.</strong> Once you approve, your item will move to "Under Repair" status and repair work will begin.
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><strong>Booking Information</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Booking ID:</strong> <span id="preview_receipt_booking_id">-</span></p>
                                <p class="mb-2"><strong>Customer:</strong> <span id="preview_receipt_customer_name">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Service:</strong> <span id="preview_receipt_service">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-cut mr-2"></i><strong>Materials Used</strong></h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Yards/Meters:</strong> <span id="preview_receipt_material_meters">-</span> @ <span id="preview_receipt_price_per_meter">₱0.00</span> = <span id="preview_receipt_material_cost" class="text-success">₱0.00</span></p>
                        <p class="mb-2"><strong>Foam Replacement:</strong> <span id="preview_receipt_foam_replacement">-</span> <span id="preview_receipt_foam_cost" class="text-success"></span></p>
                        <p class="mb-2"><strong>Accessories:</strong> <span id="preview_receipt_accessories">-</span> <span id="preview_receipt_accessories_cost" class="text-success"></span></p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><strong>Payment Breakdown</strong></h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td><strong>Labor Fee Subtotal:</strong></td>
                                    <td class="text-right"><span id="preview_receipt_labor">₱0.00</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Subtotal (Before VAT):</strong></td>
                                    <td class="text-right"><span id="preview_receipt_subtotal">₱0.00</span></td>
                                </tr>
                                <tr class="table-success" style="font-size: 1.2rem; font-weight: bold;">
                                    <td><strong>FINAL TOTAL AMOUNT:</strong></td>
                                    <td class="text-right"><span id="preview_receipt_total" style="font-weight: bold;">₱0.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
                <button type="button" class="btn btn-danger" id="rejectFromPreviewBtn" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times-circle mr-1"></i>Reject Receipt
                </button>
                <button type="button" class="btn btn-success" id="approveFromPreviewBtn" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-check-circle mr-1"></i>Approve Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle" style="font-size: 5rem; color: #28a745;"></i>
                </div>
                <h4 class="mb-3" style="color: #2c3e50; font-weight: 700;">Success!</h4>
                <p class="text-muted mb-4" id="successMessage">
                    Your action has been completed successfully.
                </p>
                <button type="button" class="btn btn-success" data-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 2rem; font-weight: 600;">
                    <i class="fas fa-thumbs-up mr-2"></i>Got It!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Confirmation Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="bulkActionModalLabel">
                    <i class="fas fa-tasks mr-2"></i>Confirm Bulk Action
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-question-circle" style="font-size: 4rem; color: #1F4E79;"></i>
                </div>
                <p class="text-muted mb-4" id="bulkActionMessage">
                    Are you sure you want to perform this action on <strong id="bulkActionCount">0</strong> selected reservation(s)?
                </p>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmBulkActionBtn" style="border-radius: 8px; padding: 0.5rem 1.5rem; background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); border: none;">
                    <i class="fas fa-check mr-1"></i>Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Checkbox styling removed */

.booking-row:hover {
    background-color: rgba(139, 69, 19, 0.05);
}

.booking-row.selected {
    background-color: rgba(139, 69, 19, 0.1);
}

/* Modal animations */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}


/* Print styles for booking details modal */
@media print {
    body * { visibility: hidden; }
    #bookingDetailsModal, #bookingDetailsModal * { visibility: visible; }
    #bookingDetailsModal {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        border: none;
    }
    #bookingDetailsModal .modal-dialog {
        max-width: 100%;
        margin: 0;
    }
    #bookingDetailsModal .modal-header .close,
    #bookingDetailsModal .modal-footer {
        display: none !important;
    }
}

/* Override Bootstrap primary colors with brown */
.btn-primary {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%) !important;
    border-color: #0F3C5F !important;
    color: white !important;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1F4E79 0%, #0F3C5F 100%) !important;
    border-color: #1F4E79 !important;
    color: white !important;
}

.btn-info {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%) !important;
    border-color: #0F3C5F !important;
    color: white !important;
}

.btn-info:hover {
    background: linear-gradient(135deg, #1F4E79 0%, #0F3C5F 100%) !important;
    border-color: #1F4E79 !important;
    color: white !important;
}

.text-primary {
    color: #1F4E79 !important;
}
</style>

<script>
// Show success/error messages
<?php if (isset($_SESSION['success'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    showNotification('success', '<?php echo addslashes($_SESSION['success']); ?>');
});
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    showNotification('error', '<?php echo addslashes($_SESSION['error']); ?>');
});
<?php unset($_SESSION['error']); endif; ?>

// Confirm Cancel Reservation Function
let reservationToCancel = null;

// View Preview Receipt before approval
function viewPreviewReceipt(bookingId) {
    // Store booking ID for approval
    window.currentPreviewBookingId = bookingId;
    
    // Fetch preview receipt data
    fetch(`<?php echo BASE_URL; ?>customer/getPreviewReceipt/${bookingId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.receipt) {
            const receipt = data.receipt;
            
            // Populate preview receipt modal
            document.getElementById('preview_receipt_booking_id').textContent = 'Booking #' + bookingId;
            document.getElementById('preview_receipt_customer_name').textContent = receipt.customer?.name || 'N/A';
            document.getElementById('preview_receipt_service').textContent = receipt.service?.name || 'N/A';
            
            // Materials Used - match admin's format exactly
            const materials = receipt.materials || [];
            const payment = receipt.payment || {};
            
            // Get quality type (Standard/Premium) - from booking data
            // Priority: color_type > fabric_type > leather_type > default 'standard'
            const qualityType = (receipt.booking?.colorType || receipt.booking?.fabricType || receipt.booking?.leatherType || 'standard').toLowerCase();
            const qualityText = qualityType === 'premium' ? 'Premium' : 'Standard';
            document.getElementById('preview_receipt_leather_quality').textContent = qualityText;
            document.getElementById('preview_receipt_fabric_type').textContent = qualityText;
            
            // Get color name, code, and price - format: "blue (INV-003) - ₱200.00/meter"
            let colorDisplay = '-';
            const colorName = receipt.booking?.colorName || null;
            const colorCode = receipt.booking?.colorCode || null;
            
            // Get price per meter from materials or payment data
            let pricePerMeter = 0;
            if (materials.length > 0 && materials[0].price) {
                pricePerMeter = parseFloat(materials[0].price || 0);
            } else if (payment.materialSubtotal && materials.length > 0 && materials[0].quantity) {
                // Calculate from material cost and quantity
                pricePerMeter = parseFloat(payment.materialSubtotal) / parseFloat(materials[0].quantity || 1);
            }
            
            if (colorName && colorName !== '-' && pricePerMeter > 0) {
                if (colorCode && colorCode.trim() !== '') {
                    colorDisplay = `${colorName} (${colorCode}) - ₱${pricePerMeter.toFixed(2)}/meter`;
            } else {
                    colorDisplay = `${colorName} - ₱${pricePerMeter.toFixed(2)}/meter`;
            }
            } else if (materials.length > 0 && materials[0].name) {
                // Fallback: extract from material name
                const materialName = materials[0].name;
                if (pricePerMeter > 0) {
                    colorDisplay = `${materialName} - ₱${pricePerMeter.toFixed(2)}/meter`;
                } else {
                    colorDisplay = materialName;
                }
            }
            document.getElementById('preview_receipt_leather_color').textContent = colorDisplay;
            
            // Get material meters, price per meter, and cost
            const meters = materials.length > 0 ? (materials[0].quantity || 0) : 0;
            const materialCost = materials.length > 0 ? parseFloat(materials[0].total || 0) : parseFloat(payment.fabricCost || 0);
            
            // If price per meter is 0, try to calculate from material cost and meters
            if (pricePerMeter === 0 && meters > 0 && materialCost > 0) {
                pricePerMeter = materialCost / meters;
            }
            
            document.getElementById('preview_receipt_material_meters').textContent = meters || '0';
            document.getElementById('preview_receipt_price_per_meter').textContent = '₱' + pricePerMeter.toFixed(2);
            document.getElementById('preview_receipt_material_cost').textContent = '₱' + materialCost.toFixed(2);
            
            // Foam replacement
            const foamCost = parseFloat(payment.foamCost || 0);
            if (foamCost > 0) {
                document.getElementById('preview_receipt_foam_replacement').textContent = '₱' + foamCost.toFixed(2);
                document.getElementById('preview_receipt_foam_cost').textContent = '';
    } else {
                document.getElementById('preview_receipt_foam_replacement').textContent = 'No foam replacement';
                document.getElementById('preview_receipt_foam_cost').textContent = '';
            }
            
            // Accessories
            const accessoriesCost = parseFloat(payment.miscMaterialsCost || 0);
            if (accessoriesCost > 0) {
                document.getElementById('preview_receipt_accessories').textContent = '₱' + accessoriesCost.toFixed(2);
                document.getElementById('preview_receipt_accessories_cost').textContent = '';
            } else {
                document.getElementById('preview_receipt_accessories').textContent = 'None';
                document.getElementById('preview_receipt_accessories_cost').textContent = '';
            }
            
            // Payment breakdown - use the EXACT values from admin's calculation
            // Do NOT recalculate - use the stored values that admin sent
            // (payment already declared above)
            
            // Use exact stored values from admin (these are what admin calculated and saved)
            const laborFee = parseFloat(payment.laborFee || 0);
            const fabricCost = parseFloat(payment.fabricCost || 0);
            const subtotal = parseFloat(payment.subtotal || (materialSubtotal + laborFee));
            document.getElementById('preview_receipt_subtotal').textContent = '₱' + subtotal.toFixed(2);
            
            // Use the EXACT totalAmount/grandTotal from admin's calculation (stored grand_total)
            // This is the preview receipt total that the admin computed and sent - DO NOT RECALCULATE
            const totalAmount = parseFloat(payment.totalAmount || payment.grandTotal || subtotal);
            document.getElementById('preview_receipt_total').textContent = '₱' + totalAmount.toFixed(2);
            
            // Set up approve button
            const approveBtn = document.getElementById('approveFromPreviewBtn');
            approveBtn.onclick = function() {
                approveReceipt(bookingId);
            };
            
            // Set up reject button
            const rejectBtn = document.getElementById('rejectFromPreviewBtn');
            rejectBtn.onclick = function() {
                rejectReceipt(bookingId);
            };
            
            // Show modal
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#previewReceiptModal').modal('show');
            } else {
                const modalEl = document.getElementById('previewReceiptModal');
                if (modalEl) new bootstrap.Modal(modalEl).show();
        }
    } else {
            alert('Failed to load preview receipt: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error loading preview receipt:', error);
        alert('Error loading preview receipt. Please try again.');
    });
}

// Approve Preview Receipt
function approveReceipt(bookingId) {
    // Use stored booking ID if available (from preview modal)
    const finalBookingId = bookingId || window.currentPreviewBookingId;
    
    if (!finalBookingId) {
        alert('Error: Booking ID not found');
        return;
    }
    
    // Confirm approval
    if (!confirm('Are you sure you want to approve this preview receipt? Once approved, your item will move to "Under Repair" status and repair work will begin.')) {
        return;
    }
    
    let acceptUrl = "<?php echo rtrim(BASE_URL, '/'); ?>/customer/approvePreviewReceipt";
    console.log("Sending approval request to:", acceptUrl);
    
    // Show loading state
    const approveBtn = event?.target?.closest('button') || document.getElementById('approveFromPreviewBtn');
    const originalText = approveBtn ? approveBtn.innerHTML : '';
    if (approveBtn) {
        approveBtn.disabled = true;
        approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
    }
    
    fetch(acceptUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: "booking_id=" + finalBookingId
    })
    .then(response => response.json())
    .then(data => {
        console.log("Server response:", data);
        if (data.success) {
            // Close preview modal if open
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#previewReceiptModal').modal('hide');
            }
            alert("Preview receipt approved successfully! Your item will now move to 'Under Repair' status.");
            location.reload();
        } else {
            alert("Error: " + (data.message || 'Failed to approve receipt'));
            if (approveBtn) {
                approveBtn.disabled = false;
                approveBtn.innerHTML = originalText;
            }
        }
    })
    .catch(error => {
        console.error("Request failed:", error);
        alert("Failed to approve receipt. Please try again.");
        if (approveBtn) {
            approveBtn.disabled = false;
            approveBtn.innerHTML = originalText;
        }
    });
}

// Reject Preview Receipt
function rejectReceipt(bookingId) {
    // Use stored booking ID if available (from preview modal)
    const finalBookingId = bookingId || window.currentPreviewBookingId;
    
    if (!finalBookingId) {
        alert('Error: Booking ID not found');
        return;
    }
    
    // Confirm rejection
    if (!confirm('Are you sure you want to reject this preview receipt? This will cancel your booking and you will need to create a new reservation if you want to proceed.')) {
        return;
    }
    
    // Double confirmation
    if (!confirm('⚠️ FINAL CONFIRMATION\n\nRejecting this receipt will permanently cancel your booking. This action cannot be undone.\n\nDo you want to proceed with cancellation?')) {
        return;
    }
    
    let rejectUrl = "<?php echo rtrim(BASE_URL, '/'); ?>/customer/rejectPreviewReceipt";
    console.log("Sending rejection request to:", rejectUrl);
    
    // Show loading state
    const rejectBtn = event?.target?.closest('button') || document.getElementById('rejectFromPreviewBtn');
    const originalText = rejectBtn ? rejectBtn.innerHTML : '';
    if (rejectBtn) {
        rejectBtn.disabled = true;
        rejectBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rejecting...';
    }
    
    fetch(rejectUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: "booking_id=" + finalBookingId
    })
    .then(response => response.json())
    .then(data => {
        console.log("Server response:", data);
        if (data.success) {
            // Close preview modal if open
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#previewReceiptModal').modal('hide');
            }
            alert("Preview receipt rejected. Your booking has been cancelled.");
            location.reload();
        } else {
            alert("Error: " + (data.message || 'Failed to reject receipt'));
            if (rejectBtn) {
                rejectBtn.disabled = false;
                rejectBtn.innerHTML = originalText;
            }
        }
    })
    .catch(error => {
        console.error("Request failed:", error);
        alert("Failed to reject receipt. Please try again.");
        if (rejectBtn) {
            rejectBtn.disabled = false;
            rejectBtn.innerHTML = originalText;
        }
    });
}

function confirmCancelReservation(reservationId) {
    reservationToCancel = reservationId;
    if (typeof jQuery !== 'undefined') {
        jQuery('#cancelReservationModal').modal('show');
    } else {
        const modalEl = document.getElementById('cancelReservationModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }
}

// Confirm Cancel Button
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('confirmCancelBtn');
    if (btn) {
        btn.addEventListener('click', function() {
            if (reservationToCancel) {
                window.location.href = '<?php echo BASE_URL; ?>customer/cancelBooking/' + reservationToCancel;
            }
        });
    }
});

// View Repair Receipt
function viewRepairReceipt(repairItemId) {
    fetch('<?php echo BASE_URL; ?>customer/getRepairReservationDetails/' + repairItemId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                populateReceiptModal(item);
                if (typeof jQuery !== 'undefined') {
                    jQuery('#repairReceiptModal').modal('show');
                } else {
                    const modalEl = document.getElementById('repairReceiptModal');
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                }
            } else {
                showNotification('error', data.message || 'Failed to load receipt details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred while loading the receipt.');
        });
}

// Populate Receipt Modal
function populateReceiptModal(item) {
    // Booking number removed - no longer displayed
    document.getElementById('receipt_item_name').textContent = item.item_name;
    document.getElementById('receipt_description').textContent = item.item_description;
    document.getElementById('receipt_urgency').textContent = item.urgency ? item.urgency.charAt(0).toUpperCase() + item.urgency.slice(1) : 'Normal';
    document.getElementById('receipt_status').textContent = item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : 'Pending';
    document.getElementById('receipt_estimated_cost').textContent = item.estimated_cost ? '₱' + parseFloat(item.estimated_cost).toFixed(2) : 'To be determined';
    document.getElementById('receipt_date').textContent = new Date(item.created_at).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    document.getElementById('receipt_customer_name').textContent = item.customer_name || 'N/A';
    document.getElementById('receipt_customer_email').textContent = item.email || 'N/A';
    document.getElementById('receipt_customer_phone').textContent = item.phone || 'N/A';
}

// View Reservation Details (Modal)
function viewReservationDetails(reservationId) {
    // Show loading state in modal
    const modalEl = document.getElementById('bookingDetailsModal');
    if (!modalEl) {
        console.error('Reservation details modal not found!');
        alert('Modal element not found. Please refresh the page.');
        return;
    }
    
    // Ensure modal is attached to <body> to avoid stacking/overflow issues
    try {
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
    } catch (_) {}

    // Show modal immediately with loading state (robust to missing libs)
    try {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal === 'function') {
            jQuery('#bookingDetailsModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            // Fallback: force visible
            document.body.classList.add('modal-open');
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.style.zIndex = '1055';
            // Add basic backdrop
            let backdrop = document.getElementById('temp-modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'temp-modal-backdrop';
                backdrop.style.cssText = 'position:fixed;inset:0;background:transparent;opacity:0;pointer-events:none;z-index:1050;';
                document.body.appendChild(backdrop);
            }
        }
    } catch (e) {
        console.error('Modal show error:', e);
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.style.zIndex = '1055';
    }
    
    // Set loading message
    const modalBody = modalEl.querySelector('.modal-body');
    if (modalBody) {
        const originalContent = modalBody.innerHTML;
        modalBody.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x mb-3" style="color: #1F4E79;"></i><p>Loading reservation details...</p></div>';
        
        // First, fetch booking details to check status
        fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + reservationId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const booking = data.data;
                    const status = (booking.status || '').toLowerCase();
                    const paymentStatus = (booking.payment_status || '').toLowerCase();
                    
                    // Check if booking is completed and paid - show official receipt
                    const isCompletedAndPaid = (
                        status === 'delivered_and_paid' || 
                        status === 'completed' ||
                        (status === 'completed' && (paymentStatus === 'paid' || paymentStatus === 'paid_full_cash' || paymentStatus === 'paid_on_delivery_cod'))
                    );
                    
                    if (isCompletedAndPaid) {
                        // Fetch official receipt data
                        fetch('<?php echo BASE_URL; ?>customer/getOfficialReceipt/' + reservationId)
                            .then(response => response.json())
                            .then(receiptData => {
                                if (receiptData.success && receiptData.receipt) {
                                    // Update modal title
                                    const modalTitle = modalEl.querySelector('#bookingDetailsModalLabel');
                                    if (modalTitle) {
                                        modalTitle.innerHTML = '<i class="fas fa-receipt mr-2"></i>Official Receipt';
                                    }
                                    // Populate with official receipt content
                                    populateOfficialReceiptModal(receiptData.receipt);
                                } else {
                                    // Fallback to booking details if receipt fails
                                    if (modalBody) modalBody.innerHTML = originalContent;
                                    populateBookingDetailsModal(booking);
                                }
                            })
                            .catch(error => {
                                console.error('Error loading official receipt:', error);
                                // Fallback to booking details
                                if (modalBody) modalBody.innerHTML = originalContent;
                                populateBookingDetailsModal(booking);
                            });
                    } else {
                        // Regular booking details for non-completed bookings
                        if (modalBody) modalBody.innerHTML = originalContent;
                        populateBookingDetailsModal(booking);
                    }
                } else {
                    if (modalBody) modalBody.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed to load reservation details.') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (modalBody) modalBody.innerHTML = '<div class="alert alert-danger">An error occurred while loading reservation details.</div>';
            });
    } else {
        // Fallback if modal body not found
        fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + reservationId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const booking = data.data;
                    const status = (booking.status || '').toLowerCase();
                    const paymentStatus = (booking.payment_status || '').toLowerCase();
                    
                    // Check if booking is completed and paid
                    const isCompletedAndPaid = (
                        status === 'delivered_and_paid' || 
                        status === 'completed' ||
                        (status === 'completed' && (paymentStatus === 'paid' || paymentStatus === 'paid_full_cash' || paymentStatus === 'paid_on_delivery_cod'))
                    );
                    
                    if (isCompletedAndPaid) {
                        // Fetch official receipt
                        fetch('<?php echo BASE_URL; ?>customer/getOfficialReceipt/' + reservationId)
                            .then(response => response.json())
                            .then(receiptData => {
                                if (receiptData.success && receiptData.receipt) {
                                    populateOfficialReceiptModal(receiptData.receipt);
                    if (typeof jQuery !== 'undefined') {
                        jQuery('#bookingDetailsModal').modal('show');
                    } else {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                                    }
                                } else {
                                    populateBookingDetailsModal(booking);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                populateBookingDetailsModal(booking);
                            });
                    } else {
                        populateBookingDetailsModal(booking);
                        if (typeof jQuery !== 'undefined') {
                            jQuery('#bookingDetailsModal').modal('show');
                        } else {
                            const modal = new bootstrap.Modal(modalEl);
                            modal.show();
                        }
                    }
                } else {
                    alert(data.message || 'Failed to load reservation details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading reservation details.');
            });
    }
}

// Populate Official Receipt Modal
function populateOfficialReceiptModal(receipt) {
    const modalBody = document.querySelector('#bookingDetailsModal .modal-body');
    if (!modalBody) return;
    
    // Update modal title
    const modalTitle = document.querySelector('#bookingDetailsModalLabel');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-receipt mr-2"></i>Official Receipt';
    }
    
    // Build official receipt HTML
    let receiptHtml = `
        <div class="text-center mb-4" style="border-bottom: 3px solid #2c3e50; padding-bottom: 20px;">
            <h2 style="color: #2c3e50; font-weight: 700; margin-bottom: 10px;">UpholCare</h2>
            <h4 style="color: #4e73df; font-weight: 600; margin-bottom: 10px;">Upholstery Services</h4>
            <p class="text-muted mb-2" style="font-size: 0.9rem;">Complete Address</p>
            <p class="text-muted mb-2" style="font-size: 0.9rem;">Contact Number: Contact Number</p>
            <p class="text-muted mb-2" style="font-size: 0.9rem;">Email: Email</p>
            <p class="text-muted mb-2" style="font-size: 0.9rem;">TIN Number: TIN Number</p>
            <p class="text-muted mb-2" style="font-size: 0.9rem;">BIR Permit Number: BIR Permit Number</p>
            <h3 style="color: #28a745; font-weight: 700; margin-top: 15px; text-transform: uppercase;">Official Receipt</h3>
            <p style="font-size: 1.1rem; font-weight: 600; color: #2c3e50; margin-top: 10px;">
                Official Receipt Number: <strong>${receipt.receiptNumber || 'N/A'}</strong>
            </p>
            <p style="font-size: 1rem; color: #6c757d; margin-top: 5px;">
                Date Issued: ${receipt.dateIssued || 'N/A'}
            </p>
        </div>
        
        <div class="section mb-4">
            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e3e6f0;">Customer Information</h6>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Customer Name:</strong></div>
                <div class="col-md-8">${receipt.customer?.name || 'N/A'}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Address:</strong></div>
                <div class="col-md-8">${receipt.customer?.address || 'N/A'}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Contact Number:</strong></div>
                <div class="col-md-8">${receipt.customer?.phone || 'N/A'}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Booking Number:</strong></div>
                <div class="col-md-8">${receipt.booking?.bookingNumber || 'N/A'}</div>
            </div>
        </div>
        
        <div class="section mb-4">
            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e3e6f0;">Item / Service Details</h6>
            <div class="table-responsive">
                <table class="table table-bordered" style="margin-bottom: 0;">
                    <thead style="background: #4e73df; color: white;">
                        <tr>
                            <th>Description of Service</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: right;">Unit Price</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>`;
    
    // Add service items
    if (receipt.services && receipt.services.length > 0) {
        receipt.services.forEach(item => {
            receiptHtml += `
                <tr>
                    <td>${item.description || 'N/A'}</td>
                    <td style="text-align: center;">${item.quantity || 1}</td>
                    <td style="text-align: right;">₱${parseFloat(item.unitPrice || 0).toFixed(2)}</td>
                    <td style="text-align: right;">₱${parseFloat(item.total || 0).toFixed(2)}</td>
                </tr>`;
        });
    }
    
    // Add materials
    if (receipt.item?.materials && receipt.item.materials.length > 0) {
        receipt.item.materials.forEach(material => {
            receiptHtml += `
                <tr>
                    <td>${material.name || 'N/A'}</td>
                    <td style="text-align: center;">${material.quantity || 0} ${material.unit || ''}</td>
                    <td style="text-align: right;">₱${parseFloat(material.price || 0).toFixed(2)}</td>
                    <td style="text-align: right;">₱${parseFloat(material.total || 0).toFixed(2)}</td>
                </tr>`;
        });
    }
    
    receiptHtml += `
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="section mb-4">
            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e3e6f0;">Summary of Charges</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="font-weight: 600; background: #f8f9fc;"><strong>Subtotal</strong></td>
                            <td style="text-align: right; font-weight: 600;">₱${parseFloat(receipt.payment?.subtotal || 0).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; background: #f8f9fc;">Pick-Up Fee (if applicable)</td>
                            <td style="text-align: right;">₱${parseFloat(receipt.payment?.pickupFee || 0).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; background: #f8f9fc;">Delivery Fee (if applicable)</td>
                            <td style="text-align: right;">₱${parseFloat(receipt.payment?.deliveryFee || 0).toFixed(2)}</td>
                        </tr>
                        <tr style="background: #28a745; color: white; font-size: 1.2rem;">
                            <td style="font-weight: 700; border-color: #28a745;"><strong>TOTAL AMOUNT DUE</strong></td>
                            <td style="text-align: right; font-weight: 700; border-color: #28a745;"><strong>₱${parseFloat(receipt.payment?.totalAmount || 0).toFixed(2)}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; background: #f8f9fc;"><strong>TOTAL AMOUNT PAID</strong></td>
                            <td style="text-align: right; font-weight: 600;"><strong>₱${parseFloat(receipt.payment?.totalPaid || receipt.payment?.totalAmount || 0).toFixed(2)}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; background: #f8f9fc;"><strong>BALANCE</strong></td>
                            <td style="text-align: right; font-weight: 600;">₱${parseFloat(receipt.payment?.balance || 0).toFixed(2)}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="section mb-4">
            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e3e6f0;">Payment Details</h6>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Mode of Payment:</strong></div>
                <div class="col-md-8">${receipt.payment?.mode || 'Cash'}</div>
            </div>
            ${receipt.payment?.referenceNumber ? `
            <div class="row mb-2">
                <div class="col-md-4"><strong>Reference Number:</strong></div>
                <div class="col-md-8">${receipt.payment.referenceNumber}</div>
            </div>
            ` : ''}
            <div class="row mb-2">
                <div class="col-md-4"><strong>Date/Time of Payment:</strong></div>
                <div class="col-md-8">${receipt.payment?.paymentDate || 'N/A'} – ${receipt.payment?.paymentTime || 'N/A'}</div>
            </div>
            ${receipt.payment?.deliveryDate ? `
            <div class="row mb-2">
                <div class="col-md-4"><strong>Delivery Date:</strong></div>
                <div class="col-md-8">${receipt.payment.deliveryDate}</div>
            </div>
            ` : ''}
        </div>
        
        <div class="section mb-4" style="margin-top: 40px;">
            <div class="row">
                <div class="col-md-6 text-center">
                    <div style="border-top: 2px solid #2c3e50; margin-top: 50px; padding-top: 5px;">
                        <strong>Customer Signature</strong>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div style="border-top: 2px solid #2c3e50; margin-top: 50px; padding-top: 5px;">
                        <strong>Authorized Signature</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mb-0" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e6f0; text-align: center; color: #6c757d; font-size: 0.9rem;">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Thank you for trusting UpholCare!</strong> Please keep this receipt for your records.
        </div>
    `;
    
    modalBody.innerHTML = receiptHtml;
}

function populateBookingDetailsModal(b) {
    function setText(id, value) {
        var el = document.getElementById(id);
        if (el) el.textContent = value;
        else console.warn('Element not found:', id);
    }
    
    function setHTML(id, value) {
        var el = document.getElementById(id);
        if (el) el.innerHTML = value;
        else console.warn('Element not found:', id);
    }
    
    // Format status with badge
    function formatStatus(status) {
        status = (status || 'pending').replace(/_/g, ' ');
        var statusClass = 'badge-success';
        if (status.toLowerCase() === 'pending') statusClass = 'badge-warning';
        else if (status.toLowerCase() === 'cancelled') statusClass = 'badge-danger';
        else if (status.toLowerCase().includes('progress')) statusClass = 'badge-primary';
        return '<span class="badge ' + statusClass + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    }
    
    // Format payment status with badge
    function formatPaymentStatus(status) {
        status = (status || 'unpaid').toLowerCase();
        var statusClass = 'badge-danger';
        var statusText = 'Unpaid';
        
        if (status === 'paid' || status === 'paid_full_cash') {
            statusClass = 'badge-success';
            statusText = 'Paid (Full Cash)';
        } else if (status === 'paid_on_delivery_cod') {
            statusClass = 'badge-success';
            statusText = 'Paid on Delivery (COD)';
        } else if (status === 'partial') {
            statusClass = 'badge-warning';
            statusText = 'Partial';
        } else if (status === 'cancelled') {
            statusClass = 'badge-secondary';
            statusText = 'Cancelled';
        }
        
        return '<span class="badge ' + statusClass + '">' + statusText + '</span>';
    }
    
    // Format dates
    function formatDate(dateString) {
        if (!dateString || dateString === '0000-00-00' || dateString === '0000-00-00 00:00:00') return '—';
        try {
            var date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch(e) {
            return dateString;
        }
    }
    
    // Reset modal title
    const modalTitle = document.querySelector('#bookingDetailsModalLabel');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-receipt mr-2"></i>Reservation Details';
    }
    
    // Populate all fields
    // Booking number removed - no longer displayed
    setText('bd_created_at', formatDate(b.created_at));
    setText('bd_service_name', b.service_name || 'N/A');
    setText('bd_category', b.category_name || 'General');
    setText('bd_service_type', b.service_type || '—');
    setText('bd_item_description', b.item_description || '—');
    setText('bd_pickup_date', b.pickup_date ? formatDate(b.pickup_date) : '—');
    setHTML('bd_status', formatStatus(b.status));
    setText('bd_total_amount', '₱' + (parseFloat(b.total_amount || 0).toFixed(2)));
    setHTML('bd_payment_status', formatPaymentStatus(b.payment_status));
    setText('bd_customer_name', b.customer_name || 'N/A');
    setText('bd_customer_email', b.email || 'N/A');
    setText('bd_customer_phone', b.phone || 'N/A');
}

// Print reservation receipt (only modal content)
function printBookingReceipt() {
    const modalContent = document.getElementById('bookingDetailsModal');
    if (!modalContent) return;
    
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const printContent = modalContent.innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reservation Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .modal-content { background: white; }
                .modal-header { background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); color: white; padding: 1.5rem; border-radius: 8px 8px 0 0; }
                .modal-body { padding: 2rem; }
                .modal-footer { display: none; }
                .close { display: none; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
                table th, table td { padding: 0.75rem; border: 1px solid #ddd; }
                table th { background-color: #f8f9fc; font-weight: 600; }
                hr { border-color: #e3e6f0; margin: 1.5rem 0; }
                .badge { padding: 0.25rem 0.5rem; border-radius: 4px; }
                .badge-success { background-color: #28a745; color: white; }
                .badge-info { background-color: #1F4E79; color: white; }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for content to load, then print
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 250);
}

</script>

<!-- Repair Receipt Modal -->
<div class="modal fade" id="repairReceiptModal" tabindex="-1" aria-labelledby="repairReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="repairReceiptModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Repair Reservation Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <!-- Receipt Header -->
                <div class="text-center mb-4">
                    <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 0.5rem;">UpholCare</h4>
                    <p class="text-muted mb-0">Repair & Restoration Services</p>
                </div>
                
                <hr style="border-color: #e3e6f0; margin: 1.5rem 0;">
                
                <!-- Receipt Details -->
                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <p class="mb-2"><strong>Date:</strong></p>
                        <p id="receipt_date">-</p>
                    </div>
                </div>
                
                <hr style="border-color: #e3e6f0; margin: 1.5rem 0;">
                
                <!-- Item Details -->
                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Item Information</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Item Name:</th>
                            <td id="receipt_item_name">-</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td id="receipt_description">-</td>
                        </tr>
                        <tr>
                            <th>Urgency:</th>
                            <td id="receipt_urgency">-</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge badge-success" id="receipt_status">-</span></td>
                        </tr>
                        <tr>
                            <th>Estimated Cost:</th>
                            <td id="receipt_estimated_cost">-</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Customer Information -->
                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem; margin-top: 1.5rem;">Customer Information</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td id="receipt_customer_name">-</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td id="receipt_customer_email">-</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td id="receipt_customer_phone">-</td>
                        </tr>
                    </table>
                </div>
                
                <hr style="border-color: #e3e6f0; margin: 1.5rem 0;">
                
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> Please keep this receipt for your records.
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 2rem 2rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-print mr-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="bookingDetailsModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Reservation Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center mb-4">
                    <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 0.25rem;">UpholCare</h4>
                    <p class="text-muted mb-0">Reservation Receipt</p>
                </div>

                <hr style="border-color: #e3e6f0; margin: 1.25rem 0;">

                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <p class="mb-2"><strong>Date:</strong></p>
                        <p id="bd_created_at">-</p>
                    </div>
                </div>

                <hr style="border-color: #e3e6f0; margin: 1.25rem 0;">

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Service Information</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Service:</th>
                            <td id="bd_service_name">-</td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td id="bd_category">-</td>
                        </tr>
                        <tr>
                            <th>Service Type:</th>
                            <td id="bd_service_type">-</td>
                        </tr>
                    </table>
                </div>

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Reservation Details</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Item Description:</th>
                            <td id="bd_item_description">-</td>
                        </tr>
                        <tr>
                            <th>Pickup Date:</th>
                            <td id="bd_pickup_date">-</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge badge-success" id="bd_status">-</span></td>
                        </tr>
                    </table>
                </div>

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Payment</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Total Amount:</strong></p>
                        <p id="bd_total_amount">₱0.00</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Payment Status:</strong></p>
                        <p><span class="badge badge-info" id="bd_payment_status">Unpaid</span></p>
                    </div>
                </div>

                <h6 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Customer</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td id="bd_customer_name">-</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td id="bd_customer_email">-</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td id="bd_customer_phone">-</td>
                        </tr>
                    </table>
                </div>

                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> Please keep this receipt for your records. Your reservation number is your reference.
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 2rem 2rem; background: #f8f9fc;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printBookingReceipt()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-print mr-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================
// Customer Booking Actions Based on Status
// ============================================

// 1️⃣ Pending Actions
// Already handled by confirmCancelReservation function above

// 2️⃣ Approved Actions
function viewEstimatedDate(bookingId) {
    viewReservationDetails(bookingId); // Show in details modal
}

function messageStore(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/messageStore/' + bookingId;
}

function uploadPhotos(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/uploadPhotos/' + bookingId;
}

// 3️⃣ In Queue Actions
function trackQueuePosition(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/trackQueuePosition/' + bookingId;
}

function trackProgress(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/trackProgress/' + bookingId;
}

// 4️⃣ Under Repair Actions
function viewProgressPhotos(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/viewProgressPhotos/' + bookingId;
}

// 5️⃣ For Quality Check Actions
function viewQualityStatus(bookingId) {
    viewReservationDetails(bookingId);
}

function preparePickup(bookingId) {
    viewReservationDetails(bookingId);
}

function arrangePickup(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/arrangePickup/' + bookingId;
}

// 6️⃣ Ready for Pickup Actions
function payNow(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/payment/' + bookingId;
}

function viewPickupInstructions(bookingId) {
    viewReservationDetails(bookingId);
}

function viewTotalAmount(bookingId) {
    viewReservationDetails(bookingId);
}

function generatePickupCode(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/generatePickupCode/' + bookingId;
}

// 7️⃣ Out for Delivery Actions
function trackDelivery(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/trackDelivery/' + bookingId;
}

function prepareCash(bookingId) {
    viewReservationDetails(bookingId);
}

function contactRider(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/contactRider/' + bookingId;
}



function downloadReceipt(bookingId) {
    window.open('<?php echo BASE_URL; ?>customer/downloadReceipt/' + bookingId, '_blank');
}

function rateService(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/rateService/' + bookingId;
}

function viewBeforeAfterPhotos(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/viewBeforeAfterPhotos/' + bookingId;
}

function bookAgain(bookingId) {
    if (confirm('Create a new reservation based on this booking?')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/bookAgain/' + bookingId;
    }
}

// 9️⃣ Cancelled Actions
function viewCancellationReason(bookingId) {
    viewReservationDetails(bookingId);
}

function requestRefund(bookingId) {
    if (confirm('Request a refund for this cancelled booking? The store will review your request.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/requestRefund/' + bookingId;
    }
}

// Repair Reservation Specific Actions
function confirmCancelRepair(repairId) {
    if (confirm('Are you sure you want to cancel this repair reservation? This action cannot be undone.')) {
        window.location.href = '<?php echo BASE_URL; ?>customer/cancelRepairReservation/' + repairId;
    }
}
// Auto-refresh booking statuses every 30 seconds to show admin updates
let statusRefreshInterval = null;

function refreshBookingStatuses() {
    // Get all booking rows
    const bookingRows = document.querySelectorAll('.booking-row[data-booking-id]');
    if (bookingRows.length === 0) return;
    
    // Get all booking IDs
    const bookingIds = Array.from(bookingRows).map(row => row.getAttribute('data-booking-id'));
    
    // Refresh each booking's status
    bookingIds.forEach(bookingId => {
        // Add cache-busting timestamp to ensure fresh data
        const timestamp = new Date().getTime();
        fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + bookingId + '?t=' + timestamp, {
            method: 'GET',
            cache: 'default',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    const booking = data.data;
                    // Preserve the actual status from server - only default to 'pending' if truly empty/null
                    let status = (booking.status || '').toString().trim();
                    
                    // Only default to 'pending' if status is truly empty/null/undefined
                    if (!status || status === 'null' || status === 'undefined' || status === '') {
                        status = 'pending';
                    }
                    
                    // Convert to lowercase for comparison, but preserve original for display
                    const statusLower = status.toLowerCase();
                    
                    // Find the row
                    const row = document.querySelector(`.booking-row[data-booking-id="${bookingId}"]`);
                    if (!row) return;
                    
                    // Find status cell using data attribute (most reliable)
                    let statusCell = row.querySelector('td.status-cell[data-booking-status]');
                    
                    // Fallback: find by badge-status class
                    if (!statusCell) {
                        const allCells = row.querySelectorAll('td');
                        for (let i = 0; i < allCells.length; i++) {
                            const cell = allCells[i];
                            const cellText = cell.textContent.trim();
                            
                            // Check if this cell contains a badge with badge-status class
                            const statusBadge = cell.querySelector('.badge-status');
                            if (statusBadge) {
                                statusCell = cell;
                                break;
                            }
                        }
                    }
                    
                    // Fallback: if badge-status not found, try finding by badge class and status keywords
                    if (!statusCell) {
                        const allCells = row.querySelectorAll('td');
                        for (let i = 0; i < allCells.length; i++) {
                            const cell = allCells[i];
                            const cellText = cell.textContent.trim();
                            
                            const badge = cell.querySelector('.badge');
                            if (badge) {
                                const badgeText = badge.textContent.toLowerCase();
                                // Check if badge contains status-related keywords
                                if (badgeText.includes('pending') || 
                                    badgeText.includes('approved') || 
                                    badgeText.includes('under repair') ||
                                    badgeText.includes('in queue') ||
                                    badgeText.includes('completed') ||
                                    badgeText.includes('cancelled') ||
                                    badgeText.includes('ready') ||
                                    badgeText.includes('delivery') ||
                                    badgeText.includes('quality')) {
                                    statusCell = cell;
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Final fallback: use nth-child(5) - the STATUS column (5th column in the table)
                    // Table structure: Flag(1), Service(2), Service Option(3), Date(4), STATUS(5), Actions(6)
                    if (!statusCell) {
                        const candidateCell = row.querySelector('td:nth-child(5)');
                        if (candidateCell) {
                            statusCell = candidateCell;
                        } else {
                            // Fallback: find status cell by searching all cells
                            const allCells = Array.from(row.querySelectorAll('td'));
                            for (let i = 0; i < allCells.length; i++) {
                                const cell = allCells[i];
                                const cellText = cell.textContent.trim();
                                // Check if this cell contains status badge
                                if (cell.querySelector('.badge-status')) {
                                    statusCell = cell;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if (!statusCell) {
                        console.error('Status cell not found for booking:', bookingId, 'Row:', row);
                        return;
                    }
                    
                    // Get current displayed status from the status cell
                    const currentStatusText = statusCell.textContent.trim().toLowerCase();
                    const currentStatusAttr = statusCell.getAttribute('data-booking-status') || '';
                    
                    // Normalize status for comparison (handle variations like 'approved', 'Approved', 'APPROVED')
                    const normalizedStatus = statusLower;
                    const normalizedCurrent = currentStatusAttr.toLowerCase();
                    
                    // Check if status actually changed
                    // Compare both the displayed text and the data attribute
                    const statusMatches = (
                        currentStatusText.includes(normalizedStatus) || 
                        normalizedCurrent === normalizedStatus
                    );
                    
                    // Only update if status actually changed
                    // CRITICAL LOGIC: Prevent approved status from being reset to pending
                    
                    // If UI already shows approved, NEVER override it with pending from server
                    // This protects against server returning stale/cached data
                    if ((currentStatusText.includes('approved') || normalizedCurrent === 'approved') && normalizedStatus === 'pending') {
                        // Don't override approved with pending (preserve approved status)
                        return;
                    }
                    
                    // If server says approved but UI shows pending, always update
                    if (normalizedStatus === 'approved' && (currentStatusText.includes('pending') || normalizedCurrent === 'pending')) {
                        // Force update from pending to approved - don't return
                    } else if (statusMatches && normalizedStatus !== 'pending') {
                        // Status matches and it's not pending, skip update to prevent loops
                        return;
                    } else if (normalizedStatus === 'pending' && currentStatusText.includes('pending') && normalizedCurrent === 'pending') {
                        // Both are pending, skip update
                        return;
                    }
                    
                    // Status mapping - use normalized status (lowercase) for lookup
                    const statusConfig = {
                        'pending': {class: 'badge-warning', icon: 'clock', text: 'Pending'},
                        'under_repair': {class: 'badge-primary', icon: 'tools', text: 'Under Repair'},
                        'for_quality_check': {class: 'badge-info', icon: 'search', text: 'For Quality Check'},
                        'ready_for_pickup': {class: 'badge-success', icon: 'box', text: 'Ready for Pickup'},
                        'out_for_delivery': {class: 'badge-warning', icon: 'truck', text: 'Out for Delivery'},
                        'completed': {class: 'badge-success', icon: 'check-double', text: 'Completed'},
                        'cancelled': {class: 'badge-secondary', icon: 'ban', text: 'Cancelled'},
                        'accepted': {class: 'badge-success', icon: 'check-circle', text: 'Approved'},
                        'confirmed': {class: 'badge-success', icon: 'check-circle', text: 'Approved'},
                        'ongoing': {class: 'badge-primary', icon: 'spinner', text: 'Under Repair'},
                        'admin_review': {class: 'badge-warning', icon: 'eye', text: 'Admin Review'},
                        'inspect_completed': {class: 'badge-success', icon: 'check-circle', text: 'Inspect Completed'},
                        'preview_receipt_sent': {class: 'badge-info', icon: 'envelope-open-text', text: 'Preview Receipt Sent'},
                        'to_inspect': {class: 'badge-warning', icon: 'clipboard-check', text: 'To Inspect'},
                        'for_inspection': {class: 'badge-info', icon: 'search', text: 'For Inspection'}
                    };
                    
                    // Get config or create default - use normalized status for lookup
                    let config = statusConfig[status] || statusConfig[statusLower];
                    if (!config) {
                        // Try to format unknown status
                        const formattedStatus = status.split(/[_-]/).map(word => 
                            word.charAt(0).toUpperCase() + word.slice(1)
                        ).join(' ');
                        config = {
                            class: 'badge-secondary',
                            icon: 'circle',
                            text: formattedStatus || 'Pending'
                        };
                    }
                    
                    // Ensure "Approved" status shows clearly (handle any case variation)
                    if (normalizedStatus === 'approved') {
                        config = {class: 'badge-success', icon: 'check-circle', text: 'Approved'};
                    }
                    
                    // Ensure text is never empty
                    if (!config.text || config.text.trim() === '') {
                        config.text = 'Pending';
                        config.class = 'badge-warning';
                        config.icon = 'clock';
                    }
                    
                    // Ensure status cell has the correct class and attributes for future detection
                    statusCell.classList.add('status-cell');
                    // Update status badge with visible text
                    statusCell.setAttribute('data-booking-status', normalizedStatus);
                    row.setAttribute('data-status', normalizedStatus); // Keep filter in sync
                    
                    statusCell.innerHTML = `
                        <span class="badge badge-status ${config.class}" style="font-size: 0.85rem !important; font-weight: 600 !important; padding: 0.5rem 0.875rem !important; display: inline-flex !important; align-items: center !important; white-space: nowrap !important;">
                            <i class="fas fa-${config.icon} mr-1"></i>
                            <strong>${config.text}</strong>
                        </span>
                    `;
                }
            })
            .catch(error => {
                // Only log actual errors, not cache-related warnings
                if (error.message && 
                    !error.message.includes('cache') && 
                    !error.message.includes('Content unavailable') &&
                    !error.message.includes('Failed to fetch')) {
                    console.error('Error refreshing booking status for booking ID ' + bookingId + ':', error);
                }
                // Silently handle network/cache-related errors - they're not critical
            });
    });
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Refresh immediately on page load to get latest status
    refreshBookingStatuses();
    
    // Refresh statuses every 8 seconds for faster updates (reduced from 10s)
    // Only set ONE interval to prevent duplicate calls
    if (!statusRefreshInterval) {
        statusRefreshInterval = setInterval(refreshBookingStatuses, 8000);
    }
    
    // Also refresh when page becomes visible (user switches back to tab)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshBookingStatuses();
        }
    });
    
    // Refresh when window gains focus
    window.addEventListener('focus', function() {
        refreshBookingStatuses();
    });
    
    // Listen for storage events (cross-tab communication)
    window.addEventListener('storage', function(e) {
        if (e.key === 'booking_status_updated') {
            const bookingId = e.newValue;
            if (bookingId) {
                // Refresh specific booking status
                if (typeof refreshSingleBookingStatus === 'function') {
                    refreshSingleBookingStatus(bookingId);
                } else {
                    refreshBookingStatuses();
                }
            }
        }
    });
});

// Clean up interval when page unloads
window.addEventListener('beforeunload', function() {
    if (statusRefreshInterval) {
        clearInterval(statusRefreshInterval);
    }
});

// Update Booking Modal Functions
function openUpdateBookingModal(bookingId) {
    if (!bookingId) {
        alert('Invalid booking ID');
        return;
    }
    
    // Fetch booking details
    fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const booking = data.data;
                populateUpdateModal(booking);
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery('#updateBookingModal').modal('show');
                } else {
                    const modalEl = document.getElementById('updateBookingModal');
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                }
            } else {
                alert('Error loading booking details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading booking details. Please try again.');
        });
}

function populateUpdateModal(booking) {
    if (!booking) return;

    // Helper to safely set value
    const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    };

    // Populate service option
    const serviceOption = booking.service_option || 'pickup';
    setVal('update_service_option', serviceOption);
    
    // Populate dates safely
    const formatDateForInput = (dateString) => {
        if (!dateString || dateString === '0000-00-00' || dateString === '0000-00-00 00:00:00') return '';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '';
            return date.toISOString().split('T')[0];
        } catch (e) {
            console.error('Date parsing error:', e);
            return '';
        }
    };

    setVal('update_pickup_date', formatDateForInput(booking.pickup_date));
    setVal('update_delivery_date', formatDateForInput(booking.delivery_date));
    
    // Populate addresses
    setVal('update_pickup_address', booking.pickup_address);
    setVal('update_delivery_address', booking.delivery_address);
    
    // Store booking ID
    setVal('update_booking_id', booking.id);
    
    // Show/hide address fields based on service option
    toggleAddressFields(serviceOption);
}

function toggleAddressFields(serviceOption) {
    const pickupGroup = document.getElementById('update_pickup_address_group');
    const deliveryGroup = document.getElementById('update_delivery_address_group');
    
    if (serviceOption === 'pickup' || serviceOption === 'both') {
        pickupGroup.style.display = 'block';
    } else {
        pickupGroup.style.display = 'none';
    }
    
    if (serviceOption === 'delivery' || serviceOption === 'both') {
        deliveryGroup.style.display = 'block';
    } else {
        deliveryGroup.style.display = 'none';
    }
}

// Listen for service option change
document.addEventListener('DOMContentLoaded', function() {
    const serviceOptionSelect = document.getElementById('update_service_option');
    if (serviceOptionSelect) {
        serviceOptionSelect.addEventListener('change', function() {
            toggleAddressFields(this.value);
        });
    }
});

function submitUpdateBooking() {
    const bookingId = document.getElementById('update_booking_id').value;
    const serviceOption = document.getElementById('update_service_option').value;
    const pickupDate = document.getElementById('update_pickup_date').value;
    const deliveryDate = document.getElementById('update_delivery_date').value;
    const pickupAddress = document.getElementById('update_pickup_address').value;
    const deliveryAddress = document.getElementById('update_delivery_address').value;
    
    if (!bookingId) {
        alert('Invalid booking ID');
        return;
    }
    
    // Validate required fields based on service option
    if ((serviceOption === 'pickup' || serviceOption === 'both') && !pickupAddress.trim()) {
        alert('Pickup address is required');
        return;
    }
    
    if ((serviceOption === 'delivery' || serviceOption === 'both') && !deliveryAddress.trim()) {
        alert('Delivery address is required');
        return;
    }
    
    // Show loading
    const submitBtn = document.getElementById('update_booking_submit_btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    // Prepare form data
    const formData = new URLSearchParams();
    formData.append('booking_id', bookingId);
    formData.append('service_option', serviceOption);
    if (pickupDate) formData.append('pickup_date', pickupDate);
    if (deliveryDate) formData.append('delivery_date', deliveryDate);
    if (pickupAddress) formData.append('pickup_address', pickupAddress);
    if (deliveryAddress) formData.append('delivery_address', deliveryAddress);
    
    // Submit update
    fetch('<?php echo BASE_URL; ?>customer/updateBooking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking updated successfully!');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#updateBookingModal').modal('hide');
            } else {
                const modalEl = document.getElementById('updateBookingModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            location.reload(); // Reload to show updated data
        } else {
            alert('Error: ' + (data.message || 'Failed to update booking'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the booking. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Filter Bookings by Status and Search
let currentStatusFilter = 'all';

function updateStatusFilter(status) {
    currentStatusFilter = status;
    
    // Synchronize select if called from elsewhere
    const select = document.getElementById('statusFilterSelect');
    if (select && select.value !== status) {
        select.value = status;
    }
    
    performBookingsFilter();
}

function performBookingsFilter() {
    const searchTerm = document.getElementById('bookingSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.booking-card-item');
    const servicesTable = document.querySelector('.services-table-wrapper');
    const repairsTable = document.querySelector('.repairs-table-wrapper');
    const repairsHeading = document.querySelector('.repairs-heading');
    
    let visibleServices = 0;
    let visibleRepairs = 0;
    
    cards.forEach(card => {
        const cardStatus = (card.getAttribute('data-status') || '').toLowerCase().trim();
        const serviceName = (card.querySelector('.font-weight-bold.text-dark') || {}).textContent || '';
        const bookingId = (card.querySelector('.text-muted.font-weight-bold') || {}).textContent || '';
        
        const normalizedFilter = (currentStatusFilter || 'all').toLowerCase().trim();
        const matchesStatus = (normalizedFilter === 'all' || cardStatus === normalizedFilter);
        const matchesSearch = !searchTerm || 
                             serviceName.toLowerCase().includes(searchTerm) || 
                             bookingId.toLowerCase().includes(searchTerm);
        
        if (matchesStatus && matchesSearch) {
            card.style.display = ''; 
            if (card.closest('#servicesContainer')) visibleServices++;
            else if (card.closest('#repairsContainer')) visibleRepairs++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Toggle visibility of tables/headings based on results
    if (servicesTable) servicesTable.style.display = visibleServices > 0 ? '' : 'none';
    if (repairsTable) {
        repairsTable.style.display = visibleRepairs > 0 ? '' : 'none';
        if (repairsHeading) repairsHeading.style.display = visibleRepairs > 0 ? '' : 'none';
    }

    // Handle overall empty state for filtered results
    let dynamicEmptyState = document.getElementById('filteredEmptyState');
    if (!dynamicEmptyState && (servicesTable || repairsTable)) {
        const container = document.querySelector('.dashboard-container');
        dynamicEmptyState = document.createElement('div');
        dynamicEmptyState.id = 'filteredEmptyState';
        dynamicEmptyState.className = 'empty-state-modern mt-4';
        dynamicEmptyState.innerHTML = `
            <div class="empty-state-icon"><i class="fas fa-search" style="color: #0F3C5F;"></i></div>
            <h3 class="empty-state-title">No matching reservations</h3>
            <p class="text-muted mb-4">We couldn't find any reservations matching your active filters. Try adjusting your search or status selection. 🔍</p>
            <button class="btn btn-primary px-4" onclick="resetFilters()" style="background: #0F3C5F; border: none; border-radius: 8px;">
                <i class="fas fa-undo mr-2"></i>Reset All Filters
            </button>
        `;
        container.appendChild(dynamicEmptyState);
    }
    
    if (dynamicEmptyState) {
        dynamicEmptyState.style.display = (visibleServices + visibleRepairs === 0) ? 'block' : 'none';
    }
}

function resetFilters() {
    document.getElementById('bookingSearchInput').value = '';
    updateStatusFilter('all');
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

