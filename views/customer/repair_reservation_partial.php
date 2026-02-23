<?php if (isset($categories) && $categories): ?>
<style>
    /* Professional Modal Styling */
    #reservationModal .modal-body {
        max-height: 85vh;
        overflow-y: auto;
        padding: 0;
        background-color: #fcfcfd;
    }
    
    .reservation-container {
        padding: 1.25rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.4rem;
        border-bottom: 1px solid #edf2f7;
    }

    .section-header i {
        width: 30px;
        height: 30px;
        background: #f0f7ff;
        color: #0F3C5F;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin-right: 10px;
        font-size: 0.9rem;
    }

    .section-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #2D3748;
    }

    .form-group label {
        font-weight: 600;
        color: #4A5568;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .form-control {
        height: 40px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 0.875rem;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .form-control:focus {
        border-color: #0F3C5F;
        box-shadow: 0 0 0 3px rgba(15, 60, 95, 0.1);
    }

    .info-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .store-info-card {
        border-left: 4px solid #1F4E79;
    }

    .service-info-card {
        border-left: 4px solid #48BB78;
    }
    
    .color-preview-box {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .color-preview-box:hover {
        border-color: #0F3C5F;
        background: #f8fbff;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .color-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
        gap: 12px;
        max-height: 220px;
        overflow-y: auto;
        padding: 1.25rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-top: 0.5rem;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    .color-item-opt {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.2s;
        margin: 0 auto;
    }

    .color-item-opt:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .color-item-opt.selected {
        border-color: #0F3C5F;
        transform: scale(1.1);
    }

    .btn-confirm {
        background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(15, 60, 95, 0.2);
    }

    .btn-confirm:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(15, 60, 95, 0.3);
        color: white;
    }

    .loading-spinner {
        color: #0F3C5F;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fade-section {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .filter-pills {
        display: flex;
        gap: 8px;
        margin-bottom: 1rem;
    }

    .filter-pill {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid #e2e8f0;
        background: white;
        color: #718096;
        transition: all 0.2s;
    }

    .filter-pill.active {
        background: #0F3C5F;
        color: white;
        border-color: #0F3C5F;
    }
</style>

<div class="reservation-container">
    <form id="modalRepairReservationForm">
        <!-- Section 1: Store -->
        <div class="section-header">
            <i class="fas fa-store"></i>
            <h5>Preferred Store</h5>
        </div>
        
        <div class="form-group mb-4">
            <select class="form-control" id="modal_store_location" name="store_location_id" required>
                <option value="">Select a store location to see available services</option>
            </select>
            <div id="modalStoreLoading" class="mt-2" style="display: none;">
                <div class="loading-spinner">
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span>Optimizing options for this location...</span>
                </div>
            </div>
        </div>

        <div id="modalSelectedStoreInfo" class="info-card store-info-card fade-section" style="display: none;">
            <div class="d-flex align-items-start">
                <i class="fas fa-map-marker-alt text-primary mt-1 mr-3"></i>
                <div>
                    <h6 id="modal_selectedStoreName" class="mb-1 font-weight-bold"></h6>
                    <p id="modal_selectedStoreAddress" class="mb-1 text-muted small"></p>
                    <div id="modal_selectedStoreContact" class="badge badge-light text-muted p-2"></div>
                </div>
            </div>
        </div>

        <!-- Section 2: Service -->
        <div id="modalServiceSection" class="fade-section" style="opacity: 0.4; pointer-events: none;">
            <div class="section-header">
                <i class="fas fa-tools"></i>
                <h5>Select Service</h5>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" id="modal_service_category" name="service_category" required disabled>
                            <option value="">Choose category</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Service Type</label>
                        <select class="form-control" id="modal_service_type" name="service_type" required disabled>
                            <option value="">Select type</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="modalServiceInfo" class="info-card service-info-card" style="display: none;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="badge badge-success px-2 py-1" style="border-radius: 20px; font-size: 0.75rem;">
                        <i class="far fa-clock mr-1"></i> <span id="modal_serviceDays">7 days</span> est.
                    </div>
                    <div class="text-right">
                        <span class="text-muted small d-block" style="font-size: 0.75rem;">Starting from</span>
                        <span class="font-weight-bold text-dark mb-0" style="font-size: 1.1rem;">₱<span id="modal_display_price">0</span></span>
                    </div>
                </div>
                <div class="mt-2">
                    <p id="modal_serviceDescription" class="mb-0 text-muted small" style="line-height: 1.5;"></p>
                </div>
            </div>
        </div>

        <!-- Section 3: Fabric -->
        <div id="modalInventorySection" class="fade-section mt-4" style="opacity: 0.4; pointer-events: none;">
            <div class="section-header">
                <i class="fas fa-palette"></i>
                <h5>Fabric & Color</h5>
            </div>
            
            <div class="color-preview-box mb-2" onclick="toggleColorGrid()">
                <div class="d-flex align-items-center">
                    <div id="selectedColorWheel" style="width: 45px; height: 45px; border-radius: 10px; background-color: #f1f5f9; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-right: 15px;"></div>
                    <div>
                        <div id="selectedColorName" class="font-weight-bold text-muted" style="font-size: 1rem;">No color selected</div>
                        <div id="selectedColorDetails" class="small text-muted">Browse available inventory for this store</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-muted small"></i>
            </div>

            <div id="modalColorGridContainer" style="display: none; animation: slideDown 0.3s ease-out;">
                <div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-1">
                    <span class="small font-weight-bold text-muted">Stock availability:</span>
                    <div class="filter-pills">
                        <div class="filter-pill active" id="btnFilterAll">All</div>
                        <div class="filter-pill" id="btnFilterStandard">Standard</div>
                        <div class="filter-pill" id="btnFilterPremium">Premium</div>
                    </div>
                </div>
                <div id="modalColorGrid" class="color-grid">
                    <div class="text-center w-100 p-4 text-muted small">
                        <i class="fas fa-store-slash d-block mb-2 h4"></i>
                        Please select a store location first
                    </div>
                </div>
            </div>

            <input type="hidden" name="selected_color_id" id="modal_selected_color_id" value="">
            <input type="hidden" name="color_type" id="modal_color_type" value="">
        </div>

        <!-- Section 4: Logistic -->
        <div class="section-header mt-4">
            <i class="fas fa-truck"></i>
            <h5>Logistic Options</h5>
        </div>

        <div class="form-group mb-4">
            <label>How should we collect/return your items?</label>
            <select class="form-control" name="service_option" id="modal_service_option" required>
                <option value="">Select an option</option>
                <option value="pickup">Pick Up Service</option>
                <option value="delivery">Delivery Service (Customer Drop-off)</option>
                <option value="both">Both (Pick Up & Return Delivery)</option>
                <option value="walk_in">Self Service (Walk-in)</option>
            </select>
        </div>

        <div class="row fade-section" id="modalDateTimeSection">
            <div class="col-md-6" id="modal_pickupDateGroup" style="display: none;">
                <div class="form-group">
                    <label id="modal_dateLabel">Pickup Date</label>
                    <input type="date" class="form-control" name="pickup_date" id="modal_pickup_date" min="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="col-md-6" id="modal_deliveryDateGroup" style="display: none;">
                <div class="form-group">
                    <label>Drop-off Date</label>
                    <input type="date" class="form-control" name="delivery_date" id="modal_delivery_date" min="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
        </div>

        <div class="fade-section" id="modalAddressSection">
            <div id="modal_pickupAddressGroup" style="display: none;">
                <div class="form-group">
                    <label>Collection Address</label>
                    <textarea class="form-control" name="pickup_address" id="modal_pickup_address" rows="2" placeholder="Where should we pick up?"></textarea>
                </div>
            </div>

            <div id="modal_deliveryAddressGroup" style="display: none;">
                <div class="form-group">
                    <label>Return Address</label>
                    <textarea class="form-control" name="delivery_address" id="modal_delivery_address" rows="2" placeholder="Where should we deliver?"></textarea>
                </div>
            </div>
        </div>

        <div class="form-group mt-4">
            <label>Additional Notes</label>
            <textarea class="form-control" name="notes" rows="2" style="height: auto;" placeholder="Any specific requests or instructions for our team?"></textarea>
        </div>

        <input type="hidden" name="total_amount" id="modal_total_amount" value="0">

        <div class="text-right mt-4 pt-3 border-top">
            <button type="button" class="btn btn-link text-muted mr-3" data-dismiss="modal">Discard</button>
            <button type="submit" class="btn btn-confirm py-2 px-4" style="padding-left: 2.5rem !important; padding-right: 2.5rem !important;">
                Confirm Reservation
            </button>
        </div>
    </form>
</div>

<script>
(function() {
    const userAddress = <?php echo json_encode($userAddress); ?>;
    const preSelectedStoreId = <?php echo json_encode(isset($_GET['store_id']) ? $_GET['store_id'] : null); ?>;
    const preSelectedCategoryId = <?php echo json_encode($preSelectedCategoryId); ?>;
    const preSelectedServiceType = <?php echo json_encode($preSelectedServiceType); ?>;
    const preSelectedColor = <?php echo json_encode($preSelectedColor); ?>;
    const preSelectedColorType = <?php echo json_encode($preSelectedColorType); ?>;
    
    let stores = [];
    let currentStoreServices = [];
    let currentStoreColors = [];
    
    initForm();

    function initForm() {
        // Fetch Stores
        fetch('<?php echo BASE_URL; ?>customer/getStoreLocations')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    stores = data.data;
                    const select = document.getElementById('modal_store_location');
                    stores.forEach(store => {
                        const opt = document.createElement('option');
                        opt.value = store.id;
                        opt.textContent = `${store.store_name} - ${store.city}`;
                        select.appendChild(opt);
                    });
                    
                    if (preSelectedStoreId) {
                        select.value = preSelectedStoreId;
                        select.dispatchEvent(new Event('change'));
                    }
                }
            });

        if (preSelectedColor) {
            selectColor(preSelectedColor, preSelectedColorType || 'standard');
        }
    }

    window.toggleColorGrid = function() {
        const grid = document.getElementById('modalColorGridContainer');
        const icon = document.querySelector('.color-preview-box i');
        if (grid.style.display === 'none') {
            grid.style.display = 'block';
            icon.className = 'fas fa-chevron-up text-muted small';
        } else {
            grid.style.display = 'none';
            icon.className = 'fas fa-chevron-down text-muted small';
        }
    };

    function selectColor(color, type) {
        document.getElementById('modal_selected_color_id').value = color.id;
        document.getElementById('modal_color_type').value = type;
        
        const wheel = document.getElementById('selectedColorWheel');
        wheel.style.backgroundColor = color.color_hex;
        
        const nameDisplay = document.getElementById('selectedColorName');
        nameDisplay.textContent = color.color_name;
        nameDisplay.classList.remove('text-muted');
        
        const detailsDisplay = document.getElementById('selectedColorDetails');
        detailsDisplay.textContent = `${color.color_code} • ${type.charAt(0).toUpperCase() + type.slice(1)}`;
        
        document.querySelectorAll('.color-item-opt').forEach(el => el.classList.remove('selected'));
        const selectedEl = document.querySelector(`.color-item-opt[data-id="${color.id}"]`);
        if (selectedEl) selectedEl.classList.add('selected');
        
        document.getElementById('modalColorGridContainer').style.display = 'none';
        document.querySelector('.color-preview-box i').className = 'fas fa-chevron-down text-muted small';
    }

    document.getElementById('modal_store_location').addEventListener('change', function() {
        const storeId = this.value;
        const info = document.getElementById('modalSelectedStoreInfo');
        const serviceSection = document.getElementById('modalServiceSection');
        const inventorySection = document.getElementById('modalInventorySection');
        const loading = document.getElementById('modalStoreLoading');
        
        if (!storeId) {
            info.style.display = 'none';
            serviceSection.style.opacity = '0.4';
            serviceSection.style.pointerEvents = 'none';
            inventorySection.style.opacity = '0.4';
            inventorySection.style.pointerEvents = 'none';
            return;
        }

        loading.style.display = 'block';
        
        const s = stores.find(x => x.id == storeId);
        if (s) {
            document.getElementById('modal_selectedStoreName').textContent = s.store_name;
            document.getElementById('modal_selectedStoreAddress').textContent = `${s.address}, ${s.city}`;
            document.getElementById('modal_selectedStoreContact').innerHTML = `<i class="fas fa-phone-alt mr-1"></i> ${s.phone}`;
            info.style.display = 'block';
        }

        fetch(`<?php echo BASE_URL; ?>customer/getStoreServicesAjax?store_id=${storeId}`)
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';
                if (data.success) {
                    currentStoreServices = data.services;
                    const catSelect = document.getElementById('modal_service_category');
                    catSelect.innerHTML = '<option value="">Choose category</option>';
                    catSelect.disabled = false;
                    
                    data.categories.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id;
                        opt.textContent = cat.name;
                        catSelect.appendChild(opt);
                    });

                    serviceSection.style.opacity = '1';
                    serviceSection.style.pointerEvents = 'auto';
                    
                    if (preSelectedCategoryId) {
                        catSelect.value = preSelectedCategoryId;
                        catSelect.dispatchEvent(new Event('change'));
                    }
                }
            });

        fetch(`<?php echo BASE_URL; ?>customer/getAvailableColors?store_id=${storeId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentStoreColors = data.colors;
                    renderColors('all');
                    inventorySection.style.opacity = '1';
                    inventorySection.style.pointerEvents = 'auto';
                }
            });
    });

    function renderColors(filter) {
        const grid = document.getElementById('modalColorGrid');
        grid.innerHTML = '';
        
        let filtered = currentStoreColors;
        if (filter !== 'all') {
            filtered = currentStoreColors.filter(c => {
                const type = (c.fabric_type || c.leather_type || 'standard').toLowerCase();
                return type === filter;
            });
        }

        if (filtered.length === 0) {
            grid.innerHTML = '<div class="text-center w-100 p-4 text-muted small"><i class="fas fa-search d-block mb-1"></i> No matching colors</div>';
            return;
        }

        filtered.forEach(c => {
            const div = document.createElement('div');
            const type = (c.fabric_type || c.leather_type || 'standard').toLowerCase();
            const colorDiv = document.createElement('div');
            colorDiv.className = 'color-item-opt';
            colorDiv.style.backgroundColor = c.color_hex;
            colorDiv.title = `${c.color_name} (${c.color_code})`;
            colorDiv.dataset.id = c.id;
            
            if (document.getElementById('modal_selected_color_id').value == c.id) {
                colorDiv.classList.add('selected');
            }

            colorDiv.onclick = () => selectColor(c, type);
            div.appendChild(colorDiv);
            grid.appendChild(div);
        });
    }

    document.getElementById('modal_service_category').addEventListener('change', function() {
        const catId = this.value;
        const typeSelect = document.getElementById('modal_service_type');
        typeSelect.innerHTML = '<option value="">Select type</option>';
        typeSelect.disabled = !catId;
        document.getElementById('modalServiceInfo').style.display = 'none';
        
        if (catId) {
            const types = [...new Set(currentStoreServices
                .filter(s => s.category_id == catId)
                .map(s => s.service_name))];
            
            types.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t;
                opt.textContent = t;
                typeSelect.appendChild(opt);
            });

            if (preSelectedServiceType) {
                typeSelect.value = preSelectedServiceType;
                typeSelect.dispatchEvent(new Event('change'));
            }
        }
    });

    document.getElementById('modal_service_type').addEventListener('change', function() {
        const catId = document.getElementById('modal_service_category').value;
        const name = this.value;
        const info = document.getElementById('modalServiceInfo');
        
        if (catId && name) {
            const s = currentStoreServices.find(x => x.category_id == catId && x.service_name === name);
            if (s) {
                document.getElementById('modal_display_price').textContent = parseFloat(s.price).toLocaleString();
                document.getElementById('modal_serviceDescription').textContent = s.description;
                document.getElementById('modal_total_amount').value = s.price;
                info.style.display = 'block';
            } else info.style.display = 'none';
        } else info.style.display = 'none';
    });

    ['All', 'Standard', 'Premium'].forEach(f => {
        document.getElementById(`btnFilter${f}`).addEventListener('click', function() {
            document.querySelectorAll('.filter-pill').forEach(x => x.classList.remove('active'));
            this.classList.add('active');
            renderColors(f.toLowerCase());
        });
    });

    document.getElementById('modal_service_option').addEventListener('change', function() {
        const opt = this.value;
        const pDate = document.getElementById('modal_pickupDateGroup');
        const dDate = document.getElementById('modal_deliveryDateGroup');
        const pAddr = document.getElementById('modal_pickupAddressGroup');
        const dAddr = document.getElementById('modal_deliveryAddressGroup');
        
        [pDate, dDate, pAddr, dAddr].forEach(x => x.style.display = 'none');
        
        const pDateInput = document.getElementById('modal_pickup_date');
        const dDateInput = document.getElementById('modal_delivery_date');
        const pAddrInput = document.getElementById('modal_pickup_address');
        const dAddrInput = document.getElementById('modal_delivery_address');
        
        [pDateInput, dDateInput, pAddrInput, dAddrInput].forEach(x => x.required = false);

        if (opt === 'pickup' || opt === 'both') {
            pDate.style.display = 'block';
            pAddr.style.display = 'block';
            pDateInput.required = true;
            pAddrInput.required = true;
            if (opt === 'both') {
                dAddr.style.display = 'block';
                dAddrInput.required = true;
                if (!dAddrInput.value) dAddrInput.value = userAddress;
            }
        } else if (opt === 'delivery') {
            dDate.style.display = 'block';
            dDateInput.required = true;
        }
    });

    // Logistic Capacity Real-time Check
    const pickupDateInput = document.getElementById('modal_pickup_date');
    const deliveryDateInput = document.getElementById('modal_delivery_date');
    const storeSelect = document.getElementById('modal_store_location');

    function checkDateAvailability(input, type) {
        const date = input.value;
        const storeId = storeSelect.value;
        if (!date || !storeId) return;

        // Visual feedback - loading
        input.classList.remove('is-invalid', 'is-valid');
        const parent = input.closest('.form-group');
        let feedback = parent.querySelector('.availability-feedback');
        if (!feedback) {
            feedback = document.createElement('small');
            feedback.className = 'availability-feedback mt-1 d-block';
            parent.appendChild(feedback);
        }
        feedback.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Checking availability...';
        feedback.className = 'availability-feedback mt-1 d-block text-muted';

        fetch(`<?php echo BASE_URL; ?>customer/checkLogisticAvailability?store_id=${storeId}&date=${date}&type=${type}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.available) {
                        input.classList.add('is-valid');
                        feedback.innerHTML = `<i class="fas fa-check-circle mr-1"></i> Available (${data.remaining} slots left)`;
                        feedback.className = 'availability-feedback mt-1 d-block text-success small';
                    } else {
                        input.classList.add('is-invalid');
                        feedback.innerHTML = `<i class="fas fa-times-circle mr-1"></i> Fully booked for ${type}. Please choose another date.`;
                        feedback.className = 'availability-feedback mt-1 d-block text-danger small';
                        // reset value after alert
                        // input.value = '';
                    }
                }
            })
            .catch(err => {
                feedback.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Could not check availability.';
                feedback.className = 'availability-feedback mt-1 d-block text-warning small';
            });
    }

    pickupDateInput.addEventListener('change', () => checkDateAvailability(pickupDateInput, 'pickup'));
    deliveryDateInput.addEventListener('change', () => checkDateAvailability(deliveryDateInput, 'inspection'));

    document.getElementById('modalRepairReservationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!document.getElementById('modal_selected_color_id').value) {
            showNotification('error', 'Please select a fabric/color to continue.');
            return;
        }

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Finalizing...';
        
        fetch('<?php echo BASE_URL; ?>customer/processBookingAjax', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message || 'Reservation confirmed! We will contact you soon.');
                setTimeout(() => {
                    $('#reservationModal').modal('hide');
                    setTimeout(() => window.location.reload(), 500);
                }, 1500);
            } else {
                showNotification('error', data.message || 'Failed to process reservation.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        })
        .catch(err => {
            showNotification('error', 'Communication error. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
    
    function showNotification(type, message) {
        const existing = document.querySelector('.booking-notification');
        if (existing) existing.remove();
        
        const notification = document.createElement('div');
        notification.className = 'booking-notification';
        notification.style.cssText = `
            position: fixed; top: 20px; right: 20px; z-index: 99999;
            min-width: 320px; padding: 1.25rem;
            border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
            display: flex; align-items: center; gap: 1rem; color: white;
            background: ${type === 'success' ? '#2f855a' : '#c53030'};
        `;
        
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}" style="font-size: 1.5rem;"></i>
            <div>
                <strong style="display: block; font-size: 1rem;">${type === 'success' ? 'Confirmed' : 'Error'}</strong>
                <p style="margin: 0; font-size: 0.85rem; opacity: 0.9;">${message}</p>
            </div>
        `;
        
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            notification.style.opacity = '0';
            notification.style.transition = 'all 0.5s ease-in';
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    }
})();
</script>
<?php else: ?>
<div class="alert alert-soft-warning m-4 border-0 shadow-sm" style="border-radius: 12px;">
    <i class="fas fa-info-circle mr-2"></i> Service initialization required. Please refresh.
</div>
<?php endif; ?>
