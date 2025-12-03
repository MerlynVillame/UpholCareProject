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
        
        // Global error handler for resource loading failures
        window.addEventListener('error', function(e) {
            if (e.target !== window && (e.target.tagName === 'LINK' || e.target.tagName === 'SCRIPT' || e.target.tagName === 'IMG')) {
                console.warn('Resource failed to load:', e.target.src || e.target.href);
                // Retry loading the resource once
                if (!e.target.dataset.retried) {
                    e.target.dataset.retried = 'true';
                    const src = e.target.src || e.target.href;
                    if (src) {
                        const cacheBuster = (src.indexOf('?') >= 0 ? '&' : '?') + 't=' + Date.now();
                        if (e.target.tagName === 'SCRIPT' || e.target.tagName === 'LINK') {
                            if (e.target.tagName === 'SCRIPT') {
                                e.target.src = src + cacheBuster;
                            } else {
                                e.target.href = src + cacheBuster;
                            }
                        }
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
    <link href="<?php echo BASE_URL; ?>assets/css/uphocare.css" rel="stylesheet">
    
    <!-- Business Mode Handler -->
    <script src="<?php echo BASE_URL; ?>assets/js/business-mode.js"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

