<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Find Nearest Store</h1>
    <div>
        <button class="btn btn-primary btn-sm" onclick="getCurrentLocation(event)">
            <i class="fas fa-map-marker-alt"></i> Use My Location
        </button>
        <a href="<?php echo BASE_URL; ?>customer/newBooking" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Book Service
        </a>
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
    <div class="modal-dialog modal-xl" role="document">
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
    let color = '#8B4513'; // Default brown
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
                                html: '<div style="background-color: #8B4513; border: 3px solid white; border-radius: 50%; width: 20px; height: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
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
            <div class="card mb-3 store-card ${index === 0 && rating >= 4.5 ? 'border-warning shadow' : index < 3 && rating >= 4.0 ? 'border-info' : ''}" style="transition: all 0.3s ease;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-1 text-center">
                            ${index === 0 && rating >= 4.5 ? '<span class="badge badge-warning badge-pill" style="font-size: 1.2rem; padding: 8px 12px;"><i class="fas fa-crown"></i> #1</span>' : ''}
                            ${index === 1 && rating >= 4.0 ? '<span class="badge badge-secondary badge-pill" style="font-size: 1rem; padding: 6px 10px;"><i class="fas fa-medal"></i> #2</span>' : ''}
                            ${index === 2 && rating >= 4.0 ? '<span class="badge badge-secondary badge-pill" style="font-size: 1rem; padding: 6px 10px;"><i class="fas fa-award"></i> #3</span>' : ''}
                            ${index >= 3 ? `<span class="text-muted" style="font-size: 1.5rem; font-weight: bold;">#${index + 1}</span>` : ''}
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="card-title mb-0">${store.store_name || 'Store'}</h5>
                            </div>
                            <p class="card-text mb-2">
                                <i class="fas fa-map-marker-alt text-primary"></i> 
                                <strong>Address:</strong> ${store.address || 'Address not provided'}<br>
                                <i class="fas fa-city text-info"></i> 
                                <strong>Location:</strong> ${store.city || 'Bohol'}, ${store.province || 'Bohol'}<br>
                                ${store.phone ? `<i class="fas fa-phone text-success"></i> <strong>Phone:</strong> ${store.phone}<br>` : ''}
                                ${store.email ? `<i class="fas fa-envelope text-info"></i> <strong>Email:</strong> ${store.email}` : ''}
                            </p>
                            <div class="mb-2">
                                <span class="badge ${ratingBadgeClass} mr-2" style="font-size: 0.9rem; padding: 6px 10px;">
                                    <i class="fas fa-star"></i> ${rating.toFixed(1)}/5.0 ${ratingText}
                                </span>
                                ${store.distance ? `<span class="badge badge-info" style="font-size: 0.85rem;"><i class="fas fa-route"></i> ${store.distance.toFixed(2)} km away</span>` : ''}
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-primary btn-sm mb-2" onclick="viewStoreDetails(${store.id})" style="width: 100%;">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn btn-success btn-sm mb-2" onclick="selectStore(${store.id})" style="width: 100%;">
                                <i class="fas fa-check"></i> Select Store
                            </button>
                            ${store.latitude && store.longitude ? `
                                <button class="btn btn-info btn-sm mb-2" onclick="focusOnStore(${store.latitude}, ${store.longitude}, ${store.id})" style="width: 100%;">
                                    <i class="fas fa-map-marker-alt"></i> Show on Map
                                </button>
                            ` : ''}
                            <button class="btn btn-warning btn-sm" onclick="showReportIssueModal(${store.id})" style="width: 100%;" title="Report an issue with this store">
                                <i class="fas fa-exclamation-triangle"></i> Report Issue
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt text-primary"></i> Address</h6>
                            <p>${store.address}, ${store.city}, ${store.province}</p>
                            
                            <h6><i class="fas fa-phone text-success"></i> Contact</h6>
                            <p>Phone: ${store.phone || 'N/A'}<br>Email: ${store.email || 'N/A'}</p>
                            
                            ${store.operating_hours ? `<h6><i class="fas fa-clock text-info"></i> Operating Hours</h6><p>${store.operating_hours}</p>` : ''}
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-star text-warning"></i> Average Rating</h6>
                            <div class="mb-2">
                                ${renderStarRating(parseFloat(store.rating || 0))}
                                <span class="ml-2"><strong>${parseFloat(store.rating || 0).toFixed(1)}</strong>/5.0</span>
                                ${store.total_ratings ? `<small class="text-muted">(${store.total_ratings} ${store.total_ratings === 1 ? 'rating' : 'ratings'})</small>` : ''}
                            </div>
                            
                            ${store.services_offered ? `<h6><i class="fas fa-tools text-primary"></i> Services Offered</h6><p>${store.services_offered}</p>` : ''}
                            ${store.features ? `<h6><i class="fas fa-star text-success"></i> Features</h6><p>${store.features}</p>` : ''}
                        </div>
                    </div>
                    
                    <!-- Rating Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <h6><i class="fas fa-star text-warning"></i> Rate This Store</h6>
                            ${userRating ? `
                                <div class="alert alert-info">
                                    <p class="mb-2">You have already rated this store:</p>
                                    <div class="mb-2">${renderStarRating(parseFloat(userRating.rating))} <strong>${userRating.rating}/5.0</strong></div>
                                    ${userRating.review_text ? `<p class="mb-0"><em>"${escapeHtml(userRating.review_text)}"</em></p>` : ''}
                                    <button class="btn btn-sm btn-primary mt-2" onclick="showRatingForm(${storeId}, ${userRating.rating}, '${escapeHtml(userRating.review_text || '')}')">
                                        <i class="fas fa-edit"></i> Update Rating
                                    </button>
                                </div>
                            ` : `
                                <div id="ratingFormContainer">
                                    <div class="form-group">
                                        <label>Your Rating:</label>
                                        <div class="star-rating" id="starRating" data-rating="0">
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
                                        <textarea class="form-control" id="reviewText" rows="3" placeholder="Share your experience with this store..."></textarea>
                                    </div>
                                    <button class="btn btn-primary" onclick="submitRating(${storeId})">
                                        <i class="fas fa-star"></i> Submit Rating
                                    </button>
                                </div>
                            `}
                        </div>
                    </div>
                    
                    <!-- Recent Reviews Section -->
                    <div class="row mt-4" id="reviewsSection">
                        <div class="col-12">
                            <hr>
                            <h6><i class="fas fa-comments text-info"></i> Recent Reviews</h6>
                            <div id="reviewsList">
                                <div class="text-center text-muted">
                                    <i class="fas fa-spinner fa-spin"></i> Loading reviews...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><i class="fas fa-map text-danger"></i> Location Map</h6>
                            <div style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 10px;">
                                <iframe 
                                    src="${mapUrl}" 
                                    width="100%" 
                                    height="400" 
                                    style="border:0; display: block;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> View the store location on Google Maps. Click and drag to explore the area.
                            </small>
                        </div>
                    </div>
                    
                    <!-- Report Issue Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <button class="btn btn-warning btn-sm" onclick="showReportIssueModal(${storeId})">
                                <i class="fas fa-exclamation-triangle"></i> Report Issue
                            </button>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Found an issue with this store? Report it to the administrator.
                            </small>
                        </div>
                    </div>
                `;
                
                // Initialize star rating
                initializeStarRating();
                
                // Load reviews
                loadStoreReviews(storeId);
                
                $('#storeDetailsModal').modal('show');
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
    
    // Default behavior: Set the selected store ID in the hidden form
    document.getElementById('selectedStoreId').value = selectedStoreId;
    
    // Submit the form to go to booking page with selected store
    document.getElementById('storeSelectionForm').submit();
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
        border-left-color: #8B4513;
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
    
    /* Override Bootstrap primary colors with brown */
    .btn-primary,
    .btn-info {
        background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
        border-color: #8B4513 !important;
        color: white !important;
    }

    .btn-primary:hover,
    .btn-info:hover {
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
        border-color: #A0522D !important;
        color: white !important;
    }

    .text-primary {
        color: #8B4513 !important;
    }
</style>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
