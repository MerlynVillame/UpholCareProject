// Debug log to verify script loading
console.log("UpholCare JavaScript loaded successfully!");

// Booking Modal
function showBookingModal() {
  document.getElementById("bookingModal").style.display = "flex";
}

function closeBookingModal() {
  document.getElementById("bookingModal").style.display = "none";
}

// Booking Form Submit
function submitBooking() {
  const serviceType = document.getElementById("serviceType").value;
  const location = document.getElementById("serviceLocation").value;
  const date = document.getElementById("preferredDate").value;
  const description = document.getElementById("serviceDescription").value;

  if (!serviceType || !location || !date || !description) {
    alert("Please fill in all required fields.");
    return;
  }

  alert("Booking submitted successfully!");
  closeBookingModal();
}

// Cart Sidebar
function toggleCart() {
  document.getElementById("cartSidebar").classList.toggle("open");
  document.getElementById("cartOverlay").classList.toggle("show");
}

function closeCart() {
  document.getElementById("cartSidebar").classList.remove("open");
  document.getElementById("cartOverlay").classList.remove("show");
}

function clearCart() {
  document.getElementById("cartItems").innerHTML = `
    <div style="text-align: center; padding: 40px; color: #6b7280">
      <div style="font-size: 48px; margin-bottom: 16px">🛒</div>
      <p>Your cart is empty</p>
      <p style="font-size: 14px">Add some materials to get started!</p>
    </div>`;
  document.getElementById("cartTotal").textContent = "₱0.00";
  document.getElementById("checkoutBtn").disabled = true;
  document.getElementById("clearCartBtn").style.display = "none";
}

function proceedToCheckout() {
  alert("Proceeding to checkout...");
}

// Enhanced Sidebar functionality
document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const sidebarToggle = document.getElementById("sidebar-toggle");
  const sidebarOverlay = document.getElementById("sidebar-overlay");

  // Sidebar collapse/expand functionality
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      localStorage.setItem(
        "sidebarCollapsed",
        sidebar.classList.contains("collapsed")
      );

      // Update toggle button icon
      const icon = this.querySelector("i");
      if (sidebar.classList.contains("collapsed")) {
        icon.className = "fas fa-chevron-right";
      } else {
        icon.className = "fas fa-bars";
      }
    });
  }

  // Restore sidebar state from localStorage
  const isCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
  if (isCollapsed && sidebar) {
    sidebar.classList.add("collapsed");
    const icon = sidebarToggle?.querySelector("i");
    if (icon) icon.className = "fas fa-chevron-right";
  }

  // Mobile sidebar toggle
  function toggleMobileSidebar() {
    if (window.innerWidth <= 1200) {
      sidebar.classList.toggle("open");
      sidebarOverlay.classList.toggle("show");
      document.body.style.overflow = sidebar.classList.contains("open")
        ? "hidden"
        : "";
    }
  }

  // Mobile menu toggle button
  const mobileMenuToggle = document.getElementById("mobile-menu-toggle");
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", toggleMobileSidebar);
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", toggleMobileSidebar);
  }

  // Close mobile sidebar when clicking overlay
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", function () {
      sidebar.classList.remove("open");
      sidebarOverlay.classList.remove("show");
      document.body.style.overflow = "";
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (e) {
    if (window.innerWidth <= 1200) {
      if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove("open");
        sidebarOverlay.classList.remove("show");
        document.body.style.overflow = "";
      }
    }
  });

  // Keyboard shortcuts
  document.addEventListener("keydown", function (e) {
    // Ctrl/Cmd + B to toggle sidebar
    if ((e.ctrlKey || e.metaKey) && e.key === "b") {
      e.preventDefault();
      if (window.innerWidth > 1200) {
        sidebarToggle?.click();
      } else {
        toggleMobileSidebar();
      }
    }

    // Escape to close mobile sidebar
    if (e.key === "Escape" && window.innerWidth <= 1200) {
      sidebar.classList.remove("open");
      sidebarOverlay.classList.remove("show");
      document.body.style.overflow = "";
    }
  });

  // Handle window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth > 1200) {
      sidebar.classList.remove("open");
      sidebarOverlay.classList.remove("show");
      document.body.style.overflow = "";
    }
  });

  // Add loading states to navigation links
  const sidebarLinks = document.querySelectorAll(".sidebar .nav-item a");
  sidebarLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      // Add loading state
      this.classList.add("loading");

      // Remove loading state after navigation
      setTimeout(() => {
        this.classList.remove("loading");
      }, 1000);
    });
  });
});

// Help and logout functions
function showHelp() {
  alert(
    "Help functionality would open here. This could include:\n\n• User guide\n• FAQ\n• Contact support\n• Keyboard shortcuts\n• Video tutorials"
  );
}

function logout() {
  if (confirm("Are you sure you want to logout?")) {
    // Add loading state
    const logoutBtn = event.target.closest(".action-btn");
    if (logoutBtn) {
      logoutBtn.classList.add("loading");
    }

    // Simulate logout process
    setTimeout(() => {
      alert("Logout successful! Redirecting to login page...");
      // In a real app, this would redirect to login page
      // window.location.href = 'login.php';
    }, 1000);
  }
}

