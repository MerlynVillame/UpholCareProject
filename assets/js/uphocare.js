/**
 * UphoCare - Main JavaScript File
 * Enhanced functionality for booking system
 */

// Base URL configuration
const BASE_URL = window.location.origin + "/UphoCare/";

// Wait for jQuery to be loaded before initializing
(function() {
  function initUphoCare() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
      // jQuery not loaded yet, wait a bit and try again
      setTimeout(initUphoCare, 50);
      return;
    }
    
    // jQuery is loaded, proceed with initialization
    var $ = jQuery;
    
    // Document Ready
    $(document).ready(function () {
      // Initialize all components
      initializeDataTables();
      initializeTooltips();
      initializePopovers();
      initializeConfirmDialogs();
      initializeFormValidation();
      initializeAjaxForms();
      initializeDatepickers();
      initializeStatusUpdates();

      // Auto-hide alerts after 5 seconds
      autoHideAlerts();
      
      // Initialize other jQuery-dependent code
      initJQueryDependentCode();
    });
  }
  
  // Start initialization
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUphoCare);
  } else {
    initUphoCare();
  }
})();

/**
 * Initialize DataTables
 */
function initializeDataTables() {
  if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
    var $ = jQuery;
    $(".data-table").DataTable({
      pageLength: 10,
      ordering: true,
      searching: true,
      lengthChange: true,
      info: true,
      autoWidth: false,
      responsive: true,
      language: {
        search: "Search:",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        paginate: {
          first: "First",
          last: "Last",
          next: "Next",
          previous: "Previous",
        },
      },
    });
  }
}

/**
 * Initialize Bootstrap Tooltips
 */
function initializeTooltips() {
  if (typeof jQuery !== 'undefined') {
    var $ = jQuery;
    $('[data-toggle="tooltip"]').tooltip();
  }
}

/**
 * Initialize Bootstrap Popovers
 */
function initializePopovers() {
  if (typeof jQuery !== 'undefined') {
    var $ = jQuery;
    $('[data-toggle="popover"]').popover();
  }
}

/**
 * Initialize Confirm Dialogs
 */
function initializeConfirmDialogs() {
  if (typeof jQuery !== 'undefined') {
    var $ = jQuery;
    $(".confirm-action").on("click", function (e) {
      const message =
        $(this).data("confirm-message") ||
        "Are you sure you want to perform this action?";
      if (!confirm(message)) {
        e.preventDefault();
        return false;
      }
    });
  }
}

/**
 * Auto-hide alerts
 */
function autoHideAlerts() {
  if (typeof jQuery !== 'undefined') {
    var $ = jQuery;
    setTimeout(function () {
      $(".alert:not(.alert-permanent)").fadeOut("slow", function () {
        $(this).remove();
      });
    }, 5000);
  }
}

/**
 * Initialize Form Validation
 */
function initializeFormValidation() {
  if (typeof jQuery !== 'undefined') {
    var $ = jQuery;
    // Add custom validation for forms
    $("form.needs-validation").on("submit", function (e) {
      if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      $(this).addClass("was-validated");
    });

    // Real-time validation for specific fields
    $('input[type="email"]').on("blur", function () {
      validateEmail($(this));
    });

    $('input[type="tel"]').on("blur", function () {
      validatePhone($(this));
    });
  }
}

/**
 * Validate Email
 */
function validateEmail(field) {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  const email = field.val();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (email && !emailRegex.test(email)) {
    field.addClass("is-invalid");
    if (!field.next(".invalid-feedback").length) {
      field.after(
        '<div class="invalid-feedback">Please enter a valid email address.</div>'
      );
    }
  } else {
    field.removeClass("is-invalid");
    field.next(".invalid-feedback").remove();
  }
}

/**
 * Validate Phone
 */
function validatePhone(field) {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  const phone = field.val();
  const phoneRegex = /^[0-9]{10,15}$/;

  if (phone && !phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ""))) {
    field.addClass("is-invalid");
    if (!field.next(".invalid-feedback").length) {
      field.after(
        '<div class="invalid-feedback">Please enter a valid phone number.</div>'
      );
    }
  } else {
    field.removeClass("is-invalid");
    field.next(".invalid-feedback").remove();
  }
}

/**
 * Initialize AJAX Forms
 */
function initializeAjaxForms() {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  $(".ajax-form").on("submit", function (e) {
    e.preventDefault();

    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();

    // Disable button and show loading
    submitBtn.prop("disabled", true);
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    $.ajax({
      url: form.attr("action"),
      method: form.attr("method") || "POST",
      data: form.serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showNotification(
            "success",
            response.message || "Operation completed successfully!"
          );
          if (response.redirect) {
            setTimeout(function () {
              window.location.href = response.redirect;
            }, 1500);
          }
        } else {
          showNotification("error", response.message || "An error occurred.");
        }
      },
      error: function (xhr, status, error) {
        showNotification("error", "An error occurred. Please try again.");
      },
      complete: function () {
        // Re-enable button
        submitBtn.prop("disabled", false);
        submitBtn.html(originalText);
      },
    });
  });
}

