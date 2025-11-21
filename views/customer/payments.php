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

.badge-paid {
    background-color: #27ae60;
    color: white;
}

.badge-refunded {
    background-color: #3498db;
    color: white;
}

.badge-failed {
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
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Payments</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Payments</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Cash on Delivery Notice -->
<div class="alert alert-info mb-4">
    <div class="row align-items-center">
        <div class="col-md-1">
            <i class="fas fa-info-circle fa-2x text-info"></i>
        </div>
        <div class="col-md-11">
            <h5 class="alert-heading mb-2">
                <i class="fas fa-money-bill-wave mr-2"></i>Cash on Delivery Only
            </h5>
            <p class="mb-2">All payments are processed through <strong>Cash on Delivery (COD)</strong> method only.</p>
            <ul class="mb-0 small">
                <li>Payment is collected when your service is completed and delivered</li>
                <li>No upfront payment required - pay only when satisfied with the service</li>
                <li>Our team will contact you to arrange delivery and payment collection</li>
                <li>Receipt will be provided upon payment completion</li>
            </ul>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Unpaid</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱3,500.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Delivered & Paid</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱11,000.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-double fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Amount</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱14,500.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payments List -->
<div class="card payment-card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background-color: white;">
        <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Service</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example: Unpaid -->
                    <tr>
                        <td><strong class="text-primary">BKG-20250124-0001</strong></td>
                        <td>Car Seat Cover Repair</td>
                        <td>₱3,500.00</td>
                        <td>₱0.00</td>
                        <td class="text-danger font-weight-bold">₱3,500.00</td>
                        <td><span class="badge badge-danger payment-status-badge"><i class="fas fa-times-circle mr-1"></i>Unpaid</span></td>
                        <td>
                            <span class="text-muted small"><i class="fas fa-clock mr-1"></i>Payment Pending</span>
                        </td>
                    </tr>
                    <!-- Example: Ready for Delivery -->
                    <tr>
                        <td><strong class="text-primary">BKG-20250115-0004</strong></td>
                        <td>Curtain Repair</td>
                        <td>₱2,200.00</td>
                        <td>₱2,200.00</td>
                        <td class="text-success font-weight-bold">₱0.00</td>
                        <td><span class="badge badge-info payment-status-badge"><i class="fas fa-box mr-1"></i>Ready for Delivery</span></td>
                        <td>
                            <span class="text-muted small"><i class="fas fa-shipping-fast mr-1"></i>In Transit</span>
                        </td>
                    </tr>
                    <!-- Example: On Delivery -->
                    <tr>
                        <td><strong class="text-primary">BKG-20250112-0005</strong></td>
                        <td>Pillow Restoration</td>
                        <td>₱1,500.00</td>
                        <td>₱1,500.00</td>
                        <td class="text-success font-weight-bold">₱0.00</td>
                        <td><span class="badge badge-warning payment-status-badge"><i class="fas fa-truck mr-1"></i>On Delivery</span></td>
                        <td>
                            <span class="text-muted small"><i class="fas fa-shipping-fast mr-1"></i>In Transit</span>
                        </td>
                    </tr>
                    <!-- Example: Delivered (can view receipt) -->
                    <tr>
                        <td><strong class="text-primary">BKG-20250110-0006</strong></td>
                        <td>Mattress Cover Repair</td>
                        <td>₱2,500.00</td>
                        <td>₱2,500.00</td>
                        <td class="text-success font-weight-bold">₱0.00</td>
                        <td><span class="badge badge-success payment-status-badge"><i class="fas fa-check-circle mr-1"></i>Delivered</span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="showReceiptModal('BKG-20250110-0006', 'Mattress Cover Repair', '2,500.00', '2,500.00', '0.00')">
                                <i class="fas fa-receipt mr-1"></i>Receipt
                            </button>
                        </td>
                    </tr>
                    <!-- Example: Delivered & Paid (fully paid) -->
                    <tr>
                        <td><strong class="text-primary">BKG-20250108-0007</strong></td>
                        <td>Sofa Reupholstering</td>
                        <td>₱8,500.00</td>
                        <td>₱8,500.00</td>
                        <td class="text-success font-weight-bold">₱0.00</td>
                        <td><span class="badge badge-success payment-status-badge"><i class="fas fa-check-double mr-1"></i>Delivered & Paid</span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="showReceiptModal('BKG-20250108-0007', 'Sofa Reupholstering', '8,500.00', '8,500.00', '0.00')">
                                <i class="fas fa-receipt mr-1"></i>Receipt
                            </button>
                        </td>
                    </tr>
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
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="receiptModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Payment Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="receipt-container p-4" style="background: #f8f9fc; border-radius: 10px;">
                    <!-- Company Header -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h3 class="mb-1" style="color: #4e73df; font-weight: 700;"><?php echo APP_NAME; ?></h3>
                        <p class="text-muted mb-0">Upholstery Services</p>
                        <p class="text-muted small mb-0">Cash on Delivery Receipt</p>
                    </div>

                    <!-- Receipt Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Booking ID:</strong> <span id="receipt-booking-id" class="text-primary"></span></p>
                            <p class="mb-2"><strong>Service:</strong> <span id="receipt-service"></span></p>
                            <p class="mb-2"><strong>Date:</strong> <span id="receipt-date"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Customer:</strong> <?php echo $_SESSION['customer_name'] ?? 'Customer Name'; ?></p>
                            <p class="mb-2"><strong>Payment Method:</strong> <span class="badge badge-info">Cash on Delivery</span></p>
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered bg-white">
                            <thead style="background: #e7e7e7;">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total Amount Due</td>
                                    <td class="text-right">₱<span id="receipt-total"></span></td>
                                </tr>
                                <tr>
                                    <td>Previously Paid</td>
                                    <td class="text-right">₱<span id="receipt-paid"></span></td>
                                </tr>
                                <tr style="background: #f1f1f1; font-weight: bold;">
                                    <td>Amount Paid Today (COD)</td>
                                    <td class="text-right text-success">₱<span id="receipt-balance"></span></td>
                                </tr>
                            </tbody>
                            <tfoot style="background: #4e73df; color: white;">
                                <tr>
                                    <th>TOTAL PAID</th>
                                    <th class="text-right">₱<span id="receipt-final-total"></span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment Status Message -->
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Payment Received!</strong> This receipt confirms your cash on delivery payment. Please keep this for your records.
                    </div>

                    <!-- Footer Note -->
                    <div class="text-center text-muted small">
                        <p class="mb-1">Thank you for choosing <?php echo APP_NAME; ?>!</p>
                        <p class="mb-0">For inquiries, please contact our customer service.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()">
                    <i class="fas fa-print mr-1"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentBookingId = '';

function showReceiptModal(bookingId, service, totalDue, amountPaid, balance) {
    currentBookingId = bookingId;
    
    // Populate modal with data
    document.getElementById('receipt-booking-id').textContent = bookingId;
    document.getElementById('receipt-service').textContent = service;
    document.getElementById('receipt-date').textContent = new Date().toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('receipt-total').textContent = totalDue;
    document.getElementById('receipt-paid').textContent = amountPaid;
    document.getElementById('receipt-balance').textContent = balance;
    
    // Calculate final total (total due = amount paid + balance)
    let finalTotal = parseFloat(totalDue.replace(/,/g, ''));
    document.getElementById('receipt-final-total').textContent = finalTotal.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    // Show modal
    $('#receiptModal').modal('show');
}

function printReceipt() {
    window.print();
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .receipt-container, .receipt-container * {
        visibility: visible;
    }
    .receipt-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .modal-header, .modal-footer {
        display: none;
    }
}
</style>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

