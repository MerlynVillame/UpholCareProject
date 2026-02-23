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

.form-card {
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

.form-card .card-header {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
    color: white;
    border-radius: 0.75rem 0.75rem 0 0 !important;
    padding: 1rem 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #0F3C5F;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d3e2;
    padding: 0.75rem 1rem;
    width: 100%;
    box-sizing: border-box;
}

/* Ensure ALL select dropdowns show full text - match Service Category exactly */
select.form-control {
    width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
    -webkit-appearance: menulist;
    -moz-appearance: menulist;
    appearance: menulist;
    box-sizing: border-box !important;
    font-size: 0.95rem !important;
    padding: 0.75rem 2.5rem 0.75rem 1rem !important; /* Extra right padding for arrow */
    min-height: 45px !important;
    height: auto !important;
    line-height: 1.5 !important;
}

/* Style for select options to show full text */
select.form-control option {
    padding: 10px 12px;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    min-height: 30px;
    display: block;
}

/* Fix for ALL dropdowns - match Service Category styling exactly */
#service_option,
#selected_color,
#color_type,
#service_category,
#service_type,
#store_location {
    width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    overflow: visible !important;
    text-overflow: clip !important;
    white-space: normal !important;
    font-size: 0.95rem !important; /* Match Service Category */
    line-height: 1.5 !important;
    padding: 0.75rem 2.5rem 0.75rem 1rem !important; /* Extra right padding for arrow */
    min-height: 45px !important; /* Match Service Category */
    height: auto !important;
}

/* Fix dropdown options to show full text - ALL dropdowns match Service Category */
#service_option option,
#selected_color option,
#color_type option,
#service_category option,
#service_type option,
#store_location option {
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    padding: 10px 12px !important;
    display: block !important;
    width: 100% !important;
    font-size: 0.95rem !important;
    min-height: 30px !important;
}

/* Fix overflow issues that cut off dropdowns */
.form-card,
.form-card .card-body,
.form-card .card-body .row,
.form-card .card-body .form-group {
    overflow: visible !important;
    position: relative;
}

/* Ensure dropdowns can display outside their containers */
.form-card .card-body {
    overflow: visible !important;
    position: relative;
    z-index: 1;
}

/* Main content area - must be above topbar but not overlap it */
.main-content,
.form-section,
.card,
.card-body {
    position: relative !important;
    z-index: 1 !important; /* Lower than topbar to prevent overlap */
    overflow: visible !important;
}

/* Fix z-index for dropdowns - ensure they don't overlap topbar */
select.form-control {
    position: relative;
    z-index: 1; /* Low z-index when not active */
}

/* When dropdown is focused/active, raise z-index but keep it below topbar */
select.form-control:focus,
select.form-control:active {
    position: relative;
    z-index: 998 !important; /* Just below topbar (999) to prevent overlap */
}

/* Ensure dropdown options are fully visible */
select.form-control option {
    position: relative;
    z-index: 999 !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* Specific fix for service option dropdown - ensure full text visible like Service Category */
.service-option-select,
#service_option {
    position: relative;
    z-index: 1;
    width: 100% !important;
    overflow: visible !important;
    font-size: 0.95rem !important;
    padding: 0.75rem 2.5rem 0.75rem 1rem !important;
}

#service_option:focus,
#service_option:active {
    z-index: 998 !important; /* Below topbar */
}

/* Color selection dropdowns - ensure full text visible like Service Category */
#selected_color,
#color_type {
    position: relative;
    z-index: 1;
    width: 100% !important;
    overflow: visible !important;
    font-size: 0.95rem !important;
    padding: 0.75rem 2.5rem 0.75rem 1rem !important;
}

#selected_color:focus,
#selected_color:active,
#color_type:focus,
#color_type:active {
    z-index: 998 !important; /* Below topbar */
}

/* Ensure all dropdowns match Service Category styling exactly */
#service_option,
#selected_color,
#color_type {
    /* Match Service Category exactly */
    font-size: 0.95rem !important;
    height: auto !important;
    min-height: 45px !important;
    line-height: 1.5 !important;
}

/* Fix for Bootstrap dropdown menu overflow */
.form-group {
    overflow: visible !important;
    position: relative;
}

.row {
    overflow: visible !important;
}

/* Container overflow fix */
.container-fluid,
#content-wrapper,
#content,
#wrapper {
    overflow: visible !important;
}

/* Additional fix for dropdown visibility */
select.form-control {
    background-color: white;
}

/* Ensure select dropdowns don't get clipped */
.form-group select.form-control {
    overflow: visible !important;
    position: relative;
}

/* Fix for when dropdown is open - critical for showing full text and above topbar */
select.form-control:active,
select.form-control:focus {
    z-index: 10000 !important;
    position: relative;
    overflow: visible !important;
}

/* Make sure parent containers don't clip dropdowns */
.card,
.card-body,
.form-card,
.form-card .card-body {
    overflow: visible !important;
    position: relative;
    z-index: 9999 !important;
}

