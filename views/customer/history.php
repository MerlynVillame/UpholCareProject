<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
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
    color: #9499ad;
    font-size: 0.95rem;
}

.filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
}

.filter-tag {
    padding: 0.5rem 1.25rem;
    background: #f8f9fc;
    border: 1.5px solid #e3e6f0;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.85rem;
    font-weight: 600;
    color: #5a5c69;
}

.filter-tag:hover {
    background: #eaecf4;
    transform: translateY(-2px);
    border-color: #d1d3e2;
    color: #0F3C5F;
}

.filter-tag.active {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(15, 60, 95, 0.3);
}
</style>

<!-- Page Heading -->
<div class="welcome-container shadow-sm">
    <div class="welcome-text">
        <i class="fas fa-history mr-2" style="color: #0F3C5F;"></i>
        Booking History
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard" style="color: #0F3C5F; font-size: 0.85rem; font-weight: 600;">Home</a></li>
            <li class="breadcrumb-item active" style="font-size: 0.85rem; font-weight: 600;">History</li>
        </ol>
    </nav>
</div>

<!-- History Filter -->
<div class="search-filter-section">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="historySearchInput" placeholder="Search by service or booking number..." onkeyup="filterHistory()">
    </div>
    <div class="filter-tags">
        <div class="filter-tag active" data-filter="all" onclick="updateHistoryFilter('all')">All History</div>
        <div class="filter-tag" data-filter="completed" onclick="updateHistoryFilter('completed')">Completed</div>
        <div class="filter-tag" data-filter="cancelled" onclick="updateHistoryFilter('cancelled')">Cancelled</div>
    </div>
</div>

<!-- History Timeline -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Booking History</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $booking): 
            $status = strtolower($booking['status'] ?? 'pending');
        ?>
        <div class="border-left-primary shadow mb-2 p-2 history-item" style="border-left: 4px solid;" data-status="<?php echo $status; ?>">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="text-xs text-muted">
                        <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                    </div>
                    <div class="font-weight-bold text-primary small">
                        <?php echo htmlspecialchars($booking['booking_number'] ?? 'N/A'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="font-weight-bold small"><?php echo htmlspecialchars($booking['service_name'] ?? 'Unknown Service'); ?></div>
                    <div class="x-small text-muted" style="font-size: 0.7rem;"><?php echo htmlspecialchars($booking['item_description'] ?? 'No description'); ?></div>
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
                    <div class="font-weight-bold small">₱<?php echo number_format($booking['total_amount'] ?? 0, 2); ?></div>
                    <?php if (isset($booking['payment_status']) && strtolower($booking['payment_status']) !== 'unpaid'): ?>
                        <div class="small text-muted"><?php echo ucfirst($booking['payment_status']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-2 text-right">
                            <button type="button" 
                                    class="btn btn-sm btn-success btn-action" 
                            onclick="viewOfficialReceipt(<?php echo $booking['id']; ?>)" 
                            title="View Official Receipt">
                                <i class="fas fa-receipt"></i>
                            </button>
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

/* Modal sizing to respect sidebar */
#officialReceiptModal .modal-dialog {
    max-width: 900px !important;
    width: 90% !important;
    margin: 1.75rem auto !important;
}

/* Ensure modal doesn't overlap sidebar on larger screens */
@media (min-width: 992px) {
    #officialReceiptModal .modal-dialog {
        max-width: 850px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
}

/* On smaller screens, make it responsive */
@media (max-width: 991.98px) {
    #officialReceiptModal .modal-dialog {
        max-width: 95% !important;
        margin: 1rem auto !important;
    }
}
</style>

<!-- Official Receipt Modal -->
<div class="modal fade" id="officialReceiptModal" tabindex="-1" role="dialog" aria-labelledby="officialReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="officialReceiptModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-receipt mr-2"></i>Official Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 2rem; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem; max-height: 80vh; overflow-y: auto;">
                <div id="officialReceiptContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading official receipt...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 1.5rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printOfficialReceipt()" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-print mr-1"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentReceiptBookingId = null;

function viewOfficialReceipt(bookingId) {
    currentReceiptBookingId = bookingId;
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#officialReceiptModal').modal('show');
    } else {
        const modalEl = document.getElementById('officialReceiptModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }
    
    // Show loading
    document.getElementById('officialReceiptContent').innerHTML = 
        '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading official receipt...</p></div>';
    
    // Load official receipt data
    fetch('<?php echo BASE_URL; ?>customer/getOfficialReceipt/' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.receipt) {
                populateOfficialReceiptModal(data.receipt);
            } else {
                document.getElementById('officialReceiptContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading official receipt: ' + (data.message || 'Unknown error') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('officialReceiptContent').innerHTML = 
                '<div class="alert alert-danger">Error loading official receipt. Please try again.</div>';
        });
}

// Populate Official Receipt Modal
function populateOfficialReceiptModal(receipt) {
    const modalBody = document.getElementById('officialReceiptContent');
    if (!modalBody) return;
    
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
            <h3 style="color: #0F3C5F; font-weight: 700; margin-top: 15px; text-transform: uppercase;">Official Receipt</h3>
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
                        <tr style="background: #0F3C5F; color: white; font-size: 1.2rem;">
                            <td style="font-weight: 700; border-color: #0F3C5F;"><strong>TOTAL AMOUNT DUE</strong></td>
                            <td style="text-align: right; font-weight: 700; border-color: #0F3C5F;"><strong>₱${parseFloat(receipt.payment?.grandTotal || receipt.payment?.totalAmount || 0).toFixed(2)}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; background: #f8f9fc;"><strong>TOTAL AMOUNT PAID</strong></td>
                            <td style="text-align: right; font-weight: 600;"><strong>₱${parseFloat(receipt.payment?.totalPaid || receipt.payment?.grandTotal || receipt.payment?.totalAmount || 0).toFixed(2)}</strong></td>
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

// History Filter Logic
let currentHistoryStatusFilter = 'all';

function updateHistoryFilter(status) {
    currentHistoryStatusFilter = status;
    
    // Update active class
    document.querySelectorAll('.filter-tag').forEach(tag => {
        if (tag.getAttribute('data-filter') === status) {
            tag.classList.add('active');
        } else {
            tag.classList.remove('active');
        }
    });
    
    filterHistory();
}

function filterHistory() {
    const searchTerm = document.getElementById('historySearchInput').value.toLowerCase().trim();
    const items = document.querySelectorAll('.history-item');
    
    items.forEach(item => {
        const itemStatus = (item.getAttribute('data-status') || '').toLowerCase();
        const serviceName = (item.querySelector('.font-weight-bold.small') || {}).textContent || '';
        const bookingNumber = (item.querySelector('.text-primary.small') || {}).textContent || '';
        
        const matchesStatus = (currentHistoryStatusFilter === 'all' || itemStatus === currentHistoryStatusFilter);
        const matchesSearch = !searchTerm || 
                             serviceName.toLowerCase().includes(searchTerm) || 
                             bookingNumber.toLowerCase().includes(searchTerm);
        
        if (matchesStatus && matchesSearch) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function printOfficialReceipt() {
    const receiptContent = document.getElementById('officialReceiptContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Official Receipt - ${currentReceiptBookingId || 'N/A'}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                table td, table th { padding: 0.75rem; border: 1px solid #ddd; }
                @media print {
                    body { padding: 0; }
                }
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

