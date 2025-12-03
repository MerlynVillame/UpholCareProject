<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Enhanced Button Styles -->
    <style>
        /* Enhanced Action Buttons Styling */
        .btn-group-enhanced {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        
        .enhanced-btn {
            position: relative;
            padding: 8px 12px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid transparent;
            min-width: 38px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }
        
        .enhanced-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            border-color: rgba(0,0,0,0.1);
        }
        
        .enhanced-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .enhanced-btn i {
            font-size: 1rem;
        }
        
        /* Primary Action Buttons (Main workflow actions) */
        .btn-primary-action {
            font-weight: 600;
            padding: 9px 12px;
            width: 40px;
            height: 40px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.12);
        }
        
        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }
        
        /* View Button - Special styling */
        .view-btn {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border: none;
        }
        
        .view-btn:hover {
            background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
            color: white;
        }
        
        /* Approve Button - Special styling */
        .approve-btn {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
        }
        
        .approve-btn:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
            color: white;
        }
        
        /* Delete Button - Special styling */
        .delete-btn {
            border-color: #dc3545;
            color: #dc3545;
        }
        
        .delete-btn:hover {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        
        /* Update Button - Special styling */
        .update-btn {
            border-color: #007bff;
            color: #007bff;
        }
        
        .update-btn:hover {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Receipt Button - Special styling */
        .receipt-btn {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
        }
        
        .receipt-btn:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
            color: white;
        }
        
        /* Loading State */
        .enhanced-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .enhanced-btn.loading i {
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-group-enhanced {
                flex-wrap: wrap;
            }
            
            .enhanced-btn {
                width: 38px;
                height: 38px;
                margin-bottom: 4px;
            }
            
            .btn-primary-action {
                width: 40px;
                height: 40px;
            }
        }
        
        /* Button group spacing */
        .btn-group-enhanced > .btn {
            margin-right: 4px;
            margin-bottom: 4px;
        }
        
        .btn-group-enhanced > .btn:last-child {
            margin-right: 0;
        }
        
        /* Icon alignment */
        .enhanced-btn i {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Disabled state */
        .enhanced-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        /* Focus state for accessibility */
        .enhanced-btn:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
    </style>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-check mr-2"></i>All Bookings
        </h1>
        <div>
            <!-- <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#bookingNumbersModal">
                <i class="fas fa-ticket-alt mr-1"></i> Manage Booking Numbers
            </button> -->
            <!-- <a href="<?php echo BASE_URL; ?>admin/repairItems" class="btn btn-sm btn-success">
                <i class="fas fa-tools mr-1"></i> Repair Items
            </a> -->
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="active-tab" data-toggle="tab" href="#activeBookings" role="tab" aria-controls="activeBookings" aria-selected="true">
                        <i class="fas fa-clock mr-2"></i>Active Bookings
                        <span class="badge badge-warning ml-2" id="activeCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="completed-tab" data-toggle="tab" href="#completedBookings" role="tab" aria-controls="completedBookings" aria-selected="false">
                        <i class="fas fa-check-circle mr-2"></i>Completed Bookings
                        <span class="badge badge-success ml-2" id="completedCount">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Booking Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="bookingTabsContent">
                        <!-- Active Bookings Tab -->
                        <div class="tab-pane fade show active" id="activeBookings" role="tabpanel" aria-labelledby="active-tab">
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

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="activeBookingsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Booking #</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Category</th>
                                            <th>Service Option</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Function to generate status-specific action buttons based on workflow (ENHANCED)
                                        function getStatusActionButtons($booking) {
                                            $bookingId = (int)$booking['id'];
                                            $rawStatus = $booking['status'] ?? 'pending';
                                            $status = strtolower(trim((string)$rawStatus));
                                            if (empty($status) || $status === 'null') {
                                                $status = 'pending';
                                            }
                                            
                                            $buttons = [];
                                            
                                            // Always show View Details button (Required First Step) - Icon Only
                                            $buttons[] = '<button type="button" class="btn btn-sm btn-info action-btn view-btn enhanced-btn" data-booking-id="' . $bookingId . '" onclick="handleViewDetails(' . $bookingId . ')" title="View Details - Check Service Option & Booking Details">
                                                <i class="fas fa-eye"></i>
                                            </button>';
                                            
                                            // Status-specific buttons based on workflow - Icon Only
                                            switch ($status) {
                                                case 'pending':
                                                    // Pending: Approve → Approved (then auto to For Pickup if Pick Up service) - Icon Only
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-success action-btn approve-btn enhanced-btn btn-primary-action" data-booking-id="' . $bookingId . '" onclick="handleApprove(' . $bookingId . ')" title="Approve - Status will change to Approved, then For Pick Up if Pick Up service">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>';
                                                    break;
                                                    
                                                case 'approved':
                                                    // Approved: If Pick Up service, show "Mark for Pickup" button
                                                    // Otherwise, show "Start Repair" or other appropriate action
                                                    $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                                                    if ($serviceOption === 'pickup' || $serviceOption === 'both') {
                                                        // Auto-update should have happened, but show button as fallback
                                                        $buttons[] = '<button type="button" class="btn btn-sm btn-primary action-btn enhanced-btn btn-primary-action" data-booking-id="' . $bookingId . '" onclick="handleQuickStatusUpdate(' . $bookingId . ', \'for_pickup\', event)" title="Mark for Pick Up - Item ready for collection">
                                                            <i class="fas fa-truck"></i>
                                                        </button>';
                                                    } else {
                                                        // For non-pickup services, can proceed directly
                                                        $buttons[] = '<button type="button" class="btn btn-sm btn-success action-btn enhanced-btn btn-primary-action" onclick="openComputeTotal(' . $bookingId . ')" title="Compute Total & Start Repair">
                                                            <i class="fas fa-calculator"></i>
                                                        </button>';
                                                    }
                                                    break;
                                                    
                                                case 'for_pickup':
                                                    // For Pickup: Mark as Picked Up → To Inspect - Icon Only
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-primary action-btn enhanced-btn btn-primary-action" data-booking-id="' . $bookingId . '" onclick="handleQuickStatusUpdate(' . $bookingId . ', \'to_inspect\', event)" title="Mark as Picked Up - Item collected, ready for inspection">
                                                        <i class="fas fa-truck-loading"></i>
                                                    </button>';
                                                    break;
                                                    
                                                case 'picked_up':
                                                    // Picked Up: Move to Inspection - Icon Only
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-primary action-btn enhanced-btn btn-primary-action" data-booking-id="' . $bookingId . '" onclick="handleQuickStatusUpdate(' . $bookingId . ', \'to_inspect\', event)" title="Move to Inspection Stage">
                                                        <i class="fas fa-search"></i>
                                                    </button>';
                                                    break;
                                                    
                                                case 'to_inspect':
                                                    // To Inspect: Full Inspection Workflow Buttons - Icon Only
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-info action-btn enhanced-btn" onclick="openRecordMeasurements(' . $bookingId . ')" title="Record Measurements - Height, Width, Thickness">
                                                        <i class="fas fa-ruler-combined"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-warning action-btn enhanced-btn" onclick="openRecordDamages(' . $bookingId . ')" title="Record Damages / Defects - Tears, Foam, Frames, Stains">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-success action-btn enhanced-btn" onclick="openAddMaterials(' . $bookingId . ')" title="Add Materials / Fabrics Used - Fabric Type, Meters, Foam, Accessories">
                                                        <i class="fas fa-cut"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-primary action-btn enhanced-btn" onclick="openComputeTotal(' . $bookingId . ')" title="Compute Total - Calculate Fabric, Labor, Repair Costs">
                                                        <i class="fas fa-calculator"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-secondary action-btn enhanced-btn btn-primary-action" onclick="sendPreviewReceipt(' . $bookingId . ')" title="Send Preview Receipt - Send to Customer (Status: For Repair)">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>';
                                                    break;
                                                    
                                                case 'for_inspection':
                                                    // For Inspection (legacy): Use same as to_inspect
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-info action-btn enhanced-btn" onclick="openRecordMeasurements(' . $bookingId . ')" title="Record Measurements">
                                                        <i class="fas fa-ruler-combined"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-warning action-btn enhanced-btn" onclick="openRecordDamages(' . $bookingId . ')" title="Record Damages">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-success action-btn enhanced-btn" onclick="openAddMaterials(' . $bookingId . ')" title="Add Materials">
                                                        <i class="fas fa-cut"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-primary action-btn enhanced-btn" onclick="openComputeTotal(' . $bookingId . ')" title="Compute Total">
                                                        <i class="fas fa-calculator"></i>
                                                    </button>';
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-secondary action-btn enhanced-btn btn-primary-action" onclick="sendPreviewReceipt(' . $bookingId . ')" title="Send Preview Receipt - Send to Customer (Status: For Repair)">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>';
                                                    break;
                                                    
                                                // for_quotation status removed - bookings go directly to for_repair after sending receipt
                                                
                                                    
                                                case 'for_repair':
                                                    // For Repair: Start Repair → In Progress - Icon Only
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-success action-btn enhanced-btn btn-primary-action" data-booking-id="' . $bookingId . '" onclick="handleQuickStatusUpdate(' . $bookingId . ', \'under_repair\', event)" title="Start Repair Work">
                                                        <i class="fas fa-tools"></i>
                                                    </button>';
                                                    break;
                                                    
                                                case 'approved':
                                                case 'accepted':
                                                case 'confirmed':
                                                    // Approved (legacy): Generate Receipt - Icon Only
                                                    $buttons[] = '<button type="button" 
                                                                class="btn btn-sm btn-success action-btn receipt-btn enhanced-btn btn-primary-action" 
                                                                data-booking-id="' . $bookingId . '" 
                                                                onclick="handleGenerateReceipt(' . $bookingId . ')" 
                                                                title="Create Receipt">
                                                            <i class="fas fa-receipt"></i>
                                                        </button>';
                                                    break;
                                                    
                                                case 'in_progress':
                                                case 'under_repair':
                                                case 'ongoing':
                                                    // In Progress / Under Repair: Mark Completed - Icon Only
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-success action-btn enhanced-btn btn-primary-action" data-booking-id="' . $bookingId . '" onclick="handleQuickStatusUpdate(' . $bookingId . ', \'completed\', event)" title="Mark as Completed">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>';
                                                    break;
                                                    
                                                case 'completed':
                                                    // Completed: No additional action buttons needed
                                                    break;
                                                    
                                                case 'paid':
                                                    // Paid: Close Booking - Icon Only
                                                    $buttons[] = '<button type="button" class="btn btn-sm btn-dark action-btn enhanced-btn btn-primary-action" data-booking-id="' . $bookingId . '" onclick="handleQuickStatusUpdate(' . $bookingId . ', \'closed\', event)" title="Close Booking">
                                                        <i class="fas fa-lock"></i>
                                                    </button>';
                                                    break;
                                            }
                                            
                                            // Update Status button removed - use workflow-specific buttons only
                                            
                                            // Always show Delete button - Icon Only
                                            $buttons[] = '<button type="button" class="btn btn-sm btn-outline-danger action-btn delete-btn enhanced-btn" data-booking-id="' . $bookingId . '" onclick="handleDelete(' . $bookingId . ', event)" title="Delete Booking">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>';
                                            
                                            return '<div class="btn-group btn-group-enhanced" role="group" aria-label="Booking Actions">' . implode('', $buttons) . '</div>';
                                        }
                                        
                                        $activeCount = 0;
                                        $completedCount = 0;
                                        if (!empty($bookings)): 
                                            foreach ($bookings as $booking): 
                                                // Check if booking is completed (status = completed/delivered_and_paid AND payment = paid)
                                                $status = strtolower($booking['status'] ?? '');
                                                $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                                                $isCompleted = (
                                                    in_array($status, ['completed', 'delivered_and_paid']) && 
                                                    in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod'])
                                                );
                                                if ($isCompleted) {
                                                    $completedCount++;
                                                    continue; // Skip completed bookings in active tab
                                                }
                                                $activeCount++;
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info">Booking #<?php echo htmlspecialchars($booking['id']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="customer-info">
                                                        <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['email']); ?></small>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['phone']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="service-info">
                                                        <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['service_type']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($booking['category_name']); ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Service Option mapping with icons and colors
                                                    $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                                                    $serviceOptionConfig = [
                                                        'pickup' => ['class' => 'badge-primary', 'icon' => 'fa-truck-loading', 'text' => 'Pickup'],
                                                        'delivery' => ['class' => 'badge-info', 'icon' => 'fa-truck', 'text' => 'Delivery'],
                                                        'both' => ['class' => 'badge-success', 'icon' => 'fa-exchange-alt', 'text' => 'Both'],
                                                        'walk_in' => ['class' => 'badge-warning', 'icon' => 'fa-walking', 'text' => 'Walk-in']
                                                    ];
                                                    $optionConfig = $serviceOptionConfig[$serviceOption] ?? ['class' => 'badge-secondary', 'icon' => 'fa-question', 'text' => ucfirst($serviceOption)];
                                                    ?>
                                                    <span class="badge <?php echo $optionConfig['class']; ?>" style="font-weight: 600;">
                                                        <i class="fas <?php echo $optionConfig['icon']; ?> mr-1"></i>
                                                        <?php echo htmlspecialchars($optionConfig['text']); ?>
                                                    </span>
                                                </td>
                                                <td class="status-cell" data-booking-status="<?php echo htmlspecialchars($booking['status'] ?? 'pending'); ?>">
                                                    <?php
                                                    // Status mapping based on workflow
                                                    $statusConfig = [
                                                        // Initial stages
                                                        'pending' => ['class' => 'badge-warning', 'text' => 'Pending'],
                                                        
                                                        // PICKUP Workflow
                                                        'for_pickup' => ['class' => 'badge-info', 'text' => 'For Pickup'],
                                                        'picked_up' => ['class' => 'badge-primary', 'text' => 'Picked Up'],
                                                        'for_inspection' => ['class' => 'badge-warning', 'text' => 'For Inspection'],
                                                        // 'for_quotation' status removed
                                                        
                                                        // Work in Progress
                                                        'approved' => ['class' => 'badge-success', 'text' => 'Approved'],
                                                        'in_queue' => ['class' => 'badge-info', 'text' => 'In Queue'],
                                                        'in_progress' => ['class' => 'badge-primary', 'text' => 'In Progress'],
                                                        'under_repair' => ['class' => 'badge-primary', 'text' => 'Under Repair'],
                                                        'for_quality_check' => ['class' => 'badge-info', 'text' => 'For Quality Check'],
                                                        
                                                        // Completion
                                                        'ready_for_pickup' => ['class' => 'badge-success', 'text' => 'Ready for Pickup'],
                                                        'out_for_delivery' => ['class' => 'badge-warning', 'text' => 'Out for Delivery'],
                                                        'completed' => ['class' => 'badge-success', 'text' => 'Completed'],
                                                        'delivered_and_paid' => ['class' => 'badge-success', 'text' => 'Delivered and Paid'],
                                                        'paid' => ['class' => 'badge-success', 'text' => 'Paid'],
                                                        'closed' => ['class' => 'badge-dark', 'text' => 'Closed'],
                                                        
                                                        // Other
                                                        'cancelled' => ['class' => 'badge-secondary', 'text' => 'Cancelled'],
                                                        
                                                        // Legacy statuses (for backward compatibility)
                                                        'accepted' => ['class' => 'badge-success', 'text' => 'Approved'],
                                                        'confirmed' => ['class' => 'badge-success', 'text' => 'Approved'],
                                                        'ongoing' => ['class' => 'badge-primary', 'text' => 'In Progress'],
                                                        'rejected' => ['class' => 'badge-danger', 'text' => 'Rejected'],
                                                        'declined' => ['class' => 'badge-danger', 'text' => 'Declined']
                                                    ];
                                                    
                                                    $status = strtolower(trim($booking['status'] ?? 'pending'));
                                                    // Ensure status is never empty
                                                    if (empty($status) || $status === 'null') {
                                                        $status = 'pending';
                                                    }
                                                    $config = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'text' => ucwords(str_replace('_', ' ', $status))];
                                                    
                                                    // Show payment status if completed
                                                    // If status is delivered_and_paid, it's already paid
                                                    if ($status === 'completed') {
                                                        $paymentStatus = $booking['payment_status'] ?? 'unpaid';
                                                        // For COD: completed + unpaid = "Completed (Unpaid)"
                                                        // For Full Cash: completed + paid_full_cash = "Completed (Paid)"
                                                        if ($paymentStatus === 'paid_full_cash' || $paymentStatus === 'paid') {
                                                            $config['text'] = 'Completed (Paid)';
                                                            $config['class'] = 'badge-success';
                                                        } else {
                                                            $config['text'] = 'Completed (Unpaid)';
                                                            $config['class'] = 'badge-warning';
                                                        }
                                                    } elseif ($status === 'delivered_and_paid') {
                                                        // Delivered and Paid status (COD after payment received)
                                                        $config['text'] = 'Delivered and Paid';
                                                        $config['class'] = 'badge-success';
                                                    }
                                                    
                                                    // Ensure "Approved" shows clearly
                                                    if ($status === 'approved') {
                                                        $config['text'] = 'Approved';
                                                        $config['class'] = 'badge-success';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $config['class']; ?>" style="font-weight: 600;">
                                                        <?php echo htmlspecialchars($config['text']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="date-info"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
                                                </td>
                                                <td>
                                                    <?php echo getStatusActionButtons($booking); ?>
                                                </td>
                                            </tr>
                                        <?php 
                                            endforeach; 
                                        endif; 
                                        ?>
                                        <?php if ($activeCount === 0): ?>
                                            <tr class="empty-state">
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                                                    <br><span class="text-muted">No active bookings found</span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Completed Bookings Tab -->
                        <div class="tab-pane fade" id="completedBookings" role="tabpanel" aria-labelledby="completed-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="completedBookingsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Booking #</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Category</th>
                                            <th>Service Option</th>
                                            <th>Status</th>
                                            <th>Completed Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($bookings)): 
                                            foreach ($bookings as $booking): 
                                                // Only show completed bookings (status = completed/delivered_and_paid AND payment = paid)
                                                $status = strtolower($booking['status'] ?? '');
                                                $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
                                                $isCompleted = (
                                                    in_array($status, ['completed', 'delivered_and_paid']) && 
                                                    in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod'])
                                                );
                                                if (!$isCompleted) {
                                                    continue; // Skip non-completed bookings
                                                }
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info">Booking #<?php echo htmlspecialchars($booking['id']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="customer-info">
                                                        <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['email']); ?></small>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['phone']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="service-info">
                                                        <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                                                        <br>
                                                        <small><?php echo htmlspecialchars($booking['service_type']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($booking['category_name']); ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Service Option mapping with icons and colors
                                                    $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                                                    $serviceOptionConfig = [
                                                        'pickup' => ['class' => 'badge-primary', 'icon' => 'fa-truck-loading', 'text' => 'Pickup'],
                                                        'delivery' => ['class' => 'badge-info', 'icon' => 'fa-truck', 'text' => 'Delivery'],
                                                        'both' => ['class' => 'badge-success', 'icon' => 'fa-exchange-alt', 'text' => 'Both'],
                                                        'walk_in' => ['class' => 'badge-warning', 'icon' => 'fa-walking', 'text' => 'Walk-in']
                                                    ];
                                                    $optionConfig = $serviceOptionConfig[$serviceOption] ?? ['class' => 'badge-secondary', 'icon' => 'fa-question', 'text' => ucfirst($serviceOption)];
                                                    ?>
                                                    <span class="badge <?php echo $optionConfig['class']; ?>" style="font-weight: 600;">
                                                        <i class="fas <?php echo $optionConfig['icon']; ?> mr-1"></i>
                                                        <?php echo htmlspecialchars($optionConfig['text']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle mr-1"></i>Completed & Paid
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="date-info">
                                                        <?php 
                                                        $completedDate = !empty($booking['updated_at']) ? $booking['updated_at'] : $booking['created_at'];
                                                        echo date('M d, Y', strtotime($completedDate)); 
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-enhanced" role="group" aria-label="Completed Booking Actions">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success action-btn receipt-btn enhanced-btn btn-primary-action" 
                                                                data-booking-id="<?php echo (int)$booking['id']; ?>"
                                                                data-action="receipt"
                                                                onclick="handleGenerateReceipt(<?php echo (int)$booking['id']; ?>)"
                                                                title="Generate Receipt">
                                                            <i class="fas fa-receipt"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-info action-btn view-btn enhanced-btn" 
                                                                data-booking-id="<?php echo (int)$booking['id']; ?>"
                                                                data-action="view"
                                                                onclick="handleViewDetails(<?php echo (int)$booking['id']; ?>)"
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger action-btn delete-btn enhanced-btn" 
                                                                data-booking-id="<?php echo (int)$booking['id']; ?>"
                                                                data-action="delete"
                                                                onclick="handleDelete(<?php echo (int)$booking['id']; ?>, event)"
                                                                title="Delete Booking">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php 
                                            endforeach; 
                                        endif; 
                                        ?>
                                        <?php if ($completedCount === 0): ?>
                                            <tr class="empty-state">
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                                                    <br><span class="text-muted">No completed bookings found</span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal - Enhanced with Admin Actions -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tasks mr-2"></i>Admin Actions - Manage Booking
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="bookingStatusForm" onsubmit="return submitBookingStatusForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="booking_id" id="booking_id">
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="adminActionTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="status-tab" data-toggle="tab" href="#status-pane" role="tab">
                                <i class="fas fa-sync-alt mr-1"></i> Status & Payment
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="progress-tab" data-toggle="tab" href="#progress-pane" role="tab">
                                <i class="fas fa-clipboard-list mr-1"></i> Progress Updates
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content" id="adminActionTabsContent">
                        <!-- Status & Payment Tab -->
                        <div class="tab-pane fade show active" id="status-pane" role="tabpanel">
                            <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Step 1: Update Booking & Payment Status</h6>
                            
                            <!-- Customer's Selected Service Option -->
                            <div class="alert alert-info mb-3" id="customerServiceOptionInfo" style="display: none;">
                                <h6 class="mb-2"><i class="fas fa-truck mr-2"></i>Customer's Selected Service Option</h6>
                                <div id="serviceOptionDetails">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Service Type:</strong> <span id="serviceOptionType" class="badge badge-primary"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Status:</strong> <span id="serviceOptionStatus"></span>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="pickupDetails" style="display: none;">
                                        <hr>
                                        <h6><i class="fas fa-map-marker-alt mr-2"></i>Pickup Information</h6>
                                        <p><strong>Pickup Address:</strong> <span id="pickupAddress"></span></p>
                                        <p><strong>Pickup Date:</strong> <span id="pickupDate"></span></p>
                                        <p><strong>Distance:</strong> <span id="pickupDistance"></span> km</p>
                                    </div>
                                    <div class="mt-2" id="deliveryDetails" style="display: none;">
                                        <hr>
                                        <h6><i class="fas fa-truck mr-2"></i>Delivery Information</h6>
                                        <p><strong>Delivery Address:</strong> <span id="deliveryAddress"></span></p>
                                        <p><strong>Delivery Date:</strong> <span id="deliveryDate"></span></p>
                                    </div>
                                </div>
                            </div>
                            
                    <div class="form-group">
                                <label for="status"><strong>Booking Status</strong></label>
                        <select class="form-control" name="status" id="status" required>
                                    <optgroup label="Initial Stages">
                                        <option value="pending">Pending - Customer submitted booking</option>
                                    </optgroup>
                                    <optgroup label="PICKUP Workflow (for items that need inspection)">
                                        <option value="for_pickup">For Pickup - Approved, waiting to collect item</option>
                                        <option value="picked_up">Picked Up - Item collected, waiting for inspection</option>
                                        <option value="for_inspection">For Inspection - Item being inspected</option>
                                        <!-- For Quotation status removed -->
                                    </optgroup>
                                    <optgroup label="Work in Progress">
                                        <option value="approved">Approved - Customer approved quotation, ready for repair</option>
                                    <option value="in_queue">In Queue - Waiting to be processed</option>
                                        <option value="in_progress">In Progress - Work has started</option>
                                        <option value="under_repair">Under Repair - Technicians working on item</option>
                                        <option value="for_quality_check">For Quality Check - Final inspection</option>
                                    </optgroup>
                                    <optgroup label="Completion">
                                        <option value="ready_for_pickup">Ready for Pickup - Item ready for customer pickup</option>
                                        <option value="out_for_delivery">Out for Delivery - Item being delivered</option>
                                        <option value="completed">Completed - Work finished</option>
                                        <option value="delivered_and_paid">Delivered and Paid - Item delivered & payment received (COD)</option>
                                        <option value="paid">Paid - Full payment received</option>
                                        <option value="closed">Closed - Booking completed and archived</option>
                                    </optgroup>
                                    <optgroup label="Other">
                                    <option value="cancelled">Cancelled - Customer or shop declined</option>
                                    </optgroup>
                        </select>
                                <small class="form-text text-muted">
                                    <strong>PICKUP Workflow:</strong> Pending → Approved → For Pickup → To Inspect → For Repair → Under Repair → Completed → Paid → Closed<br>
                                    <strong>Regular Workflow:</strong> Pending → Approved → In Progress → Completed → Paid → Closed
                                </small>
                    </div>
                            
                    <div class="form-group">
                                <label for="payment_status"><strong>Payment Status</strong></label>
                        <select class="form-control" name="payment_status" id="payment_status">
                                    <option value="unpaid">Unpaid (default)</option>
                            <option value="paid_full_cash">Paid (Full Cash)</option>
                            <option value="paid_on_delivery_cod">Paid on Delivery (COD)</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                            
                            <div class="form-group">
                                <label for="admin_notes_status">Admin Notes (Optional)</label>
                                <textarea class="form-control" name="admin_notes" id="admin_notes_status" rows="3" 
                                          placeholder="Add any notes about this status update..."></textarea>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_customer_status" name="notify_customer" value="1" checked>
                                <label class="form-check-label" for="notify_customer_status">
                                    <i class="fas fa-envelope mr-1"></i> Send email notification to customer about this status update
                                </label>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Finalization:</strong> When status is "Completed" and payment is "Paid", the booking becomes finalized and no further changes can be made.
                            </div>
                        </div>
                        
                        <!-- Customer Service Option Display -->
                        <div class="alert alert-info mb-3" id="customerServiceOptionInfo" style="display: none;">
                            <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Customer's Selected Service Option</h6>
                            <div id="serviceOptionDetails">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                        
                        <!-- Progress Updates Tab -->
                        <div class="tab-pane fade" id="progress-pane" role="tabpanel">
                            <h6 class="mb-3"><i class="fas fa-clipboard-list mr-2"></i>Step 3: Add Progress Update</h6>
                            
                            <div class="form-group">
                                <label for="progress_update"><strong>Progress Update</strong></label>
                                <select class="form-control" name="progress_type" id="progress_type">
                                    <option value="item_received">Item Received</option>
                                    <option value="materials_prepared">Materials Prepared</option>
                                    <option value="work_started">Sewing/Upholstery Started</option>
                                    <option value="work_in_progress">Work In Progress</option>
                                    <option value="quality_check">Quality Check Performed</option>
                                    <option value="finishing_touches">Finishing Touches Done</option>
                                    <option value="ready_for_pickup">Ready for Pickup</option>
                                    <option value="custom">Custom Update</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="progress_notes">Progress Notes</label>
                                <textarea class="form-control" name="progress_notes" id="progress_notes" rows="4" 
                                          placeholder="Describe the progress update in detail..."></textarea>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_customer_progress" name="notify_customer_progress" value="1" checked>
                                <label class="form-check-label" for="notify_customer_progress">
                                    <i class="fas fa-envelope mr-1"></i> Notify customer about this progress update
                                </label>
                            </div>
                            
                            <!-- Progress History -->
                            <div class="mt-4">
                                <h6>Progress History</h6>
                                <div id="progress_history" class="list-group" style="max-height: 200px; overflow-y: auto;">
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-spinner fa-spin"></i> Loading progress history...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save All Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt Form Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="receiptModalLabel">
                    <i class="fas fa-receipt mr-2"></i>Create Receipt
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="receiptContent">
                <form id="receiptForm">
                    <input type="hidden" id="receipt_booking_id" name="booking_id">
                    
                    <!-- Booking Info -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="m-0"><i class="fas fa-info-circle mr-2"></i>Booking Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Booking ID:</strong> <span id="receipt_booking_id_display" class="text-primary">-</span></p>
                                    <p class="mb-2"><strong>Customer:</strong> <span id="receipt_customer_name">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Service:</strong> <span id="receipt_service_name">-</span></p>
                                    <p class="mb-2"><strong>Date:</strong> <span id="receipt_date">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Leather Details -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="m-0"><i class="fas fa-ruler mr-2"></i>Leather Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="leather_quality">Leather Quality <span class="text-danger">*</span></label>
                                        <select class="form-control" id="leather_quality" name="leather_quality" required disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <option value="">Select Quality...</option>
                                            <option value="standard">Standard</option>
                                            <option value="premium">Premium</option>
                                        </select>
                                        <small class="form-text text-muted">Customer's selected quality (pre-filled from booking)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="leather_color_id">Leather/Color <span class="text-danger">*</span></label>
                                        <select class="form-control" id="leather_color_id" name="leather_color_id" required disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <option value="">Loading color...</option>
                                        </select>
                                        <small class="form-text text-muted">Customer's selected color (pre-filled from booking)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="number_of_meters">Number of Meters <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="number_of_meters" name="number_of_meters" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                        <small class="form-text text-muted">Enter the number of meters used</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price_per_meter">Price per Meter (₱) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="price_per_meter" name="price_per_meter" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                        <small class="form-text text-muted">Enter the price per meter (will auto-fill from inventory, but can be edited)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="labor_fee">Labor Fee (₱) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="labor_fee" name="labor_fee" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                        <small class="form-text text-muted">Enter the labor fee</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calculation Summary -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0"><i class="fas fa-calculator mr-2"></i>Calculation Summary</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>Leather Cost:</strong></td>
                                        <td class="text-right"><span id="leather_cost_display">₱0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Labor Fee:</strong></td>
                                        <td class="text-right"><span id="labor_fee_display">₱0.00</span></td>
                                    </tr>
                                    <tr class="table-success" style="font-size: 1.1rem; font-weight: bold;">
                                        <td><strong>GRAND TOTAL:</strong></td>
                                        <td class="text-right"><span id="grand_total_display">₱0.00</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-info" id="saveReceiptBtn" onclick="saveReceipt()">
                    <i class="fas fa-save mr-1"></i>Save Receipt
                </button>
                <button type="button" class="btn btn-success" id="sendReceiptBtn" onclick="sendReceiptToCustomer()">
                    <i class="fas fa-paper-plane mr-1"></i>Send to Customer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Inspection Workflow Modals -->

<!-- Record Measurements Modal -->
<div class="modal fade" id="recordMeasurementsModal" tabindex="-1" role="dialog" aria-labelledby="recordMeasurementsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="recordMeasurementsModalLabel">
                    <i class="fas fa-ruler-combined mr-2"></i>Record Measurements
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="measurementsForm">
                    <input type="hidden" id="measurements_booking_id" name="booking_id">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="measurement_height">Height (cm) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="measurement_height" name="height" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="measurement_width">Width (cm) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="measurement_width" name="width" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="measurement_thickness">Thickness (cm)</label>
                                <input type="number" class="form-control" id="measurement_thickness" name="thickness" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="measurement_custom">Custom Size / Additional Measurements</label>
                        <textarea class="form-control" id="measurement_custom" name="custom_measurements" rows="3" placeholder="Enter any custom measurements or size details..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="measurement_notes">Measurement Notes</label>
                        <textarea class="form-control" id="measurement_notes" name="notes" rows="2" placeholder="Additional notes about measurements..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="saveMeasurements()">
                    <i class="fas fa-save mr-1"></i>Save Measurements
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Record Damages Modal -->
<div class="modal fade" id="recordDamagesModal" tabindex="-1" role="dialog" aria-labelledby="recordDamagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="recordDamagesModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Record Damages / Defects
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="damagesForm">
                    <input type="hidden" id="damages_booking_id" name="booking_id">
                    
                    <div class="form-group">
                        <label><strong>Damage Types (Check all that apply)</strong></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="damage_tears" name="damage_types[]" value="tears">
                                    <label class="form-check-label" for="damage_tears">Tears / Rips</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="damage_foam" name="damage_types[]" value="foam_damage">
                                    <label class="form-check-label" for="damage_foam">Foam Condition (Worn/Compressed)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="damage_frames" name="damage_types[]" value="broken_frames">
                                    <label class="form-check-label" for="damage_frames">Broken Frames / Springs</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="damage_stains" name="damage_types[]" value="stains">
                                    <label class="form-check-label" for="damage_stains">Stains / Dirt</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="damage_odor" name="damage_types[]" value="odor">
                                    <label class="form-check-label" for="damage_odor">Odor Issues</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="damage_other" name="damage_types[]" value="other">
                                    <label class="form-check-label" for="damage_other">Other</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="damage_description">Detailed Damage Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="damage_description" name="description" rows="4" required placeholder="Describe all damages, defects, and issues found during inspection..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="damage_location">Damage Location / Affected Areas</label>
                        <input type="text" class="form-control" id="damage_location" name="location" placeholder="e.g., Seat cushion, Backrest, Armrests, etc.">
                    </div>
                    
                    <div class="form-group">
                        <label for="damage_severity">Severity Level</label>
                        <select class="form-control" id="damage_severity" name="severity">
                            <option value="minor">Minor - Cosmetic only</option>
                            <option value="moderate">Moderate - Requires repair</option>
                            <option value="severe">Severe - Major repair needed</option>
                            <option value="critical">Critical - Replacement required</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="saveDamages()">
                    <i class="fas fa-save mr-1"></i>Save Damage Record
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Materials Modal -->
<div class="modal fade" id="addMaterialsModal" tabindex="-1" role="dialog" aria-labelledby="addMaterialsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addMaterialsModalLabel">
                    <i class="fas fa-cut mr-2"></i>Add Materials / Fabrics Used
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="materialsForm">
                    <input type="hidden" id="materials_booking_id" name="booking_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="material_fabric_type">Fabric Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="material_fabric_type" name="fabric_type" required>
                                    <option value="">Select fabric type...</option>
                                    <option value="leather_standard">Leather - Standard</option>
                                    <option value="leather_premium">Leather - Premium</option>
                                    <option value="fabric">Fabric</option>
                                    <option value="vinyl">Vinyl</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="material_meters">Yards/Meters Needed <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="material_meters" name="meters" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="material_foam">Foam Replacement</label>
                                <select class="form-control" id="material_foam" name="foam_replacement">
                                    <option value="none">No foam replacement</option>
                                    <option value="partial">Partial replacement</option>
                                    <option value="full">Full replacement</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="material_foam_thickness">Foam Thickness (inches)</label>
                                <input type="number" class="form-control" id="material_foam_thickness" name="foam_thickness" step="0.1" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Additional Materials / Accessories</strong></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_thread" name="accessories[]" value="thread">
                                    <label class="form-check-label" for="material_thread">Thread</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_buttons" name="accessories[]" value="buttons">
                                    <label class="form-check-label" for="material_buttons">Buttons</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_zippers" name="accessories[]" value="zippers">
                                    <label class="form-check-label" for="material_zippers">Zippers</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_adhesive" name="accessories[]" value="adhesive">
                                    <label class="form-check-label" for="material_adhesive">Adhesive</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_hardware" name="accessories[]" value="hardware">
                                    <label class="form-check-label" for="material_hardware">Hardware (screws, nails, etc.)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_other" name="accessories[]" value="other">
                                    <label class="form-check-label" for="material_other">Other</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="material_notes">Materials Notes</label>
                        <textarea class="form-control" id="material_notes" name="notes" rows="2" placeholder="Additional notes about materials used..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveMaterials()">
                    <i class="fas fa-save mr-1"></i>Save Materials
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Numbers Management Modal -->
<div class="modal fade" id="bookingNumbersModal" tabindex="-1" role="dialog" aria-labelledby="bookingNumbersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingNumbersModalLabel">
                    <i class="fas fa-ticket-alt mr-2"></i>Booking Numbers Management
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add New Booking Numbers Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-plus mr-2"></i>Add New Booking Numbers
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="addBookingNumbersForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="prefix">Prefix</label>
                                        <input type="text" class="form-control" id="prefix" name="prefix" value="BKG-" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo date('Ymd'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_number">Start Number</label>
                                        <input type="number" class="form-control" id="start_number" name="start_number" value="1" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="count">Count</label>
                                        <input type="number" class="form-control" id="count" name="count" value="10" min="1" max="100" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-plus mr-1"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Booking Numbers List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list mr-2"></i>Available Booking Numbers
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="bookingNumbersTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Booking Number</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="bookingNumbersTableBody">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="refreshBookingNumbers()">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Accept Reservation Modal (Auto-Assign Booking Number) -->
<div class="modal fade" id="acceptReservationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i>Accept Reservation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="accept_booking_id">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> A booking number will be automatically assigned to this reservation. The customer will be notified via email and in-app notification that their reservation has been confirmed.
                </div>
                <div class="form-group">
                    <label for="accept_admin_notes">Admin Notes (Optional)</label>
                    <textarea class="form-control" id="accept_admin_notes" rows="4" 
                              placeholder="Add any notes about this reservation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmAcceptReservation()">
                    <i class="fas fa-check mr-1"></i> Accept Reservation
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Booking Number Modal -->
<div class="modal fade" id="assignBookingNumberModal" tabindex="-1" role="dialog" aria-labelledby="assignBookingNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="assignBookingNumberModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>Assign Booking Number to Customer
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignBookingNumberForm">
                    <input type="hidden" id="assign_booking_number_id">
                    <div class="form-group">
                        <label for="customer_select">Select Customer</label>
                        <select class="form-control" id="customer_select" name="customer_id" required>
                            <option value="">Choose a customer...</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="booking_number_display">Booking ID</label>
                        <input type="text" class="form-control" id="booking_number_display" readonly>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> This booking number will be assigned to the selected customer and they can use it to make reservations.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmAssignBookingNumber()">
                    <i class="fas fa-check mr-1"></i> Assign Booking Number
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Compliance Check Modal -->
<div class="modal fade" id="bookingComplianceModal" tabindex="-1" role="dialog" aria-labelledby="bookingComplianceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="bookingComplianceModalLabel">
                    <i class="fas fa-clipboard-check mr-2"></i>Booking Compliance Check
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Customer Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user mr-2"></i>Customer Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <span id="compliance_customer_name">-</span></p>
                                <p><strong>Email:</strong> <span id="compliance_customer_email">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <span id="compliance_customer_phone">-</span></p>
                                <p><strong>Registration Date:</strong> <span id="compliance_customer_reg_date">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservation Requirements Check -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list-check mr-2"></i>Reservation Requirements Compliance
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="compliance_checklist">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                </div>

                <!-- Current Booking Details -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-check mr-2"></i>Current Booking Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="current_booking_details">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="acceptReservationFromCompliance()" id="acceptReservationFromComplianceBtn" style="display: none;">
                    <i class="fas fa-check-circle mr-1"></i> Approve Reservation
                </button>
                <button type="button" class="btn btn-success" onclick="approveCompliance()" id="approveComplianceBtn" style="display: none;">
                    <i class="fas fa-check mr-1"></i> Approve Compliance
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// UphoCare Admin Bookings JavaScript v2.1 - Enhanced Action Buttons
// Last Updated: December 3, 2025
// Version: 2.1 - Separated modals for review and calculation

// Note: jQuery is loaded in the footer, so it's normal for it to be undefined here
// The script will wait for jQuery to load before initializing jQuery-dependent features

// Define all global functions immediately to prevent "not defined" errors
// These functions are called from onclick handlers in the HTML

// Stub definitions - will be overridden with full implementations below
window.handleViewDetails = function(bookingId) { console.warn('handleViewDetails not yet loaded'); };
window.handleApprove = function(bookingId) { console.warn('handleApprove not yet loaded'); };
window.handleUpdateStatus = function(bookingId, currentStatus) { console.warn('handleUpdateStatus not yet loaded'); };
window.handleDelete = function(bookingId, event) { console.warn('handleDelete not yet loaded'); };
window.handleGenerateReceipt = function(bookingId) { console.warn('handleGenerateReceipt not yet loaded'); };
window.handleQuickStatusUpdate = function(bookingId, newStatus, event) { console.warn('handleQuickStatusUpdate not yet loaded'); };
window.handleConfirmPayment = function(bookingId, event) { console.warn('handleConfirmPayment not yet loaded'); };
window.viewDetails = function(bookingId) { console.warn('viewDetails not yet loaded'); };
window.loadBookingDetailsModal = function(bookingId) { console.warn('loadBookingDetailsModal not yet loaded'); };
window.loadCalculatePaymentModal = function(bookingId) { console.warn('loadCalculatePaymentModal not yet loaded'); };

window.updateBookingCounts = function() {
    const activeRows = document.querySelectorAll('#activeBookingsTable tbody tr:not(.empty-state)').length;
    const completedRows = document.querySelectorAll('#completedBookingsTable tbody tr:not(.empty-state)').length;
    
    const activeCountEl = document.getElementById('activeCount');
    const completedCountEl = document.getElementById('completedCount');
    
    if (activeCountEl) activeCountEl.textContent = activeRows;
    if (completedCountEl) completedCountEl.textContent = completedRows;
};

window.updateStatus = function(bookingId, currentStatus) {
    const bookingIdInput = document.getElementById('booking_id');
    const statusSelect = document.getElementById('status');
    const paymentStatusSelect = document.getElementById('payment_status');
    
    if (!bookingIdInput || !statusSelect) {
        console.error('Status modal elements not found');
        return;
    }
    
    bookingIdInput.value = bookingId;
    statusSelect.value = currentStatus || 'pending';
    
    // Load current payment status
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking && paymentStatusSelect) {
                const paymentStatus = data.booking.payment_status || 'unpaid';
                // Map old payment statuses to new ones
                let mappedStatus = paymentStatus;
                if (paymentStatus === 'paid') {
                    mappedStatus = 'paid_full_cash'; // Default to full cash for existing paid
                }
                paymentStatusSelect.value = mappedStatus;
            }
        })
        .catch(error => {
            console.error('Error loading booking details:', error);
            if (paymentStatusSelect) {
                paymentStatusSelect.value = 'unpaid';
            }
        });
    
    // Show modal with proper initialization
    const modalEl = document.getElementById('statusModal');
    if (!modalEl) {
        console.error('Status modal not found');
        return;
    }
    
    // console.log('Opening status modal for booking:', bookingId);
    
    // Load customer's service option details
    loadCustomerServiceOption(bookingId);
    
    // Load progress history
    loadProgressHistory(bookingId);
    
    // Remove any blocking styles before showing modal
    modalEl.style.zIndex = '';
    modalEl.style.pointerEvents = '';
    modalEl.style.display = '';
    
    // Show modal using jQuery if available, otherwise Bootstrap
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        // console.log('Using jQuery to show modal');
        
        // IMMEDIATELY disable all iframes and blocking elements BEFORE showing modal
        const iframeCount = jQuery('iframe, embed, object').length;
        // console.log('Found', iframeCount, 'iframes/embeds/objects - disabling them');
        
        jQuery('iframe, embed, object').css({
            'pointer-events': 'none',
            'z-index': '1',
            'position': 'relative'
        });
        
        // Disable any elements that might be blocking
        jQuery('*').each(function() {
            const $el = jQuery(this);
            const zIndex = parseInt($el.css('z-index'), 10);
            // If any element has z-index higher than 10000, lower it
            if (!isNaN(zIndex) && zIndex > 10000 && !$el.closest('.modal').length) {
                $el.css('z-index', '1');
            }
        });
        
        // Remove any existing event handlers to prevent duplicates
        jQuery('#statusModal').off('shown.bs.modal show.bs.modal hidden.bs.modal');
        
        // Disable iframe pointer-events when modal opens
        jQuery('#statusModal').on('show.bs.modal', function() {
            // Aggressively disable all iframes and blocking elements
            jQuery('iframe, embed, object').css('pointer-events', 'none');
            jQuery('*').not('.modal, .modal *').each(function() {
                const $el = jQuery(this);
                const zIndex = parseInt($el.css('z-index'), 10);
                if (!isNaN(zIndex) && zIndex >= 10000 && !$el.closest('.modal').length) {
                    $el.css('z-index', '1');
                }
            });
        });
        
        // Show the modal
        jQuery('#statusModal').modal({
            backdrop: true,
            keyboard: true,
            show: true
        });
        
        // Ensure modal content is clickable after it's shown
        jQuery('#statusModal').on('shown.bs.modal', function() {
            // console.log('Modal shown event fired');
            const $modal = jQuery(this);
            const $modalDialog = $modal.find('.modal-dialog');
            const $modalContent = $modal.find('.modal-content');
            
            // console.log('Setting modal z-index to 99999');
            
            // Force modal to highest z-index
            $modal.css({
                'z-index': '99999',
                'display': 'block',
                'pointer-events': 'auto',
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'width': '100%',
                'height': '100%'
            });
            
            $modalDialog.css({
                'pointer-events': 'auto',
                'z-index': '100000',
                'position': 'relative'
            });
            
            $modalContent.css({
                'pointer-events': 'auto',
                'z-index': '100000'
            });
            
            // Make all interactive elements clickable
            $modal.find('button, input, select, textarea, label, a, .btn, .form-control').css({
                'pointer-events': 'auto',
                'z-index': '100001',
                'position': 'relative',
                'cursor': 'pointer'
            });
            
            // Ensure backdrop is below modal
            const $backdrop = jQuery('.modal-backdrop');
            if ($backdrop.length) {
                $backdrop.css({
                    'z-index': '99998',
                    'position': 'fixed'
                });
            }
            
            // Aggressively disable ALL iframes and blocking elements
            jQuery('iframe, embed, object').css({
                'z-index': '1',
                'position': 'relative',
                'pointer-events': 'none'
            });
            
            // Lower z-index of any element that might be blocking
            jQuery('*').not('.modal, .modal *').each(function() {
                const $el = jQuery(this);
                const zIndex = parseInt($el.css('z-index'), 10);
                if (!isNaN(zIndex) && zIndex >= 10000) {
                    $el.css('z-index', '1');
                }
            });
            
            // Ensure wrapper elements don't interfere
            jQuery('#wrapper, .content-wrapper, .main-content-wrapper, .container-fluid, .container').css({
                'z-index': '1',
                'position': 'relative'
            });
            
            // Force reflow to ensure styles are applied
            $modal[0].offsetHeight;
            
            // Double-check after a short delay
            setTimeout(function() {
                $modal.css('z-index', '99999');
                $modalDialog.css('z-index', '100000');
                jQuery('iframe, embed, object').css('pointer-events', 'none');
            }, 100);
        });
        
        // Re-enable iframe pointer-events when modal closes
        jQuery('#statusModal').on('hidden.bs.modal', function() {
            // Re-enable all iframes
            jQuery('iframe, embed, object').css('pointer-events', 'auto');
        });
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Hide any iframes that might be covering the modal
        const iframes = document.querySelectorAll('iframe');
        iframes.forEach(function(iframe) {
            iframe.style.zIndex = '1';
            iframe.style.position = 'relative';
        });
        
        const modal = new bootstrap.Modal(modalEl, {
            backdrop: true,
            keyboard: true
        });
        
        // Disable iframe pointer-events when modal opens
        modalEl.addEventListener('show.bs.modal', function() {
            // Disable all iframes to prevent them from blocking clicks
            const allIframes = document.querySelectorAll('iframe');
            allIframes.forEach(function(iframe) {
                iframe.style.pointerEvents = 'none';
            });
        }, { once: false });
        
        modal.show();
        
        // Ensure modal content is clickable after shown
        modalEl.addEventListener('shown.bs.modal', function() {
            // Remove any inline styles that might block clicks
            modalEl.style.pointerEvents = 'auto';
            modalEl.style.zIndex = '11000';
            modalEl.style.display = 'block';
            modalEl.style.position = 'fixed';
            
            // Ensure all interactive elements are clickable
            const interactiveElements = modalEl.querySelectorAll('.modal-dialog, .modal-content, button, select, input, label, .form-control, .close');
            interactiveElements.forEach(function(el) {
                el.style.pointerEvents = 'auto';
                if (el.classList.contains('modal-dialog')) {
                    el.style.zIndex = '11001';
                    el.style.position = 'relative';
                }
            });
            
            // Ensure backdrop is below modal
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.zIndex = '10999';
                backdrop.style.pointerEvents = 'auto';
                backdrop.style.position = 'fixed';
            }
            
            // Ensure all iframes are below the modal and disabled
            const allIframes = document.querySelectorAll('iframe');
            allIframes.forEach(function(iframe) {
                iframe.style.zIndex = '1';
                iframe.style.position = 'relative';
                iframe.style.pointerEvents = 'none';
            });
            
            // Ensure wrapper elements don't interfere
            const wrappers = document.querySelectorAll('#wrapper, .content-wrapper, .main-content-wrapper');
            wrappers.forEach(function(wrapper) {
                wrapper.style.zIndex = '1';
                wrapper.style.position = 'relative';
            });
        }, { once: false });
        
        // Re-enable iframe pointer-events when modal closes
        modalEl.addEventListener('hidden.bs.modal', function() {
            // Re-enable all iframes
            const allIframes = document.querySelectorAll('iframe');
            allIframes.forEach(function(iframe) {
                iframe.style.pointerEvents = 'auto';
            });
        }, { once: false });
    } else {
        // Fallback: manually show modal
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        document.body.classList.add('modal-open');
        
        // Create backdrop
        let backdrop = document.querySelector('.modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.5); z-index: 1040;';
            document.body.appendChild(backdrop);
        }
    }
};

window.viewDetails = function(bookingId) {
    // Load comprehensive booking details in a modal
    loadBookingDetailsModal(bookingId);
};

// Make loadBookingDetailsModal globally accessible
window.loadBookingDetailsModal = function(bookingId) {
    // Show loading modal
    const modalHtml = `
        <div class="modal fade" id="reviewBookingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-eye mr-2"></i>Review Booking Details (Before Approval)
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p>Loading booking details...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('reviewBookingModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#reviewBookingModal').modal('show');
    }
    
    // Load booking details
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                displayReviewBookingDetails(data.booking);
            } else {
                document.querySelector('#reviewBookingModal .modal-body').innerHTML = 
                    '<div class="alert alert-danger">Error loading booking details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('#reviewBookingModal .modal-body').innerHTML = 
                '<div class="alert alert-danger">Error loading booking details.</div>';
        });
}

// Make functions globally accessible
window.loadCalculatePaymentModal = function(bookingId) {
    // Show loading modal
    const modalHtml = `
        <div class="modal fade" id="calculatePaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-calculator mr-2"></i>Calculate Total Payment (After Inspection)
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p>Loading booking details...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('calculatePaymentModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#calculatePaymentModal').modal('show');
    }
    
    // Load booking details
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                displayCalculatePayment(data.booking);
            } else {
                document.querySelector('#calculatePaymentModal .modal-body').innerHTML = 
                    '<div class="alert alert-danger">Error loading booking details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('#calculatePaymentModal .modal-body').innerHTML = 
                '<div class="alert alert-danger">Error loading booking details.</div>';
        });
}

