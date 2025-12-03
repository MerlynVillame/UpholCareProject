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
<div class="row main-content form-section">
    <div class="col-lg-12">
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Service Option <span class="text-danger">*</span></label>
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

                    <!-- Color Selection Section -->
                    <div class="form-group" id="colorSelectionSection">
                        <label class="form-label">Fabric/Color Selection <span class="text-danger">*</span></label>
                        <small class="form-text text-muted mb-2 d-block">
                            <i class="fas fa-info-circle"></i> 
                            Select a color based on availability at your chosen store. Premium colors have additional cost.
                        </small>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <select class="form-control" id="selected_color" name="selected_color_id" disabled>
                                    <option value="">Select Color...</option>
                                    <option value="" disabled>Please select a store location first</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="color_type" name="color_type" disabled>
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
                            <div class="form-group" id="deliveryAddressGroup" style="display: none;">
                                <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="delivery_address" id="delivery_address" rows="3"
                                          placeholder="Enter your complete delivery address..."><?php echo htmlspecialchars($userAddress ?? ''); ?></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    Your account address is pre-filled. You can modify it if needed.
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
    
    // Check service option on page load and disable color selection if delivery
    const serviceOption = document.getElementById('service_option');
    if (serviceOption && serviceOption.value === 'delivery') {
        disableColorSelection();
    }
    
    // Trigger service option change to ensure proper state
    if (serviceOption && serviceOption.value) {
        serviceOption.dispatchEvent(new Event('change'));
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
    if (!colorSelect) return;
    
    colorSelect.innerHTML = '<option value="">Select Color...</option>';
    
    if (availableColors.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No colors available for this store';
        option.disabled = true;
        colorSelect.appendChild(option);
        // Hide preview if no colors
        const colorPreview = document.getElementById('colorPreview');
        if (colorPreview) colorPreview.style.display = 'none';
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
const colorSelect = document.getElementById('selected_color');
if (colorSelect) {
    colorSelect.addEventListener('change', function() {
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
}

// Handle color type change (premium/standard)
const colorTypeSelect = document.getElementById('color_type');
if (colorTypeSelect) {
    colorTypeSelect.addEventListener('change', function() {
        updateColorPrice();
        updatePremiumPriceDisplay();
    });
}

// Update color price display
function updateColorPrice() {
    const colorSelect = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type').value;
    const selectedOption = colorSelect ? colorSelect.options[colorSelect.selectedIndex] : null;
    
    if (!colorSelect || !colorSelect.value || !selectedOption || !selectedOption.dataset.basePrice) {
        const priceDisplay = document.getElementById('colorPriceDisplay');
        if (priceDisplay) priceDisplay.textContent = '₱0.00';
        return;
    }
    
    const basePrice = parseFloat(selectedOption.dataset.basePrice || 0);
    let totalPrice = basePrice;
    
    if (colorType === 'premium') {
        const premiumPrice = parseFloat(selectedOption.dataset.premiumPrice || 0);
        totalPrice = basePrice + premiumPrice;
    }
    
    const priceDisplay = document.getElementById('colorPriceDisplay');
    if (priceDisplay) {
        priceDisplay.textContent = '₱' + totalPrice.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    // Update badge
    const badge = document.getElementById('colorTypeBadge');
    if (badge) {
        if (colorType === 'premium') {
            badge.textContent = 'Premium';
            badge.className = 'badge badge-warning ml-2';
        } else {
            badge.textContent = 'Standard';
            badge.className = 'badge badge-secondary ml-2';
        }
    }
    
    // Update total amount
    updateTotalAmount();
}

// Update premium price display
function updatePremiumPriceDisplay() {
    const colorSelect = document.getElementById('selected_color');
    const selectedOption = colorSelect ? colorSelect.options[colorSelect.selectedIndex] : null;
    
    if (selectedOption && selectedOption.dataset.premiumPrice) {
        const premiumPrice = parseFloat(selectedOption.dataset.premiumPrice || 0);
        const premiumDisplay = document.getElementById('premiumPriceDisplay');
        if (premiumDisplay) {
            premiumDisplay.textContent = premiumPrice.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    } else {
        const premiumDisplay = document.getElementById('premiumPriceDisplay');
        if (premiumDisplay) premiumDisplay.textContent = '0.00';
    }
}

// Update total amount including color price
function updateTotalAmount() {
    const totalAmountInput = document.getElementById('total_amount');
    if (!totalAmountInput) return;
    
    const servicePrice = parseFloat(totalAmountInput.value || 0);
    const colorSelect = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type') ? document.getElementById('color_type').value : 'standard';
    const selectedOption = colorSelect && colorSelect.value ? colorSelect.options[colorSelect.selectedIndex] : null;
    
    let colorPrice = 0;
    if (colorSelect && colorSelect.value && selectedOption && selectedOption.dataset.basePrice) {
        const basePrice = parseFloat(selectedOption.dataset.basePrice || 0);
        if (colorType === 'premium') {
            const premiumPrice = parseFloat(selectedOption.dataset.premiumPrice || 0);
            colorPrice = basePrice + premiumPrice;
        } else {
            colorPrice = basePrice;
        }
    }
    
    const totalAmount = servicePrice + colorPrice;
    totalAmountInput.value = totalAmount.toFixed(2);
}

// Handle store selection change
document.getElementById('store_location').addEventListener('change', function() {
    const storeId = this.value;
    const storeInfo = document.getElementById('selectedStoreInfo');
    const colorSection = document.getElementById('colorSelectionSection');
    const colorSelect = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type');
    
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
            
            // Check service option - disable color selection for delivery
            const serviceOption = document.getElementById('service_option').value;
            if (serviceOption === 'delivery') {
                // Disable color selection for delivery service
                disableColorSelection();
            } else {
                // Enable and make color fields required for other service options
                if (colorSelect) {
                    colorSelect.removeAttribute('disabled');
                    colorSelect.setAttribute('required', 'required');
                }
                if (colorType) {
                    colorType.removeAttribute('disabled');
                    colorType.setAttribute('required', 'required');
                }
                
                // Load available colors for this store
                loadAvailableColors(storeId);
                if (colorSection) {
                    colorSection.style.display = 'block';
                    colorSection.style.opacity = '1';
                    colorSection.style.pointerEvents = 'auto';
                }
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
            
            // Restore service option
            if (formData.service_option) {
                document.getElementById('service_option').value = formData.service_option;
                document.getElementById('service_option').dispatchEvent(new Event('change'));
            }
            
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
    // List of fields to auto-save
    const fieldsToSave = [
        'service_category',
        'service_type',
        '[name="pickup_date"]',
        '[name="pickup_address"]',
        '[name="item_description"]',
        '[name="notes"]'
    ];
    
    // Function to save current form state
    const saveFormState = function() {
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
    };
    
    // Add event listeners to all fields
    document.getElementById('service_category').addEventListener('change', saveFormState);
    document.getElementById('service_type').addEventListener('change', saveFormState);
    document.getElementById('service_option').addEventListener('change', saveFormState);
    document.querySelector('[name="pickup_date"]').addEventListener('change', saveFormState);
    document.querySelector('[name="pickup_address"]').addEventListener('input', saveFormState);
    document.querySelector('[name="delivery_date"]').addEventListener('change', saveFormState);
    document.querySelector('[name="delivery_address"]').addEventListener('input', saveFormState);
    document.querySelector('[name="notes"]').addEventListener('input', saveFormState);
}

// Helper function to enable color selection
function enableColorSelection() {
    const colorSelectionSection = document.getElementById('colorSelectionSection');
    const selectedColor = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type');
    const storeLocation = document.getElementById('store_location');
    const colorLabel = colorSelectionSection ? colorSelectionSection.querySelector('label.form-label') : null;
    const colorHelpText = colorSelectionSection ? colorSelectionSection.querySelector('small.form-text') : null;
    
    if (colorSelectionSection) {
        colorSelectionSection.style.opacity = '1';
        colorSelectionSection.style.pointerEvents = 'auto';
    }
    
    // Restore original label and help text
    if (colorLabel) {
        colorLabel.innerHTML = 'Fabric/Color Selection <span class="text-danger">*</span>';
    }
    if (colorHelpText) {
        colorHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Select a color based on availability at your chosen store. Premium colors have additional cost.';
        colorHelpText.className = 'form-text text-muted mb-2 d-block';
    }
    
    // Only enable if store location is selected
    if (selectedColor && storeLocation && storeLocation.value) {
        selectedColor.disabled = false;
        selectedColor.setAttribute('required', 'required');
    }
    
    if (colorType) {
        colorType.disabled = false;
        colorType.setAttribute('required', 'required');
    }
}

// Helper function to disable color selection
function disableColorSelection() {
    const colorSelectionSection = document.getElementById('colorSelectionSection');
    const selectedColor = document.getElementById('selected_color');
    const colorType = document.getElementById('color_type');
    const colorLabel = colorSelectionSection ? colorSelectionSection.querySelector('label.form-label') : null;
    const colorHelpText = colorSelectionSection ? colorSelectionSection.querySelector('small.form-text') : null;
    
    if (colorSelectionSection) {
        colorSelectionSection.style.opacity = '0.6';
        colorSelectionSection.style.pointerEvents = 'none';
    }
    
    if (selectedColor) {
        selectedColor.disabled = true;
        selectedColor.removeAttribute('required');
        selectedColor.value = ''; // Clear selection
    }
    
    if (colorType) {
        colorType.disabled = true;
        colorType.removeAttribute('required');
        colorType.value = 'standard'; // Reset to standard
    }
    
    // Update label and help text to indicate it's disabled for delivery
    if (colorLabel) {
        colorLabel.innerHTML = 'Fabric/Color Selection <span class="text-muted">(Disabled for Delivery Service)</span>';
    }
    if (colorHelpText) {
        colorHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Color selection is not available for delivery service. Fabric will be selected during inspection.';
        colorHelpText.className = 'form-text text-muted mb-2 d-block';
    }
    
    // Hide color preview
    const colorPreview = document.getElementById('colorPreview');
    if (colorPreview) {
        colorPreview.style.display = 'none';
    }
}

// Handle service option change (Pickup, Delivery, Both, or Walk In)
document.getElementById('service_option').addEventListener('change', function() {
    const serviceOption = this.value;
    const pickupDateGroup = document.getElementById('pickupDateGroup');
    const deliveryDateGroup = document.getElementById('deliveryDateGroup');
    const pickupAddressGroup = document.getElementById('pickupAddressGroup');
    const deliveryAddressGroup = document.getElementById('deliveryAddressGroup');
    const deliveryAddress = document.getElementById('delivery_address');
    const pickupAddress = document.getElementById('pickup_address');
    const deliveryDate = document.getElementById('delivery_date');
    const pickupDate = document.getElementById('pickup_date');
    
    // Distance field removed - no longer required
    
    if (serviceOption === 'pickup') {
        // Show pickup date and address, hide delivery fields
        pickupDateGroup.style.display = 'block';
        pickupAddressGroup.style.display = 'block';
        deliveryDateGroup.style.display = 'none';
        deliveryAddressGroup.style.display = 'none';
        pickupAddress.setAttribute('required', 'required');
        pickupDate.setAttribute('required', 'required');
        deliveryAddress.removeAttribute('required');
        deliveryDate.removeAttribute('required');
        
        // Enable fabric/color selection for pickup
        enableColorSelection();
    } else if (serviceOption === 'delivery') {
        // Show delivery date and address, hide pickup fields
        pickupDateGroup.style.display = 'none';
        pickupAddressGroup.style.display = 'none';
        deliveryDateGroup.style.display = 'block';
        deliveryAddressGroup.style.display = 'block';
        deliveryAddress.setAttribute('required', 'required');
        deliveryDate.setAttribute('required', 'required');
        pickupAddress.removeAttribute('required');
        pickupDate.removeAttribute('required');
        // Auto-fill delivery address from user's account if empty
        const userAddress = `<?php echo addslashes($userAddress ?? ''); ?>`;
        if (userAddress && !deliveryAddress.value) {
            deliveryAddress.value = userAddress;
        }
        
        // Disable fabric/color selection for delivery service
        disableColorSelection();
    } else if (serviceOption === 'both') {
        // Show both pickup and delivery fields
        pickupDateGroup.style.display = 'block';
        pickupAddressGroup.style.display = 'block';
        deliveryDateGroup.style.display = 'block';
        deliveryAddressGroup.style.display = 'block';
        pickupAddress.setAttribute('required', 'required');
        pickupDate.setAttribute('required', 'required');
        deliveryAddress.setAttribute('required', 'required');
        deliveryDate.setAttribute('required', 'required');
        // Auto-fill delivery address from user's account if empty
        const userAddress = `<?php echo addslashes($userAddress ?? ''); ?>`;
        if (userAddress && !deliveryAddress.value) {
            deliveryAddress.value = userAddress;
        }
        
        // Enable fabric/color selection for both (has pickup)
        enableColorSelection();
    } else if (serviceOption === 'walk_in') {
        // Hide all date and address fields for walk-in
        pickupDateGroup.style.display = 'none';
        pickupAddressGroup.style.display = 'none';
        deliveryDateGroup.style.display = 'none';
        deliveryAddressGroup.style.display = 'none';
        pickupAddress.removeAttribute('required');
        pickupDate.removeAttribute('required');
        deliveryAddress.removeAttribute('required');
        deliveryDate.removeAttribute('required');
        
        // Enable fabric/color selection for walk-in
        enableColorSelection();
    } else {
        // Hide all if nothing selected
        pickupDateGroup.style.display = 'block';
        pickupAddressGroup.style.display = 'none';
        deliveryDateGroup.style.display = 'none';
        deliveryAddressGroup.style.display = 'none';
        pickupAddress.removeAttribute('required');
        pickupDate.removeAttribute('required');
        deliveryAddress.removeAttribute('required');
        deliveryDate.removeAttribute('required');
        
        // Disable color selection when nothing is selected
        disableColorSelection();
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
    
    // Validate color selection if store is selected (skip for delivery service)
    if (storeLocation && serviceOption !== 'delivery') {
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
    
    // Validate delivery fields if delivery or both is selected
    if (serviceOption === 'delivery' || serviceOption === 'both') {
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


