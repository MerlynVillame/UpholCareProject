# Google Maps API Setup

## Required for Store Locations Feature

The store locations feature requires a Google Maps API key to function properly. Follow these steps to set it up:

### 1. Get Google Maps API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the following APIs:
   - Maps JavaScript API
   - Places API (optional, for enhanced features)
4. Create credentials (API Key)
5. Restrict the API key to your domain for security

### 2. Configure the API Key

In `views/customer/store_locations.php`, replace `YOUR_GOOGLE_MAPS_API_KEY` with your actual API key:

```html
<script
  async
  defer
  src="https://maps.googleapis.com/maps/api/js?key=YOUR_ACTUAL_API_KEY&loading=async&callback=initMap"
></script>
```

### 3. Features That Require API Key

- Interactive map display
- Store location markers
- User location detection
- Distance calculations
- Map-based store selection

### 4. Fallback Behavior

If the API key is not configured or invalid:

- The map will show a message explaining the issue
- Users can still browse stores using the list view
- All other functionality remains available
- Store selection still works through the dropdown

### 5. Security Best Practices

- Restrict your API key to specific domains
- Use HTTP referrer restrictions
- Monitor API usage in Google Cloud Console
- Set up billing alerts to avoid unexpected charges

### 6. Testing

After setting up the API key:

1. Visit the store locations page
2. The map should load with store markers
3. Test the "Use My Location" feature
4. Verify store selection works

### 7. Troubleshooting

Common issues:

- **"google is not defined"**: API key not configured or invalid
- **Map not loading**: Check internet connection and API key
- **Markers not showing**: Verify Maps JavaScript API is enabled
- **Location not working**: Check browser permissions for geolocation

For more information, visit the [Google Maps JavaScript API documentation](https://developers.google.com/maps/documentation/javascript).
