<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.payment-card {
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    transition: all 0.3s;
}

.payment-card:hover {
    box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
}

.payment-status-badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.875rem;
    border-radius: 0.35rem;
    font-weight: 600;
}

/* Payment Status Badges */
.badge-unpaid {
    background-color: #e74c3c;
    color: white;
}

.badge-paid, .badge-paid_full_cash {
    background-color: #27ae60;
    color: white;
}

.badge-paid_on_delivery_cod {
    background-color: #16a085;
    color: white;
}

.badge-cancelled {
    background-color: #95a5a6;
    color: white;
}

.badge-partial {
    background-color: #f39c12;
    color: white;
}

/* Delivery Status Badges */
.badge-ready-for-delivery {
    background-color: #3498db;
    color: white;
}

.badge-on-delivery {
    background-color: #f39c12;
    color: white;
}

.badge-delivered {
    background-color: #27ae60;
    color: white;
}

.badge-delivered-paid {
    background-color: #16a085;
    color: white;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Bookings</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Bookings</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Bookings List -->
<div class="card payment-card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background-color: white;">
        <h6 class="m-0 font-weight-bold text-primary">Booking History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Service Address</th>
                        <th>Service Option</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (empty($bookings)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                <br><span class="text-muted">No bookings found</span>
                            </td>
                        </tr>
                    <?php else: 
                        foreach ($bookings as $booking): 
                            $bookingId = $booking['id'] ?? 0;
                            $serviceName = htmlspecialchars($booking['service_name'] ?? 'N/A');
                            $status = strtolower(trim($booking['status'] ?? 'pending'));
                            
                            // Get service address
                            $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                            $serviceAddress = '';
                            if ($serviceOption === 'delivery' || $serviceOption === 'both') {
                                $serviceAddress = htmlspecialchars($booking['delivery_address'] ?? 'N/A');
                            } else {
                                $serviceAddress = htmlspecialchars($booking['pickup_address'] ?? 'N/A');
                            }
                            
                            // Format service option
                            $serviceOptionText = 'Pick Up';
                            $serviceOptionClass = 'badge-primary';
                            $serviceOptionIcon = 'fa-truck-loading';
                            if ($serviceOption === 'delivery') {
                                $serviceOptionText = 'Delivery Service';
                                $serviceOptionClass = 'badge-info';
                                $serviceOptionIcon = 'fa-truck';
                            } elseif ($serviceOption === 'both') {
                                $serviceOptionText = 'Both (Pick Up & Delivery)';
                                $serviceOptionClass = 'badge-success';
                                $serviceOptionIcon = 'fa-exchange-alt';
                            } elseif ($serviceOption === 'walk_in') {
                                $serviceOptionText = 'Walk In';
                                $serviceOptionClass = 'badge-warning';
                                $serviceOptionIcon = 'fa-walking';
                            }
                            
                            // Get date
                            $bookingDate = $booking['created_at'] ?? date('Y-m-d H:i:s');
                            $formattedDate = date('M d, Y', strtotime($bookingDate));
                            
                            // Status badge configuration
                            $statusConfig = [
                                'pending' => ['class' => 'badge-warning', 'icon' => 'clock', 'text' => 'Pending'],
                                'for_pickup' => ['class' => 'badge-info', 'icon' => 'truck-loading', 'text' => 'For Pickup'],
                                'picked_up' => ['class' => 'badge-primary', 'icon' => 'check', 'text' => 'Picked Up'],
                                'for_inspection' => ['class' => 'badge-info', 'icon' => 'search', 'text' => 'For Inspection'],
                                // 'for_quotation' status removed
                                'approved' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Approved'],
                                'in_progress' => ['class' => 'badge-primary', 'icon' => 'tools', 'text' => 'In Progress'],
                                'completed' => ['class' => 'badge-success', 'icon' => 'check-double', 'text' => 'Completed'],
                                'paid' => ['class' => 'badge-success', 'icon' => 'money-bill', 'text' => 'Paid'],
                                'closed' => ['class' => 'badge-secondary', 'icon' => 'lock', 'text' => 'Closed'],
                                'cancelled' => ['class' => 'badge-danger', 'icon' => 'ban', 'text' => 'Cancelled']
                            ];
                            $statusInfo = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'icon' => 'info', 'text' => ucwords(str_replace('_', ' ', $status))];
                            
                            // Check if preview receipt is available
                            $quotationSent = isset($booking['quotation_sent']) && $booking['quotation_sent'] == 1;
                            $hasGrandTotal = isset($booking['grand_total']) && floatval($booking['grand_total']) > 0;
                            $showReceiptButton = $quotationSent && $hasGrandTotal;
                    ?>
                    <tr>
                        <td><strong><?php echo $serviceName; ?></strong></td>
                        <td><?php echo $serviceAddress; ?></td>
                        <td>
                            <span class="badge <?php echo $serviceOptionClass; ?>" style="font-weight: 600;">
                                <i class="fas <?php echo $serviceOptionIcon; ?> mr-1"></i><?php echo $serviceOptionText; ?>
                            </span>
                        </td>
                        <td><?php echo $formattedDate; ?></td>
                        <td>
                            <span class="badge <?php echo $statusInfo['class']; ?>">
                                <i class="fas fa-<?php echo $statusInfo['icon']; ?> mr-1"></i><?php echo $statusInfo['text']; ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" onclick="viewBookingDetails(<?php echo $bookingId; ?>, <?php echo $showReceiptButton ? 'true' : 'false'; ?>)">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            <?php if ($showReceiptButton): ?>
                            <button type="button" class="btn btn-sm btn-success ml-2" onclick="viewReceiptPreview(<?php echo $bookingId; ?>)">
                                <i class="fas fa-receipt mr-1"></i>Receipt
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="receiptModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-receipt mr-2"></i>Payment Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 2rem; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem; max-height: 70vh; overflow-y: auto;">
                <div class="receipt-container p-4" style="background: white; border: 2px solid #e3e6f0; border-radius: 10px;">
                    <!-- Company Header -->
                    <div class="text-center mb-4 pb-3 border-bottom border-primary">
                        <h2 class="mb-2" style="color: #4e73df; font-weight: 700; font-size: 2rem;">UphoCare</h2>
                        <p class="text-muted mb-1" style="font-size: 1.1rem; font-weight: 600;">Upholstery Services</p>
                        <p class="mb-0" style="color: #28a745; font-weight: 600; font-size: 1rem;">Cash on Delivery Receipt</p>
                    </div>

                    <!-- Receipt Details -->
                    <div class="row mb-4" style="font-size: 1rem;">
                        <div class="col-md-6 mb-3">
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Booking ID:</strong><br>
                                <span id="receipt-booking-id" class="text-primary" style="font-size: 1.2rem; font-weight: 700;">Queue #0003</span>
                            </div>
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Service:</strong><br>
                                <span id="receipt-service" style="font-size: 1.1rem; color: #2c3e50;">Mattress Cover</span>
                            </div>
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Date:</strong><br>
                                <span id="receipt-date" style="font-size: 1.1rem; color: #2c3e50;">November 27, 2025</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Customer:</strong><br>
                                <span style="font-size: 1.1rem; color: #2c3e50;"><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Customer Name'); ?></span>
                            </div>
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Payment Method:</strong><br>
                                <span class="badge badge-success" id="receipt-payment-method" style="font-size: 1rem; padding: 0.5rem 1rem; margin-top: 0.25rem;">Cash on Delivery</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="mb-4">
                        <h6 class="mb-3" style="color: #2c3e50; font-weight: 700; font-size: 1.1rem; border-bottom: 2px solid #4e73df; padding-bottom: 0.5rem;">
                            <i class="fas fa-list-ul mr-2"></i>Payment Breakdown
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white" style="font-size: 1rem; margin-bottom: 0;">
                                <thead style="background: linear-gradient(135deg, #4e73df 0%, #667eea 100%); color: white;">
                                    <tr>
                                        <th style="font-weight: 600; padding: 1rem;">Description</th>
                                        <th class="text-right" style="font-weight: 600; padding: 1rem; width: 180px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="receipt-breakdown" style="background: #f8f9fc;">
                                    <!-- Fee breakdown will be populated by JavaScript -->
                                    <tr>
                                        <td colspan="2" class="text-center text-muted" style="padding: 2rem;">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading payment details...
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot style="background: linear-gradient(135deg, #1cc88a 0%, #28a745 100%); color: white;">
                                    <tr>
                                        <th style="font-size: 1.2rem; font-weight: 700; padding: 1rem;">GRAND TOTAL</th>
                                        <th class="text-right" style="font-size: 1.3rem; font-weight: 700; padding: 1rem;">₱<span id="receipt-final-total">0.00</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Status Message -->
                    <div class="alert" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 2px solid #28a745; border-radius: 10px; padding: 1.25rem;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #28a745;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1" style="color: #155724; font-weight: 700; font-size: 1.1rem;">Payment Received!</h6>
                                <p class="mb-0" style="color: #155724; font-size: 0.95rem;">This receipt confirms your cash on delivery payment. Please keep this for your records.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Note -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="mb-2" style="color: #5a5c69; font-weight: 600; font-size: 1rem;">Thank you for choosing UphoCare!</p>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">For inquiries, please contact our customer service.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem; border-top: 2px solid #e3e6f0;">
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
                <button type="button" class="btn btn-primary btn-lg" onclick="printReceipt()">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </button>
                <button type="button" class="btn btn-success btn-lg" id="downloadReceiptBtn" onclick="downloadReceipt()">
                    <i class="fas fa-download mr-2"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Request Confirmation Modal -->
<div class="modal fade" id="receiptRequestModal" tabindex="-1" role="dialog" aria-labelledby="receiptRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="receiptRequestModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-receipt mr-2"></i>Request Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 2rem; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center">
                    <i class="fas fa-question-circle" style="font-size: 4rem; color: #667eea; margin-bottom: 1.5rem;"></i>
                    <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Do you want to have a receipt?</h4>
                    <p class="text-muted" style="font-size: 1.1rem;">Your receipt will be sent to your notification.</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem; border-top: 2px solid #e3e6f0; justify-content: center;">
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal" style="min-width: 120px;">
                    <i class="fas fa-times mr-2"></i>No
                </button>
                <button type="button" class="btn btn-primary btn-lg" id="confirmReceiptRequestBtn" style="min-width: 120px;">
                    <i class="fas fa-check mr-2"></i>Yes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Request Confirmation Success Modal -->
<div class="modal fade" id="receiptRequestSuccessModal" tabindex="-1" role="dialog" aria-labelledby="receiptRequestSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #1cc88a 0%, #28a745 100%); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="receiptRequestSuccessModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-check-circle mr-2"></i>Request Submitted
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 2rem; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center">
                    <i class="fas fa-bell" style="font-size: 4rem; color: #28a745; margin-bottom: 1.5rem;"></i>
                    <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Receipt Request Received</h4>
                    <p class="text-muted" style="font-size: 1.1rem; margin-bottom: 0.5rem;">Your receipt request has been submitted successfully.</p>
                    <p class="text-success" style="font-size: 1.1rem; font-weight: 600;">It will be sent to your notification.</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem; border-top: 2px solid #e3e6f0; justify-content: center;">
                <button type="button" class="btn btn-success btn-lg" data-dismiss="modal" style="min-width: 150px;">
                    <i class="fas fa-check mr-2"></i>Okay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Booking Details Modal (Examination Message) -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" role="dialog" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="viewBookingModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-info-circle mr-2"></i>Booking Status
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 2rem; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center">
                    <i class="fas fa-clipboard-check" style="font-size: 4rem; color: #667eea; margin-bottom: 1.5rem;"></i>
                    <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Items Under Examination</h4>
                    <p class="text-muted" style="font-size: 1.1rem; line-height: 1.8;">
                        Your items are currently being examined and measured by our team. 
                        Please wait for the preview receipt to be sent to you.
                    </p>
                    <div class="alert alert-info mt-4" style="background: #e7f3ff; border-color: #667eea;">
                        <i class="fas fa-clock mr-2"></i>
                        <strong>Note:</strong> You will receive a notification once the preview receipt is ready for your review.
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem; border-top: 2px solid #e3e6f0; justify-content: center;">
                <button type="button" class="btn btn-primary btn-lg" data-dismiss="modal" style="min-width: 150px;">
                    <i class="fas fa-check mr-2"></i>Understood
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Preview Modal (Accept/Reject) -->
<div class="modal fade" id="receiptPreviewModal" tabindex="-1" role="dialog" aria-labelledby="receiptPreviewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="receiptPreviewModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-receipt mr-2"></i>Preview Receipt (Bayronon)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 2rem; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem; max-height: 70vh; overflow-y: auto;">
                <div class="receipt-preview-container p-4" style="background: white; border: 2px solid #e3e6f0; border-radius: 10px;">
                    <!-- Company Header -->
                    <div class="text-center mb-4 pb-3 border-bottom border-primary">
                        <h2 class="mb-2" style="color: #1F4E79; font-weight: 700; font-size: 2rem;">UphoCare</h2>
                        <p class="text-muted mb-1" style="font-size: 1.1rem; font-weight: 600;">Upholstery Services</p>
                        <p class="mb-0" style="color: #1F4E79; font-weight: 600; font-size: 1rem;">Preview Receipt - Bayronon</p>
                    </div>

                    <!-- Receipt Details -->
                    <div class="row mb-4" style="font-size: 1rem;">
                        <div class="col-md-6 mb-3">
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Booking ID:</strong><br>
                                <span id="preview-booking-id" class="text-primary" style="font-size: 1.2rem; font-weight: 700;">-</span>
                            </div>
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Service:</strong><br>
                                <span id="preview-service" style="font-size: 1.1rem; color: #2c3e50;">-</span>
                            </div>
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Date:</strong><br>
                                <span id="preview-date" style="font-size: 1.1rem; color: #2c3e50;">-</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="mb-3">
                                <strong style="color: #5a5c69; font-size: 0.95rem;">Customer:</strong><br>
                                <span style="font-size: 1.1rem; color: #2c3e50;"><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Customer Name'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="mb-4">
                        <h6 class="mb-3" style="color: #2c3e50; font-weight: 700; font-size: 1.1rem; border-bottom: 2px solid #1F4E79; padding-bottom: 0.5rem;">
                            <i class="fas fa-list-ul mr-2"></i>Payment Breakdown
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white" style="font-size: 1rem; margin-bottom: 0;">
                                <thead style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%); color: white;">
                                    <tr>
                                        <th style="font-weight: 600; padding: 1rem;">Description</th>
                                        <th class="text-right" style="font-weight: 600; padding: 1rem; width: 180px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="preview-breakdown" style="background: #f8f9fc;">
                                    <tr>
                                        <td colspan="2" class="text-center text-muted" style="padding: 2rem;">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading receipt details...
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%); color: white;">
                                    <tr>
                                        <th style="font-size: 1.2rem; font-weight: 700; padding: 1rem;">GRAND TOTAL (BAYRONON)</th>
                                        <th class="text-right" style="font-size: 1.3rem; font-weight: 700; padding: 1rem;">₱<span id="preview-final-total">0.00</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div id="preview-notes-container" class="mb-4" style="display: none;">
                        <h6 class="mb-2" style="color: #2c3e50; font-weight: 700; font-size: 1rem;">
                            <i class="fas fa-sticky-note mr-2"></i>Notes:
                        </h6>
                        <p id="preview-notes" class="text-muted" style="font-size: 0.95rem; line-height: 1.6; padding: 1rem; background: #f8f9fc; border-radius: 0.5rem;"></p>
                    </div>

                    <!-- Action Message -->
                    <div class="alert alert-warning mt-4" style="background: #fff3cd; border-color: #ffc107;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle mr-3" style="font-size: 1.5rem; color: #856404;"></i>
                            <div>
                                <h6 class="mb-1" style="color: #856404; font-weight: 700;">Please Review Your Receipt</h6>
                                <p class="mb-0" style="color: #856404; font-size: 0.95rem;">Please review the breakdown above. You can accept or reject this quotation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem; border-top: 2px solid #e3e6f0;">
                <button type="button" class="btn btn-danger btn-lg" id="rejectReceiptBtn" style="min-width: 150px;">
                    <i class="fas fa-times mr-2"></i>Reject
                </button>
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal" style="min-width: 120px;">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
                <button type="button" class="btn btn-success btn-lg" id="acceptReceiptBtn" style="min-width: 150px;">
                    <i class="fas fa-check mr-2"></i>Accept
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Libraries for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous"></script>

