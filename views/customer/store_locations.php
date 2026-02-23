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
</style>

<!-- Tab Container for Heading and Actions -->
<div class="welcome-container shadow-sm">
    <div class="welcome-text">
        <i class="fas fa-store-alt mr-2" style="color: #0F3C5F;"></i>
        Store Locations
    </div>
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-primary btn-sm px-4 shadow-sm" onclick="getCurrentLocation(event)" style="border-radius: 50px; font-weight: 600; font-size: 0.8rem; height: 38px;">
            <i class="fas fa-map-marker-alt mr-1"></i> Use My Location
        </button>
        <button type="button" class="btn btn-primary btn-sm px-4 shadow-sm ml-2" onclick="openReservationModal()" style="border-radius: 50px; font-weight: 600; font-size: 0.8rem; height: 38px;">
            <i class="fas fa-plus mr-1"></i> Book Service
        </button>
    </div>
</div>

<!-- Return to Repair Reservation Notice -->
<div id="returnNotice" class="alert alert-info mb-4" style="display: none;">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle fa-2x mr-3"></i>
        <div>
            <h6 class="mb-1">Selecting Store for Repair Reservation</h6>
            <p class="mb-0">After selecting a store below, you'll be returned to your repair reservation form with all your information preserved.</p>
        </div>
    </div>
</div>

<!-- Location Search -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Search Stores</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="searchInput">Search by store name, address, or city:</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Enter search term...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cityFilter">Filter by City:</label>
                    <select class="form-control" id="cityFilter">
                        <option value="">All Cities</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary" onclick="searchStores()">
                    <i class="fas fa-search"></i> Search Stores
                </button>
                <button class="btn btn-secondary" onclick="loadAllStores()">
                    <i class="fas fa-map"></i> Show All Stores on Map
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Map Container -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Store Locations Map - Bohol Province</h6>
        <small class="text-muted">
            <i class="fas fa-info-circle"></i> 
            Interactive map showing store locations in Bohol Province (Powered by Leaflet & OpenStreetMap)
        </small>
        <div class="mt-2">
            <small class="text-muted">
                <strong>Map Legend:</strong>
                <span class="badge badge-success ml-1">🟢 Excellent (4.5+)</span>
                <span class="badge badge-warning ml-1">🟡 Good (4.0+)</span>
                <span class="badge badge-secondary ml-1">⚪ Fair (Below 4.0)</span>
            </small>
        </div>
    </div>
    <div class="card-body">
        <div id="map" style="height: 500px; width: 100%; border-radius: 8px;"></div>
    </div>
</div>

<!-- Highest Rated Stores Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-star text-warning"></i> Highest Rated Stores in Bohol
            </h6>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Stores are sorted by rating (highest first) to help you find the best service quality in Bohol Province.
            </small>
        </div>
        <button class="btn btn-sm btn-primary mt-2 mt-md-0" onclick="loadAllStores()" title="Refresh stores list">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
    <div class="card-body">
        <div id="storesList">
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2">Loading stores...</p>
            </div>
        </div>
    </div>
</div>

<!-- Store Details Modal -->
<div class="modal fade" id="storeDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="storeDetailsTitle">Store Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="storeDetailsBody">
                <!-- Store details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="selectStoreBtn" onclick="selectStore()">
                    <i class="fas fa-check"></i> Select This Store
                </button>
            </div> 
        </div>
    </div>
</div>

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Report Store Issue
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="report_store_id">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> Your report will be sent directly to the super admin for review. Please provide accurate information.
                </div>
                
                <div class="form-group">
                    <label><strong>Select Issue Types (Check all that apply):</strong></label>
                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="safety" id="issue_safety" name="issue_types[]">
                            <label class="form-check-label" for="issue_safety">
                                <i class="fas fa-shield-alt text-danger"></i> Safety Issues (Unsafe conditions, hazards)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="hygiene" id="issue_hygiene" name="issue_types[]">
                            <label class="form-check-label" for="issue_hygiene">
                                <i class="fas fa-soap text-warning"></i> Hygiene Issues (Unclean premises, poor sanitation)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="quality" id="issue_quality" name="issue_types[]">
                            <label class="form-check-label" for="issue_quality">
                                <i class="fas fa-award text-info"></i> Quality Issues (Poor workmanship, defective services)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="service" id="issue_service" name="issue_types[]">
                            <label class="form-check-label" for="issue_service">
                                <i class="fas fa-user-tie text-primary"></i> Service Issues (Poor customer service, unprofessional behavior)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="pricing" id="issue_pricing" name="issue_types[]">
                            <label class="form-check-label" for="issue_pricing">
                                <i class="fas fa-dollar-sign text-success"></i> Pricing Issues (Overcharging, hidden fees)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="other" id="issue_other" name="issue_types[]">
                            <label class="form-check-label" for="issue_other">
                                <i class="fas fa-ellipsis-h text-secondary"></i> Other Issues
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="report_description"><strong>Description:</strong> <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="report_description" rows="4" 
                              placeholder="Please provide details about the issue(s) you encountered..." required></textarea>
                    <small class="form-text text-muted">Be as specific as possible to help us address the issue.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitReportIssue()">
                    <i class="fas fa-paper-plane mr-1"></i> Submit Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for store selection -->
<form id="storeSelectionForm" method="POST" action="<?php echo BASE_URL; ?>customer/newBooking" style="display: none;">
    <input type="hidden" name="selected_store_id" id="selectedStoreId">
</form>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

<script>
let map;
let markers = [];
let selectedStoreId = null;
let userLocation = null;
let userMarker = null;
let stores = []; // Global stores array

// Helper function to get marker icon color based on rating
function getMarkerIcon(rating) {
    let color = '#1F4E79'; // Default brown
    if (rating) {
        const ratingValue = parseFloat(rating);
        if (ratingValue >= 4.5) {
            color = '#28a745'; // Green for excellent
        } else if (ratingValue >= 4.0) {
            color = '#ffc107'; // Yellow for good
        } else {
            color = '#6c757d'; // Gray for fair
        }
    }
    
    // Create custom icon using Leaflet's DivIcon
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="
            background-color: white;
            border: 3px solid ${color};
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
        ">🏪${rating && parseFloat(rating) >= 4.5 ? '<span style="position: absolute; top: -5px; right: -5px; background: #28a745; color: white; border-radius: 50%; width: 14px; height: 14px; font-size: 9px; display: flex; align-items: center; justify-content: center;">★</span>' : ''}</div>`,
        iconSize: [35, 35],
        iconAnchor: [17, 35],
        popupAnchor: [0, -35]
    });
}

