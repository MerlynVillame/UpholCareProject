<?php
/**
 * Status Transition Guard
 * Prevents backward status movement and enforces strict forward-only workflow
 */

class StatusTransitionGuard {
    
    /**
     * Define allowed status transitions (forward-only)
     * Key = current status, Value = array of allowed next statuses
     */
    private static $allowedTransitions = [
        // Initial stages
        'pending' => ['for_pickup', 'accepted', 'cancelled'],
        'accepted' => ['for_pickup', 'cancelled'],
        
        // Pickup workflow
        'for_pickup' => ['picked_up', 'cancelled'],
        'picked_up' => ['to_inspect', 'for_inspection', 'cancelled'],
        'to_inspect' => ['for_inspection', 'inspect_completed', 'cancelled'],
        'for_inspection' => ['inspect_completed', 'cancelled'],
        
        // Inspection completed - customer approval required
        'inspect_completed' => ['under_repair', 'cancelled'],
        'preview_receipt_sent' => ['under_repair', 'cancelled'], // Backward compatibility
        
        // Repair workflow
        'under_repair' => ['repair_completed', 'for_quality_check', 'cancelled'],
        'for_quality_check' => ['repair_completed', 'cancelled'],
        'repair_completed' => ['repair_completed_ready_to_deliver', 'ready_for_pickup', 'completed', 'cancelled'],
        'repair_completed_ready_to_deliver' => ['out_for_delivery', 'ready_for_pickup', 'cancelled'],
        
        // Delivery workflow
        'ready_for_pickup' => ['completed', 'paid', 'cancelled'],
        'out_for_delivery' => ['delivered_and_paid', 'completed', 'cancelled'],
        
        // Completion
        'completed' => ['paid', 'delivered_and_paid', 'closed'],
        'delivered_and_paid' => ['closed'],
        'paid' => ['closed'],
        'closed' => [], // Final status - no transitions allowed
        
        // Cancellation
        'cancelled' => [], // Final status - no transitions allowed
    ];
    
    /**
     * Check if a status transition is allowed
     * 
     * @param string $currentStatus Current booking status
     * @param string $newStatus Desired new status
     * @return bool True if transition is allowed, false otherwise
     */
    public static function isTransitionAllowed($currentStatus, $newStatus) {
        // Normalize statuses to lowercase
        $currentStatus = strtolower(trim($currentStatus));
        $newStatus = strtolower(trim($newStatus));
        
        // Same status is always allowed (no-op)
        if ($currentStatus === $newStatus) {
            return true;
        }
        
        // Check if current status exists in transitions
        if (!isset(self::$allowedTransitions[$currentStatus])) {
            // Unknown current status - allow transition but log warning
            error_log("Warning: Unknown current status '{$currentStatus}' in status transition check");
            return true; // Allow unknown transitions for backward compatibility
        }
        
        // Check if new status is in allowed transitions for current status
        $allowedNextStatuses = self::$allowedTransitions[$currentStatus];
        
        // Check exact match
        if (in_array($newStatus, $allowedNextStatuses)) {
            return true;
        }
        
        // Check case-insensitive match
        foreach ($allowedNextStatuses as $allowedStatus) {
            if (strtolower($allowedStatus) === $newStatus) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get allowed next statuses for a given current status
     * 
     * @param string $currentStatus Current booking status
     * @return array Array of allowed next statuses
     */
    public static function getAllowedNextStatuses($currentStatus) {
        $currentStatus = strtolower(trim($currentStatus));
        
        if (!isset(self::$allowedTransitions[$currentStatus])) {
            return [];
        }
        
        return self::$allowedTransitions[$currentStatus];
    }
    
    /**
     * Validate and enforce status transition
     * Throws exception if transition is not allowed
     * 
     * @param string $currentStatus Current booking status
     * @param string $newStatus Desired new status
     * @throws Exception If transition is not allowed
     */
    public static function validateTransition($currentStatus, $newStatus) {
        if (!self::isTransitionAllowed($currentStatus, $newStatus)) {
            $allowedNext = self::getAllowedNextStatuses($currentStatus);
            $allowedNextStr = !empty($allowedNext) ? implode(', ', $allowedNext) : 'none (final status)';
            
            throw new Exception(
                "Invalid status transition from '{$currentStatus}' to '{$newStatus}'. " .
                "Allowed next statuses: {$allowedNextStr}"
            );
        }
    }
    
    /**
     * Get status flow description for a service option
     * 
     * @param string $serviceOption Service option (pickup, delivery, both)
     * @return array Array of status flow steps
     */
    public static function getStatusFlow($serviceOption = 'pickup') {
        if ($serviceOption === 'delivery' || $serviceOption === 'both') {
            return [
                'pending',
                'accepted',
                'for_pickup',
                'picked_up',
                'to_inspect',
                'inspect_completed',
                'under_repair',
                'repair_completed',
                'repair_completed_ready_to_deliver',
                'out_for_delivery',
                'delivered_and_paid',
                'closed'
            ];
        } else {
            // Pickup only
            return [
                'pending',
                'accepted',
                'for_pickup',
                'picked_up',
                'to_inspect',
                'inspect_completed',
                'under_repair',
                'repair_completed',
                'ready_for_pickup',
                'completed',
                'paid',
                'closed'
            ];
        }
    }
}