/* Specific fix for service option dropdown - must be above topbar */
#service_option {
    overflow: visible !important;
    z-index: 10;
    position: relative;
    width: 100% !important;
}

#service_option:focus,
#service_option:active {
    z-index: 10000 !important;
    overflow: visible !important;
}

/* Fix for color dropdowns - must be above topbar */
#selected_color,
#color_type {
    overflow: visible !important;
    position: relative;
    z-index: 10;
}

#selected_color:focus,
#selected_color:active,
#color_type:focus,
#color_type:active {
    z-index: 10000 !important;
    overflow: visible !important;
}

/* Ensure dropdown options list is fully visible */
select.form-control option {
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    padding: 8px 12px !important;
    display: block !important;
}

/* Critical: Remove any overflow hidden from parent containers */
.col-md-6,
.col-md-8,
.col-md-4,
.col-lg-12,
.row {
    overflow: visible !important;
    position: relative;
}

.form-control:focus {
    border-color: #1F4E79;
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
}

.btn-submit {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 0.5rem;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-submit:hover {
    background: linear-gradient(135deg, #1F4E79 0%, #0F3C5F 100%);
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
    border-left: 4px solid #1F4E79;
}

/* Override Bootstrap primary colors with brown */
.btn-outline-primary {
    color: #1F4E79 !important;
    border-color: #1F4E79 !important;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%) !important;
    border-color: #1F4E79 !important;
    color: white !important;
}

.text-primary {
    color: #1F4E79 !important;
}

/* Additional styling consolidated - all dropdowns already styled above */

/* Ensure dropdown options are fully visible - apply to ALL dropdowns */
select#service_category option,
select#service_type option,
select#store_location option,
select#service_option option,
select#selected_color option,
select#color_type option {
    padding: 10px !important;
    font-size: 0.95rem !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    display: block !important;
    width: 100% !important;
}

/* Make sure columns have proper spacing */
.form-group {
    margin-bottom: 1.5rem;
}

/* Custom brown badge styling */
.badge-brown {
    background-color: #1F4E79;
    color: white;
}

/* Update form card border */
.form-card {
    border: 1px solid #e8dcc8;
}

/* Breadcrumb styling */
.breadcrumb-item a {
    color: #1F4E79;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #0F3C5F;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #0F3C5F;
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
    #service_option,
    #selected_color,
    #color_type,
    .form-control,
    .form-select {
        font-size: 16px !important; /* Prevents zoom on Android/iOS */
        padding: 12px 15px;
        min-height: 48px; /* Touch-friendly */
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    select.form-control option {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
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
<div class="welcome-container shadow-sm">
    <div class="welcome-text">
        <i class="fas fa-tools mr-2" style="color: #0F3C5F;"></i>
        Repair Reservation
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard" style="color: #0F3C5F; font-size: 0.85rem; font-weight: 600;">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/bookings" style="color: #0F3C5F; font-size: 0.85rem; font-weight: 600;">Bookings</a></li>
            <li class="breadcrumb-item active" style="font-size: 0.85rem; font-weight: 600;">Repair</li>
        </ol>
    </nav>
</div>

<!-- Repair Reservation Form -->
<div class="row main-content form-section">
    <div class="col-lg-12">
        <div class="card form-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tools mr-2"></i>Create Repair Reservation</h5>
            </div>
            <div class="card-body p-3">
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
                                <label class="form-label small">Service Category <span class="text-danger">*</span></label>
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
                                <label class="form-label small">Service Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="service_type" name="service_type" required>
                                    <option value="">Select Type</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="serviceInfo" class="service-info-card">
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Estimated Days:</strong> <span id="serviceDays">-</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Description:</strong>
                            <p id="serviceDescription" class="mb-0 text-muted small"></p>
                        </div>
                    </div>

                    <!-- Store Selection Section -->
                    <div class="form-group mb-3">
                        <label class="form-label small">Preferred Store Location <span class="text-danger">*</span></label>
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
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label small">Service Option <span class="text-danger">*</span></label>
                                <select class="form-control service-option-select" name="service_option" id="service_option" required>
                                    <option value="">Select Option</option>
                                    <option value="pickup">Pick Up</option>
                                    <option value="delivery">Delivery Service</option>
                                    <option value="both">Both (Pick Up & Delivery)</option>
                                    <option value="walk_in">Walk In</option>
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Choose your preferred service option: Pick Up, Delivery, Both, or Walk In.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Color Selection Section Removed -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="pickupDateGroup">
                                <label class="form-label">Preferred Pickup Date</label>
                                <input type="date" class="form-control" name="pickup_date" id="pickup_date"
                                       min="<?php echo date('Y-m-d'); ?>">
                                <small class="form-text text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    Select when you prefer to have your item picked up.
                                </small>
                            </div>
                            <div class="form-group" id="deliveryDateGroup" style="display: none;">
                                <label class="form-label">Preferred Drop-off Item to the Shop <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="delivery_date" id="delivery_date"
                                       min="<?php echo date('Y-m-d'); ?>" required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-store"></i> 
                                    Select when you prefer to bring your item to the shop for inspection.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="pickupAddressGroup" style="display: none;">
                                <label class="form-label">Pickup Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="pickup_address" id="pickup_address" rows="3"
                                          placeholder="Enter your complete pickup address..."></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Provide your complete address where we will pick up your item. Additional charges will be calculated based on distance.
                                </small>
                            </div>
                            <!-- Distance field removed - no longer required -->
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="deliveryAddressGroup" style="display: none;">
                                <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="delivery_address" id="delivery_address" rows="3"
                                          placeholder="Enter your complete delivery address for final delivery after repair..."></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Provide your complete address where we will deliver your repaired item. This is required for "Both" service option (Pick Up & Delivery).
                                </small>
                            </div>
                        </div>
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

</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<script>
// Service data
const services = <?php echo json_encode($services); ?>;

// Create a mapping of category to service names
const categoryServiceNames = {};
services.forEach(service => {
    const categoryId = parseInt(service.category_id);
    if (!categoryServiceNames[categoryId]) {
        categoryServiceNames[categoryId] = [];
    }
    // For Vehicle Upholstery, only show Motor Seat
    if (service.service_type === 'Vehicle Upholstery' && service.service_name === 'Motor Seat') {
        categoryServiceNames[categoryId].push({
            name: service.service_name,
            service: service
        });
    } else if (service.service_type !== 'Vehicle Upholstery') {
        // For other categories, show all services
        categoryServiceNames[categoryId].push({
            name: service.service_name,
            service: service
        });
    }
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
        
        if (categoryServiceNames[categoryIdInt]) {
            categoryServiceNames[categoryIdInt].forEach(item => {
                const option = document.createElement('option');
                option.value = item.name;
                option.textContent = item.name;
                option.dataset.serviceId = item.service.id;
                serviceTypeSelect.appendChild(option);
            });
        }
    }
});

// Handle service type change
document.getElementById('service_type').addEventListener('change', function() {
    const categoryId = document.getElementById('service_category').value;
    const serviceName = this.value;
    const serviceInfo = document.getElementById('serviceInfo');
    
    if (categoryId && serviceName) {
        const categoryIdInt = parseInt(categoryId);
        const matchingService = services.find(service => 
            parseInt(service.category_id) === categoryIdInt && service.service_name === serviceName
        );
        
        if (matchingService) {
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
    
    // Fix dropdown visibility issues
    fixDropdownVisibility();
    
    // Ensure color dropdown is enabled if store is already selected
    const storeLocation = document.getElementById('store_location');
    const colorSelect = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type');
    
    if (storeLocation && storeLocation.value && colorSelect) {
        // Store is selected - enable color dropdown
        colorSelect.disabled = false;
        colorSelect.removeAttribute('disabled');
        colorSelect.setAttribute('required', 'required');
        colorSelect.style.opacity = '1';
        colorSelect.style.pointerEvents = 'auto';
        colorSelect.style.cursor = 'pointer';
        colorSelect.style.backgroundColor = '#fff';
        
        // Enable color type dropdown
        if (colorType) {
            colorType.disabled = false;
            colorType.removeAttribute('disabled');
            colorType.setAttribute('required', 'required');
            colorType.style.opacity = '1';
            colorType.style.pointerEvents = 'auto';
            colorType.style.cursor = 'pointer';
            colorType.style.backgroundColor = '#fff';
        }
        
        // Load colors if store is selected
        const fabricType = colorType && colorType.value ? colorType.value : null;
        loadAvailableColors(storeLocation.value, fabricType);
    }
    
    // Reset service option to default on page load (don't restore from sessionStorage)
    const serviceOption = document.getElementById('service_option');
    if (serviceOption) {
        // Always reset service option to empty/default on page refresh
        serviceOption.value = '';
        
        // Clear service_option from sessionStorage to prevent it from being restored
        const savedData = sessionStorage.getItem('repairReservationFormData');
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                delete formData.service_option; // Remove service_option from saved data
                sessionStorage.setItem('repairReservationFormData', JSON.stringify(formData));
            } catch (e) {
                console.error('Error clearing service option from sessionStorage:', e);
            }
        }
        
        // Hide pickup date field initially since no service option is selected
        const pickupDateGroup = document.getElementById('pickupDateGroup');
        if (pickupDateGroup) {
            pickupDateGroup.style.display = 'none';
        }
        
        // Also hide other date/address fields initially
        const deliveryDateGroup = document.getElementById('deliveryDateGroup');
        const pickupAddressGroup = document.getElementById('pickupAddressGroup');
        if (deliveryDateGroup) {
            deliveryDateGroup.style.display = 'none';
        }
        if (pickupAddressGroup) {
            pickupAddressGroup.style.display = 'none';
        }
    }
});

// Fix dropdown visibility to show full text and appear above topbar
function fixDropdownVisibility() {
    // Get all select elements
    const selects = document.querySelectorAll('select.form-control');
    
    selects.forEach(select => {
        // Ensure overflow is visible
        select.style.overflow = 'visible';
        select.style.position = 'relative';
        select.style.zIndex = '10';
        
        // Fix on focus - keep below topbar to prevent overlap
        select.addEventListener('focus', function() {
            this.style.zIndex = '998'; // Just below topbar (999)
            this.style.overflow = 'visible';
            this.style.position = 'relative';
            this.style.width = '100%';
            this.style.maxWidth = '100%';
            
            // Ensure parent containers don't clip
            let parent = this.parentElement;
            while (parent && parent !== document.body) {
                const computedStyle = window.getComputedStyle(parent);
                if (computedStyle.overflow === 'hidden' || 
                    computedStyle.overflow === 'auto') {
                    parent.style.overflow = 'visible';
                }
                // Keep parent z-index low to prevent overlap
                if (parent.classList.contains('form-group') || 
                    parent.classList.contains('card-body') ||
                    parent.classList.contains('card') ||
                    parent.classList.contains('form-card')) {
                    parent.style.zIndex = '1';
                    parent.style.position = 'relative';
                }
                parent = parent.parentElement;
            }
        });
        
        // Fix on blur
        select.addEventListener('blur', function() {
            this.style.zIndex = '1';
        });
        
        // Fix on mousedown/click - keep below topbar
        select.addEventListener('mousedown', function() {
            this.style.zIndex = '998';
        });
        
        // Ensure options show full text
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            option.style.whiteSpace = 'normal';
            option.style.wordWrap = 'break-word';
            option.style.overflowWrap = 'break-word';
        });
    });
    
    // Also fix the main content area z-index - keep low to prevent overlap
    const mainContent = document.querySelector('.container-fluid') || 
                       document.querySelector('#content') ||
                       document.querySelector('.card-body');
    if (mainContent) {
        mainContent.style.zIndex = '1';
        mainContent.style.position = 'relative';
        mainContent.style.overflow = 'visible';
    }
    
    // Ensure all dropdowns have consistent styling like Service Category
    selects.forEach(select => {
        // Match Service Category styling exactly
        select.style.fontSize = '0.95rem';
        select.style.padding = '0.75rem 2.5rem 0.75rem 1rem';
        select.style.width = '100%';
        select.style.maxWidth = '100%';
        select.style.minWidth = '100%';
        select.style.boxSizing = 'border-box';
        select.style.overflow = 'visible';
        select.style.textOverflow = 'clip';
        select.style.whiteSpace = 'normal';
        select.style.minHeight = '45px';
        select.style.height = 'auto';
        select.style.lineHeight = '1.5';
        
        // Fix options to show full text
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            option.style.whiteSpace = 'normal';
            option.style.wordWrap = 'break-word';
            option.style.overflowWrap = 'break-word';
            option.style.padding = '10px 12px';
            option.style.fontSize = '0.95rem';
            option.style.display = 'block';
            option.style.width = '100%';
            option.style.minHeight = '30px';
        });
    });
}

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

