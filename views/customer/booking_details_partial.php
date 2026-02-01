<?php if (isset($booking) && $booking): ?>
<!-- Booking Details Card Partial -->
<div class="card booking-detail-card border-0 shadow-none">
    
    <!-- Booking Header -->
    <div class="detail-section" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%); color: white; border-radius: 0.5rem 0.5rem 0 0;">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <h3 class="mb-2" style="color: white; font-size: 1.5rem;">
                    <i class="fas fa-calendar-check mr-2"></i>Booking Details
                </h3>
                <p class="mb-0" style="opacity: 0.9; font-size: 0.9rem;">
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('F d, Y h:i A', strtotime($booking['created_at'])); ?>
                </p>
            </div>
            <div class="col-sm-4 text-sm-right mt-2 mt-sm-0">
                <?php
                $statusConfig = [
                    'pending' => ['class' => 'badge-warning', 'text' => 'Pending'],
                    'approved' => ['class' => 'badge-success', 'text' => 'Approved'],
                    'in_queue' => ['class' => 'badge-info', 'text' => 'In Queue'],
                    'under_repair' => ['class' => 'badge-primary', 'text' => 'Under Repair'],
                    'for_quality_check' => ['class' => 'badge-info', 'text' => 'For Quality Check'],
                    'ready_for_pickup' => ['class' => 'badge-success', 'text' => 'Ready for Pickup'],
                    'out_for_delivery' => ['class' => 'badge-warning', 'text' => 'Out for Delivery'],
                    'completed' => ['class' => 'badge-success', 'text' => 'Completed'],
                    'cancelled' => ['class' => 'badge-secondary', 'text' => 'Cancelled']
                ];
                $status = $booking['status'] ?? 'pending';
                $config = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'text' => ucwords(str_replace('_', ' ', $status))];
                ?>
                <span class="badge <?php echo $config['class']; ?>" id="modal_booking_status_badge" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                    <i class="fas fa-<?php 
                        $statusIcons = [
                            'pending' => 'clock',
                            'approved' => 'check-circle',
                            'in_queue' => 'list',
                            'under_repair' => 'tools',
                            'for_quality_check' => 'search',
                            'ready_for_pickup' => 'box',
                            'out_for_delivery' => 'truck',
                            'completed' => 'check-double',
                            'cancelled' => 'ban'
                        ];
                        echo $statusIcons[$status] ?? 'circle';
                    ?> mr-1"></i>
                    <?php echo $config['text']; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Booking Number Section -->
    <div class="detail-section text-center" style="background: #f8f9fc; border-left: 4px solid #1F4E79; padding: 1rem;">
        <div class="mb-1">
            <small class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fas fa-ticket-alt mr-1"></i>Booking Number
            </small>
        </div>
        <h3 class="mb-0" style="color: #1F4E79; font-weight: 700; font-family: monospace; font-size: 1.5rem;">
            <?php if (!empty($booking['booking_number'])): ?>
                <?php echo htmlspecialchars($booking['booking_number']); ?>
            <?php else: ?>
                <span class="text-muted" style="font-size: 1rem;">Pending Assignment</span>
            <?php endif; ?>
        </h3>
    </div>

    <!-- Service & Item Information -->
    <div class="detail-section">
        <h6 class="mb-3 font-weight-bold" style="color: #2c3e50;">
            <i class="fas fa-tools mr-2 text-primary"></i>Service & Item
        </h6>
        <div class="row">
            <div class="col-md-6 mb-2">
                <div class="detail-label" style="font-size: 0.8rem;">Service Name</div>
                <div class="detail-value" style="font-size: 0.95rem;"><?php echo htmlspecialchars($booking['service_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="detail-label" style="font-size: 0.8rem;">Category</div>
                <div class="detail-value"><span class="badge badge-secondary"><?php echo htmlspecialchars($booking['category_name'] ?? 'General'); ?></span></div>
            </div>
            <?php if (!empty($booking['item_description'])): ?>
            <div class="col-12 mb-2">
                <div class="detail-label" style="font-size: 0.8rem;">Item Description</div>
                <div class="detail-value" style="font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($booking['item_description'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment & Schedule -->
    <div class="detail-section">
        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3 font-weight-bold" style="color: #2c3e50;">
                    <i class="fas fa-money-bill-wave mr-2 text-success"></i>Payment
                </h6>
                <div class="detail-label" style="font-size: 0.8rem;">Total Amount</div>
                <div class="detail-value text-primary font-weight-bold" style="font-size: 1.2rem;">
                    ₱<?php echo number_format($booking['total_amount'] ?? 0, 2); ?>
                </div>
                <div class="mt-2">
                    <?php
                    $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                    $paymentConfig = [
                        'unpaid' => ['class' => 'badge-danger', 'text' => 'Unpaid'],
                        'paid' => ['class' => 'badge-success', 'text' => 'Paid'],
                        'partial' => ['class' => 'badge-warning', 'text' => 'Partial']
                    ];
                    $pInfo = $paymentConfig[$paymentStatus] ?? ['class' => 'badge-secondary', 'text' => ucfirst($paymentStatus)];
                    ?>
                    <span class="badge <?php echo $pInfo['class']; ?>"><?php echo $pInfo['text']; ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="mb-3 font-weight-bold" style="color: #2c3e50;">
                    <i class="fas fa-calendar-alt mr-2 text-info"></i>Schedule
                </h6>
                <?php if (!empty($booking['pickup_date'])): ?>
                <div class="detail-label" style="font-size: 0.8rem;">Pickup Date</div>
                <div class="detail-value" style="font-size: 0.95rem;"><?php echo date('F d, Y', strtotime($booking['pickup_date'])); ?></div>
                <?php endif; ?>
                <div class="detail-label mt-2" style="font-size: 0.8rem;">Service Option</div>
                <div class="detail-value" style="font-size: 0.95rem;"><?php echo ucwords(str_replace('_', ' ', $booking['service_option'] ?? 'N/A')); ?></div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="detail-section bg-light text-right">
        <?php
        $status = strtolower($booking['status'] ?? 'pending');
        $bookingId = $booking['id'] ?? 0;
        ?>
        
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
        
        <?php if ($status === 'completed'): ?>
            <a href="<?php echo BASE_URL; ?>customer/downloadReceipt/<?php echo $bookingId; ?>" class="btn btn-primary btn-sm" target="_blank">
                <i class="fas fa-download mr-1"></i> Receipt
            </a>
        <?php endif; ?>
        
        <?php if (in_array($status, ['pending', 'approved', 'in_queue'])): ?>
            <button type="button" class="btn btn-danger btn-sm" onclick="confirmCancelBooking(<?php echo $bookingId; ?>)">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
        <?php endif; ?>
        
        <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $bookingId; ?>" class="btn btn-info btn-sm">
            <i class="fas fa-external-link-alt mr-1"></i> Full Page
        </a>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning m-3">
    <i class="fas fa-exclamation-triangle mr-2"></i>Booking information not found.
</div>
<?php endif; ?>
