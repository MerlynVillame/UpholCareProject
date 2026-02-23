<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content overflow-hidden border-0" style="border-radius: 1rem; box-shadow: 0 1rem 3rem rgba(0,0,0,0.2);">
            <div class="modal-header border-0 p-3" style="background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%); color: white;">
                <h5 class="modal-title font-weight-bold" id="reservationModalLabel">
                    <i class="fas fa-tools mr-2"></i>Create Repair Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="reservationModalBody">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <p class="text-muted">Loading reservation form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Open Reservation Modal
 * @param {number|string} storeId - Optional store ID to pre-select
 * @param {number|string} serviceId - Optional service ID to pre-select
 * @param {string} serviceName - Optional service name to display
 * @param {number|string} colorId - Optional color/fabric ID to pre-select
 * @param {string} colorType - Optional color type (standard/premium)
 */
function openReservationModal(storeId = null, serviceId = null, serviceName = null, colorId = null, colorType = null) {
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#reservationModal').modal('show');
    } else {
        const modalEl = document.getElementById('reservationModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }
    
    // Reset body with spinner
    document.getElementById('reservationModalBody').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <p class="text-muted">Loading reservation form...</p>
        </div>
    `;
    
    // Fetch partial content with parameters
    let url = '<?php echo BASE_URL; ?>customer/newRepairReservationPartial';
    const params = [];
    
    if (storeId) {
        params.push('store_id=' + encodeURIComponent(storeId));
    }
    if (serviceId) {
        params.push('service_id=' + encodeURIComponent(serviceId));
    }
    if (serviceName) {
        params.push('service_name=' + encodeURIComponent(serviceName));
    }
    if (colorId) {
        params.push('color_id=' + encodeURIComponent(colorId));
    }
    if (colorType) {
        params.push('color_type=' + encodeURIComponent(colorType));
    }
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            document.getElementById('reservationModalBody').innerHTML = html;
            
            // Re-execute scripts in the loaded HTML
            const doc = document.getElementById('reservationModalBody');
            const scripts = doc.getElementsByTagName('script');
            for (let i = 0; i < scripts.length; i++) {
                // Execute script content
                try {
                    const scriptVar = scripts[i].innerText || scripts[i].textContent;
                    const newScript = document.createElement('script');
                    newScript.text = scriptVar;
                    document.body.appendChild(newScript).parentNode.removeChild(newScript);
                } catch (e) {
                    console.error('Error executing script:', e);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reservationModalBody').innerHTML = `
                <div class="alert alert-danger m-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Error loading form. Please try again.
                </div>
            `;
        });
}

// Auto-open modal if requested via URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'new_reservation') {
        // Remove param from URL to prevent reopening on reload
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path: newUrl}, '', newUrl);
        
        // Open modal
        openReservationModal();
    }
});
</script>