// Load available colors for selected store and fabric type
let availableColors = [];
let allAvailableColors = []; // Store all colors for filtering

function loadAvailableColors(storeId, fabricType = null) {
    if (!storeId) {
        console.error('Store ID is required to load colors');
        const colorSelect = document.getElementById('selected_color');
        if (colorSelect) {
            colorSelect.disabled = true;
            colorSelect.innerHTML = '<option value="">Select Color...</option><option value="" disabled>Please select a store location first</option>';
        }
        return;
    }
    
    // Always enable color dropdown when store is selected
    const colorSelect = document.getElementById('selected_color');
    if (colorSelect) {
        colorSelect.disabled = false;
        colorSelect.removeAttribute('disabled');
        colorSelect.setAttribute('required', 'required');
        colorSelect.style.opacity = '1';
        colorSelect.style.pointerEvents = 'auto';
        colorSelect.style.cursor = 'pointer';
        colorSelect.style.backgroundColor = '#fff';
    }
    
    // If no fabric type selected, load all colors (both standard and premium)
    // If fabric type is selected, filter by that type
    const timestamp = new Date().getTime();
    let url = `<?php echo BASE_URL; ?>customer/getAvailableColors?store_id=${storeId}&_t=${timestamp}`;
    
    if (fabricType && fabricType !== '') {
        url += `&fabric_type=${fabricType}`;
    }
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        },
        cache: 'no-store'
    })
    .then(response => {
        if (!response.ok) {
        if (selectedStore) {
            document.getElementById('selectedStoreName').textContent = selectedStore.store_name;
            document.getElementById('selectedStoreAddress').textContent = selectedStore.address + ', ' + selectedStore.city;
            document.getElementById('selectedStoreContact').textContent = `Phone: ${selectedStore.phone} | Email: ${selectedStore.email}`;
            const ratingBadge = document.getElementById('selectedStoreRating');
            ratingBadge.textContent = `★ ${selectedStore.rating}/5.0`;
            storeInfo.style.display = 'block';
        }
    } else {
        storeInfo.style.display = 'none';
    }
});