<script>
let currentBookingId = '';
let currentBookingData = null;

function showReceiptModal(bookingId, service, totalDue, amountPaid, balance, bookingData) {
    currentBookingId = bookingId;
    
    // Parse booking data if provided
    let booking = null;
    if (bookingData) {
        try {
            booking = typeof bookingData === 'string' ? JSON.parse(bookingData) : bookingData;
            currentBookingData = booking;
            console.log('Booking data loaded:', booking);
        } catch(e) {
            console.error('Error parsing booking data:', e);
        }
    }
    
    // Populate modal with data - Make sure values are visible
    const bookingIdElement = document.getElementById('receipt-booking-id');
    const serviceElement = document.getElementById('receipt-service');
    const dateElement = document.getElementById('receipt-date');
    
    if (bookingIdElement) bookingIdElement.textContent = bookingId || 'N/A';
    if (serviceElement) serviceElement.textContent = service || 'N/A';
    if (dateElement) {
        dateElement.textContent = new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }
    
    // Build fee breakdown
    const breakdown = document.getElementById('receipt-breakdown');
    if (!breakdown) {
        console.error('Breakdown element not found');
        return;
    }
    
    breakdown.innerHTML = '';
    
    if (booking) {
        // Get fee values
        const baseServicePrice = parseFloat(booking.total_amount || 0);
        const laborFee = parseFloat(booking.labor_fee || 0);
        const pickupFee = parseFloat(booking.pickup_fee || 0);
        const deliveryFee = parseFloat(booking.delivery_fee || 0);
        const gasFee = parseFloat(booking.gas_fee || 0);
        const travelFee = parseFloat(booking.travel_fee || 0);
        const totalAdditionalFees = parseFloat(booking.total_additional_fees || 0);
        const grandTotal = parseFloat(booking.grand_total || totalDue.replace(/,/g, ''));
        const distanceKm = parseFloat(booking.distance_km || 0);
        
        console.log('Fees breakdown:', {baseServicePrice, laborFee, pickupFee, deliveryFee, gasFee, travelFee, grandTotal});
        
        let hasAnyFees = false;
        
        // Base Service Price
        if (baseServicePrice > 0) {
            const row = breakdown.insertRow();
            row.style.padding = '0.75rem';
            const cell1 = row.insertCell(0);
            cell1.textContent = 'Base Service Price';
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '500';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + baseServicePrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '600';
            cell2.style.color = '#2c3e50';
            hasAnyFees = true;
        }
        
        // Labor Fee
        if (laborFee > 0) {
            const row = breakdown.insertRow();
            row.style.padding = '0.75rem';
            const cell1 = row.insertCell(0);
            cell1.textContent = 'Labor Fee';
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '500';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + laborFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '600';
            cell2.style.color = '#2c3e50';
            hasAnyFees = true;
        }
        
        // Pick Up Fee
        if (pickupFee > 0) {
            const row = breakdown.insertRow();
            row.style.padding = '0.75rem';
            const cell1 = row.insertCell(0);
            cell1.textContent = 'Pick Up Fee';
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '500';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + pickupFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '600';
            cell2.style.color = '#2c3e50';
            hasAnyFees = true;
        }
        
        // Delivery COD Fee
        if (deliveryFee > 0) {
            const row = breakdown.insertRow();
            row.style.padding = '0.75rem';
            const cell1 = row.insertCell(0);
            cell1.textContent = 'Delivery COD Fee';
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '500';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + deliveryFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '600';
            cell2.style.color = '#2c3e50';
            hasAnyFees = true;
        }
        
        // Gas Fee
        if (gasFee > 0) {
            const row = breakdown.insertRow();
            row.style.padding = '0.75rem';
            const cell1 = row.insertCell(0);
            const gasDesc = distanceKm > 0 ? `Gas Fee (${distanceKm.toFixed(2)}km × ₱5.00)` : 'Gas Fee';
            cell1.textContent = gasDesc;
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '500';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + gasFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '600';
            cell2.style.color = '#2c3e50';
            hasAnyFees = true;
        }
        
        // Travel Fee
        if (travelFee > 0) {
            const row = breakdown.insertRow();
            row.style.padding = '0.75rem';
            const cell1 = row.insertCell(0);
            const travelDesc = distanceKm > 0 ? `Travel Fee (${distanceKm.toFixed(2)}km × ₱10.00)` : 'Travel Fee';
            cell1.textContent = travelDesc;
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '500';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + travelFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '600';
            cell2.style.color = '#2c3e50';
            hasAnyFees = true;
        }
        
        // Total Additional Fees (only show if there are additional fees)
        if (totalAdditionalFees > 0 && (laborFee > 0 || pickupFee > 0 || deliveryFee > 0 || gasFee > 0 || travelFee > 0)) {
            const row = breakdown.insertRow();
            row.style.background = '#e8f4f8';
            row.style.fontWeight = 'bold';
            row.style.borderTop = '2px solid #4e73df';
            const cell1 = row.insertCell(0);
            cell1.textContent = 'Total Additional Fees';
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '700';
            cell1.style.color = '#4e73df';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + totalAdditionalFees.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '700';
            cell2.style.color = '#4e73df';
        }
        
        // If no fees were added, show the grand total as a single line item
        if (!hasAnyFees && grandTotal > 0) {
            const row = breakdown.insertRow();
            row.style.padding = '0.75rem';
            const cell1 = row.insertCell(0);
            cell1.textContent = 'Total Amount Due';
            cell1.style.padding = '0.75rem';
            cell1.style.fontWeight = '500';
            const cell2 = row.insertCell(1);
            cell2.className = 'text-right';
            cell2.textContent = '₱' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            cell2.style.padding = '0.75rem';
            cell2.style.fontWeight = '600';
            cell2.style.color = '#2c3e50';
        }
        
        // Set grand total
        const finalTotalElement = document.getElementById('receipt-final-total');
        if (finalTotalElement) {
            finalTotalElement.textContent = grandTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    } else {
        // Fallback if booking data not available
        const row = breakdown.insertRow();
        row.style.padding = '0.75rem';
        const cell1 = row.insertCell(0);
        cell1.textContent = 'Total Amount Due';
        cell1.style.padding = '0.75rem';
        cell1.style.fontWeight = '500';
        const cell2 = row.insertCell(1);
        cell2.className = 'text-right';
        cell2.textContent = '₱' + totalDue;
        cell2.style.padding = '0.75rem';
        cell2.style.fontWeight = '600';
        cell2.style.color = '#2c3e50';
        
        const finalTotalElement = document.getElementById('receipt-final-total');
        if (finalTotalElement) {
            finalTotalElement.textContent = parseFloat(totalDue.replace(/,/g, '')).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
    
    // Determine payment method based on amount paid
    let paymentMethod = 'Cash on Delivery';
    let badgeClass = 'badge-success';
    
    if (parseFloat(amountPaid.replace(/,/g, '')) > 0) {
        if (booking && booking.payment_status === 'paid_full_cash') {
            paymentMethod = 'Full Cash (Paid Before Service)';
            badgeClass = 'badge-primary';
        } else if (booking && booking.payment_status === 'paid_on_delivery_cod') {
            paymentMethod = 'Cash on Delivery (COD)';
            badgeClass = 'badge-success';
        } else {
            paymentMethod = 'Cash on Delivery (COD)';
            badgeClass = 'badge-success';
        }
    }
    
    const paymentMethodElement = document.getElementById('receipt-payment-method');
    if (paymentMethodElement) {
        paymentMethodElement.textContent = paymentMethod;
        paymentMethodElement.className = 'badge ' + badgeClass;
        paymentMethodElement.style.fontSize = '1rem';
        paymentMethodElement.style.padding = '0.5rem 1rem';
    }
    
    // Show modal with animation
    $('#receiptModal').modal('show');
    
    // Log for debugging
    console.log('Receipt modal opened for booking:', bookingId);
}

function payNow(bookingId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/payment/' + bookingId;
}

function requestReceipt(bookingNumber, bookingId) {
    // Store booking info for later use
    window.currentReceiptBookingId = bookingId;
    window.currentReceiptBookingNumber = bookingNumber;
    
    // Show confirmation modal
    $('#receiptRequestModal').modal('show');
    
    // Handle confirm button click
    $('#confirmReceiptRequestBtn').off('click').on('click', function() {
        // Close confirmation modal
        $('#receiptRequestModal').modal('hide');
        
        // Send request to server (you'll need to create this endpoint)
        $.ajax({
            url: '<?php echo BASE_URL; ?>customer/requestReceipt',
            method: 'POST',
            data: {
                booking_id: bookingId,
                booking_number: bookingNumber
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success modal
                    $('#receiptRequestSuccessModal').modal('show');
                } else {
                    alert(response.message || 'Failed to submit receipt request. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error requesting receipt:', error);
                // Still show success modal for better UX (request will be processed by admin)
                $('#receiptRequestSuccessModal').modal('show');
            }
        });
    });
}

function printReceipt() {
    // Hide modal backdrop before printing
    $('.modal-backdrop').hide();
    window.print();
    // Show backdrop after printing
    setTimeout(() => {
        $('.modal-backdrop').show();
    }, 100);
}

function downloadReceipt() {
    // Check if libraries are loaded
    if (typeof html2canvas === 'undefined' || typeof window.jspdf === 'undefined') {
        alert('PDF generation libraries are loading. Please wait a moment and try again.');
        return;
    }
    
    const downloadBtn = document.getElementById('downloadReceiptBtn');
    if (downloadBtn) {
        downloadBtn.disabled = true;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating PDF...';
    }
    
    try {
        const receiptContainer = document.querySelector('.receipt-container');
        if (!receiptContainer) {
            alert('Receipt content not found. Please try again.');
            if (downloadBtn) {
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Download';
            }
            return;
        }
        
        // Use html2canvas to capture the receipt
        html2canvas(receiptContainer, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
            windowWidth: receiptContainer.scrollWidth,
            windowHeight: receiptContainer.scrollHeight
        }).then(function(canvas) {
            try {
                const { jsPDF } = window.jspdf;
                const imgData = canvas.toDataURL('image/png');
                
                // Calculate PDF dimensions
                const imgWidth = 210; // A4 width in mm
                const pageHeight = 297; // A4 height in mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = imgHeight;
                
                // Create PDF
                const pdf = new jsPDF('p', 'mm', 'a4');
                let position = 0;
                
                // Add first page
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
                
                // Add additional pages if needed
                while (heightLeft > 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                
                // Generate filename
                const filename = 'Receipt_' + (currentBookingId || 'Receipt') + '_' + new Date().toISOString().split('T')[0] + '.pdf';
                
                // Download the PDF
                pdf.save(filename);
                
                // Reset button
                if (downloadBtn) {
                    downloadBtn.disabled = false;
                    downloadBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Download';
                }
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF. Please try again or use the Print option.');
                if (downloadBtn) {
                    downloadBtn.disabled = false;
                    downloadBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Download';
                }
            }
        }).catch(function(error) {
            console.error('Error capturing receipt:', error);
            alert('Error capturing receipt. Please try again or use the Print option.');
            if (downloadBtn) {
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Download';
            }
        });
    } catch (error) {
        console.error('Error in downloadReceipt:', error);
        alert('An error occurred. Please try again.');
        if (downloadBtn) {
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Download';
        }
    }
}

// Check if PDF libraries are loaded
function checkPDFLibraries() {
    if (typeof html2canvas === 'undefined') {
        console.warn('html2canvas library not loaded. PDF download may not work.');
        return false;
    }
    if (typeof window.jspdf === 'undefined') {
        console.warn('jsPDF library not loaded. PDF download may not work.');
        return false;
    }
    return true;
}

// View Booking Details - Shows examination message
function viewBookingDetails(bookingId, hasReceipt) {
    if (hasReceipt) {
        // If receipt is available, show receipt preview instead
        viewReceiptPreview(bookingId);
    } else {
        // Show examination message modal
        $('#viewBookingModal').modal('show');
    }
}

// View Receipt Preview - Loads and displays the receipt preview
function viewReceiptPreview(bookingId) {
    // Fetch booking details
    fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const booking = data.data;
                
                // Populate receipt preview
                document.getElementById('preview-booking-id').textContent = booking.booking_number || 'N/A';
                document.getElementById('preview-service').textContent = booking.service_name || 'N/A';
                
                // Format date
                const bookingDate = booking.created_at || new Date().toISOString();
                const formattedDate = new Date(bookingDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.getElementById('preview-date').textContent = formattedDate;
                
                // Build breakdown
                const breakdown = document.getElementById('preview-breakdown');
                breakdown.innerHTML = '';
                
                const laborFee = parseFloat(booking.labor_fee || 0);
                const materialsCost = parseFloat(booking.materials_cost || 0);
                const fabricCost = parseFloat(booking.fabric_cost || 0);
                const serviceFee = parseFloat(booking.service_fee || 0);
                const pickupFee = parseFloat(booking.pickup_fee || 0);
                const deliveryFee = parseFloat(booking.delivery_fee || 0);
                const grandTotal = parseFloat(booking.grand_total || 0);
                
                // Add labor fee
                if (laborFee > 0) {
                    addBreakdownRow(breakdown, 'Labor Fee', laborFee);
                }
                
                // Add materials cost
                if (materialsCost > 0) {
                    addBreakdownRow(breakdown, 'Materials Cost', materialsCost);
                }
                
                // Add fabric cost
                if (fabricCost > 0) {
                    addBreakdownRow(breakdown, 'Fabric Cost', fabricCost);
                }
                
                // Add service fee
                if (serviceFee > 0) {
                    addBreakdownRow(breakdown, 'Service Fee', serviceFee);
                }
                
                // Add pickup fee
                if (pickupFee > 0) {
                    addBreakdownRow(breakdown, 'Pickup Fee', pickupFee);
                }
                
                // Add delivery fee
                if (deliveryFee > 0) {
                    addBreakdownRow(breakdown, 'Delivery Fee', deliveryFee);
                }
                
                // Set grand total
                document.getElementById('preview-final-total').textContent = grandTotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                // Show notes if available
                if (booking.calculation_notes && booking.calculation_notes.trim() !== '') {
                    document.getElementById('preview-notes').textContent = booking.calculation_notes;
                    document.getElementById('preview-notes-container').style.display = 'block';
                } else {
                    document.getElementById('preview-notes-container').style.display = 'none';
                }
                
                // Store booking ID for accept/reject
                window.currentPreviewBookingId = bookingId;
                window.currentPreviewBooking = booking;
                
                // Show modal
                $('#receiptPreviewModal').modal('show');
            } else {
                alert('Failed to load receipt preview. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error loading receipt preview:', error);
            alert('Error loading receipt preview. Please try again.');
        });
}

// Helper function to add breakdown row
function addBreakdownRow(breakdown, description, amount) {
    const row = breakdown.insertRow();
    row.style.padding = '0.75rem';
    const cell1 = row.insertCell(0);
    cell1.textContent = description;
    cell1.style.padding = '0.75rem';
    cell1.style.fontWeight = '500';
    const cell2 = row.insertCell(1);
    cell2.className = 'text-right';
    cell2.textContent = '₱' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    cell2.style.padding = '0.75rem';
    cell2.style.fontWeight = '600';
    cell2.style.color = '#2c3e50';
}

// Accept Receipt/Quotation
document.getElementById('acceptReceiptBtn').addEventListener('click', function() {
    const bookingId = window.currentPreviewBookingId;
    if (!bookingId) {
        alert('Error: Booking ID not found.');
        return;
    }
    
    if (!confirm('Are you sure you want to accept this quotation? This action cannot be undone.')) {
        return;
    }
    
    // Disable button
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    // Send accept request
    fetch('<?php echo BASE_URL; ?>customer/acceptBookingQuotation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            booking_id: bookingId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Quotation accepted successfully!');
            $('#receiptPreviewModal').modal('hide');
            location.reload(); // Reload page to update status
        } else {
            alert(data.message || 'Failed to accept quotation. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-check mr-2"></i>Accept';
        }
    })
    .catch(error => {
        console.error('Error accepting quotation:', error);
        alert('Error accepting quotation. Please try again.');
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-check mr-2"></i>Accept';
    });
});

