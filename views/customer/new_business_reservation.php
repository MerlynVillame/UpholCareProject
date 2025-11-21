<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">New Business Reservation</h1>
        <p class="mb-0">Create a new business service reservation for your company.</p>
    </div>
    <div>
        <a href="<?php echo BASE_URL; ?>customer/profile" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>

<!-- Business Mode Notice -->
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i>
    <strong>Business Reservation:</strong> This reservation will be sent directly to admin for processing. 
    You will receive notifications about the status and scheduling of your business reservation.
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Business Reservation Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-briefcase"></i> Business Reservation Details
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>customer/processBusinessReservation" id="businessReservationForm">
                    
                    <!-- Business Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-building"></i> Business Information
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_name" class="form-label">Business Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_name" name="business_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_type" class="form-label">Business Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="business_type" name="business_type" required>
                                    <option value="">Select Business Type</option>
                                    <option value="furniture_store">Furniture Store</option>
                                    <option value="interior_design">Interior Design</option>
                                    <option value="hotel_restaurant">Hotel/Restaurant</option>
                                    <option value="office_space">Office Space</option>
                                    <option value="retail_store">Retail Store</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="business_phone" class="form-label">Business Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_phone" name="business_phone" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="business_address" class="form-label">Business Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="business_address" name="business_address" rows="3" required></textarea>
                    </div>
                    
                    <!-- Service Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-tools"></i> Service Information
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service_category" class="form-label">Service Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="service_category" name="service_category" required>
                                    <option value="">Select Service Category</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="service_type" name="service_type" required>
                                    <option value="">Select Service Type</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="project_name" name="project_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="urgency_level" class="form-label">Urgency Level <span class="text-danger">*</span></label>
                                <select class="form-control" id="urgency_level" name="urgency_level" required>
                                    <option value="">Select Urgency</option>
                                    <option value="low">Low - Can wait 2+ weeks</option>
                                    <option value="medium">Medium - Within 1-2 weeks</option>
                                    <option value="high">High - Within 1 week</option>
                                    <option value="urgent">Urgent - Within 3 days</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="service_description" class="form-label">Service Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="service_description" name="service_description" rows="4" required 
                                  placeholder="Describe the services needed for your business project..."></textarea>
                    </div>
                    
                    <!-- Scheduling Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-calendar-alt"></i> Scheduling Information
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="preferred_date" class="form-label">Preferred Start Date</label>
                                <input type="date" class="form-control" id="preferred_date" name="preferred_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimated_duration" class="form-label">Estimated Duration</label>
                                <select class="form-control" id="estimated_duration" name="estimated_duration">
                                    <option value="">Select Duration</option>
                                    <option value="1-3_days">1-3 Days</option>
                                    <option value="1_week">1 Week</option>
                                    <option value="2_weeks">2 Weeks</option>
                                    <option value="1_month">1 Month</option>
                                    <option value="custom">Custom (Specify in notes)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="budget_range" class="form-label">Budget Range</label>
                                <select class="form-control" id="budget_range" name="budget_range">
                                    <option value="">Select Budget Range</option>
                                    <option value="under_5k">Under ₱5,000</option>
                                    <option value="5k_10k">₱5,000 - ₱10,000</option>
                                    <option value="10k_25k">₱10,000 - ₱25,000</option>
                                    <option value="25k_50k">₱25,000 - ₱50,000</option>
                                    <option value="over_50k">Over ₱50,000</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="store_location" class="form-label">Preferred Store Location</label>
                                <select class="form-control" id="store_location" name="store_location">
                                    <option value="">Select Store Location</option>
                                    <?php if (!empty($stores)): ?>
                                        <?php foreach ($stores as $store): ?>
                                            <option value="<?php echo $store['id']; ?>">
                                                <?php echo htmlspecialchars($store['store_name'] . ' - ' . $store['city']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="special_requirements" class="form-label">Special Requirements or Notes</label>
                        <textarea class="form-control" id="special_requirements" name="special_requirements" rows="3" 
                                  placeholder="Any special requirements, accessibility needs, or additional information..."></textarea>
                    </div>
                    
                    <!-- Admin Processing Notice -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Admin Processing:</strong> Your business reservation will be reviewed by admin and you will receive 
                        a confirmation with scheduling details within 24-48 hours.
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane"></i> Submit Business Reservation
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Business Reservation Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-info-circle"></i> Business Reservation Info
                </h6>
            </div>
            <div class="card-body">
                <h6 class="text-primary">How Business Reservations Work:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Admin reviews your reservation</li>
                    <li><i class="fas fa-check text-success"></i> You receive confirmation within 24-48 hours</li>
                    <li><i class="fas fa-check text-success"></i> Priority scheduling for business clients</li>
                    <li><i class="fas fa-check text-success"></i> Dedicated project management</li>
                </ul>
                
                <hr>
                
                <h6 class="text-primary">Business Benefits:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-star text-warning"></i> Priority processing</li>
                    <li><i class="fas fa-star text-warning"></i> Dedicated project manager</li>
                    <li><i class="fas fa-star text-warning"></i> Flexible scheduling</li>
                    <li><i class="fas fa-star text-warning"></i> Business invoicing</li>
                </ul>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-phone"></i> Need Help?
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Business Support:</strong></p>
                <p class="mb-1"><i class="fas fa-phone text-success"></i> +63 2 1234 5678</p>
                <p class="mb-1"><i class="fas fa-envelope text-info"></i> business@uphocare.com</p>
                <p class="mb-0"><i class="fas fa-clock text-warning"></i> Mon-Fri: 9 AM - 6 PM</p>
            </div>
        </div>
    </div>
</div>

<script>
// Service category and type filtering
document.getElementById('service_category').addEventListener('change', function() {
    const categoryId = this.value;
    const serviceTypeSelect = document.getElementById('service_type');
    
    if (categoryId) {
        // Load service types for selected category
        fetch('<?php echo BASE_URL; ?>customer/getServiceTypes?category_id=' + categoryId)
        .then(response => response.json())
        .then(data => {
            serviceTypeSelect.innerHTML = '<option value="">Select Service Type</option>';
            if (data.success && data.data.length > 0) {
                data.data.forEach(serviceType => {
                    const option = document.createElement('option');
                    option.value = serviceType.name;
                    option.textContent = serviceType.name;
                    serviceTypeSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading service types:', error);
        });
    } else {
        serviceTypeSelect.innerHTML = '<option value="">Select Service Type</option>';
    }
});

// Form validation
document.getElementById('businessReservationForm').addEventListener('submit', function(e) {
    const businessName = document.getElementById('business_name').value;
    const businessType = document.getElementById('business_type').value;
    const contactPerson = document.getElementById('contact_person').value;
    const businessPhone = document.getElementById('business_phone').value;
    const businessAddress = document.getElementById('business_address').value;
    const serviceCategory = document.getElementById('service_category').value;
    const serviceType = document.getElementById('service_type').value;
    const projectName = document.getElementById('project_name').value;
    const urgencyLevel = document.getElementById('urgency_level').value;
    const serviceDescription = document.getElementById('service_description').value;
    
    if (!businessName || !businessType || !contactPerson || !businessPhone || !businessAddress || 
        !serviceCategory || !serviceType || !projectName || !urgencyLevel || !serviceDescription) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    // Show confirmation
    if (!confirm('Are you sure you want to submit this business reservation? It will be sent to admin for processing.')) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
