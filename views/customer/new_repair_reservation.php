<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #654321;
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
    color: #654321;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d3e2;
    padding: 0.75rem 1rem;
    width: 100%;
}

/* Ensure select dropdowns show full text */
select.form-control {
    min-width: 100%;
    max-width: 100%;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    -webkit-appearance: menulist;
    -moz-appearance: menulist;
    appearance: menulist;
}

/* Prevent text ellipsis in select elements */
select.form-control {
    text-overflow: clip !important;
    overflow: visible !important;
}

/* Style for select options to show full text */
select.form-control option {
    padding: 10px 12px;
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    min-height: 30px;
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
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-submit:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    color: white;
}

.service-info-card {
    background: #faf8f5;
    border: 1px solid #e8dcc8;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1rem;
    display: none;
}

.store-info-card {
    background: #faf6f1;
    border: 1px solid #d4c5b0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1rem;
    border-left: 4px solid #8B4513;
}

/* Override Bootstrap primary colors with brown */
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

/* Additional styling for better dropdown display */
#service_category,
#service_type,
#store_location {
    font-size: 0.95rem;
    height: auto;
    min-height: 45px;
}

/* Ensure dropdown options are fully visible */
select#service_category option,
select#service_type option,
select#store_location option {
    padding: 10px;
    font-size: 0.95rem;
}

/* Make sure columns have proper spacing */
.form-group {
    margin-bottom: 1.5rem;
}

/* Custom brown badge styling */
.badge-brown {
    background-color: #8B4513;
    color: white;
}

/* Update form card border */
.form-card {
    border: 1px solid #e8dcc8;
}

/* Breadcrumb styling */
.breadcrumb-item a {
    color: #8B4513;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #654321;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #A0522D;
}

/* Responsive adjustments for mobile Android */
@media (max-width: 991.98px) {
    /* Form layout adjustments */
    .row {
        margin-left: -10px;
        margin-right: -10px;
    }
    
    .col-md-6, .col-md-8, .col-md-9, .col-lg-4, .col-lg-8 {
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 1rem;
    }
    
    /* Make form full width on mobile */
    .col-lg-8, .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Card adjustments */
    .form-card .card-body {
        padding: 1rem;
    }
    
    .form-card .card-header {
        padding: 1rem;
        font-size: 1rem;
    }
    
    /* Form controls - prevent zoom on Android */
    #service_category,
    #service_type,
    #store_location,
    .form-control,
    .form-select {
        font-size: 16px !important; /* Prevents zoom on Android/iOS */
        padding: 12px 15px;
        min-height: 48px; /* Touch-friendly */
    }
    
    select.form-control option {
        font-size: 1rem;
        padding: 12px;
    }
    
    /* Textareas */
    textarea.form-control {
        font-size: 16px;
        padding: 12px;
        min-height: 120px;
    }
    
    /* Buttons */
    .btn-submit,
    .btn {
        width: 100%;
        margin-bottom: 0.75rem;
        padding: 12px 20px;
        font-size: 1rem;
        min-height: 48px;
    }
    
    .btn:last-child {
        margin-bottom: 0;
    }
    
    /* Page heading */
    .page-title {
        font-size: 1.5rem;
    }
    
    .d-sm-flex.align-items-center.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    /* Store location section */
    .col-md-9.col-lg-9,
    .col-md-3.col-lg-3 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 0.75rem;
    }
    
    /* Service info card */
    .service-info-card,
    .store-info-card {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    
    .service-info-card .row,
    .store-info-card .row {
        flex-direction: column;
    }
    
    .service-info-card .col-md-6,
    .store-info-card .col-md-8,
    .store-info-card .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .store-info-card .col-md-4.text-right {
        text-align: left !important;
    }
    
    /* Form action buttons */
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .d-flex.justify-content-between.align-items-center > * {
        width: 100%;
    }
    
    /* Breadcrumb */
    .breadcrumb {
        font-size: 0.8rem;
        flex-wrap: wrap;
    }
}

