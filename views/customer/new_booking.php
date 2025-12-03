<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
}

.form-card {
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

.form-card .card-header {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    color: white;
    border-radius: 0.75rem 0.75rem 0 0 !important;
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d3e2;
    padding: 0.75rem 1rem;
}

.form-control:focus {
    border-color: #8B4513;
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
}

.btn-submit {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 0.5rem;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(139, 69, 19, 0.2);
}

.btn-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    color: white;
}

.service-info-card {
    background: #f8f9fc;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1rem;
    display: none;
}

.store-info-card {
    background: #e8f5e8;
    border: 1px solid #c3e6c3;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1rem;
    border-left: 4px solid #28a745;
}

/* Override Bootstrap primary colors with brown */
.btn-primary {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
    border-color: #A0522D !important;
    color: white !important;
}

.btn-outline-primary {
    color: #8B4513 !important;
    border-color: #8B4513 !important;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.text-primary {
    color: #8B4513 !important;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="page-title mb-2">New Booking</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/bookings">Bookings</a></li>
                <li class="breadcrumb-item active">New Booking</li>
            </ol>
        </nav>
    </div>
</div>

<!-- New Booking Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-plus-circle mr-2"></i>Create New Booking</h3>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?php echo BASE_URL; ?>customer/processBooking" id="bookingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Service Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="service_category" name="service_category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Service Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="service_type" name="service_type" required>
                                    <option value="">Select Type</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="serviceInfo" class="service-info-card">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Base Price:</strong> <span id="servicePrice" class="text-primary">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Estimated Days:</strong> <span id="serviceDays">-</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Description:</strong>
                            <p id="serviceDescription" class="mb-0 text-muted small"></p>
                        </div>
                    </div>

                    <!-- Store Selection Section -->
                    <div class="form-group">
                        <label class="form-label">Preferred Store Location <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-8">
                                <select class="form-control" id="store_location" name="store_location_id" required>
                                    <option value="">Select a store location</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-primary btn-block" onclick="openStoreLocations()">
                                    <i class="fas fa-map-marker-alt"></i> Find Stores
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Choose the nearest store for your service. You can find stores using the map.
                        </small>
                    </div>

                    <div id="selectedStoreInfo" class="store-info-card" style="display: none;">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 id="selectedStoreName" class="mb-1"></h6>
                                <p id="selectedStoreAddress" class="mb-1 text-muted small"></p>
                                <p id="selectedStoreContact" class="mb-0 text-muted small"></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span id="selectedStoreRating" class="badge badge-warning"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Preferred Pickup Date</label>
                                <input type="date" class="form-control" name="pickup_date" 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Color Selection Section -->
                    <div class="form-group" id="colorSelectionSection" style="display: none;">
                        <label class="form-label">Fabric/Color Selection <span class="text-danger">*</span></label>
                        <small class="form-text text-muted mb-2 d-block">
                            <i class="fas fa-info-circle"></i> 
                            Select a color based on availability at your chosen store. Premium colors have additional cost.
                        </small>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <select class="form-control" id="selected_color" name="selected_color_id" required>
                                    <option value="">Select Color...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="color_type" name="color_type" required>
                                    <option value="standard">Standard</option>
                                    <option value="premium">Premium (+₱<span id="premiumPriceDisplay">0.00</span>)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div id="colorPreview" class="mt-3" style="display: none;">
                            <div class="card" style="border: 2px solid #e3e6f0;">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div id="colorSwatch" style="width: 60px; height: 60px; border-radius: 8px; border: 2px solid #ddd; background-color: #ccc;"></div>
                                        </div>
                                        <div class="col-md-10">
                                            <h6 class="mb-1" id="colorNameDisplay">-</h6>
                                            <p class="mb-1 text-muted small" id="colorCodeDisplay">-</p>
                                            <p class="mb-0">
                                                <strong>Price:</strong> 
                                                <span class="text-primary" id="colorPriceDisplay">₱0.00</span>
                                                <span id="colorTypeBadge" class="badge badge-secondary ml-2">Standard</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Item Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="item_description" rows="4" 
                                  placeholder="Please provide detailed description of the item and the repair needed..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Additional Notes</label>
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="Any special instructions or requests..."></textarea>
                    </div>

                    <input type="hidden" name="total_amount" id="total_amount" value="0">

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?php echo BASE_URL; ?>customer/bookings" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-check mr-2"></i>Create Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card form-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Booking Information</h5>
            </div>
            <div class="card-body">
                <p class="small text-white-50 mb-3">Please fill out all required fields to create your booking.</p>
                <div class="mb-3">
                    <strong class="text-white">Required Information:</strong>
                    <ul class="small text-white-50 mt-2">
                        <li>Service Category</li>
                        <li>Service Type</li>
                        <li>Item Description</li>
                    </ul>
                </div>
                <div class="alert alert-light mb-0">
                    <small>
                        <i class="fas fa-lightbulb text-warning mr-1"></i>
                        <strong>Tip:</strong> Provide detailed description for better service assessment.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<script>
// Service data
const services = <?php echo json_encode($services); ?>;
console.log('Services data:', services);

// Create a mapping of category to service types for fallback
const categoryServiceTypes = {};
services.forEach(service => {
    const categoryId = parseInt(service.category_id);
    if (!categoryServiceTypes[categoryId]) {
        categoryServiceTypes[categoryId] = new Set();
    }
    categoryServiceTypes[categoryId].add(service.service_type);
});

console.log('Category service types mapping:', categoryServiceTypes);

// Handle category change
document.getElementById('service_category').addEventListener('change', function() {
    const categoryId = this.value;
    const serviceTypeSelect = document.getElementById('service_type');
    
    // Clear and reset service type dropdown
    serviceTypeSelect.innerHTML = '<option value="">Select Type</option>';
    document.getElementById('serviceInfo').style.display = 'none';
    
    if (categoryId) {
        // Use local data directly since AJAX requires authentication
        const categoryIdInt = parseInt(categoryId);
        console.log('Loading service types for category:', categoryIdInt);
        
        if (categoryServiceTypes[categoryIdInt]) {
            categoryServiceTypes[categoryIdInt].forEach(serviceType => {
                const option = document.createElement('option');
                option.value = serviceType;
                option.textContent = serviceType;
                serviceTypeSelect.appendChild(option);
            });
            console.log('Added', categoryServiceTypes[categoryIdInt].size, 'service types to dropdown');
        } else {
            console.log('No service types found for category:', categoryIdInt);
        }
    }
});

// Handle service type change - this is now the final selection
document.getElementById('service_type').addEventListener('change', function() {
    const categoryId = document.getElementById('service_category').value;
    const serviceType = this.value;
    const serviceInfo = document.getElementById('serviceInfo');
    
    if (categoryId && serviceType) {
        // Find the first service that matches the category and service type
        const categoryIdInt = parseInt(categoryId);
        const matchingService = services.find(service => 
            parseInt(service.category_id) === categoryIdInt && service.service_type === serviceType
        );
        
        if (matchingService) {
            document.getElementById('servicePrice').textContent = '₱' + parseFloat(matchingService.price).toLocaleString('en-PH', {minimumFractionDigits: 2});
            document.getElementById('serviceDays').textContent = '7 days'; // Default since this DB doesn't have estimated_days
            document.getElementById('serviceDescription').textContent = matchingService.description;
            document.getElementById('total_amount').value = matchingService.price;
            
            serviceInfo.style.display = 'block';
            console.log('Service info loaded for:', serviceType);
        } else {
            serviceInfo.style.display = 'none';
            console.log('No matching service found for:', serviceType);
        }
    } else {
        serviceInfo.style.display = 'none';
    }
});

// Store selection functionality
let stores = [];

// Load stores when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadStores();
});

