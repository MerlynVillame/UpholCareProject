<?php 
$businessProfile = $data['businessProfile'] ?? null;
$categories = $data['categories'] ?? [];
$stores = $data['stores'] ?? [];
$isApproved = ($businessProfile && $businessProfile['status'] === 'approved');
?>

<!-- Business Reservation Modal -->
<div class="modal fade" id="businessReservationModal" tabindex="-1" role="dialog" aria-labelledby="businessReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title font-weight-bold" id="businessReservationModalLabel">
                    <i class="fas fa-briefcase mr-2"></i> New Business Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <?php if (!$isApproved): ?>
                    <div class="alert alert-danger shadow-sm mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                            <div>
                                <h6 class="font-weight-bold mb-1">Access Restricted</h6>
                                Your business account is not yet approved. Please complete your Business Profile and wait for Super Admin approval before making business reservations.
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Business Mode Notice -->
                    <div class="alert alert-info shadow-sm mb-4 border-left-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Business Reservation:</strong> This request will be sent directly to admin for processing. 
                        You will receive notifications about status updates and scheduling.
                    </div>

                    <form method="POST" action="<?php echo BASE_URL; ?>customer/processBusinessReservation" id="businessReservationModalForm">
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Business Information -->
                                <h6 class="text-success font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-building mr-1"></i> Business Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-dark">Business Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($businessProfile['business_name'] ?? ''); ?>" readonly required>
                                            <input type="hidden" name="business_name" value="<?php echo htmlspecialchars($businessProfile['business_name'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-dark">Business Type</label>
                                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($businessProfile['business_type_name'] ?? ''); ?>" readonly>
                                            <input type="hidden" name="business_type" value="<?php echo htmlspecialchars($businessProfile['business_type_name'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-dark">Contact Person <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="contact_person" placeholder="Project Lead Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-dark">Business Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="business_phone" placeholder="Direct contact number" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-dark">Service Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="business_address" rows="2" placeholder="Full address for service delivery" required></textarea>
                                </div>

                                <!-- Service Information -->
                                <h6 class="text-success font-weight-bold mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-tools mr-1"></i> Service Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-dark">Service Category <span class="text-danger">*</span></label>
                                            <select class="form-control" id="modal_service_category" name="service_category" required>
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
                                            <label class="small font-weight-bold text-dark">Service Type <span class="text-danger">*</span></label>
                                            <select class="form-control" id="modal_service_type" name="service_type" required>
                                                <option value="">Select Service Type</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-dark">Project Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="project_name" placeholder="e.g. Office Lobby Renovation" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="small font-weight-bold text-dark">Urgency <span class="text-danger">*</span></label>
                                            <select class="form-control" name="urgency_level" required>
                                                <option value="">Select Priority</option>
                                                <option value="low">Low - 2+ Weeks</option>
                                                <option value="medium">Medium - 1-2 Weeks</option>
                                                <option value="high">High - Within 1 Week</option>
                                                <option value="urgent">Urgent - Within 3 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-dark">Detailed Scope of Work <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="service_description" rows="3" placeholder="Describe specific requirements, dimensions, or details..." required></textarea>
                                </div>
                            </div>

                            <div class="col-lg-4 border-left">
                                <!-- Scheduling & Budget -->
                                <h6 class="text-success font-weight-bold mb-3 border-bottom pb-2">
                                    <i class="fas fa-calendar-alt mr-1"></i> Project Insights
                                </h6>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-dark">Preferred Start Date</label>
                                    <input type="date" class="form-control" name="preferred_date">
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-dark">Estimated Duration</label>
                                    <select class="form-control" name="estimated_duration">
                                        <option value="">Select Range</option>
                                        <option value="1-3_days">1-3 Days</option>
                                        <option value="1_week">1 Week</option>
                                        <option value="2_weeks">2 Weeks</option>
                                        <option value="1_month">1 Month</option>
                                        <option value="custom">Custom (See Notes)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-dark">Budget Estimate</label>
                                    <select class="form-control" name="budget_range">
                                        <option value="">Select Budget</option>
                                        <option value="under_5k">Under ₱5,000</option>
                                        <option value="5k_10k">₱5,000 - ₱10,000</option>
                                        <option value="10k_25k">₱10,000 - ₱25,000</option>
                                        <option value="25k_50k">₱25,000 - ₱50,000</option>
                                        <option value="over_50k">Over ₱50,000</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-dark">Preferred Service Hub</label>
                                    <select class="form-control" name="store_location">
                                        <option value="">Select Branch</option>
                                        <?php foreach ($stores as $store): ?>
                                            <option value="<?php echo $store['id']; ?>">
                                                <?php echo htmlspecialchars($store['store_name'] . ' - ' . $store['city']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-dark">Special Requirements</label>
                                    <textarea class="form-control" name="special_requirements" rows="3" placeholder="Additional notes..."></textarea>
                                </div>

                                <div class="card bg-light border-0 mt-4">
                                    <div class="card-body p-3">
                                        <h6 class="small font-weight-bold text-primary mb-2">Business Assurance:</h6>
                                        <ul class="small list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success mr-1"></i> Priority Admin Review</li>
                                            <li><i class="fas fa-check text-success mr-1"></i> Corporate Service Terms</li>
                                            <li><i class="fas fa-check text-success mr-1"></i> Dedicated Scheduling</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light px-0 pb-0 pt-3 mt-3 border-top">
                            <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Discard</button>
                            <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Send Reservation Request
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Service category and type filtering for modal
    const categorySelect = document.getElementById('modal_service_category');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            const serviceTypeSelect = document.getElementById('modal_service_type');
            
            if (categoryId) {
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
                });
            } else {
                serviceTypeSelect.innerHTML = '<option value="">Select Service Type</option>';
            }
        });
    }

    // Modal form confirmation
    const modalForm = document.getElementById('businessReservationModalForm');
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            if (!confirm('Submit this business reservation request?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
