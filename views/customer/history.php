<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Booking History</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item active">History</li>
            </ol>
        </nav>
    </div>
</div>

<!-- History Filter -->
<div class="row mb-4">
    <div class="col-md-3">
        <select class="form-control">
            <option>All Time</option>
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
            <option>Last 3 Months</option>
            <option>Last Year</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control">
            <option>All Statuses</option>
            <option>Completed</option>
            <option>Cancelled</option>
        </select>
    </div>
</div>

<!-- History Timeline -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Booking History</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $booking): ?>
        <div class="border-left-primary shadow mb-3 p-3" style="border-left: 4px solid;">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="text-xs text-muted">
                        <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                    </div>
                    <div class="font-weight-bold text-primary">
                        <?php echo htmlspecialchars($booking['booking_number'] ?? 'N/A'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="font-weight-bold"><?php echo htmlspecialchars($booking['service_name'] ?? 'Unknown Service'); ?></div>
                    <div class="small text-muted"><?php echo htmlspecialchars($booking['item_description'] ?? 'No description'); ?></div>
                </div>
                <div class="col-md-2">
                    <?php
                    $statusClass = 'badge-' . str_replace('_', '-', $booking['status'] ?? 'pending');
                    $statusText = ucwords(str_replace('_', ' ', $booking['status'] ?? 'pending'));
                    ?>
                    <span class="badge <?php echo $statusClass; ?>">
                        <?php echo $statusText; ?>
                    </span>
                </div>
                <div class="col-md-2">
                    <div class="font-weight-bold">₱<?php echo number_format($booking['total_amount'] ?? 0, 2); ?></div>
                    <div class="small text-muted"><?php echo ucfirst($booking['payment_status'] ?? 'unpaid'); ?></div>
                </div>
                <div class="col-md-2 text-right">
                    <div class="btn-group" role="group" style="flex-wrap: wrap; gap: 3px;">
                        <a href="<?php echo BASE_URL; ?>customer/viewBooking/<?php echo $booking['id']; ?>" 
                           class="btn btn-sm btn-primary btn-action" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php
                        $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                        $isPaid = in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']);
                        if ($isPaid): ?>
                            <button type="button" 
                                    class="btn btn-sm btn-success btn-action" 
                                    onclick="viewReceipt(<?php echo $booking['id']; ?>)" 
                                    title="View Receipt">
                                <i class="fas fa-receipt"></i>
                            </button>
                            <a href="<?php echo BASE_URL; ?>customer/downloadReceipt/<?php echo $booking['id']; ?>" 
                               class="btn btn-sm btn-info btn-action" 
                               title="Download Receipt" 
                               target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-500">No booking history yet</h5>
            <p class="text-muted">Your completed bookings will appear here</p>
        </div>
        <?php endif; ?>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<style>
.btn-action {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem !important;
    line-height: 1.2 !important;
    min-width: 28px !important;
    height: 28px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.btn-action i {
    font-size: 0.75rem !important;
    margin: 0 !important;
}
</style>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
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
                <div id="receiptContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading receipt...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-print mr-1"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentReceiptBookingId = null;

function viewReceipt(bookingId) {
    currentReceiptBookingId = bookingId;
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#receiptModal').modal('show');
    } else {
        const modalEl = document.getElementById('receiptModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }
    
    // Load receipt data
    fetch('<?php echo BASE_URL; ?>customer/getBookingDetails/' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                displayReceipt(data.data);
            } else {
                document.getElementById('receiptContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading receipt: ' + (data.message || 'Unknown error') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('receiptContent').innerHTML = 
                '<div class="alert alert-danger">Error loading receipt. Please try again.</div>';
        });
}

function displayReceipt(booking) {
    const paymentStatus = (booking.payment_status || 'unpaid').toLowerCase();
    const isPaid = ['paid', 'paid_full_cash', 'paid_on_delivery_cod'].includes(paymentStatus);
    
    if (!isPaid) {
        document.getElementById('receiptContent').innerHTML = 
            '<div class="alert alert-warning">Receipt is only available for paid bookings.</div>';
        return;
    }
    
    // Calculate total
    const laborFee = parseFloat(booking.labor_fee || 0);
    const pickupFee = parseFloat(booking.pickup_fee || 0);
    const deliveryFee = parseFloat(booking.delivery_fee || 0);
    const colorPrice = parseFloat(booking.color_price || 0);
    const grandTotal = laborFee + pickupFee + deliveryFee + colorPrice;
    
    const receiptHtml = `
        <div class="receipt-container p-4" style="background: white; border: 2px solid #e3e6f0; border-radius: 10px;">
            <div class="text-center mb-4 pb-3 border-bottom border-primary">
                <h2 class="mb-2" style="color: #4e73df; font-weight: 700; font-size: 2rem;">UphoCare</h2>
                <p class="text-muted mb-1" style="font-size: 1.1rem; font-weight: 600;">Upholstery Services</p>
                <p class="mb-0" style="color: #28a745; font-weight: 600; font-size: 1rem;">Payment Receipt</p>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Booking Number:</strong></p>
                    <p>${booking.booking_number || 'N/A'}</p>
                </div>
                <div class="col-md-6 text-right">
                    <p class="mb-1"><strong>Date:</strong></p>
                    <p>${new Date(booking.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                </div>
            </div>
            
            <div class="mb-3">
                <p class="mb-1"><strong>Service:</strong></p>
                <p>${booking.service_name || 'N/A'}</p>
            </div>
            
            <div class="mb-3">
                <p class="mb-1"><strong>Item Description:</strong></p>
                <p>${booking.item_description || 'N/A'}</p>
            </div>
            
            <hr style="border-color: #e3e6f0;">
            
            <h6 class="mb-3" style="font-weight: 700; color: #2c3e50;">Payment Breakdown</h6>
            <table class="table table-bordered mb-3">
                <tbody>
                    ${laborFee > 0 ? `<tr><td>Labor Fee</td><td class="text-right">₱${laborFee.toFixed(2)}</td></tr>` : ''}
                    ${pickupFee > 0 ? `<tr><td>Pickup Fee</td><td class="text-right">₱${pickupFee.toFixed(2)}</td></tr>` : ''}
                    ${deliveryFee > 0 ? `<tr><td>Delivery Fee</td><td class="text-right">₱${deliveryFee.toFixed(2)}</td></tr>` : ''}
                    ${colorPrice > 0 ? `<tr><td>Fabric/Color Price</td><td class="text-right">₱${colorPrice.toFixed(2)}</td></tr>` : ''}
                    <tr style="background: #28a745; color: white;">
                        <td><strong>TOTAL AMOUNT</strong></td>
                        <td class="text-right"><strong>₱${grandTotal.toFixed(2)}</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="text-center mt-4 pt-3 border-top border-primary">
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Thank you for your business!</p>
            </div>
        </div>
    `;
    
    document.getElementById('receiptContent').innerHTML = receiptHtml;
}

function printReceipt() {
    const receiptContent = document.getElementById('receiptContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt - ${currentReceiptBookingId || 'N/A'}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .receipt-container { background: white; }
                table { width: 100%; border-collapse: collapse; }
                table td { padding: 0.75rem; border: 1px solid #ddd; }
            </style>
        </head>
        <body>
            ${receiptContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
    }, 250);
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

