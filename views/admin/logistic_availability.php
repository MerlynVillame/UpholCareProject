<?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">📅 Logistic Availability Manager</h1>
                <a href="<?php echo BASE_URL; ?>admin/dailySchedule" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-calendar-check fa-sm text-white-50"></i> View Daily Schedule
                </a>
            </div>

            <p class="mb-4 text-muted">Set your daily service capacity to control how many jobs you can handle per day. 
                Setting a capacity to <strong>0</strong> will automatically disable that option for customers on that date.</p>

            <!-- Availability Table Card -->
            <div class="card module-card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-primary">30-Day Availability Settings</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-primary-soft text-primary font-weight-bold">
                                <tr>
                                    <th class="pl-4">Date</th>
                                    <th>Max Pickup</th>
                                    <th>Max Delivery</th>
                                    <th>Max Inspection</th>
                                    <th class="text-right pr-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($availabilityData as $date => $data): ?>
                                    <tr data-date="<?php echo $date; ?>">
                                        <td class="pl-4 font-weight-bold">
                                            <?php echo date('M d, Y', strtotime($date)); ?>
                                            <?php if ($date == date('Y-m-d')): ?>
                                                <span class="badge badge-primary ml-2">Today</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm max-pickup" value="<?php echo $data['max_pickup']; ?>" min="0" style="width: 80px;">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm max-delivery" value="<?php echo $data['max_delivery']; ?>" min="0" style="width: 80px;">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm max-inspection" value="<?php echo $data['max_inspection']; ?>" min="0" style="width: 80px;">
                                        </td>
                                        <td class="text-right pr-4">
                                            <button class="btn btn-sm btn-success save-capacity" data-date="<?php echo $date; ?>">
                                                <i class="fas fa-save mr-1"></i> Save
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <?php include ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveButtons = document.querySelectorAll('.save-capacity');
    
    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const date = this.getAttribute('data-date');
            const row = this.closest('tr');
            const maxPickup = row.querySelector('.max-pickup').value;
            const maxDelivery = row.querySelector('.max-delivery').value;
            const maxInspection = row.querySelector('.max-inspection').value;
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            const formData = new FormData();
            formData.append('date', date);
            formData.append('max_pickup', maxPickup);
            formData.append('max_delivery', maxDelivery);
            formData.append('max_inspection', maxInspection);
            
            fetch('<?php echo BASE_URL; ?>admin/updateLogisticCapacity', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Capacity updated for ' + date);
                    this.innerHTML = '<i class="fas fa-check mr-1"></i> Saved';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-save mr-1"></i> Save';
                        this.disabled = false;
                    }, 2000);
                } else {
                    alert(data.message);
                    this.innerHTML = '<i class="fas fa-save mr-1"></i> Save';
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
                this.innerHTML = '<i class="fas fa-save mr-1"></i> Save';
                this.disabled = false;
            });
        });
    });
});
</script>