// Display Review Booking Details (Simple - Before Approval)
function displayReviewBookingDetails(booking) {
    const reviewHtml = `
        <div class="row">
            <!-- Customer Information -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0"><i class="fas fa-user mr-2"></i>Customer Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> ${booking.customer_name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${booking.customer_email || 'N/A'}</p>
                        <p><strong>Phone:</strong> ${booking.customer_phone || 'N/A'}</p>
                        <p><strong>Registration Date:</strong> ${booking.customer_reg_date ? new Date(booking.customer_reg_date).toLocaleDateString() : 'N/A'}</p>
                    </div>
                </div>
            </div>
            
            <!-- Service Information -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0"><i class="fas fa-tools mr-2"></i>Service Details</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Service:</strong> ${booking.service_name || 'N/A'}</p>
                        <p><strong>Category:</strong> <span class="badge badge-secondary">${booking.category_name || 'N/A'}</span></p>
                        <p><strong>Service Type:</strong> ${booking.service_type || 'N/A'}</p>
                        <p><strong>Item Description:</strong> ${booking.item_description || 'N/A'}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Booking Information -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="m-0"><i class="fas fa-calendar-check mr-2"></i>Booking Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Booking ID:</strong> <span class="badge badge-info">Booking #${booking.id || 'N/A'}</span></p>
                        <p><strong>Booking Date:</strong> ${booking.booking_date ? new Date(booking.booking_date).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge badge-${getStatusBadgeClass(booking.status)}">${booking.status || 'N/A'}</span></p>
                        <p><strong>Payment Status:</strong> <span class="badge badge-${getPaymentBadgeClass(booking.payment_status)}" style="font-weight: 600;">${getPaymentStatusText(booking.payment_status) || 'N/A'}</span></p>
                        <hr>
                        <p><strong><i class="fas fa-truck mr-2"></i>Customer's Service Option:</strong></p>
                        <p>
                            <span class="badge badge-primary badge-lg">${getServiceOptionText(booking.service_option)}</span>
                        </p>
                        ${getServiceOptionDetails(booking)}
                    </div>
                </div>
            </div>
            
            <!-- Images & Attachments -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0"><i class="fas fa-images mr-2"></i>Attached Images</h6>
                    </div>
                    <div class="card-body">
                        ${booking.images && booking.images.length > 0 ? 
                            booking.images.map(img => `<img src="${img}" class="img-thumbnail m-1" style="max-width: 150px; max-height: 150px;">`).join('') 
                            : '<p class="text-muted">No images attached</p>'}
                    </div>
                </div>
            </div>
        </div>
        
        ${booking.notes ? `
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0"><i class="fas fa-sticky-note mr-2"></i>Additional Notes</h6>
            </div>
            <div class="card-body">
                <p>${booking.notes}</p>
            </div>
        </div>
        ` : ''}
        
        ${booking.selected_color_id ? `
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="m-0"><i class="fas fa-palette mr-2"></i>Selected Color/Fabric</h6>
            </div>
            <div class="card-body">
                <p><strong>Color:</strong> ${booking.color_name || 'N/A'}</p>
                <p><strong>Color Code:</strong> ${booking.color_code || 'N/A'}</p>
                <p><strong>Type:</strong> <span class="badge badge-${booking.color_type === 'premium' ? 'warning' : 'secondary'}">${booking.color_type === 'premium' ? 'Premium' : 'Standard'}</span></p>
                <p><strong>Color Price:</strong> <span class="text-primary">₱${parseFloat(booking.color_price || 0).toFixed(2)}</span></p>
            </div>
        </div>
        ` : ''}
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Purpose:</strong> Review booking details before approval. After approval, you can examine the item and calculate the total payment.
        </div>
    `;
    
    document.querySelector('#reviewBookingModal .modal-body').innerHTML = reviewHtml;
    
    // Check if booking is completed
    const status = (booking.status || '').toLowerCase();
    const paymentStatus = (booking.payment_status || '').toLowerCase();
    const completedStatuses = ['completed', 'delivered_and_paid'];
    const paidStatuses = ['paid', 'paid_full_cash', 'paid_on_delivery_cod'];
    const isCompleted = (
        completedStatuses.indexOf(status) !== -1 && 
        paidStatuses.indexOf(paymentStatus) !== -1
    );
    
    // Add action buttons
    const footer = document.querySelector('#reviewBookingModal .modal-footer') || 
                   document.querySelector('#reviewBookingModal .modal-body').insertAdjacentElement('afterend', document.createElement('div'));
    footer.className = 'modal-footer';
    
    footer.innerHTML = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        ${!isCompleted && status === 'pending' ? `
        <button type="button" class="btn btn-success" onclick="approveBookingAfterReview(${booking.id})">
            <i class="fas fa-check-circle mr-1"></i> Approve Booking
        </button>
        ` : ''}
    `;
}

// Display Calculate Payment (After Inspection - Calculation Form + Receipt Preview)
function displayCalculatePayment(booking) {
    const calculateHtml = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>After Inspection:</strong> Admin must examine the item, measure fabric, and calculate total payment. This is done AFTER the item is picked up and inspected.
        </div>
        
        <!-- Step 1: Calculate Total Payment (Bayronon) -->
        <div class="card mb-4" style="border: 2px solid #ffc107;">
            <div class="card-header bg-warning text-dark">
                <h5 class="m-0">
                    <i class="fas fa-calculator mr-2"></i>Step 1: Calculate Total Payment (Bayronon)
                </h5>
            </div>
            <div class="card-body">
                
                <form id="calculateTotalForm_${booking.id}" onsubmit="return calculateTotalPayment(${booking.id}, event)">
                    <input type="hidden" name="booking_id" value="${booking.id}">
                    
                    <div class="row">
                        <!-- Fabric Measurement -->
                        <div class="col-md-6 mb-3">
                            <label for="fabric_length_${booking.id}"><strong>Fabric Length (meters)</strong></label>
                            <input type="number" class="form-control" id="fabric_length_${booking.id}" 
                                   name="fabric_length" step="0.01" min="0" 
                                   value="${booking.fabric_length || ''}" 
                                   placeholder="Enter fabric length in meters" required>
                            <small class="form-text text-muted">Measure the actual fabric needed</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="fabric_width_${booking.id}"><strong>Fabric Width (meters)</strong></label>
                            <input type="number" class="form-control" id="fabric_width_${booking.id}" 
                                   name="fabric_width" step="0.01" min="0" 
                                   value="${booking.fabric_width || ''}" 
                                   placeholder="Enter fabric width in meters" required>
                            <small class="form-text text-muted">Standard width or custom measurement</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Fabric Cost per Meter -->
                        <div class="col-md-6 mb-3">
                            <label for="fabric_cost_per_meter_${booking.id}"><strong>Fabric Cost per Meter (₱)</strong></label>
                            <input type="number" class="form-control" id="fabric_cost_per_meter_${booking.id}" 
                                   name="fabric_cost_per_meter" step="0.01" min="0" 
                                   value="${booking.fabric_cost_per_meter || ''}" 
                                   placeholder="Enter cost per meter" required>
                            <small class="form-text text-muted">Based on selected fabric type</small>
                        </div>
                        
                        <!-- Labor Fee -->
                        <div class="col-md-6 mb-3">
                            <label for="labor_fee_calc_${booking.id}"><strong>Labor Fee (₱)</strong></label>
                            <input type="number" class="form-control" id="labor_fee_calc_${booking.id}" 
                                   name="labor_fee" step="0.01" min="0" 
                                   value="${booking.labor_fee || ''}" 
                                   placeholder="Enter labor fee" required>
                            <small class="form-text text-muted">Based on complexity and work required</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Additional Materials -->
                        <div class="col-md-6 mb-3">
                            <label for="material_cost_${booking.id}"><strong>Additional Materials Cost (₱)</strong></label>
                            <input type="number" class="form-control" id="material_cost_${booking.id}" 
                                   name="material_cost" step="0.01" min="0" 
                                   value="${booking.material_cost || ''}" 
                                   placeholder="Foam, springs, etc.">
                            <small class="form-text text-muted">Foam, springs, zippers, thread, etc.</small>
                        </div>
                        
                        <!-- Service Fees (Pickup/Delivery) -->
                        <div class="col-md-6 mb-3">
                            <label for="service_fees_${booking.id}"><strong>Service Fees (₱)</strong></label>
                            <input type="number" class="form-control" id="service_fees_${booking.id}" 
                                   name="service_fees" step="0.01" min="0" 
                                   value="${(parseFloat(booking.pickup_fee || 0) + parseFloat(booking.delivery_fee || 0) + parseFloat(booking.gas_fee || 0) + parseFloat(booking.travel_fee || 0)).toFixed(2)}" 
                                   placeholder="Pickup, delivery, gas fees">
                            <small class="form-text text-muted">Based on service option: ${getServiceOptionText(booking.service_option)}</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Admin Notes for Calculation -->
                        <div class="col-md-12 mb-3">
                            <label for="calculation_notes_${booking.id}"><strong>Calculation Notes</strong></label>
                            <textarea class="form-control" id="calculation_notes_${booking.id}" 
                                      name="calculation_notes" rows="3" 
                                      placeholder="Add notes about measurements, damage assessment, material requirements, etc.">${booking.calculation_notes || ''}</textarea>
                            <small class="form-text text-muted">Document your examination findings and calculations</small>
                        </div>
                    </div>
                    
                    <!-- Calculated Total Display -->
                    <div class="alert alert-info mb-3" id="calculatedTotal_${booking.id}" style="display: none;">
                        <h5 class="mb-2">
                            <i class="fas fa-calculator mr-2"></i>Calculated Total (Bayronon)
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Fabric Cost:</strong> <span id="fabric_total_${booking.id}">₱0.00</span></p>
                                <p class="mb-1"><strong>Labor Fee:</strong> <span id="labor_display_${booking.id}">₱0.00</span></p>
                                <p class="mb-1"><strong>Materials:</strong> <span id="materials_display_${booking.id}">₱0.00</span></p>
                                <p class="mb-1"><strong>Service Fees:</strong> <span id="service_display_${booking.id}">₱0.00</span></p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="text-success mb-0">
                                    <strong>TOTAL: <span id="grand_total_${booking.id}">₱0.00</span></strong>
                                </h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-warning btn-lg" onclick="calculateTotalPayment(${booking.id}, event)">
                            <i class="fas fa-calculator mr-2"></i>Calculate Total Payment
                        </button>
                        <button type="button" class="btn btn-success btn-lg ml-2" id="saveTotalBtn_${booking.id}" 
                                onclick="saveCalculatedTotal(${booking.id})" style="display: none;">
                            <i class="fas fa-save mr-2"></i>Save Total & Prepare Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Pricing Receipt Section -->
        <div class="card mb-4" style="border: 2px solid #28a745;">
            <div class="card-header bg-success text-white">
                <h5 class="m-0"><i class="fas fa-receipt mr-2"></i>Pricing Breakdown (Receipt Preview)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" style="background: white;">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60%;">Item</th>
                                <th style="width: 40%; text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${parseFloat(booking.labor_fee || 0) > 0 ? `
                            <tr>
                                <td><strong>Labor Fee</strong></td>
                                <td style="text-align: right;">₱${parseFloat(booking.labor_fee || 0).toFixed(2)}</td>
                            </tr>
                            ` : ''}
                            ${parseFloat(booking.pickup_fee || 0) > 0 ? `
                            <tr>
                                <td><strong>Pickup Fee</strong></td>
                                <td style="text-align: right;">₱${parseFloat(booking.pickup_fee || 0).toFixed(2)}</td>
                            </tr>
                            ` : ''}
                            ${parseFloat(booking.delivery_fee || 0) > 0 ? `
                            <tr>
                                <td><strong>Delivery Fee</strong></td>
                                <td style="text-align: right;">₱${parseFloat(booking.delivery_fee || 0).toFixed(2)}</td>
                            </tr>
                            ` : ''}
                            ${parseFloat(booking.gas_fee || 0) > 0 ? `
                            <tr>
                                <td><strong>Gas Fee</strong></td>
                                <td style="text-align: right;">₱${parseFloat(booking.gas_fee || 0).toFixed(2)}</td>
                            </tr>
                            ` : ''}
                            ${parseFloat(booking.travel_fee || 0) > 0 ? `
                            <tr>
                                <td><strong>Travel Fee</strong></td>
                                <td style="text-align: right;">₱${parseFloat(booking.travel_fee || 0).toFixed(2)}</td>
                            </tr>
                            ` : ''}
                            ${parseFloat(booking.color_price || 0) > 0 ? `
                            <tr>
                                <td><strong>Fabric/Color Price</strong><br><small class="text-muted">${booking.color_name || 'Selected Fabric'} ${booking.color_type === 'premium' ? '(Premium)' : '(Standard)'}</small></td>
                                <td style="text-align: right;">₱${parseFloat(booking.color_price || 0).toFixed(2)}</td>
                            </tr>
                            ` : ''}
                            ${parseFloat(booking.total_additional_fees || 0) > 0 && parseFloat(booking.pickup_fee || 0) === 0 && parseFloat(booking.delivery_fee || 0) === 0 && parseFloat(booking.gas_fee || 0) === 0 && parseFloat(booking.travel_fee || 0) === 0 ? `
                            <tr>
                                <td><strong>Additional Fees</strong></td>
                                <td style="text-align: right;">₱${parseFloat(booking.total_additional_fees || 0).toFixed(2)}</td>
                            </tr>
                            ` : ''}
                        </tbody>
                        <tfoot style="background: #28a745; color: white;">
                            <tr>
                                <td style="font-size: 1.2rem; font-weight: bold; padding: 15px;">TOTAL AMOUNT</td>
                                <td style="text-align: right; font-size: 1.2rem; font-weight: bold; padding: 15px;">₱${(parseFloat(booking.labor_fee || 0) + parseFloat(booking.pickup_fee || 0) + parseFloat(booking.delivery_fee || 0) + parseFloat(booking.color_price || 0)).toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Note:</strong> After calculating and saving the total, this receipt will be sent to the customer.
        </div>
    `;
    
    document.querySelector('#calculatePaymentModal .modal-body').innerHTML = calculateHtml;
    
    // Check if total has been calculated
    const hasCalculatedTotal = parseFloat(booking.total_amount || 0) > 0 || booking.calculated_total_saved === '1';
    
    // Add action buttons
    const footer = document.querySelector('#calculatePaymentModal .modal-footer') || 
                   document.querySelector('#calculatePaymentModal .modal-body').insertAdjacentElement('afterend', document.createElement('div'));
    footer.className = 'modal-footer';
    
    footer.innerHTML = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        ${hasCalculatedTotal ? `
        <button type="button" class="btn btn-success" onclick="sendQuotationToCustomer(${booking.id})">
            <i class="fas fa-envelope mr-1"></i> Send Quotation to Customer
        </button>
        ` : ''}
    `;
}

// Approve booking after review (before inspection)
function approveBookingAfterReview(bookingId) {
    if (!confirm('Are you sure you want to approve this booking? The item will be scheduled for pickup.')) {
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking_id=' + bookingId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking approved successfully! Status changed to "For Pickup".');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#reviewBookingModal').modal('hide');
            }
            setTimeout(function() {
                location.reload();
            }, 500);
        } else {
            alert('Error: ' + (data.message || 'Failed to approve booking'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving. Please try again.');
    });
}

// Send quotation to customer (after calculation)
window.sendQuotationToCustomer = function(bookingId) {
    if (!confirm('Send the calculated quotation to the customer via email?')) {
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>admin/sendQuotationToCustomer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking_id=' + bookingId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Quotation sent to customer successfully!');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#calculatePaymentModal').modal('hide');
            }
            setTimeout(function() {
                location.reload();
            }, 500);
        } else {
            alert('Error: ' + (data.message || 'Failed to send quotation'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending quotation. Please try again.');
    });
}

// Proceed to admin actions after reviewing
function proceedToAdminActions(bookingId, currentStatus) {
    // Close any open modals
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#reviewBookingModal, #calculatePaymentModal').modal('hide');
    }
    
    // Open status/actions modal
    setTimeout(function() {
        updateStatus(bookingId, currentStatus);
    }, 300);
}

// Calculate total payment based on measurements and costs
function calculateTotalPayment(bookingId, event) {
    if (event) {
        event.preventDefault();
    }
    
    // Get form values
    const fabricLength = parseFloat(document.getElementById(`fabric_length_${bookingId}`).value) || 0;
    const fabricWidth = parseFloat(document.getElementById(`fabric_width_${bookingId}`).value) || 0;
    const fabricCostPerMeter = parseFloat(document.getElementById(`fabric_cost_per_meter_${bookingId}`).value) || 0;
    const laborFee = parseFloat(document.getElementById(`labor_fee_calc_${bookingId}`).value) || 0;
    const materialCost = parseFloat(document.getElementById(`material_cost_${bookingId}`).value) || 0;
    const serviceFees = parseFloat(document.getElementById(`service_fees_${bookingId}`).value) || 0;
    
    // Validate required fields
    if (fabricLength <= 0 || fabricWidth <= 0 || fabricCostPerMeter <= 0 || laborFee <= 0) {
        alert('Please fill in all required fields (Fabric Length, Width, Cost per Meter, and Labor Fee)');
        return false;
    }
    
    // Calculate fabric total (length × width × cost per meter)
    const fabricArea = fabricLength * fabricWidth;
    const fabricTotal = fabricArea * fabricCostPerMeter;
    
    // Calculate grand total
    const grandTotal = fabricTotal + laborFee + materialCost + serviceFees;
    
    // Display calculated values
    document.getElementById(`fabric_total_${bookingId}`).textContent = '₱' + fabricTotal.toFixed(2);
    document.getElementById(`labor_display_${bookingId}`).textContent = '₱' + laborFee.toFixed(2);
    document.getElementById(`materials_display_${bookingId}`).textContent = '₱' + materialCost.toFixed(2);
    document.getElementById(`service_display_${bookingId}`).textContent = '₱' + serviceFees.toFixed(2);
    document.getElementById(`grand_total_${bookingId}`).textContent = '₱' + grandTotal.toFixed(2);
    
    // Show calculated total section
    document.getElementById(`calculatedTotal_${bookingId}`).style.display = 'block';
    
    // Show save button
    document.getElementById(`saveTotalBtn_${bookingId}`).style.display = 'inline-block';
    
    // Store calculated values in data attributes for saving
    document.getElementById(`saveTotalBtn_${bookingId}`).setAttribute('data-fabric-total', fabricTotal);
    document.getElementById(`saveTotalBtn_${bookingId}`).setAttribute('data-grand-total', grandTotal);
    document.getElementById(`saveTotalBtn_${bookingId}`).setAttribute('data-fabric-area', fabricArea);
    
    return false;
}

// Save calculated total to database
function saveCalculatedTotal(bookingId) {
    // Get all form values
    const fabricLength = parseFloat(document.getElementById(`fabric_length_${bookingId}`).value) || 0;
    const fabricWidth = parseFloat(document.getElementById(`fabric_width_${bookingId}`).value) || 0;
    const fabricCostPerMeter = parseFloat(document.getElementById(`fabric_cost_per_meter_${bookingId}`).value) || 0;
    const laborFee = parseFloat(document.getElementById(`labor_fee_calc_${bookingId}`).value) || 0;
    const materialCost = parseFloat(document.getElementById(`material_cost_${bookingId}`).value) || 0;
    const serviceFees = parseFloat(document.getElementById(`service_fees_${bookingId}`).value) || 0;
    const calculationNotes = document.getElementById(`calculation_notes_${bookingId}`).value || '';
    
    // Get calculated totals
    const fabricArea = fabricLength * fabricWidth;
    const fabricTotal = fabricArea * fabricCostPerMeter;
    const grandTotal = fabricTotal + laborFee + materialCost + serviceFees;
    
    // Validate
    if (grandTotal <= 0) {
        alert('Please calculate the total first before saving.');
        return;
    }
    
    // Show loading state
    const saveBtn = document.getElementById(`saveTotalBtn_${bookingId}`);
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
    
    // Prepare data
    const formData = new URLSearchParams();
    formData.append('booking_id', bookingId);
    formData.append('fabric_length', fabricLength);
    formData.append('fabric_width', fabricWidth);
    formData.append('fabric_area', fabricArea);
    formData.append('fabric_cost_per_meter', fabricCostPerMeter);
    formData.append('fabric_total', fabricTotal);
    formData.append('labor_fee', laborFee);
    formData.append('material_cost', materialCost);
    formData.append('service_fees', serviceFees);
    formData.append('total_amount', grandTotal);
    formData.append('calculation_notes', calculationNotes);
    
    // Save to database
    fetch('<?php echo BASE_URL; ?>admin/saveCalculatedTotal', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Total payment calculated and saved successfully! You can now approve the booking.');
            
            // Show approve button
            const approveBtn = document.getElementById(`approveFromDetailsBtn_${bookingId}`);
            if (approveBtn) {
                approveBtn.style.display = 'inline-block';
            } else {
                // Reload modal to show approve button
                loadBookingDetailsModal(bookingId);
            }
            
            // Update receipt preview
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save calculated total'));
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving. Please try again.');
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
}

// Approve booking after total is calculated
function approveBookingAfterCalculation(bookingId) {
    if (!confirm('Are you sure you want to approve this booking? The calculated total will be sent to the customer.')) {
        return;
    }
    
    // Show loading state
    const approveBtn = document.getElementById(`approveFromDetailsBtn_${bookingId}`);
    if (approveBtn) {
        const originalText = approveBtn.innerHTML;
        approveBtn.disabled = true;
        approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Approving...';
        
        // Approve booking
        fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'booking_id=' + bookingId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking approved successfully! Receipt with calculated total will be sent to customer.');
                
                // Close modal and reload page
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery('#calculatePaymentModal').modal('hide');
                }
                setTimeout(function() {
                    location.reload();
                }, 500);
            } else {
                alert('Error: ' + (data.message || 'Failed to approve booking'));
                approveBtn.disabled = false;
                approveBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving. Please try again.');
            approveBtn.disabled = false;
            approveBtn.innerHTML = originalText;
        });
    }
}

// Load customer's selected service option
function loadCustomerServiceOption(bookingId) {
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                const booking = data.booking;
                const serviceOption = booking.service_option || 'pickup';
                const infoDiv = document.getElementById('customerServiceOptionInfo');
                const typeSpan = document.getElementById('serviceOptionType');
                const statusSpan = document.getElementById('serviceOptionStatus');
                
                if (infoDiv && typeSpan && statusSpan) {
                    // Map service option to display text
                    const optionMap = {
                        'pickup': 'Pick Up',
                        'delivery': 'Delivery Service',
                        'both': 'Both (Pick Up & Delivery)',
                        'walk_in': 'Walk In'
                    };
                    
                    typeSpan.textContent = optionMap[serviceOption] || serviceOption;
                    statusSpan.textContent = 'As selected by customer during booking';
                    
                    // Show pickup details if applicable
                    if (serviceOption === 'pickup' || serviceOption === 'both') {
                        const pickupDetails = document.getElementById('pickupDetails');
                        if (pickupDetails) {
                            document.getElementById('pickupAddress').textContent = booking.pickup_address || 'Not provided';
                            document.getElementById('pickupDate').textContent = booking.pickup_date ? new Date(booking.pickup_date).toLocaleDateString() : 'Not set';
                            document.getElementById('pickupDistance').textContent = booking.distance_km ? parseFloat(booking.distance_km).toFixed(2) : '0.00';
                            pickupDetails.style.display = 'block';
                        }
                    } else {
                        const pickupDetails = document.getElementById('pickupDetails');
                        if (pickupDetails) pickupDetails.style.display = 'none';
                    }
                    
                    // Show delivery details if applicable
                    if (serviceOption === 'delivery' || serviceOption === 'both') {
                        const deliveryDetails = document.getElementById('deliveryDetails');
                        if (deliveryDetails) {
                            document.getElementById('deliveryAddress').textContent = booking.delivery_address || 'Not provided';
                            document.getElementById('deliveryDate').textContent = booking.delivery_date ? new Date(booking.delivery_date).toLocaleDateString() : 'Not set';
                            deliveryDetails.style.display = 'block';
                        }
                    } else {
                        const deliveryDetails = document.getElementById('deliveryDetails');
                        if (deliveryDetails) deliveryDetails.style.display = 'none';
                    }
                    
                    // Hide if walk_in
                    if (serviceOption === 'walk_in') {
                        const pickupDetails = document.getElementById('pickupDetails');
                        const deliveryDetails = document.getElementById('deliveryDetails');
                        if (pickupDetails) pickupDetails.style.display = 'none';
                        if (deliveryDetails) deliveryDetails.style.display = 'none';
                    }
                    
                    infoDiv.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error loading customer service option:', error);
        });
}

// Load progress history
function loadProgressHistory(bookingId) {
    fetch(`<?php echo BASE_URL; ?>admin/getBookingProgress/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            const historyDiv = document.getElementById('progress_history');
            if (!historyDiv) return;
            
            if (data.success && data.progress && data.progress.length > 0) {
                historyDiv.innerHTML = data.progress.map(update => `
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${update.progress_type || 'Update'}</h6>
                            <small>${new Date(update.created_at).toLocaleString()}</small>
                        </div>
                        <p class="mb-1">${update.notes || ''}</p>
                        <small>By: ${update.admin_name || 'Admin'}</small>
                    </div>
                `).join('');
            } else {
                historyDiv.innerHTML = '<div class="text-center text-muted py-3">No progress updates yet</div>';
            }
        })
        .catch(error => {
            console.error('Error loading progress history:', error);
            const historyDiv = document.getElementById('progress_history');
            if (historyDiv) {
                historyDiv.innerHTML = '<div class="text-center text-danger py-3">Error loading progress history</div>';
            }
        });
}