/**
 * Initialize Datepickers
 */
function initializeDatepickers() {
  if (typeof jQuery !== 'undefined') {
    var $ = jQuery;
    // Set minimum date to today
    const today = new Date().toISOString().split("T")[0];
    $('input[type="date"]').attr("min", today);
  }
}

/**
 * Initialize Status Updates
 */
function initializeStatusUpdates() {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  $(".status-update-select").on("change", function () {
    const select = $(this);
    const bookingId = select.data("booking-id");
    const newStatus = select.val();

    if (confirm("Are you sure you want to change the status?")) {
      updateBookingStatus(bookingId, newStatus);
    } else {
      // Reset to original value
      select.val(select.data("original-value"));
    }
  });
}

/**
 * Update Booking Status via AJAX
 */
function updateBookingStatus(bookingId, status) {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  $.ajax({
    url: BASE_URL + "admin/updateBookingStatus",
    method: "POST",
    data: {
      booking_id: bookingId,
      status: status,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        showNotification("success", "Status updated successfully!");
        setTimeout(function () {
          location.reload();
        }, 1500);
      } else {
        showNotification(
          "error",
          response.message || "Failed to update status."
        );
      }
    },
    error: function () {
      showNotification("error", "An error occurred while updating status.");
    },
  });
}

/**
 * Show Notification using SweetAlert2
 */
function showNotification(type, message) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      icon: type,
      title: type === "success" ? "Success!" : "Error!",
      text: message,
      timer: 3000,
      showConfirmButton: false,
      toast: true,
      position: "top-end",
    });
  } else {
    // Fallback to alert
    alert(message);
  }
}

/**
 * Filter Bookings by Status
 */
function filterBookings(status) {
  window.location.href = BASE_URL + "customer/bookings?status=" + status;
}

/**
 * Load Service Details
 */
function loadServiceDetails(serviceId) {
  if (!serviceId || typeof jQuery === 'undefined') return;
  var $ = jQuery;
  $.ajax({
    url: BASE_URL + "customer/getServiceDetails/" + serviceId,
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response.success) {
        displayServiceDetails(response.data);
      }
    },
    error: function () {
      console.error("Failed to load service details");
    },
  });
}

/**
 * Display Service Details
 */
function displayServiceDetails(service) {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  $("#servicePrice").text(
    "₱" +
      parseFloat(service.base_price).toLocaleString("en-PH", {
        minimumFractionDigits: 2,
      })
  );
  $("#serviceDays").text(service.estimated_days + " days");
  $("#serviceDescription").text(service.description);
  $("#total_amount").val(service.base_price);
  $("#serviceInfo").slideDown();
}

/**
 * Print Booking Details
 */
function printBooking(bookingId) {
  window.open(BASE_URL + "customer/printBooking/" + bookingId, "_blank");
}

/**
 * Print Receipt
 */
function printReceipt(paymentId) {
  window.open(BASE_URL + "customer/printReceipt/" + paymentId, "_blank");
}

/**
 * Delete Confirmation
 */
function confirmDelete(url, message) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Are you sure?",
      text: message || "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url;
      }
    });
  } else {
    if (confirm(message || "Are you sure you want to delete this?")) {
      window.location.href = url;
    }
  }
  return false;
}

/**
 * Calculate Total Amount
 */
function calculateTotal() {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  let total = 0;
  $(".item-price").each(function () {
    const price = parseFloat($(this).val()) || 0;
    const quantity =
      parseFloat($(this).closest("tr").find(".item-quantity").val()) || 1;
    total += price * quantity;
  });
  $("#totalAmount").text(
    "₱" +
      total.toLocaleString("en-PH", {
        minimumFractionDigits: 2,
      })
  );
  $("#total_amount").val(total);
}

/**
 * Preview Image Before Upload
 */
function previewImage(input, previewId) {
  if (typeof jQuery === 'undefined' || !input.files || !input.files[0]) return;
  var $ = jQuery;
  const reader = new FileReader();
  reader.onload = function (e) {
    $("#" + previewId)
      .attr("src", e.target.result)
      .show();
  };
  reader.readAsDataURL(input.files[0]);
}

/**
 * Toggle Password Visibility
 */
function togglePassword(buttonElement) {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  const input = $(buttonElement).siblings("input");
  const icon = $(buttonElement).find("i");

  if (input.attr("type") === "password") {
    input.attr("type", "text");
    icon.removeClass("fa-eye").addClass("fa-eye-slash");
  } else {
    input.attr("type", "password");
    icon.removeClass("fa-eye-slash").addClass("fa-eye");
  }
}

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
  const textarea = document.createElement("textarea");
  textarea.value = text;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand("copy");
  document.body.removeChild(textarea);

  showNotification("success", "Copied to clipboard!");
}

/**
 * Format Currency
 */
