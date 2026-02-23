# Test Data Management for UphoCare Reports

## Overview
This folder contains scripts to seed and manage test data for testing the yearly reports functionality.

## Files

### 1. `seed_yearly_test_data.php`
Creates sample booking records for multiple years (2010-2025) - 15 years of historical data.

**Features:**
- Creates 3-8 completed bookings per month (increases with recent years)
- Generates realistic payment amounts with inflation (4% annual increase)
- Simulates business growth over time
- Marks all test records with `TEST_DATA_DO_NOT_DELETE` flag
- Creates test customer account
- Provides visual feedback of records created

**To Run:**
```
http://localhost/UphoCare/database/seed_yearly_test_data.php
```

### 2. `remove_test_data.php`
Safely removes all test booking records.

**Features:**
- Confirmation page before deletion
- Shows count of records to be deleted
- Displays sample of records being deleted
- Removes test customer if no other bookings exist
- Cannot be undone - use carefully!

**To Run:**
```
http://localhost/UphoCare/database/remove_test_data.php
```

## Usage Instructions

### Step 1: Add Test Data
1. Open your browser
2. Navigate to: `http://localhost/UphoCare/database/seed_yearly_test_data.php`
3. Wait for the script to complete (creates ~600-900 records across 15 years)
4. Click "View Reports Dashboard" to test

### Step 2: Test Reports
1. Go to Admin Reports page
2. Use year search (try 2010, 2015, 2020, 2021, 2025, etc.)
3. View line graphs for each year
4. Check monthly breakdown tables
5. Verify profit margins are displayed
6. Compare trends across different years

### Step 3: Remove Test Data (When Done)
1. Navigate to: `http://localhost/UphoCare/database/remove_test_data.php`
2. Review confirmation page
3. Click "Yes, Delete Test Data"
4. All test records will be removed

## Test Data Characteristics

### Years Covered
- **2010 through 2025** (15 years of historical data)
- Comprehensive year-over-year comparison capability

### Bookings Per Year
- **Early years (2010-2015):** ~36-60 bookings per year (3-5 per month)
- **Recent years (2016-2025):** ~50-96 bookings per year (4-8 per month)
- **Total:** ~600-900 test bookings across all years
- Business growth simulation included

### Services Used
- Sofa Repair (₱1,500 base in 2010, ~₱2,400 in 2025)
- Mattress Cover (₱800 base in 2010, ~₱1,280 in 2025)
- Chair Upholstery (₱1,200 base in 2010, ~₱1,920 in 2025)
- Couch Restoration (₱2,500 base in 2010, ~₱4,000 in 2025)
- Cushion Repair (₱600 base in 2010, ~₱960 in 2025)
- Dining Chair Set (₱3,500 base in 2010, ~₱5,600 in 2025)
- Ottoman Repair (₱900 base in 2010, ~₱1,440 in 2025)
- Recliner Restoration (₱2,800 base in 2010, ~₱4,480 in 2025)

### Price Variations
- **Inflation simulation:** 4% annual price increase (realistic market growth)
- Base prices vary ±₱200-500 per booking
- Additional fees (labor, pickup, delivery, gas, travel) added randomly
- Grand totals range from ₱600 to ₱6,000+ depending on year and service
- Older years have lower prices, recent years have higher prices

### Record Identification
All test records have:
- `notes` field = `'TEST_DATA_DO_NOT_DELETE'`
- `item_description` starts with `'[TEST]'`
- `booking_number` starts with `'TEST-'`

## Safety Features

### Protection Against Accidental Deletion
- Confirmation page required before deletion
- Only deletes records with specific test marker
- Shows preview of records to be deleted
- Preserves all real customer data

### Easy Identification
- Test records clearly marked in database
- Separate test customer account
- Unique booking number format
- Can be filtered in queries

## Troubleshooting

### Script Not Loading
- Check Apache/MySQL is running (XAMPP)
- Verify file path is correct
- Check PHP error logs

### No Records Created
- Ensure database connection is working
- Check services table has at least one service
- Verify database user has INSERT permissions

### Cannot Delete Records
- Ensure database user has DELETE permissions
- Check if records exist: `SELECT * FROM bookings WHERE notes = 'TEST_DATA_DO_NOT_DELETE'`

## Database Impact

### Before Seeding
```sql
SELECT COUNT(*) FROM bookings WHERE status = 'completed' AND payment_status = 'paid';
```

### After Seeding
Approximately 180-300 additional completed bookings

### After Removal
Database returns to original state

## Tips

1. **Seed once, test multiple times**: Test data persists until you remove it
2. **Compare years**: Add test data for multiple years to compare trends
3. **Realistic testing**: Prices and distributions mimic real usage patterns
4. **Easy cleanup**: Remove all test data with one click when done

## Quick Links

- **Seed Data**: `/database/seed_yearly_test_data.php`
- **Remove Data**: `/database/remove_test_data.php`
- **View Reports**: `/admin/reports`
- **Admin Dashboard**: `/admin/dashboard`

## Notes

- Test customer email: `test_customer@uphocare.test`
- Test customer password: `TestPass123!`
- All test bookings are marked as completed and paid
- Dates are distributed throughout each year
- Safe to run multiple times (creates new records each time)

---

**Created for:** UphoCare Reports Testing  
**Version:** 1.0  
**Last Updated:** November 2025