// Initialize Leaflet map
function initMap() {
    try {
        // Default center (Bohol, Philippines) - Tagbilaran City area
        const defaultCenter = [9.6576, 123.8544]; // Tagbilaran City, Bohol
        const defaultZoom = 10; // Zoom level to show Bohol province
        
        // Bohol province bounds (approximate)
        // North: 10.2, South: 9.5, East: 124.4, West: 123.6
        const boholBounds = [
            [9.5, 123.6],   // Southwest corner
            [10.2, 124.4]   // Northeast corner
        ];
        
        // Initialize the map centered on Bohol with restricted bounds
        map = L.map('map', {
            center: defaultCenter,
            zoom: defaultZoom,
            maxBounds: boholBounds, // Restrict map to Bohol area
            maxBoundsViscosity: 1.0 // Prevent panning outside Bohol (1.0 = completely locked)
        });
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 9 // Prevent zooming out too far from Bohol
        }).addTo(map);
        
        // Set view to ensure map is centered on Bohol
        map.setView(defaultCenter, defaultZoom);
        
        // Load all stores after map is ready
        loadAllStores();
    } catch (error) {
        console.error('Error initializing map:', error);
        document.getElementById('map').innerHTML = `
            <div class="alert alert-danger text-center" style="padding: 40px; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                <i class="fas fa-exclamation-triangle fa-3x mb-3 text-danger"></i>
                <h5>Map Error</h5>
                <p>An error occurred while loading the map. Store locations are still available below.</p>
                <button class="btn btn-primary btn-sm mt-2" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Reload Page
                </button>
            </div>
        `;
        // Load stores even if map fails
        loadAllStores();
    }
}

