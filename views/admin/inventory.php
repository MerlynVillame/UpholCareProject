<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Leather Inventory Management</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addInventoryModal">
        <i class="fas fa-plus"></i> Add Leather Stock
    </a>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Colors</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalColors">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-palette fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStock">0 rolls</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="lowStock">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" id="inventorySearch" placeholder="Search by color name or code...">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-control" id="stockFilter">
                    <option value="all">All Stock Levels</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Inventory List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="inventoryTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Color Code</th>
                        <th>Color Name</th>
                        <th>Color Preview</th>
                        <th>Leather Type</th>
                        <th>Quantity (rolls)</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No inventory data available yet</p>
                            <p class="text-muted">Click "Add Leather Stock" to start adding items</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Inventory Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Leather Stock</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addInventoryForm">
                    <div class="form-group">
                        <label for="colorCode">Color Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="colorCode" name="color_code" placeholder="e.g., BRN-001" required>
                    </div>
                    <div class="form-group">
                        <label for="colorName">Color Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="colorName" name="color_name" placeholder="e.g., Dark Brown" required>
                    </div>
                    <div class="form-group">
                        <label for="colorPicker">Color Preview</label>
                        <div class="input-group">
                            <input type="color" class="form-control" id="colorPicker" name="color" value="#8B4513" style="height: 40px;">
                            <input type="text" class="form-control" id="colorHex" name="color_hex" value="#8B4513" placeholder="#8B4513" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="leatherType">Leather Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="leatherType" name="leather_type" required>
                            <option value="">Select type...</option>
                            <option value="Genuine Leather">Genuine Leather</option>
                            <option value="Faux Leather">Faux Leather</option>
                            <option value="Suede">Suede</option>
                            <option value="Nubuck">Nubuck</option>
                            <option value="Vinyl">Vinyl</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity (rolls) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitInventoryBtn" onclick="submitInventoryForm()">
                    <i class="fas fa-plus mr-1"></i> Add Item
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let inventoryData = [];