/* Small mobile devices */
@media (max-width: 575.98px) {
    .page-title {
        font-size: 1.25rem;
    }
    
    .form-card .card-header h3,
    .form-card .card-header h5 {
        font-size: 1rem;
    }
    
    .form-label {
        font-size: 0.9rem;
    }
    
    #service_category,
    #service_type,
    #store_location,
    .form-control,
    .form-select {
        font-size: 16px !important;
        padding: 10px 12px;
    }
    
    .btn-submit,
    .btn {
        padding: 10px 16px;
        font-size: 0.95rem;
    }
    
    .service-info-card,
    .store-info-card {
        padding: 0.65rem;
        font-size: 0.85rem;
    }
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="page-title mb-2">Repair Reservation</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/bookings">Bookings</a></li>
                <li class="breadcrumb-item active">Repair Reservation</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Repair Reservation Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-tools mr-2"></i>Create Repair Reservation</h3>
            </div>
            <div class="card-body p-4">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>customer/processRepairReservation" id="repairReservationForm">
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
                            <div class="col-md-9 col-lg-9">
                                <select class="form-control" id="store_location" name="store_location_id" required>
                                    <option value="">Select a store location</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-3">
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
                                <span id="selectedStoreRating" class="badge badge-brown"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Item Type</label>
                                <input type="text" class="form-control" name="item_type" 
                                       placeholder="e.g., Car Seat, Sofa, Mattress">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Service Option <span class="text-danger">*</span></label>
                                <select class="form-control" name="service_option" id="service_option" required>
                                    <option value="">Select Option</option>
                                    <option value="pickup">Pick Up Item</option>
                                    <option value="delivery">Delivery Service</option>
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Choose whether you will bring the item to the store or request delivery service.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="pickupDateGroup">
                                <label class="form-label">Preferred Pickup Date</label>
                                <input type="date" class="form-control" name="pickup_date" id="pickup_date"
                                       min="<?php echo date('Y-m-d'); ?>">
                                <small class="form-text text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    Select when you prefer to bring the item to the store.
                                </small>
                            </div>
                            <div class="form-group" id="deliveryDateGroup" style="display: none;">
                                <label class="form-label">Preferred Delivery Date</label>
                                <input type="date" class="form-control" name="delivery_date" id="delivery_date"
                                       min="<?php echo date('Y-m-d'); ?>">
                                <small class="form-text text-muted">
                                    <i class="fas fa-truck"></i> 
                                    Select when you prefer to receive the item at your location.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="deliveryAddressGroup" style="display: none;">
                                <label class="form-label">Delivery Address</label>
                                <textarea class="form-control" name="delivery_address" id="delivery_address" rows="3"
                                          placeholder="Enter your complete delivery address..."></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Provide your complete address for delivery service.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Item Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="item_description" rows="5" 
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
                        <li>Store Location</li>
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

// Create a mapping of category to service types for fallback
const categoryServiceTypes = {};
services.forEach(service => {
    const categoryId = parseInt(service.category_id);
    if (!categoryServiceTypes[categoryId]) {
        categoryServiceTypes[categoryId] = new Set();
    }
    categoryServiceTypes[categoryId].add(service.service_type);
});

// Handle category change
document.getElementById('service_category').addEventListener('change', function() {
    const categoryId = this.value;
    const serviceTypeSelect = document.getElementById('service_type');
    
    // Clear and reset service type dropdown
    serviceTypeSelect.innerHTML = '<option value="">Select Type</option>';
    document.getElementById('serviceInfo').style.display = 'none';
    
    if (categoryId) {
        const categoryIdInt = parseInt(categoryId);
        
        if (categoryServiceTypes[categoryIdInt]) {
            categoryServiceTypes[categoryIdInt].forEach(serviceType => {
                const option = document.createElement('option');
                option.value = serviceType;
                option.textContent = serviceType;
                serviceTypeSelect.appendChild(option);
            });
        }
    }
});

// Handle service type change
document.getElementById('service_type').addEventListener('change', function() {
    const categoryId = document.getElementById('service_category').value;
    const serviceType = this.value;
    const serviceInfo = document.getElementById('serviceInfo');
    
    if (categoryId && serviceType) {
        const categoryIdInt = parseInt(categoryId);
        const matchingService = services.find(service => 
            parseInt(service.category_id) === categoryIdInt && service.service_type === serviceType
        );
        
        if (matchingService) {
            document.getElementById('servicePrice').textContent = '₱' + parseFloat(matchingService.price).toLocaleString('en-PH', {minimumFractionDigits: 2});
            document.getElementById('serviceDays').textContent = '7 days';
            document.getElementById('serviceDescription').textContent = matchingService.description;
            document.getElementById('total_amount').value = matchingService.price;
            
            serviceInfo.style.display = 'block';
        } else {
            serviceInfo.style.display = 'none';
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
    restoreFormData();
    setupAutoSave();
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
        const optionText = `${store.store_name} - ${store.city}`;
        option.textContent = optionText;
        option.title = optionText; // Add title for tooltip on hover
        storeSelect.appendChild(option);
    });
}

// Handle store selection change
document.getElementById('store_location').addEventListener('change', function() {
    const storeId = this.value;
    const storeInfo = document.getElementById('selectedStoreInfo');
    
    if (storeId) {
        const selectedStore = stores.find(store => store.id == storeId);
        if (selectedStore) {
            document.getElementById('selectedStoreName').textContent = selectedStore.store_name;
            document.getElementById('selectedStoreAddress').textContent = selectedStore.address + ', ' + selectedStore.city;
            document.getElementById('selectedStoreContact').textContent = `Phone: ${selectedStore.phone} | Email: ${selectedStore.email}`;
            const ratingBadge = document.getElementById('selectedStoreRating');
            ratingBadge.textContent = `★ ${selectedStore.rating}/5.0`;
            ratingBadge.className = 'badge badge-brown';
            
            storeInfo.style.display = 'block';
        }
    } else {
        storeInfo.style.display = 'none';
    }
});

// Open store locations page
function openStoreLocations() {
    // Save current form data to sessionStorage before opening store locations
    const formData = {
        service_category: document.getElementById('service_category').value,
        service_type: document.getElementById('service_type').value,
        service_option: document.getElementById('service_option').value,
        item_type: document.querySelector('[name="item_type"]').value,
        pickup_date: document.querySelector('[name="pickup_date"]').value,
        delivery_date: document.querySelector('[name="delivery_date"]').value,
        delivery_address: document.querySelector('[name="delivery_address"]').value,
        item_description: document.querySelector('[name="item_description"]').value,
        notes: document.querySelector('[name="notes"]').value,
        returnTo: 'repairReservation'
    };
    sessionStorage.setItem('repairReservationFormData', JSON.stringify(formData));
    
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

// Restore form data from sessionStorage
function restoreFormData() {
    // First, check if we're coming from the services catalog with a selected service
    const selectedServiceData = sessionStorage.getItem('selectedServiceData');
    if (selectedServiceData) {
        try {
            const serviceData = JSON.parse(selectedServiceData);
            
            // Pre-fill the service category
            if (serviceData.categoryId) {
                document.getElementById('service_category').value = serviceData.categoryId;
                document.getElementById('service_category').dispatchEvent(new Event('change'));
                
                // Wait for service types to load, then pre-fill service type
                setTimeout(function() {
                    if (serviceData.serviceType) {
                        document.getElementById('service_type').value = serviceData.serviceType;
                        document.getElementById('service_type').dispatchEvent(new Event('change'));
                    }
                }, 100);
            }
            
            // Clear the selected service data after using it
            sessionStorage.removeItem('selectedServiceData');
            return; // Don't load other saved form data if we have service data
        } catch (e) {
            console.error('Error loading selected service data:', e);
        }
    }
    
    // Otherwise, restore form data from previous session
    const savedData = sessionStorage.getItem('repairReservationFormData');
    if (savedData) {
        try {
            const formData = JSON.parse(savedData);
            
            // Restore service category
            if (formData.service_category) {
                document.getElementById('service_category').value = formData.service_category;
                document.getElementById('service_category').dispatchEvent(new Event('change'));
                
                // Wait a bit for service types to load, then restore service type
                setTimeout(function() {
                    if (formData.service_type) {
                        document.getElementById('service_type').value = formData.service_type;
                        document.getElementById('service_type').dispatchEvent(new Event('change'));
                    }
                }, 100);
            }
            
            // Restore service option
            if (formData.service_option) {
                document.getElementById('service_option').value = formData.service_option;
                document.getElementById('service_option').dispatchEvent(new Event('change'));
            }
            
            // Restore other form fields
            if (formData.item_type) {
                document.querySelector('[name="item_type"]').value = formData.item_type;
            }
            if (formData.pickup_date) {
                document.querySelector('[name="pickup_date"]').value = formData.pickup_date;
            }
            if (formData.delivery_date) {
                document.querySelector('[name="delivery_date"]').value = formData.delivery_date;
            }
            if (formData.delivery_address) {
                document.querySelector('[name="delivery_address"]').value = formData.delivery_address;
            }
            if (formData.item_description) {
                document.querySelector('[name="item_description"]').value = formData.item_description;
            }
            if (formData.notes) {
                document.querySelector('[name="notes"]').value = formData.notes;
            }
        } catch (e) {
            console.error('Error restoring form data:', e);
        }
    }
    
    // Check if a store was selected from store locations page
    const selectedStoreId = sessionStorage.getItem('selectedStoreId');
    if (selectedStoreId) {
        // Wait for stores to load
        const checkStores = setInterval(function() {
            if (stores.length > 0) {
                clearInterval(checkStores);
                document.getElementById('store_location').value = selectedStoreId;
                document.getElementById('store_location').dispatchEvent(new Event('change'));
                sessionStorage.removeItem('selectedStoreId');
            }
        }, 100);
    }
}

// Setup auto-save for form fields
function setupAutoSave() {
    // List of fields to auto-save
    const fieldsToSave = [
        'service_category',
        'service_type',
        '[name="item_type"]',
        '[name="pickup_date"]',
        '[name="item_description"]',
        '[name="notes"]'
    ];
    
    // Function to save current form state
    const saveFormState = function() {
        const formData = {
            service_category: document.getElementById('service_category').value,
            service_type: document.getElementById('service_type').value,
            service_option: document.getElementById('service_option').value,
            item_type: document.querySelector('[name="item_type"]').value,
            pickup_date: document.querySelector('[name="pickup_date"]').value,
            delivery_date: document.querySelector('[name="delivery_date"]').value,
            delivery_address: document.querySelector('[name="delivery_address"]').value,
            item_description: document.querySelector('[name="item_description"]').value,
            notes: document.querySelector('[name="notes"]').value,
            returnTo: 'repairReservation'
        };
        sessionStorage.setItem('repairReservationFormData', JSON.stringify(formData));
    };
    
    // Add event listeners to all fields
    document.getElementById('service_category').addEventListener('change', saveFormState);
    document.getElementById('service_type').addEventListener('change', saveFormState);
    document.getElementById('service_option').addEventListener('change', function() {
        saveFormState();
        // Trigger the change handler to show/hide fields
        this.dispatchEvent(new Event('change'));
    });
    document.querySelector('[name="item_type"]').addEventListener('input', saveFormState);
    document.querySelector('[name="pickup_date"]').addEventListener('change', saveFormState);
    document.querySelector('[name="delivery_date"]').addEventListener('change', saveFormState);
    document.querySelector('[name="delivery_address"]').addEventListener('input', saveFormState);
    document.querySelector('[name="item_description"]').addEventListener('input', saveFormState);
    document.querySelector('[name="notes"]').addEventListener('input', saveFormState);
}

// Handle service option change (Pickup or Delivery)
document.getElementById('service_option').addEventListener('change', function() {
    const serviceOption = this.value;
    const pickupDateGroup = document.getElementById('pickupDateGroup');
    const deliveryDateGroup = document.getElementById('deliveryDateGroup');
    const deliveryAddressGroup = document.getElementById('deliveryAddressGroup');
    const deliveryAddress = document.getElementById('delivery_address');
    const deliveryDate = document.getElementById('delivery_date');
    const pickupDate = document.getElementById('pickup_date');
    
    if (serviceOption === 'pickup') {
        // Show pickup date, hide delivery fields
        pickupDateGroup.style.display = 'block';
        deliveryDateGroup.style.display = 'none';
        deliveryAddressGroup.style.display = 'none';
        deliveryAddress.removeAttribute('required');
        deliveryDate.removeAttribute('required');
        pickupDate.removeAttribute('required');
    } else if (serviceOption === 'delivery') {
        // Show delivery date and address, hide pickup date
        pickupDateGroup.style.display = 'none';
        deliveryDateGroup.style.display = 'block';
        deliveryAddressGroup.style.display = 'block';
        deliveryAddress.setAttribute('required', 'required');
        deliveryDate.setAttribute('required', 'required');
        pickupDate.removeAttribute('required');
    } else {
        // Hide all if nothing selected
        pickupDateGroup.style.display = 'block';
        deliveryDateGroup.style.display = 'none';
        deliveryAddressGroup.style.display = 'none';
        deliveryAddress.removeAttribute('required');
        deliveryDate.removeAttribute('required');
        pickupDate.removeAttribute('required');
    }
});

// Form validation
document.getElementById('repairReservationForm').addEventListener('submit', function(e) {
    const categoryId = document.getElementById('service_category').value;
    const serviceType = document.getElementById('service_type').value;
    const storeLocation = document.getElementById('store_location').value;
    const description = document.querySelector('[name="item_description"]').value;
    const serviceOption = document.getElementById('service_option').value;
    
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
    
    if (!serviceOption) {
        e.preventDefault();
        alert('Please select a service option (Pick Up or Delivery)');
        return false;
    }
    
    if (!description.trim()) {
        e.preventDefault();
        alert('Please provide item description');
        return false;
    }
    
    // Validate delivery fields if delivery is selected
    if (serviceOption === 'delivery') {
        const deliveryAddress = document.getElementById('delivery_address').value.trim();
        const deliveryDate = document.getElementById('delivery_date').value;
        
        if (!deliveryAddress) {
            e.preventDefault();
            alert('Please provide delivery address');
            return false;
        }
        
        if (!deliveryDate) {
            e.preventDefault();
            alert('Please select a delivery date');
            return false;
        }
    }
    
    // If validation passes, clear saved form data
    sessionStorage.removeItem('repairReservationFormData');
    sessionStorage.removeItem('selectedStoreId');
    sessionStorage.removeItem('selectedServiceData');
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>