// Get user's current location
function getCurrentLocation(event) {
    if (!map) {
        alert('Map is not available. Please wait for the map to load.');
        return;
    }
    
    if (navigator.geolocation) {
        // Show loading message if event is provided
        let btn = null;
        let originalText = '';
        if (event && event.target) {
            btn = event.target;
            originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting Location...';
            btn.disabled = true;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Check if user is within Bohol bounds
                const userInBohol = userLocation.lat >= 9.5 && userLocation.lat <= 10.2 && 
                                    userLocation.lng >= 123.6 && userLocation.lng <= 124.4;
                
                // Center map on user location if in Bohol, otherwise center on Bohol
                if (map) {
                    if (userInBohol) {
                        // User is in Bohol, center on their location
                        map.setView([userLocation.lat, userLocation.lng], 15);
                        
                        // Remove existing user marker if any
                        if (userMarker) {
                            map.removeLayer(userMarker);
                        }
                        
                        // Add user location marker
                        userMarker = L.marker([userLocation.lat, userLocation.lng], {
                            icon: L.divIcon({
                                className: 'user-location-marker',
                                html: '<div style="background-color: #1F4E79; border: 3px solid white; border-radius: 50%; width: 20px; height: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            })
                        }).addTo(map);
                        
                        userMarker.bindPopup('<strong>Your Location</strong><br>You are in Bohol').openPopup();
                    } else {
                        // User is outside Bohol, center on Bohol and show message
                        map.setView([9.6576, 123.8544], 10);
                        alert('Your location is outside Bohol. The map is showing Bohol province where our stores are located.');
                    }
                }
                
                // Reset button if it exists
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
                
                // Find nearest stores (this will still work even if user is outside Bohol)
                findNearestStores();
            },
            function(error) {
                alert('Error getting location: ' + error.message);
                // Reset button if it exists
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Find nearest stores based on user location
function findNearestStores() {
    if (!userLocation) {
        alert('Please get your location first.');
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>customer/findNearestStores', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `latitude=${userLocation.lat}&longitude=${userLocation.lng}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Filter stores to only include those within Bohol bounds
            const boholStores = data.data.filter(store => {
                if (!store.latitude || !store.longitude) return false;
                const lat = parseFloat(store.latitude);
                const lng = parseFloat(store.longitude);
                // Check if within Bohol bounds
                return lat >= 9.5 && lat <= 10.2 && lng >= 123.6 && lng <= 124.4;
            });
            
            // Sort by rating (highest first) for nearest stores
            boholStores.sort((a, b) => {
                const ratingA = parseFloat(a.rating || 0);
                const ratingB = parseFloat(b.rating || 0);
                return ratingB - ratingA;
            });
            
            // Store in global variable
            stores = boholStores;
            
            // Display stores in list
            displayStores(boholStores);
            
            // Display stores on map if map is available
            if (map) {
                displayStoresOnMap(boholStores);
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error finding nearest stores.');
    });
}

// Load all stores
function loadAllStores() {
    // Show loading state
    const storesList = document.getElementById('storesList');
    storesList.innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading stores...</p>
        </div>
    `;
    
    fetch('<?php echo BASE_URL; ?>customer/getStoreLocations')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (data.data && data.data.length > 0) {
                // Filter stores to only include those within Bohol bounds
                const boholStores = data.data.filter(store => {
                    if (!store.latitude || !store.longitude) return false;
                    const lat = parseFloat(store.latitude);
                    const lng = parseFloat(store.longitude);
                    // Check if within Bohol bounds
                    return lat >= 9.5 && lat <= 10.2 && lng >= 123.6 && lng <= 124.4;
                });
                
                // Sort by rating (highest first)
                boholStores.sort((a, b) => {
                    const ratingA = parseFloat(a.rating || 0);
                    const ratingB = parseFloat(b.rating || 0);
                    return ratingB - ratingA;
                });
                
                // Store in global variable for access in selectStore function
                stores = boholStores;
                
                // Display stores in list (highest rated first)
                displayStores(boholStores);
                
                // Display stores on map if map is available
                if (map) {
                    displayStoresOnMap(boholStores);
                }
                
                loadCities(boholStores);
            } else {
                // Show message if no stores found
                storesList.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-store-slash fa-3x text-muted"></i>
                        <p class="mt-2 text-muted">No stores found in Bohol</p>
                        <small class="text-muted">Please contact administrator to add store locations.</small>
                    </div>
                `;
                
                // Show message on map if no stores found
                if (map) {
                    const noStoresMsg = L.popup()
                        .setLatLng([9.6576, 123.8544])
                        .setContent(`
                            <div style="text-align: center; padding: 10px;">
                                <i class="fas fa-store-slash fa-2x text-muted"></i>
                                <h6>No Stores Found</h6>
                                <p class="text-muted small">No store locations found in Bohol.</p>
                            </div>
                        `);
                    noStoresMsg.openOn(map);
                }
            }
        } else {
            storesList.innerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5>Error Loading Stores</h5>
                    <p>${data.message || 'Unable to load store locations. Please try again later.'}</p>
                </div>
            `;
            console.error('Error loading stores:', data.message);
            // Show error message on map
            if (map) {
                const errorMsg = L.popup()
                    .setLatLng([9.6576, 123.8544])
                    .setContent(`
                        <div style="text-align: center; padding: 10px;">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                            <h6>Error Loading Stores</h6>
                            <p class="text-muted small">${data.message || 'Unable to load store locations.'}</p>
                        </div>
                    `);
                errorMsg.openOn(map);
            }
        }
    })
    .catch(error => {
        console.error('Error loading stores:', error);
        storesList.innerHTML = `
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                <h5>Connection Error</h5>
                <p>Unable to load stores. Please check your internet connection and try again.</p>
                <button class="btn btn-primary btn-sm mt-2" onclick="loadAllStores()">
                    <i class="fas fa-redo"></i> Retry
                </button>
            </div>
        `;
        // Show error message on map
        if (map) {
            const errorMsg = L.popup()
                .setLatLng([9.6576, 123.8544])
                .setContent(`
                    <div style="text-align: center; padding: 10px;">
                        <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                        <h6>Connection Error</h6>
                        <p class="text-muted small">Unable to load stores. Please check your internet connection.</p>
                        <button class="btn btn-primary btn-sm mt-2" onclick="loadAllStores()" style="margin-top: 10px;">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `);
            errorMsg.openOn(map);
        }
    });
}

// Display stores in list (sorted by rating)
function displayStores(stores) {
    const storesList = document.getElementById('storesList');
    
    if (stores.length === 0) {
        storesList.innerHTML = `
            <div class="text-center">
                <i class="fas fa-store-slash fa-3x text-muted"></i>
                <p class="mt-2 text-muted">No stores found in Bohol</p>
            </div>
        `;
        return;
    }
    
    // Stores are already sorted by rating (highest first)
    storesList.innerHTML = stores.map((store, index) => {
        const rating = parseFloat(store.rating || 0);
        const ratingBadgeClass = rating >= 4.5 ? 'badge-success' : rating >= 4.0 ? 'badge-warning' : 'badge-secondary';
        const ratingText = rating >= 4.5 ? 'Excellent' : rating >= 4.0 ? 'Good' : rating > 0 ? 'Fair' : 'No Rating';
        
        return `
            <div class="card mb-3 store-card shadow-sm hover-shadow" style="transition: all 0.3s ease; border-radius: 12px; overflow: hidden; border: 1px solid #e3e6f0;">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            ${index === 0 && rating >= 4.5 ? '<span class="badge badge-warning badge-pill mr-2 shadow-sm" style="font-size: 0.9rem;"><i class="fas fa-crown"></i> #1</span>' : ''}
                            <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 1.1rem;">${store.store_name || 'Store'}</h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge ${ratingBadgeClass} shadow-sm px-2 py-1" style="border-radius: 6px;">
                                <i class="fas fa-star mr-1"></i>${rating.toFixed(1)} ${ratingText}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-7 border-right-md">
                            <div class="mb-2">
                                <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Location Details</small>
                                <p class="mb-1 mt-1 text-dark" style="font-size: 0.9rem;">
                                    <i class="fas fa-map-marker-alt text-primary mr-2" style="width: 16px; text-align: center;"></i> 
                                    ${store.address || 'Address not provided'}
                                </p>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                    <i class="fas fa-city text-info mr-2" style="width: 16px; text-align: center;"></i> 
                                    ${store.city || 'Bohol'}, ${store.province || 'Bohol'}
                                    ${store.distance ? `<span class="ml-2 badge badge-light border"><i class="fas fa-route text-success"></i> ${store.distance.toFixed(2)} km</span>` : ''}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-2">
                                <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Contact Info</small>
                                <p class="mb-1 mt-1" style="font-size: 0.9rem;">
                                    <i class="fas fa-phone text-success mr-2" style="width: 16px; text-align: center;"></i> 
                                    ${store.phone || '<span class="text-muted font-italic">Not available</span>'}
                                </p>
                                <p class="mb-0" style="font-size: 0.9rem;">
                                    <i class="fas fa-envelope text-info mr-2" style="width: 16px; text-align: center;"></i> 
                                    ${store.email || '<span class="text-muted font-italic">Not available</span>'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-top-0 pt-0 pb-3">
                    <div class="d-flex justify-content-end align-items-center pt-2">
                        <button class="btn btn-outline-primary btn-sm mr-2 px-3 shadow-none" onclick="viewStoreDetails(${store.id})" style="border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                            <i class="fas fa-eye mr-1"></i> View
                        </button>
                        <button class="btn btn-outline-warning btn-sm mr-2 px-3 shadow-none text-dark" onclick="showReportIssueModal(${store.id})" style="border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                            <i class="fas fa-flag mr-1"></i> Report
                        </button>
                        <button class="btn btn-success btn-sm px-4 shadow-sm" onclick="selectStore(${store.id})" style="border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                            <i class="fas fa-check mr-1"></i> Select Store
                        </button>
                    </div>
                </div>
            </div>
            <!-- Standard CSS media query for border styling -->
            <style>
                @media (min-width: 768px) {
                    .border-right-md { border-right: 1px solid #e3e6f0; }
                }
                .hover-shadow:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; transform: translateY(-2px); }
            </style>
        `;
    }).join('');
}

// Focus on specific store on map
function focusOnStore(lat, lng, storeId) {
    if (!map) {
        alert('Map is not available. Please wait for the map to load.');
        return;
    }
    
    // Center map on store and zoom in
    map.setView([lat, lng], 15);
    
    // Find and open the marker popup if it exists
    markers.forEach(marker => {
        if (marker.storeId === storeId) {
            marker.openPopup();
            // Briefly animate the marker
            const originalIcon = marker.getIcon();
            // Create a slightly larger version for highlight effect
            if (marker.storeData) {
                const highlightIcon = getMarkerIcon(parseFloat(marker.storeData.rating || 0));
                marker.setIcon(highlightIcon);
                // Reset after animation
                setTimeout(() => {
                    marker.setIcon(originalIcon);
                }, 500);
            }
        }
    });
}

// Search stores
function searchStores() {
    const searchTerm = document.getElementById('searchInput').value;
    const cityFilter = document.getElementById('cityFilter').value;
    
    if (!searchTerm && !cityFilter) {
        loadAllStores();
        return;
    }
    
    let url = '<?php echo BASE_URL; ?>customer/searchStores?q=' + encodeURIComponent(searchTerm);
    
    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let filteredStores = data.data;
            
            // Apply city filter if selected
            if (cityFilter) {
                filteredStores = filteredStores.filter(store => store.city === cityFilter);
            }
            
            // Filter stores to only include those within Bohol bounds
            const boholStores = filteredStores.filter(store => {
                if (!store.latitude || !store.longitude) return false;
                const lat = parseFloat(store.latitude);
                const lng = parseFloat(store.longitude);
                // Check if within Bohol bounds
                return lat >= 9.5 && lat <= 10.2 && lng >= 123.6 && lng <= 124.4;
            });
            
            // Sort by rating (highest first) even after filtering
            boholStores.sort((a, b) => {
                const ratingA = parseFloat(a.rating || 0);
                const ratingB = parseFloat(b.rating || 0);
                return ratingB - ratingA;
            });
            
            // Store in global variable
            stores = boholStores;
            
            // Display stores in list
            displayStores(boholStores);
            
            // Display stores on map if map is available
            if (map) {
                displayStoresOnMap(boholStores);
            }
        } else {
            alert('Error searching stores: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error searching stores.');
    });
}

// Display stores on map
function displayStoresOnMap(stores) {
    // Check if map is available
    if (!map) {
        return;
    }
    
    try {
        // Clear existing markers (except user marker)
        markers.forEach(marker => {
            map.removeLayer(marker);
        });
        markers = [];
        
        // Adjust map bounds to fit all stores (within Bohol)
        if (stores.length > 0) {
            const bounds = [];
            
            stores.forEach(store => {
                if (!store.latitude || !store.longitude) {
                    // Skip stores without coordinates
                    return;
                }
                
                const lat = parseFloat(store.latitude);
                const lng = parseFloat(store.longitude);
                
                // Ensure coordinates are within Bohol bounds
                if (lat < 9.5 || lat > 10.2 || lng < 123.6 || lng > 124.4) {
                    console.warn(`Store ${store.store_name} is outside Bohol bounds, skipping`);
                    return;
                }
                
                // Create marker with custom icon based on rating
                const marker = L.marker([lat, lng], {
                    icon: getMarkerIcon(store.rating)
                }).addTo(map);
                
                // Create popup content
                const popupContent = `
                    <div style="min-width: 220px;">
                        <h6 style="margin-bottom: 10px; font-weight: bold; color: #333;">${store.store_name}</h6>
                        <p style="margin: 5px 0; color: #666;">
                            <i class="fas fa-map-marker-alt text-primary"></i> ${store.address}
                        </p>
                        <p style="margin: 5px 0; color: #666;">
                            <i class="fas fa-phone text-success"></i> <strong>Phone:</strong> ${store.phone || 'N/A'}
                        </p>
                        <p style="margin: 5px 0; color: #666;">
                            <i class="fas fa-star text-warning"></i> <strong>Rating:</strong> ${store.rating || '0'}/5.0
                        </p>
                        ${store.distance ? `<p style="margin: 5px 0; color: #666;"><i class="fas fa-route text-info"></i> <strong>Distance:</strong> ${store.distance.toFixed(2)} km</p>` : ''}
                        <div style="margin-top: 10px;">
                            <button class="btn btn-primary btn-sm" onclick="viewStoreDetails(${store.id})" style="width: 100%; margin-bottom: 5px;">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn btn-success btn-sm" onclick="selectStore(${store.id})" style="width: 100%; margin-bottom: 5px;">
                                <i class="fas fa-check"></i> Select Store
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="showReportIssueModal(${store.id})" style="width: 100%;" title="Report an issue with this store">
                                <i class="fas fa-exclamation-triangle"></i> Report Issue
                            </button>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                
                // Store reference to marker and store data
                marker.storeId = store.id;
                marker.storeData = store; // Store full store data for easy access
                markers.push(marker);
                bounds.push([lat, lng]);
            });
            
            // Fit map to show all stores (within Bohol bounds)
            if (bounds.length > 0) {
                if (bounds.length === 1) {
                    // If only one store, center on it and zoom in
                    map.setView(bounds[0], 15);
                } else {
                    // Fit bounds for multiple stores
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            } else {
                // No stores with valid coordinates in Bohol, ensure map shows Bohol
                map.setView([9.6576, 123.8544], 10);
            }
        }
    } catch (error) {
        console.error('Error displaying stores on map:', error);
    }
}

// Load cities for filter
function loadCities(stores) {
    const cities = [...new Set(stores.map(store => store.city))].sort();
    const cityFilter = document.getElementById('cityFilter');
    
    cityFilter.innerHTML = '<option value="">All Cities</option>' +
        cities.map(city => `<option value="${city}">${city}</option>`).join('');
}

// View store details
function viewStoreDetails(storeId) {
    fetch(`<?php echo BASE_URL; ?>customer/getStoreDetails?id=${storeId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const store = data.data;
            selectedStoreId = storeId;
            
            document.getElementById('storeDetailsTitle').textContent = store.store_name;
            
            // Build full address for map
            const fullAddress = `${store.address}, ${store.city}, ${store.province}`;
            const encodedAddress = encodeURIComponent(fullAddress);
            
            // Create Google Maps embed URL using coordinates (more accurate) or address
            let mapUrl;
            if (store.latitude && store.longitude) {
                // Use coordinates for precise location
                mapUrl = `https://www.google.com/maps?q=${store.latitude},${store.longitude}&output=embed`;
            } else {
                // Fallback to address if coordinates are not available
                mapUrl = `https://www.google.com/maps?q=${encodedAddress}&output=embed`;
            }
            
            // Get user's existing rating for this store
            checkUserRating(storeId).then(userRating => {
                document.getElementById('storeDetailsBody').innerHTML = `
                    <!-- Refined Compact Modal Layout -->
                    <div class="text-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center p-2 mb-2 rounded-circle bg-light shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-store text-primary"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1">${store.store_name}</h5>
                        <div class="d-flex justify-content-center align-items-center small">
                            <div class="mr-2">${renderStarRating(parseFloat(store.rating || 0))}</div>
                            <span class="font-weight-bold text-dark">${parseFloat(store.rating || 0).toFixed(1)}</span>
                            <span class="text-muted ml-1">(${store.total_ratings || 0} reviews)</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="card h-100 border-0 shadow-sm bg-light" style="border-radius: 10px;">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-muted font-weight-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-map-marker-alt mr-1 text-primary"></i> Location
                                    </h6>
                                    <p class="mb-1 font-weight-bold text-dark small">${store.address}</p>
                                    <p class="mb-0 text-muted extra-small">${store.city}, ${store.province}</p>
                                    ${store.operating_hours ? `
                                        <hr class="my-2">
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;"><i class="fas fa-clock mr-1"></i> ${store.operating_hours}</p>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm bg-light" style="border-radius: 10px;">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-muted font-weight-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-address-card mr-1 text-success"></i> Contact Info
                                    </h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="fas fa-phone fa-xs text-success mr-2" style="width: 15px;"></i>
                                            <span class="text-dark small">${store.phone || '<span class="text-muted font-italic">N/A</span>'}</span>
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <i class="fas fa-envelope fa-xs text-info mr-2" style="width: 15px;"></i>
                                            <span class="text-dark small" style="word-break: break-all;">${store.email || '<span class="text-muted font-italic">N/A</span>'}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${store.services_offered || store.features ? `
                        <div class="alert alert-secondary border-0 mb-3 py-2 px-3 rounded-lg" style="font-size: 0.8rem;">
                            <div class="row">
                                ${store.services_offered ? `
                                    <div class="col-md-6">
                                        <span class="font-weight-bold text-primary mr-1"><i class="fas fa-tools"></i> Services:</span>
                                        <span class="text-dark">${store.services_offered}</span>
                                    </div>
                                ` : ''}
                                ${store.features ? `
                                    <div class="col-md-6 ${store.services_offered ? 'mt-1 mt-md-0' : ''}">
                                        <span class="font-weight-bold text-success mr-1"><i class="fas fa-check-circle"></i> Features:</span>
                                        <span class="text-dark">${store.features}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    ` : ''}

                    <!-- Compact Tabs Navigation -->
                    <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-pill" id="storeModalTabs" role="tablist" style="font-size: 0.8rem;">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active rounded-pill py-1" id="map-tab" data-toggle="tab" href="#modal-map" role="tab">
                                <i class="fas fa-map-marker-alt"></i> Map
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link rounded-pill py-1" id="reviews-tab" data-toggle="tab" href="#modal-reviews" role="tab">
                                <i class="fas fa-star"></i> Services
                            </a>
                        </li>
                        ${store.can_rate ? `
                        <li class="nav-item" role="presentation">
                            <a class="nav-link rounded-pill py-1" id="rate-tab" data-toggle="tab" href="#modal-rate" role="tab">
                                <i class="fas fa-pen"></i> Rate
                            </a>
                        </li>
                        ` : ''}
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="storeModalTabContent">
                        
                        <!-- Map Tab -->
                        <div class="tab-pane fade show active" id="modal-map" role="tabpanel">
                            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 10px;">
                                <iframe src="${mapUrl}" width="100%" height="250" style="border:0; display: block;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>

                        <!-- Services Tab -->
                        <div class="tab-pane fade" id="modal-reviews" role="tabpanel">
                            <div class="bg-light p-2 rounded" style="max-height: 300px; overflow-y: auto; font-size: 0.85rem;">
                                <div id="servicesList">
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-spinner fa-spin mb-1"></i>
                                        <p class="small">Loading services...</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Book Now Button -->
                            <div class="mt-3 text-center" id="bookNowContainer" style="display: none;">
                                <div class="alert alert-info py-2 mb-2" style="font-size: 0.85rem;">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <strong>Selected:</strong> <span id="selectedServiceName"></span>
                                </div>
                                <button class="btn btn-primary btn-block" id="bookNowBtn" onclick="bookSelectedService()">
                                    <i class="fas fa-calendar-check mr-2"></i>Book This Service
                                </button>
                            </div>
                        </div>

                        <!-- Rate Tab -->
                        ${store.can_rate ? `
                        <div class="tab-pane fade" id="modal-rate" role="tabpanel">
                            <div class="card border-0 shadow-sm py-2">
                                <div class="card-body text-center p-2">
                                    ${userRating ? `
                                        <div class="alert alert-success d-inline-block px-3 py-2 mb-0">
                                            <h6 class="alert-heading small mb-1">Your Rating: ${userRating.rating}/5.0</h6>
                                            <div class="mb-2 text-warning" style="font-size: 1rem;">
                                                ${renderStarRating(parseFloat(userRating.rating))}
                                            </div>
                                            <button class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.7rem;" onclick="showRatingForm(${storeId}, ${userRating.rating}, '${escapeHtml(userRating.review_text || '')}')">
                                                Edit
                                            </button>
                                        </div>
                                    ` : `
                                        <div id="ratingFormContainer" class="text-left mx-auto" style="max-width: 400px;">
                                            <div class="form-group text-center mb-2">
                                                <div class="star-rating justify-content-center mb-1" id="starRating" data-rating="0">
                                                    <span class="star" data-value="1" style="font-size: 1.8rem; padding: 0 3px;"><i class="far fa-star"></i></span>
                                                    <span class="star" data-value="2" style="font-size: 1.8rem; padding: 0 3px;"><i class="far fa-star"></i></span>
                                                    <span class="star" data-value="3" style="font-size: 1.8rem; padding: 0 3px;"><i class="far fa-star"></i></span>
                                                    <span class="star" data-value="4" style="font-size: 1.8rem; padding: 0 3px;"><i class="far fa-star"></i></span>
                                                    <span class="star" data-value="5" style="font-size: 1.8rem; padding: 0 3px;"><i class="far fa-star"></i></span>
                                                </div>
                                                <small class="text-muted extra-small" id="ratingText">Tap stars to rate</small>
                                            </div>
                                            <textarea class="form-control bg-light border-0 mb-2 small" id="reviewText" rows="2" placeholder="Write a review..."></textarea>
                                            <button class="btn btn-primary btn-sm btn-block font-weight-bold" onclick="submitRating(${storeId})">
                                                Submit
                                            </button>
                                        </div>
                                    `}
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div class="text-center mt-3">
                        <li class="list-unstyled">
                            <a href="#" class="text-muted extra-small text-decoration-none" onclick="showReportIssueModal(${storeId}); return false;">
                                <i class="fas fa-flag mr-1"></i>Report Issue
                            </a>
                        </li>
                    </div>`;
                
                // Initialize star rating
                initializeStarRating();
                
                // Load services with store name and rating
                loadStoreServices(storeId, store.store_name, store.rating);
                
                // Show modal
                $('#storeDetailsModal').modal('show');
                
                // Set up tab event listeners after modal is shown
                // Use setTimeout to ensure modal content is fully rendered
                setTimeout(() => {
                    const mapTab = document.getElementById('map-tab');
                    const servicesTab = document.getElementById('reviews-tab');
                    const rateTab = document.getElementById('rate-tab');
                    
                    // Remove any existing listeners to avoid duplicates
                    if (mapTab) {
                        $(mapTab).off('shown.bs.tab');
                        $(mapTab).on('shown.bs.tab', function() {
                            // Always show "Select This Store" button on Map tab
                            const selectStoreBtn = document.getElementById('selectStoreBtn');
                            if (selectStoreBtn) {
                                selectStoreBtn.style.display = 'inline-block';
                            }
                        });
                    }
                    
                    if (servicesTab) {
                        $(servicesTab).off('shown.bs.tab');
                        $(servicesTab).on('shown.bs.tab', function() {
                            // On Services tab, hide button only if a service is selected
                            const selectStoreBtn = document.getElementById('selectStoreBtn');
                            if (selectStoreBtn && selectedService) {
                                selectStoreBtn.style.display = 'none';
                            } else if (selectStoreBtn) {
                                selectStoreBtn.style.display = 'inline-block';
                            }
                        });
                    }
                    
                    if (rateTab) {
                        $(rateTab).off('shown.bs.tab');
                        $(rateTab).on('shown.bs.tab', function() {
                            // Always show "Select This Store" button on Rate tab
                            const selectStoreBtn = document.getElementById('selectStoreBtn');
                            if (selectStoreBtn) {
                                selectStoreBtn.style.display = 'inline-block';
                            }
                        });
                    }
                }, 100);
            });
        } else {
            alert('Error loading store details: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading store details.');
    });
}

// Select store
function selectStore(storeId = null) {
    if (storeId) {
        selectedStoreId = storeId;
    }
    
    if (!selectedStoreId) {
        alert('Please select a store first.');
        return;
    }
    
    // Check if we were opened from repair reservation page
    const savedData = sessionStorage.getItem('repairReservationFormData');
    if (savedData) {
        try {
            const formData = JSON.parse(savedData);
            if (formData.returnTo === 'repairReservation') {
                // Save the selected store ID to sessionStorage
                sessionStorage.setItem('selectedStoreId', selectedStoreId);
                
                // Get store name for confirmation
                const selectedStore = stores.find(s => s.id == selectedStoreId);
                const storeName = selectedStore ? selectedStore.store_name : 'Store';
                
                // Show loading overlay with success message
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                `;
                overlay.innerHTML = `
                    <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 400px;">
                        <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                        <h4 class="mt-3">Store Selected Successfully!</h4>
                        <p class="text-muted">${storeName}</p>
                        <p class="mb-0">Returning to your repair reservation form...</p>
                        <div class="spinner-border text-primary mt-3" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
                
                // Redirect after a short delay
                setTimeout(function() {
                    window.location.href = '<?php echo BASE_URL; ?>customer/newRepairReservation';
                }, 1500);
                
                return;
            }
        } catch (e) {
            console.error('Error checking return destination:', e);
        }
    }
    
    // Default behavior: Open modal with selected store
    // document.getElementById('selectedStoreId').value = selectedStoreId;
    // document.getElementById('storeSelectionForm').submit();
    $('#storeDetailsModal').modal('hide');
    openReservationModal(selectedStoreId);
}

// Render star rating display
function renderStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let html = '';
    
    for (let i = 1; i <= 5; i++) {
        if (i <= fullStars) {
            html += '<i class="fas fa-star text-warning"></i>';
        } else if (i === fullStars + 1 && hasHalfStar) {
            html += '<i class="fas fa-star-half-alt text-warning"></i>';
        } else {
            html += '<i class="far fa-star text-warning"></i>';
        }
    }
    
    return html;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Check if user has already rated this store
function checkUserRating(storeId) {
    return fetch(`<?php echo BASE_URL; ?>customer/getUserRating?store_id=${storeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                return data.data;
            }
            return null;
        })
        .catch(error => {
            console.error('Error checking user rating:', error);
            return null;
        });
}

// Initialize star rating interaction
function initializeStarRating() {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingText = document.getElementById('ratingText');
    
    if (!stars.length) return;
    
    let currentRating = 0;
    
    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const value = parseInt(this.getAttribute('data-value'));
            highlightStars(value);
        });
        
        star.addEventListener('click', function() {
            currentRating = parseInt(this.getAttribute('data-value'));
            document.getElementById('starRating').setAttribute('data-rating', currentRating);
            highlightStars(currentRating);
            updateRatingText(currentRating);
        });
    });
    
    const starRatingContainer = document.getElementById('starRating');
    if (starRatingContainer) {
        starRatingContainer.addEventListener('mouseleave', function() {
            const savedRating = parseInt(this.getAttribute('data-rating')) || 0;
            highlightStars(savedRating);
            updateRatingText(savedRating);
        });
    }
    
    function highlightStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.innerHTML = '<i class="fas fa-star"></i>';
                star.style.color = '#ffc107';
            } else {
                star.innerHTML = '<i class="far fa-star"></i>';
                star.style.color = '#ffc107';
            }
        });
    }
    
    function updateRatingText(rating) {
        if (ratingText) {
            const ratings = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            ratingText.textContent = rating > 0 ? `${rating}/5 - ${ratings[rating]}` : 'Click stars to rate';
        }
    }
}

