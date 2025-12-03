<?php
/**
 * Fee Calculator Helper
 * Calculates pickup, delivery, labor, gas, and travel fees
 */

class FeeCalculator {
    // Fixed fees
    const LABOR_FEE = 100.00;
    const PICKUP_BASE_FEE = 150.00;
    const DELIVERY_BASE_FEE = 150.00;
    
    // Per kilometer rates
    const GAS_FEE_PER_KM = 5.00;
    const TRAVEL_FEE_PER_KM = 10.00;
    
    /**
     * Calculate all fees based on booking details
     * 
     * @param float $baseServicePrice Base service price
     * @param string $deliveryType 'pickup' or 'delivery'
     * @param float $distanceKm Distance in kilometers (optional, defaults to 0)
     * @return array Array of calculated fees
     */
    public static function calculateFees($baseServicePrice, $deliveryType = 'pickup', $distanceKm = 0) {
        // Calculate distance-based fees
        $gasFee = $distanceKm * self::GAS_FEE_PER_KM;
        $travelFee = $distanceKm * self::TRAVEL_FEE_PER_KM;
        
        // Labor fee (fixed)
        $laborFee = self::LABOR_FEE;
        
        // Pickup fee (only if pickup is selected)
        $pickupFee = 0;
        if ($deliveryType === 'pickup') {
            $pickupFee = self::PICKUP_BASE_FEE + $travelFee + $gasFee;
        }
        
        // Delivery fee (only if delivery COD is selected)
        $deliveryFee = 0;
        if ($deliveryType === 'delivery') {
            $deliveryFee = self::DELIVERY_BASE_FEE + $travelFee + $gasFee;
        }
        
        // Total additional fees
        $totalAdditionalFees = $laborFee + $pickupFee + $deliveryFee + $gasFee + $travelFee;
        
        // Grand total
        $grandTotal = $baseServicePrice + $totalAdditionalFees;
        
        return [
            'base_service_price' => $baseServicePrice,
            'labor_fee' => $laborFee,
            'pickup_fee' => $pickupFee,
            'delivery_fee' => $deliveryFee,
            'gas_fee' => $gasFee,
            'travel_fee' => $travelFee,
            'distance_km' => $distanceKm,
            'total_additional_fees' => $totalAdditionalFees,
            'grand_total' => $grandTotal
        ];
    }
    
    /**
     * Get fee breakdown for display
     * 
     * @param array $fees Fee array from calculateFees()
     * @return array Formatted fee breakdown
     */
    public static function getFeeBreakdown($fees) {
        $breakdown = [];
        
        if ($fees['base_service_price'] > 0) {
            $breakdown[] = [
                'description' => 'Base Service Price',
                'amount' => $fees['base_service_price']
            ];
        }
        
        if ($fees['labor_fee'] > 0) {
            $breakdown[] = [
                'description' => 'Labor Fee',
                'amount' => $fees['labor_fee']
            ];
        }
        
        if ($fees['pickup_fee'] > 0) {
            $breakdown[] = [
                'description' => 'Pick Up Fee',
                'amount' => $fees['pickup_fee']
            ];
        }
        
        if ($fees['delivery_fee'] > 0) {
            $breakdown[] = [
                'description' => 'Delivery COD Fee',
                'amount' => $fees['delivery_fee']
            ];
        }
        
        if ($fees['gas_fee'] > 0) {
            $breakdown[] = [
                'description' => 'Gas Fee' . ($fees['distance_km'] > 0 ? ' (' . number_format($fees['distance_km'], 2) . 'km × ₱' . number_format(self::GAS_FEE_PER_KM, 2) . ')' : ''),
                'amount' => $fees['gas_fee']
            ];
        }
        
        if ($fees['travel_fee'] > 0) {
            $breakdown[] = [
                'description' => 'Travel Fee' . ($fees['distance_km'] > 0 ? ' (' . number_format($fees['distance_km'], 2) . 'km × ₱' . number_format(self::TRAVEL_FEE_PER_KM, 2) . ')' : ''),
                'amount' => $fees['travel_fee']
            ];
        }
        
        if ($fees['total_additional_fees'] > 0) {
            $breakdown[] = [
                'description' => 'Total Additional Fees',
                'amount' => $fees['total_additional_fees'],
                'is_total' => true
            ];
        }
        
        $breakdown[] = [
            'description' => 'GRAND TOTAL',
            'amount' => $fees['grand_total'],
            'is_grand_total' => true
        ];
        
        return $breakdown;
    }
}