// Removed color selection logic functions: loadAvailableColors, populateColorDropdown, updateColorPrice, etc.
            ratingBadge.className = 'badge badge-brown';
            
            storeInfo.style.display = 'block';
            
            // Enable color type selection (Standard/Premium)
                if (colorType) {
                    colorType.removeAttribute('disabled');
                    colorType.setAttribute('required', 'required');
                colorType.style.opacity = '1';
                colorType.style.pointerEvents = 'auto';
                colorType.style.cursor = 'pointer';
                colorType.style.backgroundColor = '#fff';
            }
            
            // Enable color selection immediately when store is selected
            // Load all colors initially, then filter when type is selected
            if (colorSelect) {
                colorSelect.disabled = false;
                colorSelect.removeAttribute('disabled');
                colorSelect.setAttribute('required', 'required');
                colorSelect.style.opacity = '1';
                colorSelect.style.pointerEvents = 'auto';
                colorSelect.style.cursor = 'pointer';
                colorSelect.style.backgroundColor = '#fff';
            }
            
            // Load all colors initially (will be filtered when type is selected)
            loadAvailableColors(storeId, null);
            
                if (colorSection) {
                    colorSection.style.display = 'block';
                    colorSection.style.opacity = '1';
                    colorSection.style.pointerEvents = 'auto';
            }
        }
    } else {
        storeInfo.style.display = 'none';
        // Disable and remove required attribute
        if (colorSelect) {
            colorSelect.setAttribute('disabled', 'disabled');
            colorSelect.removeAttribute('required');
            colorSelect.innerHTML = '<option value="">Select Color...</option><option value="" disabled>Please select a store location first</option>';
        }
        if (colorType) {
            colorType.setAttribute('disabled', 'disabled');
            colorType.removeAttribute('required');
        }
        const colorPreview = document.getElementById('colorPreview');
        if (colorPreview) colorPreview.style.display = 'none';
    }
});

