<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo APP_DESC; ?>">
    <meta name="author" content="">
    
    <!-- Cache Control -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title><?php echo $title; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' fill='%23103058'><path d='M466.5 83.7l-192-80c-4.9-2-10.3-2-15.2 0l-192 80C60 86.6 56 93.6 56 101.2V256c0 137.4 90.7 248 200 248s200-110.6 200-248V101.2c0-7.6-4-14.6-11.5-17.5zM256 448c-79.5 0-144-85.9-144-192V117.8l144-60 144 60V256c0 106.1-64.5 192-144 192z'/></svg>" type="image/svg+xml">

    <!-- Preconnect to Google Fonts and CDN for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Custom fonts for this template-->
    <link href="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Disable service workers and clear caches to prevent caching issues -->
    <script>
        // Unregister all service workers
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.unregister();
                    console.log('Service worker unregistered:', registration);
                }
            }).catch(function(err) {
                console.warn('Failed to unregister service workers:', err);
            });
        }
        
        // Clear all caches
        if ('caches' in window) {
            caches.keys().then(function(names) {
                for (let name of names) {
                    caches.delete(name);
                }
            }).catch(function(err) {
                console.warn('Failed to clear caches:', err);
            });
        }
        
        // Global error handler for resource loading failures (only for images)
        window.addEventListener('error', function(e) {
            // Only handle image loading errors, ignore script/link errors
            if (e.target !== window && e.target.tagName === 'IMG') {
                const imgSrc = e.target.src || '';
                // Only log if it's not a default fallback image
                if (imgSrc && !imgSrc.includes('default-avatar.svg') && !imgSrc.includes('default-cover.svg') && !imgSrc.includes('undraw_profile.svg')) {
                    // Fix common URL issues - remove /customer/ or /admin/ from path
                    if (imgSrc.includes('/customer/') || imgSrc.includes('/admin/')) {
                        const fixedSrc = imgSrc.replace(/\/(customer|admin)\//, '/');
                        if (fixedSrc !== imgSrc) {
                            e.target.src = fixedSrc;
                            return; // Don't retry if we fixed the URL
                        }
                    }
                    
                    // Retry loading the image once with cache buster
                    if (!e.target.dataset.retried) {
                        e.target.dataset.retried = 'true';
                        const cacheBuster = (imgSrc.indexOf('?') >= 0 ? '&' : '?') + 't=' + Date.now();
                        e.target.src = imgSrc + cacheBuster;
                    }
                }
            }
        }, true);
        
        // Override fetch to add cache-busting
        if (window.fetch) {
            const originalFetch = window.fetch;
            window.fetch = function(url, options) {
                options = options || {};
                options.cache = options.cache || 'no-cache';
                options.headers = options.headers || {};
                options.headers['Cache-Control'] = 'no-cache';
                options.headers['Pragma'] = 'no-cache';
                return originalFetch(url, options);
            };
        }
    </script>

    <!-- Custom styles for this template-->
    <link href="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- UphoCare Custom Styles -->
    <link href="<?php echo BASE_URL; ?>assets/css/uphocare.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Inline Modal Fix Styles (Fallback if CSS fails to load) -->
    <style>
    /* Fix Modal Visibility and Brightness - Applied inline to ensure it works */
    .modal-backdrop {
        background-color: transparent !important; /* Fully transparent - no dark overlay */
        opacity: 0 !important; /* Fully transparent - bright modal */
        z-index: 1040 !important;
        pointer-events: none !important; /* Allow clicks through */
    }
    
    .modal-backdrop.show {
        background-color: transparent !important; /* Fully transparent - no dark overlay */
        opacity: 0 !important; /* Fully transparent - bright modal like Official Receipt */
        z-index: 1040 !important;
        pointer-events: none !important; /* Allow clicks through */
    }
    
    .modal {
        z-index: 1050 !important;
        opacity: 1 !important; /* ensure modal is not transparent */
    }
    
    .modal.show {
        z-index: 1050 !important;
        opacity: 1 !important; /* ensure modal is not transparent */
    }
    
    .modal-content {
        background-color: #ffffff !important; /* bright white */
        opacity: 1 !important; /* ensure not transparent */
        backdrop-filter: none !important; /* remove any backdrop filter */
    }
    
    .modal-dialog {
        z-index: 1055 !important;
    }
    </style>
    
    <!-- Business Mode Handler -->
    <script src="<?php echo BASE_URL; ?>assets/js/business-mode.js"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

