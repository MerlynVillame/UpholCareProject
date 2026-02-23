<?php 
// Force no cache for this page
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; 
require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; 

// Store location is automatically assigned based on admin's shop location
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Leather Inventory Management</h1>
    <a href="#" class="btn btn-sm btn-primary-admin shadow-sm" data-toggle="modal" data-target="#addInventoryModal" style="display: inline-block !important; visibility: visible !important; opacity: 1 !important; z-index: 10 !important;">
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
                        <div class="text-xs font-weight-bold text-primary-admin text-uppercase mb-1">Total Colors</div>
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
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" id="inventorySearch" placeholder="Search by color name or code...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="qualityFilter">
                    <option value="all">All Quality Types</option>
                    <option value="standard">Standard</option>
                    <option value="premium">Premium</option>
                </select>
            </div>
            <div class="col-md-3">
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
        <h6 class="m-0 font-weight-bold text-primary-admin">Inventory List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="inventoryTable" width="100%" cellspacing="0" data-skip-datatable="true">
                <thead>
                    <tr>
                        <th>Color Code</th>
                        <th>Color Name</th>
                        <th>Color Preview</th>
                        <th>Leather Type</th>
                        <th>Price per Meter</th>
                        <th>Quantity (rolls)</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <tr>
                        <td colspan="9" class="text-center py-5">
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
<div class="modal fade" id="addInventoryModal" tabindex="-1" role="dialog" aria-labelledby="addInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInventoryModalLabel">Add Leather Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addInventoryForm" onsubmit="event.preventDefault(); submitInventoryForm(); return false;">
                    <div class="form-group">
                        <label for="colorName">Color Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="colorName" name="color_name" placeholder="e.g., Dark Brown" required>
                    </div>
                    <div class="form-group">
                        <label for="colorPicker">Color Preview</label>
                        <div class="input-group">
                            <input type="color" class="form-control" id="colorPicker" name="color" value="#1F4E79" style="height: 40px;">
                            <input type="text" class="form-control" id="colorHex" name="color_hex" value="#1F4E79" placeholder="#1F4E79" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="leatherType">Leather Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="leatherType" name="leather_type" required>
                            <option value="">Select type...</option>
                            <option value="standard">Standard</option>
                            <option value="premium">Premium</option>
                        </select>
                        <small class="form-text text-muted">Select Standard or Premium leather type</small>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity (rolls) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="pricePerMeter">Price per Meter (₱) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="pricePerMeter" name="price_per_meter" placeholder="0.00" step="0.01" min="0" required>
                        <small class="form-text text-muted">Price per meter for leather material</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 1.25rem 1.5rem !important; border-top: 1px solid #dee2e6 !important; background-color: #f8f9fa !important; visibility: visible !important; opacity: 1 !important; z-index: 10 !important;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="pointer-events: auto !important; cursor: pointer !important;">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary-admin" id="submitInventoryBtn" onclick="event.preventDefault(); submitInventoryForm(); return false;" style="pointer-events: auto !important; cursor: pointer !important; min-width: 120px;">
                    <i class="fas fa-plus mr-1"></i> Add Item
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   MODAL BRIGHTNESS FIX - ENSURE MODAL IS BRIGHT WHITE
   ============================================ */

/* Fix Modal Backdrop - Make it fully transparent (like Official Receipt modal) */
body .modal-backdrop,
body .modal-backdrop.show,
.modal-backdrop,
.modal-backdrop.show,
.modal-backdrop.fade,
.modal-backdrop.fade.show {
    opacity: 0 !important; /* Fully transparent - no dark overlay */
    background-color: transparent !important;
    background: transparent !important;
    z-index: 1040 !important;
    filter: none !important;
    pointer-events: none !important; /* Allow clicks through transparent backdrop */
}

/* FIX A - Remove stray modal-backdrop when modal is closed */
body:not(.modal-open) .modal-backdrop,
.modal:not(.show) ~ .modal-backdrop,
.modal-backdrop:not(.show) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

/* Fix Modal - Ensure it's bright white */
.modal {
    z-index: 1050 !important;
    opacity: 1 !important; /* ensure modal is not transparent */
}

.modal.show {
    z-index: 1050 !important;
    opacity: 1 !important; /* ensure modal is not transparent */
    display: block !important;
}

/* Fix Modal Content - Bright white background - HIGHEST SPECIFICITY */
body .modal.fade.show .modal-dialog .modal-content,
body .modal.show .modal-dialog .modal-content,
.modal.fade.show .modal-content,
.modal.show .modal-content,
#addInventoryModal .modal-content,
#addInventoryModal.fade.show .modal-content,
#addInventoryModal.show .modal-content,
.modal-content {
    background-color: #ffffff !important; /* bright white */
    background: #ffffff !important; /* bright white */
    opacity: 1 !important; /* ensure not transparent */
    backdrop-filter: none !important; /* remove any backdrop filter */
    -webkit-backdrop-filter: none !important;
    color: #212529 !important; /* ensure text is visible */
    filter: none !important;
}

/* Ensure modal is always on top */
#addInventoryModal {
    z-index: 1055 !important;
    position: fixed !important;
}

#addInventoryModal.show {
    display: block !important;
    z-index: 1055 !important;
    opacity: 1 !important;
}

/* Modal Header - Bright white */
#addInventoryModal .modal-header {
    background-color: #ffffff !important;
    border-bottom: 1px solid #dee2e6 !important;
    opacity: 1 !important;
}