// Show rating form (for updating existing rating)
function showRatingForm(storeId, currentRating, currentReview) {
    document.getElementById('ratingFormContainer').innerHTML = `
        <div class="form-group">
            <label>Your Rating:</label>
            <div class="star-rating" id="starRating" data-rating="${currentRating}">
                <span class="star" data-value="1"><i class="far fa-star"></i></span>
                <span class="star" data-value="2"><i class="far fa-star"></i></span>
                <span class="star" data-value="3"><i class="far fa-star"></i></span>
                <span class="star" data-value="4"><i class="far fa-star"></i></span>
                <span class="star" data-value="5"><i class="far fa-star"></i></span>
            </div>
            <small class="text-muted" id="ratingText">Click stars to rate</small>
        </div>
        <div class="form-group">
            <label for="reviewText">Your Review (Optional):</label>
            <textarea class="form-control" id="reviewText" rows="3" placeholder="Share your experience with this store...">${escapeHtml(currentReview)}</textarea>
        </div>
        <button class="btn btn-primary" onclick="submitRating(${storeId})">
            <i class="fas fa-star"></i> Update Rating
        </button>
    `;
    
    // Initialize with current rating
    const stars = document.querySelectorAll('.star-rating .star');
    const rating = parseInt(currentRating);
    stars.forEach((star, index) => {
        if (index < rating) {
            star.innerHTML = '<i class="fas fa-star"></i>';
            star.style.color = '#ffc107';
        }
    });
    
    document.getElementById('starRating').setAttribute('data-rating', rating);
    const ratingText = document.getElementById('ratingText');
    if (ratingText) {
        const ratings = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        ratingText.textContent = `${rating}/5 - ${ratings[rating]}`;
    }
    
    // Re-initialize star rating
    initializeStarRating();
}

