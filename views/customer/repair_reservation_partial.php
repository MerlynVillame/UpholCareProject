<?php if (isset($categories) && $categories): ?>
<style>
    /* Modal scrollable fix */
    #reservationModal .modal-body {
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .form-group label {
        font-weight: 600;
        color: #0F3C5F;
    }
    
    .service-info-card, .store-info-card {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .store-info-card {
        border-left: 4px solid #1F4E79;
    }
    
    #modalColorPreview {
        border: 2px solid #e3e6f0;
        border-radius: 0.5rem;
        padding: 1rem;
        background: white;
    }
</style>

<div class="row m-0">
    <div class="col-12 p-4">
        <form id="modalRepairReservationForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Service Category <span class="text-danger">*</span></label>
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
                        <label>Service Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="modal_service_type" name="service_type" required>
                            <option value="">Select Type</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="modalServiceInfo" class="service-info-card" style="display: none;">
                <div class="row">
                    <div class="col-12">
                        <strong>Estimated Days:</strong> <span id="modal_serviceDays">-</span>
                    </div>
                </div>
                <div class="mt-2">
                    <strong>Description:</strong>
                    <p id="modal_serviceDescription" class="mb-0 text-muted small"></p>
                </div>
            </div>

            <div class="form-group">
                <label>Preferred Store Location <span class="text-danger">*</span></label>
                <select class="form-control" id="modal_store_location" name="store_location_id" required>
                    <option value="">Select a store location</option>
                </select>
            </div>

            <div id="modalSelectedStoreInfo" class="store-info-card" style="display: none;">
                <h6 id="modal_selectedStoreName" class="mb-1 font-weight-bold"></h6>
                <p id="modal_selectedStoreAddress" class="mb-1 text-muted small"></p>
                <p id="modal_selectedStoreContact" class="mb-0 text-muted small"></p>
            </div>

            <div class="form-group">
                <label>Service Option <span class="text-danger">*</span></label>
                <select class="form-control" name="service_option" id="modal_service_option" required>
                    <option value="">Select Option</option>
                    <option value="pickup">Pick Up</option>
                    <option value="delivery">Delivery Service (Customer drop-off)</option>
                    <option value="both">Both (Pick Up & Delivery)</option>
                    <option value="walk_in">Walk In</option>
                </select>
            </div>

            <div id="modal_colorSelectionSection" style="display: none;">
                <label>Fabric/Color Selection <span class="text-danger">*</span></label>
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control" id="modal_color_type" name="color_type">
                            <option value="standard">Standard</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <select class="form-control" id="modal_selected_color" name="selected_color_id" required>
                            <option value="">Select Color...</option>
                        </select>
                    </div>
                </div>
                
                <div id="modal_colorPreview" class="mt-3" style="display: none;">
                    <div id="modalColorPreview">
                        <div class="d-flex align-items-center">
                            <div id="modal_colorSwatch" style="width: 50px; height: 50px; border-radius: 8px; border: 1px solid #ddd; background-color: #ccc; margin-right: 15px;"></div>
                            <div>
                                <h6 class="mb-0" id="modal_colorNameDisplay">-</h6>
                                <p class="mb-0 text-muted small" id="modal_colorCodeDisplay">-</p>
                                <span class="text-primary font-weight-bold" id="modal_colorPriceDisplay">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6" id="modal_pickupDateGroup" style="display: none;">
                    <div class="form-group">
                        <label id="modal_dateLabel">Pickup Date</label>
                        <input type="date" class="form-control" name="pickup_date" id="modal_pickup_date" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-6" id="modal_deliveryDateGroup" style="display: none;">
                    <div class="form-group">
                        <label>Drop-off Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="delivery_date" id="modal_delivery_date" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <div id="modal_pickupAddressGroup" style="display: none;">
                <div class="form-group">
                    <label>Pickup Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="pickup_address" id="modal_pickup_address" rows="2"></textarea>
                </div>
            </div>

            <div id="modal_deliveryAddressGroup" style="display: none;">
                <div class="form-group">
                    <label>Delivery Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="delivery_address" id="modal_delivery_address" rows="2"></textarea>
                </div>
            </div>

            <div class="form-group mt-3">
                <label>Additional Notes</label>
                <textarea class="form-control" name="notes" rows="2" placeholder="Any special instructions..."></textarea>
            </div>

            <input type="hidden" name="total_amount" id="modal_total_amount" value="0">

            <div class="text-right mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ml-2 px-4 shadow-sm" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); border: none;">
                    Confirm Reservation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const services = <?php echo json_encode($services); ?>;
    const userAddress = <?php echo json_encode($userAddress); ?>;
    let stores = [];
    
    // Category mapping
    const categoryServiceNames = {};
    services.forEach(service => {
        const categoryId = parseInt(service.category_id);
        if (!categoryServiceNames[categoryId]) categoryServiceNames[categoryId] = [];
        if (service.service_type === 'Vehicle Upholstery' && service.service_name === 'Motor Seat') {
            categoryServiceNames[categoryId].push({name: service.service_name, service: service});
        } else if (service.service_type !== 'Vehicle Upholstery') {
            categoryServiceNames[categoryId].push({name: service.service_name, service: service});
        }
    });

    // Populate stores
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
            }
        });

    // Event Listeners
    document.getElementById('modal_service_category').addEventListener('change', function() {
        const catId = parseInt(this.value);
        const typeSelect = document.getElementById('modal_service_type');
        typeSelect.innerHTML = '<option value="">Select Type</option>';
        document.getElementById('modalServiceInfo').style.display = 'none';
        
        if (categoryServiceNames[catId]) {
            categoryServiceNames[catId].forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.name;
                opt.textContent = item.name;
                typeSelect.appendChild(opt);
            });
        }
    });

    document.getElementById('modal_service_type').addEventListener('change', function() {
        const catId = parseInt(document.getElementById('modal_service_category').value);
        const name = this.value;
        const info = document.getElementById('modalServiceInfo');
        if (catId && name) {
            const s = services.find(x => parseInt(x.category_id) === catId && x.service_name === name);
            if (s) {
                document.getElementById('modal_serviceDays').textContent = '7 days';
                document.getElementById('modal_serviceDescription').textContent = s.description;
                document.getElementById('modal_total_amount').value = s.price;
                info.style.display = 'block';
            } else info.style.display = 'none';
        } else info.style.display = 'none';
    });

    document.getElementById('modal_store_location').addEventListener('change', function() {
        const storeId = this.value;
        const info = document.getElementById('modalSelectedStoreInfo');
        const colorSec = document.getElementById('modal_colorSelectionSection');
        if (storeId) {
            const s = stores.find(x => x.id == storeId);
            if (s) {
                document.getElementById('modal_selectedStoreName').textContent = s.store_name;
                document.getElementById('modal_selectedStoreAddress').textContent = `${s.address}, ${s.city}`;
                document.getElementById('modal_selectedStoreContact').textContent = `Phone: ${s.phone}`;
                info.style.display = 'block';
                colorSec.style.display = 'block';
                loadColors(storeId);
            }
        } else {
            info.style.display = 'none';
            colorSec.style.display = 'none';
        }
    });

    function loadColors(storeId, type = 'standard') {
        const url = `<?php echo BASE_URL; ?>customer/getAvailableColors?store_id=${storeId}&fabric_type=${type}`;
        fetch(url).then(r => r.json()).then(data => {
            const select = document.getElementById('modal_selected_color');
            select.innerHTML = '<option value="">Select Color...</option>';
            if (data.success && data.colors) {
                data.colors.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    const p = parseFloat(c.price_per_meter || 0);
                    opt.textContent = `${c.color_name} (${c.color_code}) - ₱${p.toFixed(2)}`;
                    opt.dataset.hex = c.color_hex;
                    opt.dataset.name = c.color_name;
                    opt.dataset.code = c.color_code;
                    opt.dataset.price = p;
                    select.appendChild(opt);
                });
            }
        });
    }

    document.getElementById('modal_color_type').addEventListener('change', function() {
        const storeId = document.getElementById('modal_store_location').value;
        if (storeId) loadColors(storeId, this.value);
    });

    document.getElementById('modal_selected_color').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const preview = document.getElementById('modal_colorPreview');
        if (this.value && opt.dataset.name) {
            document.getElementById('modal_colorSwatch').style.backgroundColor = opt.dataset.hex;
            document.getElementById('modal_colorNameDisplay').textContent = opt.dataset.name;
            document.getElementById('modal_colorCodeDisplay').textContent = `Code: ${opt.dataset.code}`;
            document.getElementById('modal_colorPriceDisplay').textContent = `₱${parseFloat(opt.dataset.price).toFixed(2)}`;
            preview.style.display = 'block';
        } else preview.style.display = 'none';
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

    // Form Submission
    document.getElementById('modalRepairReservationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('<?php echo BASE_URL; ?>customer/processBookingAjax', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Show success message using standard alert or custom dialog
                alert(data.message);
                $('#reservationModal').modal('hide');
                window.location.reload(); // Reload to show new booking in table
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong. Please try again.');
        });
    });
})();
</script>
<?php else: ?>
<div class="alert alert-warning m-3">Form data missing.</div>
<?php endif; ?>
