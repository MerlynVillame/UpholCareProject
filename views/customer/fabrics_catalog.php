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

.category-standard .category-badge { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.category-premium .category-badge { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }

.color-card {
    background: white;
    border-radius: 1.25rem;
    border: 2px solid #e3e6f0;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

.color-card:hover {
    box-shadow: 0 15px 40px rgba(139, 69, 19, 0.3);
    transform: translateY(-12px) scale(1.02);
    border-color: #8B4513;
}

.color-card::before {
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

.color-card:hover::before {
    transform: scaleX(1);
}

.color-swatch {
    width: 100%;
    height: 180px;
    border-bottom: 2px solid #e3e6f0;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.color-swatch::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(0,0,0,0.1) 100%);
    pointer-events: none;
}

.color-card-body {
    padding: 2rem;
}

.color-name {
    font-size: 1.35rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.color-name i {
    color: #8B4513;
    margin-right: 0.75rem;
    font-size: 1.25rem;
}

.color-code {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    font-family: 'Courier New', monospace;
}

.color-info {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
}

.color-price {
    flex: 1;
    text-align: center;
}

.price-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.price-amount {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.2;
}

.color-stock {
    flex: 1;
    text-align: center;
    border-left: 2px solid #e3e6f0;
    padding-left: 1.25rem;
}

.stock-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.stock-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #8B4513;
}

.fabric-badge {
    display: inline-block;
    padding: 0.35rem 0.85rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-standard {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.badge-premium {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.badge-in-stock {
    background: #28a745;
    color: white;
}

.badge-low-stock {
    background: #ffc107;
    color: #212529;
}

.badge-out-of-stock {
    background: #dc3545;
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

.store-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #e3e6f0;
    color: #6c757d;
    border-radius: 1rem;
    font-size: 0.8rem;
    margin-top: 0.5rem;
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
    
    .color-info {
        flex-direction: column;
    }
    
    .color-stock {
        border-left: none;
        border-top: 2px solid #e3e6f0;
        padding-left: 0;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    
    .filter-tags {
        justify-content: center;
    }
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
</style>

<!-- Catalog Header -->
<div class="catalog-header text-center">
    <h1><i class="fas fa-palette mr-3"></i>Fabric/Color Catalog</h1>
    <p>Browse our extensive collection of fabrics and colors. Find the perfect match for your upholstery needs.</p>
</div>

<?php if (empty($colors)): ?>
    <!-- Empty State -->
    <div class="empty-state">
        <i class="fas fa-palette"></i>
        <h4>No Colors Available</h4>
        <p>We're currently updating our fabric/color catalog. Please check back later.</p>
    </div>
<?php else: ?>
    <!-- Search and Filter Section -->
    <div class="search-filter-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search colors by name or code...">
        </div>
        <div class="filter-tags" id="filterTags">
            <div class="filter-tag active" data-filter="all">All Colors</div>
            <div class="filter-tag" data-filter="standard">Standard</div>
            <div class="filter-tag" data-filter="premium">Premium</div>
            <?php
            $uniqueStores = [];
            foreach ($colors as $color) {
                if (!empty($color['store_name']) && !in_array($color['store_name'], $uniqueStores)) {
                    $uniqueStores[] = $color['store_name'];
                }
            }
            foreach ($uniqueStores as $storeName):
            ?>
                <div class="filter-tag" data-filter="store-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $storeName))); ?>">
                    <?php echo htmlspecialchars($storeName); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="results-count" id="resultsCount">
            Showing all <?php echo count($colors); ?> colors
        </div>
    </div>

    <!-- Color Categories -->
    <div id="colorsContainer">
        <?php 
        $currentFabricType = '';
        $fabricTypeCount = 0;
        
        // Group colors by fabric type
        $standardColors = array_filter($colors, function($c) { return ($c['fabric_type'] ?? 'standard') === 'standard'; });
        $premiumColors = array_filter($colors, function($c) { return ($c['fabric_type'] ?? 'standard') === 'premium'; });
        
        // Display Standard Colors
        if (!empty($standardColors)): ?>
            <div class="category-section category-standard" data-category="standard">
                <div class="category-header">
                    <div class="category-badge">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="category-title">
                        <h3>Standard Colors</h3>
                        <p>Affordable and durable fabric options</p>
                    </div>
                </div>
                
                <div class="row">
                    <?php foreach ($standardColors as $color): 
                        $basePrice = floatval($color['price_per_unit'] ?? 0);
                        $status = $color['status'] ?? 'in-stock';
                        $quantity = floatval($color['quantity'] ?? 0);
                        $storeName = $color['store_name'] ?? 'All Stores';
                    ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 px-3 color-item" 
                             data-name="<?php echo strtolower(htmlspecialchars($color['color_name'] ?? '')); ?>" 
                             data-code="<?php echo strtolower(htmlspecialchars($color['color_code'] ?? '')); ?>"
                             data-fabric-type="standard"
                             data-store="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $storeName))); ?>"
                             data-color-id="<?php echo $color['id']; ?>"
                             data-color-name="<?php echo htmlspecialchars($color['color_name'] ?? ''); ?>"
                             data-color-code="<?php echo htmlspecialchars($color['color_code'] ?? ''); ?>"
                             data-color-hex="<?php echo htmlspecialchars($color['color_hex'] ?? '#000000'); ?>"
                             data-price="<?php echo number_format($basePrice, 2); ?>"
                             data-status="<?php echo htmlspecialchars($status); ?>"
                             data-quantity="<?php echo $quantity; ?>"
                             data-store-name="<?php echo htmlspecialchars($storeName); ?>">
                            <div class="color-card">
                                <div class="color-swatch" style="background-color: <?php echo htmlspecialchars($color['color_hex'] ?? '#000000'); ?>;"></div>
                                <div class="color-card-body">
                                    <div class="color-name">
                                        <span><i class="fas fa-palette"></i><?php echo htmlspecialchars($color['color_name'] ?? 'Unnamed Color'); ?></span>
                                        <span class="fabric-badge badge-standard">Standard</span>
                                    </div>
                                    <div class="color-code"><?php echo htmlspecialchars($color['color_code'] ?? ''); ?></div>
                                    
                                    <div class="color-info">
                                        <div class="color-price">
                                            <div class="price-label">Price</div>
                                            <div class="price-amount">₱<?php echo number_format($basePrice, 2); ?></div>
                                        </div>
                                        
                                        <div class="color-stock">
                                            <div class="stock-label">Stock</div>
                                            <div class="stock-value"><?php echo number_format($quantity, 2); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <?php if ($status === 'in-stock'): ?>
                                            <span class="fabric-badge badge-in-stock">In Stock</span>
                                        <?php elseif ($status === 'low-stock'): ?>
                                            <span class="fabric-badge badge-low-stock">Low Stock</span>
                                        <?php else: ?>
                                            <span class="fabric-badge badge-out-of-stock">Out of Stock</span>
                                        <?php endif; ?>
                                        <?php if ($storeName !== 'All Stores'): ?>
                                            <div class="store-badge"><?php echo htmlspecialchars($storeName); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Display Premium Colors -->
        <?php if (!empty($premiumColors)): ?>
            <div class="category-section category-premium" data-category="premium">
                <div class="category-header">
                    <div class="category-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="category-title">
                        <h3>Premium Colors</h3>
                        <p>High-quality premium fabric options</p>
                    </div>
                </div>
                
                <div class="row">
                    <?php foreach ($premiumColors as $color): 
                        $basePrice = floatval($color['price_per_unit'] ?? 0);
                        $premiumPrice = floatval($color['premium_price'] ?? 0);
                        $totalPrice = $basePrice + $premiumPrice;
                        $status = $color['status'] ?? 'in-stock';
                        $quantity = floatval($color['quantity'] ?? 0);
                        $storeName = $color['store_name'] ?? 'All Stores';
                    ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 px-3 color-item" 
                             data-name="<?php echo strtolower(htmlspecialchars($color['color_name'] ?? '')); ?>" 
                             data-code="<?php echo strtolower(htmlspecialchars($color['color_code'] ?? '')); ?>"
                             data-fabric-type="premium"
                             data-store="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $storeName))); ?>"
                             data-color-id="<?php echo $color['id']; ?>"
                             data-color-name="<?php echo htmlspecialchars($color['color_name'] ?? ''); ?>"
                             data-color-code="<?php echo htmlspecialchars($color['color_code'] ?? ''); ?>"
                             data-color-hex="<?php echo htmlspecialchars($color['color_hex'] ?? '#000000'); ?>"
                             data-price="<?php echo number_format($totalPrice, 2); ?>"
                             data-status="<?php echo htmlspecialchars($status); ?>"
                             data-quantity="<?php echo $quantity; ?>"
                             data-store-name="<?php echo htmlspecialchars($storeName); ?>">
                            <div class="color-card">
                                <div class="color-swatch" style="background-color: <?php echo htmlspecialchars($color['color_hex'] ?? '#000000'); ?>;"></div>
                                <div class="color-card-body">
                                    <div class="color-name">
                                        <span><i class="fas fa-star"></i><?php echo htmlspecialchars($color['color_name'] ?? 'Unnamed Color'); ?></span>
                                        <span class="fabric-badge badge-premium">Premium</span>
                                    </div>
                                    <div class="color-code"><?php echo htmlspecialchars($color['color_code'] ?? ''); ?></div>
                                    
                                    <div class="color-info">
                                        <div class="color-price">
                                            <div class="price-label">Price</div>
                                            <div class="price-amount">₱<?php echo number_format($totalPrice, 2); ?></div>
                                        </div>
                                        
                                        <div class="color-stock">
                                            <div class="stock-label">Stock</div>
                                            <div class="stock-value"><?php echo number_format($quantity, 2); ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <?php if ($status === 'in-stock'): ?>
                                            <span class="fabric-badge badge-in-stock">In Stock</span>
                                        <?php elseif ($status === 'low-stock'): ?>
                                            <span class="fabric-badge badge-low-stock">Low Stock</span>
                                        <?php else: ?>
                                            <span class="fabric-badge badge-out-of-stock">Out of Stock</span>
                                        <?php endif; ?>
                                        <?php if ($storeName !== 'All Stores'): ?>
                                            <div class="store-badge"><?php echo htmlspecialchars($storeName); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
<?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<script>
// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterTags = document.querySelectorAll('.filter-tag');
    const colorItems = document.querySelectorAll('.color-item');
    const categorySections = document.querySelectorAll('.category-section');
    const resultsCount = document.getElementById('resultsCount');
    
    let activeFilter = 'all';
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterColors(activeFilter, searchTerm);
        });
    }
    
    // Filter tag functionality
    filterTags.forEach(tag => {
        tag.addEventListener('click', function() {
            // Update active state
            filterTags.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            activeFilter = this.getAttribute('data-filter');
            filterColors(activeFilter, searchInput ? searchInput.value.toLowerCase() : '');
        });
    });
    
    function filterColors(filterType, searchTerm) {
        let visibleCount = 0;
        let categoryVisibleMap = {};
        
        colorItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const code = item.getAttribute('data-code');
            const fabricType = item.getAttribute('data-fabric-type');
            const store = item.getAttribute('data-store');
            const category = item.closest('.category-section');
            const categoryId = category ? category.getAttribute('data-category') : '';
            
            // Check if item matches filter
            let matchesFilter = false;
            if (filterType === 'all') {
                matchesFilter = true;
            } else if (filterType === 'standard' && fabricType === 'standard') {
                matchesFilter = true;
            } else if (filterType === 'premium' && fabricType === 'premium') {
                matchesFilter = true;
            } else if (filterType.startsWith('store-') && store === filterType) {
                matchesFilter = true;
            }
            
            const matchesSearch = !searchTerm || 
                                 name.includes(searchTerm) || 
                                 code.includes(searchTerm);
            
            if (matchesFilter && matchesSearch) {
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
            const hasVisibleItems = Array.from(row.querySelectorAll('.color-item')).some(item => 
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
            resultsCount.textContent = `Showing ${visibleCount} ${visibleCount === 1 ? 'color' : 'colors'}`;
            if (visibleCount === 0) {
                resultsCount.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>No colors found matching your criteria';
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
    
    colorItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(item);
    });
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>