// Submit rating
function submitRating(storeId) {
    const ratingElement = document.getElementById('starRating');
    if (!ratingElement) {
        alert('Rating form not found.');
        return;
    }
    
    const rating = parseInt(ratingElement.getAttribute('data-rating')) || 0;
    const reviewTextElement = document.getElementById('reviewText');
    const reviewText = reviewTextElement ? reviewTextElement.value.trim() : '';
    
    if (rating === 0) {
        alert('Please select a rating by clicking on the stars.');
        return;
    }
    
    if (rating < 1 || rating > 5) {
        alert('Rating must be between 1 and 5.');
        return;
    }
    
    // Disable button
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    fetch('<?php echo BASE_URL; ?>customer/submitStoreRating', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `store_id=${storeId}&rating=${rating}&review_text=${encodeURIComponent(reviewText)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Thank you for your rating! Your feedback has been submitted.');
            // Reload store details to show updated rating
            viewStoreDetails(storeId);
        } else {
            alert('Error submitting rating: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting rating. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Show report issue modal
function showReportIssueModal(storeId) {
    document.getElementById('report_store_id').value = storeId;
    document.getElementById('report_description').value = '';
    // Uncheck all checkboxes
    const checkboxes = document.querySelectorAll('input[name="issue_types[]"]');
    checkboxes.forEach(cb => cb.checked = false);
    $('#reportIssueModal').modal('show');
}

// Submit report issue
function submitReportIssue() {
    const storeId = document.getElementById('report_store_id').value;
    const description = document.getElementById('report_description').value.trim();
    const checkboxes = document.querySelectorAll('input[name="issue_types[]"]:checked');
    
    if (!storeId) {
        alert('Store ID is missing.');
        return;
    }
    
    if (checkboxes.length === 0) {
        alert('Please select at least one issue type.');
        return;
    }
    
    if (!description) {
        alert('Please provide a description of the issue.');
        return;
    }
    
    // Collect selected issue types
    const issueTypes = Array.from(checkboxes).map(cb => cb.value);
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...';
    button.disabled = true;
    
    // Submit report - use URLSearchParams for proper encoding
    const params = new URLSearchParams();
    params.append('store_id', storeId);
    params.append('issue_types', JSON.stringify(issueTypes));
    params.append('description', description);
    
    fetch('<?php echo BASE_URL; ?>customer/submitStoreComplianceReport', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Thank you for your report! Your issue has been submitted to the administrator for review.');
            $('#reportIssueModal').modal('hide');
        } else {
            alert('Error submitting report: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the report. Please try again.');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Load store reviews
function loadStoreReviews(storeId) {
    fetch(`<?php echo BASE_URL; ?>customer/getStoreReviews?store_id=${storeId}`)
        .then(response => response.json())
        .then(data => {
            const reviewsList = document.getElementById('reviewsList');
            if (!reviewsList) return;
            
            if (data.success && data.data && data.data.length > 0) {
                reviewsList.innerHTML = data.data.map(review => `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    ${renderStarRating(parseFloat(review.rating))}
                                    <strong class="ml-2">${review.rating}/5.0</strong>
                                </div>
                                <small class="text-muted">${formatDate(review.created_at)}</small>
                            </div>
                            ${review.review_text ? `<p class="mb-0">${escapeHtml(review.review_text)}</p>` : ''}
                            <small class="text-muted">- ${escapeHtml(review.customer_name || 'Anonymous')}</small>
                        </div>
                    </div>
                `).join('');
            } else {
                reviewsList.innerHTML = '<p class="text-muted text-center">No reviews yet. Be the first to rate this store!</p>';
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            const reviewsList = document.getElementById('reviewsList');
            if (reviewsList) {
                reviewsList.innerHTML = '<p class="text-danger">Error loading reviews.</p>';
            }
        });
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

// Global variable to track selected service
let selectedService = null;
let currentStoreId = null;
let currentStoreName = null;
let currentStoreRating = 0;

// Load store services/inventory
function loadStoreServices(storeId, storeName = '', storeRating = 0) {
    const servicesList = document.getElementById('servicesList');
    currentStoreId = storeId;
    currentStoreName = storeName;
    currentStoreRating = parseFloat(storeRating) || 0;
    selectedService = null; // Reset selection
    
    if (!servicesList) return;
    
    // Hide Book Now button initially
    const bookNowContainer = document.getElementById('bookNowContainer');
    if (bookNowContainer) {
        bookNowContainer.style.display = 'none';
    }
    
    // Show loading state
    servicesList.innerHTML = `
        <div class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin mb-1"></i>
            <p class="small">Loading services...</p>
        </div>
    `;
    
    // Fetch services from backend
    fetch(`<?php echo BASE_URL; ?>customer/getStoreInventory?store_id=${storeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                servicesList.innerHTML = data.data.map((service, index) => {
                    // Use store rating for all services (already implemented)
                    const rating = currentStoreRating > 0 ? currentStoreRating.toFixed(1) : '0.0';
                    
                    // Use REAL booking count from database
                    const bookings = parseInt(service.total_bookings) || 0;
                    
                    // Use REAL slots left (calculated from daily_capacity - today_bookings)
                    const slotsLeft = parseInt(service.slots_left) || 0;
                    
                    // Use REAL most popular determination (service with highest booking count)
                    const isPopular = service.is_most_popular || false;
                    
                    return `
                    <div class="card mb-3 border-0 shadow-sm service-card" 
                         id="service-${service.id}"
                         onclick="selectService(${service.id}, '${escapeHtml(service.service_name)}', ${service.price || 0})"
                         style="cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;">
                        
                        ${isPopular ? `
                            <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                <span class="badge badge-danger" style="font-size: 0.7rem; padding: 4px 8px;">
                                    🔥 Most Booked
                                </span>
                            </div>
                        ` : ''}
                        
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%);">
                                        <i class="fas fa-tools text-white" style="font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-weight-bold text-dark" style="font-size: 1rem;">${escapeHtml(service.service_name)}</h6>
                                    
                                    <!-- Rating (same as store rating) -->
                                    <div class="mb-2">
                                        <span class="text-warning" style="font-size: 0.85rem;">
                                            ⭐ ${rating}
                                        </span>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            (${bookings} bookings)
                                        </span>
                                    </div>
                                    
                                    ${service.description ? `
                                        <p class="text-muted mb-2" style="font-size: 0.8rem; line-height: 1.4;">${escapeHtml(service.description)}</p>
                                    ` : ''}
                                    
                                    <!-- Price and Service Type -->
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        ${service.price ? `
                                            <div class="mr-3">
                                                <div class="font-weight-bold text-success" style="font-size: 1.1rem;">
                                                    ₱${parseFloat(service.price).toFixed(2)}
                                                </div>
                                            </div>
                                        ` : ''}
                                        
                                        ${service.service_type ? `
                                            <span class="badge badge-info" style="font-size: 0.7rem; padding: 4px 8px;">
                                                ${escapeHtml(service.service_type)}
                                            </span>
                                        ` : ''}
                                    </div>
                                    
                                    <!-- Availability Status (REAL DATA) -->
                                    <div class="mt-2">
                                        ${slotsLeft > 3 ? `
                                            <span class="badge badge-success" style="font-size: 0.75rem; padding: 4px 10px;">
                                                🟢 ${slotsLeft} slots left today
                                            </span>
                                        ` : slotsLeft > 0 ? `
                                            <span class="badge badge-warning" style="font-size: 0.75rem; padding: 4px 10px;">
                                                🟡 Only ${slotsLeft} slots left
                                            </span>
                                        ` : `
                                            <span class="badge badge-danger" style="font-size: 0.75rem; padding: 4px 10px;">
                                                🔴 Fully Booked Today
                                            </span>
                                        `}
                                    </div>
                                </div>
                                
                                <!-- Selection Checkmark -->
                                <div class="text-right ml-2">
                                    <i class="fas fa-check-circle text-success selected-icon" 
                                       style="display: none; font-size: 2rem; transition: all 0.3s ease;"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Overlay -->
                        <div class="selected-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                             background: linear-gradient(135deg, rgba(15, 60, 95, 0.1) 0%, rgba(31, 78, 121, 0.1) 100%);
                             opacity: 0; transition: opacity 0.3s ease; pointer-events: none;"></div>
                    </div>
                    `;
                }).join('');
                
                // Add premium hover and selection effects
                addPremiumServiceCardEffects();
            } else {
                servicesList.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p class="mb-0">No services available at this store yet.</p>
                        <small class="text-muted">Check back soon!</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading services:', error);
            servicesList.innerHTML = `
                <div class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="small mb-0">Error loading services. Please try again.</p>
                </div>
            `;
        });
}

