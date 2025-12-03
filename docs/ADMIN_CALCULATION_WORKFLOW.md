# Admin Calculation Workflow (Bayronon)

## Overview
This document describes the new workflow where admins must examine booking details, measure fabric, and calculate the total payment (bayronon) before approving customer reservations.

## Workflow Steps

### Step 1: View Booking Details (Required First Step)
1. Admin clicks **"View Details"** button on any booking
2. Modal opens showing:
   - Customer information
   - Service details
   - Service option (Pickup/Delivery/Both/Walk-in)
   - Booking information
   - Attached images

### Step 2: Calculate Total Payment (Bayronon)
After viewing details, admin must:

1. **Examine the Repair Item**
   - Review attached images
   - Assess damage and complexity
   - Determine materials needed

2. **Measure Fabric**
   - Enter **Fabric Length** (in meters)
   - Enter **Fabric Width** (in meters)
   - Enter **Fabric Cost per Meter** (₱)

3. **Calculate Costs**
   - Enter **Labor Fee** (based on complexity)
   - Enter **Additional Materials Cost** (foam, springs, zippers, thread, etc.)
   - Enter **Service Fees** (pickup, delivery, gas, travel fees)

4. **Add Calculation Notes**
   - Document measurements
   - Note damage assessment
   - Record material requirements

5. **Calculate Total**
   - Click **"Calculate Total Payment"** button
   - System calculates:
     - Fabric Total = (Length × Width × Cost per Meter)
     - Grand Total = Fabric Total + Labor Fee + Materials + Service Fees

6. **Save Total**
   - Click **"Save Total & Prepare Receipt"** button
   - Total is saved to database
   - Receipt is prepared with calculated total

### Step 3: Approve Booking
After total is calculated and saved:
- **"Approve Booking"** button becomes available
- Admin can approve the booking
- Receipt with calculated total is sent to customer

## Important Rules

### ⚠️ Approval Requirements
- **Cannot approve** without calculating total first
- **Cannot approve** without saving the calculated total
- System will redirect to "View Details" if approval is attempted without calculation

### 📋 Calculation Fields

| Field | Description | Required |
|-------|-------------|----------|
| Fabric Length | Length of fabric needed (meters) | Yes |
| Fabric Width | Width of fabric needed (meters) | Yes |
| Fabric Cost per Meter | Cost per meter of selected fabric (₱) | Yes |
| Labor Fee | Cost for labor/work (₱) | Yes |
| Material Cost | Additional materials (foam, springs, etc.) (₱) | No |
| Service Fees | Pickup, delivery, gas, travel fees (₱) | No |
| Calculation Notes | Admin notes about examination | No |

### 💰 Total Calculation Formula

```
Fabric Area = Length × Width
Fabric Total = Fabric Area × Cost per Meter
Grand Total = Fabric Total + Labor Fee + Material Cost + Service Fees
```

## Database Fields Added

The following fields were added to the `bookings` table:

- `fabric_length` - Fabric length in meters
- `fabric_width` - Fabric width in meters
- `fabric_area` - Calculated fabric area (length × width)
- `fabric_cost_per_meter` - Cost per meter of fabric
- `fabric_total` - Total fabric cost (area × cost per meter)
- `material_cost` - Additional materials cost
- `service_fees` - Service-related fees (pickup, delivery, etc.)
- `calculated_total_saved` - Flag indicating total has been calculated (0/1)
- `calculation_notes` - Admin notes about the calculation

## User Interface

### Booking Details Modal
- **Step 1**: Review Booking Details section
- **Step 2**: Calculate Total Payment section (with form)
- **Pricing Receipt**: Preview of receipt with calculated total

### Action Buttons
- **View Details**: Opens modal (always available)
- **Approve**: Only enabled after total is calculated and saved
- **Update Status**: Available for all bookings
- **Delete**: Available for all bookings

## Backend Endpoints

### `POST /admin/saveCalculatedTotal`
Saves the calculated total payment to the database.

**Request Parameters:**
- `booking_id` (required)
- `fabric_length` (required)
- `fabric_width` (required)
- `fabric_area` (calculated)
- `fabric_cost_per_meter` (required)
- `fabric_total` (calculated)
- `labor_fee` (required)
- `material_cost` (optional)
- `service_fees` (optional)
- `total_amount` (required)
- `calculation_notes` (optional)

**Response:**
```json
{
    "success": true,
    "message": "Calculated total saved successfully. You can now approve the booking.",
    "total_amount": "5000.00"
}
```

## Benefits

1. **Accurate Pricing**: Total is calculated based on actual measurements and examination
2. **Transparency**: Customer receives receipt with detailed breakdown
3. **Quality Control**: Admin must examine item before approval
4. **Documentation**: Calculation notes provide audit trail
5. **Prevents Errors**: System enforces calculation before approval

## Migration

To apply this feature, run:
```bash
php database/run_calculation_fields_migration.php
```

This adds the necessary database fields to the `bookings` table.

## Notes

- The calculation workflow is **mandatory** for all bookings
- Admins cannot bypass the calculation step
- The calculated total is included in the receipt sent to customers
- All calculations are saved to the database for record-keeping

