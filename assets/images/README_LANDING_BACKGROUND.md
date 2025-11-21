# Landing Page Background Image

## Image Setup Instructions

To use the custom background image on the landing page, please follow these steps:

1. **Place your image file** in this directory (`assets/images/`)
2. **Name the image file** as: `landing-background.jpg`
   - Supported formats: `.jpg`, `.jpeg`, `.png`, `.webp`
   - Recommended size: 1920x1080px or larger
   - Recommended file size: Under 1MB for optimal loading

3. **Image Description** (from the design):
   - Dominant brown/chocolate background
   - Two cutout areas showing interior design scenes:
     - Top-right: Modern cafe/dining area with wooden table and stools
     - Bottom-left: Furniture showroom with beige sofa
   - Decorative brown blobs and circles scattered across the background

4. **Alternative Image Names:**
   If you want to use a different filename, update the path in `views/landing.php`:
   ```css
   background-image: url('<?php echo BASE_URL; ?>assets/images/your-image-name.jpg');
   ```

## Current Styling

The landing page has been configured with:
- **Dark overlay**: Ensures text readability over the background image
- **Fixed attachment**: Creates a parallax effect (disabled on mobile)
- **Responsive design**: Adapts to different screen sizes
- **Fallback color**: Brown (#8B4513) if image fails to load

## Image Optimization Tips

1. Compress the image to reduce file size
2. Use WebP format for better compression
3. Ensure the image is high quality but optimized
4. Test loading speed on different devices

## Testing

After placing the image:
1. Clear browser cache
2. Refresh the landing page
3. Check on different screen sizes
4. Verify text readability

