<?php
/**
 * Geocoding Service
 * Converts addresses to latitude/longitude coordinates
 * Uses Nominatim (OpenStreetMap) - Free, no API key required
 */

class GeocodingService {
    
    /**
     * Geocode an address to get coordinates
     * 
     * @param string $address Full address string
     * @param string $city City name
     * @param string $province Province/State name
     * @return array|null Returns ['lat' => float, 'lng' => float] or null on failure
     */
    public static function geocodeAddress($address, $city = 'Bohol', $province = 'Bohol') {
        // Build full address string
        $fullAddress = trim($address . ', ' . $city . ', ' . $province . ', Philippines');
        
        // Use Nominatim (OpenStreetMap) geocoding service (free, no API key)
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q' => $fullAddress,
            'format' => 'json',
            'limit' => 1,
            'countrycodes' => 'ph', // Limit to Philippines
            'addressdetails' => 1
        ]);
        
        // Set user agent (required by Nominatim)
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: UphoCare/1.0 (Contact: admin@uphocare.com)'
                ],
                'timeout' => 10
            ]
        ]);
        
        try {
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                error_log("Geocoding failed: Could not fetch data from Nominatim");
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
                error_log("Geocoding failed: No results found for address: " . $fullAddress);
                return null;
            }
            
            $result = $data[0];
            $latitude = floatval($result['lat']);
            $longitude = floatval($result['lon']);
            
            // Verify coordinates are within reasonable range for Philippines/Bohol
            // Bohol approximate bounds: Lat 9.5-10.2, Lng 123.6-124.4
            if ($latitude >= 9.0 && $latitude <= 10.5 && $longitude >= 123.0 && $longitude <= 125.0) {
                return [
                    'lat' => $latitude,
                    'lng' => $longitude
                ];
            } else {
                error_log("Geocoding warning: Coordinates outside Bohol range. Lat: {$latitude}, Lng: {$longitude}");
                // Still return coordinates, but log warning
                return [
                    'lat' => $latitude,
                    'lng' => $longitude
                ];
            }
            
        } catch (Exception $e) {
            error_log("Geocoding error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Geocode address with retry mechanism
     * 
     * @param string $address Full address string
     * @param string $city City name
     * @param string $province Province/State name
     * @param int $maxRetries Maximum number of retry attempts
     * @return array|null Returns coordinates or null
     */
    public static function geocodeAddressWithRetry($address, $city = 'Bohol', $province = 'Bohol', $maxRetries = 3) {
        for ($i = 0; $i < $maxRetries; $i++) {
            $result = self::geocodeAddress($address, $city, $province);
            
            if ($result !== null) {
                return $result;
            }
            
            // Wait before retry (respectful rate limiting)
            if ($i < $maxRetries - 1) {
                sleep(1);
            }
        }
        
        return null;
    }
    
    /**
     * Get default coordinates for Bohol (Tagbilaran City center)
     * Used as fallback when geocoding fails
     * 
     * @return array Returns default Bohol coordinates
     */
    public static function getDefaultBoholCoordinates() {
        return [
            'lat' => 9.6576, // Tagbilaran City, Bohol
            'lng' => 123.8544
        ];
    }
}