/* Modal Body - Bright white */
#addInventoryModal .modal-body {
    background-color: #ffffff !important;
    opacity: 1 !important;
    color: #212529 !important;
}

/* Modal Footer - Light gray background */
#addInventoryModal .modal-footer {
    background-color: #f8f9fa !important;
    border-top: 1px solid #dee2e6 !important;
    opacity: 1 !important;
}

/* Modal dialog and content - MUST be clickable and bright */
#addInventoryModal .modal-dialog {
    z-index: 1056 !important;
    position: relative !important;
    pointer-events: auto !important;
    margin: 1.75rem auto !important;
    max-height: 90vh !important;
    display: flex !important;
    flex-direction: column !important;
    opacity: 1 !important;
}

#addInventoryModal .modal-content {
    display: flex !important;
    flex-direction: column !important;
    max-height: 90vh !important;
    overflow: hidden !important;
    background-color: #ffffff !important;
    opacity: 1 !important;
    color: #212529 !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

#addInventoryModal .modal-body {
    overflow-y: auto !important;
    background-color: #ffffff !important;
    opacity: 1 !important;
    color: #212529 !important;
}

/* Ensure all form elements are visible */
#addInventoryModal .form-control,
#addInventoryModal .form-group,
#addInventoryModal label,
#addInventoryModal input,
#addInventoryModal select,
#addInventoryModal textarea {
    color: #212529 !important;
    background-color: #ffffff !important;
    opacity: 1 !important;
}

#addInventoryModal .form-control:focus {
    background-color: #ffffff !important;
    border-color: #4e73df !important;
    color: #212529 !important;
}
    flex: 1 1 auto !important;
    max-height: calc(90vh - 200px) !important;
}

#addInventoryModal .modal-footer {
    display: flex !important;
    flex-shrink: 0 !important;
    visibility: visible !important;
    opacity: 1 !important;
    z-index: 10 !important;
    position: relative !important;
    padding: 1.25rem 1.5rem !important;
    border-top: 1px solid #dee2e6 !important;
    background-color: #f8f9fa !important;
}

#addInventoryModal .modal-content {
    z-index: 1057 !important;
    position: relative !important;
    pointer-events: auto !important;
    border-radius: 0.5rem !important;
    background-color: #ffffff !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Bright modal header */
#addInventoryModal .modal-header {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6 !important;
    padding: 1.25rem 1.5rem !important;
}

#addInventoryModal .modal-header .modal-title {
    color: #212529 !important;
    font-weight: 600 !important;
}

#addInventoryModal .modal-header .close {
    color: #6c757d !important;
    opacity: 0.8 !important;
}

#addInventoryModal .modal-header .close:hover {
    opacity: 1 !important;
    color: #212529 !important;
}

/* Bright modal body */
#addInventoryModal .modal-body {
    background-color: #ffffff !important;
    padding: 1.5rem !important;
    color: #212529 !important;
}

/* Form styling for better visibility */
#addInventoryModal .form-group label {
    color: #212529 !important;
    font-weight: 500 !important;
    margin-bottom: 0.5rem !important;
}

#addInventoryModal .form-control {
    background-color: #ffffff !important;
    border: 1px solid #ced4da !important;
    color: #212529 !important;
}

#addInventoryModal .form-control:focus {
    background-color: #ffffff !important;
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    color: #212529 !important;
}

#addInventoryModal .form-text {
    color: #6c757d !important;
}

/* All form elements MUST be clickable */
#addInventoryModal * {
    pointer-events: auto !important;
}

#addInventoryModal input,
#addInventoryModal select,
#addInventoryModal textarea,
#addInventoryModal button,
#addInventoryModal label {
    pointer-events: auto !important;
    cursor: default !important;
}

#addInventoryModal input[type="text"],
#addInventoryModal input[type="number"],
#addInventoryModal input[type="color"],
#addInventoryModal textarea {
    cursor: text !important;
}

#addInventoryModal input[type="color"] {
    cursor: pointer !important;
}

#addInventoryModal select {
    cursor: pointer !important;
}

#addInventoryModal button {
    cursor: pointer !important;
}

/* Lower z-index of other elements when modal is open */
body.modal-open .topbar,
body.modal-open .navbar,
body.modal-open .sidebar,
body.modal-open #accordionSidebar {
    z-index: 1 !important;
}

/* Ensure topbar/navbar doesn't block modal */
.navbar,
.topbar {
    z-index: 100 !important;
}

body.modal-open .navbar,
body.modal-open .topbar {
    z-index: 1 !important;
}

/* Hide logoutModal completely when addInventoryModal is open */
body.modal-open #logoutModal {
    display: none !important;
    visibility: hidden !important;
    pointer-events: none !important;
    z-index: -1 !important;
    opacity: 0 !important;
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden !important;
    padding-right: 0 !important;
}

/* Ensure "Add Leather Stock" button is always visible and not blocked */
.d-sm-flex .btn-primary[data-target="#addInventoryModal"],
a[data-target="#addInventoryModal"],
.btn[data-target="#addInventoryModal"] {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    z-index: 100 !important;
    position: relative !important;
    pointer-events: auto !important;
    cursor: pointer !important;
}

/* Ensure button is visible even when backdrop exists */
body.modal-open .btn-primary[data-target="#addInventoryModal"],
body.modal-open a[data-target="#addInventoryModal"] {
    z-index: 2000 !important;
    position: relative !important;
}