// Badge update functions
function updateBadge(badgeId, count) {
  const badge = document.getElementById(badgeId);
  if (badge) {
    badge.textContent = count;
    badge.style.display = count > 0 ? "block" : "none";
  }
}

// Simulate real-time badge updates
function simulateBadgeUpdates() {
  // Update booking badge
  updateBadge("booking-badge", Math.floor(Math.random() * 5));

  // Update payment badge
  updateBadge("payment-badge", Math.floor(Math.random() * 3));

  // Update cart badge
  updateBadge("cart-badge", Math.floor(Math.random() * 10));

  // Update quote badge
  updateBadge("quote-badge", Math.floor(Math.random() * 2));
}

// Run badge updates every 30 seconds
setInterval(simulateBadgeUpdates, 30000);

// Initialize badges on page load
document.addEventListener("DOMContentLoaded", function () {
  simulateBadgeUpdates();
});

// Search functionality for shop
function filterProducts() {
  const searchTerm = document.getElementById("shopSearch").value.toLowerCase();
  const products = document.querySelectorAll("#productsGrid .col-md-4");

  products.forEach((product) => {
    const title = product
      .querySelector(".card-title")
      .textContent.toLowerCase();
    const description = product
      .querySelector(".card-text")
      .textContent.toLowerCase();

    if (title.includes(searchTerm) || description.includes(searchTerm)) {
      product.style.display = "block";
    } else {
      product.style.display = "none";
    }
  });
}

// Add to cart functionality
function addToCart(productName, price) {
  // This would integrate with a real cart system
  alert(`${productName} added to cart for ${price}`);
}

// Payment functionality
function makePayment(method) {
  switch (method) {
    case "online":
      alert("Redirecting to online payment gateway...");
      break;
    case "cod":
      alert(
        "Cash on Delivery selected. Payment will be collected upon service completion."
      );
      break;
    case "bank":
      alert("Bank transfer details will be sent to your email.");
      break;
    default:
      alert("Payment method not recognized.");
  }
}

// Services filtering functionality
function filterServices(category) {
  // Remove active class from all filter buttons
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.classList.remove("active");
  });

  // Add active class to clicked button
  event.target.classList.add("active");

  // Show/hide model selection based on category
  const modelSelection = document.getElementById("modelSelection");
  if (category === "vehicle" || category === "motorcycle") {
    modelSelection.style.display = "block";
  } else {
    modelSelection.style.display = "none";
  }

  // Filter services based on category
  const servicesGrid = document.getElementById("servicesGrid");
  if (servicesGrid) {
    // This would filter the services based on category
    // For now, just show a message
    servicesGrid.innerHTML = `<div class="text-center p-4">
      <h5>Filtering services for: ${category}</h5>
      <p class="text-muted">Services will be loaded here based on the selected category.</p>
    </div>`;
  }
}

// Vehicle model selection
function updateModels() {
  const brand = document.getElementById("vehicleBrand").value;
  const modelSelect = document.getElementById("vehicleModel");

  // Clear existing options
  modelSelect.innerHTML = '<option value="">Select Model</option>';

  // Add models based on brand
  const models = {
    honda: ["Civic", "Accord", "CR-V", "HR-V", "City"],
    yamaha: ["YZF-R1", "YZF-R6", "MT-07", "MT-09", "FZ-16"],
    suzuki: ["GSX-R1000", "GSX-R600", "SV650", "V-Strom", "Burgman"],
    kawasaki: ["Ninja ZX-10R", "Ninja ZX-6R", "Z900", "Versys", "Concours"],
    toyota: ["Camry", "Corolla", "RAV4", "Highlander", "Prius"],
    mitsubishi: ["Lancer", "Outlander", "Eclipse Cross", "Mirage", "Montero"],
  };

  if (models[brand]) {
    models[brand].forEach((model) => {
      const option = document.createElement("option");
      option.value = model.toLowerCase();
      option.textContent = model;
      modelSelect.appendChild(option);
    });
  }
}

// Booking modal functionality
function showBookingModal() {
  // Create modal if it doesn't exist
  let modal = document.getElementById("bookingModal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "bookingModal";
    modal.className = "modal";
    modal.innerHTML = `
      <div class="modal-content">
        <span class="close" onclick="closeBookingModal()">&times;</span>
        <h2>Create New Booking</h2>
        <form>
          <div class="form-group">
            <label>Service Type:</label>
            <select id="serviceType" required>
              <option value="">Select Service</option>
              <option value="furniture">Furniture Repair</option>
              <option value="vehicle">Vehicle Upholstery</option>
              <option value="appliance">Appliance Repair</option>
            </select>
          </div>
          <div class="form-group">
            <label>Description:</label>
            <textarea id="serviceDescription" rows="3" required></textarea>
          </div>
          <div class="form-group">
            <label>Preferred Date:</label>
            <input type="date" id="preferredDate" required>
          </div>
          <div class="form-group">
            <label>Location:</label>
            <input type="text" id="serviceLocation" required>
          </div>
          <button type="button" onclick="submitBooking()">Submit Booking</button>
        </form>
      </div>
    `;
    document.body.appendChild(modal);
  }

  modal.style.display = "flex";
}

