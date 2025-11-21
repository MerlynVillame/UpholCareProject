<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Banned Stores' ?> - UphoCare</title>
    
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
            display: flex;
            align-items: center;
            gap: 10px;
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
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }

        .stats-card .card-body {
            padding: 25px;
        }

        .store-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #dc3545;
        }

        .store-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .store-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
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
            margin-bottom: 5px;
        }

        .ban-info {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .ban-info-title {
            font-weight: 700;
            color: #721c24;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 500;
        }

        .store-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .btn-unban {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-unban:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .badge-permanent {
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-temporary {
            background: #ffc107;
            color: #212529;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-expired {
            background: #6c757d;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .no-stores {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .no-stores i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .no-stores h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <?php require_once ROOT . DS . 'views' . DS . 'control_panel' . DS . 'layouts' . DS . 'sidebar.php'; ?>
    
    <div class="main-content-wrapper">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <h1 class="page-title">
                    <i class="<?= $data['page_icon'] ?? 'fas fa-ban' ?>"></i>
                    <?= $data['page_title'] ?? 'Banned Stores List' ?>
                </h1>
                <p class="page-subtitle"><?= $data['page_subtitle'] ?? 'View all banned stores and their ban information' ?></p>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="small text-muted mb-2">Total Banned Stores</div>
                            <h3 class="mb-0"><?= $data['stats']['total_banned_stores'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="small text-muted mb-2">Permanently Banned</div>
                            <h3 class="mb-0 text-danger"><?= $data['stats']['permanently_banned'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="small text-muted mb-2">Temporarily Banned</div>
                            <h3 class="mb-0 text-warning"><?= $data['stats']['temporarily_banned'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="small text-muted mb-2">Expired Bans</div>
                            <h3 class="mb-0 text-secondary"><?= $data['stats']['expired_bans'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="filterBanType" class="form-label">Filter by Ban Type</label>
                        <select class="form-select" id="filterBanType" onchange="filterBannedStores()">
                            <option value="all">All Banned Stores</option>
                            <option value="permanent">Permanent Bans</option>
                            <option value="temporary">Temporary Bans</option>
                            <option value="expired">Expired Bans</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="searchStore" class="form-label">Search Store Name</label>
                        <input type="text" class="form-control" id="searchStore" placeholder="Type store name to search..." onkeyup="filterBannedStores()">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Banned Stores List -->
            <?php if (empty($data['banned_stores'])): ?>
                <div class="no-stores">
                    <i class="fas fa-ban"></i>
                    <h3>No Banned Stores</h3>
                    <p>There are no banned stores in the system.</p>
                </div>
            <?php else: ?>
                <div id="bannedStoresList">
                <?php foreach ($data['banned_stores'] as $store): ?>
                    <?php
                    // Determine ban status
                    $isExpired = false;
                    $isPermanent = false;
                    $isTemporary = false;
                    
                    if (empty($store['banned_until']) || empty($store['ban_duration_days'])) {
                        $isPermanent = true;
                        $banType = 'permanent';
                    } elseif (strtotime($store['banned_until']) <= time()) {
                        $isExpired = true;
                        $banType = 'expired';
                    } else {
                        $isTemporary = true;
                        $banType = 'temporary';
                    }
                    
                    // Calculate days remaining
                    $daysRemaining = 0;
                    if ($isTemporary && $store['banned_until']) {
                        $daysRemaining = max(0, ceil((strtotime($store['banned_until']) - time()) / 86400));
                    }
                    
                    // Calculate days since banned
                    $daysSinceBanned = 0;
                    if ($store['banned_at']) {
                        $daysSinceBanned = floor((time() - strtotime($store['banned_at'])) / 86400);
                    }
                    ?>
                    <div class="store-card" data-ban-type="<?= $banType ?>" data-store-name="<?= strtolower(htmlspecialchars($store['store_name'])) ?>">
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
                            <div>
                                <?php if ($isPermanent): ?>
                                    <span class="badge-permanent">
                                        <i class="fas fa-ban"></i> Permanent Ban
                                    </span>
                                <?php elseif ($isExpired): ?>
                                    <span class="badge-expired">
                                        <i class="fas fa-clock"></i> Ban Expired
                                    </span>
                                <?php else: ?>
                                    <span class="badge-temporary">
                                        <i class="fas fa-hourglass-half"></i> Temporary Ban
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="ban-info">
                            <div class="ban-info-title">
                                <i class="fas fa-info-circle"></i>
                                Ban Information
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Banned On</div>
                                        <div class="info-value">
                                            <?= $store['banned_at'] ? date('Y-m-d H:i:s', strtotime($store['banned_at'])) : 'N/A' ?>
                                            <?php if ($daysSinceBanned > 0): ?>
                                                <small class="text-muted">(<?= $daysSinceBanned ?> day(s) ago)</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Ban Duration</div>
                                        <div class="info-value">
                                            <?php if ($isPermanent): ?>
                                                <span class="text-danger">Permanent</span>
                                            <?php elseif ($store['ban_duration_days']): ?>
                                                <?= $store['ban_duration_days'] ?> day(s)
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Banned Until</div>
                                        <div class="info-value">
                                            <?php if ($isPermanent): ?>
                                                <span class="text-danger">Never (Permanent)</span>
                                            <?php elseif ($store['banned_until']): ?>
                                                <?= date('Y-m-d H:i:s', strtotime($store['banned_until'])) ?>
                                                <?php if ($isTemporary): ?>
                                                    <br><small class="text-warning">(<?= $daysRemaining ?> day(s) remaining)</small>
                                                <?php elseif ($isExpired): ?>
                                                    <br><small class="text-secondary">(Expired)</small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Ban Reason</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($store['ban_reason'] ?? 'No reason provided') ?>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Banned By</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($store['banned_by_name'] ?? 'Unknown') ?>
                                        </div>
                                    </div>
                                    <?php if ($store['admin_name'] || $store['admin_email']): ?>
                                    <div class="info-item">
                                        <div class="info-label">Store Owner (Admin)</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($store['admin_name'] ?? 'Unknown') ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($store['admin_email'] ?? 'N/A') ?></small>
                                            <br>
                                            <?php 
                                            // If store is banned, admin should be banned too
                                            // Check if admin has banned stores (based on email)
                                            $adminHasBannedStores = true; // Since this store is banned, admin is banned
                                            ?>
                                            <?php if ($adminHasBannedStores || ($store['admin_status'] ?? '') == 'inactive'): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-ban"></i> Banned (Cannot Log In)
                                                </span>
                                                <br><small class="text-danger" style="font-size: 11px;">
                                                    Admin account is banned because store is banned
                                                </small>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle"></i> Check Required
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="store-actions">
                            <button class="btn btn-unban" onclick="unbanStore(<?= $store['id'] ?>, '<?= htmlspecialchars($store['store_name'], ENT_QUOTES) ?>')">
                                <i class="fas fa-check-circle"></i> Unban Store Only
                            </button>
                            <?php if ($store['admin_email']): ?>
                            <button class="btn btn-primary" onclick="unbanAdmin('<?= htmlspecialchars($store['admin_email'], ENT_QUOTES) ?>', '<?= htmlspecialchars($store['admin_name'] ?? 'Admin', ENT_QUOTES) ?>')" 
                                    title="Unban admin account. Admin will remain banned even if store is unbanned until you click this button.">
                                <i class="fas fa-user-check"></i> Unban Admin Account
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Filter banned stores
        function filterBannedStores() {
            const banTypeFilter = document.getElementById('filterBanType').value;
            const searchTerm = document.getElementById('searchStore').value.toLowerCase().trim();
            const storeCards = document.querySelectorAll('#bannedStoresList .store-card');
            
            let visibleCount = 0;
            storeCards.forEach(card => {
                const banType = card.getAttribute('data-ban-type');
                const storeName = card.getAttribute('data-store-name');
                
                let show = true;
                
                // Filter by ban type
                if (banTypeFilter !== 'all' && banType !== banTypeFilter) {
                    show = false;
                }
                
                // Filter by search term
                if (searchTerm && !storeName.includes(searchTerm)) {
                    show = false;
                }
                
                if (show) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            let noResultsMsg = document.getElementById('noResultsMessage');
            if (visibleCount === 0 && (banTypeFilter !== 'all' || searchTerm)) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMessage';
                    noResultsMsg.className = 'alert alert-warning text-center mt-3';
                    noResultsMsg.innerHTML = '<i class="fas fa-search"></i> No banned stores found matching the filter criteria.';
                    document.getElementById('bannedStoresList').parentNode.insertBefore(noResultsMsg, document.getElementById('bannedStoresList').nextSibling);
                }
            } else {
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
        }
        
        // Reset filters
        function resetFilters() {
            document.getElementById('filterBanType').value = 'all';
            document.getElementById('searchStore').value = '';
            filterBannedStores();
        }
        
        // Unban store
        function unbanStore(storeId, storeName) {
            if (!confirm(`Are you sure you want to unban "${storeName}"?\n\nNote: This will restore the store to the map and make it visible to customers. However, the admin account will remain banned and cannot log in until you explicitly unban the admin account.`)) {
                return;
            }
            
            fetch('<?= BASE_URL ?>control-panel/unbanStore', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `store_id=${storeId}`
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
                alert('Error unbanning store. Please try again.');
            });
        }
        
        // Unban admin account
        function unbanAdmin(adminEmail, adminName) {
            if (!confirm(`Are you sure you want to unban the admin account "${adminName}" (${adminEmail})?\n\nThis will allow the admin to log in to the system again.`)) {
                return;
            }
            
            fetch('<?= BASE_URL ?>control-panel/unbanAdmin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `admin_email=${encodeURIComponent(adminEmail)}`
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
                alert('Error unbanning admin. Please try again.');
            });
        }
    </script>
</body>
</html>

