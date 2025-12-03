<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'customer_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">New Order</h1>
<p class="mb-4">Create a new repair or restoration order.</p>

<!-- Order Form -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo BASE_URL; ?>customer/processOrder">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="service_category">Service Category <span class="text-danger">*</span></label>
                        <select class="form-control" id="service_category" name="service_category" required>
                            <option value="">Select Category</option>
                            <option value="1">Vehicle Covers</option>
                            <option value="2">Bedding</option>
                            <option value="3">Furniture</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="service">Service Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="service" name="service_id" required>
                            <option value="">Select Service</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="pickup_date">Preferred Pickup Date</label>
                        <input type="date" class="form-control" id="pickup_date" name="pickup_date">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="item_description">Item Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="item_description" name="item_description" rows="4" 
                    placeholder="Please provide detailed description of the item and the repair needed..." required></textarea>
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" 
                    placeholder="Any special instructions or requests..."></textarea>
            </div>

            <div class="form-group">
                <label for="images">Upload Images (Optional)</label>
                <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*">
                <small class="form-text text-muted">Upload photos of the item for better assessment.</small>
            </div>

            <hr>

            <div class="text-right">
                <a href="<?php echo BASE_URL; ?>customer/dashboard" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Order</button>
            </div>
        </form>
    </div>
</div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