// Open store locations page
function openStoreLocations() {
    // Save current form data to sessionStorage before opening store locations
    const formData = {
        service_category: document.getElementById('service_category').value,
        service_type: document.getElementById('service_type').value,
        service_option: document.getElementById('service_option').value,
        pickup_date: document.querySelector('[name="pickup_date"]').value,
        pickup_address: document.querySelector('[name="pickup_address"]').value,
        delivery_date: document.querySelector('[name="delivery_date"]').value,
        delivery_address: document.querySelector('[name="delivery_address"]').value,
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
            
            // DO NOT restore service option - it should always reset to "Select Option" on page refresh
            // This ensures the customer must explicitly select a service option each time
            
            // Restore other form fields
            if (formData.pickup_date) {
                document.querySelector('[name="pickup_date"]').value = formData.pickup_date;
            }
            if (formData.pickup_address) {
                document.querySelector('[name="pickup_address"]').value = formData.pickup_address;
            }
            if (formData.delivery_date) {
                document.querySelector('[name="delivery_date"]').value = formData.delivery_date;
            }
            if (formData.delivery_address) {
                document.querySelector('[name="delivery_address"]').value = formData.delivery_address;
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
    // Function to save current form state
    const saveFormState = function() {
        const formData = {
            service_category: document.getElementById('service_category')?.value || '',
            service_type: document.getElementById('service_type')?.value || '',
            service_option: document.getElementById('service_option')?.value || '',
            pickup_date: document.querySelector('[name="pickup_date"]')?.value || '',
            pickup_address: document.querySelector('[name="pickup_address"]')?.value || '',
            delivery_date: document.querySelector('[name="delivery_date"]')?.value || '',
            delivery_address: document.querySelector('[name="delivery_address"]')?.value || '',
            notes: document.querySelector('[name="notes"]')?.value || '',
            returnTo: 'repairReservation'
        };
        sessionStorage.setItem('repairReservationFormData', JSON.stringify(formData));
    };
    
    // Add event listeners to all fields (check if elements exist first)
    const serviceCategory = document.getElementById('service_category');
    const serviceType = document.getElementById('service_type');
    const serviceOption = document.getElementById('service_option');
    const pickupDate = document.querySelector('[name="pickup_date"]');
    const pickupAddress = document.querySelector('[name="pickup_address"]');
    const deliveryDate = document.querySelector('[name="delivery_date"]');
    const deliveryAddress = document.querySelector('[name="delivery_address"]');
    const notes = document.querySelector('[name="notes"]');
    
    if (serviceCategory) serviceCategory.addEventListener('change', saveFormState);
    if (serviceType) serviceType.addEventListener('change', saveFormState);
    if (serviceOption) serviceOption.addEventListener('change', saveFormState);
    if (pickupDate) pickupDate.addEventListener('change', saveFormState);
    if (pickupAddress) pickupAddress.addEventListener('input', saveFormState);
    if (deliveryDate) deliveryDate.addEventListener('change', saveFormState);
    if (deliveryAddress) deliveryAddress.addEventListener('input', saveFormState);
    if (notes) notes.addEventListener('input', saveFormState);
}

// Helper function to enable fabric and quality selection
function enableFabricAndQuality() {
    const selectedColor = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type');
    const colorSelectionSection = document.getElementById('colorSelectionSection');
    const storeLocation = document.getElementById('store_location');
    const colorLabel = colorSelectionSection ? colorSelectionSection.querySelector('label.form-label') : null;
    const colorHelpText = colorSelectionSection ? colorSelectionSection.querySelector('small.form-text') : null;
    
    // Enable color selection section
    if (colorSelectionSection) {
        colorSelectionSection.style.opacity = '1';
        colorSelectionSection.style.pointerEvents = 'auto';
        colorSelectionSection.style.display = 'block';
    }
    
    // Restore original label and help text
    if (colorLabel) {
        colorLabel.innerHTML = 'Fabric/Color Selection <span class="text-danger">*</span>';
    }
    if (colorHelpText) {
        colorHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Select a color based on availability at your chosen store. Premium colors have additional cost.';
        colorHelpText.className = 'form-text text-muted mb-2 d-block';
    }
    
    // ALWAYS enable color type dropdown (Standard/Premium) - should be clickable immediately
    if (colorType) {
        colorType.disabled = false;
        colorType.removeAttribute('disabled');
        colorType.setAttribute('required', 'required');
        colorType.classList.remove('disabled');
        colorType.style.opacity = '1';
        colorType.style.pointerEvents = 'auto';
        colorType.style.cursor = 'pointer';
        colorType.style.backgroundColor = '#fff';
    }
    
    // Enable fabric/color selection dropdown when store location is selected
    if (selectedColor) {
        const storeLocation = document.getElementById('store_location');
        
        if (storeLocation && storeLocation.value) {
            // Store selected - enable color dropdown immediately
            selectedColor.disabled = false;
            selectedColor.removeAttribute('disabled');
            selectedColor.setAttribute('required', 'required');
            selectedColor.style.opacity = '1';
            selectedColor.style.pointerEvents = 'auto';
            selectedColor.style.cursor = 'pointer';
            selectedColor.style.backgroundColor = '#fff';
        } else {
            // No store selected - keep disabled
            selectedColor.disabled = true;
            selectedColor.setAttribute('disabled', 'disabled');
            selectedColor.removeAttribute('required');
            selectedColor.innerHTML = '<option value="">Select Color...</option><option value="" disabled>Please select a store location first</option>';
        }
    }
}

// Alias for backward compatibility
function enableColorSelection() {
    enableFabricAndQuality();
}

// Helper function to disable fabric and quality selection
function disableFabricAndQuality() {
    const selectedColor = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type');
    const colorSelectionSection = document.getElementById('colorSelectionSection');
    const colorLabel = colorSelectionSection ? colorSelectionSection.querySelector('label.form-label') : null;
    const colorHelpText = colorSelectionSection ? colorSelectionSection.querySelector('small.form-text') : null;
    
    if (colorSelectionSection) {
        colorSelectionSection.style.opacity = '0.6';
        colorSelectionSection.style.pointerEvents = 'none';
    }
    
    if (selectedColor) {
        selectedColor.disabled = true;
        selectedColor.setAttribute('disabled', 'disabled');
        selectedColor.removeAttribute('required');
        selectedColor.classList.add('disabled');
        selectedColor.value = ''; // Clear selection
    }
    
    if (colorType) {
        colorType.disabled = true;
        colorType.setAttribute('disabled', 'disabled');
        colorType.removeAttribute('required');
        colorType.classList.add('disabled');
        colorType.value = 'standard'; // Reset to standard
    }
    
    // Update label and help text to indicate it's disabled
    if (colorLabel) {
        colorLabel.innerHTML = 'Fabric/Color Selection <span class="text-muted">(Disabled)</span>';
    }
    if (colorHelpText) {
        colorHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Color selection is not available for this service option.';
        colorHelpText.className = 'form-text text-muted mb-2 d-block';
    }
    
    // Hide color preview
    const colorPreview = document.getElementById('colorPreview');
    if (colorPreview) {
        colorPreview.style.display = 'none';
    }
}

// Alias for backward compatibility
function disableColorSelection() {
    disableFabricAndQuality();
}

// MAIN LOGIC - Handle service option change (Pickup, Delivery, Both, or Walk In)
// Get elements
const serviceOption = document.getElementById('service_option');
const fabricSelect = document.getElementById('selected_color');
const qualitySelect = document.getElementById('color_type');

// Handle service option change
serviceOption.addEventListener('change', function() {
    const option = this.value;
    const pickupDateGroup = document.getElementById('pickupDateGroup');
    const deliveryDateGroup = document.getElementById('deliveryDateGroup');
    const pickupAddressGroup = document.getElementById('pickupAddressGroup');
    const deliveryAddressGroup = document.getElementById('deliveryAddressGroup');
    const deliveryAddress = document.getElementById('delivery_address');
    const pickupAddress = document.getElementById('pickup_address');
    const deliveryDate = document.getElementById('delivery_date');
    const pickupDate = document.getElementById('pickup_date');
    const storeLocation = document.getElementById('store_location');
    
        // Handle date and address fields based on service option
    if (option === 'pickup') {
        // Show pickup date and address, hide delivery fields
        pickupDateGroup.style.display = 'block';
        pickupAddressGroup.style.display = 'block';
        deliveryDateGroup.style.display = 'none';
        if (deliveryAddressGroup) {
        deliveryAddressGroup.style.display = 'none';
        }
        pickupAddress.setAttribute('required', 'required');
        pickupDate.setAttribute('required', 'required');
        if (deliveryAddress) {
        deliveryAddress.removeAttribute('required');
        }
        deliveryDate.removeAttribute('required');
        
        // Enable fabric/color selection for pickup
        enableFabricAndQuality();
        
    } else if (option === 'delivery') {
        // Delivery = customer drops item to shop → fabric selection MUST be enabled
        // Show drop-off date only, hide pickup fields and delivery address (removed)
        // Hide Preferred Pickup Date field completely
        if (pickupDateGroup) {
        pickupDateGroup.style.display = 'none';
        }
        pickupAddressGroup.style.display = 'none';
        deliveryDateGroup.style.display = 'block';
        // Delivery address removed - customer brings item to shop, no delivery address needed
        if (deliveryAddressGroup) {
            deliveryAddressGroup.style.display = 'none';
        }
        deliveryDate.setAttribute('required', 'required');
        pickupAddress.removeAttribute('required');
        if (pickupDate) {
        pickupDate.removeAttribute('required');
        }
        if (deliveryAddress) {
            deliveryAddress.removeAttribute('required');
        }
        
        // ✅ CRITICAL: Enable fabric/color selection for delivery service
        // DAPAT ENABLED ANG COLOR SELECTION KAPAG DELIVERY SERVICE
        console.log('Delivery Service selected - Enabling fabric/color selection');
        enableFabricAndQuality();
        
        // Verify it's enabled
        const colorTypeCheck = document.getElementById('color_type');
        const colorSelectCheck = document.getElementById('selected_color');
        if (colorTypeCheck) {
            console.log('Color Type (Standard/Premium) enabled:', !colorTypeCheck.disabled);
        }
        if (colorSelectCheck && storeLocation && storeLocation.value) {
            console.log('Color Select enabled:', !colorSelectCheck.disabled);
        }
        
        // If store is already selected, trigger store change to load colors
        if (storeLocation && storeLocation.value) {
            storeLocation.dispatchEvent(new Event('change'));
        }
        
    } else if (option === 'both') {
        // Show pickup fields and delivery address (for final delivery after repair)
        // Hide drop-off date field - "Both" means pickup service, not customer drop-off
        pickupDateGroup.style.display = 'block';
        pickupAddressGroup.style.display = 'block';
        deliveryDateGroup.style.display = 'none'; // Hide drop-off date for "Both" option
        // For "both" service, show delivery address (for final delivery after repair)
        if (deliveryAddressGroup) {
        deliveryAddressGroup.style.display = 'block';
        }
        pickupAddress.setAttribute('required', 'required');
        pickupDate.setAttribute('required', 'required');
        if (deliveryAddress) {
        deliveryAddress.setAttribute('required', 'required');
        }
        deliveryDate.removeAttribute('required'); // Remove required since drop-off date is hidden
        
        // Auto-fill delivery address from user's account if empty
        const userAddress = `<?php echo addslashes($userAddress ?? ''); ?>`;
        if (userAddress && deliveryAddress && !deliveryAddress.value) {
            deliveryAddress.value = userAddress;
        }
        
        // Enable fabric/color selection for both (has pickup)
        enableFabricAndQuality();
        
    } else if (option === 'walk_in') {
        // Walk-in = enable normally
        // Hide all date and address fields for walk-in
        // Hide Preferred Pickup Date field completely
        if (pickupDateGroup) {
        pickupDateGroup.style.display = 'none';
        }
        pickupAddressGroup.style.display = 'none';
        deliveryDateGroup.style.display = 'none';
        if (deliveryAddressGroup) {
        deliveryAddressGroup.style.display = 'none';
        }
        pickupAddress.removeAttribute('required');
        if (pickupDate) {
        pickupDate.removeAttribute('required');
        }
        if (deliveryAddress) {
        deliveryAddress.removeAttribute('required');
        }
        deliveryDate.removeAttribute('required');
        
        // Enable fabric/color selection for walk-in
        enableFabricAndQuality();
        
    } else {
        // Hide all if nothing selected
        // Hide Preferred Pickup Date field when no service option is selected
        if (pickupDateGroup) {
            pickupDateGroup.style.display = 'none';
        }
        pickupAddressGroup.style.display = 'none';
        deliveryDateGroup.style.display = 'none';
        if (deliveryAddressGroup) {
        deliveryAddressGroup.style.display = 'none';
        }
        pickupAddress.removeAttribute('required');
        if (pickupDate) {
        pickupDate.removeAttribute('required');
        }
        if (deliveryAddress) {
        deliveryAddress.removeAttribute('required');
        }
        deliveryDate.removeAttribute('required');
        
        // Disable color selection when nothing is selected
        disableFabricAndQuality();
    }
});

// Form validation
document.getElementById('repairReservationForm').addEventListener('submit', function(e) {
    const categoryId = document.getElementById('service_category').value;
    const serviceType = document.getElementById('service_type').value;
    const storeLocation = document.getElementById('store_location').value;
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
        alert('Please select a service option (Pick Up, Delivery, Both, or Walk In)');
        return false;
    }
    
    // Validate color selection if store is selected (required for all service options including delivery)
    if (storeLocation) {
        const selectedColor = document.getElementById('selected_color');
        const colorType = document.getElementById('color_type');
        
        // Check if color fields are disabled (store not selected)
        if (selectedColor && selectedColor.disabled) {
            e.preventDefault();
            alert('Please select a store location first before choosing a color');
            return false;
        }
        
        if (!selectedColor || !selectedColor.value) {
            e.preventDefault();
            alert('Please select a color/fabric for your booking');
            return false;
        }
        
        if (!colorType || !colorType.value) {
            e.preventDefault();
            alert('Please select color type (Standard or Premium)');
            return false;
        }
    }
    
    // Validate pickup fields if pickup or both is selected
    if (serviceOption === 'pickup' || serviceOption === 'both') {
        const pickupAddress = document.getElementById('pickup_address').value.trim();
        const pickupDate = document.getElementById('pickup_date').value;
        
        if (!pickupAddress) {
            e.preventDefault();
            alert('Please provide pickup address');
            return false;
        }
        
        if (!pickupDate) {
            e.preventDefault();
            alert('Please select a pickup date');
            return false;
        }
    }
    
    // Validate delivery fields based on service option
    if (serviceOption === 'delivery') {
        // For delivery service: customer drops off item to shop - only date is required, no address needed
        const deliveryDate = document.getElementById('delivery_date').value;
        
        if (!deliveryDate) {
            e.preventDefault();
            alert('Please select a drop-off date');
            return false;
        }
    } else if (serviceOption === 'both') {
        // For "both" service: needs delivery address (for final delivery after repair)
        // Drop-off date is not required for "Both" option (only pickup date is needed)
        const deliveryAddress = document.getElementById('delivery_address')?.value.trim();
        
        if (!deliveryAddress) {
            e.preventDefault();
            alert('Please provide delivery address for final delivery');
            return false;
        }
        
        // No delivery date validation needed for "Both" option
    }
    
    // If validation passes, clear saved form data
    sessionStorage.removeItem('repairReservationFormData');
    sessionStorage.removeItem('selectedStoreId');
    sessionStorage.removeItem('selectedServiceData');
});

// Capacity Availability Check
function checkAvailability() {
    const storeId = document.getElementById('store_location').value;
    const serviceOption = document.getElementById('service_option').value;
    let date = null;
    let type = null;

    if (serviceOption === 'pickup' || serviceOption === 'both') {
        date = document.getElementById('pickup_date').value;
        type = 'pickup';
    } else if (serviceOption === 'delivery') {
        date = document.getElementById('delivery_date').value;
        type = 'delivery';
    }

    if (storeId && date && type) {
        fetch(`<?php echo BASE_URL; ?>customer/checkLogisticAvailability?store_id=${storeId}&date=${date}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fully Booked',
                        text: `Sorry, ${type} service is fully booked for ${date}. Please choose another date.`,
                        confirmButtonColor: '#0F3C5F'
                    });
                    if (type === 'pickup') {
                        document.getElementById('pickup_date').value = '';
                    } else {
                        document.getElementById('delivery_date').value = '';
                    }
                }
            });
    }
}

document.getElementById('pickup_date').addEventListener('change', checkAvailability);
document.getElementById('delivery_date').addEventListener('change', checkAvailability);
document.getElementById('service_option').addEventListener('change', checkAvailability);
document.getElementById('store_location').addEventListener('change', checkAvailability);
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>