// History filtering functionality
function filterHistory() {
  const searchTerm = document
    .getElementById("historySearch")
    .value.toLowerCase();
  const filterType = document.getElementById("historyTypeFilter").value;
  const timelineItems = document.querySelectorAll(".timeline-item");

  timelineItems.forEach((item) => {
    const content = item
      .querySelector(".timeline-content")
      .textContent.toLowerCase();
    const type = item
      .querySelector(".timeline-content h4")
      .textContent.toLowerCase();

    let showItem = true;

    // Filter by search term
    if (searchTerm && !content.includes(searchTerm)) {
      showItem = false;
    }

    // Filter by type
    if (filterType !== "all") {
      if (filterType === "repair" && !type.includes("repair")) {
        showItem = false;
      } else if (filterType === "upholstery" && !type.includes("upholstery")) {
        showItem = false;
      } else if (
        filterType === "maintenance" &&
        !type.includes("maintenance")
      ) {
        showItem = false;
      }
    }

    item.style.display = showItem ? "block" : "none";
  });
}

// Management menu functionality
function showManagementMenu() {
  const managementOptions = [
    {
      name: "Quotations",
      url: "index.php?m=quotation",
      icon: "fas fa-file-invoice",
    },
    { name: "History", url: "index.php?m=history", icon: "fas fa-history" },
    { name: "Profile", url: "index.php?m=profile", icon: "fas fa-user" },
    { name: "Settings", url: "index.php?m=profile", icon: "fas fa-cog" },
  ];

  // Create management menu modal
  let modal = document.getElementById("managementModal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "managementModal";
    modal.className = "modal";
    modal.innerHTML = `
      <div class="modal-content">
        <span class="close" onclick="closeManagementModal()">&times;</span>
        <h2>Management Options</h2>
        <div class="management-grid">
          ${managementOptions
            .map(
              (option) => `
            <div class="management-option" onclick="window.location.href='${option.url}'">
              <i class="${option.icon}"></i>
              <span>${option.name}</span>
            </div>
          `
            )
            .join("")}
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  }

  modal.style.display = "flex";
}

function closeManagementModal() {
  const modal = document.getElementById("managementModal");
  if (modal) {
    modal.style.display = "none";
  }
}

// Enhanced badge click functionality
function handleBadgeClick(badgeType) {
  switch (badgeType) {
    case "booking":
      window.location.href = "index.php?m=booking";
      break;
    case "payment":
      window.location.href = "index.php?m=payment";
      break;
    case "quote":
      window.location.href = "index.php?m=quotation";
      break;
    default:
      console.log("Unknown badge type:", badgeType);
  }
}

// Add click handlers to badges
document.addEventListener("DOMContentLoaded", function () {
  console.log("Sidebar initialization started");

  // Add click handlers to badges
  const bookingBadge = document.getElementById("booking-badge");
  const paymentBadge = document.getElementById("payment-badge");
  const quoteBadge = document.getElementById("quote-badge");

  console.log("Badges found:", { bookingBadge, paymentBadge, quoteBadge });

  if (bookingBadge) {
    bookingBadge.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log("Booking badge clicked");
      handleBadgeClick("booking");
    });
  }

  if (paymentBadge) {
    paymentBadge.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log("Payment badge clicked");
      handleBadgeClick("payment");
    });
  }

  if (quoteBadge) {
    quoteBadge.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log("Quote badge clicked");
      handleBadgeClick("quote");
    });
  }

  // Test all sidebar links
  const sidebarLinks = document.querySelectorAll(".sidebar a");
  console.log("Sidebar links found:", sidebarLinks.length);

  sidebarLinks.forEach((link, index) => {
    link.addEventListener("click", (e) => {
      console.log(`Sidebar link ${index} clicked:`, link.href);
    });
  });

  // Test section titles
  const sectionTitles = document.querySelectorAll(".section-title.clickable");
  console.log("Clickable section titles found:", sectionTitles.length);

  sectionTitles.forEach((title, index) => {
    title.addEventListener("click", (e) => {
      console.log(`Section title ${index} clicked:`, title);
    });
  });

  // Test user info click
  const userInfo = document.querySelector(".user-info.clickable");
  if (userInfo) {
    console.log("User info clickable element found");
    userInfo.addEventListener("click", (e) => {
      console.log("User info clicked");
    });
  }

  // Add ripple effect to clickable elements
  const clickableElements = document.querySelectorAll(".clickable");
  clickableElements.forEach((element) => {
    element.addEventListener("click", function (e) {
      const ripple = document.createElement("span");
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;

      ripple.style.width = ripple.style.height = size + "px";
      ripple.style.left = x + "px";
      ripple.style.top = y + "px";
      ripple.classList.add("ripple");

      this.appendChild(ripple);

      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });
});
