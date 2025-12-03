<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
/* Enhanced Catalog Page Styles */
.catalog-header {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    padding: 3rem 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.catalog-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.catalog-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    position: relative;
}

.catalog-header p {
    font-size: 1.1rem;
    opacity: 0.95;
    position: relative;
}

/* Search and Filter Section */
.search-filter-section {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.search-box {
    position: relative;
    margin-bottom: 1.5rem;
}

.search-box input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid #e3e6f0;
    border-radius: 0.75rem;
    font-size: 1rem;
    transition: all 0.3s;
}

.search-box input:focus {
    border-color: #8B4513;
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
    align-items: center;
}

.filter-tag {
    padding: 0.5rem 1.25rem;
    background: #f8f9fc;
    border: 2px solid #e3e6f0;
    border-radius: 2rem;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.9rem;
    font-weight: 500;
}

.filter-tag:hover {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    color: white;
    border-color: #8B4513;
    transform: translateY(-2px);
}

.filter-tag.active {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(139, 69, 19, 0.4);
}

.results-count {
    text-align: center;
    padding: 1rem;
    background: #f8f9fc;
    border-radius: 0.75rem;
    margin-bottom: 2rem;
    color: #6c757d;
}

.category-section {
    margin-bottom: 4rem;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.category-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid #e3e6f0;
    position: relative;
}

.category-header::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 100px;
    height: 3px;
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
}

.category-badge {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin-right: 1.5rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: transform 0.3s;
}

.category-header:hover .category-badge {
    transform: scale(1.1) rotate(5deg);
}

.category-title h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #2c3e50;
}

.category-title p {
    color: #6c757d;
    margin: 0;
    font-size: 1rem;
}