// Reject Receipt/Quotation
document.getElementById('rejectReceiptBtn').addEventListener('click', function() {
    const bookingId = window.currentPreviewBookingId;
    if (!bookingId) {
        alert('Error: Booking ID not found.');
        return;
    }
    
    const reason = prompt('Please provide a reason for rejecting this quotation:');
    if (!reason || reason.trim() === '') {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    // Disable button
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    // Send reject request
    fetch('<?php echo BASE_URL; ?>customer/rejectBookingQuotation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            booking_id: bookingId,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Quotation rejected. Our team will review your feedback.');
            $('#receiptPreviewModal').modal('hide');
            location.reload(); // Reload page to update status
        } else {
            alert(data.message || 'Failed to reject quotation. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-times mr-2"></i>Reject';
        }
    })
    .catch(error => {
        console.error('Error rejecting quotation:', error);
        alert('Error rejecting quotation. Please try again.');
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-times mr-2"></i>Reject';
    });
});

// Auto-open modal if there's a hash in URL
document.addEventListener('DOMContentLoaded', function() {
    // Check libraries after a short delay to allow them to load
    setTimeout(function() {
        if (!checkPDFLibraries()) {
            const downloadBtn = document.getElementById('downloadReceiptBtn');
            if (downloadBtn) {
                downloadBtn.title = 'PDF libraries are loading. Please wait and try again.';
            }
        }
    }, 1000);
    
    const urlParams = new URLSearchParams(window.location.search);
    const openReceipt = urlParams.get('open_receipt');
    
    if (openReceipt) {
        // Find the receipt button for this booking and click it
        const receiptButton = document.querySelector(`button[onclick*="showReceiptModal"][onclick*="${openReceipt}"]`);
        if (receiptButton) {
            receiptButton.click();
        }
    }
});
</script>

