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

    <!-- Custom Alert/Confirm/Prompt Modal -->
    <div class="modal fade" id="customDialogModal" tabindex="-1" role="dialog" aria-labelledby="customDialogModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="modal-header" id="customDialogHeader" style="border-bottom: 2px solid #e3e6f0;">
                    <h5 class="modal-title" id="customDialogModalLabel" style="font-weight: 600;">
                        <i class="fas" id="customDialogIcon"></i>
                        <span id="customDialogTitle"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="customDialogCloseBtn" style="display: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <p id="customDialogMessage" style="margin: 0; font-size: 1rem; line-height: 1.6;"></p>
                    <div id="customDialogInputContainer" style="display: none; margin-top: 1rem;">
                        <input type="text" class="form-control" id="customDialogInput" placeholder="Enter your response...">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 2px solid #e3e6f0;">
                    <button type="button" class="btn btn-secondary" id="customDialogCancelBtn" data-dismiss="modal" style="display: none;">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="button" class="btn" id="customDialogOkBtn">
                        <i class="fas fa-check mr-1"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>

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
    
    <!-- Custom Dialog System (Replaces alert, confirm, prompt) -->
    <script>
    (function() {
        'use strict';
        
        // Wait for DOM and Bootstrap to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Store original functions as fallback
            const originalAlert = window.alert;
            const originalConfirm = window.confirm;
            const originalPrompt = window.prompt;
            
            // Helper function to show modal
            function showModal(modalEl) {
                if (!modalEl) return;
                
                // CRITICAL FIX: Set aria-hidden to false BEFORE showing modal
                modalEl.setAttribute('aria-hidden', 'false');
                
                // Remove aria-hidden from wrapper if modal is inside it
                const wrapper = document.getElementById('wrapper');
                if (wrapper && wrapper.contains(modalEl)) {
                    wrapper.removeAttribute('aria-hidden');
                }
                
                // CRITICAL: If this is the custom dialog modal, force it to highest z-index
                if (modalEl.id === 'customDialogModal') {
                    // Ensure modal is appended to body (not nested inside other modals)
                    if (modalEl.parentElement !== document.body) {
                        document.body.appendChild(modalEl);
                    }
                    modalEl.classList.add('confirmation-modal');
                    modalEl.style.zIndex = '99999';
                    modalEl.style.position = 'fixed';
                }
                
                // Set up event listeners to manage aria-hidden
                const handleShown = function() {
                    modalEl.setAttribute('aria-hidden', 'false');
                    if (wrapper) wrapper.removeAttribute('aria-hidden');
                    
                    // CRITICAL: Ensure custom dialog modal is always on top when shown
                    if (modalEl.id === 'customDialogModal') {
                        // Ensure modal is appended to body
                        if (modalEl.parentElement !== document.body) {
                            document.body.appendChild(modalEl);
                        }
                        modalEl.classList.add('confirmation-modal');
                        modalEl.style.zIndex = '99999';
                        modalEl.style.position = 'fixed';
                        
                        // Update backdrop z-index - add confirmation-backdrop class
                        setTimeout(function() {
                            const backdrops = document.querySelectorAll('.modal-backdrop.show');
                            if (backdrops.length > 0) {
                                // Set the last backdrop (for custom dialog) to highest
                                const lastBackdrop = backdrops[backdrops.length - 1];
                                lastBackdrop.classList.add('confirmation-backdrop');
                                lastBackdrop.style.zIndex = '99998';
                            }
                        }, 50);
                    }
                };
                
                const handleHidden = function() {
                    modalEl.setAttribute('aria-hidden', 'true');
                };
                
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    const $modal = jQuery(modalEl);
                    $modal.off('shown.bs.modal hidden.bs.modal');
                    $modal.on('shown.bs.modal', handleShown);
                    $modal.on('hidden.bs.modal', handleHidden);
                    $modal.modal('show');
                } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalEl.addEventListener('shown.bs.modal', handleShown, { once: false });
                    modalEl.addEventListener('hidden.bs.modal', handleHidden, { once: false });
                    modal.show();
                }
            }
            
            // Helper function to hide modal
            function hideModal(modalEl) {
                if (!modalEl) return;
                
                // CRITICAL FIX: Remove focus from any elements inside the modal before hiding
                const focusedElement = modalEl.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }
                
                // Remove any forced styles that might interfere
                modalEl.style.removeProperty('display');
                modalEl.style.removeProperty('opacity');
                modalEl.style.removeProperty('pointer-events');
                
                if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                    jQuery(modalEl).modal('hide');
                } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
            }
            
            // Custom Alert Function
            window.alert = function(message) {
                const modal = document.getElementById('customDialogModal');
                if (!modal) {
                    return originalAlert(message); // Fallback
                }
                
                return new Promise((resolve) => {
                    const title = document.getElementById('customDialogTitle');
                    const messageEl = document.getElementById('customDialogMessage');
                    const icon = document.getElementById('customDialogIcon');
                    const header = document.getElementById('customDialogHeader');
                    const okBtn = document.getElementById('customDialogOkBtn');
                    const cancelBtn = document.getElementById('customDialogCancelBtn');
                    const closeBtn = document.getElementById('customDialogCloseBtn');
                    const inputContainer = document.getElementById('customDialogInputContainer');
                    
                    // Reset modal
                    inputContainer.style.display = 'none';
                    cancelBtn.style.display = 'none';
                    closeBtn.style.display = 'none';
                    
                    // Set content
                    title.textContent = 'Notification';
                    messageEl.textContent = message;
                    icon.className = 'fas fa-info-circle mr-2';
                    icon.style.color = '#17a2b8';
                    header.style.background = 'linear-gradient(135deg, #17a2b8 0%, #138496 100%)';
                    header.style.color = 'white';
                    okBtn.className = 'btn btn-info';
                    okBtn.innerHTML = '<i class="fas fa-check mr-1"></i>OK';
                    
                    // Remove any existing event listeners first by cloning the button
                    const newOkBtn = okBtn.cloneNode(true);
                    okBtn.parentNode.replaceChild(newOkBtn, okBtn);
                    const freshOkBtn = document.getElementById('customDialogOkBtn');
                    
                    // Setup OK button with fresh event listener
                    // Use both onclick and addEventListener for maximum compatibility
                    freshOkBtn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        hideModal(modal);
                        resolve(true);
                        return false;
                    };
                    
                    // Also add event listener as backup
                    freshOkBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        hideModal(modal);
                        resolve(true);
                    }, { once: true });
                    
                    // Ensure button is visible and clickable
                    freshOkBtn.style.pointerEvents = 'auto';
                    freshOkBtn.style.cursor = 'pointer';
                    freshOkBtn.disabled = false;
                    
                    // Show modal (showModal helper already handles aria-hidden)
                    showModal(modal);
                    
                    // Ensure button is always clickable - add inline styles
                    setTimeout(() => {
                        if (freshOkBtn && document.body.contains(freshOkBtn)) {
                            // Force button to be clickable
                            freshOkBtn.style.pointerEvents = 'auto';
                            freshOkBtn.style.cursor = 'pointer';
                            // Ensure modal aria-hidden is false (showModal already handles this, but double-check)
                            modal.setAttribute('aria-hidden', 'false');
                            const wrapper = document.getElementById('wrapper');
                            if (wrapper) wrapper.removeAttribute('aria-hidden');
                            freshOkBtn.style.zIndex = '9999';
                            freshOkBtn.style.position = 'relative';
                            freshOkBtn.disabled = false;
                            
                            // Focus the OK button (for keyboard accessibility)
                            freshOkBtn.focus();
                            
                            // Add a direct mousedown handler as additional fallback
                            freshOkBtn.addEventListener('mousedown', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                hideModal(modal);
                                resolve(true);
                            }, { once: true, passive: false });
                            
                            // Also handle Enter key on the button
                            freshOkBtn.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter' || e.key === ' ') {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    hideModal(modal);
                                    resolve(true);
                                }
                            }, { once: true });
                        }
                    }, 100);
                });
            };
            
            // Custom Confirm Function
            window.confirm = function(message) {
                const modal = document.getElementById('customDialogModal');
                if (!modal) {
                    return originalConfirm(message); // Fallback
                }
                
                return new Promise((resolve) => {
                    // CRITICAL: Ensure modal is appended to body, not nested inside other modals
                    if (modal.parentElement !== document.body) {
                        document.body.appendChild(modal);
                    }
                    
                    // Add confirmation-modal class for CSS targeting
                    modal.classList.add('confirmation-modal');
                    
                    const title = document.getElementById('customDialogTitle');
                    const messageEl = document.getElementById('customDialogMessage');
                    const icon = document.getElementById('customDialogIcon');
                    const header = document.getElementById('customDialogHeader');
                    const okBtn = document.getElementById('customDialogOkBtn');
                    const cancelBtn = document.getElementById('customDialogCancelBtn');
                    const closeBtn = document.getElementById('customDialogCloseBtn');
                    const inputContainer = document.getElementById('customDialogInputContainer');
                    
                    // Reset modal
                    inputContainer.style.display = 'none';
                    closeBtn.style.display = 'none';
                    cancelBtn.style.display = 'inline-block';
                    
                    // Set content
                    title.textContent = 'Confirmation';
                    messageEl.textContent = message;
                    icon.className = 'fas fa-question-circle mr-2';
                    icon.style.color = '#ffc107';
                    header.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
                    header.style.color = '#212529';
                    okBtn.className = 'btn btn-warning';
                    okBtn.innerHTML = '<i class="fas fa-check mr-1"></i>OK';
                    cancelBtn.className = 'btn btn-secondary';
                    
                    // Remove any existing event listeners first by cloning buttons
                    const newOkBtn = okBtn.cloneNode(true);
                    const newCancelBtn = cancelBtn.cloneNode(true);
                    okBtn.parentNode.replaceChild(newOkBtn, okBtn);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                    const freshOkBtn = document.getElementById('customDialogOkBtn');
                    const freshCancelBtn = document.getElementById('customDialogCancelBtn');
                    
                    // Setup buttons with fresh event listeners
                    freshOkBtn.onclick = function() {
                        hideModal(modal);
                        resolve(true);
                    };
                    
                    freshCancelBtn.onclick = function() {
                        hideModal(modal);
                        resolve(false);
                    };
                    
                    // Show modal (showModal helper already handles aria-hidden and z-index)
                    showModal(modal);
                    
                    // Additional z-index enforcement after modal is shown
                    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                        jQuery(modal).on('shown.bs.modal', function() {
                            modal.style.zIndex = '99999';
                            const backdrops = document.querySelectorAll('.modal-backdrop');
                            if (backdrops.length > 0) {
                                const lastBackdrop = backdrops[backdrops.length - 1];
                                lastBackdrop.classList.add('confirmation-backdrop');
                                lastBackdrop.style.zIndex = '99998';
                            }
                        });
                    }
                });
            };
            
            // Custom Prompt Function
            window.prompt = function(message, defaultValue = '') {
                const modal = document.getElementById('customDialogModal');
                if (!modal) {
                    return originalPrompt(message, defaultValue); // Fallback
                }
                
                return new Promise((resolve) => {
                    const title = document.getElementById('customDialogTitle');
                    const messageEl = document.getElementById('customDialogMessage');
                    const icon = document.getElementById('customDialogIcon');
                    const header = document.getElementById('customDialogHeader');
                    const okBtn = document.getElementById('customDialogOkBtn');
                    const cancelBtn = document.getElementById('customDialogCancelBtn');
                    const closeBtn = document.getElementById('customDialogCloseBtn');
                    const inputContainer = document.getElementById('customDialogInputContainer');
                    const input = document.getElementById('customDialogInput');
                    
                    // Reset modal
                    closeBtn.style.display = 'none';
                    cancelBtn.style.display = 'inline-block';
                    inputContainer.style.display = 'block';
                    
                    // Set content
                    title.textContent = 'Input Required';
                    messageEl.textContent = message;
                    icon.className = 'fas fa-keyboard mr-2';
                    icon.style.color = '#007bff';
                    header.style.background = 'linear-gradient(135deg, #007bff 0%, #0056b3 100%)';
                    header.style.color = 'white';
                    okBtn.className = 'btn btn-primary';
                    okBtn.innerHTML = '<i class="fas fa-check mr-1"></i>OK';
                    cancelBtn.className = 'btn btn-secondary';
                    input.value = defaultValue || '';
                    
                    // Setup buttons
                    const handleOk = () => {
                        const value = input.value;
                        hideModal(modal);
                        okBtn.onclick = null;
                        cancelBtn.onclick = null;
                        input.onkeypress = null;
                        resolve(value);
                    };
                    
                    const handleCancel = () => {
                        hideModal(modal);
                        okBtn.onclick = null;
                        cancelBtn.onclick = null;
                        input.onkeypress = null;
                        resolve(null);
                    };
                    
                    const handleEnter = (e) => {
                        if (e.key === 'Enter') {
                            handleOk();
                        }
                    };
                    
                    okBtn.onclick = handleOk;
                    cancelBtn.onclick = handleCancel;
                    input.onkeypress = handleEnter;
                    
                    // Focus input
                    setTimeout(() => {
                        input.focus();
                        input.select();
                    }, 500);
                    
                    // Show modal (showModal helper already handles aria-hidden)
                    showModal(modal);
                });
            };
            
            // Helper function for synchronous-style confirm (for backward compatibility)
            window.syncConfirm = function(message) {
                let result = false;
                let resolved = false;
                
                confirm(message).then((value) => {
                    result = value;
                    resolved = true;
                });
                
                // Wait for result (this is a workaround - not truly synchronous)
                const startTime = Date.now();
                while (!resolved && (Date.now() - startTime < 100)) {
                    // Small delay to allow promise to resolve
                }
                
                return result;
            };
            
            console.log('Custom dialog system loaded. alert(), confirm(), and prompt() have been replaced with modal dialogs.');
        });
    })();
    </script>
    
    <!-- Modal Stacking Handler - Dynamic z-index calculation for stacked modals -->
    <script>
    // Wait for jQuery to be available, then initialize modal stacking
    (function() {
        function initModalStacking() {
            if (typeof jQuery === 'undefined' || !jQuery.fn.modal) {
                // Retry after a short delay if jQuery isn't loaded yet
                setTimeout(initModalStacking, 100);
                return;
            }
            
            var $ = jQuery;
            
            // This makes the stacking dynamic (clean solution)
            $(document).on('show.bs.modal', '.modal', function() {
                var $modal = $(this);
                
                // CRITICAL: Custom dialog modal always gets highest z-index
                if ($modal.attr('id') === 'customDialogModal') {
                    $modal.css({
                        'z-index': '5000',
                        'position': 'fixed'
                    });
                    
                    setTimeout(function() {
                        $('.modal-backdrop.show').last().css('z-index', '4990');
                    }, 0);
                } else {
                    // Other modals get dynamic z-index
                    var zIndex = 2000 + ($('.modal:visible').length * 20);
                    $modal.css('z-index', zIndex);
                    
                    setTimeout(function() {
                        $('.modal-backdrop').not('.modal-stack')
                            .css('z-index', zIndex - 10)
                            .addClass('modal-stack');
                    }, 0);
                }
            });
            
            // Also handle when modal is fully shown
            $(document).on('shown.bs.modal', '.modal', function() {
                var $modal = $(this);
                
                // Ensure custom dialog modal is always on top
                if ($modal.attr('id') === 'customDialogModal') {
                    $modal.css({
                        'z-index': '5000',
                        'position': 'fixed'
                    });
                    
                    setTimeout(function() {
                        $('.modal-backdrop.show').last().css('z-index', '4990');
                    }, 50);
                }
            });
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initModalStacking);
        } else {
            initModalStacking();
        }
    })();
    </script>
    
    <!-- Compatibility Helper: Make confirm work with existing code -->
    <script>
    // Override confirm to work with both async and sync patterns
    (function() {
        const originalConfirm = window.confirm;
        window.confirm = function(message) {
            // Check if we're in an async context
            const stack = new Error().stack;
            const isAsync = stack.includes('async') || stack.includes('await');
            
            if (isAsync) {
                // Use Promise-based confirm
                return originalConfirm(message);
            } else {
                // For synchronous code, show modal and return immediately
                // Note: This won't truly block, but will show the modal
                let result = false;
                originalConfirm(message).then((value) => {
                    result = value;
                });
                // Return false initially, the callback will handle the action
                return false;
            }
        };
    })();
    </script>

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
                        var hasColspan = false;
                        cells.each(function() {
                            var colspan = parseInt($(this).attr('colspan') || '1', 10);
                            totalColspan += colspan;
                            if (colspan > 1) {
                                hasColspan = true;
                            }
                        });
                        
                        // If row has colspan and total colspan matches header count, it's valid
                        if (hasColspan && totalColspan === headerCount) {
                            // Row is valid with colspan, skip fixing
                            return;
                        }
                        
                        // If row has wrong number of cells and no colspan, fix it
                        if (cellCount !== headerCount && !hasColspan) {
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
                        
                        // Final validation - check if row is valid
                        var finalCells = $row.find('td, th');
                        var finalCellCount = finalCells.length;
                        var finalTotalColspan = 0;
                        finalCells.each(function() {
                            finalTotalColspan += parseInt($(this).attr('colspan') || '1', 10);
                        });
                        
                        // Row is valid if: 
                        // 1. Cell count matches header count, OR
                        // 2. Total colspan matches header count (for colspan rows)
                        if (finalCellCount !== headerCount && finalTotalColspan !== headerCount) {
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
            
            // Determine click action based on notification type and related_id
            let clickAction = `markAsRead(${notif.id}, this); return false;`;
            let href = '#';
            
            // If notification is related to a booking, make it clickable to view the booking
            if (notif.related_id && notif.related_type === 'booking') {
                const role = '<?php echo isset($user) && isset($user['role']) ? $user['role'] : ''; ?>';
                if (role === 'customer') {
                    // For customers, navigate to bookings page and open preview receipt if it's a payment receipt notification
                    if (notif.title && notif.title.includes('Payment Receipt')) {
                        href = `<?php echo BASE_URL; ?>customer/bookings`;
                        clickAction = `handleNotificationClick(${notif.id}, ${notif.related_id}, this); return false;`;
                    } else {
                        href = `<?php echo BASE_URL; ?>customer/bookings`;
                        clickAction = `markAsRead(${notif.id}, this); window.location.href='${href}'; return false;`;
                    }
                } else if (role === 'admin') {
                    href = `<?php echo BASE_URL; ?>admin/allBookings`;
                    clickAction = `markAsRead(${notif.id}, this); window.location.href='${href}'; return false;`;
                }
            }
            
            html += `
                <a class="dropdown-item d-flex align-items-center notification-item ${isUnread ? 'notification-unread' : ''}" 
                   href="${href}" data-notification-id="${notif.id}" data-related-id="${notif.related_id || ''}" data-related-type="${notif.related_type || ''}" onclick="${clickAction}">
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
    
    // Handle notification click - mark as read and navigate to booking/preview receipt
    function handleNotificationClick(notificationId, bookingId, element) {
        // Mark as read first
        markAsRead(notificationId, element);
        
        // Navigate to bookings page
        window.location.href = '<?php echo BASE_URL; ?>customer/bookings';
        
        // Store booking ID to open preview receipt after page loads
        sessionStorage.setItem('openPreviewReceipt', bookingId);
    }
    
    // Check if we need to open preview receipt after page load
    $(document).ready(function() {
        const bookingId = sessionStorage.getItem('openPreviewReceipt');
        if (bookingId) {
            sessionStorage.removeItem('openPreviewReceipt');
            // Wait a bit for page to fully load, then open preview receipt
            setTimeout(function() {
                if (typeof viewPreviewReceipt === 'function') {
                    viewPreviewReceipt(bookingId);
                } else {
                    // If function not available yet, wait a bit more
                    setTimeout(function() {
                        if (typeof viewPreviewReceipt === 'function') {
                            viewPreviewReceipt(bookingId);
                        }
                    }, 500);
                }
            }, 500);
        }
    });
    
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
        color: #1F4E79;
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
        color: #1F4E79;
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
        background: linear-gradient(135deg, #0F3C5F 0%, #1F4E79 50%, #4CAF50 100%);
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

