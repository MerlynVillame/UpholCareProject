<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Repair Items Management</h1>
    <div>
        <a href="<?php echo BASE_URL; ?>admin/bookingNumbers" class="btn btn-sm btn-primary mr-2">
            <i class="fas fa-ticket-alt mr-1"></i> Manage Booking Numbers
        </a>
        <a href="<?php echo BASE_URL; ?>admin/assignBookingNumber" class="btn btn-sm btn-success">
            <i class="fas fa-plus mr-1"></i> Assign Booking Number
        </a>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tools mr-2"></i>Customer Repair Requests
                </h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="repairItemsTable">
                        <thead>
                            <tr>
                                <th>Booking #</th>
                                <th>Customer</th>
                                <th>Repair Item</th>
                                <th>Type</th>
                                <th>Urgency</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Assigned By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($repairItems)): ?>
                                <?php foreach ($repairItems as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($item['booking_number']); ?></span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['customer_name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['email']); ?></small>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['phone']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($item['item_description'], 0, 50)) . '...'; ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary"><?php echo ucfirst($item['item_type']); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $urgencyClass = '';
                                            switch($item['urgency']) {
                                                case 'low': $urgencyClass = 'badge-secondary'; break;
                                                case 'normal': $urgencyClass = 'badge-info'; break;
                                                case 'high': $urgencyClass = 'badge-warning'; break;
                                                case 'urgent': $urgencyClass = 'badge-danger'; break;
                                                default: $urgencyClass = 'badge-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $urgencyClass; ?>"><?php echo ucfirst($item['urgency']); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch($item['status']) {
                                                case 'pending': $statusClass = 'badge-warning'; break;
                                                case 'quoted': $statusClass = 'badge-info'; break;
                                                case 'approved': $statusClass = 'badge-success'; break;
                                                case 'in_progress': $statusClass = 'badge-primary'; break;
                                                case 'completed': $statusClass = 'badge-success'; break;
                                                case 'cancelled': $statusClass = 'badge-danger'; break;
                                                default: $statusClass = 'badge-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($item['status']); ?></span>
                                        </td>
                                        <td>
                                            <small><?php echo date('M d, Y', strtotime($item['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($item['assigned_by_admin']); ?></small>
                                            <br>
                                            <small class="text-muted"><?php echo date('M d', strtotime($item['assigned_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if ($item['status'] === 'pending'): ?>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="acceptRepairItem(<?php echo $item['id']; ?>)"
                                                            title="Accept Repair Request">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="rejectRepairItem(<?php echo $item['id']; ?>)"
                                                            title="Reject Repair Request">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        onclick="viewRepairDetails(<?php echo $item['id']; ?>)"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="createQuotation(<?php echo $item['id']; ?>)"
                                                        title="Create Quotation">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        <i class="fas fa-tools fa-2x mb-2"></i>
                                        <br>No repair items found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accept Repair Item Modal -->
<div class="modal fade" id="acceptRepairModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i>Accept Repair Request & Assign Booking Number
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="accept_repair_item_id">
                <div class="form-group">
                    <label for="booking_number_id">Select Booking Number <span class="text-danger">*</span></label>
                    <select class="form-control" id="booking_number_id" required>
                        <option value="">Select a booking number...</option>
                    </select>
                    <small class="form-text text-muted">Select the booking number to assign to this customer.</small>
                </div>
                <div class="form-group">
                    <label for="admin_notes">Admin Notes (Optional)</label>
                    <textarea class="form-control" id="admin_notes" rows="4" 
                              placeholder="Add any notes about this repair request..."></textarea>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> The customer will be notified via email and in-app notification that their repair request has been approved and their booking number.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmAcceptRepair()">
                    <i class="fas fa-check mr-1"></i> Accept & Assign Booking Number
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Repair Item Modal -->
<div class="modal fade" id="rejectRepairModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle mr-2"></i>Reject Repair Request
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject_repair_item_id">
                <div class="form-group">
                    <label for="rejection_reason">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejection_reason" rows="4" 
                              placeholder="Please provide a reason for rejecting this repair request..." required></textarea>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The customer will be notified of the rejection.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejectRepair()">
                    <i class="fas fa-times mr-1"></i> Reject Repair Request
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// DataTable initialization
$(document).ready(function() {
    $('#repairItemsTable').DataTable({
        "order": [[ 6, "desc" ]], // Sort by request date descending
        "pageLength": 25
    });
});

// Accept repair item
function acceptRepairItem(repairItemId) {
    document.getElementById('accept_repair_item_id').value = repairItemId;
    document.getElementById('admin_notes').value = '';
    document.getElementById('booking_number_id').value = '';
    
    // Load available booking numbers
    loadAvailableBookingNumbers();
    
    $('#acceptRepairModal').modal('show');
}

// Load available booking numbers
function loadAvailableBookingNumbers() {
    fetch('<?php echo BASE_URL; ?>admin/getAvailableBookingNumbers', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('booking_number_id');
        select.innerHTML = '<option value="">Select a booking number...</option>';
        
        if (data.success && data.data && data.data.length > 0) {
            data.data.forEach(function(bookingNumber) {
                const option = document.createElement('option');
                option.value = bookingNumber.id;
                option.textContent = bookingNumber.booking_number;
                select.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No available booking numbers';
            option.disabled = true;
            select.appendChild(option);
        }
    })
    .catch(error => {
        console.error('Error loading booking numbers:', error);
    });
}

// Confirm accept repair
function confirmAcceptRepair() {
    const repairItemId = document.getElementById('accept_repair_item_id').value;
    const adminNotes = document.getElementById('admin_notes').value.trim();
    const bookingNumberId = document.getElementById('booking_number_id').value;
    
    if (!bookingNumberId) {
        alert('Please select a booking number.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/acceptRepairItem', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'repair_item_id=' + repairItemId + '&admin_notes=' + encodeURIComponent(adminNotes) + '&booking_number_id=' + bookingNumberId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close accept modal
            $('#acceptRepairModal').modal('hide');
            
            // Show confirmation modal with booking number
            showAcceptConfirmationModal(data.message, data.booking_number);
            
            // Update the row status
            const row = document.querySelector(`button[onclick*="${repairItemId}"]`).closest('tr');
            const statusCell = row.querySelector('td:nth-child(6)'); // Status column
            statusCell.innerHTML = '<span class="badge badge-success">Approved</span>';
            
            // Update booking number in the row if exists
            const bookingCell = row.querySelector('td:nth-child(1)'); // Booking # column
            if (bookingCell && data.booking_number) {
                bookingCell.innerHTML = '<span class="badge badge-info">' + data.booking_number + '</span>';
            }
            
            // Remove accept/reject buttons
            const actionCell = row.querySelector('td:last-child');
            actionCell.innerHTML = `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-info" 
                            onclick="viewRepairDetails(${repairItemId})"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="createQuotation(${repairItemId})"
                            title="Create Quotation">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </button>
                </div>
            `;
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while accepting the repair request.');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Reject repair item
function rejectRepairItem(repairItemId) {
    document.getElementById('reject_repair_item_id').value = repairItemId;
    document.getElementById('rejection_reason').value = '';
    $('#rejectRepairModal').modal('show');
}

// Confirm reject repair
function confirmRejectRepair() {
    const repairItemId = document.getElementById('reject_repair_item_id').value;
    const rejectionReason = document.getElementById('rejection_reason').value.trim();
    
    if (!rejectionReason) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/rejectRepairItem', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'repair_item_id=' + repairItemId + '&rejection_reason=' + encodeURIComponent(rejectionReason)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            $('#rejectRepairModal').modal('hide');
            
            // Show success message
            showAlert('success', data.message);
            
            // Update the row status
            const row = document.querySelector(`button[onclick*="${repairItemId}"]`).closest('tr');
            const statusCell = row.querySelector('td:nth-child(6)'); // Status column
            statusCell.innerHTML = '<span class="badge badge-danger">Cancelled</span>';
            
            // Remove accept/reject buttons
            const actionCell = row.querySelector('td:last-child');
            actionCell.innerHTML = `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-info" 
                            onclick="viewRepairDetails(${repairItemId})"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            `;
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while rejecting the repair request.');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// View repair details
function viewRepairDetails(repairItemId) {
    // Implement repair details view
    alert('Repair details for ID: ' + repairItemId);
}

// Create quotation
function createQuotation(repairItemId) {
    // Implement quotation creation
    alert('Create quotation for repair item ID: ' + repairItemId);
}

// Show alert message
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Insert at the top of the card body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = cardBody.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Show accept confirmation modal
function showAcceptConfirmationModal(message, bookingNumber) {
    document.getElementById('confirmation_message').textContent = message;
    document.getElementById('confirmation_booking_number').textContent = bookingNumber || 'N/A';
    $('#acceptConfirmationModal').modal('show');
}
</script>

<!-- Accept Confirmation Modal -->
<div class="modal fade" id="acceptConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="acceptConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="acceptConfirmationModalLabel">
                    <i class="fas fa-check-circle mr-2"></i>Reservation Accepted Successfully!
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4" style="padding: 2rem;">
                <div class="mb-4">
                    <i class="fas fa-check-circle" style="font-size: 5rem; color: #1cc88a;"></i>
                </div>
                <h4 style="color: #2c3e50; font-weight: 700; margin-bottom: 1rem;">Success!</h4>
                <p class="text-muted mb-4" id="confirmation_message"></p>
                
                <div class="alert alert-success" style="background: #d4edda; border-color: #c3e6cb; color: #155724;">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-ticket-alt mr-2" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Booking Number Assigned:</strong>
                            <div class="mt-2">
                                <span class="badge badge-success" style="font-size: 1.25rem; padding: 0.5rem 1rem;" id="confirmation_booking_number">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3 mb-0" style="background: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
                    <i class="fas fa-info-circle mr-2"></i>
                    <small>The customer has been notified via email and in-app notification about their booking number assignment.</small>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 1rem 2rem 2rem;">
                <button type="button" class="btn btn-success btn-block" data-dismiss="modal" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600;">
                    <i class="fas fa-check mr-2"></i>Got It!
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