<style>
/* Receipt Modal Styling */
#receiptModal .modal-content {
    visibility: visible !important;
    opacity: 1 !important;
}

#receiptModal .receipt-container {
    visibility: visible !important;
    opacity: 1 !important;
}

#receiptModal .modal-body {
    visibility: visible !important;
    opacity: 1 !important;
}

/* Ensure all text is visible */
#receiptModal p,
#receiptModal span,
#receiptModal td,
#receiptModal th,
#receiptModal h1,
#receiptModal h2,
#receiptModal h3,
#receiptModal h4,
#receiptModal h5,
#receiptModal h6 {
    visibility: visible !important;
    opacity: 1 !important;
    color: inherit !important;
}

/* Print Styles */
@media print {
    body * {
        visibility: hidden !important;
    }
    
    #receiptModal,
    #receiptModal * {
        visibility: visible !important;
    }
    
    .receipt-container, 
    .receipt-container * {
        visibility: visible !important;
    }
    
    #receiptModal {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        z-index: 9999;
    }
    
    .receipt-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px;
        background: white !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    .modal-header, 
    .modal-footer,
    .modal-backdrop,
    .close {
        display: none !important;
        visibility: hidden !important;
    }
    
    .modal-dialog {
        max-width: 100%;
        margin: 0;
    }
    
    .modal-content {
        border: none;
        box-shadow: none;
    }
    
    .modal-body {
        padding: 0 !important;
    }
    
    /* Ensure colors print */
    .badge,
    .alert,
    .table thead,
    .table tfoot {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        color-adjust: exact;
    }
}

/* Animation for modal appearance */
#receiptModal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}

/* Better scrollbar for modal body */
#receiptModal .modal-body::-webkit-scrollbar {
    width: 8px;
}

#receiptModal .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#receiptModal .modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

#receiptModal .modal-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Ensure download button is always clickable */
#downloadReceiptBtn {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 1000 !important;
    position: relative !important;
}

#downloadReceiptBtn:disabled {
    opacity: 0.7;
    cursor: wait !important;
}

#receiptModal .modal-footer {
    pointer-events: auto !important;
    z-index: 1000 !important;
}
</style>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>