function formatCurrency(amount) {
  return (
    "₱" +
    parseFloat(amount).toLocaleString("en-PH", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
  );
}

/**
 * Format Date
 */
function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: "numeric", month: "short", day: "numeric" };
  return date.toLocaleDateString("en-US", options);
}

/**
 * Export Table to Excel
 */
function exportTableToExcel(tableId, filename) {
  const table = document.getElementById(tableId);
  const html = table.outerHTML;
  const url = "data:application/vnd.ms-excel," + encodeURIComponent(html);
  const link = document.createElement("a");
  link.href = url;
  link.download = filename + ".xls";
  link.click();
}

/**
 * Print Table
 */
function printTable(tableId) {
  const printContents = document.getElementById(tableId).outerHTML;
  const originalContents = document.body.innerHTML;

  document.body.innerHTML =
    "<html><head><title>Print</title></head><body>" +
    printContents +
    "</body></html>";
  window.print();
  document.body.innerHTML = originalContents;
  location.reload();
}

/**
 * Scroll to Top
 */
function scrollToTop() {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  $("html, body").animate({ scrollTop: 0 }, "smooth");
}

/**
 * Load More Items (Pagination)
 */
function loadMore(url, containerId) {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  $.ajax({
    url: url,
    method: "GET",
    beforeSend: function () {
      $("#" + containerId).append(
        '<div class="text-center loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
      );
    },
    success: function (response) {
      $(".loading").remove();
      $("#" + containerId).append(response);
    },
    error: function () {
      $(".loading").remove();
      showNotification("error", "Failed to load more items.");
    },
  });
}

/**
 * Real-time Search
 */
function liveSearch(query, searchUrl, resultsContainer) {
  if (typeof jQuery === 'undefined') return;
  var $ = jQuery;
  if (query.length < 2) {
    $(resultsContainer).empty();
    return;
  }

  $.ajax({
    url: searchUrl,
    method: "GET",
    data: { q: query },
    success: function (response) {
      $(resultsContainer).html(response);
    },
  });
}

/**
 * Initialize jQuery-dependent code (called after jQuery is loaded)
 */
function initJQueryDependentCode() {
  if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
    return;
  }
  
  var $ = jQuery;
  
  /**
   * Initialize Live Search
   */
  $(".live-search-input").on("keyup", function () {
    const query = $(this).val();
    const searchUrl = $(this).data("search-url");
    const resultsContainer = $(this).data("results-container");

    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(function () {
      liveSearch(query, searchUrl, resultsContainer);
    }, 300);
  });

  /**
   * Character Counter for Textareas
   */
  $("textarea[maxlength]").on("input", function () {
    const maxLength = $(this).attr("maxlength");
    const currentLength = $(this).val().length;
    const remaining = maxLength - currentLength;

    let counter = $(this).next(".char-counter");
    if (!counter.length) {
      counter = $('<small class="char-counter text-muted"></small>');
      $(this).after(counter);
    }

    counter.text(remaining + " characters remaining");

    if (remaining < 20) {
      counter.removeClass("text-muted").addClass("text-warning");
    } else {
      counter.removeClass("text-warning").addClass("text-muted");
    }
  });

  /**
   * Auto-resize Textareas
   */
  $("textarea.auto-resize").on("input", function () {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
  });

  /**
   * Initialize Select2 (if available)
   */
  if ($.fn.select2) {
    $(".select2").select2({
      theme: "bootstrap4",
      width: "100%",
    });
  }

  /**
   * Mobile Menu Toggle
   */
  $(".mobile-menu-toggle").on("click", function () {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
  });

  /**
   * Prevent Double Submit
   */
  $("form").on("submit", function () {
    $(this).find('button[type="submit"]').prop("disabled", true);
    setTimeout(
      function () {
        $(this).find('button[type="submit"]').prop("disabled", false);
      }.bind(this),
      3000
    );
  });
}

/**
 * Session Timeout Warning
 */
let sessionTimeout = 3600000; // 1 hour in milliseconds
let warningTimeout = sessionTimeout - 300000; // 5 minutes before session expires

setTimeout(function () {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Session Expiring Soon",
      text: "Your session will expire in 5 minutes. Do you want to continue?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Continue",
      cancelButtonText: "Logout",
    }).then((result) => {
      if (result.isConfirmed) {
        // Refresh session
        $.get(BASE_URL + "auth/refreshSession");
      } else {
        window.location.href = BASE_URL + "auth/logout";
      }
    });
  }
}, warningTimeout);

// Expose functions globally
window.filterBookings = filterBookings;
window.loadServiceDetails = loadServiceDetails;
window.printBooking = printBooking;
window.printReceipt = printReceipt;
window.confirmDelete = confirmDelete;
window.calculateTotal = calculateTotal;
window.previewImage = previewImage;
window.togglePassword = togglePassword;
window.copyToClipboard = copyToClipboard;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.exportTableToExcel = exportTableToExcel;
window.printTable = printTable;
window.scrollToTop = scrollToTop;
window.showNotification = showNotification;
