/**
 * Global Business Mode Handler
 * Handles sidebar visibility and business mode state across all pages
 */

// Check business mode on page load (only on profile page)
document.addEventListener("DOMContentLoaded", function () {
  // Only apply business mode on profile page
  if (window.location.pathname.includes("/customer/profile")) {
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
  const isProfilePage = window.location.pathname.includes("/customer/profile");

  console.log("Business mode status:", isBusinessMode);
  console.log("Is profile page:", isProfilePage);

  if (isBusinessMode && isProfilePage) {
    hideSidebar();
    console.log(
      "✅ Applied business mode - sidebar hidden (profile page only)"
    );
  } else {
    showSidebar();
    console.log("✅ Applied local mode - sidebar shown");
  }
}

// Function to hide sidebar
function hideSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const contentWrapper = document.querySelector("#content-wrapper");

  if (sidebar) {
    sidebar.style.display = "none";
  }

  if (contentWrapper) {
    contentWrapper.style.marginLeft = "0";
    contentWrapper.style.width = "100%";
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

  if (sidebar) {
    sidebar.style.display = "block";
    sidebar.style.visibility = "visible";
  }

  if (contentWrapper) {
    contentWrapper.style.marginLeft = "";
    contentWrapper.style.width = "";
  }

  // Remove business mode class from body
  if (document.body) {
    document.body.classList.remove("business-mode");
  }

  // Force sidebar to be visible
  if (sidebar) {
    sidebar.style.setProperty("display", "block", "important");
  }
}

// Function to set business mode (used by profile page)
function setBusinessMode(enabled) {
  // Only work on profile page
  if (!window.location.pathname.includes("/customer/profile")) {
    console.log("Business mode only works on profile page");
    return;
  }

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

.business-mode #content-wrapper {
    margin-left: 0 !important;
    width: 100% !important;
}

/* Smooth transitions */
.sidebar, #content-wrapper {
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
