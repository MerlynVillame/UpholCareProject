<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>



<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700;">Manage Booking Numbers</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Booking Numbers</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary-admin" data-toggle="modal" data-target="#addNumbersModal">
        <i class="fas fa-plus mr-2"></i>Add Booking Numbers
    </button>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Available Numbers Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3" style="background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%); color: white;">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-check-circle mr-2"></i>Available Booking Numbers (<?php echo count($availableNumbers); ?>)
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booking Number</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($availableNumbers)): ?>
                        <?php foreach ($availableNumbers as $number): ?>
                        <tr>
                            <td><?php echo $number['id']; ?></td>
                            <td><span class="text-primary-admin font-weight-bold" style="font-size: 0.9rem;"><?php echo htmlspecialchars($number['booking_number']); ?></span></td>
                            <td><?php echo date('M d, Y H:i', strtotime($number['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3" style="opacity: 0.3;"></i>
                                <p>No available booking numbers. Add some to continue.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Used Numbers Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3" style="background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%); color: white;">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-calendar-check mr-2"></i>Used Booking Numbers (<?php echo count($usedNumbers); ?>)
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Booking Number</th>
                        <th>Booking ID</th>
                        <th>Used Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usedNumbers)): ?>
                        <?php foreach ($usedNumbers as $number): ?>
                        <tr>
                            <td><span class="text-primary-admin font-weight-bold" style="font-size: 0.9rem;"><?php echo htmlspecialchars($number['booking_number']); ?></span></td>
                            <td>#<?php echo $number['booking_id']; ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($number['booking_created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <i class="fas fa-info-circle fa-3x mb-3" style="opacity: 0.3;"></i>
                                <p>No booking numbers have been used yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Numbers Modal -->
<div class="modal fade" id="addNumbersModal" tabindex="-1" role="dialog" aria-labelledby="addNumbersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--uphol-navy) 0%, var(--uphol-blue) 100%); color: white;">
                <h5 class="modal-title" id="addNumbersModalLabel">
                    <i class="fas fa-plus mr-2"></i>Add New Booking Numbers
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?php echo BASE_URL; ?>admin/addBookingNumbers">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="prefix">Prefix</label>
                        <input type="text" class="form-control" name="prefix" id="prefix" value="BKG-" required>
                        <small class="form-text text-muted">Prefix for booking numbers (e.g., BKG-, BOOK-, etc.)</small>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="text" class="form-control" name="date" id="date" value="<?php echo date('Ymd'); ?>" required>
                        <small class="form-text text-muted">Date format: YYYYMMDD (e.g., 20250125)</small>
                    </div>
                    <div class="form-group">
                        <label for="start_number">Start Number</label>
                        <input type="number" class="form-control" name="start_number" id="start_number" value="1" min="1" required>
                        <small class="form-text text-muted">Starting number for the sequence</small>
                    </div>
                    <div class="form-group">
                        <label for="count">Count</label>
                        <input type="number" class="form-control" name="count" id="count" value="10" min="1" max="100" required>
                        <small class="form-text text-muted">How many booking numbers to generate (max 100)</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Preview:</strong> <span id="preview">BKG-20250125-0001 to BKG-20250125-0010</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-admin">Generate Numbers</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Update preview when form fields change
function updatePreview() {
    const prefix = document.getElementById('prefix').value || 'BKG-';
    const date = document.getElementById('date').value || '<?php echo date('Ymd'); ?>';
    const startNumber = parseInt(document.getElementById('start_number').value) || 1;
    const count = parseInt(document.getElementById('count').value) || 10;
    
    const firstNumber = prefix + date + '-' + String(startNumber).padStart(4, '0');
    const lastNumber = prefix + date + '-' + String(startNumber + count - 1).padStart(4, '0');
    
    document.getElementById('preview').textContent = firstNumber + ' to ' + lastNumber;
}

// Add event listeners
document.getElementById('prefix').addEventListener('input', updatePreview);
document.getElementById('date').addEventListener('input', updatePreview);
document.getElementById('start_number').addEventListener('input', updatePreview);
document.getElementById('count').addEventListener('input', updatePreview);

// Initial preview
updatePreview();
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
