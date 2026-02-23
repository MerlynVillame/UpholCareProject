
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
/* Override Bootstrap primary colors with brown */
.btn-primary {
    background: var(--uphol-blue);
    border-color: var(--uphol-blue);
    color: white !important;
}

.btn-primary:hover {
    background: var(--uphol-navy);
    border-color: var(--uphol-navy);
    color: white !important;
}

.btn-info {
    background: var(--uphol-blue);
    border-color: var(--uphol-blue);
    color: white !important;
}

.btn-info:hover {
    background: var(--uphol-navy);
    border-color: var(--uphol-navy);
    color: white !important;
}

.text-primary {
    color: #1F4E79 !important;
}

.card-header {
    background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%);
    color: white;
}

.modal-header {
    background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%);
    color: white;
}

/* ============================================
   MODAL CLICKABILITY FIX - COMPREHENSIVE
   ============================================ */

/* Ensure modal is always on top */
#serviceModal {
    z-index: 1055 !important;
    position: fixed !important;
}

#serviceModal.show {
    display: block !important;
    z-index: 1055 !important;
}

/* Backdrop - MUST NOT block clicks */
.modal-backdrop {
    z-index: 1050 !important;
    pointer-events: none !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
}

.modal-backdrop.show {
    z-index: 1050 !important;
    pointer-events: none !important;
}

/* Modal dialog and content - MUST be clickable */
#serviceModal .modal-dialog {
    z-index: 1056 !important;
    position: relative !important;
    pointer-events: auto !important;
    margin: 1.75rem auto !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
}

#serviceModal .modal-content {
    z-index: 1057 !important;
    position: relative !important;
    pointer-events: auto !important;
    border-radius: 0.35rem !important;
}

/* All form elements MUST be clickable */
#serviceModal * {
    pointer-events: auto !important;
}

#serviceModal input,
#serviceModal select,
#serviceModal textarea,
#serviceModal button,
#serviceModal label {
    pointer-events: auto !important;
    cursor: default !important;
}

#serviceModal input[type="text"],
#serviceModal input[type="number"],
#serviceModal textarea {
    cursor: text !important;
}

#serviceModal select {
    cursor: pointer !important;
}

#serviceModal button {
    cursor: pointer !important;
}

/* Lower z-index of other elements when modal is open */
body.modal-open .topbar,
body.modal-open .navbar,
body.modal-open .sidebar,
body.modal-open #accordionSidebar {
    z-index: 1 !important;
}

/* Ensure topbar doesn't block modal */
.navbar {
    z-index: 100 !important;
}

body.modal-open .navbar {
    z-index: 1 !important;
}

/* Hide any other modals when serviceModal is open */
body.modal-open #logoutModal {
    display: none !important;
    visibility: hidden !important;
    pointer-events: none !important;
    z-index: -1 !important;
    opacity: 0 !important;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Services Management</h1>
    </div>
    <div>
        <button type="button" class="btn btn-secondary mr-2" onclick="loadArchivedServices()" data-toggle="modal" data-target="#archivedModal">
            <i class="fas fa-archive mr-2"></i>Archived Services
        </button>
        <button type="button" class="btn btn-primary-admin" data-toggle="modal" data-target="#serviceModal" onclick="openServiceModal()">
            <i class="fas fa-plus mr-2"></i>Add New Service
        </button>
    </div>
</div>

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

