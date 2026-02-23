# Admin Store Location Setup Guide

## Overview
When an admin registers with their business information, their store location will automatically appear on the customer store locations map after verification.

## Process Flow

### 1. Admin Registration
- Admin fills out registration form with:
  - Personal information (name, email, phone)
  - Business name
  - Business address (full address)
  - Business city (default: Bohol)
  - Business province (default: Bohol)
  - Business permit (PDF upload)

### 2. Super Admin Approval
- Super admin reviews the registration
- System automatically geocodes the business address to get coordinates
- Coordinates are stored in `admin_registrations` table
- Verification code is sent to admin

### 3. Admin Verification
- Admin enters verification code
- System creates store location entry in `store_locations` table
- Store location includes:
  - Business name
  - Business address
  - City and province
  - Latitude and longitude (from geocoding)
  - Phone and email
  - Status: active

### 4. Store Appears on Map
- Verified admin stores automatically appear on customer store locations map
- Stores are pinned at their geocoded coordinates
- Customers can view store details and select stores for booking

## Geocoding Service

The system uses **Nominatim (OpenStreetMap)** for geocoding:
- **Free** - No API key required
- **Automatic** - Converts addresses to coordinates
- **Bohol-focused** - Validates coordinates are within Bohol bounds
- **Fallback** - Uses default Bohol coordinates if geocoding fails

## Database Structure

### admin_registrations table
- `business_name` - Store/business name
- `business_address` - Full business address
- `business_city` - City (default: Bohol)
- `business_province` - Province (default: Bohol)
- `business_latitude` - Latitude coordinate
- `business_longitude` - Longitude coordinate
- `business_permit_path` - Path to uploaded PDF
- `business_permit_filename` - Original filename

### store_locations table
- Stores are automatically created here when admin is verified
- Includes all business information and coordinates
- Status is set to 'active' automatically

## Map Display

### Store Locations Map
- Shows all active stores with valid coordinates
- Restricted to Bohol province bounds
- Stores are displayed as markers on the map
- Clicking a marker shows store details
- Customers can select stores for booking

### Map Features
- **Interactive map** - Powered by Leaflet & OpenStreetMap
- **Store markers** - Color-coded by rating
- **Store popups** - Show store information
- **Store details modal** - Full store information with Google Maps embed
- **Search functionality** - Search stores by name, address, or city
- **City filter** - Filter stores by city
- **Nearest stores** - Find stores near user location

## Important Notes

1. **Coordinates Required**: Stores only appear on the map if they have valid coordinates
2. **Bohol Bounds**: Stores outside Bohol bounds (9.5-10.2 lat, 123.6-124.4 lng) are filtered out
3. **Geocoding**: Addresses are automatically geocoded during super admin approval and verification
4. **Store Creation**: Store locations are created automatically when admin verifies their code
5. **Map Updates**: Store locations are updated in real-time when new admins are verified

## Troubleshooting

### Store not appearing on map
1. Check if admin has been verified (status = 'active')
2. Verify coordinates exist in `store_locations` table
3. Check if coordinates are within Bohol bounds
4. Verify store status is 'active'

### Geocoding failed
1. Check internet connection (geocoding requires API call)
2. Verify address is complete and correct
3. Check server logs for geocoding errors
4. System will use default Bohol coordinates as fallback

### Coordinates outside Bohol
1. System validates coordinates are within Bohol bounds
2. Stores outside bounds are filtered out from map
3. Check address accuracy if coordinates seem wrong
4. Super admin can manually adjust coordinates if needed

## Manual Coordinate Adjustment

If geocoding fails or coordinates are incorrect, super admin can:
1. Access admin registration in control panel
2. Manually update coordinates in database
3. Update store location coordinates
4. Store will appear on map after coordinates are set

## Testing

1. Register a test admin with business information
2. Approve the admin registration (super admin)
3. Verify the admin code
4. Check `store_locations` table for new entry
5. View customer store locations map
6. Verify store appears on map with correct coordinates