// Add premium hover and selection effects
function addPremiumServiceCardEffects() {
    const style = document.createElement('style');
    style.innerHTML = `
        .service-card {
            border-radius: 12px !important;
        }
        
        .service-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
        }
        
        .service-card.selected {
            border: 3px solid #0F3C5F !important;
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(15, 60, 95, 0.3) !important;
        }
        
        .service-card.selected .selected-overlay {
            opacity: 1 !important;
        }
        
        .service-card.selected .selected-icon {
            animation: checkmarkPop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        @keyframes checkmarkPop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%) !important;
        }
    `;
    if (!document.getElementById('premium-service-card-styles')) {
        style.id = 'premium-service-card-styles';
        document.head.appendChild(style);
    }
}

// Select a service
function selectService(serviceId, serviceName, servicePrice) {
    // Remove previous selection
    document.querySelectorAll('.service-card').forEach(card => {
        card.classList.remove('selected');
        const icon = card.querySelector('.selected-icon');
        if (icon) icon.style.display = 'none';
    });
    
    // Add selection to clicked service
    const selectedCard = document.getElementById(`service-${serviceId}`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
        const icon = selectedCard.querySelector('.selected-icon');
        if (icon) icon.style.display = 'block';
    }
    
    // Store selected service info
    selectedService = {
        id: serviceId,
        name: serviceName,
        price: servicePrice,
        storeId: currentStoreId,
        storeName: currentStoreName
    };
    
    // Hide "Select This Store" button when service is selected
    const selectStoreBtn = document.getElementById('selectStoreBtn');
    if (selectStoreBtn) {
        selectStoreBtn.style.display = 'none';
    }
    
    // Show Book Now button with sticky positioning
    const bookNowContainer = document.getElementById('bookNowContainer');
    if (bookNowContainer) {
        document.getElementById('selectedServiceName').textContent = serviceName;
        bookNowContainer.style.display = 'block';
        
        // Make it sticky
        bookNowContainer.style.position = 'sticky';
        bookNowContainer.style.bottom = '0';
        bookNowContainer.style.zIndex = '100';
        bookNowContainer.style.backgroundColor = 'white';
        bookNowContainer.style.boxShadow = '0 -4px 12px rgba(0,0,0,0.1)';
    }
}