// Load all stores
function loadStores() {
    fetch('<?php echo BASE_URL; ?>customer/getStoreLocations')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            stores = data.data;
            populateStoreDropdown();
        } else {
            console.error('Error loading stores:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading stores:', error);
    });
}

// Populate store dropdown
function populateStoreDropdown() {
    const storeSelect = document.getElementById('store_location');
    storeSelect.innerHTML = '<option value="">Select a store location</option>';
    
    stores.forEach(store => {
        const option = document.createElement('option');
        option.value = store.id;
        option.textContent = `${store.store_name} - ${store.city}`;
        storeSelect.appendChild(option);
    });
}

// Handle store selection change
document.getElementById('store_location').addEventListener('change', function() {
    const storeId = this.value;
    const storeInfo = document.getElementById('selectedStoreInfo');
    const colorSection = document.getElementById('colorSelectionSection');
    
    if (storeId) {
        const selectedStore = stores.find(store => store.id == storeId);
        if (selectedStore) {
            document.getElementById('selectedStoreName').textContent = selectedStore.store_name;
            document.getElementById('selectedStoreAddress').textContent = selectedStore.address + ', ' + selectedStore.city;
            document.getElementById('selectedStoreContact').textContent = `Phone: ${selectedStore.phone} | Email: ${selectedStore.email}`;
            document.getElementById('selectedStoreRating').textContent = `★ ${selectedStore.rating}/5.0`;
            
            storeInfo.style.display = 'block';
            
            // Load available colors for this store
            loadAvailableColors(storeId);
            colorSection.style.display = 'block';
        }
    } else {
        storeInfo.style.display = 'none';
        colorSection.style.display = 'none';
    }
});