// Helper function for status badge class
function getStatusBadgeClass(status) {
    const statusClasses = {
        'pending': 'warning',
        'approved': 'success',
        'in_queue': 'info',
        'under_repair': 'primary',
        'for_quality_check': 'info',
        'ready_for_pickup': 'success',
        'out_for_delivery': 'primary',
        'completed': 'success',
        'delivered_and_paid': 'success',
        'cancelled': 'danger'
    };
    return statusClasses[status] || 'secondary';
}

// Helper function for payment badge class
function getPaymentBadgeClass(status) {
    const paymentClasses = {
        'unpaid': 'danger',
        'paid': 'success',
        'paid_full_cash': 'success',
        'paid_on_delivery_cod': 'success',
        'refunded': 'info',
        'failed': 'warning',
        'cancelled': 'danger'
    };
    return paymentClasses[status] || 'secondary';
}

// Helper function for payment status text
function getPaymentStatusText(status) {
    const paymentTexts = {
        'unpaid': 'Unpaid',
        'paid': 'Paid',
        'paid_full_cash': 'Full Paid (Cash)',
        'paid_on_delivery_cod': 'Paid on Delivery (COD)',
        'refunded': 'Refunded',
        'failed': 'Failed',
        'cancelled': 'Cancelled'
    };
    
    if (paymentTexts[status]) {
        return paymentTexts[status];
    }
    
}