// Book selected service - open repair reservation modal
function bookSelectedService() {
    if (!selectedService) {
        alert('Please select a service first.');
        return;
    }
    
    // Close the store details modal first
    $('#storeDetailsModal').modal('hide');
    
    // Open repair reservation modal with pre-filled service and store data
    // Using setTimeout to ensure store modal closes smoothly before opening reservation modal
    setTimeout(() => {
        openReservationModal(
            currentStoreId,           // Store ID
            selectedService.id,       // Service ID
            selectedService.name      // Service name
        );
    }, 300);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Leaflet map immediately (no API key needed)
    initMap();
    
    // Check if we were opened from repair reservation page and show notice
    const savedData = sessionStorage.getItem('repairReservationFormData');
    if (savedData) {
        try {
            const formData = JSON.parse(savedData);
            if (formData.returnTo === 'repairReservation') {
                const returnNotice = document.getElementById('returnNotice');
                if (returnNotice) {
                    returnNotice.style.display = 'block';
                }
            }
        } catch (e) {
            console.error('Error checking return destination:', e);
        }
    }
    
    // Add event listeners
    const searchInput = document.getElementById('searchInput');
    const cityFilter = document.getElementById('cityFilter');
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchStores();
            }
        });
    }
    
    if (cityFilter) {
        cityFilter.addEventListener('change', function() {
            searchStores();
        });
    }
});
</script>

