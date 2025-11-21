<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Store Ratings' ?> - UphoCare</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .main-content-wrapper {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: all 0.3s ease;
            min-height: 100vh;
            position: relative;
        }

        .top-navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            margin: 0;
            margin-bottom: 30px;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .page-subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 5px 0 0 0;
        }

        .content-area {
            padding: 0 30px 30px 30px;
            width: 100%;
            max-width: 100%;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .stats-card-title {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-card-value {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
        }

        .stats-card-icon {
            font-size: 48px;
            color: #667eea;
            opacity: 0.3;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .store-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .store-card.low-rating {
            border-left-color: #e74c3c;
        }

        .store-card.medium-rating {
            border-left-color: #f39c12;
        }

        .store-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .store-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .store-name {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .store-address {
            color: #7f8c8d;
            font-size: 14px;
        }

        .rating-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .rating-badge.low {
            background: #fee;
            color: #e74c3c;
        }

        .rating-badge.medium {
            background: #fff3cd;
            color: #f39c12;
        }

        .rating-badge.high {
            background: #d4edda;
            color: #27ae60;
        }

        .store-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .store-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-remove {
            background: #f39c12;
            color: white;
        }

        .btn-remove:hover {
            background: #e67e22;
        }

        .btn-ban {
            background: #e74c3c;
            color: white;
        }

        .btn-ban:hover {
            background: #c0392b;
        }

        .btn-ban-admin {
            background: #8e44ad;
            color: white;
        }

        .btn-ban-admin:hover {
            background: #7d3c98;
        }

        .star-rating {
            color: #ffc107;
        }

        .star-rating .far {
            color: #ddd;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .no-stores {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .no-stores i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <?php require_once ROOT . DS . 'views' . DS . 'control_panel' . DS . 'layouts' . DS . 'sidebar.php'; ?>

    <div class="main-content-wrapper">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div>
                <h1 class="page-title">
                    <i class="fas <?= $data['page_icon'] ?? 'fa-star' ?>"></i>
                    <?= $data['page_title'] ?? 'Store Ratings' ?>
                </h1>
                <p class="page-subtitle"><?= $data['page_subtitle'] ?? 'Monitor store ratings and manage low-rated stores' ?></p>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-card-title">Total Stores</div>
                                <div class="stats-card-value"><?= $data['stats']['total_stores'] ?? 0 ?></div>
                            </div>
                            <div class="stats-card-icon">
                                <i class="fas fa-store"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-card-title">Stores with Ratings</div>
                                <div class="stats-card-value"><?= $data['stats']['stores_with_ratings'] ?? 0 ?></div>
                            </div>
                            <div class="stats-card-icon">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-card-title">Below Threshold</div>
                                <div class="stats-card-value text-danger"><?= $data['stats']['stores_below_threshold'] ?? 0 ?></div>
                            </div>
                            <div class="stats-card-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stats-card-title">Average Rating</div>
                                <div class="stats-card-value"><?= number_format($data['stats']['avg_rating'] ?? 0, 1) ?>/5.0</div>
                            </div>
                            <div class="stats-card-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="<?= BASE_URL ?>control-panel/storeRatings" class="row g-3">
                    <div class="col-md-4">
                        <label for="threshold" class="form-label">Filter by Rating</label>
                        <select class="form-select" id="threshold" name="threshold">
                            <option value="" <?= ($data['rating_threshold'] ?? null) === null || $data['rating_threshold'] === '' ? 'selected' : '' ?>>All Stores (Sorted by Lowest Rating)</option>
                            <option value="1.0" <?= ($data['rating_threshold'] ?? null) == 1.0 ? 'selected' : '' ?>>Below 1.0 (Very Low)</option>
                            <option value="1.5" <?= ($data['rating_threshold'] ?? null) == 1.5 ? 'selected' : '' ?>>Below 1.5 (Low)</option>
                            <option value="2.0" <?= ($data['rating_threshold'] ?? null) == 2.0 ? 'selected' : '' ?>>Below 2.0 (Poor)</option>
                            <option value="2.5" <?= ($data['rating_threshold'] ?? null) == 2.5 ? 'selected' : '' ?>>Below 2.5 (Fair)</option>
                            <option value="3.0" <?= ($data['rating_threshold'] ?? null) == 3.0 ? 'selected' : '' ?>>Below 3.0 (Average)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="searchStore" class="form-label">Search Store Name</label>
                        <input type="text" class="form-control" id="searchStore" placeholder="Type store name (e.g., kyle store)..." onkeyup="filterStores()">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                        <a href="<?= BASE_URL ?>control-panel/storeRatings" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Use the search box to quickly find specific stores (e.g., "kyle store"). The search works instantly as you type.
                </div>
            </div>

            <!-- Stores List -->
            <?php if (empty($data['stores'])): ?>
                <div class="no-stores">
                    <i class="fas fa-store"></i>
                    <h3>No Stores Found</h3>
                    <p>No stores match the current filter criteria.</p>
                    <p class="text-muted small mt-2">
                        <i class="fas fa-info-circle"></i> 
                        Make sure stores have:
                        <ul class="text-left d-inline-block mt-2">
                            <li>Status = 'active'</li>
                            <?php if ($data['rating_threshold']): ?>
                                <li>Rating below <?= $data['rating_threshold'] ?></li>
                            <?php endif; ?>
                        </ul>
                    </p>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> 
                    Showing <strong><?= count($data['stores']) ?></strong> store(s) 
                    <?php if ($data['rating_threshold']): ?>
                        with rating below <strong><?= $data['rating_threshold'] ?></strong>
                    <?php else: ?>
                        (all stores, sorted by lowest rating first - high-rated stores like 5.0/5.0 appear at the bottom)
                    <?php endif; ?>
                </div>
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Note:</strong> Stores are sorted by lowest rating first to help you identify stores that need attention. 
                    High-rated stores (like 5.0/5.0) will appear at the bottom of the list. Use the search box above to find specific stores quickly.
                </div>
                <div id="storesList">
                <?php foreach ($data['stores'] as $store): ?>
                    <?php
                    // Use avg_rating from store_ratings if available, otherwise use store rating
                    $rating = floatval($store['avg_rating'] ?? $store['rating'] ?? 0);
                    $totalRatings = intval($store['total_ratings'] ?? 0);
                    $cardClass = 'store-card';
                    $badgeClass = 'rating-badge';
                    
                    // Determine rating category for styling
                    if ($rating == 0 || $totalRatings == 0) {
                        $cardClass .= ' low-rating';
                        $badgeClass .= ' low';
                        $ratingText = 'No Ratings';
                    } elseif ($rating < 2.0) {
                        $cardClass .= ' low-rating';
                        $badgeClass .= ' low';
                        $ratingText = number_format($rating, 1) . '/5.0';
                    } elseif ($rating < 3.0) {
                        $cardClass .= ' medium-rating';
                        $badgeClass .= ' medium';
                        $ratingText = number_format($rating, 1) . '/5.0';
                    } else {
                        $badgeClass .= ' high';
                        $ratingText = number_format($rating, 1) . '/5.0';
                    }
                    
                    // Render star rating
                    $fullStars = floor($rating);
                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                    $starsHtml = '';
                    if ($totalRatings > 0) {
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $fullStars) {
                                $starsHtml .= '<i class="fas fa-star"></i>';
                            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                $starsHtml .= '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                $starsHtml .= '<i class="far fa-star"></i>';
                            }
                        }
                    } else {
                        // No ratings - show empty stars
                        for ($i = 1; $i <= 5; $i++) {
                            $starsHtml .= '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                    <div class="<?= $cardClass ?>">
                        <div class="store-header">
                            <div>
                                <div class="store-name"><?= htmlspecialchars($store['store_name'] ?? 'Unknown Store') ?></div>
                                <div class="store-address">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($store['address'] ?? '') ?>, 
                                    <?= htmlspecialchars($store['city'] ?? '') ?>, 
                                    <?= htmlspecialchars($store['province'] ?? '') ?>
                                </div>
                            </div>
                            <div class="<?= $badgeClass ?>">
                                <span class="star-rating"><?= $starsHtml ?></span>
                                <span><?= $ratingText ?></span>
                                <?php if ($totalRatings > 0): ?>
                                    <small class="ms-2">(<?= $totalRatings ?> <?= $totalRatings == 1 ? 'rating' : 'ratings' ?>)</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="store-info">
                            <div class="info-item">
                                <div class="info-label">Total Ratings</div>
                                <div class="info-value"><?= $totalRatings ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Average Rating</div>
                                <div class="info-value">
                                    <?php if ($totalRatings > 0): ?>
                                        <?= number_format($rating, 2) ?>/5.0
                                    <?php else: ?>
                                        <span class="text-muted">No ratings yet</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($totalRatings > 0): ?>
                            <div class="info-item">
                                <div class="info-label">Min Rating</div>
                                <div class="info-value"><?= number_format(floatval($store['min_rating'] ?? 0), 1) ?>/5.0</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Max Rating</div>
                                <div class="info-value"><?= number_format(floatval($store['max_rating'] ?? 0), 1) ?>/5.0</div>
                            </div>
                            <?php endif; ?>
                            <?php if ($store['admin_name']): ?>
                            <div class="info-item">
                                <div class="info-label">Admin</div>
                                <div class="info-value"><?= htmlspecialchars($store['admin_name']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Admin Email</div>
                                <div class="info-value"><?= htmlspecialchars($store['admin_email'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Admin Status</div>
                                <div class="info-value">
                                    <?php if ($store['admin_status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($store['banned_at'])): ?>
                            <div class="info-item">
                                <div class="info-label">Ban Status</div>
                                <div class="info-value">
                                    <span class="badge bg-danger">Banned</span>
                                    <?php if (!empty($store['ban_duration_days'])): ?>
                                        <br><small class="text-muted">Duration: <?= $store['ban_duration_days'] ?> day(s)</small>
                                    <?php else: ?>
                                        <br><small class="text-muted">Permanent ban</small>
                                    <?php endif; ?>
                                    <?php if (!empty($store['banned_until'])): ?>
                                        <br><small class="text-muted">Until: <?= date('Y-m-d H:i', strtotime($store['banned_until'])) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($store['ban_reason'])): ?>
                                        <br><small class="text-muted">Reason: <?= htmlspecialchars($store['ban_reason']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="store-actions">
                            <button class="btn btn-action btn-remove" onclick="removeStoreFromMap(<?= $store['id'] ?>, '<?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?>')">
                                <i class="fas fa-map-marker-times"></i> Remove from Map
                            </button>
                            <button class="btn btn-action btn-ban-admin" onclick="banStore(<?= $store['id'] ?>, '<?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?>', true, <?= $store['admin_id'] ?? 'null' ?>)">
                                <i class="fas fa-ban"></i> Ban Store & Admin
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Filter stores by name (client-side search)
        function filterStores() {
            const searchInput = document.getElementById('searchStore');
            if (!searchInput) return;
            
            const searchTerm = searchInput.value.toLowerCase().trim();
            const storeCards = document.querySelectorAll('#storesList .store-card');
            const storesList = document.getElementById('storesList');
            
            if (!storesList || !storeCards.length) return;
            
            let visibleCount = 0;
            storeCards.forEach(card => {
                const storeNameElement = card.querySelector('.store-name');
                if (!storeNameElement) {
                    card.style.display = 'block';
                    return;
                }
                
                const storeName = storeNameElement.textContent.toLowerCase();
                if (searchTerm === '' || storeName.includes(searchTerm)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            let noResultsMsg = document.getElementById('noResultsMessage');
            if (searchTerm && visibleCount === 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMessage';
                    noResultsMsg.className = 'alert alert-warning text-center mt-3';
                    noResultsMsg.innerHTML = '<i class="fas fa-search"></i> No stores found matching "<strong>' + 
                                             escapeHtml(searchInput.value) + '</strong>"';
                    storesList.parentNode.insertBefore(noResultsMsg, storesList.nextSibling);
                }
            } else {
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>

    <!-- Remove Store Modal -->
    <div class="modal fade" id="removeStoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Store from Map</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong id="removeStoreName"></strong> from the map?</p>
                    <div class="mb-3">
                        <label for="removeReason" class="form-label">Reason</label>
                        <textarea class="form-control" id="removeReason" rows="3" placeholder="Enter reason for removal...">Low ratings</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="confirmRemoveStore()">Remove from Map</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ban Store Modal -->
    <div class="modal fade" id="banStoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ban Store</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="banStoreMessage"></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> Banning a store will automatically ban the store owner's admin account. The admin will not be able to log in to the system.
                    </div>
                    <div class="mb-3">
                        <label for="banReason" class="form-label">Reason</label>
                        <textarea class="form-control" id="banReason" rows="3" placeholder="Enter reason for banning...">Low ratings</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="banDurationDays" class="form-label">Ban Duration (Days)</label>
                        <input type="number" class="form-control" id="banDurationDays" min="1" placeholder="Leave empty for permanent ban">
                        <small class="form-text text-muted">Enter number of days to ban (e.g., 7, 30, 90). Leave empty for permanent ban. This applies to both the store and the admin account.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmBanStore()">Ban Store & Admin</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentStoreId = null;
        let currentBanAdmin = false;
        let currentAdminId = null;

        function removeStoreFromMap(storeId, storeName) {
            currentStoreId = storeId;
            document.getElementById('removeStoreName').textContent = storeName;
            const modal = new bootstrap.Modal(document.getElementById('removeStoreModal'));
            modal.show();
        }

        function confirmRemoveStore() {
            const reason = document.getElementById('removeReason').value || 'Low ratings';
            
            fetch('<?= BASE_URL ?>control-panel/removeStoreFromMap', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `store_id=${currentStoreId}&reason=${encodeURIComponent(reason)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Store removed from map successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function banStore(storeId, storeName, banAdmin, adminId = null) {
            currentStoreId = storeId;
            currentBanAdmin = true; // Always ban admin when store is banned
            currentAdminId = adminId;
            
            const message = `Are you sure you want to ban <strong>${storeName}</strong>? This will:<br>
                <ul>
                    <li>Remove the store from the map</li>
                    <li>Automatically ban the store owner's admin account</li>
                    <li>Prevent the admin from logging in to the system</li>
                </ul>`;
            
            document.getElementById('banStoreMessage').innerHTML = message;
            const modal = new bootstrap.Modal(document.getElementById('banStoreModal'));
            modal.show();
        }

        function confirmBanStore() {
            const reason = document.getElementById('banReason').value || 'Low ratings';
            const banDurationDays = document.getElementById('banDurationDays').value;
            
            // Always set ban_admin to true since we always ban the admin when store is banned
            let body = `store_id=${currentStoreId}&reason=${encodeURIComponent(reason)}&ban_admin=true`;
            if (banDurationDays && banDurationDays > 0) {
                body += `&ban_duration_days=${encodeURIComponent(banDurationDays)}`;
            }
            
            fetch('<?= BASE_URL ?>control-panel/banStore', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: body
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error banning store. Please try again.');
            });
        }
    </script>
</body>
</html>