.category-vehicle .category-badge { background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); }
.category-bedding .category-badge { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.category-furniture .category-badge { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.category-general .category-badge { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.service-card {
    background: white;
    border-radius: 1.25rem;
    border: 2px solid #e3e6f0;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
}

.service-card:hover {
    box-shadow: 0 15px 40px rgba(139, 69, 19, 0.3);
    transform: translateY(-12px) scale(1.02);
    border-color: #8B4513;
}

.service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.service-card:hover::before {
    transform: scaleX(1);
}

.service-card-body {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.service-name {
    font-size: 1.35rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.service-name i {
    color: #8B4513;
    margin-right: 0.75rem;
    font-size: 1.25rem;
}

.service-description {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.7;
    margin-bottom: 1.5rem;
    min-height: 60px;
    flex: 1;
}

.service-info {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
}

.service-price {
    flex: 1;
    text-align: center;
}

.price-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    font-weight: 600;
    white-space: nowrap;
}

.price-amount {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.2;
}

.service-duration {
    flex: 1;
    text-align: center;
    border-left: 2px solid #e3e6f0;
    padding-left: 1.25rem;
}

.duration-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.duration-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #8B4513;
}

.btn-book-service {
    width: 100%;
    padding: 1.25rem;
    font-size: 1.05rem;
    font-weight: 700;
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    border: none;
    border-radius: 0.875rem;
    color: white;
    transition: all 0.3s;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(139, 69, 19, 0.4);
}

.btn-book-service:hover {
    background: linear-gradient(135deg, #A0522D 0%, #8B4513 50%, #654321 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(139, 69, 19, 0.5);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border-radius: 1.5rem;
    border: 2px dashed #e3e6f0;
}

.empty-state i {
    font-size: 5rem;
    color: #d1d3e2;
    margin-bottom: 1.5rem;
}

.empty-state h4 {
    color: #6c757d;
    margin-bottom: 0.75rem;
    font-size: 1.5rem;
}

.empty-state p {
    color: #858796;
    font-size: 1.1rem;
}

/* Enhanced Service Card Features */
.service-card {
    position: relative;
}

.service-card::after {
    content: '';
    position: absolute;
    top: 20px;
    right: 20px;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, rgba(139, 69, 19, 0.1) 0%, rgba(160, 82, 45, 0.1) 100%);
    border-radius: 50%;
    opacity: 0;
    transition: all 0.3s;
}

.service-card:hover::after {
    opacity: 1;
    transform: scale(1.5);
}

/* Loading State */
.loading-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

/* Enhanced Button Styles */
.btn-book-service {
    position: relative;
    overflow: hidden;
}

.btn-book-service::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-book-service:hover::before {
    width: 300px;
    height: 300px;
}

.btn-book-service i {
    position: relative;
    z-index: 1;
}

/* Smooth Transitions for All Elements */
.service-card,
.filter-tag,
.search-box input,
.btn-book-service {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced Typography */
.service-name {
    text-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.price-amount {
    text-shadow: 0 2px 8px rgba(139, 69, 19, 0.2);
}

/* Category Section Enhancement */
.category-section {
    padding: 2rem 0;
    border-radius: 1.5rem;
    transition: all 0.3s;
    width: 100%;
    overflow: hidden;
}

.category-section:hover {
    background: linear-gradient(135deg, rgba(139, 69, 19, 0.02) 0%, rgba(160, 82, 45, 0.02) 100%);
}

/* Override Bootstrap primary colors with brown */
.btn-primary,
.text-primary {
    color: #8B4513 !important;
}

.btn-primary {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
    border-color: #A0522D !important;
    color: white !important;
}

.btn-outline-primary {
    color: #8B4513 !important;
    border-color: #8B4513 !important;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-info {
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%) !important;
    border-color: #8B4513 !important;
    color: white !important;
}

.btn-info:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #654321 100%) !important;
    border-color: #A0522D !important;
    color: white !important;
}

/* Add spacing around service cards */
.service-item {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    margin-bottom: 1.5rem;
}

#servicesContainer .row {
    margin-left: -0.75rem;
    margin-right: -0.75rem;
    display: flex;
    flex-wrap: wrap;
}

#servicesContainer .row > [class*="col-"] {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

/* Modal enhancements */
.modal-content {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3);
}

.modal-backdrop.show {
    opacity: 0.7;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .catalog-header {
        padding: 2rem 1.5rem;
    }
    
    .catalog-header h1 {
        font-size: 2rem;
    }
    
    .category-header {
        flex-direction: column;
        text-align: center;
    }
    
    .category-badge {
        margin: 0 auto 1rem;
    }
    
    .service-info {
        flex-direction: column;
    }
    
    .service-duration {
        border-left: none;
        border-top: 2px solid #e3e6f0;
        padding-left: 0;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    
    .filter-tags {
        justify-content: center;
    }
    
    .service-item {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        margin-bottom: 1rem;
    }
    
    #servicesContainer .row {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    #servicesContainer .row > [class*="col-"] {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}
</style>

<!-- Catalog Header -->
<div class="catalog-header text-center">
    <h1><i class="fas fa-store mr-3"></i>Services Catalog</h1>
    <p>Browse our recommended services curated by our team. Select your preferred service to book.</p>
</div>

<?php if (empty($services)): ?>
    <!-- Empty State -->
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h4>No Services Available</h4>
        <p>We're currently updating our services catalog. Please check back later.</p>
    </div>
<?php else: ?>
    <!-- Search Section -->
    <div class="search-filter-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search services by name or description...">
        </div>
        <div class="results-count" id="resultsCount">
            Showing all <?php echo count($services); ?> services
        </div>
    </div>
    
    <!-- Category Filters Section -->
    <div class="search-filter-section" style="margin-top: 1rem;">
        <div class="filter-tags" id="filterTags">
            <div class="filter-tag active" data-filter="all">All Services</div>
            <?php
            $uniqueCategories = [];
            foreach ($services as $service) {
                $cat = isset($service['category_name']) ? $service['category_name'] : 'General Services';
                if (!in_array($cat, $uniqueCategories)) {
                    $uniqueCategories[] = $cat;
                    echo '<div class="filter-tag" data-filter="' . htmlspecialchars(strtolower(str_replace(' ', '-', $cat))) . '">' . htmlspecialchars($cat) . '</div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Service Categories -->
    <div id="servicesContainer" style="width: 100%; overflow: hidden;">
        <?php 
        $currentCategory = '';
        $categoryCount = 0;
        
        foreach ($services as $service): 
            $categoryName = isset($service['category_name']) ? $service['category_name'] : 'General Services';
            $categoryIcon = isset($service['category_icon']) ? $service['category_icon'] : 'tag';
            
            if ($currentCategory !== $categoryName): 
                if ($currentCategory !== ''): ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php 
                $currentCategory = $categoryName;
                $categoryCount++;
                
                // Determine category class
                $categoryClass = 'category-general';
                if (stripos($categoryName, 'Vehicle') !== false || stripos($categoryName, 'Truck') !== false || stripos($categoryName, 'Car') !== false) {
                    $categoryClass = 'category-vehicle';
                } elseif (stripos($categoryName, 'Bedding') !== false || stripos($categoryName, 'Bed') !== false) {
                    $categoryClass = 'category-bedding';
                } elseif (stripos($categoryName, 'Furniture') !== false) {
                    $categoryClass = 'category-furniture';
                }
                ?>
                
                <!-- Category Section -->
                <div class="category-section <?php echo $categoryClass; ?>" data-category="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $categoryName))); ?>">
                    <div class="category-header">
                        <div class="category-badge">
                            <i class="fas fa-<?php echo htmlspecialchars($categoryIcon); ?>"></i>
                        </div>
                        <div class="category-title">
                            <h3><?php echo htmlspecialchars($currentCategory); ?></h3>
                            <p>Premium <?php echo htmlspecialchars(strtolower($currentCategory)); ?> solutions</p>
                        </div>
                    </div>
                    
                    <div class="row">
            <?php endif; ?>
            
             <!-- Service Card -->
             <div class="col-lg-4 col-md-6 service-item" 
                  data-name="<?php echo strtolower(htmlspecialchars($service['service_name'])); ?>" 
                  data-description="<?php echo strtolower(htmlspecialchars($service['description'] ?? '')); ?>"
                  data-service-id="<?php echo $service['id']; ?>"
                  data-service-name="<?php echo htmlspecialchars($service['service_name']); ?>"
                  data-service-desc="<?php echo htmlspecialchars($service['description'] ?? 'Professional service by our expert team'); ?>"
                  data-service-price="<?php echo number_format($service['price'] ?? $service['base_price'] ?? 0, 2); ?>"
                  data-service-days="<?php echo isset($service['estimated_days']) ? $service['estimated_days'] : '7'; ?>"
                  data-category-name="<?php echo htmlspecialchars($service['category_name'] ?? 'General Services'); ?>"
                  data-category-id="<?php echo isset($service['category_id']) ? $service['category_id'] : ''; ?>"
                  data-service-type="<?php echo htmlspecialchars($service['service_type'] ?? ''); ?>">
                <div class="service-card">
                    <div class="service-card-body">
                        <div class="service-name">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($service['service_name']); ?>
                        </div>
                        
                        <div class="service-description">
                            <?php echo htmlspecialchars($service['description'] ?? 'Professional service by our expert team'); ?>
                        </div>
                        
                        <div class="service-info">
                            <div class="service-price">
                                <div class="price-label">From</div>
                                <div class="price-amount">
                                    ₱<?php 
                                    $price = $service['price'] ?? $service['base_price'] ?? 0;
                                    echo number_format($price, 2); 
                                    ?>
                                </div>
                            </div>
                            
                            <div class="service-duration">
                                <div class="duration-label">Estimated Time</div>
                                <div class="duration-value">
                                    <?php echo isset($service['estimated_days']) ? $service['estimated_days'] : '7'; ?> days
                                </div>
                            </div>
                        </div>
                        
                        <button class="btn-book-service" onclick="showServiceModal(<?php echo $service['id']; ?>)">
                            <i class="fas fa-calendar-plus mr-2"></i>Book This Service
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- Close last category -->
        <?php if ($currentCategory !== ''): ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
<?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Service Details Modal -->
<div class="modal fade" id="serviceDetailsModal" tabindex="-1" role="dialog" aria-labelledby="serviceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 1rem; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white; border-radius: 1rem 1rem 0 0;">
                <h5 class="modal-title" id="serviceDetailsModalLabel">
                    <i class="fas fa-info-circle mr-2"></i>Service Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Service Name -->
                <div class="mb-4 text-center">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1rem; background: linear-gradient(135deg, rgba(139, 69, 19, 0.1) 0%, rgba(160, 82, 45, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-tools" style="font-size: 2rem; color: #8B4513;"></i>
                    </div>
                    <h3 id="modalServiceName" class="mb-2" style="color: #2c3e50; font-weight: 700;"></h3>
                    <span id="modalCategoryBadge" class="badge" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white; padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.85rem;"></span>
                </div>
                
                <!-- Service Description -->
                <div class="mb-4">
                    <h6 style="color: #8B4513; font-weight: 700; margin-bottom: 0.75rem;">
                        <i class="fas fa-file-alt mr-2"></i>Description
                    </h6>
                    <p id="modalServiceDesc" style="color: #6c757d; line-height: 1.8; font-size: 1rem;"></p>
                </div>
                
                <!-- Service Details Grid -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3" style="background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); border-radius: 0.75rem; border: 1px solid #e3e6f0;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-tag mr-2" style="color: #8B4513; font-size: 1.25rem;"></i>
                                <h6 class="mb-0" style="color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Price</h6>
                            </div>
                            <h3 id="modalServicePrice" class="mb-0" style="font-weight: 800; background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3" style="background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); border-radius: 0.75rem; border: 1px solid #e3e6f0;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock mr-2" style="color: #8B4513; font-size: 1.25rem;"></i>
                                <h6 class="mb-0" style="color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Estimated Time</h6>
                            </div>
                            <h3 id="modalServiceDays" class="mb-0" style="color: #2c3e50; font-weight: 700;"></h3>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="alert alert-info" style="background: linear-gradient(135deg, rgba(139, 69, 19, 0.05) 0%, rgba(160, 82, 45, 0.05) 100%); border: 1px solid rgba(139, 69, 19, 0.2); border-radius: 0.75rem;">
                    <div class="d-flex">
                        <i class="fas fa-info-circle mt-1 mr-3" style="color: #8B4513; font-size: 1.25rem;"></i>
                        <div>
                            <h6 class="mb-2" style="color: #654321; font-weight: 700;">What to Expect</h6>
                            <ul class="mb-0 pl-3" style="color: #6c757d; line-height: 1.8;">
                                <li>Professional service by our expert team</li>
                                <li>Quality materials and workmanship</li>
                                <li>Warranty on all services provided</li>
                                <li>Flexible scheduling to fit your needs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e3e6f0; padding: 1.5rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem;">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button type="button" class="btn" id="confirmBookingBtn" style="background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%); color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 700; border: none;">
                    <i class="fas fa-calendar-check mr-2"></i>Proceed to Booking
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedServiceId = null;

function showServiceModal(serviceId) {
    // Find service data from the service item
    const serviceItem = document.querySelector(`[data-service-id="${serviceId}"]`);
    if (!serviceItem) return;
    
    // Get service details from data attributes
    const serviceName = serviceItem.getAttribute('data-service-name');
    const serviceDesc = serviceItem.getAttribute('data-service-desc');
    const servicePrice = serviceItem.getAttribute('data-service-price');
    const serviceDays = serviceItem.getAttribute('data-service-days');
    const categoryName = serviceItem.getAttribute('data-category-name');
    const categoryId = serviceItem.getAttribute('data-category-id');
    const serviceType = serviceItem.getAttribute('data-service-type');
    
    // Update modal content
    document.getElementById('modalServiceName').textContent = serviceName;
    document.getElementById('modalServiceDesc').textContent = serviceDesc;
    document.getElementById('modalServicePrice').textContent = '₱' + servicePrice;
    document.getElementById('modalServiceDays').textContent = serviceDays + ' days';
    document.getElementById('modalCategoryBadge').textContent = categoryName;
    
    // Store selected service ID and related data
    selectedServiceId = serviceId;
    
    // Store service data for booking
    sessionStorage.setItem('selectedServiceData', JSON.stringify({
        serviceId: serviceId,
        categoryId: categoryId,
        serviceType: serviceType,
        serviceName: serviceName
    }));
    
    // Show modal
    $('#serviceDetailsModal').modal('show');
}

function bookService(serviceId) {
    // Navigate to repair reservation page with selected service
    window.location.href = '<?php echo BASE_URL; ?>customer/newRepairReservation?service=' + serviceId;
}

// Handle confirm booking button
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmBookingBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (selectedServiceId) {
                bookService(selectedServiceId);
            }
        });
    }
});

// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterTags = document.querySelectorAll('.filter-tag');
    const serviceItems = document.querySelectorAll('.service-item');
    const categorySections = document.querySelectorAll('.category-section');
    const resultsCount = document.getElementById('resultsCount');
    
    let activeFilter = 'all';
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterServices(activeFilter, searchTerm);
        });
    }
    
    // Filter tag functionality
    filterTags.forEach(tag => {
        tag.addEventListener('click', function() {
            // Update active state
            filterTags.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            activeFilter = this.getAttribute('data-filter');
            filterServices(activeFilter, searchInput ? searchInput.value.toLowerCase() : '');
        });
    });
    
    function filterServices(categoryFilter, searchTerm) {
        let visibleCount = 0;
        let categoryVisibleMap = {};
        
        serviceItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const description = item.getAttribute('data-description');
            const category = item.closest('.category-section');
            const categoryId = category ? category.getAttribute('data-category') : '';
            
            // Check if item matches filter
            const matchesCategory = categoryFilter === 'all' || categoryId === categoryFilter;
            const matchesSearch = !searchTerm || 
                                 name.includes(searchTerm) || 
                                 description.includes(searchTerm);
            
            if (matchesCategory && matchesSearch) {
                item.style.display = '';
                visibleCount++;
                if (category) {
                    categoryVisibleMap[categoryId] = true;
                }
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide category sections based on visible items
        categorySections.forEach(section => {
            const categoryId = section.getAttribute('data-category');
            const row = section.querySelector('.row');
            const hasVisibleItems = Array.from(row.querySelectorAll('.service-item')).some(item => 
                item.style.display !== 'none'
            );
            
            if (hasVisibleItems) {
                section.style.display = '';
            } else {
                section.style.display = 'none';
            }
        });
        
        // Update results count
        if (resultsCount) {
            resultsCount.textContent = `Showing ${visibleCount} ${visibleCount === 1 ? 'service' : 'services'}`;
            if (visibleCount === 0) {
                resultsCount.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>No services found matching your criteria';
            }
        }
    }
    
    // Add animation on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    serviceItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(item);
    });
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
