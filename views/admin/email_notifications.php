<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Email Notifications</h1>
</div>

<!-- Email Configuration Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary-admin">
                    <i class="fas fa-envelope mr-2"></i>Email Configuration
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>SMTP Host</strong></label>
                            <p class="form-control-plaintext"><?php echo EMAIL_SMTP_HOST; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>SMTP Port</strong></label>
                            <p class="form-control-plaintext"><?php echo EMAIL_SMTP_PORT; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>From Email</strong></label>
                            <p class="form-control-plaintext"><?php echo EMAIL_FROM_ADDRESS; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>From Name</strong></label>
                            <p class="form-control-plaintext"><?php echo EMAIL_FROM_NAME; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Email Status</strong></label>
                            <p>
                                <?php if (EMAIL_ENABLED): ?>
                                    <span class="font-weight-bold" style="color: var(--uphol-green);">Enabled</span>
                                <?php else: ?>
                                    <span class="text-danger font-weight-bold">Disabled</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Test Mode</strong></label>
                            <p>
                                <?php if (EMAIL_TEST_MODE): ?>
                                    <span class="font-weight-bold" style="color: var(--uphol-orange);">Test Mode</span>
                                <?php else: ?>
                                    <span class="text-primary-admin font-weight-bold">Live Mode</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Test Email Configuration</h6>
                        <p class="text-muted">Send a test email to verify your email configuration is working properly.</p>
                        
                        <form id="testEmailForm">
                            <div class="form-group">
                                <label for="testEmail">Test Email Address</label>
                                <input type="email" class="form-control" id="testEmail" placeholder="Enter email address to test" required>
                            </div>
                            <button type="submit" class="btn btn-primary-admin">
                                <i class="fas fa-paper-plane mr-1"></i> Send Test Email
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary-admin">
                    <i class="fas fa-info-circle mr-2"></i>Email Templates
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Reservation Approval</h6>
                            <small class="text-muted">Sent when admin accepts a reservation</small>
                        </div>
                        <span class="font-weight-bold" style="color: var(--uphol-green);">Active</span>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Reservation Rejection</h6>
                            <small class="text-muted">Sent when admin rejects a reservation</small>
                        </div>
                        <span class="font-weight-bold" style="color: var(--uphol-green);">Active</span>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="font-weight-bold">Quick Actions</h6>
                <div class="btn-group-vertical w-100" role="group">
                    <button type="button" class="btn btn-outline-primary-admin btn-sm" onclick="viewEmailLogs()">
                        <i class="fas fa-list mr-1"></i> View Email Logs
                    </button>
                    <button type="button" class="btn btn-outline-primary-admin btn-sm" onclick="previewTemplates()">
                        <i class="fas fa-eye mr-1"></i> Preview Templates
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary-admin">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Setup Instructions
                </h6>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold">Gmail Setup:</h6>
                <ol class="small">
                    <li>Enable 2-Factor Authentication</li>
                    <li>Generate App Password</li>
                    <li>Update config/email.php</li>
                    <li>Test configuration</li>
                </ol>
                
                <h6 class="font-weight-bold">Other SMTP:</h6>
                <ol class="small">
                    <li>Update SMTP settings</li>
                    <li>Configure credentials</li>
                    <li>Test configuration</li>
                </ol>
                
                <div class="alert alert-info small mt-3" style="border-left: 5px solid var(--uphol-blue);">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Note:</strong> Email notifications are automatically sent when admins accept or reject reservations.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Logs Modal -->
<div class="modal fade" id="emailLogsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-list mr-2"></i>Email Logs
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="emailLogsContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">Loading email logs...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary-admin" onclick="refreshEmailLogs()">
                    <i class="fas fa-sync mr-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Test email form submission
document.getElementById('testEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const testEmail = document.getElementById('testEmail').value;
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';
    submitBtn.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/testEmail', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'test_email=' + encodeURIComponent(testEmail)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while sending test email.');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// View email logs
function viewEmailLogs() {
    $('#emailLogsModal').modal('show');
    loadEmailLogs();
}

// Load email logs
function loadEmailLogs() {
    fetch('<?php echo BASE_URL; ?>admin/getEmailLogs')
    .then(response => response.json())
    .then(data => {
        const content = document.getElementById('emailLogsContent');
        
        if (data.success && data.logs.length > 0) {
            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Time</th><th>To</th><th>Subject</th><th>Status</th><th>Result</th></tr></thead><tbody>';
            
            data.logs.forEach(log => {
                const logData = JSON.parse(log);
                const statusText = logData.success === 'YES' ? 
                    '<span class="text-success font-weight-bold">Success</span>' : 
                    '<span class="text-danger font-weight-bold">Failed</span>';
                
                html += `<tr>
                    <td>${logData.timestamp}</td>
                    <td>${logData.to}</td>
                    <td>${logData.subject}</td>
                    <td>${statusText}</td>
                    <td><small>${logData.status || 'N/A'}</small></td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            content.innerHTML = html;
        } else {
            content.innerHTML = '<div class="text-center text-muted"><i class="fas fa-inbox fa-2x mb-2"></i><p>No email logs found</p></div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('emailLogsContent').innerHTML = '<div class="alert alert-danger">Error loading email logs</div>';
    });
}

// Refresh email logs
function refreshEmailLogs() {
    loadEmailLogs();
}

// Preview templates
function previewTemplates() {
    alert('Email template preview feature coming soon!');
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
    
    // Insert at the top of the page
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