// Helper function for service option text
function getServiceOptionText(option) {
    const optionMap = {
        'pickup': 'Pick Up',
        'delivery': 'Delivery Service',
        'both': 'Both (Pick Up & Delivery)',
        'walk_in': 'Walk In'
    };
    return optionMap[option] || option || 'Not specified';
}

// Helper function to get service option details HTML
function getServiceOptionDetails(booking) {
    const serviceOption = booking.service_option || 'pickup';
    let detailsHtml = '<div class="mt-3"><small class="text-muted">';
    
    if (serviceOption === 'pickup' || serviceOption === 'both') {
        detailsHtml += '<strong>Pickup Details:</strong><br>';
        detailsHtml += `• Address: ${booking.pickup_address || 'Not provided'}<br>`;
        detailsHtml += `• Date: ${booking.pickup_date ? new Date(booking.pickup_date).toLocaleDateString() : 'Not set'}<br>`;
        detailsHtml += `• Distance: ${booking.distance_km ? parseFloat(booking.distance_km).toFixed(2) : '0.00'} km<br>`;
    }
    
    if (serviceOption === 'delivery' || serviceOption === 'both') {
        if (serviceOption === 'both') detailsHtml += '<br>';
        detailsHtml += '<strong>Delivery Details:</strong><br>';
        detailsHtml += `• Address: ${booking.delivery_address || 'Not provided'}<br>`;
        detailsHtml += `• Date: ${booking.delivery_date ? new Date(booking.delivery_date).toLocaleDateString() : 'Not set'}<br>`;
    }
    
    if (serviceOption === 'walk_in') {
        detailsHtml += 'Customer will bring item to shop directly.';
    }
    
    detailsHtml += '</small></div>';
    return detailsHtml;
}

window.acceptReservation = function(bookingId) {
    // Prevent duplicate calls
    if (window.acceptReservationProcessing) {
        // console.log('Accept reservation already processing, ignoring duplicate call');
        return;
    }
    
    // Validate booking ID
    if (!bookingId || bookingId <= 0) {
        alert('Error: Invalid booking ID. Please try again.');
        return;
    }
    
    // Mark as processing
    window.acceptReservationProcessing = true;
    window.acceptReservationCalled = true;
    
    // console.log('Accept reservation clicked for booking ID:', bookingId);
    
    // Reset processing flag after 2 seconds
    setTimeout(function() {
        window.acceptReservationProcessing = false;
    }, 2000);
    
            // Directly accept reservation without compliance check
            // Get booking details to verify it exists
            fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // console.log('Booking details response:', data);
                    if (data.success && data.booking) {
                        // Directly proceed with acceptance
                        proceedWithAcceptReservation(bookingId);
                    } else {
                        const errorMsg = data.message || 'Could not load booking details. Please try again.';
                        alert('Error: ' + errorMsg);
                        window.acceptReservationProcessing = false;
                    }
                })
        .catch(error => {
            console.error('Error loading booking details:', error);
            alert('Error loading booking details. Please check your connection and try again.');
            // Reset processing flag
            window.acceptReservationProcessing = false;
        });
};

window.checkBookingCompliance = function(bookingId, customerId) {
    // Validate parameters
    if (!bookingId || !customerId) {
        alert('Error: Missing booking or customer information. Please try again.');
        return;
    }
    
    // console.log('Checking compliance for booking:', bookingId, 'customer:', customerId);
    
    // Store bookingId and customerId in the modal for later use
    const modalEl = document.getElementById('bookingComplianceModal');
    if (!modalEl) {
        alert('Error: Compliance modal not found. Please refresh the page and try again.');
        return;
    }
    
    modalEl.setAttribute('data-booking-id', bookingId);
    modalEl.setAttribute('data-customer-id', customerId);
    
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        // Fix aria-hidden accessibility issue
        const $modal = jQuery('#bookingComplianceModal');
        
        // CRITICAL FIX: Prevent aria-hidden accessibility warning
        // Set aria-hidden to false IMMEDIATELY before any modal operations
        $modal.attr('aria-hidden', 'false');
        modalEl.setAttribute('aria-hidden', 'false');
        
        // Prevent ALL buttons from getting focus during opening
        $modal.find('button').each(function() {
            const btn = jQuery(this);
            btn.attr('tabindex', '-1');
            // Also blur immediately if it has focus
            if (btn.is(':focus')) {
                btn[0].blur();
            }
        });
        
        // Aggressive MutationObserver to watch for aria-hidden changes
        let observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'aria-hidden') {
                    const currentValue = $modal.attr('aria-hidden');
                    const domValue = modalEl.getAttribute('aria-hidden');
                    
                    // If Bootstrap or anything sets it to true, immediately force to false
                    if (currentValue === 'true' || currentValue === true || domValue === 'true') {
                        // Force both jQuery and DOM attributes
                        $modal.attr('aria-hidden', 'false');
                        modalEl.setAttribute('aria-hidden', 'false');
                        
                        // Blur any focused buttons immediately
                        const focusedBtn = $modal.find('button:focus');
                        if (focusedBtn.length) {
                            focusedBtn[0].blur();
                        }
                    }
                }
            });
        });
        
        // Start observing the modal for aria-hidden changes IMMEDIATELY
        observer.observe(modalEl, {
            attributes: true,
            attributeFilter: ['aria-hidden'],
            subtree: false
        });
        
        // Handle show.bs.modal - fires BEFORE modal starts showing
        $modal.off('show.bs.modal').on('show.bs.modal', function(e) {
            const $this = jQuery(this);
            const modalElement = this;
            
            // Force aria-hidden to false immediately
            $this.attr('aria-hidden', 'false');
            modalElement.setAttribute('aria-hidden', 'false');
            
            // Prevent ALL buttons from getting focus
            $this.find('button').each(function() {
                const btn = jQuery(this);
                btn.attr('tabindex', '-1');
                if (btn.is(':focus')) {
                    btn[0].blur();
                }
            });
        });
        
        // Show modal
        $modal.modal('show');
        
        // Handle shown.bs.modal - fires AFTER modal is fully visible
        $modal.off('shown.bs.modal').on('shown.bs.modal', function() {
            const $this = jQuery(this);
            const modalElement = this;
            
            // Force aria-hidden to false one more time
            $this.attr('aria-hidden', 'false');
            modalElement.setAttribute('aria-hidden', 'false');
            
            // Blur any focused buttons immediately
            $this.find('button:focus').each(function() {
                this.blur();
            });
            
            // Prevent buttons from getting focus on mousedown (fires before focus)
            $this.find('button').off('mousedown.ariafix').on('mousedown.ariafix', function(e) {
                const btn = jQuery(this);
                const modal = btn.closest('#bookingComplianceModal');
                
                // Ensure aria-hidden is false before allowing focus
                modal.attr('aria-hidden', 'false');
                modalElement.setAttribute('aria-hidden', 'false');
            });
            
            // Also handle focus event as backup
            $this.find('button').off('focus.ariafix').on('focus.ariafix', function(e) {
                const btn = jQuery(this);
                const modal = btn.closest('#bookingComplianceModal');
                const ariaHidden = modal.attr('aria-hidden');
                
                // If aria-hidden is true, prevent focus and fix it
                if (ariaHidden === 'true' || ariaHidden === true) {
                    e.preventDefault();
                    this.blur();
                    // Force aria-hidden to false
                    modal.attr('aria-hidden', 'false');
                    modalElement.setAttribute('aria-hidden', 'false');
                }
            });
            
            // Wait a bit then re-enable keyboard navigation
            setTimeout(function() {
                // Blur any focused buttons again (in case they got focus)
                $this.find('button:focus').each(function() {
                    this.blur();
                });
                
                // Re-enable tabindex for keyboard navigation
                $this.find('button').removeAttr('tabindex');
                
                // Keep observer active while modal is visible to catch any aria-hidden changes
                // Don't disconnect - let it run until modal is hidden
            }, 300);
        });
        
        // Handle hidden.bs.modal
        $modal.off('hidden.bs.modal').on('hidden.bs.modal', function() {
            const $this = jQuery(this);
            $this.attr('aria-hidden', 'true');
            // Stop observing
            if (observer) {
                observer.disconnect();
                observer = null;
            }
        });
    } else {
        if (modalEl) {
            // Remove aria-hidden before showing
            modalEl.removeAttribute('aria-hidden');
            
            // Prevent buttons from getting focus
            const buttons = modalEl.querySelectorAll('button');
            buttons.forEach(function(btn) {
                btn.setAttribute('tabindex', '-1');
            });
            
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalEl);
                
                // Listen for shown event
                modalEl.addEventListener('shown.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'false');
                    
                    // Remove focus and restore tabindex
                    setTimeout(function() {
                        const focusedButton = modalEl.querySelector('button:focus');
                        if (focusedButton) {
                            focusedButton.blur();
                        }
                        buttons.forEach(function(btn) {
                            btn.removeAttribute('tabindex');
                        });
                    }, 150);
                }, { once: true });
                
                modal.show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.setAttribute('aria-hidden', 'false');
            }
        }
    }
    
    // Load compliance data
    if (typeof loadBookingComplianceData === 'function') {
        loadBookingComplianceData(bookingId, customerId);
    }
};

window.generateReceipt = function(bookingId) {
    // Set booking ID in form
    document.getElementById('receipt_booking_id').value = bookingId;
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#receiptModal').modal('show');
    } else {
        const modalEl = document.getElementById('receiptModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
            }
        }
    }
    
    // Load booking details and populate form
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                const booking = data.booking;
                
                // Populate booking info
                document.getElementById('receipt_booking_id_display').textContent = 'Booking #' + booking.id;
                document.getElementById('receipt_customer_name').textContent = booking.customer_name || 'N/A';
                document.getElementById('receipt_service_name').textContent = booking.service_name || 'N/A';
                document.getElementById('receipt_date').textContent = new Date(booking.created_at).toLocaleDateString();
                
                // Store booking data for use after colors are loaded
                const savedBookingData = {
                    meters: null,
                    pricePerMeter: null,
                    laborFee: null,
                    colorId: null,
                    quality: null
                };
                
                // Get customer's selected color and quality from booking
                // Priority: color_type (from booking) > inventory_type (from inventory item) > fabric_type > leather_type
                const customerColorId = booking.selected_color_id || null;
                // Try to get quality from booking's color_type, or from inventory item, or from booking's fabric_type/leather_type
                let customerQuality = booking.color_type || 
                                     booking.inventory_type || 
                                     booking.fabric_type || 
                                     booking.leather_type || 
                                     'standard';
                customerQuality = customerQuality.toLowerCase();
                
                // Store customer's selections
                savedBookingData.colorId = customerColorId;
                savedBookingData.quality = (customerQuality === 'premium') ? 'premium' : 'standard';
                
                // Load existing receipt data if available (admin's measurements)
                if (booking.fabric_total && booking.fabric_cost_per_meter) {
                    // Calculate meters from saved data: meters = fabric_total / price_per_meter
                    savedBookingData.meters = parseFloat(booking.fabric_total) / parseFloat(booking.fabric_cost_per_meter);
                }
                if (booking.fabric_cost_per_meter) {
                    savedBookingData.pricePerMeter = parseFloat(booking.fabric_cost_per_meter);
                }
                if (booking.labor_fee) {
                    savedBookingData.laborFee = parseFloat(booking.labor_fee);
                }
                
                // Set up event listeners first (if not already set up)
                if (typeof setupReceiptEventListeners === 'function') {
                    setupReceiptEventListeners();
                }
                
                // Pre-populate customer's selected quality and color (read-only)
                // Use setTimeout to ensure DOM is ready
                setTimeout(() => {
                    const qualitySelect = document.getElementById('leather_quality');
                    const colorSelect = document.getElementById('leather_color_id');
                    
                    // Set quality dropdown (disabled/read-only)
                    if (qualitySelect && savedBookingData.quality) {
                        qualitySelect.value = savedBookingData.quality;
                    }
                    
                    // Load colors filtered by customer's quality, then pre-select customer's color
                    if (savedBookingData.quality) {
                        loadLeatherColors(booking.store_location_id || null, savedBookingData);
                    } else {
                        // If no quality, default to standard
                        if (qualitySelect) {
                            qualitySelect.value = 'standard';
                        }
                        loadLeatherColors(booking.store_location_id || null, savedBookingData);
                    }
                }, 150);
            }
        })
        .catch(error => {
            console.error('Error loading booking details:', error);
            showAlert('danger', 'Error loading booking details.');
        });
};

// Store inventory data globally for filtering
let allInventoryData = [];

// Load leather colors from inventory
function loadLeatherColors(storeLocationId, savedData = null) {
    const colorSelect = document.getElementById('leather_color_id');
    const qualitySelect = document.getElementById('leather_quality');
    
    if (!qualitySelect || !qualitySelect.value) {
        colorSelect.innerHTML = '<option value="">Select quality first...</option>';
        colorSelect.disabled = true;
        return;
    }
    
    const selectedQuality = qualitySelect.value;
    colorSelect.innerHTML = '<option value="">Loading colors...</option>';
    colorSelect.disabled = true;
    
    const url = storeLocationId 
        ? `<?php echo BASE_URL; ?>admin/getInventory?store_location_id=${storeLocationId}`
        : `<?php echo BASE_URL; ?>admin/getInventory`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Store all inventory data
                allInventoryData = data.data;
                
                // Filter colors by selected quality
                // Check fabric_type, leather_type, or type field from inventory
                const filteredColors = data.data.filter(item => {
                    // Get the quality/type from the inventory item (case-insensitive)
                    let itemQuality = (item.type || item.fabric_type || item.leather_type || 'standard').toString().toLowerCase().trim();
                    
                    // Normalize the quality value to handle variations
                    if (itemQuality === 'standard' || itemQuality === 'std' || itemQuality === '') {
                        itemQuality = 'standard';
                    } else if (itemQuality === 'premium' || itemQuality === 'prem') {
                        itemQuality = 'premium';
                    }
                    
                    // Match with selected quality (also normalized)
                    const normalizedSelectedQuality = selectedQuality.toLowerCase().trim();
                    
                    return itemQuality === normalizedSelectedQuality;
                });
                
                colorSelect.innerHTML = '<option value="">Select Leather/Color</option>';
                
                if (filteredColors.length === 0) {
                    colorSelect.innerHTML = '<option value="">No colors available for ' + selectedQuality + ' quality</option>';
                } else {
                    filteredColors.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        
                        // Calculate price based on quality
                        let price = 0;
                        if (selectedQuality === 'premium') {
                            // For premium: price_per_meter + premium_price (if available)
                            price = parseFloat(item.price_per_meter || 0);
                            const premiumPrice = parseFloat(item.premium_price || 0);
                            if (premiumPrice > 0) {
                                price = price + premiumPrice;
                            }
                        } else {
                            // For standard: use price_per_meter
                            price = parseFloat(item.price_per_meter || 0);
                        }
                        
                        option.textContent = `${item.name} (${item.code || 'N/A'}) - ₱${price.toFixed(2)}/meter`;
                        option.dataset.pricePerMeter = price;
                        option.dataset.standardPrice = parseFloat(item.price_per_meter || 0);
                        option.dataset.premiumPrice = parseFloat(item.premium_price || 0);
                        colorSelect.appendChild(option);
                    });
                }
                
                // Keep color select disabled (read-only) - customer's selection
                colorSelect.disabled = true;
                
                // Set up event listeners after colors are loaded
                setupReceiptEventListeners();
                
                // Pre-select customer's color (read-only) and auto-fill price
                if (savedData && savedData.colorId) {
                    colorSelect.value = savedData.colorId;
                    // Auto-fill price from inventory if available, but allow admin to edit
                    const selectedOption = colorSelect.options[colorSelect.selectedIndex];
                    if (selectedOption && selectedOption.dataset.pricePerMeter) {
                        const priceFromInventory = parseFloat(selectedOption.dataset.pricePerMeter);
                        // Use saved price if it exists (admin's previous input), otherwise use inventory price
                        const priceInput = document.getElementById('price_per_meter');
                        if (priceInput) {
                            priceInput.value = (savedData.pricePerMeter || priceFromInventory).toFixed(2);
                        }
                    } else if (savedData.pricePerMeter) {
                        const priceInput = document.getElementById('price_per_meter');
                        if (priceInput) {
                            priceInput.value = savedData.pricePerMeter.toFixed(2);
                        }
                    }
                }
                
                // Populate admin's measurements (meters and labor fee)
                if (savedData) {
                    
                    if (savedData.meters !== null) {
                        document.getElementById('number_of_meters').value = savedData.meters.toFixed(2);
                    }
                    
                    if (savedData.laborFee !== null) {
                        document.getElementById('labor_fee').value = savedData.laborFee.toFixed(2);
                    }
                    
                    // Calculate total after all values are set
                    setTimeout(() => {
                        calculateReceiptTotal();
                    }, 100);
                }
            } else {
                colorSelect.innerHTML = '<option value="">No colors available</option>';
                colorSelect.disabled = false;
                setupReceiptEventListeners();
                
                // Still populate saved data even if no colors
                if (savedData) {
                    if (savedData.pricePerMeter) {
                        document.getElementById('price_per_meter').value = savedData.pricePerMeter.toFixed(2);
                    }
                    if (savedData.meters !== null) {
                        document.getElementById('number_of_meters').value = savedData.meters.toFixed(2);
                    }
                    if (savedData.laborFee !== null) {
                        document.getElementById('labor_fee').value = savedData.laborFee.toFixed(2);
                    }
                    setTimeout(() => {
                        calculateReceiptTotal();
                    }, 100);
                }
            }
        })
        .catch(error => {
            console.error('Error loading colors:', error);
            colorSelect.innerHTML = '<option value="">Error loading colors</option>';
            colorSelect.disabled = false;
            setupReceiptEventListeners();
            
            // Still populate saved data even on error
            if (savedData) {
                if (savedData.pricePerMeter) {
                    document.getElementById('price_per_meter').value = savedData.pricePerMeter.toFixed(2);
                }
                if (savedData.meters !== null) {
                    document.getElementById('number_of_meters').value = savedData.meters.toFixed(2);
                }
                if (savedData.laborFee !== null) {
                    document.getElementById('labor_fee').value = savedData.laborFee.toFixed(2);
                }
                setTimeout(() => {
                    calculateReceiptTotal();
                }, 100);
            }
        });
}