<!-- Services Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">All Services</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Service Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $service): ?>
                        <tr data-service-id="<?php echo $service['id']; ?>">
                            <td><?php echo $service['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($service['service_name']); ?></strong></td>
                            <td>
                                <?php if (!empty($service['category_name'])): ?>
                                    <span class="text-info font-weight-bold"><?php echo htmlspecialchars($service['category_name']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">No Category</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($service['service_type'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($service['price']): ?>
                                    <strong class="text-primary-admin">₱<?php echo number_format($service['price'], 2); ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">Not Set</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($service['status'] === 'active'): ?>
                                    <span style="color:#28a745; font-weight:600;">Active</span>
                                <?php else: ?>
                                    <span style="color:#6c757d; font-weight:600;"><?php echo ucfirst($service['status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($service['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-info" onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="removeService(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars($service['service_name']); ?>')" title="Remove (Archive)" style="background:#e67e22;border-color:#e67e22;color:white;">
                                        <i class="fas fa-archive"></i> Remove
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-tools fa-3x mb-3" style="opacity: 0.3;"></i>
                                <p>No services found. Add your first service to get started.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Service Modal (Create/Edit) -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">
                    <i class="fas fa-tools mr-2"></i><span id="modalTitle">Add New Service</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="serviceForm">
                <div class="modal-body">
                    <input type="hidden" id="service_id" name="service_id">
                    
                    <div class="form-group">
                        <label for="service_name">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="service_name" name="service_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service_type">Service Type</label>
                                <input type="text" class="form-control" id="service_type" name="service_type" placeholder="e.g., Vehicle Upholstery, Bedding, Furniture">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Service description..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price (₱)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-admin">
                        <i class="fas fa-save mr-1"></i><span id="submitBtnText">Create Service</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archived Services Modal -->
<div class="modal fade" id="archivedModal" tabindex="-1" role="dialog" aria-labelledby="archivedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #495057 0%, #6c757d 100%);">
                <h5 class="modal-title" id="archivedModalLabel" style="color:white;">
                    <i class="fas fa-archive mr-2"></i>Archived Services
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <!-- Info banner -->
                <div style="background:#fff3cd; border-left:4px solid #ffc107; padding:12px 20px; margin:0; font-size:13px; color:#856404;">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Archived Services</strong> — These services are hidden from customers. Click <strong>Restore</strong> to make a service active again.
                </div>
                <!-- Table -->
                <div class="table-responsive" style="padding: 20px;">
                    <table class="table table-bordered" id="archivedTable" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Service Type</th>
                                <th>Price</th>
                                <th>Archived On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="archivedTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<script>
// ============================================
// MODAL CLICKABILITY FIX - JAVASCRIPT
// ============================================

function fixServiceModalClickability() {
    const modal = document.getElementById('serviceModal');
    if (!modal) return;
    
    // Hide logoutModal completely
    const logoutModal = document.getElementById('logoutModal');
    if (logoutModal) {
        logoutModal.style.cssText = 'display: none !important; visibility: hidden !important; pointer-events: none !important; z-index: -1 !important; opacity: 0 !important;';
    }
    
    // Fix backdrop - MUST NOT block clicks
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(backdrop) {
        backdrop.style.cssText = 'z-index: 1050 !important; pointer-events: none !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background-color: rgba(0, 0, 0, 0.5) !important;';
    });
    
    // Ensure modal has highest z-index
    modal.style.cssText = 'z-index: 1055 !important; position: fixed !important; display: block !important;';
    
    // Fix modal dialog
    const modalDialog = modal.querySelector('.modal-dialog');
    if (modalDialog) {
        modalDialog.style.cssText = 'z-index: 1056 !important; position: relative !important; pointer-events: auto !important; margin: 1.75rem auto !important; max-height: 90vh !important; overflow-y: auto !important;';
    }
    
    // Fix modal content
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.style.cssText = 'z-index: 1057 !important; position: relative !important; pointer-events: auto !important;';
    }
    
    // Lower topbar and sidebar z-index
    const topbar = document.querySelector('.topbar, .navbar');
    if (topbar) {
        topbar.style.zIndex = '1';
    }
    
    const sidebar = document.querySelector('.sidebar, #accordionSidebar');
    if (sidebar) {
        sidebar.style.zIndex = '1';
    }
    
    // Ensure all form elements are clickable
    const formElements = modal.querySelectorAll('input, select, textarea, button, label');
    formElements.forEach(function(el) {
        el.style.pointerEvents = 'auto';
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            el.style.cursor = 'text';
        } else if (el.tagName === 'SELECT' || el.tagName === 'BUTTON') {
            el.style.cursor = 'pointer';
        }
    });
}

// Fix modal when it opens - wrapped in safe initialization
(function initServiceModal() {
    if (typeof jQuery === 'undefined') {
        // Retry after jQuery loads
        setTimeout(initServiceModal, 100);
        return;
    }
    
    jQuery(function($) {
        $('#serviceModal').on('show.bs.modal', function() {
            setTimeout(fixServiceModalClickability, 10);
        });
        
        $('#serviceModal').on('shown.bs.modal', function() {
            fixServiceModalClickability();
            // Keep fixing every 200ms while modal is open
            const fixInterval = setInterval(function() {
                if ($('#serviceModal').hasClass('show')) {
                    fixServiceModalClickability();
                } else {
                    clearInterval(fixInterval);
                }
            }, 200);
        });
        
        $('#serviceModal').on('hidden.bs.modal', function() {
            // Reset styles when modal closes
            const logoutModal = document.getElementById('logoutModal');
            if (logoutModal) {
                logoutModal.style.removeProperty('display');
                logoutModal.style.removeProperty('visibility');
                logoutModal.style.removeProperty('pointer-events');
                logoutModal.style.removeProperty('z-index');
                logoutModal.style.removeProperty('opacity');
            }
            
            const topbar = document.querySelector('.topbar, .navbar');
            if (topbar) {
                topbar.style.removeProperty('z-index');
            }
            
            const sidebar = document.querySelector('.sidebar, #accordionSidebar');
            if (sidebar) {
                sidebar.style.removeProperty('z-index');
            }
        });
    });
})();

// Also fix on any modal backdrop click (prevent backdrop from blocking)
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('modal-backdrop')) {
        const backdrop = e.target;
        backdrop.style.pointerEvents = 'none';
        fixServiceModalClickability();
    }
});

