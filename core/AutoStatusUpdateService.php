<?php
/**
 * AutoStatusUpdateService
 * 
 * Automatically updates booking statuses based on pickup_date
 * This service checks bookings where pickup_date has arrived and updates their status accordingly
 */

require_once __DIR__ . '/../config/database.php';

class AutoStatusUpdateService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Check and update booking statuses based on pickup_date
     * This method should be called periodically (via cron or on page load)
     * 
     * @return array Statistics about the update process
     */
    public function checkAndUpdateStatuses() {
        $stats = [
            'checked' => 0,
            'updated' => 0,
            'errors' => 0,
            'details' => []
        ];
        
        try {
            $today = date('Y-m-d');
            $todayDateTime = date('Y-m-d H:i:s');
            
            error_log("AutoStatusUpdate: Starting check at {$todayDateTime}, looking for bookings with pickup_date <= {$today}");
            
            // STEP 1: Check for bookings where pickup_date is today or has passed
            // CRITICAL: Only process 'approved' and 'for_pickup' statuses
            // NEVER touch bookings that are already in 'to_inspect', 'for_repair', or beyond
            // This prevents any status from reverting backwards
            $sql = "SELECT id, status, pickup_date, service_option, user_id
                    FROM bookings
                    WHERE pickup_date IS NOT NULL
                    AND pickup_date != ''
                    AND (
                        DATE(pickup_date) <= :today
                        OR pickup_date <= :todayDateTime
                    )
                    AND (
                        status = 'approved' OR status = 'for_pickup'
                    )
                    AND status NOT IN ('to_inspect', 'for_repair', 'under_repair', 'in_progress', 'completed', 'paid', 'cancelled', 'picked_up', 'pending')
                    ORDER BY pickup_date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'today' => $today,
                'todayDateTime' => $todayDateTime
            ]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['checked'] = count($bookings);
            error_log("AutoStatusUpdate: Found {$stats['checked']} bookings to check for pickup date updates");
            
            foreach ($bookings as $booking) {
                $bookingId = $booking['id'];
                $currentStatus = $booking['status'];
                $pickupDate = $booking['pickup_date'];
                $serviceOption = strtolower(trim($booking['service_option'] ?? ''));
                
                error_log("AutoStatusUpdate: Processing booking #{$bookingId} - Status: {$currentStatus}, Pickup Date: {$pickupDate}, Service: {$serviceOption}");
                
                try {
                    $newStatus = $this->determineNewStatus($currentStatus, $serviceOption, $pickupDate);
                    
                    if ($newStatus && $newStatus !== $currentStatus) {
                        error_log("AutoStatusUpdate: Booking #{$bookingId} will be updated from '{$currentStatus}' to '{$newStatus}'");
                        $updateResult = $this->updateBookingStatus($bookingId, $currentStatus, $newStatus);
                        
                        if ($updateResult) {
                            $stats['updated']++;
                            $stats['details'][] = [
                                'booking_id' => $bookingId,
                                'old_status' => $currentStatus,
                                'new_status' => $newStatus,
                                'pickup_date' => $pickupDate
                            ];
                            
                            error_log("AutoStatusUpdate: ✓ Booking #{$bookingId} successfully updated from '{$currentStatus}' to '{$newStatus}' (pickup_date: {$pickupDate})");
                        } else {
                            $stats['errors']++;
                            error_log("AutoStatusUpdate: ✗ Failed to update booking #{$bookingId} from '{$currentStatus}' to '{$newStatus}'");
                        }
                    } else {
                        error_log("AutoStatusUpdate: Booking #{$bookingId} - No status change needed (current: {$currentStatus}, determined: " . ($newStatus ?? 'null') . ")");
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    error_log("AutoStatusUpdate: ✗ Error processing booking #{$bookingId}: " . $e->getMessage());
                    error_log("AutoStatusUpdate: Stack trace: " . $e->getTraceAsString());
                }
            }
            
            // STEP 2: Check for bookings in 'under_repair' status where repair days have elapsed
            $repairStats = $this->checkAndUpdateRepairCompletion();
            $stats['checked'] += $repairStats['checked'];
            $stats['updated'] += $repairStats['updated'];
            $stats['errors'] += $repairStats['errors'];
            $stats['details'] = array_merge($stats['details'], $repairStats['details']);
            
            // STEP 3: Fix existing bookings with "Both" service option that are in "repair_completed" 
            // but should be "repair_completed_ready_to_deliver"
            $fixStats = $this->fixBothServiceOptionStatus();
            $stats['checked'] += $fixStats['checked'];
            $stats['updated'] += $fixStats['updated'];
            $stats['errors'] += $fixStats['errors'];
            $stats['details'] = array_merge($stats['details'], $fixStats['details']);
            
            // STEP 4: Check for bookings in delivery workflow (Both service option)
            // Automatically progress: repair_completed_ready_to_deliver → out_for_delivery → delivered_and_paid
            $deliveryStats = $this->checkAndUpdateDeliveryStatus();
            $stats['checked'] += $deliveryStats['checked'];
            $stats['updated'] += $deliveryStats['updated'];
            $stats['errors'] += $deliveryStats['errors'];
            $stats['details'] = array_merge($stats['details'], $deliveryStats['details']);
            
            // STEP 5: Check for pending bookings with delivery service where drop-off date has arrived
            // Automatically change: pending → for_dropoff and send notification
            $dropoffStats = $this->checkAndUpdateDeliveryDropoffStatus();
            $stats['checked'] += $dropoffStats['checked'];
            $stats['updated'] += $dropoffStats['updated'];
            $stats['errors'] += $dropoffStats['errors'];
            $stats['details'] = array_merge($stats['details'], $dropoffStats['details']);
            
        } catch (Exception $e) {
            error_log("AutoStatusUpdate: Fatal error - " . $e->getMessage());
            $stats['errors']++;
        }
        
        return $stats;
    }
    
    /**
     * Determine the new status based on current status and service option
     * 
     * IMPORTANT: This method only processes forward transitions. It NEVER reverts statuses.
     * Once a booking reaches 'to_inspect' or beyond, it will not be processed by this service.
     * 
     * @param string $currentStatus Current booking status
     * @param string $serviceOption Service option (pickup, delivery, both)
     * @param string $pickupDate Pickup date
     * @return string|null New status or null if no update needed
     */
    private function determineNewStatus($currentStatus, $serviceOption, $pickupDate) {
        $today = date('Y-m-d');
        
        // Handle different date formats (DATE, DATETIME, TIMESTAMP)
        $pickupTimestamp = strtotime($pickupDate);
        if ($pickupTimestamp === false) {
            error_log("AutoStatusUpdate: Invalid pickup_date format: {$pickupDate}");
            return null;
        }
        
        $pickupDateOnly = date('Y-m-d', $pickupTimestamp);
        $todayTimestamp = strtotime($today);
        
        // Only update if pickup_date is today or has passed
        if ($pickupTimestamp > $todayTimestamp) {
            error_log("AutoStatusUpdate: Pickup date {$pickupDateOnly} is in the future, skipping");
            return null; // Pickup date hasn't arrived yet
        }
        
        // SAFETY: Never process bookings that are already past the pickup stage
        // This prevents any accidental status reversions
        // Also protect delivery workflow statuses
        $advancedStatuses = [
            'to_inspect', 'for_repair', 'under_repair', 'in_progress', 
            'completed', 'paid', 'cancelled',
            'for_dropoff', 'for_inspect', 'inspection_completed_waiting_approval',
            'repair_completed', 'repair_completed_ready_to_deliver',
            'out_for_delivery', 'delivered_and_paid'
        ];
        if (in_array(strtolower($currentStatus), $advancedStatuses)) {
            return null; // Already past initial stage, do not touch
        }
        
        // Status transition logic based on current status
        // Only forward transitions allowed
        switch ($currentStatus) {
            case 'approved':
                // If service option is pickup or both, move to for_pickup
                if ($serviceOption === 'pickup' || $serviceOption === 'both') {
                    return 'for_pickup';
                }
                // For delivery-only, status remains approved
                return null;
                
            case 'for_pickup':
                // If pickup date has passed, item should be picked up
                // Move to picked_up (which will then move to to_inspect)
                if ($pickupDateOnly <= $today) {
                    return 'picked_up';
                }
                return null;
                
            default:
                // For any other status, do not update (safety measure)
                return null;
        }
    }
    
    /**
     * Update booking status
     * 
     * @param int $bookingId Booking ID
     * @param string $oldStatus Old status
     * @param string $newStatus New status
     * @return bool Success status
     */
    private function updateBookingStatus($bookingId, $oldStatus, $newStatus) {
        try {
            $this->db->beginTransaction();
            
            // CRITICAL SAFETY CHECK: Never update if current status is already advanced
            // This prevents any accidental reverting of status
            $checkSql = "SELECT status FROM bookings WHERE id = :booking_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute(['booking_id' => $bookingId]);
            $currentBooking = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$currentBooking) {
                $this->db->rollBack();
                error_log("AutoStatusUpdate: Booking #{$bookingId} not found");
                return false;
            }
            
            $actualCurrentStatus = $currentBooking['status'];
            $advancedStatuses = ['to_inspect', 'for_repair', 'under_repair', 'in_progress', 'completed', 'paid', 'cancelled'];
            
            // If booking is already in an advanced status, DO NOT TOUCH IT
            if (in_array(strtolower($actualCurrentStatus), $advancedStatuses)) {
                $this->db->rollBack();
                error_log("AutoStatusUpdate: Booking #{$bookingId} is already in advanced status '{$actualCurrentStatus}', skipping update to prevent reversion");
                return false;
            }
            
            // Only proceed if actual status matches expected old status
            if ($actualCurrentStatus !== $oldStatus) {
                $this->db->rollBack();
                error_log("AutoStatusUpdate: Booking #{$bookingId} status mismatch - expected '{$oldStatus}', found '{$actualCurrentStatus}'");
                return false;
            }
            
            // Update status
            $sql = "UPDATE bookings 
                    SET status = :new_status, 
                        updated_at = NOW() 
                    WHERE id = :booking_id 
                    AND status = :old_status";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'new_status' => $newStatus,
                'booking_id' => $bookingId,
                'old_status' => $oldStatus
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                // If status changed to picked_up, automatically move to to_inspect
                // (This matches the workflow where picked_up immediately goes to inspection)
                // IMPORTANT: Once in 'to_inspect', status will NOT revert - preview receipt is next step
                if ($newStatus === 'picked_up') {
                    $inspectSql = "UPDATE bookings 
                                   SET status = 'to_inspect', 
                                       updated_at = NOW() 
                                   WHERE id = :booking_id 
                                   AND status = 'picked_up'";
                    $inspectStmt = $this->db->prepare($inspectSql);
                    $inspectStmt->execute(['booking_id' => $bookingId]);
                    
                    // Update newStatus for logging
                    $newStatus = 'to_inspect';
                    error_log("AutoStatusUpdate: ✓ Booking #{$bookingId} automatically moved to 'to_inspect' (ready for preview receipt - will NOT revert)");
                }
                
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                error_log("AutoStatusUpdate: ✗ Failed to update booking #{$bookingId} - no rows affected");
                return false;
            }
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("AutoStatusUpdate: Database error - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check and update repair completion status
     * Automatically changes status from 'under_repair' to 'repair_completed' or 'repair_completed_ready_to_deliver'
     * when the allotted repair days have elapsed
     * 
     * @return array Statistics about the update process
     */
    private function checkAndUpdateRepairCompletion() {
        $stats = [
            'checked' => 0,
            'updated' => 0,
            'errors' => 0,
            'details' => []
        ];
        
        try {
            $today = date('Y-m-d');
            $todayDateTime = date('Y-m-d H:i:s');
            
            error_log("AutoStatusUpdate: Checking for repair completion - looking for bookings in 'under_repair' status where repair days have elapsed");
            
            // Find bookings in 'under_repair' status where repair days have elapsed
            // Calculate: repair_start_date + repair_days <= today
            $sql = "SELECT id, status, repair_start_date, repair_days, service_option, delivery_date, delivery_address
                    FROM bookings
                    WHERE status = 'under_repair'
                    AND repair_start_date IS NOT NULL
                    AND repair_days IS NOT NULL
                    AND repair_days > 0
                    AND DATE_ADD(DATE(repair_start_date), INTERVAL repair_days DAY) <= :today
                    ORDER BY repair_start_date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['today' => $today]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['checked'] = count($bookings);
            error_log("AutoStatusUpdate: Found {$stats['checked']} bookings ready for repair completion");
            
            foreach ($bookings as $booking) {
                $bookingId = $booking['id'];
                $repairStartDate = $booking['repair_start_date'];
                $repairDays = $booking['repair_days'];
                $serviceOption = strtolower(trim($booking['service_option'] ?? ''));
                $deliveryDate = $booking['delivery_date'] ?? null;
                $deliveryAddress = $booking['delivery_address'] ?? null;
                
                // Calculate expected completion date
                $expectedCompletionDate = date('Y-m-d', strtotime($repairStartDate . " +{$repairDays} days"));
                
                error_log("AutoStatusUpdate: Processing repair completion for booking #{$bookingId} - Started: {$repairStartDate}, Days: {$repairDays}, Expected Completion: {$expectedCompletionDate}");
                
                try {
                    // Determine new status based on service option
                    // For "Both" service option, always go to 'repair_completed_ready_to_deliver' (needs delivery)
                    // For "Delivery" service option, go to 'repair_completed_ready_to_deliver' if delivery info exists
                    // For "Pickup" only, go to 'repair_completed'
                    $newStatus = 'repair_completed';
                    
                    if ($serviceOption === 'both') {
                        // "Both" always requires delivery, so always go to ready_to_deliver
                        $newStatus = 'repair_completed_ready_to_deliver';
                    } elseif ($serviceOption === 'delivery') {
                        // "Delivery" option - always goes to ready_to_deliver (customer requested delivery)
                        $newStatus = 'repair_completed_ready_to_deliver';
                    }
                    // For "pickup" only, keep $newStatus as 'repair_completed'
                    
                    // Update status
                    $updateSql = "UPDATE bookings 
                                 SET status = :new_status, 
                                     completion_date = NOW(),
                                     updated_at = NOW() 
                                 WHERE id = :booking_id 
                                 AND status = 'under_repair'";
                    
                    $updateStmt = $this->db->prepare($updateSql);
                    $updateResult = $updateStmt->execute([
                        'new_status' => $newStatus,
                        'booking_id' => $bookingId
                    ]);
                    
                    if ($updateResult && $updateStmt->rowCount() > 0) {
                        $stats['updated']++;
                        $stats['details'][] = [
                            'booking_id' => $bookingId,
                            'old_status' => 'under_repair',
                            'new_status' => $newStatus,
                            'repair_start_date' => $repairStartDate,
                            'repair_days' => $repairDays,
                            'expected_completion' => $expectedCompletionDate
                        ];
                        
                        error_log("AutoStatusUpdate: ✓ Booking #{$bookingId} repair completed - Status changed from 'under_repair' to '{$newStatus}' (Started: {$repairStartDate}, Days: {$repairDays})");
                        
                        // Send notification to customer when repair is completed
                        if ($newStatus === 'repair_completed_ready_to_deliver') {
                            try {
                                $this->sendRepairCompletedNotification($bookingId);
                                error_log("AutoStatusUpdate: ✓ Notification sent to customer for booking #{$bookingId}");
                            } catch (Exception $e) {
                                error_log("AutoStatusUpdate: ✗ Failed to send notification for booking #{$bookingId}: " . $e->getMessage());
                                // Don't fail the whole update if notification fails
                            }
                        }
                    } else {
                        $stats['errors']++;
                        error_log("AutoStatusUpdate: ✗ Failed to update booking #{$bookingId} - no rows affected (may have been updated by another process)");
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    error_log("AutoStatusUpdate: ✗ Error processing repair completion for booking #{$bookingId}: " . $e->getMessage());
                    error_log("AutoStatusUpdate: Stack trace: " . $e->getTraceAsString());
                }
            }
            
        } catch (Exception $e) {
            error_log("AutoStatusUpdate: Fatal error in checkAndUpdateRepairCompletion - " . $e->getMessage());
            $stats['errors']++;
        }
        
        return $stats;
    }
    
    /**
     * Fix existing bookings with "Both" service option that are in "repair_completed" 
     * but should be "repair_completed_ready_to_deliver"
     * This is a one-time fix for bookings that were completed before the new logic was implemented
     * 
     * @return array Statistics about the update process
     */
    private function fixBothServiceOptionStatus() {
        $stats = [
            'checked' => 0,
            'updated' => 0,
            'errors' => 0,
            'details' => []
        ];
        
        try {
            error_log("AutoStatusUpdate: Fixing 'Both' service option bookings in 'repair_completed' status");
            
            // Find bookings with "Both" service option that are in "repair_completed" status
            // These should be "repair_completed_ready_to_deliver" instead
            $sql = "SELECT id, status, service_option, completion_date
                    FROM bookings
                    WHERE status = 'repair_completed'
                    AND LOWER(TRIM(service_option)) = 'both'
                    ORDER BY id ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['checked'] = count($bookings);
            error_log("AutoStatusUpdate: Found {$stats['checked']} 'Both' service option bookings to fix");
            
            foreach ($bookings as $booking) {
                $bookingId = $booking['id'];
                $completionDate = $booking['completion_date'];
                
                try {
                    // Update status to repair_completed_ready_to_deliver
                    $updateSql = "UPDATE bookings 
                                 SET status = 'repair_completed_ready_to_deliver', 
                                     updated_at = NOW() 
                                 WHERE id = :booking_id 
                                 AND status = 'repair_completed'
                                 AND LOWER(TRIM(service_option)) = 'both'";
                    
                    $updateStmt = $this->db->prepare($updateSql);
                    $updateResult = $updateStmt->execute(['booking_id' => $bookingId]);
                    
                    if ($updateResult && $updateStmt->rowCount() > 0) {
                        $stats['updated']++;
                        $stats['details'][] = [
                            'booking_id' => $bookingId,
                            'old_status' => 'repair_completed',
                            'new_status' => 'repair_completed_ready_to_deliver',
                            'reason' => 'Fixed Both service option status'
                        ];
                        error_log("AutoStatusUpdate: ✓ Fixed booking #{$bookingId} - Changed from 'repair_completed' to 'repair_completed_ready_to_deliver' (Both service option)");
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    error_log("AutoStatusUpdate: ✗ Error fixing booking #{$bookingId}: " . $e->getMessage());
                }
            }
            
        } catch (Exception $e) {
            error_log("AutoStatusUpdate: Fatal error in fixBothServiceOptionStatus - " . $e->getMessage());
            $stats['errors']++;
        }
        
        return $stats;
    }
    
    /**
     * Check and update delivery status progression for "Both" service option
     * Automatically progresses bookings through:
     * repair_completed_ready_to_deliver → out_for_delivery → delivered_and_paid
     * 
     * @return array Statistics about the update process
     */
    private function checkAndUpdateDeliveryStatus() {
        $stats = [
            'checked' => 0,
            'updated' => 0,
            'errors' => 0,
            'details' => []
        ];
        
        try {
            $today = date('Y-m-d');
            $todayDateTime = date('Y-m-d H:i:s');
            
            error_log("AutoStatusUpdate: Checking for delivery status progression - looking for 'Both' service option bookings");
            
            // Find bookings with "Both" service option that need delivery status updates
            // Step 1: repair_completed_ready_to_deliver → out_for_delivery
            // (Auto-progress after 1 day of being ready, or immediately if completion_date is today)
            $sql1 = "SELECT id, status, service_option, completion_date, delivery_date
                    FROM bookings
                    WHERE status = 'repair_completed_ready_to_deliver'
                    AND LOWER(TRIM(service_option)) = 'both'
                    AND completion_date IS NOT NULL
                    ORDER BY completion_date ASC";
            
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute();
            $readyToDeliver = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['checked'] += count($readyToDeliver);
            error_log("AutoStatusUpdate: Found " . count($readyToDeliver) . " bookings ready to move to 'out_for_delivery'");
            
            foreach ($readyToDeliver as $booking) {
                $bookingId = $booking['id'];
                $completionDate = $booking['completion_date'];
                $deliveryDate = $booking['delivery_date'];
                
                try {
                    // Auto-progress to out_for_delivery if:
                    // 1. Completion date is today or earlier, OR
                    // 2. Delivery date is today or earlier
                    $shouldProgress = false;
                    if ($completionDate) {
                        $completionDateOnly = date('Y-m-d', strtotime($completionDate));
                        if ($completionDateOnly <= $today) {
                            $shouldProgress = true;
                        }
                    }
                    if ($deliveryDate && !$shouldProgress) {
                        $deliveryDateOnly = date('Y-m-d', strtotime($deliveryDate));
                        if ($deliveryDateOnly <= $today) {
                            $shouldProgress = true;
                        }
                    }
                    
                    if ($shouldProgress) {
                        $updateSql = "UPDATE bookings 
                                     SET status = 'out_for_delivery', 
                                         updated_at = NOW() 
                                     WHERE id = :booking_id 
                                     AND status = 'repair_completed_ready_to_deliver'";
                        
                        $updateStmt = $this->db->prepare($updateSql);
                        $updateResult = $updateStmt->execute(['booking_id' => $bookingId]);
                        
                        if ($updateResult && $updateStmt->rowCount() > 0) {
                            $stats['updated']++;
                            $stats['details'][] = [
                                'booking_id' => $bookingId,
                                'old_status' => 'repair_completed_ready_to_deliver',
                                'new_status' => 'out_for_delivery',
                                'reason' => 'Auto-progress for Both service option'
                            ];
                            error_log("AutoStatusUpdate: ✓ Booking #{$bookingId} moved to 'out_for_delivery' (Both service option)");
                        }
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    error_log("AutoStatusUpdate: ✗ Error processing delivery progression for booking #{$bookingId}: " . $e->getMessage());
                }
            }
            
            // Step 2: out_for_delivery → delivered_and_paid
            // Auto-progress after delivery date has passed (or immediately if delivery_date is today)
            $sql2 = "SELECT id, status, service_option, delivery_date, updated_at
                    FROM bookings
                    WHERE status = 'out_for_delivery'
                    AND LOWER(TRIM(service_option)) = 'both'
                    AND delivery_date IS NOT NULL
                    ORDER BY delivery_date ASC";
            
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute();
            $outForDelivery = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['checked'] += count($outForDelivery);
            error_log("AutoStatusUpdate: Found " . count($outForDelivery) . " bookings ready to move to 'delivered_and_paid'");
            
            foreach ($outForDelivery as $booking) {
                $bookingId = $booking['id'];
                $deliveryDate = $booking['delivery_date'];
                $updatedAt = $booking['updated_at'];
                
                try {
                    // Auto-progress to delivered_and_paid if delivery date has passed
                    // Also check if status has been 'out_for_delivery' for at least 1 day
                    $shouldProgress = false;
                    if ($deliveryDate) {
                        $deliveryDateOnly = date('Y-m-d', strtotime($deliveryDate));
                        if ($deliveryDateOnly <= $today) {
                            // Check if it's been out for delivery for at least 1 day
                            if ($updatedAt) {
                                $updatedDateOnly = date('Y-m-d', strtotime($updatedAt));
                                $daysSinceUpdate = (strtotime($today) - strtotime($updatedDateOnly)) / (60 * 60 * 24);
                                if ($daysSinceUpdate >= 1 || $deliveryDateOnly < $today) {
                                    $shouldProgress = true;
                                }
                            } else {
                                // If no updated_at, progress if delivery date has passed
                                $shouldProgress = true;
                            }
                        }
                    }
                    
                    if ($shouldProgress) {
                        $updateSql = "UPDATE bookings 
                                     SET status = 'delivered_and_paid', 
                                         payment_status = 'paid_on_delivery_cod',
                                         updated_at = NOW() 
                                     WHERE id = :booking_id 
                                     AND status = 'out_for_delivery'";
                        
                        $updateStmt = $this->db->prepare($updateSql);
                        $updateResult = $updateStmt->execute(['booking_id' => $bookingId]);
                        
                        if ($updateResult && $updateStmt->rowCount() > 0) {
                            $stats['updated']++;
                            $stats['details'][] = [
                                'booking_id' => $bookingId,
                                'old_status' => 'out_for_delivery',
                                'new_status' => 'delivered_and_paid',
                                'reason' => 'Auto-progress for Both service option - delivery completed'
                            ];
                            error_log("AutoStatusUpdate: ✓ Booking #{$bookingId} moved to 'delivered_and_paid' (Both service option - delivery completed)");
                        }
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    error_log("AutoStatusUpdate: ✗ Error processing delivery completion for booking #{$bookingId}: " . $e->getMessage());
                }
            }
            
        } catch (Exception $e) {
            error_log("AutoStatusUpdate: Fatal error in checkAndUpdateDeliveryStatus - " . $e->getMessage());
            $stats['errors']++;
        }
        
        return $stats;
    }
    
    /**
     * Check and update delivery drop-off status for pending bookings
     * Changes pending → for_dropoff when delivery_date (drop-off date) arrives
     * Sends notification to customer
     * 
     * @return array Statistics
     */
    private function checkAndUpdateDeliveryDropoffStatus() {
        $stats = [
            'checked' => 0,
            'updated' => 0,
            'errors' => 0,
            'details' => []
        ];
        
        try {
            $today = date('Y-m-d');
            $todayDateTime = date('Y-m-d H:i:s');
            
            error_log("AutoStatusUpdate: Checking for pending delivery service bookings with drop-off date <= {$today}");
            
            // Find pending bookings with delivery service where delivery_date (drop-off date) has arrived
            $sql = "SELECT b.id, b.status, b.delivery_date, b.service_option, b.user_id, u.email, u.fullname
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.id
                    WHERE b.service_option = 'delivery'
                    AND b.status = 'pending'
                    AND b.delivery_date IS NOT NULL
                    AND b.delivery_date != ''
                    AND (
                        DATE(b.delivery_date) <= :today
                        OR b.delivery_date <= :todayDateTime
                    )
                    ORDER BY b.delivery_date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'today' => $today,
                'todayDateTime' => $todayDateTime
            ]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['checked'] = count($bookings);
            error_log("AutoStatusUpdate: Found {$stats['checked']} pending delivery bookings with drop-off date <= {$today}");
            
            foreach ($bookings as $booking) {
                $bookingId = $booking['id'];
                $currentStatus = $booking['status'];
                $dropoffDate = $booking['delivery_date'];
                $customerEmail = $booking['email'];
                $customerName = $booking['fullname'];
                
                error_log("AutoStatusUpdate: Processing booking #{$bookingId} - Status: {$currentStatus}, Drop-off Date: {$dropoffDate}");
                
                try {
                    // Update status from pending to for_dropoff
                    $updateResult = $this->updateBookingStatus($bookingId, 'pending', 'for_dropoff');
                    
                    if ($updateResult) {
                        $stats['updated']++;
                        $stats['details'][] = [
                            'booking_id' => $bookingId,
                            'old_status' => $currentStatus,
                            'new_status' => 'for_dropoff',
                            'dropoff_date' => $dropoffDate
                        ];
                        
                        error_log("AutoStatusUpdate: ✓ Booking #{$bookingId} successfully updated from '{$currentStatus}' to 'for_dropoff' (drop-off date: {$dropoffDate})");
                        
                        // Send notification to customer
                        if ($customerEmail) {
                            try {
                                $this->sendDropoffNotification($bookingId, $customerEmail, $customerName, $dropoffDate);
                                error_log("AutoStatusUpdate: ✓ Notification sent to customer for booking #{$bookingId}");
                            } catch (Exception $e) {
                                error_log("AutoStatusUpdate: ✗ Failed to send notification for booking #{$bookingId}: " . $e->getMessage());
                                // Don't fail the whole update if notification fails
                            }
                        }
                    } else {
                        $stats['errors']++;
                        error_log("AutoStatusUpdate: ✗ Failed to update booking #{$bookingId} from '{$currentStatus}' to 'for_dropoff'");
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    error_log("AutoStatusUpdate: ✗ Error processing booking #{$bookingId}: " . $e->getMessage());
                    error_log("AutoStatusUpdate: Stack trace: " . $e->getTraceAsString());
                }
            }
        } catch (Exception $e) {
            error_log("AutoStatusUpdate: Error in checkAndUpdateDeliveryDropoffStatus: " . $e->getMessage());
            $stats['errors']++;
        }
        
        return $stats;
    }
    
    /**
     * Send drop-off notification to customer
     * 
     * @param int $bookingId Booking ID
     * @param string|null $customerEmail Customer email (optional, will fetch if not provided)
     * @param string|null $customerName Customer name (optional, will fetch if not provided)
     * @param string|null $dropoffDate Drop-off date (optional, will fetch if not provided)
     * @return bool Success status
     */
    private function sendDropoffNotification($bookingId, $customerEmail = null, $customerName = null, $dropoffDate = null) {
        try {
            // Fetch booking details if not provided
            if (!$customerEmail || !$customerName || !$dropoffDate) {
                $sql = "SELECT b.delivery_date, b.user_id, u.email, u.fullname
                        FROM bookings b
                        LEFT JOIN users u ON b.user_id = u.id
                        WHERE b.id = :booking_id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['booking_id' => $bookingId]);
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$booking) {
                    error_log("AutoStatusUpdate: Booking #{$bookingId} not found for notification");
                    return false;
                }
                
                $customerEmail = $customerEmail ?? $booking['email'];
                $customerName = $customerName ?? $booking['fullname'];
                $dropoffDate = $dropoffDate ?? $booking['delivery_date'];
            }
            
            if (!$customerEmail) {
                error_log("AutoStatusUpdate: No email found for booking #{$bookingId}");
                return false;
            }
            
            // Format drop-off date
            $formattedDate = $dropoffDate ? date('F d, Y', strtotime($dropoffDate)) : 'your scheduled date';
            
            // Load NotificationService
            require_once __DIR__ . '/NotificationService.php';
            $notificationService = new NotificationService();
            
            // Prepare email
            $subject = "Reminder: Bring Your Item to Shop - Booking #{$bookingId}";
            $message = "Dear {$customerName},\n\n";
            $message .= "This is a reminder that your scheduled drop-off date is today ({$formattedDate}).\n\n";
            $message .= "Please bring your item to the shop on {$formattedDate} for inspection.\n\n";
            $message .= "After you bring the item, our team will inspect it and send you a preview receipt with the estimated cost.\n\n";
            $message .= "Thank you for choosing UphoCare!\n\n";
            $message .= "Best regards,\nUphoCare Team";
            
            // Send email
            return $notificationService->sendEmail($customerEmail, $subject, $message);
            
        } catch (Exception $e) {
            error_log("AutoStatusUpdate: Error sending drop-off notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Run the automatic status update check
     * This is a static method that can be called from anywhere
     * 
     * @return array Statistics
     */
    public static function run() {
        try {
            $service = new self();
            return $service->checkAndUpdateStatuses();
        } catch (Exception $e) {
            error_log("AutoStatusUpdateService::run() exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'checked' => 0,
                'updated' => 0,
                'errors' => 1,
                'details' => [],
                'error_message' => $e->getMessage()
            ];
        }
    }
}