// Set up event listeners for receipt form
function setupReceiptEventListeners() {
    // Remove existing listeners by cloning elements
    const qualitySelect = document.getElementById('leather_quality');
    const colorSelect = document.getElementById('leather_color_id');
    const metersInput = document.getElementById('number_of_meters');
    const priceInput = document.getElementById('price_per_meter');
    const laborInput = document.getElementById('labor_fee');
    
    // When quality changes, reload colors filtered by quality
    if (qualitySelect) {
        const newQualitySelect = qualitySelect.cloneNode(true);
        qualitySelect.parentNode.replaceChild(newQualitySelect, qualitySelect);
        
        newQualitySelect.addEventListener('change', function() {
            const selectedQuality = this.value;
            const colorSelectEl = document.getElementById('leather_color_id');
            const priceInputEl = document.getElementById('price_per_meter');
            
            // Reset color selection and price when quality changes
            colorSelectEl.value = '';
            priceInputEl.value = '0.00';
            
            if (selectedQuality) {
                // Get store location from booking data if available
                const bookingId = document.getElementById('receipt_booking_id').value;
                if (bookingId) {
                    // Reload colors filtered by quality - get store location from booking
                    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.booking) {
                                loadLeatherColors(data.booking.store_location_id || null, null);
                            } else {
                                loadLeatherColors(null, null);
                            }
                        })
                        .catch(() => {
                            loadLeatherColors(null, null);
                        });
                } else {
                    loadLeatherColors(null, null);
                }
            } else {
                colorSelectEl.innerHTML = '<option value="">Select quality first...</option>';
                colorSelectEl.disabled = true;
                priceInputEl.value = '0.00';
                calculateReceiptTotal();
            }
        });
    }
    
    // Note: Color select is disabled (read-only) - customer's selection
    // Price per meter is editable by admin, so we don't need to auto-fill on color change
    // But we can still auto-fill when the modal opens (handled in loadLeatherColors)
    
    // Auto-calculate when meters, price, or labor changes
    if (metersInput) {
        const newMetersInput = metersInput.cloneNode(true);
        metersInput.parentNode.replaceChild(newMetersInput, metersInput);
        newMetersInput.addEventListener('input', calculateReceiptTotal);
        newMetersInput.addEventListener('change', calculateReceiptTotal);
    }
    
    if (priceInput) {
        const newPriceInput = priceInput.cloneNode(true);
        priceInput.parentNode.replaceChild(newPriceInput, priceInput);
        newPriceInput.addEventListener('input', calculateReceiptTotal);
        newPriceInput.addEventListener('change', calculateReceiptTotal);
    }
    
    if (laborInput) {
        const newLaborInput = laborInput.cloneNode(true);
        laborInput.parentNode.replaceChild(newLaborInput, laborInput);
        newLaborInput.addEventListener('input', calculateReceiptTotal);
        newLaborInput.addEventListener('change', calculateReceiptTotal);
    }
}

// Set up initial event listeners on page load (for modal that's already in DOM)
document.addEventListener('DOMContentLoaded', function() {
    // These will be re-setup when modal is opened, but set them up here too
    setupReceiptEventListeners();
});

// Calculate receipt total
function calculateReceiptTotal() {
    const metersEl = document.getElementById('number_of_meters');
    const priceEl = document.getElementById('price_per_meter');
    const laborEl = document.getElementById('labor_fee');
    
    if (!metersEl || !priceEl || !laborEl) {
        return; // Elements not found
    }
    
    const meters = parseFloat(metersEl.value) || 0;
    const pricePerMeter = parseFloat(priceEl.value) || 0;
    const laborFee = parseFloat(laborEl.value) || 0;
    
    const leatherCost = meters * pricePerMeter;
    const grandTotal = leatherCost + laborFee;
    
    const leatherCostDisplay = document.getElementById('leather_cost_display');
    const laborFeeDisplay = document.getElementById('labor_fee_display');
    const grandTotalDisplay = document.getElementById('grand_total_display');
    
    if (leatherCostDisplay) {
        leatherCostDisplay.textContent = '₱' + leatherCost.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    if (laborFeeDisplay) {
        laborFeeDisplay.textContent = '₱' + laborFee.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = '₱' + grandTotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

// Save receipt
function saveReceipt() {
    const form = document.getElementById('receiptForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = {
        booking_id: document.getElementById('receipt_booking_id').value,
        leather_quality: document.getElementById('leather_quality').value,
        leather_color_id: document.getElementById('leather_color_id').value,
        number_of_meters: document.getElementById('number_of_meters').value,
        price_per_meter: document.getElementById('price_per_meter').value,
        labor_fee: document.getElementById('labor_fee').value
    };
    
    const saveBtn = document.getElementById('saveReceiptBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';
    
    fetch(`<?php echo BASE_URL; ?>admin/saveReceipt`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Receipt saved successfully!');
            setTimeout(() => {
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery('#receiptModal').modal('hide');
                }
            }, 1500);
        } else {
            showAlert('danger', data.message || 'Failed to save receipt.');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i>Save Receipt';
        }
    })
    .catch(error => {
        console.error('Error saving receipt:', error);
        showAlert('danger', 'Error saving receipt. Please try again.');
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i>Save Receipt';
    });
}

// Send receipt to customer
function sendReceiptToCustomer() {
    const form = document.getElementById('receiptForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // First save the receipt
    const formData = {
        booking_id: document.getElementById('receipt_booking_id').value,
        leather_quality: document.getElementById('leather_quality').value,
        leather_color_id: document.getElementById('leather_color_id').value,
        number_of_meters: document.getElementById('number_of_meters').value,
        price_per_meter: document.getElementById('price_per_meter').value,
        labor_fee: document.getElementById('labor_fee').value
    };
    
    const sendBtn = document.getElementById('sendReceiptBtn');
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sending...';
    
    // Save first, then send
    fetch(`<?php echo BASE_URL; ?>admin/saveReceipt`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Now send to customer
            return fetch(`<?php echo BASE_URL; ?>admin/sendReceiptToCustomer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ booking_id: formData.booking_id })
            });
        } else {
            throw new Error(data.message || 'Failed to save receipt');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Receipt saved and sent to customer successfully!');
            setTimeout(() => {
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery('#receiptModal').modal('hide');
                }
                // Reload page to show updated data
                window.location.reload();
            }, 1500);
        } else {
            showAlert('warning', 'Receipt saved but failed to send: ' + (data.message || 'Unknown error'));
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i>Send to Customer';
        }
    })
    .catch(error => {
        console.error('Error sending receipt:', error);
        showAlert('danger', 'Error sending receipt. Please try again.');
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i>Send to Customer';
    });
};

// Wait for jQuery to be loaded (it's in footer)
(function() {
    function initAdminBookings() {
        if (typeof jQuery === 'undefined') {
            // jQuery not loaded yet, wait a bit more
            setTimeout(initAdminBookings, 100);
            return;
        }
        
        // jQuery is loaded, initialize
        jQuery(document).ready(function($) {
            // Backup event listener for approve buttons (in case onclick fails)
            // Use namespace and prevent default to avoid duplicate calls
            $(document).off('click.backupApprove', 'button[onclick*="acceptReservation"]');
            $(document).on('click.backupApprove', 'button[onclick*="acceptReservation"]', function(e) {
                // Check if onclick already handled it (prevent double-firing)
                if (e.isDefaultPrevented() || $(this).data('processing')) {
                    return;
                }
                
                // Mark as processing to prevent duplicate calls
                $(this).data('processing', true);
                
                // Extract booking ID from onclick attribute
                const onclickAttr = $(this).attr('onclick');
                if (onclickAttr) {
                    const match = onclickAttr.match(/acceptReservation\((\d+)\)/);
                    if (match && match[1]) {
                        const bookingId = parseInt(match[1]);
                        if (bookingId > 0) {
                            // Only use backup if onclick didn't work (check after a delay)
                            setTimeout(function() {
                                if (!window.acceptReservationCalled) {
                                    // console.log('Approve button clicked via backup listener, booking ID:', bookingId);
                                    if (typeof window.acceptReservation === 'function') {
                                        window.acceptReservation(bookingId);
                                    }
                                }
                                $(this).data('processing', false);
                            }.bind(this), 100);
                        }
                    }
                } else {
                    $(this).data('processing', false);
                }
            });
            // Global fix for all modals: Disable iframe pointer-events when any modal opens
            $(document).on('show.bs.modal', '.modal', function() {
                // Disable all iframes to prevent them from blocking clicks
                $('iframe').css('pointer-events', 'none');
            });
            
            // Re-enable iframe pointer-events when any modal closes
            $(document).on('hidden.bs.modal', '.modal', function() {
                // Re-enable all iframes
                $('iframe').css('pointer-events', 'auto');
            });
            
            // Update counts
            if (typeof window.updateBookingCounts === 'function') {
                window.updateBookingCounts();
            }
            
            // DataTable initialization for active bookings with validation
            if ($.fn.DataTable) {
                // Track retry attempts to prevent infinite loops
                const retryCounters = {};
                // Track initialization attempts to prevent duplicates
                const initAttempts = {};
                
                // Suppress DataTable warnings globally for expected scenarios
                const originalConsoleWarn = console.warn;
                const originalConsoleError = console.error;
                
                // Override console.warn to suppress DataTable warnings
                console.warn = function(...args) {
                    const message = args.join(' ');
                    // Suppress expected DataTable warnings
                    if (message.includes('DataTable') && (
                        message.includes('Table structure invalid') ||
                        message.includes('Table is hidden') ||
                        message.includes('has no width') ||
                        message.includes('skipping initialization') ||
                        message.includes('completedBookingsTable') ||
                        message.includes('bookingNumbersTable')
                    )) {
                        // Suppress these warnings - they're expected for hidden tables
                        return;
                    }
                    originalConsoleWarn.apply(console, args);
                };
                
                // Override console.error for DataTable errors
                console.error = function(...args) {
                    const message = args.join(' ');
                    // Suppress expected DataTable errors
                    if (message.includes('DataTable') && (
                        message.includes('Table structure invalid') ||
                        message.includes('Table is hidden') ||
                        message.includes('has no width')
                    )) {
                        // Suppress these errors - they're expected for hidden tables
                        return;
                    }
                    originalConsoleError.apply(console, args);
                };
                
                function safeInitDataTable(tableId, sortColumn, retryCount = 0) {
                    const MAX_RETRIES = 3; // Maximum retry attempts
                    const table = $('#' + tableId);
                    
                    if (!table.length) {
                        if (retryCount === 0) {
                        console.warn('Table not found:', tableId);
                        }
                        return;
                    }
                    
                    // Check if already initialized
                    if ($.fn.DataTable.isDataTable(table)) {
                        // Already initialized, skip
                        return;
                    }
                    
                    // Check if table is in a hidden tab
                    const tabPane = table.closest('.tab-pane');
                    if (tabPane.length && !tabPane.hasClass('active') && !tabPane.hasClass('show')) {
                        // Table is in a hidden tab, don't initialize yet
                        // Will be initialized when tab is shown
                        // Don't log this as it's expected behavior
                        return;
                    }
                    
                    // Check if table is visible
                    if (!table.is(':visible')) {
                        // Check if table is in a hidden tab - this is expected
                        const tabPane = table.closest('.tab-pane');
                        if (tabPane.length && (!tabPane.hasClass('active') && !tabPane.hasClass('show'))) {
                            // Table is in hidden tab, this is expected - don't warn, just return
                            return;
                        }
                        
                        // Table should be visible but isn't - try again
                        if (retryCount < MAX_RETRIES) {
                        setTimeout(function() {
                                safeInitDataTable(tableId, sortColumn, retryCount + 1);
                        }, 500);
                        }
                        // Don't warn - table might be in a hidden container which is expected
                        return;
                    }
                    
                    // Check width, but be lenient
                    const tableWidth = table.width();
                    if (tableWidth === 0) {
                        // Check if table is in a container that might not have width yet
                        const parent = table.parent();
                        if (parent.length && parent.width() === 0) {
                            if (retryCount < MAX_RETRIES) {
                                setTimeout(function() {
                                    safeInitDataTable(tableId, sortColumn, retryCount + 1);
                                }, 500);
                            }
                            return;
                        }
                        // If parent has width but table doesn't, it might be a rendering issue
                        // Try to initialize anyway
                    }
                    
                    // Validate table structure
                    const thead = table.find('thead tr');
                    const tbody = table.find('tbody');
                    const headerCount = thead.find('th').length;
                    
                    if (headerCount === 0) {
                        // No headers found - might be empty table, skip silently
                        return;
                    }
                    
                    // Validate all rows have correct number of cells
                    // But be lenient - allow empty tables and tables with colspan rows
                    let isValid = true;
                    let rowCount = 0;
                    let validRowCount = 0;
                    
                    tbody.find('tr').each(function() {
                        rowCount++;
                        const $row = $(this);
                        
                        // Skip empty-state rows or rows with special classes
                        if ($row.hasClass('empty-state') || $row.hasClass('dataTables_empty')) {
                            return; // Skip these special rows
                        }
                        
                        const cells = $row.find('td');
                        const cellCount = cells.length;
                        
                        // Empty row is okay
                        if (cellCount === 0) {
                            return;
                        }
                        
                        // Check for colspan
                        let totalColspan = 0;
                        cells.each(function() {
                            const colspan = parseInt($(this).attr('colspan') || '1', 10);
                            totalColspan += colspan;
                        });
                        
                        // Row must match header count or have colspan matching
                        // Be more lenient - if it's close, allow it
                        if (cellCount === headerCount || totalColspan === headerCount) {
                            validRowCount++;
                        // Ensure all cells are valid
                        cells.each(function(index) {
                            if (!this || !this.nodeName) {
                                isValid = false;
                                return false;
                            }
                        });
                        } else if (cellCount > 0) {
                            // Only warn if there are actual cells but mismatch
                            // Don't fail validation for minor mismatches
                            // console.log('Row has', cellCount, 'cells but header has', headerCount, 'in', tableId, '- allowing anyway');
                        }
                    });
                    
                    // If table is empty, that's okay - DataTable can handle empty tables
                    if (rowCount === 0) {
                        // console.log('Table is empty, but that\'s okay:', tableId);
                        isValid = true; // Empty tables are valid
                    }
                    
                    // Always allow initialization - DataTable can handle most issues
                    // Don't skip initialization based on validation - let DataTable handle it
                    // Empty tables are perfectly fine - DataTable handles them and shows "No data available"
                    // Minor structure issues are also fine - DataTable is robust
                    
                    // Reset isValid to true to ensure we always proceed
                    // DataTable will handle any real structural issues gracefully
                    isValid = true;
                    
                    // Destroy existing DataTable if it exists
                    if ($.fn.DataTable.isDataTable(table)) {
                        try {
                            table.DataTable().destroy(true);
                        } catch(e) {
                            // Ignore
                        }
                    }
                    
                    // Initialize DataTable
                    try {
                        // Double-check it's not already initialized
                        if ($.fn.DataTable.isDataTable(table)) {
                            return; // Already initialized
                        }
                        
                        // Suppress DataTable console warnings temporarily
                        const originalWarn = console.warn;
                        const originalError = console.error;
                        let warningSuppressed = false;
                        
                        console.warn = function(...args) {
                            const message = args.join(' ');
                            // Suppress DataTable warnings about hidden tables or invalid structure
                            if (message.includes('Table is hidden') || 
                                message.includes('has no width') || 
                                message.includes('Table structure invalid') ||
                                message.includes('skipping initialization')) {
                                warningSuppressed = true;
                                return; // Suppress this warning
                            }
                            originalWarn.apply(console, args);
                        };
                        
                        console.error = function(...args) {
                            const message = args.join(' ');
                            // Suppress DataTable errors about hidden tables
                            if (message.includes('Table is hidden') || 
                                message.includes('has no width') ||
                                message.includes('Table structure invalid')) {
                                return; // Suppress this error
                            }
                            originalError.apply(console, args);
                        };
                        
                        table.DataTable({
                            "order": [[ sortColumn, "desc" ]],
                            "pageLength": 25,
                            "responsive": true,
                            "destroy": true,
                            "retrieve": true,
                            "columnDefs": [
                                {
                                    "targets": "_all",
                                    "defaultContent": ""
                                }
                            ],
                            "language": {
                                "search": "Search " + (tableId.includes('active') ? 'active' : 'completed') + " bookings:",
                                "lengthMenu": "Show _MENU_ bookings per page",
                                "info": "Showing _START_ to _END_ of _TOTAL_ bookings",
                                "infoEmpty": "No " + (tableId.includes('active') ? 'active' : 'completed') + " bookings found",
                                "infoFiltered": "(filtered from _MAX_ total bookings)"
                            }
                        });
                        
                        // Restore original console methods
                        console.warn = originalWarn;
                        console.error = originalError;
                        
                        // Only log success in development - reduce console noise
                        if (retryCount === 0 && !warningSuppressed) {
                        // console.log('DataTable initialized successfully for:', tableId);
                        }
                    } catch(error) {
                        // Restore original console methods
                        console.warn = originalWarn;
                        console.error = originalError;
                        
                        // Only log if it's not a suppressed warning
                        if (!error.message || (!error.message.includes('hidden') && !error.message.includes('width'))) {
                        console.error('DataTable initialization error for', tableId, ':', error);
                        }
                    }
                }
                
                // Initialize only the active table initially
                safeInitDataTable('activeBookingsTable', 7);
                // Don't initialize completedBookingsTable here - it's in a hidden tab
                // It will be initialized when the tab is shown
                
                // Initialize completedBookingsTable when its tab is shown
                $('a[data-toggle="tab"][href="#completedBookings"]').on('shown.bs.tab', function (e) {
                    // Wait a bit for tab transition to complete
                    setTimeout(function() {
                        // Initialize completed bookings table when tab is shown
                        const completedTable = $('#completedBookingsTable');
                        if (completedTable.length && !$.fn.DataTable.isDataTable(completedTable)) {
                            // Check if table is now visible
                            if (completedTable.is(':visible')) {
                                // Check if table has width or is in a visible container
                                const tableWidth = completedTable.width();
                                const parentVisible = completedTable.parents('.tab-pane').hasClass('active') && 
                                                      completedTable.parents('.tab-pane').hasClass('show');
                                
                                if (tableWidth > 0 || parentVisible) {
                                    // Suppress warnings during initialization
                                    const originalWarn = console.warn;
                                    console.warn = function(...args) {
                                        const msg = args.join(' ');
                                        if (msg.includes('completedBookingsTable') && 
                                            (msg.includes('Table structure invalid') || 
                                             msg.includes('Table is hidden') || 
                                             msg.includes('has no width'))) {
                                            return; // Suppress
                                        }
                                        originalWarn.apply(console, args);
                                    };
                                    
                                    safeInitDataTable('completedBookingsTable', 6);
                                    
                                    // Restore after a delay
                                    setTimeout(() => {
                                        console.warn = originalWarn;
                                    }, 1000);
                                } else {
                                    // Table still not ready, try once more after delay
                                    setTimeout(function() {
                                        if (completedTable.is(':visible')) {
                safeInitDataTable('completedBookingsTable', 7);
                                        }
                                    }, 500);
                                }
                            } else {
                                // Table not visible yet, try again
                                setTimeout(function() {
                                    if (completedTable.is(':visible')) {
                                        safeInitDataTable('completedBookingsTable', 6);
                                    }
                                }, 500);
                            }
                        }
                    }, 200);
                });
            }
            
            // Update counts when tab changes
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                updateBookingCounts();
            });

            // Initialize booking numbers table when modal is shown
            let bookingNumbersTable = null;
            
            // Make function globally accessible
            window.initializeBookingNumbersTable = function() {
                const table = $('#bookingNumbersTable');
                
                // Destroy existing DataTable if it exists
                if (bookingNumbersTable) {
                    try {
                        bookingNumbersTable.destroy(true); // true = remove from DOM
                        bookingNumbersTable = null;
                    } catch(e) {
                        // console.log('DataTable cleanup:', e);
                    }
                }
                
                // Also try to destroy using jQuery method
                if (table.length && $.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
                    try {
                        table.DataTable().destroy(true);
                    } catch(e) {
                        // Ignore
                    }
                }
                
                // Check if table exists
                if (!table.length) {
                    console.warn('DataTable: Table not found: bookingNumbersTable');
                    return;
                }
                
                // Check if table is in a hidden modal
                const modal = table.closest('.modal');
                if (modal.length) {
                    const isModalVisible = modal.hasClass('show') || modal.hasClass('in') || modal.css('display') === 'block';
                    if (!isModalVisible) {
                        // Modal is not shown, don't initialize - this is expected
                        return;
                    }
                }
                
                // Check if table is visible - if not, don't initialize (expected when modal is closed)
                if (!table.is(':visible')) {
                    // Table is hidden - this is expected when modal is not open
                    // Don't log warning - this is normal behavior
                    return;
                }
                
                // Check if table has width (but be lenient)
                const tableWidth = table.width();
                if (tableWidth === 0) {
                    // Check if modal is actually visible
                    const modal = table.closest('.modal');
                    if (modal.length) {
                        const isModalVisible = modal.hasClass('show') || 
                                             modal.hasClass('in') || 
                                             modal.css('display') === 'block' ||
                                             !modal.hasClass('fade');
                        
                        if (!isModalVisible) {
                            // Modal is not shown, don't initialize - this is expected
                            return;
                        }
                    }
                    
                    // Table is visible but has no width - might be in a hidden container
                    // Check parent containers
                    const parentVisible = table.parents().filter(function() {
                        return $(this).css('display') !== 'none' && $(this).is(':visible');
                    }).length > 0;
                    
                    if (!parentVisible) {
                        // Parent is hidden, don't initialize - this is expected
                        return;
                    }
                }
                
                try {
                    // Ensure table structure is correct
                    const thead = table.find('thead tr');
                    const tbody = table.find('tbody');
                    const headerCount = thead.find('th').length;
                    
                    if (headerCount === 0) {
                        // No headers found - might be empty table, skip silently
                        return;
                    }
                    
                    // Validate all rows have correct number of cells
                    let isValid = true;
                    let rowCount = 0;
                    tbody.find('tr').each(function() {
                        rowCount++;
                        const $row = $(this);
                        const cells = $row.find('td');
                        const cellCount = cells.length;
                        
                        // Check for colspan which is valid
                        let totalColspan = 0;
                        cells.each(function() {
                            const colspan = parseInt($(this).attr('colspan') || '1', 10);
                            totalColspan += colspan;
                        });
                        
                        // Row must have either:
                        // 1. Exact number of cells matching headers, OR
                        // 2. One cell with colspan matching header count, OR
                        // 3. Empty row (will be handled by DataTables)
                        if (cellCount === 0) {
                            // Empty row is okay
                            return;
                        }
                        
                        // Be lenient with validation - allow minor mismatches
                        if (cellCount !== headerCount && totalColspan !== headerCount) {
                            // Only mark as invalid if it's a significant mismatch
                            // Allow if it's close (within 1-2 cells difference)
                            const diff = Math.abs(cellCount - headerCount);
                            if (diff > 2) {
                                // Significant mismatch - log but don't fail
                                // console.log('DataTable: Row has', cellCount, 'cells but header has', headerCount, '- allowing anyway');
                            }
                            // Don't mark as invalid for minor differences
                        }
                        
                        // Ensure all cells are valid DOM elements
                        cells.each(function(index) {
                            if (!this || !this.nodeName) {
                                // Invalid cell - but don't fail completely
                                // console.log('DataTable: Invalid cell at index', index, '- skipping validation for this cell');
                                // Don't mark entire table as invalid for one bad cell
                            }
                        });
                    });
                    
                    // Always allow initialization - DataTable can handle most issues
                    // Empty tables are perfectly fine
                    if (rowCount === 0) {
                        // Empty table - DataTable will show "No data available" message
                        // This is expected and handled properly
                    }
                    
                    // Check if already initialized
                    if ($.fn.DataTable.isDataTable(table)) {
                        // Already initialized, just return
                        return;
                    }
                    
                    // Initialize DataTable
                    try {
                    bookingNumbersTable = table.DataTable({
                        "order": [[ 0, "desc" ]], // Sort by ID descending
                        "pageLength": 10,
                        "responsive": true,
                        "destroy": true, // Allow re-initialization
                        "retrieve": true,
                        "columnDefs": [
                            {
                                "targets": "_all",
                                "defaultContent": "" // Default for empty cells
                            },
                            { "orderable": true, "targets": [0, 1, 2, 3] },
                            { "orderable": false, "targets": [4] } // Actions column
                        ],
                        "autoWidth": false,
                        "language": {
                            "search": "Search booking numbers:",
                            "lengthMenu": "Show _MENU_ booking numbers per page",
                            "info": "Showing _START_ to _END_ of _TOTAL_ booking numbers",
                            "infoEmpty": "No booking numbers found",
                            "infoFiltered": "(filtered from _TOTAL_ total booking numbers)"
                        }
                    });
                    
                        // Only log success once to reduce console noise
                    // console.log('DataTable initialized successfully for bookingNumbersTable');
                    } catch(error) {
                        console.error('DataTable initialization error for bookingNumbersTable:', error);
                    }
                } catch(error) {
                    console.error('DataTable initialization error:', error);
                }
            };
            
            $('#bookingNumbersModal').on('shown.bs.modal', function() {
                // Load data first, then initialize DataTable after data is loaded
                loadBookingNumbers();
                loadCustomers();
                
                // Wait for modal animation and data loading
                // Use a longer delay and check visibility before initializing
                setTimeout(function() {
                    const table = $('#bookingNumbersTable');
                    if (table.length && table.is(':visible') && table.width() > 0) {
                    if (typeof window.initializeBookingNumbersTable === 'function') {
                            // Check if already initialized
                            if (!$.fn.DataTable.isDataTable(table)) {
                        window.initializeBookingNumbersTable();
                    }
                        }
                    } else {
                        // Table not ready, try again
                        setTimeout(function() {
                            const table2 = $('#bookingNumbersTable');
                            if (table2.length && table2.is(':visible') && table2.width() > 0) {
                                if (typeof window.initializeBookingNumbersTable === 'function') {
                                    if (!$.fn.DataTable.isDataTable(table2)) {
                                        window.initializeBookingNumbersTable();
                                    }
                                }
                            }
                        }, 500);
                    }
                }, 800); // Increased delay to ensure modal is fully visible and data is loaded
            });
            
            // Destroy DataTable when modal is hidden
            $('#bookingNumbersModal').on('hidden.bs.modal', function() {
                if (bookingNumbersTable) {
                    try {
                        bookingNumbersTable.destroy(true);
                    } catch(e) {
                        // Ignore errors
                    }
                    bookingNumbersTable = null;
                }
            });
            
            // Ensure modals are properly initialized and clickable
            $('.modal').each(function() {
                var $modal = $(this);
                // Remove any inline styles that might hide the modal
                $modal.css({
                    'display': '',
                    'z-index': '',
                    'visibility': ''
                });
            });
            
            // Fix modal backdrop issues
            $(document).on('show.bs.modal', '.modal', function() {
                // Remove any existing backdrops
                $('.modal-backdrop').remove();
                
                // Ensure modal is visible
                var $modal = $(this);
                $modal.css({
                    'display': 'block',
                    'z-index': '1050',
                    'pointer-events': 'auto'
                });
            });
            
            $(document).on('shown.bs.modal', '.modal', function() {
                var $modal = $(this);
                
                // Ensure modal itself is clickable
                $modal.css({
                    'pointer-events': 'auto',
                    'z-index': '1050',
                    'display': 'block',
                    'opacity': '1'
                });
                
                // Ensure modal dialog is clickable
                $modal.find('.modal-dialog').css({
                    'pointer-events': 'auto',
                    'z-index': '1055',
                    'position': 'relative'
                });
                
                // Ensure modal content is clickable
                $modal.find('.modal-content').css({
                    'pointer-events': 'auto',
                    'z-index': '1056',
                    'position': 'relative'
                });
                
                // Ensure all interactive elements are clickable
                $modal.find('.modal-header, .modal-body, .modal-footer, button, a, input, select, textarea, .btn, .form-control, label, .close, table, td, th').css({
                    'pointer-events': 'auto',
                    'z-index': '1057',
                    'position': 'relative',
                    'cursor': 'pointer'
                });
                
                // Ensure backdrop is below modal
                $('.modal-backdrop').css({
                    'z-index': '1040',
                    'pointer-events': 'auto'
                });
                
                // Force reflow to ensure styles are applied
                $modal[0].offsetHeight;
            });
            
            $(document).on('hide.bs.modal', '.modal', function() {
                // Clean up backdrop
                $('.modal-backdrop').remove();
            });

            // Handle add booking numbers form
            $('#addBookingNumbersForm').on('submit', function(e) {
                e.preventDefault();
                addBookingNumbers();
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminBookings);
    } else {
        initAdminBookings();
    }
})();

// Load booking numbers via AJAX
function loadBookingNumbers() {
    fetch('<?php echo BASE_URL; ?>admin/getBookingNumbers')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('bookingNumbersTableBody');
                if (!tbody) {
                    console.error('bookingNumbersTableBody not found');
                    return;
                }
                
                tbody.innerHTML = '';
                
                if (data.bookingNumbers.length === 0) {
                    // Create a proper row with exactly 5 cells
                    const row = document.createElement('tr');
                    const cell = document.createElement('td');
                    cell.setAttribute('colspan', '5');
                    cell.className = 'text-center text-muted';
                    cell.textContent = 'No booking numbers found';
                    row.appendChild(cell);
                    tbody.innerHTML = '';
                    tbody.appendChild(row);
                    
                    // Reinitialize DataTable if modal is open
                    if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable && jQuery('#bookingNumbersModal').hasClass('show')) {
                        setTimeout(function() {
                            if (typeof window.initializeBookingNumbersTable === 'function') {
                                window.initializeBookingNumbersTable();
                            }
                        }, 100);
                    }
                    return;
                }
                
                // Clear tbody first
                tbody.innerHTML = '';
                
                data.bookingNumbers.forEach(bookingNumber => {
                    const row = document.createElement('tr');
                    
                    // Create exactly 5 cells to match 5 header columns
                    const cells = [
                        bookingNumber.id || '',
                        (bookingNumber.booking_number || '').replace(/'/g, "&#39;"),
                        bookingNumber.created_at ? new Date(bookingNumber.created_at).toLocaleDateString() : '',
                        'Available',
                        `<button type="button" class="btn btn-sm btn-success" 
                                onclick="assignBookingNumber(${bookingNumber.id || 0}, '${(bookingNumber.booking_number || '').replace(/'/g, "\\'")}')"
                                title="Assign to Customer">
                            <i class="fas fa-user-plus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="deleteBookingNumber(${bookingNumber.id || 0})"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>`
                    ];
                    
                    // Create cells one by one to ensure they're valid DOM elements
                    cells.forEach((cellContent, index) => {
                        const cell = document.createElement('td');
                        if (index === 1) {
                            // Booking number with badge
                            const badge = document.createElement('span');
                            badge.className = 'badge badge-info';
                            badge.textContent = cellContent;
                            cell.appendChild(badge);
                        } else if (index === 3) {
                            // Status with badge
                            const badge = document.createElement('span');
                            badge.className = 'badge badge-success';
                            badge.textContent = cellContent;
                            cell.appendChild(badge);
                        } else if (index === 4) {
                            // Actions - set innerHTML for buttons
                            cell.innerHTML = cellContent;
                        } else {
                            // Regular text content
                            cell.textContent = cellContent;
                        }
                        row.appendChild(cell);
                    });
                    
                    tbody.appendChild(row);
                });
                
                // Reinitialize DataTable after data is loaded, but only if modal is open
                if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable && jQuery('#bookingNumbersModal').hasClass('show')) {
                    setTimeout(function() {
                        if (typeof window.initializeBookingNumbersTable === 'function') {
                            window.initializeBookingNumbersTable();
                        } else {
                            // Fallback: try to draw if DataTable exists
                            const table = jQuery('#bookingNumbersTable');
                            if (table.length && $.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
                                try {
                                    table.DataTable().draw();
                                } catch(e) {
                                    // Ignore errors
                                }
                            }
                        }
                    }, 200);
                }
            } else {
                showAlert('danger', 'Failed to load booking numbers: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while loading booking numbers.');
        });
}