let isEditMode = false;

function openServiceModal() {
    isEditMode = false;
    document.getElementById('serviceForm').reset();
    document.getElementById('service_id').value = '';
    document.getElementById('modalTitle').textContent = 'Add New Service';
    document.getElementById('submitBtnText').textContent = 'Create Service';
}

function editService(service) {
    isEditMode = true;
    document.getElementById('service_id').value = service.id;
    document.getElementById('service_name').value = service.service_name || '';
    document.getElementById('category_id').value = service.category_id || '';
    document.getElementById('service_type').value = service.service_type || '';
    document.getElementById('description').value = service.description || '';
    document.getElementById('price').value = service.price || '';
    document.getElementById('status').value = service.status || 'active';
    
    document.getElementById('modalTitle').textContent = 'Edit Service';
    document.getElementById('submitBtnText').textContent = 'Update Service';
    
    // Wait for jQuery to be available before showing modal
    (function showEditModal() {
        if (typeof jQuery === 'undefined') {
            setTimeout(showEditModal, 100);
            return;
        }
        jQuery('#serviceModal').modal('show');
    })();
}

function removeService(serviceId, serviceName) {
    if (!confirm('Move "' + serviceName + '" to Archived Services?\n\nYou can restore it later from Archived Services.')) {
        return;
    }

    const formData = new FormData();
    formData.append('service_id', serviceId);

    fetch('<?php echo BASE_URL; ?>admin/deleteService', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row from the table immediately
            const row = document.querySelector(`tr[data-service-id="${serviceId}"]`);
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
            showAlert('success', data.message);
            // Open archived modal after short delay
            setTimeout(() => {
                loadArchivedServices();
                jQuery('#archivedModal').modal('show');
            }, 400);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while archiving the service.');
    });
}

// Handle form submission - wrapped in safe initialization
(function initFormHandler() {
    if (typeof jQuery === 'undefined') {
        setTimeout(initFormHandler, 100);
        return;
    }
    
    document.getElementById('serviceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = isEditMode ? '<?php echo BASE_URL; ?>admin/updateService' : '<?php echo BASE_URL; ?>admin/createService';
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Processing...';
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                jQuery('#serviceModal').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert('danger', data.message);
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while saving the service.');
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        });
    });
})();

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.cssText = 'min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" onclick="this.parentElement.remove()">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    alertContainer.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// ============================================
// ARCHIVED SERVICES MODAL
// ============================================

function loadArchivedServices() {
    const tbody = document.getElementById('archivedTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</td></tr>';

    fetch('<?php echo BASE_URL; ?>admin/getArchivedServices', {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.services.length > 0) {
            let rows = '';
            data.services.forEach(s => {
                const price = s.price ? 'PHP ' + parseFloat(s.price).toLocaleString('en-US', {minimumFractionDigits:2}) : '<span class="text-muted">Not Set</span>';
                const category = s.category_name ? s.category_name : '<span class="text-muted">—</span>';
                const serviceType = s.service_type ? s.service_type : '<span class="text-muted">—</span>';
                const created = new Date(s.created_at).toLocaleDateString('en-US', {month:'short', day:'2-digit', year:'numeric'});
                rows += `
                <tr>
                    <td>${s.id}</td>
                    <td><strong>${s.service_name}</strong></td>
                    <td>${category}</td>
                    <td>${serviceType}</td>
                    <td>${price}</td>
                    <td>${created}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="restoreService(${s.id}, '${s.service_name.replace(/'/g, "\\'")}')"
                            style="background:linear-gradient(135deg,#0F3C5F,#1a6fad);border:none;font-weight:600;">
                            <i class="fas fa-undo mr-1"></i>Restore
                        </button>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = rows;
        } else if (data.success && data.services.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-archive fa-3x mb-3 d-block" style="opacity:0.25;"></i>
                    <p>No archived services yet.</p>
                </td>
            </tr>`;
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Failed to load archived services.</td></tr>';
        }
    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Error loading data.</td></tr>';
    });
}

function restoreService(serviceId, serviceName) {
    if (!confirm('Restore "' + serviceName + '" back to active services?')) return;

    const formData = new FormData();
    formData.append('service_id', serviceId);

    fetch('<?php echo BASE_URL; ?>admin/restoreService', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // Refresh the archived table
            loadArchivedServices();
            // Also refresh main table after short delay
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(() => showAlert('danger', 'Error restoring service.'));
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