/* Ensure no container clips the modal */
body.modal-open .container-fluid,
body.modal-open .container,
body.modal-open #content-wrapper,
body.modal-open .content-wrapper {
    overflow: visible !important;
}
</style>

<script>
// Cache-busting version
console.log('Inventory page script loaded at:', new Date().toISOString());

let inventoryData = [];

function renderInventoryTable(data) {
    const tableBody = document.getElementById('inventoryTableBody');
    
    if (!tableBody) {
        console.error('Table body element not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No inventory data available yet</p>
                    <p class="text-muted">Click "Add Leather Stock" to start adding items</p>
                </td>
            </tr>
        `;
        console.log('Rendered empty state');
        return;
    }

    console.log('Rendering', data.length, 'inventory items');
    tableBody.innerHTML = data.map(item => {
        // Changed from badge-{color} to text-{color} for text-only display
        const statusClass = item.status === 'in-stock' ? 'text-success' : 
                           item.status === 'low-stock' ? 'text-warning' : 'text-danger';
        const statusText = item.status === 'in-stock' ? 'In Stock' : 
                          item.status === 'low-stock' ? 'Low Stock' : 'Out of Stock';
        
        // Determine leather type display (use fabric_type or type field)
        // Normalize to handle case variations
        const leatherTypeRaw = (item.type || item.fabric_type || item.leather_type || 'standard').toLowerCase().trim();
        let leatherType = 'Standard'; // Default
        if (leatherTypeRaw === 'premium') {
            leatherType = 'Premium';
        } else if (leatherTypeRaw === 'standard') {
            leatherType = 'Standard';
        } else {
            // If it's something else, capitalize first letter
            leatherType = leatherTypeRaw.charAt(0).toUpperCase() + leatherTypeRaw.slice(1);
        }
        const colorHex = item.color || item.color_hex || '#000000';
        const itemName = item.name || '';
        const itemCode = item.code || '';
        
        const pricePerMeter = parseFloat(item.price_per_meter || 0);
        
        return `
            <tr>
                <td><strong>${escapeHtml(itemCode)}</strong></td>
                <td>${escapeHtml(itemName)}</td>
                <td><div style="width: 40px; height: 40px; border-radius: 8px; border: 2px solid #e5e7eb; background-color: ${colorHex};"></div></td>
                <td>${escapeHtml(leatherType)}</td>
                <td>
                    <strong class="text-success" style="font-size: 1.1rem; font-weight: 700;">
                        ₱${pricePerMeter.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </strong>
                </td>
                <td>${parseFloat(item.quantity || 0).toFixed(2)} rolls</td>
                <td><span class="${statusClass}" style="font-weight: 600;">${statusText}</span></td>
                <td>${item.lastUpdated || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-info" title="Edit" onclick="editInventoryItem(${item.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
            `;
    }).join('');
    
    // Log rendering completion
    console.log('Table rendered with', data.length, 'items');
    
    // Verify the table was updated
    const rows = tableBody.querySelectorAll('tr');
    console.log('Table now has', rows.length, 'rows');
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Combined filter function that applies all filters
function applyAllFilters() {
    const searchTerm = (document.getElementById('inventorySearch')?.value || '').toLowerCase();
    const qualityFilter = document.getElementById('qualityFilter')?.value || 'all';
    const stockFilter = document.getElementById('stockFilter')?.value || 'all';
    
    console.log('applyAllFilters called:', {
        searchTerm,
        qualityFilter,
        stockFilter,
        totalItems: inventoryData.length
    });
    
    let filtered = inventoryData.filter(item => {
        // Search filter
        const matchesSearch = !searchTerm || 
            (item.name && item.name.toLowerCase().includes(searchTerm)) ||
            (item.code && item.code.toLowerCase().includes(searchTerm));
        
        if (!matchesSearch) return false;
        
        // Quality filter - CRITICAL for premium filtering
        if (qualityFilter !== 'all') {
            // Get item quality from multiple possible fields
            const itemQualityRaw = item.type || item.fabric_type || item.leather_type || 'standard';
            const itemQuality = String(itemQualityRaw).toLowerCase().trim();
            const normalizedQuality = String(qualityFilter).toLowerCase().trim();
            
            // Enhanced logging for premium filter debugging
            if (normalizedQuality === 'premium') {
                console.log(`[Premium Filter] Item: "${item.name || item.code}", Quality: "${itemQuality}", Match: ${itemQuality === normalizedQuality}`);
            }
            
            if (itemQuality !== normalizedQuality) {
                return false;
            }
        }
        
        // Stock level filter
        if (stockFilter !== 'all') {
            if (item.status !== stockFilter) {
                return false;
            }
        }
        
        return true;
    });
    
    console.log(`Filtering result: ${filtered.length} items shown (filter: ${qualityFilter})`);
    if (qualityFilter === 'premium') {
        console.log('Premium items displayed:', filtered.map(i => i.name || i.code));
    }
    
    renderInventoryTable(filtered);
}

// Search functionality
document.getElementById('inventorySearch')?.addEventListener('keyup', function() {
    applyAllFilters();
});

// Quality filter functionality
const qualityFilterEl = document.getElementById('qualityFilter');
if (qualityFilterEl) {
    qualityFilterEl.addEventListener('change', function() {
        const selectedValue = this.value;
        console.log('Quality filter changed to:', selectedValue);
        console.log('Current inventory data count:', inventoryData.length);
        applyAllFilters();
    });
    
    // Also trigger on initial load if a value is selected
    if (qualityFilterEl.value !== 'all') {
        console.log('Initial quality filter value:', qualityFilterEl.value);
    }
}

// Stock level filter functionality
document.getElementById('stockFilter')?.addEventListener('change', function() {
    applyAllFilters();
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

// Generate next color code
function generateNextColorCode() {
    if (inventoryData.length === 0) {
        return 'INV-001';
    }
    
    // Extract all numeric parts from existing color codes
    const codes = inventoryData
        .map(item => {
            const code = (item.code || '').toString();
            // Match pattern like "INV-001" or "BRN-123" or just numbers
            const match = code.match(/-(\d+)$/);
            return match ? parseInt(match[1], 10) : 0;
        })
        .filter(num => num > 0)
        .sort((a, b) => b - a);
    
    // Get the highest number
    const highestNumber = codes.length > 0 ? codes[0] : 0;
    
    // Generate next code
    const nextNumber = highestNumber + 1;
    return `INV-${String(nextNumber).padStart(3, '0')}`;
}

// Submit inventory form
function submitInventoryForm() {
    const form = document.getElementById('addInventoryForm');
    const submitBtn = document.getElementById('submitInventoryBtn');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Auto-generate color code automatically
    const colorCode = generateNextColorCode();
    
    // Get form values
    const leatherTypeSelect = document.getElementById('leatherType');
    const leatherTypeValue = leatherTypeSelect ? leatherTypeSelect.value : '';
    
    // Debug: Log the selected value
    console.log('DEBUG: leatherTypeSelect element:', leatherTypeSelect);
    console.log('DEBUG: leatherTypeSelect.value:', leatherTypeValue);
    console.log('DEBUG: leatherTypeSelect.selectedIndex:', leatherTypeSelect ? leatherTypeSelect.selectedIndex : 'N/A');
    if (leatherTypeSelect && leatherTypeSelect.options[leatherTypeSelect.selectedIndex]) {
        console.log('DEBUG: Selected option text:', leatherTypeSelect.options[leatherTypeSelect.selectedIndex].text);
        console.log('DEBUG: Selected option value:', leatherTypeSelect.options[leatherTypeSelect.selectedIndex].value);
    }
    
    const formData = {
        color_code: colorCode,
        color_name: document.getElementById('colorName').value.trim(),
        color: document.getElementById('colorPicker').value,
        color_hex: document.getElementById('colorHex').value,
        leather_type: leatherTypeValue, // Use the captured value
        quantity: parseFloat(document.getElementById('quantity').value),
        price_per_meter: parseFloat(document.getElementById('pricePerMeter')?.value || 0)
    };
    
    // Validate
    if (!formData.color_code || !formData.color_name) {
        alert('Please fill in color code and color name.');
        return;
    }
    
    // Validate leather type specifically
    if (!formData.leather_type || formData.leather_type === '') {
        alert('Please select a leather type (Standard or Premium).');
        if (leatherTypeSelect) {
            leatherTypeSelect.focus();
        }
        return;
    }
    
    // Ensure leather_type is lowercase
    formData.leather_type = formData.leather_type.toLowerCase().trim();
    
    if (formData.price_per_meter < 0) {
        alert('Price per meter cannot be negative.');
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
    const existingCode = inventoryData.find(item => item.code.toLowerCase() === formData.color_code.toLowerCase());
    if (existingCode) {
        alert('Color code already exists. Please use a different code.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        return;
    }
    
    // Check if color name already exists
    const existingName = inventoryData.find(item => item.name.toLowerCase() === formData.color_name.toLowerCase());
    if (existingName) {
        alert('This color name already exists. Please use a different color name.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        return;
    }
    
    
    // Save to database via AJAX
    const formDataToSend = new FormData();
    formDataToSend.append('color_code', formData.color_code);
    formDataToSend.append('color_name', formData.color_name);
    formDataToSend.append('color_hex', formData.color_hex);
    formDataToSend.append('leather_type', formData.leather_type); // This should be 'standard' or 'premium'
    formDataToSend.append('quantity', formData.quantity);
    formDataToSend.append('price_per_meter', formData.price_per_meter);
    
    // Debug: Log FormData contents
    console.log('DEBUG: FormData contents before sending:');
    for (let pair of formDataToSend.entries()) {
        console.log('  ' + pair[0] + ': ' + pair[1]);
    }
    
    console.log('Submitting inventory:', {
        color_code: formData.color_code,
        color_name: formData.color_name,
        leather_type: formData.leather_type,
        quantity: formData.quantity,
        price_per_meter: formData.price_per_meter
    });
    
    // Final validation - ensure leather_type is exactly 'standard' or 'premium'
    if (formData.leather_type !== 'standard' && formData.leather_type !== 'premium') {
        alert('Invalid leather type. Please select Standard or Premium.');
        console.error('ERROR: Invalid leather_type value:', formData.leather_type);
        return;
    }
    
    // Ensure we're using POST method - remove trailing slash if present
    let url = '<?php echo rtrim(BASE_URL, '/'); ?>/admin/createInventory';
    console.log('Sending POST request to:', url);
    console.log('FormData contents:', Array.from(formDataToSend.entries()));
    console.log('Request method will be: POST');
    
    // Use fetch with proper error handling
    // Note: Don't set Content-Type header when using FormData - browser will set it with boundary
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
            // Content-Type will be set automatically by browser for FormData
        },
        body: formDataToSend,
        credentials: 'same-origin',
        cache: 'no-store',
        redirect: 'follow' // Allow redirects but they should preserve method
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Expected JSON but got:', text.substring(0, 500));
                throw new Error('Server returned non-JSON response');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Reload inventory from database
            loadInventoryFromDatabase();
            
            // Reset form
            form.reset();
            document.getElementById('colorPicker').value = '#1F4E79';
            document.getElementById('colorHex').value = '#1F4E79';
            
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
        } else {
            alert('Error: ' + (data.message || 'Failed to add inventory item'));
        }
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding inventory item. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    });
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
    document.getElementById('colorName').value = item.name || '';
    const colorHex = item.color || item.color_hex || '#1F4E79';
    document.getElementById('colorPicker').value = colorHex;
    document.getElementById('colorHex').value = colorHex;
    // Set leather type - normalize to lowercase for comparison
    const itemLeatherType = (item.type || item.fabric_type || item.leather_type || '').toLowerCase().trim();
    const leatherTypeSelect = document.getElementById('leatherType');
    if (leatherTypeSelect) {
        // Find the option that matches (case-insensitive)
        for (let i = 0; i < leatherTypeSelect.options.length; i++) {
            if (leatherTypeSelect.options[i].value.toLowerCase() === itemLeatherType) {
                leatherTypeSelect.selectedIndex = i;
                break;
            }
        }
        // If no match found, default to first option (empty)
        if (leatherTypeSelect.value === '' && itemLeatherType) {
            console.warn('Leather type not found in options:', itemLeatherType);
        }
    }
    document.getElementById('quantity').value = item.quantity || 0;
    if (document.getElementById('pricePerMeter')) {
        document.getElementById('pricePerMeter').value = item.price_per_meter || 0;
    }
    
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
    
    // Get the item to preserve color_code
    const item = inventoryData.find(i => i.id === itemId);
    if (!item) {
        alert('Item not found.');
        return;
    }
    
    // Get form values
    const leatherTypeSelect = document.getElementById('leatherType');
    const leatherTypeValue = leatherTypeSelect ? leatherTypeSelect.value : '';
    
    // Ensure leather_type is lowercase
    const normalizedLeatherType = leatherTypeValue.toLowerCase().trim();
    
    // Validate leather type
    if (!normalizedLeatherType || (normalizedLeatherType !== 'standard' && normalizedLeatherType !== 'premium')) {
        alert('Please select a valid leather type (Standard or Premium).');
        if (leatherTypeSelect) {
            leatherTypeSelect.focus();
        }
        return;
    }
    
    const formData = {
        color_code: item.code || item.color_code || '',
        color_name: document.getElementById('colorName').value.trim(),
        color: document.getElementById('colorPicker').value,
        color_hex: document.getElementById('colorHex').value,
        leather_type: normalizedLeatherType, // Use normalized lowercase value
        quantity: parseFloat(document.getElementById('quantity').value),
        price_per_meter: parseFloat(document.getElementById('pricePerMeter')?.value || 0)
    };
    
    console.log('DEBUG updateInventoryItem - Form data:', formData);
    
    // Validate prices
    if (formData.price_per_meter < 0) {
        alert('Price per meter cannot be negative.');
        return;
    }
    
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
    
    // Update in database via AJAX
    const formDataToSend = new FormData();
    formDataToSend.append('id', itemId);
    formDataToSend.append('color_code', formData.color_code);
    formDataToSend.append('color_name', formData.color_name);
    formDataToSend.append('color_hex', formData.color_hex);
    formDataToSend.append('leather_type', formData.leather_type);
    formDataToSend.append('price_per_meter', formData.price_per_meter);
    formDataToSend.append('quantity', formData.quantity);
    
    // Ensure URL doesn't have trailing slash issues
    let url = '<?php echo rtrim(BASE_URL, '/'); ?>/admin/updateInventory';
    console.log('Updating inventory item:', itemId);
    console.log('Sending POST request to:', url);
    console.log('FormData contents:', Array.from(formDataToSend.entries()));
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
            // Content-Type will be set automatically by browser for FormData
        },
        body: formDataToSend,
        credentials: 'same-origin',
        cache: 'no-store',
        redirect: 'follow'
    })
    .then(response => {
        console.log('Update response status:', response.status);
        console.log('Update response headers:', response.headers);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Expected JSON but got:', text.substring(0, 500));
                throw new Error('Server returned non-JSON response');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Update response data:', data);
        if (data.success) {
            // Reload inventory from database
            loadInventoryFromDatabase();
            
            // Reset form and button
            form.reset();
            document.getElementById('colorPicker').value = '#1F4E79';
            document.getElementById('colorHex').value = '#1F4E79';
            submitBtn.innerHTML = '<i class="fas fa-plus mr-1"></i> Add Item';
            submitBtn.onclick = function() { submitInventoryForm(); };
            document.querySelector('#addInventoryModal .modal-title').textContent = 'Add Leather Stock';
            
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
            showSuccessMessage('Leather stock updated successfully!');
        } else {
            const errorMsg = data.message || 'Failed to update inventory item';
            console.error('Update failed:', errorMsg);
            alert('Error: ' + errorMsg);
        }
    })
    .catch(error => {
        console.error('Error updating inventory:', error);
        console.error('Error details:', error.message, error.stack);
        alert('Error updating inventory item: ' + error.message + '. Please check the console for details.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating inventory item. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    });
}

/* 
// Delete inventory item
function deleteInventoryItem(itemId, itemName) {
    if (!confirm(`Are you sure you want to delete "${itemName}"?\n\nThis action cannot be undone.`)) {
        return;
    }
    
    // Delete from database via AJAX
    const formData = new FormData();
    formData.append('id', itemId);
    
    fetch('<?php echo BASE_URL; ?>admin/deleteInventory', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload inventory from database
            loadInventoryFromDatabase();
            
            // Show success message (use message from server)
            showSuccessMessage(data.message || 'Inventory item processed successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to delete inventory item'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting inventory item. Please try again.');
    });
}
*/

// ============================================
// MODAL CLICKABILITY FIX - CLEAN & EFFECTIVE
// ============================================

function fixModalClickability() {
    const modal = document.getElementById('addInventoryModal');
    if (!modal) return;
    
    // Hide logoutModal when addInventoryModal opens
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
    
    // Ensure modal dialog and content are clickable
    const modalDialog = modal.querySelector('.modal-dialog');
    const modalContent = modal.querySelector('.modal-content');
    
    if (modalDialog) {
        modalDialog.style.cssText = 'z-index: 1056 !important; position: relative !important; pointer-events: auto !important; margin: 1.75rem auto !important; max-height: 90vh !important; overflow-y: auto !important;';
    }
    
    if (modalContent) {
        modalContent.style.cssText = 'z-index: 1057 !important; position: relative !important; pointer-events: auto !important;';
    }
    
    // Force all form elements to be clickable
    const formElements = modal.querySelectorAll('input, select, textarea, button, label');
    formElements.forEach(function(el) {
        el.style.pointerEvents = 'auto';
        el.style.cursor = el.tagName === 'BUTTON' || el.tagName === 'SELECT' || el.type === 'color' ? 'pointer' : 'text';
    });
    
    // Lower z-index of topbar and sidebar
    const topbar = document.querySelector('.topbar');
    const sidebar = document.querySelector('.sidebar, #accordionSidebar');
    if (topbar) topbar.style.zIndex = '1';
    if (sidebar) sidebar.style.zIndex = '1';
}

// Generate next color code
function generateNextColorCode() {
    if (inventoryData.length === 0) {
        return 'INV-001';
    }
    
    // Extract all numeric parts from existing color codes
    const codes = inventoryData
        .map(item => {
            const code = (item.code || '').toString();
            // Match pattern like "INV-001" or "BRN-123" or just numbers
            const match = code.match(/-(\d+)$/);
            return match ? parseInt(match[1], 10) : 0;
        })
        .filter(num => num > 0)
        .sort((a, b) => b - a);
    
    // Get the highest number
    const highestNumber = codes.length > 0 ? codes[0] : 0;
    
    // Generate next code
    const nextNumber = highestNumber + 1;
    return `INV-${String(nextNumber).padStart(3, '0')}`;
}

// Reset modal when closed
if (typeof jQuery !== 'undefined') {
    // Auto-generate color code when modal opens
    jQuery('#addInventoryModal').on('show.bs.modal', function() {
    });
    
    jQuery('#addInventoryModal').on('hidden.bs.modal', function() {
        // FIX B - Force Bootstrap to clean up the modal on close
        jQuery('body').removeClass('modal-open');
        jQuery('.modal-backdrop').remove();
        jQuery(this).removeClass('show');
        jQuery(this).css('display', 'none');
        jQuery(this).attr('aria-hidden', 'true');
        
        const form = document.getElementById('addInventoryForm');
        if (form) form.reset();
        
        const colorPicker = document.getElementById('colorPicker');
        const colorHex = document.getElementById('colorHex');
        if (colorPicker) colorPicker.value = '#1F4E79';
        if (colorHex) colorHex.value = '#1F4E79';
        
        const submitBtn = document.getElementById('submitInventoryBtn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-plus mr-1"></i> Add Item';
            submitBtn.onclick = function() { submitInventoryForm(); };
        }
        
        const modalTitle = document.querySelector('#addInventoryModal .modal-title');
        if (modalTitle) modalTitle.textContent = 'Add Leather Stock';
        
        // Restore logoutModal when inventory modal closes
        const logoutModal = document.getElementById('logoutModal');
        if (logoutModal) {
            logoutModal.style.removeProperty('display');
            logoutModal.style.removeProperty('visibility');
            logoutModal.style.removeProperty('pointer-events');
            logoutModal.style.removeProperty('z-index');
            logoutModal.style.removeProperty('opacity');
        }
        
        // Clean up brightness interval if exists
        if (this.dataset.brightnessInterval) {
            clearInterval(parseInt(this.dataset.brightnessInterval));
            delete this.dataset.brightnessInterval;
        }
    });
    
    // Fix modal when it's about to show
    jQuery('#addInventoryModal').on('show.bs.modal', function() {
        // FIX C - Remove aria-hidden to prevent warning
        jQuery(this).removeAttr('aria-hidden');
        jQuery(this).attr('aria-hidden', 'false');
        
        populateStoreDropdown();
        fixModalClickability();
    });
    
    // Force modal brightness when it opens - Use setProperty with important flag
    jQuery('#addInventoryModal').on('show.bs.modal', function() {
        const modal = this;
        const modalContent = modal.querySelector('.modal-content');
        const modalHeader = modal.querySelector('.modal-header');
        const modalBody = modal.querySelector('.modal-body');
        const modalFooter = modal.querySelector('.modal-footer');
        
        // Force bright white background immediately with setProperty for maximum priority
        if (modalContent) {
            modalContent.style.setProperty('background-color', '#ffffff', 'important');
            modalContent.style.setProperty('background', '#ffffff', 'important');
            modalContent.style.setProperty('opacity', '1', 'important');
            modalContent.style.setProperty('color', '#212529', 'important');
        }
        if (modalHeader) {
            modalHeader.style.setProperty('background-color', '#ffffff', 'important');
            modalHeader.style.setProperty('opacity', '1', 'important');
        }
        if (modalBody) {
            modalBody.style.setProperty('background-color', '#ffffff', 'important');
            modalBody.style.setProperty('opacity', '1', 'important');
            modalBody.style.setProperty('color', '#212529', 'important');
        }
        if (modalFooter) {
            modalFooter.style.setProperty('background-color', '#f8f9fa', 'important');
            modalFooter.style.setProperty('opacity', '1', 'important');
        }
        
        // Wait for backdrop to be created, then make it transparent
        setTimeout(function() {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.setProperty('background-color', 'transparent', 'important');
                backdrop.style.setProperty('opacity', '0', 'important');
                backdrop.style.setProperty('pointer-events', 'none', 'important');
            }
        }, 10);
    });
    
    // Fix modal when it's shown
    jQuery('#addInventoryModal').on('shown.bs.modal', function() {
        // Force brightness again after modal is fully shown
        const modal = this;
        const modalContent = modal.querySelector('.modal-content');
        const modalHeader = modal.querySelector('.modal-header');
        const modalBody = modal.querySelector('.modal-body');
        const modalFooter = modal.querySelector('.modal-footer');
        const backdrop = document.querySelector('.modal-backdrop');
        
        // Force bright white background
        if (modalContent) {
            modalContent.style.setProperty('background-color', '#ffffff', 'important');
            modalContent.style.setProperty('opacity', '1', 'important');
            modalContent.style.setProperty('color', '#212529', 'important');
        }
        if (modalHeader) {
            modalHeader.style.setProperty('background-color', '#ffffff', 'important');
            modalHeader.style.setProperty('opacity', '1', 'important');
        }
        if (modalBody) {
            modalBody.style.setProperty('background-color', '#ffffff', 'important');
            modalBody.style.setProperty('opacity', '1', 'important');
            modalBody.style.setProperty('color', '#212529', 'important');
        }
        if (modalFooter) {
            modalFooter.style.setProperty('background-color', '#f8f9fa', 'important');
            modalFooter.style.setProperty('opacity', '1', 'important');
        }
        if (backdrop) {
            backdrop.style.setProperty('background-color', 'transparent', 'important');
            backdrop.style.setProperty('opacity', '0', 'important');
            backdrop.style.setProperty('pointer-events', 'none', 'important');
        }
        
        // Continuous monitoring to force brightness while modal is open
        const brightnessInterval = setInterval(function() {
            if (!modal.classList.contains('show')) {
                clearInterval(brightnessInterval);
                return;
            }
            
            if (modalContent) {
                modalContent.style.setProperty('background-color', '#ffffff', 'important');
                modalContent.style.setProperty('background', '#ffffff', 'important');
                modalContent.style.setProperty('opacity', '1', 'important');
                modalContent.style.setProperty('color', '#212529', 'important');
            }
            if (modalHeader) {
                modalHeader.style.setProperty('background-color', '#ffffff', 'important');
                modalHeader.style.setProperty('opacity', '1', 'important');
            }
            if (modalBody) {
                modalBody.style.setProperty('background-color', '#ffffff', 'important');
                modalBody.style.setProperty('opacity', '1', 'important');
                modalBody.style.setProperty('color', '#212529', 'important');
            }
            if (modalFooter) {
                modalFooter.style.setProperty('background-color', '#f8f9fa', 'important');
                modalFooter.style.setProperty('opacity', '1', 'important');
            }
            if (backdrop) {
                backdrop.style.setProperty('background-color', 'transparent', 'important');
                backdrop.style.setProperty('opacity', '0', 'important');
                backdrop.style.setProperty('pointer-events', 'none', 'important');
            }
        }, 200);
        
        // Store interval for cleanup when modal closes
        modal.dataset.brightnessInterval = brightnessInterval;
        
        // Cleanup interval when modal is hidden
        jQuery(modal).on('hidden.bs.modal', function() {
            if (modal.dataset.brightnessInterval) {
                clearInterval(parseInt(modal.dataset.brightnessInterval));
                delete modal.dataset.brightnessInterval;
            }
        });
        
        fixModalClickability();
        
        // Additional fix after a short delay to ensure everything is applied
        setTimeout(function() {
            fixModalClickability();
            
            // Test if modal is actually clickable
            const testInput = modal.querySelector('#colorName');
            if (testInput) {
                try {
                    testInput.focus();
                    console.log('✅ Modal is clickable - input focused successfully');
                } catch(e) {
                    console.warn('⚠️ Modal clickability issue detected, applying additional fix...');
                    fixModalClickability();
                }
            }
        }, 100);
    });
} else {
    // Bootstrap 5 fallback
    const modalElement = document.getElementById('addInventoryModal');
    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function() {
            fixModalClickability();
        });
        
        modalElement.addEventListener('shown.bs.modal', function() {
            fixModalClickability();
            setTimeout(fixModalClickability, 100);
        });
    }
}

// Also fix on any modal backdrop click (prevent backdrop from blocking)
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('modal-backdrop')) {
        const backdrop = e.target;
        backdrop.style.pointerEvents = 'none';
    }
}, true);

// Prevent DataTable initialization on this table
// Store location is automatically assigned based on admin's shop location

document.addEventListener('DOMContentLoaded', function() {
    
    const inventoryTable = document.getElementById('inventoryTable');
    if (inventoryTable) {
        // Ensure the skip attribute is set
        inventoryTable.setAttribute('data-skip-datatable', 'true');
        inventoryTable.setAttribute('data-no-datatable', 'true');
        
        // If DataTable was already initialized, destroy it
        if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
            const $table = jQuery(inventoryTable);
            if (jQuery.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }
        }
    }
});

// Load inventory from database
function loadInventoryFromDatabase() {
    const url = '<?php echo BASE_URL; ?>admin/getInventory?t=' + new Date().getTime();
    console.log('Loading inventory from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        },
        cache: 'no-store'
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Check if response is actually JSON
        const contentType = response.headers.get('content-type') || '';
        console.log('Content-Type:', contentType);
        
        if (!contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Expected JSON but got HTML. First 500 chars:', text.substring(0, 500));
                console.error('Full response URL:', response.url);
                throw new Error('Server returned HTML instead of JSON. The endpoint might not exist or there\'s a PHP error.');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Inventory data received:', data);
        if (data.success) {
            inventoryData = data.data || [];
            console.log('Loaded', inventoryData.length, 'inventory items');
            console.log('Inventory items:', inventoryData);
            
            // Ensure table body exists
            const tableBody = document.getElementById('inventoryTableBody');
            if (!tableBody) {
                console.error('Table body element not found!');
                return;
            }
            
            // Debug: Log inventory data to verify structure
            console.log('DEBUG: Sample inventory item structure:', inventoryData.length > 0 ? inventoryData[0] : 'No items');
            console.log('DEBUG: All inventory items:', inventoryData.map(item => ({
                id: item.id,
                name: item.name,
                code: item.code,
                type: item.type,
                fabric_type: item.fabric_type,
                leather_type: item.leather_type
            })));
            
            // Apply all filters (search, quality, stock level) after loading data
            applyAllFilters();
            updateSummaryCards();
            
            // Verify table was rendered
            const rows = tableBody.querySelectorAll('tr');
            console.log('Table rendered with', rows.length, 'rows');
        } else {
            console.error('Error loading inventory:', data.message);
            inventoryData = [];
            applyAllFilters();
            updateSummaryCards();
        }
    })
    .catch(error => {
        console.error('Error loading inventory:', error);
        inventoryData = [];
        applyAllFilters();
        updateSummaryCards();
        
        // Show user-friendly error message
        const tableBody = document.getElementById('inventoryTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <p class="text-danger">Error loading inventory data</p>
                        <p class="text-muted small">${error.message || 'Please refresh the page or contact support'}</p>
                    </td>
                </tr>
            `;
        }
    });
}

// Initialize table - load from database
(function() {
    function initInventory() {
        console.log('Initializing inventory page...');
        loadInventoryFromDatabase();
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInventory);
    } else {
        // DOM already loaded
        initInventory();
    }
})();

// Additional safeguard - monitor and fix modal clickability continuously when open
(function() {
    let fixInterval = null;
    
    function startMonitoring() {
        if (fixInterval) return; // Already monitoring
        
        fixInterval = setInterval(function() {
            const modal = document.getElementById('addInventoryModal');
            if (modal && modal.classList.contains('show')) {
                // Modal is open, ensure it's clickable
                fixModalClickability();
            } else {
                // Modal is closed, stop monitoring
                if (fixInterval) {
                    clearInterval(fixInterval);
                    fixInterval = null;
                }
            }
        }, 200); // Check every 200ms
    }
    
    function stopMonitoring() {
        if (fixInterval) {
            clearInterval(fixInterval);
            fixInterval = null;
        }
    }
    
    // Start monitoring when modal opens
    if (typeof jQuery !== 'undefined') {
        jQuery('#addInventoryModal').on('show.bs.modal', startMonitoring);
        jQuery('#addInventoryModal').on('shown.bs.modal', startMonitoring);
        jQuery('#addInventoryModal').on('hidden.bs.modal', stopMonitoring);
    } else {
        const modal = document.getElementById('addInventoryModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', startMonitoring);
            modal.addEventListener('shown.bs.modal', startMonitoring);
            modal.addEventListener('hidden.bs.modal', stopMonitoring);
        }
    }
})();

// FIX - Remove stuck backdrops on page load and after modal close
(function() {
    function cleanupStuckModals() {
        // Remove any stuck modal backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.remove();
        });
        
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
        
        // Hide any modals that are stuck open (except the one that should be open)
        const modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            // Only hide if it has 'show' class but shouldn't be visible
            if (modal.classList.contains('show') && modal.style.display === 'none') {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    }
    
    // Clean up on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', cleanupStuckModals);
    } else {
        cleanupStuckModals();
    }
    
    // Clean up when any modal is hidden
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('hidden.bs.modal', '.modal', function() {
            jQuery('body').removeClass('modal-open');
            jQuery('.modal-backdrop').remove();
            jQuery(this).removeClass('show');
            jQuery(this).css('display', 'none');
            jQuery(this).attr('aria-hidden', 'true');
        });
    }
    
    // Also clean up when page becomes visible (in case modal was stuck)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            cleanupStuckModals();
        }
    });
    
    // Periodic cleanup check (every 2 seconds)
    setInterval(function() {
        // Only cleanup if no modal should be open
        const openModals = document.querySelectorAll('.modal.show');
        if (openModals.length === 0) {
            cleanupStuckModals();
        }
    }, 2000);
})();
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