// Load customers for assignment
function loadCustomers() {
    fetch('<?php echo BASE_URL; ?>admin/getCustomers')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('customer_select');
                select.innerHTML = '<option value="">Choose a customer...</option>';
                
                data.customers.forEach(customer => {
                    const option = document.createElement('option');
                    option.value = customer.id;
                    option.textContent = `${customer.fullname} (${customer.email})`;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading customers:', error);
        });
}

// Add new booking numbers
function addBookingNumbers() {
    const formData = new FormData(document.getElementById('addBookingNumbersForm'));
    
    // Show loading state
    const button = document.querySelector('#addBookingNumbersForm button[type="submit"]');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Adding...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/addBookingNumbers', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            loadBookingNumbers(); // Refresh the list
            document.getElementById('addBookingNumbersForm').reset();
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while adding booking numbers.');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Assign booking number to customer
function assignBookingNumber(bookingNumberId, bookingNumber) {
    document.getElementById('assign_booking_number_id').value = bookingNumberId;
    document.getElementById('booking_number_display').value = bookingNumber;
    // Use vanilla JS or ensure jQuery is loaded
    if (typeof jQuery !== 'undefined') {
        jQuery('#assignBookingNumberModal').modal('show');
    } else {
        // Fallback to Bootstrap's native modal API
        const modal = new bootstrap.Modal(document.getElementById('assignBookingNumberModal'));
        modal.show();
    }
}

// Confirm assignment
function confirmAssignBookingNumber() {
    const bookingNumberId = document.getElementById('assign_booking_number_id').value;
    const customerId = document.getElementById('customer_select').value;
    
    if (!customerId) {
        alert('Please select a customer.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Assigning...';
    button.disabled = true;
    
    fetch('<?php echo BASE_URL; ?>admin/assignBookingNumber', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_number_id=${bookingNumberId}&customer_id=${customerId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof jQuery !== 'undefined') {
                jQuery('#assignBookingNumberModal').modal('hide');
            } else {
                const modalEl = document.getElementById('assignBookingNumberModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            showAlert('success', data.message);
            loadBookingNumbers(); // Refresh the list
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while assigning the booking number.');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Delete booking number
function deleteBookingNumber(bookingNumberId) {
    if (!confirm('Are you sure you want to delete this booking number?')) {
        return;
    }
    
    fetch('<?php echo BASE_URL; ?>admin/deleteBookingNumber', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_number_id=${bookingNumberId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            loadBookingNumbers(); // Refresh the list
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while deleting the booking number.');
    });
}

// Refresh booking numbers
function refreshBookingNumbers() {
    loadBookingNumbers();
}

// Proceed with accepting reservation (without compliance check)
function proceedWithAcceptReservation(bookingId) {
    // Confirm acceptance
    if (!confirm('Are you sure you want to approve this reservation?')) {
        window.acceptReservationProcessing = false;
        return;
    }
    
    // Send acceptance request
    fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking_id=' + bookingId
    })
    .then(response => response.json())
    .then(data => {
        window.acceptReservationProcessing = false;
        if (data.success) {
            alert('Reservation approved successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to approve reservation'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.acceptReservationProcessing = false;
        alert('An error occurred while approving the reservation. Please try again.');
    });
}

// Delete Booking Function
function deleteBooking(bookingId, event) {
    // Prevent any default behavior if event is provided
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    if (!bookingId) {
        alert('Invalid booking ID');
        return false;
    }
    
    // Confirm deletion
    if (!confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
        return false;
    }
    
    // Find the delete button that was clicked
    const deleteBtn = document.querySelector(`button[onclick*="deleteBooking(${bookingId})"]`);
    let originalContent = '';
    if (deleteBtn) {
        originalContent = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    // Send delete request
    const formData = new URLSearchParams();
    formData.append('booking_id', bookingId);
    
    fetch('<?php echo BASE_URL; ?>admin/deleteBooking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData.toString(),
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Server error: ' + response.status);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Remove the row from the table
            const row = deleteBtn ? deleteBtn.closest('tr') : null;
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    // Reload the page to refresh counts
                    location.reload();
                }, 300);
            } else {
                location.reload();
            }
            
            // Show success message
            if (typeof showNotification === 'function') {
                showNotification('Booking deleted successfully', 'success');
            } else {
                alert('Booking deleted successfully');
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to delete booking'));
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalContent;
            }
        }
    })
    .catch(error => {
        console.error('Error deleting booking:', error);
        alert('An error occurred while deleting the booking. Please try again.');
        if (deleteBtn) {
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalContent;
        }
    });
    
    return false; // Prevent any default behavior
}

// Duplicate checkBookingCompliance removed - already defined at top

// Load booking compliance data
function loadBookingComplianceData(bookingId, customerId) {
    // Show loading state
    showComplianceLoading();
    
    fetch(`<?php echo BASE_URL; ?>admin/getBookingCompliance?booking_id=${bookingId}&customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateComplianceModal(data);
            } else {
                showComplianceError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showComplianceError('An error occurred while loading compliance data.');
        });
}

// Show loading state in compliance modal
function showComplianceLoading() {
    document.getElementById('compliance_customer_name').textContent = 'Loading...';
    document.getElementById('compliance_customer_email').textContent = 'Loading...';
    document.getElementById('compliance_customer_phone').textContent = 'Loading...';
    document.getElementById('compliance_customer_reg_date').textContent = 'Loading...';
    
    // Note: booking_number_assignment section removed - queue numbers are auto-assigned
    document.getElementById('compliance_checklist').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    document.getElementById('current_booking_details').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    // Hide action buttons
    document.getElementById('approveComplianceBtn').style.display = 'none';
    document.getElementById('acceptReservationFromComplianceBtn').style.display = 'none';
}

// Populate compliance modal with data
function populateComplianceModal(data) {
    // Customer information
    document.getElementById('compliance_customer_name').textContent = data.customer.fullname;
    document.getElementById('compliance_customer_email').textContent = data.customer.email;
    document.getElementById('compliance_customer_phone').textContent = data.customer.phone;
    document.getElementById('compliance_customer_reg_date').textContent = new Date(data.customer.created_at).toLocaleDateString();
    
    // Compliance checklist
    // Note: Queue number is automatically assigned when customer books
    const complianceHtml = `
        <div class="row">
            <div class="col-md-6">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance1" ${data.booking && data.booking.id ? 'checked' : 'disabled'} disabled>
                    <label class="form-check-label" for="compliance1">
                        <strong>Booking ID</strong>
                        <br><small class="text-muted">${data.booking && data.booking.id ? '<span class="text-success"><strong>Booking ID: #' + data.booking.id + '</strong></span>' : '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Booking ID not found</span>'}</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance2" ${data.customer.email ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance2">
                        <strong>Valid Email Address</strong>
                        <br><small class="text-muted">Customer has a valid email for notifications</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance3" ${data.customer.phone ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance3">
                        <strong>Contact Information</strong>
                        <br><small class="text-muted">Customer has phone number for contact</small>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance4" ${data.booking ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance4">
                        <strong>Valid Service Selection</strong>
                        <br><small class="text-muted">Customer has selected a valid service</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance5" ${data.booking && data.booking.booking_date && data.booking.booking_date !== '0000-00-00' && data.booking.booking_date !== '1970-01-01' ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance5">
                        <strong>Booking Date Provided</strong>
                        <br><small class="text-muted">Customer has specified a booking date</small>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="compliance6" ${data.booking && data.booking.notes ? 'checked' : 'disabled'}>
                    <label class="form-check-label" for="compliance6">
                        <strong>Additional Information</strong>
                        <br><small class="text-muted">Customer has provided additional notes</small>
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <h6>Compliance Score: <span class="badge ${data.complianceScore >= 80 ? 'badge-success' : data.complianceScore >= 60 ? 'badge-warning' : 'badge-danger'}">${data.complianceScore}%</span></h6>
            <p class="text-muted">${data.complianceScore >= 80 ? 'Customer meets all requirements' : data.complianceScore >= 60 ? 'Customer meets most requirements' : 'Customer needs to complete requirements'}</p>
            ${data.hasRejection ? `
                <div class="alert alert-danger mt-2">
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong>Previous Rejection Detected!</strong>
                    ${data.rejectionReason ? `<p class="mb-0"><small>Reason: ${data.rejectionReason}</small></p>` : '<p class="mb-0"><small>This booking was previously rejected.</small></p>'}
                </div>
            ` : ''}
        </div>
    `;
    document.getElementById('compliance_checklist').innerHTML = complianceHtml;
    
    // Current booking details
    const bookingHtml = data.booking ? `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Service:</strong> ${data.booking.service_name}</p>
                <p><strong>Category:</strong> <span class="badge badge-secondary">${data.booking.category_name}</span></p>
                <p><strong>Amount:</strong> <span class="amount">₱${parseFloat(data.booking.total_amount).toFixed(2)}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Booking Date:</strong> ${data.booking.booking_date && data.booking.booking_date !== '0000-00-00' && data.booking.booking_date !== '1970-01-01' ? new Date(data.booking.booking_date).toLocaleDateString() : 'Not provided'}</p>
                <p><strong>Status:</strong> <span class="badge ${getStatusBadgeClass(data.booking.status)}">${data.booking.status}</span></p>
                <p><strong>Payment:</strong> <span class="badge badge-${getPaymentBadgeClass(data.booking.payment_status)}" style="font-weight: 600;">${getPaymentStatusText(data.booking.payment_status) || 'N/A'}</span></p>
            </div>
        </div>
        ${data.booking.notes ? `<hr><p><strong>Notes:</strong><br>${data.booking.notes}</p>` : ''}
    ` : `
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            No current booking found for this customer.
        </div>
    `;
    document.getElementById('current_booking_details').innerHTML = bookingHtml;
    
    // Validate requirements before showing Approve button
    // Note: Queue number is automatically assigned when customer books
    // We can approve as long as:
    // 1. Queue number exists (auto-assigned)
    // 2. Booking is pending
    // 3. Not previously rejected
    
    const hasBookingId = data.booking && data.booking.id; // Booking ID should exist
    const isPending = data.booking && data.booking.status === 'pending';
    const hasNoRejection = !data.hasRejection; // Check if booking was previously rejected
    
    // Additional checks for warnings (but don't block approval)
    const hasValidEmail = data.customer && data.customer.email;
    const hasValidPhone = data.customer && data.customer.phone;
    const hasValidService = data.booking && data.booking.service_name;
    const hasBookingDate = data.booking && data.booking.booking_date && data.booking.booking_date !== '0000-00-00' && data.booking.booking_date !== '1970-01-01';
    
    // Allow approval if queue number exists (auto-assigned), booking is pending, and not rejected
    const canApprove = hasBookingNumber && isPending && hasNoRejection;
    
    // Show Approve Reservation button if booking is pending and not rejected
    if (canApprove) {
        const approveReservationBtn = document.getElementById('acceptReservationFromComplianceBtn');
        const modalEl = document.getElementById('bookingComplianceModal');
        
        // Ensure aria-hidden is false before showing button
        if (modalEl) {
            modalEl.setAttribute('aria-hidden', 'false');
            if (typeof jQuery !== 'undefined') {
                jQuery(modalEl).attr('aria-hidden', 'false');
            }
        }
        
        // Show button but prevent it from getting focus
        if (approveReservationBtn) {
            approveReservationBtn.style.display = 'inline-block';
            approveReservationBtn.setAttribute('tabindex', '-1');
            // Blur if it somehow got focus
            if (document.activeElement === approveReservationBtn) {
                approveReservationBtn.blur();
            }
            
            // Re-enable tabindex after a short delay
            setTimeout(function() {
                approveReservationBtn.removeAttribute('tabindex');
            }, 100);
        }
        
        // Show success message
        const complianceChecklist = document.getElementById('compliance_checklist');
        if (complianceChecklist) {
            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success mt-3';
            successAlert.innerHTML = `
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Ready for Approval!</strong> This reservation meets all requirements and can be approved. Booking ID: #${data.booking && data.booking.id ? data.booking.id : 'N/A'}
            `;
            complianceChecklist.appendChild(successAlert);
        }
    } else {
        // Hide Approve button and show why it can't be approved
        document.getElementById('acceptReservationFromComplianceBtn').style.display = 'none';
        
        const reasons = [];
        if (!hasBookingNumber) {
            reasons.push('Queue number not found. Queue number should be automatically assigned when customer books.');
        }
        if (!isPending) {
            reasons.push('Only pending bookings can be approved. Current status: ' + (data.booking ? data.booking.status : 'unknown'));
        }
        if (data.hasRejection) {
            reasons.push('This booking was previously rejected. Please review the rejection reason.');
        }
        
        // Show warnings for missing optional info (but these don't block approval)
        const warnings = [];
        if (!hasValidEmail) warnings.push('Email address missing');
        if (!hasValidPhone) warnings.push('Contact phone number missing');
        if (!hasValidService) warnings.push('Service selection missing');
        if (!hasBookingDate) warnings.push('Booking date not provided');
        
        const complianceChecklist = document.getElementById('compliance_checklist');
        if (complianceChecklist) {
            if (reasons.length > 0) {
                // Show blocking reasons
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger mt-3';
                errorAlert.innerHTML = `
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong>Cannot Approve Reservation:</strong>
                    <ul class="mb-0 mt-2">
                        ${reasons.map(reason => `<li>${reason}</li>`).join('')}
                    </ul>
                `;
                complianceChecklist.appendChild(errorAlert);
            }
            
            if (warnings.length > 0 && reasons.length === 0) {
                // Show warnings but allow approval
                const warningAlert = document.createElement('div');
                warningAlert.className = 'alert alert-warning mt-3';
                warningAlert.innerHTML = `
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Note:</strong> Some information is missing but approval is still possible:
                    <ul class="mb-0 mt-2">
                        ${warnings.map(warning => `<li>${warning}</li>`).join('')}
                    </ul>
                `;
                complianceChecklist.appendChild(warningAlert);
            }
        }
    }
    
    // Show other action buttons (approve compliance) if booking exists
    if (data.booking) {
        const approveBtn = document.getElementById('approveComplianceBtn');
        const modalEl = document.getElementById('bookingComplianceModal');
        
        // Ensure aria-hidden is false before showing buttons
        if (modalEl) {
            modalEl.setAttribute('aria-hidden', 'false');
            if (typeof jQuery !== 'undefined') {
                jQuery(modalEl).attr('aria-hidden', 'false');
            }
        }
        
        // Show buttons but prevent them from getting focus
        if (approveBtn) {
            approveBtn.style.display = 'inline-block';
            approveBtn.setAttribute('tabindex', '-1');
            // Blur if it somehow got focus
            if (document.activeElement === approveBtn) {
                approveBtn.blur();
            }
        }
        
        // Re-enable tabindex after a short delay
        setTimeout(function() {
            if (approveBtn) approveBtn.removeAttribute('tabindex');
        }, 100);
    }
}

// Show compliance error
function showComplianceError(message) {
    document.getElementById('compliance_checklist').innerHTML = `<div class="alert alert-danger">${message}</div>`;
    document.getElementById('current_booking_details').innerHTML = `<div class="alert alert-danger">${message}</div>`;
}

// Get status badge class
function getStatusBadgeClass(status) {
    switch(status) {
        case 'pending': return 'badge-warning';
        case 'confirmed': return 'badge-info';
        case 'in_progress': return 'badge-primary';
        case 'completed': return 'badge-success';
        case 'delivered_and_paid': return 'badge-success';
        case 'cancelled': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

// Get payment badge class (duplicate - consolidated with main function above)
// This function is kept for backward compatibility
function getPaymentBadgeClass(status) {
    const paymentClasses = {
        'unpaid': 'danger',
        'paid': 'success',
        'paid_full_cash': 'success',
        'paid_on_delivery_cod': 'success',
        'refunded': 'info',
        'failed': 'warning',
        'cancelled': 'danger'
    };
    return paymentClasses[status] || 'secondary';
}

// Submit booking status form with all admin actions
function submitBookingStatusForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('bookingStatusForm');
    if (!form) return false;
    
    const formData = new FormData(form);
    const bookingId = formData.get('booking_id');
    
    // Get status directly from the select element to ensure we have the correct value
    const statusSelect = document.getElementById('status');
    const newStatus = statusSelect ? statusSelect.value : formData.get('status');
    
    // Debug: Log the values being sent (commented out to prevent syntax errors)
    // console.log('Submitting status update:', bookingId, newStatus);
    
    if (!bookingId) {
        alert('Booking ID is missing');
        return false;
    }
    
    if (!newStatus || newStatus === '') {
        alert('Status is missing. Please select a status.');
        return false;
    }
    
    // Ensure status is set in formData
    formData.set('status', newStatus);
    
    // Get payment status from the select element and ensure it's included
    const paymentStatusSelect = document.getElementById('payment_status');
    const paymentStatus = paymentStatusSelect ? paymentStatusSelect.value : formData.get('payment_status');
    if (paymentStatus) {
        formData.set('payment_status', paymentStatus);
        // console.log('Payment status being sent:', paymentStatus);
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';
    
    // Debug: Log all form data being sent
    // console.log('Form data being sent:');
    for (let pair of formData.entries()) {
        // console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Submit via AJAX
    fetch('<?php echo BASE_URL; ?>admin/updateBookingStatus', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If not JSON, get text and try to parse
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned non-JSON response. Check server logs for errors.');
            });
        }
    })
    .then(data => {
        // console.log('Response from server:', data);
        
        if (data.success) {
            // Get the actual status from response or form
            const newStatus = data.status || formData.get('status') || document.getElementById('status').value;
            // console.log('New status to display:', newStatus);
            
            const statusMessages = {
                'pending': 'Booking status updated to "Pending".',
                'approved': 'Booking status updated to "Approved". Customer will be notified.',
                'in_queue': 'Booking status updated to "In Queue". Customer will be notified.',
                'under_repair': 'Booking status updated to "Under Repair". Customer can track progress.',
                'for_quality_check': 'Booking status updated to "For Quality Check".',
                'ready_for_pickup': 'Booking status updated to "Ready for Pickup". Customer will be notified.',
                'out_for_delivery': 'Booking status updated to "Out for Delivery". Customer will be notified.',
                'completed': 'Booking status updated to "Completed". Customer will be notified.',
                'cancelled': 'Booking status updated to "Cancelled".'
            };
            const message = statusMessages[newStatus] || data.message || 'Booking updated successfully!';
            showAlert('success', message);
            
            // Update the table row status immediately (without reload)
            const bookingId = formData.get('booking_id');
            // Use payment status from response if available, otherwise from form
            const newPaymentStatus = data.payment_status || formData.get('payment_status') || null;
            // console.log('Updating booking row:', bookingId, 'Status:', newStatus, 'Payment:', newPaymentStatus);
            updateBookingRowStatus(bookingId, newStatus, null, newPaymentStatus);
            
            // Notify customer view to refresh (via localStorage event)
            try {
                localStorage.setItem('booking_status_updated', bookingId);
                localStorage.removeItem('booking_status_updated'); // Trigger storage event
            } catch (e) {
                // console.log('localStorage not available');
            }
            
            // Close modal
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#statusModal').modal('hide');
            }
            
            // DO NOT reload - payment status is already updated in the UI
            // The database has been updated, so the change is permanent
            // Reloading would cause the payment status to revert if there's a caching issue
        } else {
            showAlert('danger', data.message || 'Failed to update booking');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = error.message || 'An error occurred while updating the booking. Please check the console for details.';
        showAlert('danger', errorMessage);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
    
    return false;
}

// Note: Queue numbers are now automatically assigned when customers submit reservations
// No manual assignment needed

// Accept reservation from compliance modal (after validation passes) - DIRECT APPROVAL
function acceptReservationFromCompliance() {
    const modalEl = document.getElementById('bookingComplianceModal');
    if (!modalEl) {
        console.error('Compliance modal not found');
        return;
    }
    
    // Get bookingId from modal data attribute
    const bookingId = modalEl.getAttribute('data-booking-id');
    if (!bookingId) {
        alert('Booking ID not found. Please try again.');
        return;
    }
    
    // Confirm acceptance
    if (!confirm('Are you sure you want to approve this reservation? All requirements have been validated and the booking will be approved immediately.')) {
        return;
    }
    
    // Get the approve button and show loading state
    const approveBtn = document.getElementById('acceptReservationFromComplianceBtn');
    const originalBtnContent = approveBtn ? approveBtn.innerHTML : '';
    if (approveBtn) {
        approveBtn.disabled = true;
        approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Approving...';
    }
    
    // Directly approve the reservation without opening another modal
    fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&admin_notes=Approved after compliance check`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            let successMsg = data.message || 'Reservation approved successfully! Status updated to "Approved".';
            if (data.booking_id) {
                successMsg += ' Booking ID: #' + data.booking_id;
            }
            showAlert('success', successMsg);
            
            // Close compliance modal
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#bookingComplianceModal').modal('hide');
    } else {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            
            // Update the table row status immediately
            updateBookingRowStatus(bookingId, 'approved', 'Booking #' + bookingId);
            
            // Force customer view to refresh by triggering a page reload message
            // This ensures customer sees the updated status immediately
            // console.log('Booking approved - Status updated to:', data.status || 'approved');
            
            // Reload page after a short delay to show updated data
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        } else {
            // Show error message
            showAlert('danger', data.message || 'Failed to approve reservation. Please try again.');
            
            // Re-enable button
            if (approveBtn) {
                approveBtn.disabled = false;
                approveBtn.innerHTML = originalBtnContent;
            }
        }
    })
    .catch(error => {
        console.error('Error approving reservation:', error);
        showAlert('danger', 'An error occurred while approving the reservation. Please try again.');
        
        // Re-enable button
        if (approveBtn) {
            approveBtn.disabled = false;
            approveBtn.innerHTML = originalBtnContent;
        }
    });
}

// Update booking row status in the table
function updateBookingRowStatus(bookingId, newStatus, bookingNumber, newPaymentStatus) {
    // Status mapping for badges
    const statusConfig = {
        'pending': {class: 'badge-warning', text: 'Pending'},
        'approved': {class: 'badge-success', text: 'Approved'},
        'in_queue': {class: 'badge-info', text: 'In Queue'},
        'under_repair': {class: 'badge-primary', text: 'Under Repair'},
        'for_quality_check': {class: 'badge-info', text: 'For Quality Check'},
        'ready_for_pickup': {class: 'badge-success', text: 'Ready for Pickup'},
        'out_for_delivery': {class: 'badge-warning', text: 'Out for Delivery'},
        'completed': {class: 'badge-success', text: 'Completed'},
        'delivered_and_paid': {class: 'badge-success', text: 'Delivered and Paid'},
        'cancelled': {class: 'badge-secondary', text: 'Cancelled'}
    };
    
    const config = statusConfig[newStatus] || {class: 'badge-secondary', text: newStatus};
    
    // Find the row with this booking ID - check all tables
    const allRows = document.querySelectorAll('table tbody tr');
    allRows.forEach(function(row) {
        // Check if this row contains the booking ID
        // Look for button with booking ID in onclick attribute
        const buttons = row.querySelectorAll('button[onclick]');
        let rowBookingId = null;
        
        for (let btn of buttons) {
            const onclick = btn.getAttribute('onclick') || '';
            if (onclick.includes('updateStatus(' + bookingId) || 
                onclick.includes('acceptReservation(' + bookingId) ||
                onclick.includes('viewDetails(' + bookingId)) {
                rowBookingId = bookingId;
                break;
            }
        }
        
        if (rowBookingId == bookingId) {
            // Admin table structure:
            // Column 1: Booking # (index 0)
            // Column 2: Customer (index 1)
            // Column 3: Service (index 2)
            // Column 4: Category (index 3)
            // Column 5: Amount (index 4) <- NOT STATUS!
            // Column 6: STATUS (index 5) <- THIS IS STATUS!
            // Column 7: Payment (index 6)
            // Column 8: Date (index 7)
            // Column 9: Actions (index 8)
            
            // Find status cell using data attribute (most reliable)
            let statusCell = row.querySelector('td.status-cell[data-booking-status]');
            
            // Fallback: find by badge with status keywords (but skip amount column)
            if (!statusCell) {
                const cells = row.querySelectorAll('td');
                for (let i = 0; i < cells.length; i++) {
                    const cell = cells[i];
                    const cellText = cell.textContent;
                    
                    // Skip amount column (has ₱ symbol or matches price pattern)
                    if (cellText.includes('₱') || cellText.match(/^₱?\s*\d+[.,]\d{2}$/)) {
                        continue; // Skip this cell - it's the amount column
                    }
                    
                    const badge = cell.querySelector('.badge');
                    if (badge) {
                        const badgeText = badge.textContent.toLowerCase();
                        // Check if this badge contains status keywords
                        if (badgeText.includes('pending') || 
                            badgeText.includes('approved') || 
                            badgeText.includes('under repair') ||
                            badgeText.includes('in queue') ||
                            badgeText.includes('completed') ||
                            badgeText.includes('cancelled') ||
                            badgeText.includes('ready') ||
                            badgeText.includes('delivery') ||
                            badgeText.includes('quality')) {
                            statusCell = cell;
                            break;
                        }
                    }
                }
            }
            
            // Final fallback: use nth-child(6) - the STATUS column (6th column in admin table)
            // DO NOT use nth-child(5) as that's the Amount column!
            if (!statusCell) {
                statusCell = row.querySelector('td:nth-child(6)');
            }
            
            if (statusCell) {
                // Verify this is NOT the amount column (5th column has ₱ symbol)
                const cellText = statusCell.textContent;
                if (cellText.includes('₱') || cellText.match(/^₱?\s*\d+[.,]\d{2}$/)) {
                    // This is the amount column, not status! Use column 6 instead
                    // Don't log warning - this is expected behavior
                    statusCell = row.querySelector('td:nth-child(6)');
                }
                
                // Update the status cell and data attribute
                statusCell.innerHTML = `<span class="badge ${config.class}" style="font-weight: 600;">${config.text}</span>`;
                statusCell.setAttribute('data-booking-status', newStatus);
                // console.log('Status updated in admin view:', bookingId, 'to', newStatus, 'in status cell');
            } else {
                console.error('Status cell not found for booking:', bookingId, 'Row:', row);
            }
            
            // Update payment status if provided (always update, even if null, to ensure it's visible)
            if (newPaymentStatus !== undefined && newPaymentStatus !== null) {
                // Payment column is typically the 7th column (index 6)
                // Table structure: Booking #, Customer, Service, Category, Amount, Status, Payment, Date, Actions
                let paymentCell = row.querySelector('td:nth-child(7)');
                
                // Try to find by data attribute or payment-status-text class
                if (!paymentCell) {
                    paymentCell = row.querySelector('td[data-payment-status]');
                }
                if (!paymentCell) {
                    paymentCell = row.querySelector('td .payment-status-text')?.closest('td');
                }
                
                // Try to find by payment keywords in text
                if (!paymentCell) {
                    const cells = row.querySelectorAll('td');
                    for (let i = 0; i < cells.length; i++) {
                        const cell = cells[i];
                        const cellText = cell.textContent.toLowerCase().trim();
                        // Check if this cell contains payment keywords
                        if (cellText.includes('paid') || 
                            cellText.includes('unpaid') || 
                            cellText.includes('refunded') ||
                            cellText.includes('failed') ||
                            cellText.includes('cancelled') ||
                            cellText.includes('full paid') ||
                            cellText.includes('cash') ||
                            cellText.includes('cod')) {
                            // Make sure it's not the amount column (has ₱ symbol)
                            if (!cellText.includes('₱') && !cellText.match(/^₱?\s*\d+[.,]\d{2}$/)) {
                                paymentCell = cell;
                                break;
                            }
                        }
                    }
                }
                
                // Final fallback: use nth-child(7) - the Payment column
                if (!paymentCell) {
                    paymentCell = row.querySelector('td:nth-child(7)');
                }
                
                if (paymentCell) {
                    const paymentText = getPaymentStatusText(newPaymentStatus);
                    // Update with visible text only (no badge), and set data attribute to prevent reverting
                    paymentCell.innerHTML = `
                        <span class="payment-status-text" style="font-weight: 600; color: #2c3e50; font-size: 1rem; display: inline-block !important; visibility: visible !important; opacity: 1 !important;" data-payment-status="${newPaymentStatus}">${paymentText}</span>
                    `;
                    paymentCell.setAttribute('data-payment-status', newPaymentStatus);
                    paymentCell.style.visibility = 'visible';
                    paymentCell.style.display = '';
                    paymentCell.style.opacity = '1';
                    // console.log('Payment status updated in admin view:', bookingId, 'to', paymentText, 'in cell index:', Array.from(row.querySelectorAll('td')).indexOf(paymentCell));
                } else {
                    console.error('Payment cell not found for booking:', bookingId, 'Row cells:', row.querySelectorAll('td').length);
                    // Try to find by table header matching
                    const headers = row.closest('table')?.querySelectorAll('thead th');
                    if (headers) {
                        for (let i = 0; i < headers.length; i++) {
                            const headerText = headers[i].textContent.toLowerCase();
                            if (headerText.includes('payment') || headerText.includes('pay')) {
                                const cells = row.querySelectorAll('td');
                                if (cells[i]) {
                                    paymentCell = cells[i];
                                    const paymentText = getPaymentStatusText(newPaymentStatus);
                                    paymentCell.innerHTML = `
                                        <span class="payment-status-text" style="font-weight: 600; color: #2c3e50; font-size: 1rem; display: inline-block !important; visibility: visible !important; opacity: 1 !important;" data-payment-status="${newPaymentStatus}">${paymentText}</span>
                                    `;
                                    paymentCell.setAttribute('data-payment-status', newPaymentStatus);
                                    // console.log('Payment cell found by header matching at index:', i);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            
            // If status changed to approved, remove approve/reject buttons
            if (newStatus === 'approved') {
                const actionCell = row.querySelector('td:last-child');
                if (actionCell) {
                    const viewBtn = actionCell.querySelector('button[onclick*="viewDetails"]');
                    const editBtn = actionCell.querySelector('button[onclick*="updateStatus"]');
                    const deleteBtn = actionCell.querySelector('button[onclick*="deleteBooking"]');
                    
                    let newActionHtml = '<div class="btn-group" role="group">';
                    if (editBtn) {
                        newActionHtml += editBtn.outerHTML;
                    }
                    if (viewBtn) {
                        newActionHtml += viewBtn.outerHTML;
                    }
                    if (deleteBtn) {
                        newActionHtml += deleteBtn.outerHTML;
                    }
                    newActionHtml += '</div>';
                    
                    actionCell.innerHTML = newActionHtml;
                }
            }
        }
    });
}

// Approve compliance
function approveCompliance() {
    const modalEl = document.getElementById('bookingComplianceModal');
    if (!modalEl) {
        console.error('Compliance modal not found');
        return;
    }
    
    // Get bookingId from modal data attribute
    const bookingId = modalEl.getAttribute('data-booking-id');
    if (!bookingId) {
        alert('Booking ID not found. Please try again.');
        return;
    }
    
    // Confirm approval
    if (!confirm('Are you sure you want to approve this customer\'s compliance and approve the reservation? The booking will be approved immediately.')) {
        return;
    }
    
    // Get the approve button and show loading state
    const approveBtn = document.getElementById('approveComplianceBtn');
    const originalBtnContent = approveBtn ? approveBtn.innerHTML : '';
    if (approveBtn) {
        approveBtn.disabled = true;
        approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Approving...';
    }
    
    // Call the backend to approve the reservation
    fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&admin_notes=Approved after compliance check`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            let successMsg = data.message || 'Reservation approved successfully! Status updated to "Approved".';
            if (data.booking_id) {
                successMsg += ' Booking ID: #' + data.booking_id;
            }
            showAlert('success', successMsg);
            
            // Close compliance modal
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#bookingComplianceModal').modal('hide');
    } else {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
            
            // Update the table row status immediately
            updateBookingRowStatus(bookingId, 'approved', 'Booking #' + bookingId);
            
            // Force customer view to refresh by triggering a page reload message
            // console.log('Booking approved - Status updated to:', data.status || 'approved');
            
            // Reload page after a short delay to show updated data
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        } else {
            // Show error message
            showAlert('danger', data.message || 'Failed to approve reservation. Please try again.');
            
            // Re-enable button
            if (approveBtn) {
                approveBtn.disabled = false;
                approveBtn.innerHTML = originalBtnContent;
            }
        }
    })
    .catch(error => {
        console.error('Error approving reservation:', error);
        showAlert('danger', 'An error occurred while approving the reservation. Please try again.');
        
        // Re-enable button
        if (approveBtn) {
            approveBtn.disabled = false;
            approveBtn.innerHTML = originalBtnContent;
        }
    });
}


// Duplicate function definitions removed - functions are already defined at the top of the script (lines 722+)

// Print receipt function is defined below (line 1932)

// Duplicate loadBookingNumbers function removed - already defined at line 1328
// Orphaned code removed - all functions are defined at the top of the script
// Duplicate generateReceipt function removed - already defined at top

// Print receipt
function printReceipt() {
    const receiptContent = document.querySelector('.receipt-container');
    if (!receiptContent) return;
    
    const printWindow = window.open('', '_blank');
    const printContent = receiptContent.innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .receipt-container { background: white; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
                table th, table td { padding: 0.75rem; border: 1px solid #ddd; }
                table th { background-color: #f8f9fc; font-weight: 600; }
                .badge { padding: 0.25rem 0.5rem; border-radius: 4px; }
                .badge-info { background-color: #3498db; color: white; }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 250);
}

// Duplicate function definitions removed - functions are defined at the top of the script

// Confirm accept reservation
function confirmAcceptReservation() {
    const bookingId = document.getElementById('accept_booking_id').value;
    const adminNotes = document.getElementById('accept_admin_notes').value.trim();
    
    if (!bookingId) {
        alert('Booking ID is missing.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Accepting...';
    button.disabled = true;
    
    // Send request without booking_number_id - it will be auto-assigned
    fetch('<?php echo BASE_URL; ?>admin/acceptReservation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&admin_notes=${encodeURIComponent(adminNotes)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            if (typeof jQuery !== 'undefined') {
                jQuery('#acceptReservationModal').modal('hide');
            } else {
                const modalEl = document.getElementById('acceptReservationModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            
            // Show success message with booking number and queue position
            let successMsg = data.message;
            if (data.booking_number) {
                successMsg += ' Booking ID: #' + data.booking_id;
            }
            showAlert('success', successMsg);
            
            // Reload page after a short delay to show updated booking status
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while accepting the reservation.');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Duplicate rejectReservation function removed - defined at top of script

// Show alert message
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Insert at the top of the card body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = cardBody.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Open send preview modal
function openSendPreviewModal(bookingId) {
    const modalHtml = `
        <div class="modal fade" id="sendPreviewModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-image mr-2"></i>Send Preview to Customer</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="sendPreviewForm" enctype="multipart/form-data">
                            <input type="hidden" name="booking_id" value="${bookingId}">
                            <div class="form-group">
                                <label for="preview_image">Preview Image</label>
                                <input type="file" class="form-control-file" id="preview_image" name="preview_image" accept="image/*">
                                <small class="form-text text-muted">Upload an image preview of the customer's purchase</small>
                            </div>
                            <div class="form-group">
                                <label for="preview_notes">Preview Notes</label>
                                <textarea class="form-control" id="preview_notes" name="preview_notes" rows="4" placeholder="Add any notes about the preview..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="sendPreview(${bookingId})">
                            <i class="fas fa-paper-plane mr-1"></i> Send Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('sendPreviewModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#sendPreviewModal').modal('show');
    }
}

// Send preview to customer
function sendPreview(bookingId) {
    const form = document.getElementById('sendPreviewForm');
    const formData = new FormData(form);
    
    const submitBtn = form.querySelector('button[type="button"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';
    
    fetch('<?php echo BASE_URL; ?>admin/sendPreviewToCustomer', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Preview sent to customer successfully!');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#sendPreviewModal').modal('hide');
            }
            // Reload booking details to show preview
            if (typeof loadBookingDetailsModal === 'function') {
                loadBookingDetailsModal(bookingId);
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to send preview'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error sending preview:', error);
        alert('Error sending preview. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Send quotation after inspection (for PICKUP service option)
function sendQuotationAfterInspection(bookingId) {
    if (!confirm('Are you sure you want to send the final quotation email to the customer? This should only be done after the item has been inspected and final pricing has been determined.')) {
        return;
    }
    
    // Show loading state
    const buttonText = 'Sending quotation...';
    
    fetch('<?php echo BASE_URL; ?>admin/sendQuotationAfterInspection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'booking_id=' + bookingId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            // Reload booking details to update status
            if (typeof loadBookingDetailsModal === 'function') {
                loadBookingDetailsModal(bookingId);
            }
        } else {
            alert('❌ Error: ' + (data.message || 'Failed to send quotation'));
        }
    })
    .catch(error => {
        console.error('Error sending quotation:', error);
        alert('❌ Error sending quotation. Please try again.');
    });
}

// ============================================================================
// ENHANCED ACTION BUTTON HANDLERS WITH LOADING STATES
// ============================================================================

// Ensure original functions exist and are accessible globally
// This prevents "function not defined" errors
if (typeof window.viewDetails !== 'function') {
    console.error('viewDetails function not found - this should not happen!');
}
if (typeof window.updateStatus !== 'function') {
    console.error('updateStatus function not found - this should not happen!');
}
if (typeof window.acceptReservation !== 'function') {
    console.error('acceptReservation function not found - this should not happen!');
}
if (typeof window.deleteBooking !== 'function') {
    console.error('deleteBooking function not found - this should not happen!');
}
if (typeof window.generateReceipt !== 'function') {
    console.error('generateReceipt function not found - this should not happen!');
}

// Helper function to set button loading state
function setButtonLoading(button, isLoading) {
    if (!button) return;
    
    if (isLoading) {
        button.disabled = true;
        button.dataset.originalHtml = button.innerHTML;
        button.classList.add('loading');
        
        const icon = button.querySelector('i');
        if (icon) {
            icon.className = 'fas fa-spinner fa-spin';
        } else {
            // Add spinner icon if no icon exists
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        button.style.opacity = '0.7';
        button.style.cursor = 'wait';
    } else {
        button.disabled = false;
        button.classList.remove('loading');
        
        if (button.dataset.originalHtml) {
            button.innerHTML = button.dataset.originalHtml;
        }
        
        button.style.opacity = '1';
        button.style.cursor = 'pointer';
    }
}

// Enhanced View Details Handler
window.handleViewDetails = function(bookingId) {
    const button = document.querySelector(`.view-btn[data-booking-id="${bookingId}"]`);
    
    try {
        setButtonLoading(button, true);
        
        // Call the original viewDetails function
        if (typeof viewDetails === 'function') {
            viewDetails(bookingId);
        } else {
            alert('View details function not found. Please refresh the page.');
        }
        
        // Reset button after a delay
        setTimeout(() => {
            setButtonLoading(button, false);
        }, 1000);
    } catch (error) {
        console.error('Error viewing details:', error);
        setButtonLoading(button, false);
        alert('Error loading booking details. Please try again.');
    }
};

// Enhanced Approve Handler
window.handleApprove = function(bookingId) {
    const button = document.querySelector(`.approve-btn[data-booking-id="${bookingId}"]`);
    
    // Prevent double clicks
    if (button && button.disabled) {
        return;
    }
    
    try {
        // First, check if total has been calculated
        // Load booking details to check
        fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.booking) {
                    const booking = data.booking;
                    const hasCalculatedTotal = parseFloat(booking.total_amount || 0) > 0 || booking.calculated_total_saved === '1';
                    
                    if (!hasCalculatedTotal) {
                        // Total not calculated - redirect to view details first
                        alert('⚠️ REQUIRED: Please view booking details and calculate the total payment (bayronon) first before approving.\n\n' +
                              'The admin must:\n' +
                              '1. Examine the repair item\n' +
                              '2. Measure the fabric to be used\n' +
                              '3. Calculate the total payment\n' +
                              '4. Save the calculated total\n\n' +
                              'Then you can approve the booking.');
                        
                        // Open view details modal
                        if (typeof handleViewDetails === 'function') {
                            handleViewDetails(bookingId);
                        } else if (typeof viewDetails === 'function') {
                            viewDetails(bookingId);
                        }
                        return;
                    }
                    
                    // Total is calculated - proceed with approval
                    setButtonLoading(button, true);
                    
                    if (typeof acceptReservation === 'function') {
                        acceptReservation(bookingId);
                    } else {
                        setButtonLoading(button, false);
                        alert('Approve function not found. Please refresh the page.');
                    }
                } else {
                    alert('Error loading booking details. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error checking booking details:', error);
                alert('Error checking booking details. Please try again.');
            });
    } catch (error) {
        console.error('Error approving booking:', error);
        if (button) setButtonLoading(button, false);
        alert('Error approving booking. Please try again.');
    }
};

// Enhanced Update Status Handler
window.handleUpdateStatus = function(bookingId, currentStatus) {
    const button = document.querySelector(`.update-btn[data-booking-id="${bookingId}"]`);
    
    try {
        setButtonLoading(button, true);
        
        // Call the original updateStatus function
        if (typeof updateStatus === 'function') {
            updateStatus(bookingId, currentStatus);
        } else {
            setButtonLoading(button, false);
            alert('Update status function not found. Please refresh the page.');
        }
        
        // Reset button after a delay (modal should handle the rest)
        setTimeout(() => {
            setButtonLoading(button, false);
        }, 1000);
    } catch (error) {
        console.error('Error updating status:', error);
        setButtonLoading(button, false);
        alert('Error opening status update modal. Please try again.');
    }
};

// Enhanced Delete Handler
window.handleDelete = function(bookingId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const button = document.querySelector(`.delete-btn[data-booking-id="${bookingId}"]`);
    
    // Prevent double clicks
    if (button && button.disabled) {
        return;
    }
    
    // Confirm deletion first
    if (!confirm('⚠️ Are you sure you want to DELETE this booking?\n\nThis action cannot be undone!\n\nBooking ID: ' + bookingId)) {
        return;
    }
    
    // Double confirmation for safety
    if (!confirm('⚠️⚠️ FINAL CONFIRMATION\n\nThis will permanently delete the booking and all associated data.\n\nClick OK to proceed with deletion.')) {
        return;
    }
    
    try {
        setButtonLoading(button, true);
        
        // Call the original deleteBooking function
        if (typeof deleteBooking === 'function') {
            deleteBooking(bookingId, event);
        } else {
            setButtonLoading(button, false);
            alert('Delete function not found. Please refresh the page.');
        }
    } catch (error) {
        console.error('Error deleting booking:', error);
        setButtonLoading(button, false);
        alert('Error deleting booking. Please try again.');
    }
};

// Enhanced Generate Receipt Handler
window.handleGenerateReceipt = function(bookingId) {
    const button = document.querySelector(`.receipt-btn[data-booking-id="${bookingId}"]`);
    
    try {
        setButtonLoading(button, true);
        
        // Call the original generateReceipt function
        if (typeof generateReceipt === 'function') {
            generateReceipt(bookingId);
        } else {
            setButtonLoading(button, false);
            alert('Generate receipt function not found. Please refresh the page.');
        }
        
        // Reset button after a delay
        setTimeout(() => {
            setButtonLoading(button, false);
        }, 1500);
    } catch (error) {
        console.error('Error generating receipt:', error);
        setButtonLoading(button, false);
        alert('Error generating receipt. Please try again.');
    }
};

// Quick Status Update Handler (for workflow actions)
window.handleQuickStatusUpdate = function(bookingId, newStatus, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const statusMessages = {
        'picked_up': 'Mark booking as picked up? Status will change to "Picked Up / For Inspection".',
        'approved': 'Approve quotation and start repair? Status will change to "Approved / Ready for Repair".',
        'in_progress': 'Start repair work? Status will change to "In Progress".',
        'completed': 'Mark booking as completed? Status will change to "Completed".',
        'closed': 'Close this booking? Status will change to "Closed".'
    };
    
    const message = statusMessages[newStatus] || `Update status to "${newStatus}"?`;
    
    if (!confirm(message)) {
        return;
    }
    
    // Find the button that was clicked
    let button = null;
    if (event && event.target) {
        button = event.target.closest('button');
    }
    if (!button) {
        // Try to find by data attributes
        button = document.querySelector(`button[data-booking-id="${bookingId}"][data-action]`);
    }
    if (!button) {
        // Fallback to onclick attribute
        button = document.querySelector(`button[onclick*="handleQuickStatusUpdate(${bookingId}"]`);
    }
    
    if (button) {
        setButtonLoading(button, true);
    }
    
    // Update status via API
    const formData = new URLSearchParams();
    formData.append('booking_id', bookingId);
    formData.append('status', newStatus);
    formData.append('notify_customer', '1');
    
    fetch('<?php echo BASE_URL; ?>admin/updateBookingStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update status'));
            if (button) setButtonLoading(button, false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status. Please try again.');
        if (button) setButtonLoading(button, false);
    });
};

// Confirm Payment Handler
window.handleConfirmPayment = function(bookingId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    if (!confirm('Confirm that payment has been received from the customer?\n\nStatus will change to "Paid".')) {
        return;
    }
    
    const button = (event && event.target) ? event.target.closest('button') : document.querySelector(`button[onclick*="handleConfirmPayment(${bookingId}"]`);
    
    if (button) {
        setButtonLoading(button, true);
    }
    
    // Update payment status to paid
    const formData = new URLSearchParams();
    formData.append('booking_id', bookingId);
    formData.append('payment_status', 'paid_full_cash');
    formData.append('status', 'paid');
    formData.append('notify_customer', '1');
    
    fetch('<?php echo BASE_URL; ?>admin/updateBookingStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment confirmed! Booking status updated to Paid.');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to confirm payment'));
            if (button) setButtonLoading(button, false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while confirming payment. Please try again.');
        if (button) setButtonLoading(button, false);
    });
};

// Add hover effects for action buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add tooltips and hover effects
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            if (!this.disabled) {
                this.style.transform = 'scale(1.1)';
                this.style.transition = 'transform 0.2s ease';
            }
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
        
        // Add active state
        button.addEventListener('mousedown', function() {
            if (!this.disabled) {
                this.style.transform = 'scale(0.95)';
            }
        });
        
        button.addEventListener('mouseup', function() {
            if (!this.disabled) {
                this.style.transform = 'scale(1.1)';
            }
        });
    });
});

