<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<style>
.service-card {
    border-radius: 0.75rem;
    border: 1px solid #e3e6f0;
    transition: all 0.3s;
    height: 100%;
}

.service-card:hover {
    box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
    transform: translateY(-5px);
}

.service-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.category-vehicle { background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%); }
.category-bedding { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.category-furniture { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

/* Override Bootstrap primary colors with brown */
.btn-primary {
    background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%) !important;
    border-color: #1F4E79 !important;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1F4E79 0%, #4CAF50 50%, #0F3C5F 100%) !important;
    border-color: #4CAF50 !important;
}

.text-primary {
    color: #1F4E79 !important;
}
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Our Services</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>customer/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Services</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Service Categories -->
<?php 
$currentCategory = '';
foreach ($services as $service): 
    $categoryName = isset($service['category_name']) ? $service['category_name'] : 'General Services';
    
    if ($currentCategory !== $categoryName): 
        if ($currentCategory !== '') echo '</div>'; // Close previous row
        $currentCategory = $categoryName;
        
        // Determine category class
        $categoryClass = 'category-vehicle'; // default
        if (stripos($categoryName, 'Bedding') !== false || stripos($categoryName, 'Bed') !== false) {
            $categoryClass = 'category-bedding';
        } elseif (stripos($categoryName, 'Furniture') !== false) {
            $categoryClass = 'category-furniture';
        }
        
        $categoryIcon = isset($service['category_icon']) ? $service['category_icon'] : 'tag';
?>

<h4 class="mb-3 mt-4">
    <span class="service-icon <?php echo $categoryClass; ?> d-inline-flex mr-2">
        <i class="fas fa-<?php echo htmlspecialchars($categoryIcon); ?>"></i>
    </span>
    <?php echo htmlspecialchars($currentCategory); ?>
</h4>
<div class="row">
<?php 
    endif; 
?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card service-card">
            <div class="card-body">
                <h5 class="card-title font-weight-bold"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                <p class="card-text text-muted small"><?php echo htmlspecialchars($service['description'] ?? 'No description available'); ?></p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <?php 
                        $price = $service['price'] ?? $service['base_price'] ?? 0;
                        ?>
                        <div class="text-primary font-weight-bold h5 mb-0">₱<?php echo number_format($price, 2); ?></div>
                        <small class="text-muted"><?php echo isset($service['estimated_days']) ? $service['estimated_days'] : '7'; ?> days</small>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="bookService(<?php echo $service['id']; ?>)">
                        <i class="fas fa-calendar-plus mr-1"></i>Book Now
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php 
endforeach; 
if ($currentCategory !== '') echo '</div>'; // Close last row
?>

<!-- No services available message -->
<?php if (empty($services)): ?>
<div class="row">
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No Services Available</h4>
            <p class="text-muted">There are currently no services available. Please check back later.</p>
        </div>
    </div>
</div>
<?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<script>
function bookService(serviceId) {
    window.location.href = '<?php echo BASE_URL; ?>customer/newBooking?service=' + serviceId;
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

