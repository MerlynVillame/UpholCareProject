# Guide: Pin Your Admin Store Location on Customer Map

## Overview
After registering as an admin with your business information, you need to ensure your store location is pinned on the customer's store locations map. This guide will help you verify and create your store location.

## Prerequisites
1. ✅ You have registered as an admin with:
   - Business name
   - Business address
   - Business city and province
   - Business permit (PDF)

2. ✅ Your registration has been approved by the super admin

3. ✅ You have verified your email with the verification code

## Steps to Pin Your Store Location

### Step 1: Verify Your Registration Status
1. Check if your admin registration status is `approved`
2. Check if your user account status is `active`
3. Verify that you have business name and address in your registration

### Step 2: Run the Store Location Creation Script
1. Open your browser
2. Navigate to: `http://localhost/UphoCare/database/create_store_location_for_verified_admins.php`
3. The script will:
   - List all verified admins with business information
   - Show which admins already have store locations
   - Allow you to create/update store locations

### Step 3: Process Your Store Location
1. Find your business in the list
2. Click "Create/Update Store" button next to your business
3. The script will:
   - Geocode your address to get coordinates (if not already done)
   - Create or update your store location in the `store_locations` table
   - Set your store status to `active`

### Step 4: Verify Your Store is Pinned
1. Log in as a customer (or use customer view)
2. Navigate to Store Locations page
3. Your store should appear on the map as a marker
4. Your store should appear in the "Highest Rated Stores" list

## What Happens Automatically

When you verify your admin code, the system should automatically:
1. ✅ Geocode your business address
2. ✅ Create store location entry
3. ✅ Pin your store on the customer map

However, if this didn't happen (e.g., if you registered before this feature was added), you can use the utility script to manually create your store location.

## Troubleshooting

### Store Not Appearing on Map
1. **Check if store location exists:**
   - Run the utility script: `create_store_location_for_verified_admins.php`
   - Check if your store is listed
   - If not, click "Create/Update Store"

2. **Check coordinates:**
   - Store must have valid latitude and longitude
   - Coordinates must be within Bohol bounds (Lat: 9.5-10.2, Lng: 123.6-124.4)

3. **Check store status:**
   - Store status must be `active`
   - Only active stores appear on the map

### Geocoding Failed
1. **Check address format:**
   - Ensure your address is complete and accurate
   - Include city and province (should be "Bohol")

2. **Check internet connection:**
   - Geocoding requires internet connection
   - If geocoding fails, default Bohol coordinates will be used

3. **Manual coordinate update:**
   - If needed, you can manually update coordinates in the database
   - Contact super admin for assistance

### Store Location Not Created During Verification
1. **Run the utility script:**
   - Navigate to: `http://localhost/UphoCare/database/create_store_location_for_verified_admins.php`
   - Click "Process All Admins" or process your specific admin
   - This will create store locations for all verified admins

2. **Check database:**
   - Verify your admin registration has `registration_status = 'approved'`
   - Verify you have `business_name` and `business_address` in `admin_registrations` table

## Database Tables

### admin_registrations
- `id`: Admin registration ID
- `email`: Your email address
- `business_name`: Your business/store name
- `business_address`: Your business address
- `business_city`: City (usually "Bohol")
- `business_province`: Province (usually "Bohol")
- `business_latitude`: Latitude coordinate (geocoded)
- `business_longitude`: Longitude coordinate (geocoded)
- `registration_status`: Should be `approved`

### store_locations
- `id`: Store location ID
- `store_name`: Store name (from business_name)
- `address`: Store address (from business_address)
- `city`: City
- `province`: Province
- `latitude`: Latitude coordinate
- `longitude`: Longitude coordinate
- `email`: Your email address
- `status`: Should be `active`

## Quick Check

Run this SQL query to check if your store location exists:

```sql
SELECT * FROM store_locations 
WHERE email = 'your_email@example.com';
```

If no results, your store location needs to be created. Use the utility script to create it.

## Need Help?

If you're still having issues:
1. Check the utility script output for error messages
2. Verify your admin registration is approved
3. Verify you have business information in your registration
4. Contact the super admin for assistance