// Load available colors for selected store
let availableColors = [];
function loadAvailableColors(storeId) {
    fetch(`<?php echo BASE_URL; ?>customer/getAvailableColors?store_id=${storeId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            availableColors = data.colors;
            populateColorDropdown();
        } else {
            console.error('Error loading colors:', data.message);
            availableColors = [];
            populateColorDropdown();
        }
    })
    .catch(error => {
        console.error('Error loading colors:', error);
        availableColors = [];
        populateColorDropdown();
    });
}

// Populate color dropdown
function populateColorDropdown() {
    const colorSelect = document.getElementById('selected_color');
    colorSelect.innerHTML = '<option value="">Select Color...</option>';
    
    if (availableColors.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No colors available for this store';
        option.disabled = true;
        colorSelect.appendChild(option);
        return;
    }
    
    availableColors.forEach(color => {
        const option = document.createElement('option');
        option.value = color.id;
        option.textContent = `${color.color_name} (${color.color_code}) - ${color.fabric_type === 'premium' ? 'Premium' : 'Standard'}`;
        option.dataset.colorName = color.color_name;
        option.dataset.colorCode = color.color_code;
        option.dataset.colorHex = color.color_hex;
        option.dataset.basePrice = color.price_per_unit;
        option.dataset.premiumPrice = color.premium_price;
        colorSelect.appendChild(option);
    });
}

// Handle color selection change
document.getElementById('selected_color').addEventListener('change', function() {
    const colorId = this.value;
    const colorPreview = document.getElementById('colorPreview');
    const selectedOption = this.options[this.selectedIndex];
    
    if (colorId && selectedOption.dataset.colorName) {
        document.getElementById('colorSwatch').style.backgroundColor = selectedOption.dataset.colorHex;
        document.getElementById('colorNameDisplay').textContent = selectedOption.dataset.colorName;
        document.getElementById('colorCodeDisplay').textContent = `Code: ${selectedOption.dataset.colorCode}`;
        
        updateColorPrice();
        colorPreview.style.display = 'block';
    } else {
        colorPreview.style.display = 'none';
    }
});

// Handle color type change (premium/standard)
document.getElementById('color_type').addEventListener('change', function() {
    updateColorPrice();
    updatePremiumPriceDisplay();
});

// Update color price display
function updateColorPrice() {
    const colorSelect = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type').value;
    const selectedOption = colorSelect.options[colorSelect.selectedIndex];
    
    if (!colorSelect.value || !selectedOption.dataset.basePrice) {
        document.getElementById('colorPriceDisplay').textContent = '₱0.00';
        return;
    }
    
    const basePrice = parseFloat(selectedOption.dataset.basePrice || 0);
    let totalPrice = basePrice;
    
    if (colorType === 'premium') {
        const premiumPrice = parseFloat(selectedOption.dataset.premiumPrice || 0);
        totalPrice = basePrice + premiumPrice;
    }
    
    document.getElementById('colorPriceDisplay').textContent = '₱' + totalPrice.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    // Update badge
    const badge = document.getElementById('colorTypeBadge');
    if (colorType === 'premium') {
        badge.textContent = 'Premium';
        badge.className = 'badge badge-warning ml-2';
    } else {
        badge.textContent = 'Standard';
        badge.className = 'badge badge-secondary ml-2';
    }
    
    // Update total amount
    updateTotalAmount();
}

// Update premium price display
function updatePremiumPriceDisplay() {
    const colorSelect = document.getElementById('selected_color');
    const selectedOption = colorSelect.options[colorSelect.selectedIndex];
    
    if (selectedOption.dataset.premiumPrice) {
        const premiumPrice = parseFloat(selectedOption.dataset.premiumPrice || 0);
        document.getElementById('premiumPriceDisplay').textContent = premiumPrice.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    } else {
        document.getElementById('premiumPriceDisplay').textContent = '0.00';
    }
}

// Update total amount including color price
function updateTotalAmount() {
    const servicePrice = parseFloat(document.getElementById('total_amount').value || 0);
    const colorSelect = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type').value;
    const selectedOption = colorSelect.options[colorSelect.selectedIndex];
    
    let colorPrice = 0;
    if (colorSelect.value && selectedOption.dataset.basePrice) {
        const basePrice = parseFloat(selectedOption.dataset.basePrice || 0);
        if (colorType === 'premium') {
            const premiumPrice = parseFloat(selectedOption.dataset.premiumPrice || 0);
            colorPrice = basePrice + premiumPrice;
        } else {
            colorPrice = basePrice;
        }
    }
    
    const totalAmount = servicePrice + colorPrice;
    document.getElementById('total_amount').value = totalAmount.toFixed(2);
}

// Open store locations page
function openStoreLocations() {
    window.open('<?php echo BASE_URL; ?>customer/storeLocations', '_blank');
}

// Handle store selection from store locations page
window.addEventListener('message', function(event) {
    if (event.data.type === 'storeSelected') {
        const storeId = event.data.storeId;
        document.getElementById('store_location').value = storeId;
        document.getElementById('store_location').dispatchEvent(new Event('change'));
    }
});

// Form validation
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const categoryId = document.getElementById('service_category').value;
    const serviceType = document.getElementById('service_type').value;
    const storeLocation = document.getElementById('store_location').value;
    const description = document.querySelector('[name="item_description"]').value;
    
    if (!categoryId) {
        e.preventDefault();
        alert('Please select a service category');
        return false;
    }
    
    if (!serviceType) {
        e.preventDefault();
        alert('Please select a service type');
        return false;
    }
    
    if (!storeLocation) {
        e.preventDefault();
        alert('Please select a store location');
        return false;
    }
    
    if (!description.trim()) {
        e.preventDefault();
        alert('Please provide item description');
        return false;
    }
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