<!-- Custom CSS for Leaflet markers and store cards -->
<style>
    .custom-marker {
        background: transparent;
        border: none;
    }
    .user-location-marker {
        background: transparent;
        border: none;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
    .leaflet-popup-content {
        margin: 15px;
    }
    
    /* Store card styling */
    .store-card {
        transition: all 0.3s ease;
        border-left: 4px solid #dee2e6;
    }
    
    .store-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .store-card.border-warning {
        border-left-color: #ffc107;
        background-color: #fffbf0;
    }
    
    .store-card.border-info {
        border-left-color: #1F4E79;
    }
    
    /* Star rating styling */
    .star-rating {
        display: flex;
        gap: 5px;
        margin-bottom: 10px;
    }
    
    .star-rating .star {
        font-size: 24px;
        color: #ffc107;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .star-rating .star:hover {
        transform: scale(1.2);
    }
    
    .star-rating .star i {
        display: inline-block;
    }
    
    @media (max-width: 768px) {
        .store-card .col-md-1 {
            margin-bottom: 10px;
        }
        
        .store-card .col-md-4 {
            margin-top: 10px;
        }
        
        .store-card .col-md-4.text-right {
            text-align: left !important;
        }
        
        .star-rating .star {
            font-size: 20px;
        }
    }
    
    /* Override Bootstrap primary colors with navy blue */
    .btn-primary,
    .btn-info {
        background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 100%) !important;
        border-color: #0F3C5F !important;
        color: white !important;
    }

    .btn-primary:hover,
    .btn-info:hover {
        background: linear-gradient(135deg, #1F4E79 0%, #0F3C5F 100%) !important;
        border-color: #1F4E79 !important;
        color: white !important;
    }

    .text-primary {
        color: #1F4E79 !important;
    }
</style>

<!-- Repair Reservation Modal -->
<?php require_once ROOT . DS . 'views' . DS . 'customer' . DS . 'repair_reservation_modal_wrapper.php'; ?>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