function renderInventoryTable(data) {
    const tableBody = document.getElementById('inventoryTableBody');
    
    if (data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No inventory data available yet</p>
                    <p class="text-muted">Click "Add Leather Stock" to start adding items</p>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = data.map(item => {
        const statusClass = item.status === 'in-stock' ? 'badge-success' : 
                           item.status === 'low-stock' ? 'badge-warning' : 'badge-danger';
        const statusText = item.status === 'in-stock' ? 'In Stock' : 
                          item.status === 'low-stock' ? 'Low Stock' : 'Out of Stock';
        
        return `
            <tr>
                <td><strong>${item.code}</strong></td>
                <td>${item.name}</td>
                <td><div style="width: 40px; height: 40px; border-radius: 8px; border: 2px solid #e5e7eb; background-color: ${item.color};"></div></td>
                <td>${item.type}</td>
                <td>${item.quantity} rolls</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>${item.lastUpdated}</td>
                <td>
                    <button class="btn btn-sm btn-info" title="Edit" onclick="editInventoryItem(${item.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" title="Delete" onclick="deleteInventoryItem(${item.id}, '${item.name}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Search functionality
document.getElementById('inventorySearch')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const filtered = inventoryData.filter(item =>
        item.name.toLowerCase().includes(searchTerm) ||
        item.code.toLowerCase().includes(searchTerm)
    );
    renderInventoryTable(filtered);
});

// Filter functionality
document.getElementById('stockFilter')?.addEventListener('change', function() {
    const filterValue = this.value;
    if (filterValue === 'all') {
        renderInventoryTable(inventoryData);
    } else {
        const filtered = inventoryData.filter(item => item.status === filterValue);
        renderInventoryTable(filtered);
    }
});

// Color picker sync
document.getElementById('colorPicker')?.addEventListener('input', function() {
    document.getElementById('colorHex').value = this.value;
});

document.getElementById('colorHex')?.addEventListener('input', function() {
    const colorValue = this.value;
    if (/^#[0-9A-F]{6}$/i.test(colorValue)) {
        document.getElementById('colorPicker').value = colorValue;
    }
});

// Submit inventory form
function submitInventoryForm() {
    const form = document.getElementById('addInventoryForm');
    const submitBtn = document.getElementById('submitInventoryBtn');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Get form values
    const formData = {
        color_code: document.getElementById('colorCode').value.trim(),
        color_name: document.getElementById('colorName').value.trim(),
        color: document.getElementById('colorPicker').value,
        leather_type: document.getElementById('leatherType').value,
        quantity: parseFloat(document.getElementById('quantity').value)
    };
    
    // Validate
    if (!formData.color_code || !formData.color_name || !formData.leather_type) {
        alert('Please fill in all required fields.');
        return;
    }
    
    if (formData.quantity < 0) {
        alert('Quantity cannot be negative.');
        return;
    }
    
    // Disable button and show loading
    const originalContent = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Adding...';
    
    // Check if color code already exists
    const existingItem = inventoryData.find(item => item.code.toLowerCase() === formData.color_code.toLowerCase());
    if (existingItem) {
        alert('Color code already exists. Please use a different code.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        return;
    }
    
    // Determine status based on quantity
    let status = 'in-stock';
    if (formData.quantity === 0) {
        status = 'out-of-stock';
    } else if (formData.quantity < 5) {
        status = 'low-stock';
    }
    
    // Create new inventory item
    const newItem = {
        id: Date.now(), // Temporary ID
        code: formData.color_code,
        name: formData.color_name,
        color: formData.color,
        type: formData.leather_type,
        quantity: formData.quantity,
        status: status,
        lastUpdated: new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })
    };
    
    // Add to inventory data
    inventoryData.push(newItem);
    
    // Update summary cards
    updateSummaryCards();
    
    // Re-render table
    renderInventoryTable(inventoryData);
    
    // Reset form
    form.reset();
    document.getElementById('colorPicker').value = '#8B4513';
    document.getElementById('colorHex').value = '#8B4513';
    
    // Close modal
    if (typeof jQuery !== 'undefined') {
        jQuery('#addInventoryModal').modal('hide');
    } else {
        const modal = document.getElementById('addInventoryModal');
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        }
    }
    
    // Show success message
    showSuccessMessage('Leather stock added successfully!');
    
    // Re-enable button
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalContent;
}

// Update summary cards
function updateSummaryCards() {
    const totalColors = inventoryData.length;
    const totalStock = inventoryData.reduce((sum, item) => sum + item.quantity, 0);
    const lowStock = inventoryData.filter(item => item.status === 'low-stock' || item.status === 'out-of-stock').length;
    
    document.getElementById('totalColors').textContent = totalColors;
    document.getElementById('totalStock').textContent = totalStock.toFixed(2) + ' rolls';
    document.getElementById('lowStock').textContent = lowStock;
}

// Show success message
function showSuccessMessage(message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '80px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        <i class="fas fa-check-circle mr-2"></i>${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Edit inventory item
function editInventoryItem(itemId) {
    const item = inventoryData.find(i => i.id === itemId);
    if (!item) {
        alert('Item not found.');
        return;
    }
    
    // Populate form with item data
    document.getElementById('colorCode').value = item.code;
    document.getElementById('colorName').value = item.name;
    document.getElementById('colorPicker').value = item.color;
    document.getElementById('colorHex').value = item.color;
    document.getElementById('leatherType').value = item.type;
    document.getElementById('quantity').value = item.quantity;
    
    // Change button text
    const submitBtn = document.getElementById('submitInventoryBtn');
    submitBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Update Item';
    submitBtn.onclick = function() { updateInventoryItem(itemId); };
    
    // Change modal title
    document.querySelector('#addInventoryModal .modal-title').textContent = 'Edit Leather Stock';
    
    // Open modal
    if (typeof jQuery !== 'undefined') {
        jQuery('#addInventoryModal').modal('show');
    }
}

// Update inventory item
function updateInventoryItem(itemId) {
    const form = document.getElementById('addInventoryForm');
    const submitBtn = document.getElementById('submitInventoryBtn');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Get form values
    const formData = {
        color_code: document.getElementById('colorCode').value.trim(),
        color_name: document.getElementById('colorName').value.trim(),
        color: document.getElementById('colorPicker').value,
        leather_type: document.getElementById('leatherType').value,
        quantity: parseFloat(document.getElementById('quantity').value)
    };
    
    // Find item index
    const itemIndex = inventoryData.findIndex(i => i.id === itemId);
    if (itemIndex === -1) {
        alert('Item not found.');
        return;
    }
    
    // Check if color code already exists (excluding current item)
    const existingItem = inventoryData.find(item => 
        item.code.toLowerCase() === formData.color_code.toLowerCase() && item.id !== itemId
    );
    if (existingItem) {
        alert('Color code already exists. Please use a different code.');
        return;
    }
    
    // Disable button and show loading
    const originalContent = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';
    
    // Determine status based on quantity
    let status = 'in-stock';
    if (formData.quantity === 0) {
        status = 'out-of-stock';
    } else if (formData.quantity < 5) {
        status = 'low-stock';
    }
    
    // Update item
    inventoryData[itemIndex] = {
        ...inventoryData[itemIndex],
        code: formData.color_code,
        name: formData.color_name,
        color: formData.color,
        type: formData.leather_type,
        quantity: formData.quantity,
        status: status,
        lastUpdated: new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })
    };
    
    // Update summary cards
    updateSummaryCards();
    
    // Re-render table
    renderInventoryTable(inventoryData);
    
    // Reset form and button
    form.reset();
    document.getElementById('colorPicker').value = '#8B4513';
    document.getElementById('colorHex').value = '#8B4513';
    submitBtn.innerHTML = '<i class="fas fa-plus mr-1"></i> Add Item';
    submitBtn.onclick = function() { submitInventoryForm(); };
    document.querySelector('#addInventoryModal .modal-title').textContent = 'Add Leather Stock';
    
    // Close modal
    if (typeof jQuery !== 'undefined') {
        jQuery('#addInventoryModal').modal('hide');
    }
    
    // Show success message
    showSuccessMessage('Leather stock updated successfully!');
    
    // Re-enable button
    submitBtn.disabled = false;
}

// Delete inventory item
function deleteInventoryItem(itemId, itemName) {
    if (!confirm(`Are you sure you want to delete "${itemName}"?\n\nThis action cannot be undone.`)) {
        return;
    }
    
    // Remove item from array
    const itemIndex = inventoryData.findIndex(i => i.id === itemId);
    if (itemIndex === -1) {
        alert('Item not found.');
        return;
    }
    
    inventoryData.splice(itemIndex, 1);
    
    // Update summary cards
    updateSummaryCards();
    
    // Re-render table
    renderInventoryTable(inventoryData);
    
    // Show success message
    showSuccessMessage('Leather stock deleted successfully!');
}

// Reset modal when closed
if (typeof jQuery !== 'undefined') {
    jQuery('#addInventoryModal').on('hidden.bs.modal', function() {
        const form = document.getElementById('addInventoryForm');
        form.reset();
        document.getElementById('colorPicker').value = '#8B4513';
        document.getElementById('colorHex').value = '#8B4513';
        const submitBtn = document.getElementById('submitInventoryBtn');
        submitBtn.innerHTML = '<i class="fas fa-plus mr-1"></i> Add Item';
        submitBtn.onclick = function() { submitInventoryForm(); };
        document.querySelector('#addInventoryModal .modal-title').textContent = 'Add Leather Stock';
    });
}

// Initialize table
renderInventoryTable(inventoryData);
updateSummaryCards();
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
