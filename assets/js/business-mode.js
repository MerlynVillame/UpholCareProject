// Check business mode on page load
document.addEventListener("DOMContentLoaded", function () {
  // Apply business mode on relevant pages
  const businessPages = [
    "/customer/profile",
    "/customer/business_bookings",
    "/customer/newBusinessReservation",
    "/customer/businessHistory",
  ];

  const isBusinessPage = businessPages.some((page) =>
    window.location.pathname.includes(page)
  );

  if (isBusinessPage) {
    checkBusinessMode();
  }

  // Add business mode indicator after DOM is loaded
  if (document.body) {
    // Check if indicator already exists
    if (!document.querySelector(".business-mode-indicator")) {
      const indicator = document.createElement("div");
      indicator.className = "business-mode-indicator";
      indicator.innerHTML = '<i class="fas fa-briefcase"></i> Business Mode';
      document.body.appendChild(indicator);
    }
  }
});

// Function to check and apply business mode
function checkBusinessMode() {
  const isBusinessMode = sessionStorage.getItem("businessMode") === "true";
  
  if (isBusinessMode) {
    hideSidebar();
    console.log("✅ Applied business mode - sidebar hidden");
  } else {
    showSidebar();
    console.log("✅ Applied local mode - sidebar shown");
  }
}

// Function to hide sidebar
function hideSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const contentWrapper = document.querySelector("#content-wrapper");
  const topbar = document.querySelector(".topbar");
  const footer = document.querySelector(".sticky-footer");

  if (sidebar) {
    sidebar.style.setProperty("display", "none", "important");
  }

  if (contentWrapper) {
    contentWrapper.style.setProperty("margin-left", "0", "important");
    contentWrapper.style.setProperty("width", "100%", "important");
  }
  
  if (topbar) {
    topbar.style.setProperty("width", "100%", "important");
    topbar.style.setProperty("margin-left", "0", "important");
    topbar.style.setProperty("left", "0", "important");
  }

  if (footer) {
    footer.style.setProperty("width", "100%", "important");
    footer.style.setProperty("margin-left", "0", "important");
    footer.style.setProperty("left", "0", "important");
  }

  // Add business mode class to body
  if (document.body) {
    document.body.classList.add("business-mode");
  }
}

// Function to show sidebar
function showSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const contentWrapper = document.querySelector("#content-wrapper");
  const topbar = document.querySelector(".topbar");
  const footer = document.querySelector(".sticky-footer");

  if (sidebar) {
    sidebar.style.setProperty("display", "block", "important");
    sidebar.style.visibility = "visible";
  }

  if (contentWrapper) {
    contentWrapper.style.marginLeft = "";
    contentWrapper.style.width = "";
  }
  
  if (topbar) {
    topbar.style.width = "";
    topbar.style.marginLeft = "";
    topbar.style.left = "";
  }

  if (footer) {
    footer.style.width = "";
    footer.style.marginLeft = "";
    footer.style.left = "";
  }

  // Remove business mode class from body
  if (document.body) {
    document.body.classList.remove("business-mode");
  }
}

// Function to set business mode
function setBusinessMode(enabled) {
  if (enabled) {
    sessionStorage.setItem("businessMode", "true");
    hideSidebar();
    console.log("🔒 Sidebar hidden - Business Mode active");
  } else {
    sessionStorage.setItem("businessMode", "false");
    showSidebar();
    console.log("👁️ Sidebar shown - Local Mode active");
  }
}

// Listen for storage changes (if user opens multiple tabs)
window.addEventListener("storage", function (e) {
  if (e.key === "businessMode") {
    checkBusinessMode();
  }
});

// Add CSS for business mode
const businessModeCSS = `
<style>
/* Business mode styles */
.business-mode .sidebar {
    display: none !important;
}

.business-mode #wrapper #content-wrapper,
.business-mode .topbar,
.business-mode .sticky-footer {
    margin-left: 0 !important;
    padding-left: 0 !important;
    width: 100% !important;
    left: 0 !important;
    right: 0 !important;
}

/* Smooth transitions */
.sidebar, #content-wrapper, .topbar, .sticky-footer {
    transition: all 0.3s ease;
}

/* Business mode indicator */
.business-mode-indicator {
    position: fixed;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    z-index: 9999;
    display: none;
}

.business-mode .business-mode-indicator {
    display: block;
}
</style>
`;

// Inject CSS
if (document.head) {
  document.head.insertAdjacentHTML("beforeend", businessModeCSS);
}