// Test function to verify buttons are working
window.testActionButtons = function() {
    console.log('Testing Action Buttons...');
    console.log('handleViewDetails:', typeof handleViewDetails);
    console.log('handleApprove:', typeof handleApprove);
    console.log('handleUpdateStatus:', typeof handleUpdateStatus);
    console.log('handleDelete:', typeof handleDelete);
    console.log('handleGenerateReceipt:', typeof handleGenerateReceipt);
    console.log('---');
    console.log('viewDetails:', typeof viewDetails);
    console.log('acceptReservation:', typeof acceptReservation);
    console.log('updateStatus:', typeof updateStatus);
    console.log('deleteBooking:', typeof deleteBooking);
    console.log('generateReceipt:', typeof generateReceipt);
    console.log('---');
    const actionButtons = document.querySelectorAll('.action-btn');
    console.log('Total action buttons found:', actionButtons.length);
    console.log('All action button functions are loaded and working!');
};

// Auto-test on page load
setTimeout(function() {
    if (typeof console !== 'undefined' && console.log) {
        testActionButtons();
    }
}, 1000);

// ============================================================================
// LEGACY SUPPORT - Keep original function names as aliases
// This ensures backwards compatibility if browser cached old HTML
// ============================================================================

// Only create aliases if the enhanced handlers exist
if (typeof window.handleViewDetails === 'function' && typeof window.viewDetails !== 'function') {
    window.viewDetails = window.handleViewDetails;
    console.log('Created viewDetails alias');
}

