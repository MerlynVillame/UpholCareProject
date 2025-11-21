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

    <!-- SweetAlert2 for beautiful notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- UphoCare Custom JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/uphocare.js"></script>

    <!-- Mobile Sidebar Toggle -->
    <script>
    // Mobile sidebar toggle function
    function toggleSidebar() {
        if (window.innerWidth <= 991.98) {
            document.body.classList.toggle('sidebar-toggled');
        } else {
            // Desktop behavior - use default SB Admin 2 toggle
            document.body.classList.toggle('sidebar-toggled');
        }
    }
    
    // Close sidebar when clicking backdrop
    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.querySelector('.sidebar-backdrop');
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                if (window.innerWidth <= 991.98) {
                    document.body.classList.remove('sidebar-toggled');
                }
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 991.98) {
                const sidebar = document.querySelector('.sidebar');
                const toggleBtn = document.getElementById('sidebarToggleTop');
                
                if (sidebar && !sidebar.contains(e.target) && 
                    toggleBtn && !toggleBtn.contains(e.target) &&
                    document.body.classList.contains('sidebar-toggled')) {
                    document.body.classList.remove('sidebar-toggled');
                }
            }
        });
        
        // Close sidebar when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991.98) {
                document.body.classList.remove('sidebar-toggled');
            }
        });
    });
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
            url: url,
            method: 'GET',
            dataType: 'json',
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

