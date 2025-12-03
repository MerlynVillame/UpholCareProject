            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; <?php echo APP_NAME; ?> <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?php echo BASE_URL; ?>auth/logout">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <!-- Chart.js is loaded per-page where needed (v3.9.1) -->
    <!-- <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/chart.js/Chart.min.js"></script> -->
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    
    <!-- Safe DataTables Initialization -->
    <script>
    (function() {
        function safeInitDataTables() {
            if (typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
                return;
            }
            
            var $ = jQuery;
            
            // Initialize tables with proper error handling
            $('table[id*="Table"], table[id*="DataTable"], table.data-table').each(function() {
                try {
                    var $table = $(this);
                    var tableId = $table.attr('id') || 'unnamed';
                    
                    // Skip if table has data-skip-datatable or data-no-datatable attribute
                    if ($table.attr('data-skip-datatable') || $table.attr('data-no-datatable') || $table.data('skip-datatable') || $table.data('no-datatable')) {
                        // Silently skip custom tables (no console message needed)
                        return;
                    }
                    
                    // Skip if already initialized
                    if ($.fn.DataTable.isDataTable($table)) {
                        return;
                    }
                    
                    // Skip if this table previously failed initialization
                    if ($table.data('datatable-error')) {
                        return;
                    }
                    
                    // Check if table has proper structure
                    var thead = $table.find('thead');
                    var tbody = $table.find('tbody');
                    
                    if (thead.length === 0) {
                        return; // No thead, skip
                    }
                    
                    var headerCells = thead.find('th');
                    if (headerCells.length === 0) {
                        return; // No header cells, skip
                    }
                    
                    // Check if tbody exists
                    if (tbody.length === 0) {
                        // Create empty tbody if missing
                        $table.append('<tbody></tbody>');
                        tbody = $table.find('tbody');
                    }
                    
                    // Validate row structure - check if all rows have same number of cells as headers
                    var headerCount = headerCells.length;
                    var rows = tbody.find('tr');
                    var isValid = true;
                    var fixedRows = false;
                    
                    // Fix rows with incorrect cell counts
                    rows.each(function() {
                        var $row = $(this);
                        var cells = $row.find('td, th');
                        var cellCount = cells.length;
                        
                        // Check for colspan which would affect cell count
                        var totalColspan = 0;
                        cells.each(function() {
                            var colspan = parseInt($(this).attr('colspan') || '1', 10);
                            totalColspan += colspan;
                        });
                        
                        // If row has wrong number of cells and no colspan, fix it
                        if (cellCount !== headerCount && totalColspan === cellCount) {
                            // Row might have colspan, check if total matches
                            if (totalColspan !== headerCount) {
                                // Fix by adding or removing cells
                                if (cellCount < headerCount) {
                                    // Add missing cells
                                    for (var i = cellCount; i < headerCount; i++) {
                                        $row.append('<td></td>');
                                    }
                                    fixedRows = true;
                                } else if (cellCount > headerCount) {
                                    // Remove extra cells
                                    cells.slice(headerCount).remove();
                                    fixedRows = true;
                                }
                            }
                        } else if (cellCount !== headerCount && totalColspan === cellCount) {
                            // No colspan but wrong count
                            if (cellCount < headerCount) {
                                for (var i = cellCount; i < headerCount; i++) {
                                    $row.append('<td></td>');
                                }
                                fixedRows = true;
                            } else if (cellCount > headerCount) {
                                cells.slice(headerCount).remove();
                                fixedRows = true;
                            }
                        }
                        
                        // Final validation - ensure all cells exist
                        var finalCells = $row.find('td, th');
                        if (finalCells.length !== headerCount) {
                            isValid = false;
                            return false;
                        }
                        
                        // Ensure no undefined cells
                        finalCells.each(function(index) {
                            if (!this || !this.nodeName) {
                                isValid = false;
                                return false;
                            }
                        });
                        
                        if (!isValid) {
                            return false;
                        }
                    });
                    
                    if (!isValid) {
                        console.warn('DataTable: Table structure invalid for', tableId, '- skipping initialization');
                        return;
                    }
                    
                    if (fixedRows) {
                        console.info('DataTable: Fixed row structure for', tableId);
                    }
                    
                    // Additional safety check - ensure table is visible and has dimensions
                    // Skip modal tables as they may not have dimensions yet
                    if ($table.closest('.modal').length > 0) {
                        // Silently skip modal tables
                        return;
                    }
                    
                    if ($table.is(':hidden') || $table.width() === 0) {
                        // Silently skip hidden tables
                        return;
                    }
                    
                    // Initialize DataTable with error handling and columnDefs to handle edge cases
                    try {
                        // Wrap in additional try-catch for cell index errors
                        var dtOptions = {
                            pageLength: 10,
                            ordering: true,
                            searching: true,
                            responsive: true,
                            autoWidth: false,
                            destroy: false,
                            retrieve: true,
                            columnDefs: [
                                {
                                    // Default renderer for all columns to handle undefined/null
                                    render: function(data, type, row, meta) {
                                        if (data === null || data === undefined || data === '') {
                                            return '';
                                        }
                                        return String(data);
                                    },
                                    targets: '_all'
                                }
                            ],
                            // Error handling
                            error: function(xhr, error, thrown) {
                                console.error('DataTable error for', tableId, ':', error, thrown);
                            }
                        };
                        
                        // Additional validation before initialization
                        var allRowsValid = true;
                        rows.each(function() {
                            var $row = $(this);
                            var cells = $row.find('td, th');
                            if (cells.length !== headerCount) {
                                allRowsValid = false;
                                return false;
                            }
                            // Check each cell exists
                            cells.each(function() {
                                if (!this || !this.nodeName) {
                                    allRowsValid = false;
                                    return false;
                                }
                            });
                            if (!allRowsValid) {
                                return false;
                            }
                        });
                        
                        if (!allRowsValid) {
                            console.warn('DataTable: Invalid row structure detected for', tableId, '- skipping');
                            return;
                        }
                        
                        $table.DataTable(dtOptions);
                    } catch (dtError) {
                        console.error('DataTable initialization failed for', tableId, ':', dtError);
                        // Mark table to prevent retry
                        $table.data('datatable-error', true);
                        // Don't rethrow - just log and continue
                    }
                    
                } catch (error) {
                    console.error('DataTable setup error for table:', error);
                    // Prevent error from breaking the page
                }
            });
        }
        
        // Initialize after jQuery and DataTables are loaded with multiple attempts
        function initWithRetry(attempts) {
            attempts = attempts || 0;
            if (attempts > 3) {
                console.warn('DataTable initialization: Max retries reached');
                return;
            }
            
            if (typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
                setTimeout(function() {
                    initWithRetry(attempts + 1);
                }, 200);
                return;
            }
            
            try {
                safeInitDataTables();
            } catch (e) {
                console.error('DataTable initialization error:', e);
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    initWithRetry(0);
                }, 100);
            });
        } else {
            setTimeout(function() {
                initWithRetry(0);
            }, 100);
        }
    })();
    </script>

    <!-- SweetAlert2 for beautiful notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" crossorigin="anonymous" onerror="console.warn('SweetAlert2 failed to load from CDN')"></script>

    <!-- UphoCare Custom JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/uphocare.js?v=<?php echo time(); ?>" crossorigin="anonymous"></script>

    <!-- Mobile Sidebar Toggle -->
    <script>
    // Enhanced sidebar toggle function that works with or without jQuery
    // Make it globally accessible
    window.toggleSidebar = function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        const body = document.body;
        const sidebar = document.querySelector('.sidebar') || document.querySelector('#accordionSidebar');
        const backdrop = document.querySelector('.sidebar-backdrop');
        
        if (!sidebar) {
            return false;
        }
        
        // Check current state
        const isCurrentlyToggled = body.classList.contains('sidebar-toggled');
        
        // Toggle the sidebar-toggled class on body
        if (isCurrentlyToggled) {
            body.classList.remove('sidebar-toggled');
            sidebar.classList.remove('toggled');
        } else {
            body.classList.add('sidebar-toggled');
            sidebar.classList.add('toggled');
        }
        
        // Force sidebar visibility on mobile
        if (window.innerWidth <= 991.98) {
            if (!isCurrentlyToggled) {
                // Opening sidebar - ensure it's fully visible and not transparent
                sidebar.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; height: 100vh !important; width: 14rem !important; z-index: 1060 !important; display: block !important; visibility: visible !important; opacity: 1 !important; transform: translateX(0) !important; background: linear-gradient(180deg, #1a252f 0%, #2c3e50 100%) !important; background-color: #1a252f !important;';
                
                // Ensure all child elements are also opaque
                var sidebarChildren = sidebar.querySelectorAll('*');
                for (var i = 0; i < sidebarChildren.length; i++) {
                    sidebarChildren[i].style.opacity = '1';
                }
                
                if (backdrop) {
                    backdrop.style.cssText = 'display: block !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background-color: rgba(0, 0, 0, 0.5) !important; z-index: 1050 !important; opacity: 1 !important;';
                }
            } else {
                // Closing sidebar
                sidebar.style.left = '-14rem';
                sidebar.style.transform = 'translateX(-14rem)';
                if (backdrop) {
                    backdrop.style.display = 'none';
                    backdrop.style.opacity = '0';
                }
            }
        }
        
        return false;
    };
    
    // Initialize sidebar toggle functionality
    (function() {
        let sidebarInitialized = false;
        let isToggling = false; // Prevent multiple simultaneous toggles
        
        function initSidebar() {
            if (sidebarInitialized) return; // Prevent multiple initializations
            
            const sidebar = document.querySelector('.sidebar') || document.querySelector('#accordionSidebar');
            const backdrop = document.querySelector('.sidebar-backdrop');
            const toggleBtn = document.getElementById('sidebarToggleTop');
            const toggleBtnBottom = document.getElementById('sidebarToggle');
            
            if (!sidebar) {
                if (!sidebarInitialized) {
                    setTimeout(initSidebar, 100);
                }
                return;
            }
            
            sidebarInitialized = true;
            
            // Ensure sidebar is hidden on mobile by default but not transparent
            if (window.innerWidth <= 991.98) {
                sidebar.style.cssText = 'position: fixed !important; top: 0 !important; left: -14rem !important; height: 100vh !important; width: 14rem !important; z-index: 1060 !important; display: block !important; visibility: visible !important; opacity: 1 !important; transition: left 0.3s ease, transform 0.3s ease !important; background: linear-gradient(180deg, #1a252f 0%, #2c3e50 100%) !important; background-color: #1a252f !important;';
                
                // Ensure all child elements are opaque
                var allChildren = sidebar.querySelectorAll('*');
                for (var i = 0; i < allChildren.length; i++) {
                    allChildren[i].style.opacity = '1';
                }
            }
            
            if (backdrop) {
                backdrop.style.cssText = 'display: none !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background-color: rgba(0, 0, 0, 0.5) !important; z-index: 1050 !important; opacity: 0 !important; transition: opacity 0.3s ease !important;';
            }
            
            // Add click handler to backdrop - close sidebar
            if (backdrop) {
                backdrop.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!isToggling && window.innerWidth <= 991.98 && document.body.classList.contains('sidebar-toggled')) {
                        isToggling = true;
                        window.toggleSidebar(e);
                        setTimeout(() => { isToggling = false; }, 300);
                    }
                }, { once: false, passive: false });
            }
            
            // Add click handlers to toggle buttons - use once flag to prevent duplicates
            if (toggleBtn) {
                // Remove any existing onclick
                toggleBtn.removeAttribute('onclick');
                
                // Use a flag to prevent duplicate handlers
                if (!toggleBtn.dataset.handlerAttached) {
                    toggleBtn.dataset.handlerAttached = 'true';
                    toggleBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        if (!isToggling) {
                            isToggling = true;
                            window.toggleSidebar(e);
                            setTimeout(() => { isToggling = false; }, 300);
                        }
                        return false;
                    }, { capture: true, passive: false });
                }
            }
            
            if (toggleBtnBottom) {
                toggleBtnBottom.removeAttribute('onclick');
                if (!toggleBtnBottom.dataset.handlerAttached) {
                    toggleBtnBottom.dataset.handlerAttached = 'true';
                    toggleBtnBottom.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        if (!isToggling) {
                            isToggling = true;
                            window.toggleSidebar(e);
                            setTimeout(() => { isToggling = false; }, 300);
                        }
                        return false;
                    }, { capture: true, passive: false });
                }
            }
            
            // Close sidebar when clicking outside on mobile (with delay to avoid conflicts)
            let clickTimeout;
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 991.98 && !isToggling) {
                    clearTimeout(clickTimeout);
                    clickTimeout = setTimeout(function() {
                        const currentToggleBtn = document.getElementById('sidebarToggleTop');
                        const currentToggleBtnBottom = document.getElementById('sidebarToggle');
                        const currentSidebar = document.querySelector('.sidebar') || document.querySelector('#accordionSidebar');
                        const currentBackdrop = document.querySelector('.sidebar-backdrop');
                        
                        if (currentSidebar && 
                            !currentSidebar.contains(e.target) && 
                            currentToggleBtn && !currentToggleBtn.contains(e.target) &&
                            (!currentToggleBtnBottom || !currentToggleBtnBottom.contains(e.target)) &&
                            currentBackdrop && !currentBackdrop.contains(e.target) &&
                            document.body.classList.contains('sidebar-toggled')) {
                            isToggling = true;
                            window.toggleSidebar();
                            setTimeout(() => { isToggling = false; }, 300);
                        }
                    }, 100);
                }
            }, { passive: true });
            
            // Close sidebar when window is resized to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991.98) {
                    document.body.classList.remove('sidebar-toggled');
                    sidebar.classList.remove('toggled');
                    sidebar.style.left = '';
                    if (backdrop) {
                        backdrop.style.display = 'none';
                    }
                } else {
                    if (!document.body.classList.contains('sidebar-toggled')) {
                        sidebar.style.left = '-14rem';
                    }
                }
            }, { passive: true });
            
            // Also handle jQuery-based toggle if jQuery is available
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    $("#sidebarToggle, #sidebarToggleTop").off('click.sidebar').on('click.sidebar', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        if (!isToggling) {
                            isToggling = true;
                            window.toggleSidebar(e);
                            setTimeout(() => { isToggling = false; }, 300);
                        }
                        return false;
                    });
                });
            }
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebar);
        } else {
            initSidebar();
        }
    })();
    </script>

    <!-- Notifications System -->
    <script>
    $(document).ready(function() {
        // Hide badge initially until notifications are loaded
        $('#notificationBadge').addClass('hide-badge');
        
        // Load notifications on page load
        loadNotifications();
        
        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
        
        // Load notifications when dropdown is opened
        $('#alertsDropdown').on('show.bs.dropdown', function() {
            loadNotifications();
        });
    });
    
    function loadNotifications() {
        // Only load if user is logged in (customer or admin)
        <?php if (isset($user) && isset($user['role'])): ?>
        var role = '<?php echo $user['role']; ?>';
        var url = role === 'admin' ? '<?php echo BASE_URL; ?>admin/getNotifications' : '<?php echo BASE_URL; ?>customer/getNotifications';
        var markReadUrl = role === 'admin' ? '<?php echo BASE_URL; ?>admin/markNotificationRead' : '<?php echo BASE_URL; ?>customer/markNotificationRead';
        
        $.ajax({
            url: url + '?t=' + new Date().getTime(),
            method: 'GET',
            dataType: 'json',
            cache: false,
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            },
            success: function(response) {
                if (response.success) {
                    updateNotificationBadge(response.unread_count);
                    renderNotifications(response.notifications, markReadUrl);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading notifications:', error);
                $('#notificationsList').html('<div class="dropdown-item text-center text-muted">Unable to load notifications</div>');
            }
        });
        <?php endif; ?>
    }
    
    function updateNotificationBadge(count) {
        const badge = $('#notificationBadge');
        const bellLink = $('#alertsDropdown');
        
        if (count > 0) {
            // Show badge with count
            badge.text(count > 99 ? '99+' : count).removeClass('hide-badge');
            // Add has-notifications class to bell icon for animation
            bellLink.addClass('has-notifications');
        } else {
            // Hide badge when no notifications
            badge.text('0').addClass('hide-badge');
            // Remove has-notifications class
            bellLink.removeClass('has-notifications');
        }
    }
    
    function renderNotifications(notifications, markReadUrl) {
        const list = $('#notificationsList');
        
        if (notifications.length === 0) {
            list.html('<div class="dropdown-item text-center text-muted">No notifications</div>');
            $('#showAllNotifications').hide();
            return;
        }
        
        let html = '';
        notifications.forEach(function(notif) {
            const iconClass = getNotificationIcon(notif.type);
            const iconBg = getNotificationIconBg(notif.type);
            const isUnread = !notif.is_read;
            
            html += `
                <a class="dropdown-item d-flex align-items-center notification-item ${isUnread ? 'notification-unread' : ''}" 
                   href="#" data-notification-id="${notif.id}" onclick="markAsRead(${notif.id}, this); return false;">
                    <div class="mr-3">
                        <div class="icon-circle ${iconBg}">
                            <i class="${iconClass} text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small text-gray-500">${notif.time_ago}</div>
                        <span class="font-weight-bold">${escapeHtml(notif.title)}</span>
                        <div class="small text-gray-600">${escapeHtml(notif.message)}</div>
                    </div>
                </a>
            `;
        });
        
        list.html(html);
        $('#showAllNotifications').show();
    }
    
    function getNotificationIcon(type) {
        const icons = {
            'success': 'fas fa-check-circle',
            'info': 'fas fa-info-circle',
            'warning': 'fas fa-exclamation-triangle',
            'error': 'fas fa-times-circle'
        };
        return icons[type] || 'fas fa-bell';
    }
    
    function getNotificationIconBg(type) {
        const backgrounds = {
            'success': 'bg-success',
            'info': 'bg-primary',
            'warning': 'bg-warning',
            'error': 'bg-danger'
        };
        return backgrounds[type] || 'bg-primary';
    }
    
    function markAsRead(notificationId, element) {
        var role = '<?php echo isset($user) && isset($user['role']) ? $user['role'] : ''; ?>';
        var url = role === 'admin' ? '<?php echo BASE_URL; ?>admin/markNotificationRead' : '<?php echo BASE_URL; ?>customer/markNotificationRead';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                notification_id: notificationId
            },
            dataType: 'json',
            cache: false,
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            },
            success: function(response) {
                if (response.success) {
                    $(element).removeClass('notification-unread');
                    // Reload notifications to update count
                    loadNotifications();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    </script>

    <style>
    .notification-unread {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .notification-item:hover {
        background-color: #e9ecef;
    }
    
    /* Enhanced notification bell styling */
    .notification-bell-link {
        position: relative;
        padding: 0.75rem 1rem !important;
    }
    
    .notification-bell-link .fa-bell {
        color: #5a5c69;
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }
    
    .notification-bell-link:hover .fa-bell {
        color: #8B4513;
        transform: scale(1.1);
    }
    
    /* Enhanced badge counter styling */
    .badge-counter {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.3rem 0.5rem;
        min-width: 20px;
        height: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e74a3b !important;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 10;
        animation: pulse 2s infinite;
    }
    
    .badge-counter.hide-badge {
        display: none !important;
    }
    
    /* Pulse animation for notification badge */
    @keyframes pulse {
        0% {
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        50% {
            box-shadow: 0 2px 8px rgba(231, 74, 59, 0.6);
        }
        100% {
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    }
    
    /* Make notification icon more prominent when there are unread notifications */
    .notification-bell-link.has-notifications .fa-bell {
        color: #8B4513;
        animation: ring 4s ease-in-out infinite;
    }
    
    @keyframes ring {
        0%, 100% {
            transform: rotate(0deg);
        }
        5%, 15% {
            transform: rotate(-15deg);
        }
        10%, 20% {
            transform: rotate(15deg);
        }
        25% {
            transform: rotate(0deg);
        }
    }
    
    /* Notification dropdown styling */
    #notificationsDropdown .dropdown-header {
        background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
        color: white;
        font-weight: 700;
        padding: 1rem;
        border-radius: 0.35rem 0.35rem 0 0;
    }
    
    #notificationsDropdown {
        min-width: 350px;
        max-width: 400px;
        border-radius: 0.35rem;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    #notificationsDropdown .icon-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Mobile responsive */
    @media (max-width: 576px) {
        #notificationsDropdown {
            min-width: 300px;
        }
    }
    </style>

</body>

</html>