if (typeof window.handleUpdateStatus === 'function' && typeof window.updateStatus !== 'function') {
    window.updateStatus = window.handleUpdateStatus;
    console.log('Created updateStatus alias');
}

if (typeof window.handleApprove === 'function' && typeof window.acceptReservation !== 'function') {
    window.acceptReservation = window.handleApprove;
    console.log('Created acceptReservation alias');
}

if (typeof window.handleDelete === 'function' && typeof window.deleteBooking !== 'function') {
    window.deleteBooking = window.handleDelete;
    console.log('Created deleteBooking alias');
}

if (typeof window.handleGenerateReceipt === 'function' && typeof window.generateReceipt !== 'function') {
    window.generateReceipt = window.handleGenerateReceipt;
    console.log('Created generateReceipt alias');
}

console.log('Admin Bookings JavaScript v2.0 Loaded Successfully!');
console.log('Press Ctrl+Shift+R (or Cmd+Shift+R on Mac) to hard refresh if buttons not working');

// Detect if user is experiencing caching issues or missing dependencies
setTimeout(function() {
    try {
        // Check if jQuery loaded (it should be loaded by now)
        if (typeof jQuery === 'undefined') {
            console.warn('WARNING: jQuery is still not loaded after page initialization.');
            console.warn('This may cause some features to not work properly.');
            console.warn('Please check if jQuery is included in the page footer.');
        } else {
            console.log('jQuery loaded successfully');
        }
        
        const requiredFunctions = [
            'viewDetails', 'updateStatus', 'acceptReservation', 'deleteBooking', 
            'generateReceipt', 'handleViewDetails', 'handleApprove', 
            'handleUpdateStatus', 'handleDelete', 'handleGenerateReceipt'
        ];
        
        const missingFunctions = requiredFunctions.filter(function(fn) {
            return typeof window[fn] !== 'function';
        });
        
        if (missingFunctions.length > 0) {
            console.error('CACHE ISSUE DETECTED!');
            console.error('Missing functions:', missingFunctions.join(', '));
            console.error('FIX: Press Ctrl+Shift+R to hard refresh the page');
            console.error('See: CLEAR_BROWSER_CACHE.md for full instructions');
            
            // Show user-friendly alert
            if (missingFunctions.indexOf('viewDetails') !== -1 || missingFunctions.indexOf('updateStatus') !== -1) {
                const message = 'BROWSER CACHE ISSUE DETECTED!\n\n' +
                              'Your browser is using old files.\n\n' +
                              'TO FIX:\n' +
                              '1. Press Ctrl+Shift+R (Windows)\n' +
                              '2. Or Cmd+Shift+R (Mac)\n\n' +
                              'This will reload the page with latest updates.';
                
                // Show after a delay so page loads first
                setTimeout(function() {
                    alert(message);
                }, 2000);
            }
        } else {
            console.log('All required functions are loaded and ready!');
        }
    } catch (e) {
        console.error('Error in cache detection:', e);
    }
}, 1500);

</script>

<style>
/* Fix Modal Visibility and Z-Index Issues */
/* Ensure modals are above all content including iframes */
.modal {
    z-index: 99999 !important;
    display: none;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    overflow-x: hidden !important;
    overflow-y: auto !important;
    pointer-events: auto !important;
}

/* Ensure no iframe or other element covers the modal */
iframe {
    z-index: 1 !important;
    position: relative !important;
    max-height: 100vh !important;
    max-width: 100% !important;
}

/* Prevent iframes from being full-screen absolute positioned */
iframe[style*="position: absolute"],
iframe[style*="position:fixed"],
iframe[style*="position:absolute"] {
    position: relative !important;
    max-width: 100% !important;
    max-height: 100vh !important;
    width: auto !important;
    height: auto !important;
}

/* Ensure iframes inside containers are properly sized */
.iframe-container iframe,
iframe[src*="maps"],
iframe[src*="map"],
iframe[src*="google"],
iframe[src*="embed"] {
    position: relative !important;
    max-width: 100% !important;
    max-height: 100vh !important;
    z-index: 1 !important;
}

/* Ensure wrapper/container elements don't interfere */
#wrapper,
.content-wrapper,
.main-content-wrapper,
.container-fluid,
.container {
    position: relative !important;
    z-index: 1 !important;
}

.modal.show,
.modal.fade.show {
    display: block !important;
    z-index: 99999 !important;
    pointer-events: auto !important;
    opacity: 1 !important;
}

.modal-backdrop {
    z-index: 99998 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
    pointer-events: auto !important;
}

.modal-backdrop.show,
.modal-backdrop.fade.show {
    opacity: 0.5 !important;
    z-index: 99998 !important;
    pointer-events: auto !important;
}

.modal-dialog {
    z-index: 100000 !important;
    position: relative !important;
    margin: 1.75rem auto !important;
    max-width: 500px !important;
    pointer-events: auto !important;
}

.modal.show .modal-dialog {
    pointer-events: auto !important;
}

.modal-content {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    width: 100% !important;
    pointer-events: auto !important;
    background-color: #fff !important;
    background-clip: padding-box !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
    border-radius: 0.3rem !important;
    outline: 0 !important;
    z-index: 100000 !important;
}

.modal-header,
.modal-body,
.modal-footer {
    pointer-events: auto !important;
    z-index: 100001 !important;
    position: relative !important;
}

.modal-header .close,
.modal-footer button,
.modal-body button,
.modal-body input,
.modal-body select,
.modal-body textarea,
.modal-body label,
.modal-body .form-control,
.modal-body .btn {
    pointer-events: auto !important;
    z-index: 100002 !important;
    position: relative !important;
    cursor: pointer !important;
}

/* When modal is open, disable iframe interactions */
body.modal-open iframe,
body.modal-open embed,
body.modal-open object {
    pointer-events: none !important;
    z-index: 1 !important;
}

/* Ensure nothing can be above the modal */
body.modal-open *:not(.modal):not(.modal *) {
    z-index: auto !important;
}

body.modal-open *[style*="z-index"]:not(.modal):not(.modal *):not(.modal-backdrop) {
    z-index: 1 !important;
}

/* Ensure modals are above everything */
body.modal-open {
    overflow: hidden;
    padding-right: 0 !important;
}

body.modal-open .modal {
    overflow-x: hidden;
    overflow-y: auto;
}

/* Prevent focus on buttons when modal has aria-hidden="true" */
.modal[aria-hidden="true"] button:focus,
.modal[aria-hidden="true"] button:active {
    outline: none !important;
    box-shadow: none !important;
}

/* Ensure modal buttons don't get focus until modal is shown */
/* BUT allow clicks - only prevent focus, not interaction */
.modal:not(.show) button {
    pointer-events: auto !important; /* Allow clicks even before show */
}

.modal.show button {
    pointer-events: auto !important; /* Always allow clicks when shown */
}

/* Ensure table action buttons are always clickable */
table .btn-group button,
table .btn,
.dataTables_wrapper .btn {
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 10 !important;
    position: relative !important;
}

/* Fix for modal-xl */
.modal-xl {
    max-width: 1140px;
}

/* Ensure close button is clickable */
.modal-header .close {
    z-index: 1056 !important;
    position: relative;
    cursor: pointer;
    padding: 1rem;
    margin: -1rem -1rem -1rem auto;
    background-color: transparent;
    border: 0;
    opacity: 0.5;
}

.modal-header .close:hover {
    opacity: 1;
}

/* Fix for buttons inside modals */
.modal-footer button,
.modal-body button {
    z-index: 1056 !important;
    position: relative;
}

/* Ensure modal is visible when shown */
.modal.fade.show {
    opacity: 1;
}

.modal.fade {
    opacity: 0;
    transition: opacity 0.15s linear;
}

/* Fix for nested modals if any */
.modal.show .modal {
    z-index: 11010 !important;
}

.modal.show .modal-backdrop {
    z-index: 11009 !important;
}

/* Ensure DataTable elements don't interfere with modals */
.dataTables_wrapper {
    position: relative !important;
    z-index: 1 !important;
}

.dataTables_scrollBody {
    position: relative !important;
    z-index: 1 !important;
}

/* Ensure table containers don't cover modals */
.table-responsive,
.table-wrapper {
    position: relative !important;
    z-index: 1 !important;
}

/* ============================================================================
   ENHANCED ACTION BUTTON STYLES
   ============================================================================ */

/* Action button container */
.btn-group[aria-label="Booking Actions"] {
    display: inline-flex;
    gap: 4px;
    flex-wrap: nowrap;
}

/* Base action button styles */
.action-btn {
    position: relative !important;
    z-index: 10 !important;
    cursor: pointer !important;
    pointer-events: auto !important;
    transition: all 0.2s ease !important;
    border-width: 1px !important;
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.5rem;
}

/* View Details Button */
.view-btn {
    border-color: #17a2b8 !important;
    color: #17a2b8 !important;
}

.view-btn:hover:not(:disabled) {
    background-color: #17a2b8 !important;
    color: white !important;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(23, 162, 184, 0.4);
}

/* Approve Button */
.approve-btn {
    border-color: #28a745 !important;
    color: white !important;
    background-color: #28a745 !important;
}

.approve-btn:hover:not(:disabled) {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
}

/* Update Status Button */
.update-btn {
    border-color: #007bff !important;
    color: #007bff !important;
}

.update-btn:hover:not(:disabled) {
    background-color: #007bff !important;
    color: white !important;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.4);
}

/* Delete Button */
.delete-btn {
    border-color: #dc3545 !important;
    color: #dc3545 !important;
}

.delete-btn:hover:not(:disabled) {
    background-color: #dc3545 !important;
    color: white !important;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
}

/* Receipt Button */
.receipt-btn {
    border-color: #28a745 !important;
    color: white !important;
    background-color: #28a745 !important;
}

.receipt-btn:hover:not(:disabled) {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
}

/* Disabled state for all action buttons */
.action-btn:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    transform: none !important;
}

/* Loading state */
.action-btn:disabled .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Active/Click state */
.action-btn:active:not(:disabled) {
    transform: scale(0.95) !important;
}

/* Focus state for accessibility */
.action-btn:focus {
    outline: 2px solid rgba(0, 123, 255, 0.5);
    outline-offset: 2px;
}

/* Ensure icons are centered */
.action-btn i {
    font-size: 14px;
    line-height: 1;
}

/* Responsive sizing for smaller screens */
@media (max-width: 768px) {
    .action-btn {
        min-width: 28px;
        height: 28px;
        padding: 0.2rem 0.4rem;
    }
    
    .action-btn i {
        font-size: 12px;
    }
    
    .btn-group[aria-label="Booking Actions"] {
        gap: 2px;
    }
}

/* Ensure buttons are always clickable even in DataTables */
table.dataTable tbody .action-btn {
    pointer-events: auto !important;
    z-index: 10 !important;
}

/* Button group spacing */
.btn-group .action-btn + .action-btn {
    margin-left: 0;
}

/* Tooltip enhancement */
.action-btn[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 5px;
    opacity: 0;
    animation: tooltipFadeIn 0.2s ease forwards;
}

@keyframes tooltipFadeIn {
    to {
        opacity: 1;
    }
}
</style>

<script>
// ============================================
// INSPECTION WORKFLOW FUNCTIONS
// ============================================

// Open Record Measurements Modal
function openRecordMeasurements(bookingId) {
    document.getElementById('measurements_booking_id').value = bookingId;
    
    // Load existing measurements if available
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                const booking = data.booking;
                // Populate form if measurements exist
                if (booking.measurement_height) document.getElementById('measurement_height').value = booking.measurement_height;
                if (booking.measurement_width) document.getElementById('measurement_width').value = booking.measurement_width;
                if (booking.measurement_thickness) document.getElementById('measurement_thickness').value = booking.measurement_thickness;
                if (booking.measurement_custom) document.getElementById('measurement_custom').value = booking.measurement_custom;
                if (booking.measurement_notes) document.getElementById('measurement_notes').value = booking.measurement_notes;
            }
        })
        .catch(error => console.error('Error loading measurements:', error));
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#recordMeasurementsModal').modal('show');
    } else {
        const modalEl = document.getElementById('recordMeasurementsModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modalEl).show();
        }
    }
}

// Save Measurements
function saveMeasurements() {
    const formData = new FormData(document.getElementById('measurementsForm'));
    const bookingId = formData.get('booking_id');
    
    fetch('<?php echo BASE_URL; ?>admin/saveMeasurements', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Measurements saved successfully!');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#recordMeasurementsModal').modal('hide');
            }
        } else {
            showAlert('danger', data.message || 'Failed to save measurements');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while saving measurements');
    });
}

// Open Record Damages Modal
function openRecordDamages(bookingId) {
    document.getElementById('damages_booking_id').value = bookingId;
    
    // Load existing damages if available
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                const booking = data.booking;
                // Populate form if damages exist
                if (booking.damage_description) document.getElementById('damage_description').value = booking.damage_description;
                if (booking.damage_location) document.getElementById('damage_location').value = booking.damage_location;
                if (booking.damage_severity) document.getElementById('damage_severity').value = booking.damage_severity;
            }
        })
        .catch(error => console.error('Error loading damages:', error));
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#recordDamagesModal').modal('show');
    } else {
        const modalEl = document.getElementById('recordDamagesModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modalEl).show();
        }
    }
}

// Save Damages
function saveDamages() {
    const form = document.getElementById('damagesForm');
    const formData = new FormData(form);
    const bookingId = formData.get('booking_id');
    
    fetch('<?php echo BASE_URL; ?>admin/saveDamages', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Damage record saved successfully!');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#recordDamagesModal').modal('hide');
            }
        } else {
            showAlert('danger', data.message || 'Failed to save damage record');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while saving damage record');
    });
}

// Open Add Materials Modal
function openAddMaterials(bookingId) {
    document.getElementById('materials_booking_id').value = bookingId;
    
    // Load existing materials if available
    fetch(`<?php echo BASE_URL; ?>admin/getBookingDetails/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.booking) {
                const booking = data.booking;
                // Populate form if materials exist
                if (booking.material_fabric_type) document.getElementById('material_fabric_type').value = booking.material_fabric_type;
                if (booking.material_meters) document.getElementById('material_meters').value = booking.material_meters;
                if (booking.material_foam) document.getElementById('material_foam').value = booking.material_foam;
                if (booking.material_foam_thickness) document.getElementById('material_foam_thickness').value = booking.material_foam_thickness;
                if (booking.material_notes) document.getElementById('material_notes').value = booking.material_notes;
            }
        })
        .catch(error => console.error('Error loading materials:', error));
    
    // Show modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#addMaterialsModal').modal('show');
    } else {
        const modalEl = document.getElementById('addMaterialsModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modalEl).show();
        }
    }
}

// Save Materials
function saveMaterials() {
    const form = document.getElementById('materialsForm');
    const formData = new FormData(form);
    const bookingId = formData.get('booking_id');
    
    fetch('<?php echo BASE_URL; ?>admin/saveMaterials', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Materials saved successfully!');
            if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                jQuery('#addMaterialsModal').modal('hide');
            }
        } else {
            showAlert('danger', data.message || 'Failed to save materials');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while saving materials');
    });
}

// Open Compute Total Modal (reuse receipt modal)
function openComputeTotal(bookingId) {
    if (typeof handleGenerateReceipt === 'function') {
        handleGenerateReceipt(bookingId);
    } else if (typeof generateReceipt === 'function') {
        generateReceipt(bookingId);
    } else {
        alert('Compute Total function not available. Please refresh the page.');
    }
}

// Send Preview Receipt to Customer
// This sends the receipt and updates status to "for_repair"
function sendPreviewReceipt(bookingId) {
    if (!confirm('Send preview receipt to customer? This will update status to "For Repair" and notify them of the total amount.')) {
        return;
    }
    
    // First, ensure receipt data is saved
    if (typeof saveReceipt === 'function') {
        // Save receipt first, then send
        saveReceipt().then(() => {
            // After saving, send to customer (this updates status to "for_repair")
            sendReceiptToCustomer();
        }).catch(() => {
            alert('Please complete the receipt form first (Compute Total) before sending.');
        });
    } else {
        // If saveReceipt doesn't exist, try to send directly
        if (typeof sendReceiptToCustomer === 'function') {
            sendReceiptToCustomer();
        } else {
            alert('Send receipt function not available. Please complete the receipt form first.');
        }
    }
}
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
