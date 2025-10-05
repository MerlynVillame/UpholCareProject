// Global Variables
let currentUser = {
  id: 1,
  name: "Demo User",
  email: "demo@upholcare.com",
  phone: "+1234567890",
  address: "123 Demo Street, Demo City",
};
let isLoggedIn = true;
let cart = [];
let currentFilter = "all";
let currentServiceFilter = "all";

// Backend Simulation - Data Storage
const backend = {
  // Initialize data from localStorage or use defaults
  users: JSON.parse(
    localStorage.getItem("backend_users") ||
      JSON.stringify([
        {
          id: 1,
          name: "Jane Smith",
          email: "jane@example.com",
          phone: "+63 912 345 6789",
          password: "password123",
          address: "123 Main Street, Quezon City, Metro Manila",
          createdAt: new Date().toISOString(),
        },
      ])
  ),

  bookings: JSON.parse(
    localStorage.getItem("backend_bookings") ||
      JSON.stringify([
        {
          id: "BKG-1023",
          userId: 1,
          serviceType: "Appliance Repair",
          status: "in-progress",
          date: "2025-09-10",
          amount: 1500,
          description: "Washing machine motor repair",
          createdAt: new Date().toISOString(),
          updates: [
            {
              status: "pending",
              message: "Booking submitted",
              timestamp: new Date().toISOString(),
            },
            {
              status: "approved",
              message: "Booking approved",
              timestamp: new Date().toISOString(),
            },
            {
              status: "in-progress",
              message: "Service started",
              timestamp: new Date().toISOString(),
            },
          ],
        },
        {
          id: "BKG-1022",
          userId: 1,
          serviceType: "Vehicle Upholstery",
          status: "completed",
          date: "2025-09-05",
          amount: 2800,
          description: "Honda Civic seat reupholstery",
          createdAt: new Date().toISOString(),
          updates: [
            {
              status: "pending",
              message: "Booking submitted",
              timestamp: new Date().toISOString(),
            },
            {
              status: "approved",
              message: "Booking approved",
              timestamp: new Date().toISOString(),
            },
            {
              status: "in-progress",
              message: "Service started",
              timestamp: new Date().toISOString(),
            },
            {
              status: "completed",
              message: "Service completed successfully",
              timestamp: new Date().toISOString(),
            },
          ],
        },
      ])
  ),

  quotations: JSON.parse(
    localStorage.getItem("backend_quotations") ||
      JSON.stringify([
        {
          id: "QT-20250901",
          userId: 1,
          serviceType: "Car Seat Reupholstery",
          vehicleBrand: "Honda",
          vehicleModel: "Civic",
          amount: 2450,
          status: "pending_approval",
          description: "Front seat reupholstery with premium leather",
          details: [
            "Front seat reupholstery with premium leather",
            "Foam replacement for both seats",
            "Color: Ocean Blue",
            "Estimated completion: 3-5 business days",
          ],
          createdAt: new Date().toISOString(),
        },
      ])
  ),

  payments: JSON.parse(
    localStorage.getItem("backend_payments") ||
      JSON.stringify([
        {
          id: "PAY-20250905",
          userId: 1,
          bookingId: "BKG-1022",
          amount: 2800,
          method: "Online Payment",
          status: "completed",
          date: "2025-09-05",
          reference: "PAY-20250905",
        },
        {
          id: "PAY-20250830",
          userId: 1,
          bookingId: "BKG-1021",
          amount: 1200,
          method: "Cash on Delivery",
          status: "completed",
          date: "2025-08-30",
          reference: "PAY-20250830",
        },
      ])
  ),

  orders: JSON.parse(
    localStorage.getItem("backend_orders") || JSON.stringify([])
  ),

  // Save data to localStorage
  save() {
    localStorage.setItem("backend_users", JSON.stringify(this.users));
    localStorage.setItem("backend_bookings", JSON.stringify(this.bookings));
    localStorage.setItem("backend_quotations", JSON.stringify(this.quotations));
    localStorage.setItem("backend_payments", JSON.stringify(this.payments));
    localStorage.setItem("backend_orders", JSON.stringify(this.orders));
  },

  // Generate unique IDs
  generateId(prefix = "") {
    return prefix + Date.now() + Math.random().toString(36).substr(2, 9);
  },
};

function updateUIForLoggedInUser() {
  document.getElementById("welcomeMessage").textContent =
    "Welcome to UpholCare!";
  document.getElementById("sidebar").style.display = "block";
  document.getElementById("newBookingBtn").style.display = "block";
}

function showNotification(message, type) {
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: ${type === "success" ? "#10b981" : "#ef4444"};
          color: white;
          padding: 12px 20px;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          z-index: 1001;
          animation: slideIn 0.3s ease;
        `;
  notification.textContent = message;
  document.body.appendChild(notification);
  setTimeout(() => notification.remove(), 3000);
}

// Mobile Menu Functions
function toggleMobileMenu() {
  document.getElementById("sidebar").classList.toggle("open");
}

// Services Data
const services = [
  {
    id: 1,
    name: "Car Seat Reupholstery",
    category: "vehicle",
    price: 2500,
    description: "Complete car seat reupholstery with premium materials",
    specifications: "Premium leather/fabric • Foam replacement • 3-5 days",
    brands: ["honda", "toyota", "mitsubishi"],
    models: ["civic", "accord", "vios", "altis", "lancer"],
    icon: "🚗",
  },
  {
    id: 2,
    name: "Motorcycle Seat Repair",
    category: "motorcycle",
    price: 800,
    description: "Motorcycle seat repair and customization",
    specifications: "Custom design • Waterproof materials • 2-3 days",
    brands: ["honda", "yamaha", "suzuki"],
    models: ["xrm", "wave", "mio", "nmax"],
    icon: "🏍️",
  },
  {
    id: 3,
    name: "Sofa Reupholstery",
    category: "furniture",
    price: 3500,
    description: "Complete sofa reupholstery service",
    specifications: "Fabric/leather options • Frame repair • 5-7 days",
    brands: [],
    models: [],
    icon: "🛋️",
  },
  {
    id: 4,
    name: "Bed Mattress Repair",
    category: "bedding",
    price: 1200,
    description: "Mattress repair and foam replacement",
    specifications: "Memory foam • High density foam • 2-3 days",
    brands: [],
    models: [],
    icon: "🛏️",
  },
];

// Vehicle Models Data
const vehicleModels = {
  honda: ["civic", "accord", "crv", "city", "xrm", "wave", "click", "beat"],
  yamaha: ["nmax", "mio", "aerox", "r15", "fz16"],
  suzuki: ["gsx-r", "gixxer", "access", "burgman"],
  toyota: ["vios", "altis", "camry", "innova", "fortuner"],
  mitsubishi: ["lancer", "mirage", "montero", "strada"],
  kawasaki: ["ninja", "z1000", "versys", "klx"],
};

// Material Shop Data
const products = [
  {
    id: 1,
    name: "Premium Leather - Black",
    category: "leather",
    price: 1200,
    originalPrice: 1500,
    description:
      "High-quality genuine leather perfect for car seats and furniture",
    specs: "100% Genuine Leather • 2mm thickness • Water resistant",
    stock: 15,
    badge: "sale",
    icon: "🖤",
  },
  {
    id: 2,
    name: "Ocean Teal Fabric",
    category: "fabric",
    price: 350,
    originalPrice: null,
    description: "Soft, durable fabric with beautiful ocean teal color",
    specs: "100% Cotton • Machine washable • Fade resistant",
    stock: 25,
    badge: "new",
    icon: "🌊",
  },
  {
    id: 3,
    name: "High-Density Foam - 2 inch",
    category: "foam",
    price: 450,
    originalPrice: null,
    description: "Premium high-density foam for seat cushions and padding",
    specs: "High Density • 2 inch thickness • Fire retardant",
    stock: 8,
    badge: null,
    icon: "🧽",
  },
  {
    id: 4,
    name: "Synthetic Leather - Brown",
    category: "leather",
    price: 800,
    originalPrice: 1000,
    description: "Durable synthetic leather with authentic look and feel",
    specs: "Synthetic Leather • Easy to clean • UV resistant",
    stock: 12,
    badge: "sale",
    icon: "🤎",
  },
  {
    id: 5,
    name: "Velvet Fabric - Royal Blue",
    category: "fabric",
    price: 420,
    originalPrice: null,
    description: "Luxurious velvet fabric for premium upholstery projects",
    specs: "100% Polyester Velvet • Soft texture • Rich color",
    stock: 18,
    badge: null,
    icon: "👑",
  },
  {
    id: 6,
    name: "Memory Foam - 3 inch",
    category: "foam",
    price: 650,
    originalPrice: null,
    description: "Memory foam for ultimate comfort and support",
    specs: "Memory Foam • 3 inch thickness • Temperature sensitive",
    stock: 5,
    badge: null,
    icon: "🧠",
  },
  {
    id: 7,
    name: "Upholstery Staple Gun",
    category: "tools",
    price: 280,
    originalPrice: null,
    description: "Professional staple gun for upholstery work",
    specs: "Heavy Duty • Includes staples • Ergonomic grip",
    stock: 10,
    badge: null,
    icon: "🔫",
  },
  {
    id: 8,
    name: "Thread Set - Assorted Colors",
    category: "accessories",
    price: 120,
    originalPrice: null,
    description: "High-quality thread set in various colors",
    specs: "50 colors • 100m each • UV resistant",
    stock: 20,
    badge: null,
    icon: "🧵",
  },
  {
    id: 9,
    name: "Canvas Fabric - Natural",
    category: "fabric",
    price: 380,
    originalPrice: null,
    description: "Heavy-duty canvas fabric for outdoor furniture",
    specs: "100% Cotton Canvas • Water resistant • Durable",
    stock: 14,
    badge: null,
    icon: "🎨",
  },
  {
    id: 10,
    name: "Leather Conditioner",
    category: "accessories",
    price: 180,
    originalPrice: null,
    description: "Premium leather conditioner for maintenance",
    specs: "250ml bottle • Natural ingredients • Easy application",
    stock: 30,
    badge: null,
    icon: "💧",
  },
];

// Services Functions
function initializeServices() {
  renderServices();
  updateServiceStats();
}

function renderServices() {
  const grid = document.getElementById("servicesGrid");
  const filteredServices = getFilteredServices();

  grid.innerHTML = "";

  filteredServices.forEach((service) => {
    const serviceCard = createServiceCard(service);
    grid.appendChild(serviceCard);
  });

  document.getElementById("totalServices").textContent =
    filteredServices.length;
}

function createServiceCard(service) {
  const card = document.createElement("div");
  card.className = "product-card";
  card.innerHTML = `
          <div class="product-image">
            ${service.icon}
          </div>
          <div class="product-info">
            <div class="product-category">${service.category.toUpperCase()}</div>
            <div class="product-name">${service.name}</div>
            <div class="product-description">${service.description}</div>
            <div class="product-specs">${service.specifications}</div>
            <div class="product-price">
              <span class="current-price">₱${service.price.toLocaleString()}</span>
            </div>
            <div class="product-actions">
              <button class="btn-add-cart" onclick="bookService(${service.id})">
                Book Service
              </button>
              <button class="btn-view-details" onclick="viewServiceDetails(${
                service.id
              })">
                Details
              </button>
            </div>
          </div>
        `;
  return card;
}

function getFilteredServices() {
  const brand = document.getElementById("vehicleBrand")?.value;
  const model = document.getElementById("vehicleModel")?.value;

  return services.filter((service) => {
    const matchesCategory =
      currentServiceFilter === "all" ||
      service.category === currentServiceFilter;

    if (brand && model) {
      const matchesBrand = service.brands.includes(brand);
      const matchesModel = service.models.includes(model);
      return matchesCategory && matchesBrand && matchesModel;
    }

    return matchesCategory;
  });
}

function filterServices(category) {
  currentServiceFilter = category;

  document.querySelectorAll("#services-section .filter-btn").forEach((btn) => {
    btn.classList.remove("active");
  });
  document
    .querySelector(`#services-section [data-category="${category}"]`)
    .classList.add("active");

  const modelSelection = document.getElementById("modelSelection");
  if (category === "vehicle" || category === "motorcycle") {
    modelSelection.style.display = "block";
  } else {
    modelSelection.style.display = "none";
  }

  renderServices();
}

function updateModels() {
  const brand = document.getElementById("vehicleBrand").value;
  const modelSelect = document.getElementById("vehicleModel");

  modelSelect.innerHTML = "<option value=''>Select Model</option>";

  if (brand && vehicleModels[brand]) {
    vehicleModels[brand].forEach((model) => {
      const option = document.createElement("option");
      option.value = model;
      option.textContent = model.charAt(0).toUpperCase() + model.slice(1);
      modelSelect.appendChild(option);
    });
  }

  renderServices();
}

function bookService(serviceId) {
  // Authentication removed - always allow booking

  const service = services.find((s) => s.id === serviceId);
  if (service) {
    showBookingModal();
    document.getElementById("serviceType").value = service.category;
    updateServiceOptions();
  }
}

function viewServiceDetails(serviceId) {
  const service = services.find((s) => s.id === serviceId);
  if (!service) return;

  const details = `
Service: ${service.name}
Category: ${service.category}
Price: ₱${service.price.toLocaleString()}
Description: ${service.description}
Specifications: ${service.specifications}
${
  service.brands.length > 0
    ? `Compatible Brands: ${service.brands.join(", ")}`
    : ""
}
        `;

  alert(details);
}

function updateServiceStats() {
  document.getElementById("totalServices").textContent = services.length;
  document.getElementById("totalDesigns").textContent = "45"; // Static for demo
}

// Dashboard Loading Functions
function loadUserDashboard() {
  // Authentication removed - always load dashboard

  // Update dashboard stats
  const userBookings = backend.bookings.filter(
    (b) => b.userId === currentUser.id
  );
  const pendingBookings = userBookings.filter(
    (b) => b.status === "pending"
  ).length;
  const pendingQuotations = backend.quotations.filter(
    (q) => q.userId === currentUser.id && q.status === "pending_approval"
  ).length;
  const totalSpent = backend.payments
    .filter((p) => p.userId === currentUser.id && p.status === "completed")
    .reduce((sum, p) => sum + p.amount, 0);

  // Update dashboard stats display
  const statsElements = document.querySelectorAll(".stat span:last-child");
  if (statsElements.length >= 3) {
    statsElements[0].textContent = userBookings.filter((b) =>
      ["pending", "approved", "in-progress"].includes(b.status)
    ).length;
    statsElements[1].textContent = pendingQuotations;
    statsElements[2].textContent = `₱${totalSpent.toLocaleString()}`;
  }

  // Load bookings table
  loadBookingsTable();
  loadQuotationsSection();
  loadPaymentsSection();
  loadHistorySection();
}

function loadBookingsTable() {
  // Authentication removed - always load bookings

  const userBookings = backend.bookings.filter(
    (b) => b.userId === currentUser.id
  );
  const tableBody = document.getElementById("bookingsTable");

  if (tableBody) {
    tableBody.innerHTML = userBookings
      .map(
        (booking) => `
            <tr>
              <td>${booking.id}</td>
              <td>${booking.serviceType}</td>
              <td>${booking.date}</td>
              <td><span class="status-badge ${booking.status.replace(
                "-",
                ""
              )}">${booking.status
          .replace("-", " ")
          .replace(/\b\w/g, (l) => l.toUpperCase())}</span></td>
              <td>₱${booking.amount.toLocaleString()}</td>
              <td><button class="btn-small" onclick="viewBookingDetails('${
                booking.id
              }')">View Details</button></td>
            </tr>
          `
      )
      .join("");
  }
}

function loadQuotationsSection() {
  // Authentication removed - always load quotations

  const userQuotations = backend.quotations.filter(
    (q) => q.userId === currentUser.id
  );
  const quotationsSection = document.querySelector("#quotations-section .card");

  if (quotationsSection && userQuotations.length > 0) {
    quotationsSection.innerHTML = `
            <h2>Quotations</h2>
            ${userQuotations
              .map(
                (quote) => `
              <div class="quotation-item">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                  <div>
                    <h3>Quotation #${quote.id}</h3>
                    <p class="muted">${quote.serviceType} - ${
                  quote.vehicleBrand
                } ${quote.vehicleModel}</p>
                    <p class="muted">Submitted: ${new Date(
                      quote.createdAt
                    ).toLocaleDateString()}</p>
                  </div>
                  <div style="text-align: right">
                    <div style="font-size: 24px; font-weight: bold; color: var(--accent);">₱${quote.amount.toLocaleString()}</div>
                    <div class="muted">Estimated Total</div>
                  </div>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                  <h4>Service Details:</h4>
                  <ul>
                    ${quote.details
                      .map((detail) => `<li>${detail}</li>`)
                      .join("")}
                  </ul>
                </div>
                <div style="display: flex; gap: 10px">
                  <button class="btn" onclick="acceptQuotation('${
                    quote.id
                  }')">Accept Quotation</button>
                  <button class="btn-secondary" onclick="rejectQuotation('${
                    quote.id
                  }')">Request Changes</button>
                  <button class="btn-ghost" onclick="downloadQuotation('${
                    quote.id
                  }')">Download PDF</button>
                </div>
              </div>
            `
              )
              .join("")}
          `;
  }
}

function loadPaymentsSection() {
  // Authentication removed - always load payments

  const userPayments = backend.payments.filter(
    (p) => p.userId === currentUser.id
  );
  const pendingQuotations = backend.quotations.filter(
    (q) => q.userId === currentUser.id && q.status === "pending_approval"
  );
  const outstandingAmount = pendingQuotations.reduce(
    (sum, q) => sum + q.amount,
    0
  );

  const paymentsSection = document.querySelector("#payments-section .card");
  if (paymentsSection) {
    paymentsSection.innerHTML = `
            <h2>Payments</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
              <div class="payment-card">
                <h3>Outstanding Balance</h3>
                <div style="font-size: 28px; font-weight: bold; color: var(--accent);">₱${outstandingAmount.toLocaleString()}</div>
                <p class="muted">${pendingQuotations.length} pending quotation${
      pendingQuotations.length !== 1 ? "s" : ""
    }</p>
              </div>
              <div class="payment-card">
                <h3>Payment Methods</h3>
                <div style="display: flex; flex-direction: column; gap: 10px">
                  <button class="btn" onclick="makePayment('online')">💳 Online Payment</button>
                  <button class="btn-secondary" onclick="makePayment('cod')">💰 Cash on Delivery</button>
                  <button class="btn-ghost" onclick="makePayment('bank')">🏦 Bank Transfer</button>
                </div>
              </div>
            </div>
            <h3>Payment History</h3>
            <table>
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Status</th>
                  <th>Reference</th>
                </tr>
              </thead>
              <tbody>
                ${userPayments
                  .map(
                    (payment) => `
                  <tr>
                    <td>${payment.date}</td>
                    <td>₱${payment.amount.toLocaleString()}</td>
                    <td>${payment.method}</td>
                    <td><span class="status-badge completed">${
                      payment.status.charAt(0).toUpperCase() +
                      payment.status.slice(1)
                    }</span></td>
                    <td>${payment.reference}</td>
                  </tr>
                `
                  )
                  .join("")}
              </tbody>
            </table>
          `;
  }
}

function loadHistorySection() {
  // Authentication removed - always load history

  const userBookings = backend.bookings.filter(
    (b) => b.userId === currentUser.id && b.status === "completed"
  );
  const historySection = document.querySelector(
    "#history-section .history-timeline"
  );

  if (historySection) {
    historySection.innerHTML = userBookings
      .map(
        (booking) => `
            <div class="timeline-item">
              <div class="timeline-date">${new Date(
                booking.date
              ).toLocaleDateString()}</div>
              <div class="timeline-content">
                <h4>${booking.serviceType} - ${booking.description}</h4>
                <p>Service completed successfully</p>
                <div class="timeline-meta">
                  <span class="status-badge completed">Completed</span>
                  <span class="amount">₱${booking.amount.toLocaleString()}</span>
                </div>
              </div>
            </div>
          `
      )
      .join("");
  }
}

// Enhanced Booking Functions
function updateServiceOptions() {
  const serviceType = document.getElementById("serviceType").value;
  const vehicleOptions = document.getElementById("vehicleOptions");

  if (serviceType === "vehicle" || serviceType === "motorcycle") {
    vehicleOptions.style.display = "block";
  } else {
    vehicleOptions.style.display = "none";
  }
}

function submitBooking() {
  // Authentication removed - always allow booking submission

  const serviceType = document.getElementById("serviceType").value;
  const serviceLocation = document.getElementById("serviceLocation").value;
  const description = document.getElementById("serviceDescription").value;
  const designUpload = document.getElementById("designUpload").files[0];
  const materialPurchase = document.getElementById("materialPurchase").checked;

  if (!serviceType || !description) {
    showNotification("Please fill in all required fields", "error");
    return;
  }

  // Create new booking with real data
  const bookingId = backend.generateId("BKG-");
  const preferredDate = document.getElementById("preferredDate").value;
  const vehicleBrand = document.getElementById("bookingBrand")?.value || "";
  const vehicleModel = document.getElementById("bookingModel")?.value || "";
  const service = services.find((s) => s.category === serviceType);
  const estimatedAmount = service ? service.price : 1000;

  const newBooking = {
    id: bookingId,
    userId: currentUser.id,
    serviceType: serviceType,
    vehicleBrand: vehicleBrand,
    vehicleModel: vehicleModel,
    serviceLocation: serviceLocation,
    status: "pending",
    date: preferredDate || new Date().toISOString().split("T")[0],
    amount: estimatedAmount,
    description: description,
    designUpload: designUpload ? designUpload.name : null,
    materialPurchase: materialPurchase,
    createdAt: new Date().toISOString(),
    updates: [
      {
        status: "pending",
        message: "Booking submitted successfully",
        timestamp: new Date().toISOString(),
      },
    ],
  };

  // Add booking to backend
  backend.bookings.push(newBooking);
  backend.save();

  showNotification(
    `Booking submitted successfully! Booking ID: ${bookingId}`,
    "success"
  );

  if (designUpload) {
    showNotification("Design reference uploaded successfully", "success");
  }

  if (materialPurchase) {
    showNotification(
      "Material purchase option noted. You can browse materials in the shop.",
      "success"
    );
  }

  closeBookingModal();
  showSection("bookings");

  // Reload dashboard to show new booking
  loadUserDashboard();

  // Clear form
  document.getElementById("newBookingForm").reset();

  // Simulate email notification
  simulateEmailNotification("booking_confirmation", {
    bookingId: bookingId,
    serviceType: serviceType,
    customerName: currentUser.name,
    email: currentUser.email,
  });

  // Auto-generate quotation for some services
  setTimeout(() => {
    generateQuotation(newBooking);
  }, 2000);
}

function generateQuotation(booking) {
  const quotationId = backend.generateId("QT-");
  const service = services.find((s) => s.category === booking.serviceType);

  if (service) {
    const newQuotation = {
      id: quotationId,
      userId: booking.userId,
      bookingId: booking.id,
      serviceType: booking.serviceType,
      vehicleBrand: booking.vehicleBrand,
      vehicleModel: booking.vehicleModel,
      amount: service.price,
      status: "pending_approval",
      description: booking.description,
      details: [
        `${service.name} - ${service.description}`,
        `Service location: ${booking.serviceLocation}`,
        `Estimated completion: ${
          service.specifications.split("•")[2] || "3-5 business days"
        }`,
        booking.designUpload
          ? "Custom design reference provided"
          : "Standard design",
      ],
      createdAt: new Date().toISOString(),
    };

    backend.quotations.push(newQuotation);
    backend.save();

    // Update booking status
    booking.status = "approved";
    booking.updates.push({
      status: "approved",
      message: "Booking approved and quotation generated",
      timestamp: new Date().toISOString(),
    });
    backend.save();

    showNotification(
      `Quotation generated for booking ${booking.id}`,
      "success"
    );

    simulateEmailNotification("quotation_ready", {
      customerName: currentUser.name,
      email: currentUser.email,
      quotationId: quotationId,
      amount: service.price,
    });

    loadUserDashboard();
  }
}

// Email Notification Simulation
function simulateEmailNotification(type, data) {
  const notifications = {
    booking_confirmation: {
      subject: "Booking Confirmation - UpholCare",
      message: `Dear ${data.customerName},\n\nYour booking has been confirmed!\nBooking ID: ${data.bookingId}\nService: ${data.serviceType}\n\nWe will contact you within 24 hours to schedule your service.\n\nThank you for choosing UpholCare!`,
    },
    quotation_ready: {
      subject: "Quotation Ready - UpholCare",
      message: `Dear ${data.customerName},\n\nYour quotation is ready for review!\nQuotation ID: ${data.quotationId}\nAmount: ₱${data.amount}\n\nPlease log in to your account to view and approve the quotation.\n\nBest regards,\nUpholCare Team`,
    },
    service_update: {
      subject: "Service Update - UpholCare",
      message: `Dear ${
        data.customerName
      },\n\nYour service status has been updated!\nBooking ID: ${
        data.bookingId
      }\nStatus: ${data.status}\n\n${
        data.message || "Please check your dashboard for more details."
      }\n\nThank you for your patience.`,
    },
  };

  const notification = notifications[type];
  if (notification) {
    console.log(`📧 Email sent to ${data.email}`);
    console.log(`Subject: ${notification.subject}`);
    console.log(`Message: ${notification.message}`);

    showNotification("Email notification sent successfully!", "success");
  }
}

// Enhanced Booking Status Tracking
function updateBookingStatus(bookingId, newStatus, message) {
  // In a real app, this would update the database
  console.log(`Booking ${bookingId} status updated to: ${newStatus}`);

  if (currentUser) {
    simulateEmailNotification("service_update", {
      customerName: currentUser.name,
      email: currentUser.email,
      bookingId: bookingId,
      status: newStatus,
      message: message,
    });
  }

  showNotification(
    `Booking ${bookingId} status updated to ${newStatus}`,
    "success"
  );
}

// Simulate real-time status updates
function simulateStatusUpdates() {
  const statuses = ["Pending", "Approved", "In Progress", "Completed"];
  const bookingIds = ["#BKG-1023", "#BKG-1022", "#BKG-1021"];

  setInterval(() => {
    if (Math.random() > 0.95) {
      // 5% chance every interval
      const randomBooking =
        bookingIds[Math.floor(Math.random() * bookingIds.length)];
      const randomStatus =
        statuses[Math.floor(Math.random() * statuses.length)];

      updateBookingStatus(
        randomBooking,
        randomStatus,
        "Your service is progressing well!"
      );
    }
  }, 30000); // Check every 30 seconds
}

// Initialize status tracking
setTimeout(simulateStatusUpdates, 5000); // Start after 5 seconds

// Initialize shop
function initializeShop() {
  renderProducts();
  updateCartUI();
}

// Initialize bookings section
function initializeBookings() {
  loadBookingsTable();
  // Add event listener for booking filter
  const bookingFilter = document.getElementById("bookingFilter");
  if (bookingFilter) {
    bookingFilter.addEventListener("change", function () {
      loadBookingsTable();
    });
  }
}

// Initialize quotations section
function initializeQuotations() {
  loadQuotationsSection();
}

// Initialize payments section
function initializePayments() {
  loadPaymentsSection();
}

// Initialize history section
function initializeHistory() {
  loadHistorySection();
  // Add event listeners for history filters
  const historySearch = document.getElementById("historySearch");
  const historyTypeFilter = document.getElementById("historyTypeFilter");

  if (historySearch) {
    historySearch.addEventListener("input", function () {
      loadHistorySection();
    });
  }

  if (historyTypeFilter) {
    historyTypeFilter.addEventListener("change", function () {
      loadHistorySection();
    });
  }
}

// Initialize profile section
function initializeProfile() {
  // Load user profile data
  const profileForm = document.getElementById("profileForm");
  if (profileForm) {
    // Populate form with current user data
    const nameInput = profileForm.querySelector('input[type="text"]');
    const emailInput = profileForm.querySelector('input[type="email"]');

    if (nameInput) nameInput.value = currentUser.name || "";
    if (emailInput) emailInput.value = currentUser.email || "";
  }
}

// Render products
function renderProducts() {
  const grid = document.getElementById("productsGrid");
  const filteredProducts = getFilteredProducts();

  grid.innerHTML = "";

  filteredProducts.forEach((product) => {
    const productCard = createProductCard(product);
    grid.appendChild(productCard);
  });

  document.getElementById("totalProducts").textContent =
    filteredProducts.length;
}

// Create product card
function createProductCard(product) {
  const card = document.createElement("div");
  card.className = "product-card";
  card.innerHTML = `
          <div class="product-image">
            ${product.icon}
            ${
              product.badge
                ? `<div class="product-badge ${
                    product.badge
                  }">${product.badge.toUpperCase()}</div>`
                : ""
            }
            ${
              product.stock === 0
                ? '<div class="product-badge out-of-stock">OUT OF STOCK</div>'
                : ""
            }
          </div>
          <div class="product-info">
            <div class="product-category">${product.category.toUpperCase()}</div>
            <div class="product-name">${product.name}</div>
            <div class="product-description">${product.description}</div>
            <div class="product-specs">${product.specs}</div>
            <div class="product-price">
              <span class="current-price">₱${product.price.toLocaleString()}</span>
              ${
                product.originalPrice
                  ? `<span class="original-price">₱${product.originalPrice.toLocaleString()}</span>`
                  : ""
              }
            </div>
            <div class="product-actions">
              <button class="btn-add-cart" onclick="addToCart(${product.id})" ${
    product.stock === 0 ? "disabled" : ""
  }>
                ${product.stock === 0 ? "Out of Stock" : "Add to Cart"}
              </button>
              <button class="btn-view-details" onclick="viewProductDetails(${
                product.id
              })">
                Details
              </button>
            </div>
          </div>
        `;
  return card;
}

// Get filtered products
function getFilteredProducts() {
  const searchTerm = document.getElementById("shopSearch").value.toLowerCase();
  return products.filter((product) => {
    const matchesCategory =
      currentFilter === "all" || product.category === currentFilter;
    const matchesSearch =
      product.name.toLowerCase().includes(searchTerm) ||
      product.description.toLowerCase().includes(searchTerm);
    return matchesCategory && matchesSearch;
  });
}

// Filter by category
function filterByCategory(category) {
  currentFilter = category;

  // Update filter buttons
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.classList.remove("active");
  });
  document
    .querySelector(`[data-category="${category}"]`)
    .classList.add("active");

  renderProducts();
}

// Filter products by search
function filterProducts() {
  renderProducts();
}

// Add to cart
function addToCart(productId) {
  const product = products.find((p) => p.id === productId);
  if (!product || product.stock === 0) return;

  const existingItem = cart.find((item) => item.id === productId);
  if (existingItem) {
    if (existingItem.quantity < product.stock) {
      existingItem.quantity++;
    } else {
      alert("Not enough stock available!");
      return;
    }
  } else {
    cart.push({
      ...product,
      quantity: 1,
    });
  }

  updateCartUI();
  showCartNotification(`${product.name} added to cart!`);
}

// Remove from cart
function removeFromCart(productId) {
  cart = cart.filter((item) => item.id !== productId);
  updateCartUI();
}

// Update quantity
function updateQuantity(productId, newQuantity) {
  const item = cart.find((item) => item.id === productId);
  if (item) {
    if (newQuantity <= 0) {
      removeFromCart(productId);
    } else if (newQuantity <= item.stock) {
      item.quantity = newQuantity;
      updateCartUI();
    } else {
      alert("Not enough stock available!");
    }
  }
}

// Update cart UI
function updateCartUI() {
  const cartItems = document.getElementById("cartItems");
  const cartBadge = document.getElementById("cartBadge");
  const cartCount = document.getElementById("cartCount");
  const cartTotal = document.getElementById("cartTotal");
  const checkoutBtn = document.getElementById("checkoutBtn");
  const clearCartBtn = document.getElementById("clearCartBtn");

  // Update cart count
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  cartBadge.textContent = totalItems;
  cartCount.textContent = totalItems;

  // Update cart total
  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  cartTotal.textContent = `₱${total.toLocaleString()}`;

  // Update cart items
  if (cart.length === 0) {
    cartItems.innerHTML = `
            <div style="text-align: center; padding: 40px; color: var(--muted);">
              <div style="font-size: 48px; margin-bottom: 16px;">🛒</div>
              <p>Your cart is empty</p>
              <p style="font-size: 14px;">Add some materials to get started!</p>
            </div>
          `;
    checkoutBtn.disabled = true;
    clearCartBtn.style.display = "none";
  } else {
    cartItems.innerHTML = cart
      .map(
        (item) => `
            <div class="cart-item">
              <div class="cart-item-image">${item.icon}</div>
              <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">₱${item.price.toLocaleString()}</div>
                <div class="cart-item-controls">
                  <button class="quantity-btn" onclick="updateQuantity(${
                    item.id
                  }, ${item.quantity - 1})">-</button>
                  <input type="number" class="quantity-input" value="${
                    item.quantity
                  }" min="1" max="${item.stock}" 
                         onchange="updateQuantity(${
                           item.id
                         }, parseInt(this.value))">
                  <button class="quantity-btn" onclick="updateQuantity(${
                    item.id
                  }, ${item.quantity + 1})">+</button>
                  <button class="remove-item" onclick="removeFromCart(${
                    item.id
                  })" title="Remove item">×</button>
                </div>
              </div>
            </div>
          `
      )
      .join("");
    checkoutBtn.disabled = false;
    clearCartBtn.style.display = "block";
  }
}

// Toggle cart
function toggleCart() {
  const cartSidebar = document.getElementById("cartSidebar");
  const cartOverlay = document.getElementById("cartOverlay");

  cartSidebar.classList.toggle("open");
  cartOverlay.classList.toggle("show");
}

// Close cart
function closeCart() {
  const cartSidebar = document.getElementById("cartSidebar");
  const cartOverlay = document.getElementById("cartOverlay");

  cartSidebar.classList.remove("open");
  cartOverlay.classList.remove("show");
}

// Clear cart
function clearCart() {
  if (confirm("Are you sure you want to clear your cart?")) {
    cart = [];
    updateCartUI();
  }
}

// Proceed to checkout
function proceedToCheckout() {
  if (cart.length === 0) return;

  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);

  // Create order
  const orderId = backend.generateId("ORD-");
  const order = {
    id: orderId,
    userId: currentUser.id,
    items: cart.map((item) => ({
      productId: item.id,
      name: item.name,
      price: item.price,
      quantity: item.quantity,
      total: item.price * item.quantity,
    })),
    total: total,
    status: "pending",
    createdAt: new Date().toISOString(),
  };

  backend.orders.push(order);
  backend.save();

  showNotification(
    `Order created successfully! Order ID: ${orderId}`,
    "success"
  );

  // Clear cart
  cart = [];
  updateCartUI();
  closeCart();

  // Show order confirmation
  showOrderConfirmation(order);
}

function showOrderConfirmation(order) {
  const modal = document.createElement("div");
  modal.className = "modal";
  modal.style.display = "flex";
  modal.innerHTML = `
          <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
              <h2>Order Confirmation</h2>
              <button onclick="this.closest('.modal').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div style="margin-bottom: 20px;">
              <h3>Order ID: ${order.id}</h3>
              <p>Thank you for your purchase!</p>
            </div>
            <div style="margin-bottom: 20px;">
              <h4>Order Summary:</h4>
              ${order.items
                .map(
                  (item) => `
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                  <span>${item.name} x${item.quantity}</span>
                  <span>₱${item.total.toLocaleString()}</span>
                </div>
              `
                )
                .join("")}
              <div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 1px solid #ddd; padding-top: 10px;">
                <span>Total:</span>
                <span>₱${order.total.toLocaleString()}</span>
              </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
              <button class="btn" onclick="this.closest('.modal').remove()">Close</button>
              <button class="btn-secondary" onclick="showSection('history'); this.closest('.modal').remove();">View Order History</button>
            </div>
          </div>
        `;
  document.body.appendChild(modal);
}

// View product details
function viewProductDetails(productId) {
  const product = products.find((p) => p.id === productId);
  if (!product) return;

  const details = `
Product: ${product.name}
Category: ${product.category}
Price: ₱${product.price.toLocaleString()}
${
  product.originalPrice
    ? `Original Price: ₱${product.originalPrice.toLocaleString()}`
    : ""
}
Stock: ${product.stock} units
Description: ${product.description}
Specifications: ${product.specs}
        `;

  alert(details);
}

// Show cart notification
function showCartNotification(message) {
  // Create notification element
  const notification = document.createElement("div");
  notification.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: var(--accent);
          color: white;
          padding: 12px 20px;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          z-index: 1001;
          animation: slideIn 0.3s ease;
        `;
  notification.textContent = message;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.remove();
  }, 3000);
}

// Navigation functionality
function showSection(sectionId) {
  // Hide all sections
  const sections = document.querySelectorAll(".section");
  sections.forEach((section) => (section.style.display = "none"));

  // Show selected section
  const targetSection = document.getElementById(sectionId + "-section");
  if (targetSection) {
    targetSection.style.display = "block";
  }

  // Update active nav item
  const navItems = document.querySelectorAll(".nav a");
  navItems.forEach((item) => item.classList.remove("active"));
  const activeItem = document.querySelector(`[data-section="${sectionId}"]`);
  if (activeItem) {
    activeItem.classList.add("active");
  }
}

// Add click event listeners to navigation
document.addEventListener("DOMContentLoaded", function () {
  // Authentication removed - always show logged in state
  updateUIForLoggedInUser();
  loadUserDashboard();

  const navItems = document.querySelectorAll(".nav a");
  navItems.forEach((item) => {
    item.addEventListener("click", function (e) {
      e.preventDefault();
      const section = this.getAttribute("data-section");
      showSection(section);

      // Initialize sections when navigating to them
      if (section === "shop") {
        initializeShop();
      } else if (section === "services") {
        initializeServices();
      } else if (section === "bookings") {
        initializeBookings();
      } else if (section === "quotations") {
        initializeQuotations();
      } else if (section === "payments") {
        initializePayments();
      } else if (section === "history") {
        initializeHistory();
      } else if (section === "profile") {
        initializeProfile();
      } else if (section === "dashboard") {
        loadUserDashboard();
      }

      // Close mobile menu after navigation (for better mobile UX)
      const sidebar = document.getElementById("sidebar");
      if (window.innerWidth <= 768) {
        sidebar.classList.remove("open");
      }
    });
  });

  // Initialize shop and services on page load
  initializeShop();
  initializeServices();

  // Close mobile menu when clicking outside
  document.addEventListener("click", function (e) {
    const sidebar = document.getElementById("sidebar");
    const menuToggle = document.querySelector(".mobile-menu-toggle");
    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      sidebar.classList.remove("open");
    }
  });

  // Add keyboard navigation support for sidebar
  document.addEventListener("keydown", function (e) {
    // Close mobile menu with Escape key
    if (e.key === "Escape") {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.remove("open");
      const cartSidebar = document.getElementById("cartSidebar");
      cartSidebar.classList.remove("open");
      const cartOverlay = document.getElementById("cartOverlay");
      cartOverlay.classList.remove("show");
    }
  });

  // Handle window resize to ensure proper sidebar behavior
  window.addEventListener("resize", function () {
    const sidebar = document.getElementById("sidebar");
    // Close mobile menu when switching to desktop view
    if (window.innerWidth > 768) {
      sidebar.classList.remove("open");
    }
  });
});

// Booking modal functions
function showBookingModal() {
  document.getElementById("bookingModal").style.display = "flex";
}

function closeBookingModal() {
  document.getElementById("bookingModal").style.display = "none";
}

function submitBooking() {
  alert("Booking submitted successfully! We will contact you soon.");
  closeBookingModal();
  // Refresh bookings table
  showSection("bookings");
}

// Quotation functions
function acceptQuotation(quotationId) {
  if (confirm("Are you sure you want to accept this quotation?")) {
    const quotation = backend.quotations.find((q) => q.id === quotationId);
    if (quotation) {
      quotation.status = "accepted";
      quotation.acceptedAt = new Date().toISOString();

      // Update related booking
      const booking = backend.bookings.find(
        (b) => b.id === quotation.bookingId
      );
      if (booking) {
        booking.status = "approved";
        booking.updates.push({
          status: "approved",
          message: "Quotation accepted by customer",
          timestamp: new Date().toISOString(),
        });
      }

      backend.save();
      loadUserDashboard();
      showNotification(
        "Quotation accepted! Payment instructions will be sent to your email.",
        "success"
      );
      showSection("payments");
    }
  }
}

function rejectQuotation(quotationId) {
  const reason = prompt("Please provide reason for requesting changes:");
  if (reason) {
    const quotation = backend.quotations.find((q) => q.id === quotationId);
    if (quotation) {
      quotation.status = "rejected";
      quotation.rejectionReason = reason;
      quotation.rejectedAt = new Date().toISOString();

      backend.save();
      loadUserDashboard();
      showNotification(
        "Your feedback has been submitted. We will review and send an updated quotation.",
        "success"
      );
    }
  }
}

function downloadQuotation(quotationId) {
  const quotation = backend.quotations.find((q) => q.id === quotationId);
  if (quotation) {
    // Create a simple text-based "PDF" download
    const content = `
UPHOLCARE SERVICES
QUOTATION

Quotation ID: ${quotation.id}
Date: ${new Date(quotation.createdAt).toLocaleDateString()}
Customer: ${currentUser.name}

Service Details:
${quotation.details.map((detail) => `• ${detail}`).join("\n")}

Total Amount: ₱${quotation.amount.toLocaleString()}

Thank you for choosing UpholCare!
          `;

    const blob = new Blob([content], { type: "text/plain" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `quotation-${quotationId}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);

    showNotification("Quotation downloaded successfully!", "success");
  }
}

// Enhanced Payment Functions
function makePayment(method) {
  // Authentication removed - always allow payments

  switch (method) {
    case "online":
      showOnlinePaymentModal();
      break;
    case "cod":
      confirmCODPayment();
      break;
    case "bank":
      showBankTransferDetails();
      break;
  }
}

function showOnlinePaymentModal() {
  const modal = document.createElement("div");
  modal.className = "modal";
  modal.style.display = "flex";
  modal.innerHTML = `
          <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
              <h2>Online Payment</h2>
              <button onclick="this.closest('.modal').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div style="margin-bottom: 20px;">
              <h3>Payment Amount: ₱2,450</h3>
              <p>Quotation #QT-20250901</p>
            </div>
            <div style="margin-bottom: 20px;">
              <label>Payment Method</label>
              <select style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; margin-top: 5px;">
                <option>Credit Card (Visa/Mastercard)</option>
                <option>GCash</option>
                <option>PayMaya</option>
                <option>GrabPay</option>
              </select>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
              <button class="btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button>
              <button class="btn" onclick="processOnlinePayment()">Pay Now</button>
            </div>
          </div>
        `;
  document.body.appendChild(modal);
}

function processOnlinePayment() {
  showNotification("Redirecting to secure payment gateway...", "success");
  setTimeout(() => {
    // Create payment record
    const pendingQuotations = backend.quotations.filter(
      (q) => q.userId === currentUser.id && q.status === "accepted"
    );
    if (pendingQuotations.length > 0) {
      const quotation = pendingQuotations[0];
      const paymentId = backend.generateId("PAY-");

      const newPayment = {
        id: paymentId,
        userId: currentUser.id,
        quotationId: quotation.id,
        bookingId: quotation.bookingId,
        amount: quotation.amount,
        method: "Online Payment",
        status: "completed",
        date: new Date().toISOString().split("T")[0],
        reference: paymentId,
      };

      backend.payments.push(newPayment);
      backend.save();

      // Update quotation status
      quotation.status = "paid";
      quotation.paidAt = new Date().toISOString();

      // Update booking status
      const booking = backend.bookings.find(
        (b) => b.id === quotation.bookingId
      );
      if (booking) {
        booking.status = "in-progress";
        booking.updates.push({
          status: "in-progress",
          message: "Payment received, service in progress",
          timestamp: new Date().toISOString(),
        });
      }

      backend.save();
      loadUserDashboard();
    }

    showNotification(
      "Payment successful! Receipt sent to your email.",
      "success"
    );
    document.querySelector(".modal").remove();
  }, 2000);
}

function confirmCODPayment() {
  const pendingQuotations = backend.quotations.filter(
    (q) => q.userId === currentUser.id && q.status === "accepted"
  );
  if (pendingQuotations.length > 0) {
    const quotation = pendingQuotations[0];
    if (
      confirm(
        `Confirm Cash on Delivery payment?\n\nYou will pay ₱${quotation.amount.toLocaleString()} when the service is completed.`
      )
    ) {
      // Create COD payment record
      const paymentId = backend.generateId("PAY-");

      const newPayment = {
        id: paymentId,
        userId: currentUser.id,
        quotationId: quotation.id,
        bookingId: quotation.bookingId,
        amount: quotation.amount,
        method: "Cash on Delivery",
        status: "pending",
        date: new Date().toISOString().split("T")[0],
        reference: paymentId,
      };

      backend.payments.push(newPayment);
      backend.save();

      // Update quotation status
      quotation.status = "cod_confirmed";
      quotation.codConfirmedAt = new Date().toISOString();

      // Update booking status
      const booking = backend.bookings.find(
        (b) => b.id === quotation.bookingId
      );
      if (booking) {
        booking.status = "approved";
        booking.updates.push({
          status: "approved",
          message: "COD payment confirmed, service will begin",
          timestamp: new Date().toISOString(),
        });
      }

      backend.save();
      loadUserDashboard();
      showNotification(
        "COD payment confirmed. You will be contacted for delivery arrangements.",
        "success"
      );
    }
  }
}

function showBankTransferDetails() {
  const details = `
Bank Transfer Details:
Account Name: UpholCare Services
Account Number: 1234-5678-9012
Bank: BDO
Reference: QT-20250901
Amount: ₱2,450

Please send proof of payment to support@upholcare.com
        `;
  alert(details);
}

// Material Availability Check
function checkMaterialAvailability(serviceId) {
  const service = services.find((s) => s.id === serviceId);
  if (!service) return false;

  // Simulate material availability check
  const availableMaterials = {
    1: true, // Car Seat Reupholstery
    2: true, // Motorcycle Seat Repair
    3: false, // Sofa Reupholstery (temporarily unavailable)
    4: true, // Bed Mattress Repair
  };

  return availableMaterials[serviceId] || false;
}

function showMaterialAvailability(serviceId) {
  const isAvailable = checkMaterialAvailability(serviceId);
  const service = services.find((s) => s.id === serviceId);

  if (isAvailable) {
    showNotification(`Materials for ${service.name} are available!`, "success");
  } else {
    showNotification(
      `Materials for ${service.name} are currently unavailable. We'll notify you when they're back in stock.`,
      "error"
    );
  }
}

// Profile functions
function updateProfile() {
  alert("Profile updated successfully!");
}

function changePassword() {
  alert(
    "Password change request submitted. Check your email for instructions."
  );
}

// Enhanced History Filter
function filterHistory() {
  const searchTerm = document
    .getElementById("historySearch")
    .value.toLowerCase();
  const typeFilter = document.getElementById("historyTypeFilter").value;

  const timelineItems = document.querySelectorAll(".timeline-item");
  timelineItems.forEach((item) => {
    const content = item.textContent.toLowerCase();
    const matchesSearch = content.includes(searchTerm);
    const matchesType = typeFilter === "all" || content.includes(typeFilter);

    item.style.display = matchesSearch && matchesType ? "block" : "none";
  });
}

// Add real-time search
document.addEventListener("DOMContentLoaded", function () {
  const historySearch = document.getElementById("historySearch");
  if (historySearch) {
    historySearch.addEventListener("input", filterHistory);
  }
});

// Booking Details Viewer
function viewBookingDetails(bookingId) {
  const booking = backend.bookings.find((b) => b.id === bookingId);
  if (!booking) return;

  const modal = document.createElement("div");
  modal.className = "modal";
  modal.style.display = "flex";
  modal.innerHTML = `
          <div class="modal-content" style="max-width: 600px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
              <h2>Booking Details - ${booking.id}</h2>
              <button onclick="this.closest('.modal').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div style="margin-bottom: 20px;">
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                  <h4>Service Information</h4>
                  <p><strong>Service Type:</strong> ${booking.serviceType}</p>
                  <p><strong>Status:</strong> <span class="status-badge ${booking.status.replace(
                    "-",
                    ""
                  )}">${booking.status
    .replace("-", " ")
    .replace(/\b\w/g, (l) => l.toUpperCase())}</span></p>
                  <p><strong>Date:</strong> ${booking.date}</p>
                  <p><strong>Amount:</strong> ₱${booking.amount.toLocaleString()}</p>
                </div>
                <div>
                  <h4>Additional Details</h4>
                  ${
                    booking.vehicleBrand
                      ? `<p><strong>Vehicle:</strong> ${booking.vehicleBrand} ${booking.vehicleModel}</p>`
                      : ""
                  }
                  <p><strong>Location:</strong> ${booking.serviceLocation}</p>
                  ${
                    booking.designUpload
                      ? `<p><strong>Design Upload:</strong> ${booking.designUpload}</p>`
                      : ""
                  }
                  ${
                    booking.materialPurchase
                      ? "<p><strong>Material Purchase:</strong> Requested</p>"
                      : ""
                  }
                </div>
              </div>
              <div style="margin-top: 20px;">
                <h4>Description</h4>
                <p>${booking.description}</p>
              </div>
              <div style="margin-top: 20px;">
                <h4>Status Updates</h4>
                <div class="history-timeline">
                  ${booking.updates
                    .map(
                      (update) => `
                    <div class="timeline-item">
                      <div class="timeline-date">${new Date(
                        update.timestamp
                      ).toLocaleDateString()}</div>
                      <div class="timeline-content">
                        <h4>${update.status
                          .replace("-", " ")
                          .replace(/\b\w/g, (l) => l.toUpperCase())}</h4>
                        <p>${update.message}</p>
                      </div>
                    </div>
                  `
                    )
                    .join("")}
                </div>
              </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
              <button class="btn" onclick="this.closest('.modal').remove()">Close</button>
            </div>
          </div>
        `;
  document.body.appendChild(modal);
}

// Enhanced Profile Functions
function updateProfile() {
  const name = document.querySelector('#profileForm input[type="text"]').value;
  const email = document.querySelector(
    '#profileForm input[type="email"]'
  ).value;
  const phone = document.querySelector('#profileForm input[type="tel"]').value;
  const address = document.querySelector("#profileForm textarea").value;

  currentUser.name = name;
  currentUser.email = email;
  currentUser.phone = phone;
  currentUser.address = address;

  // Update in backend
  const userIndex = backend.users.findIndex((u) => u.id === currentUser.id);
  if (userIndex !== -1) {
    backend.users[userIndex] = { ...currentUser };
    backend.save();
  }

  updateUIForLoggedInUser();
  showNotification("Profile updated successfully!", "success");
}

function changePassword() {
  const currentPassword = document.querySelector(
    '#profileForm input[placeholder="Current password"]'
  ).value;
  const newPassword = document.querySelector(
    '#profileForm input[placeholder="New password"]'
  ).value;
  const confirmPassword = document.querySelector(
    '#profileForm input[placeholder="Confirm new password"]'
  ).value;

  // Password validation removed for demo purposes

  if (newPassword !== confirmPassword) {
    showNotification("New passwords do not match", "error");
    return;
  }

  if (newPassword.length < 6) {
    showNotification("Password must be at least 6 characters long", "error");
    return;
  }

  currentUser.password = newPassword;

  // Update in backend
  const userIndex = backend.users.findIndex((u) => u.id === currentUser.id);
  if (userIndex !== -1) {
    backend.users[userIndex].password = newPassword;
    backend.save();
  }

  showNotification("Password changed successfully!", "success");

  // Clear password fields
  document.querySelector(
    '#profileForm input[placeholder="Current password"]'
  ).value = "";
  document.querySelector(
    '#profileForm input[placeholder="New password"]'
  ).value = "";
  document.querySelector(
    '#profileForm input[placeholder="Confirm new password"]'
  ).value = "";
}

// Add keyboard shortcuts
document.addEventListener("keydown", function (e) {
  // Ctrl/Cmd + K to open search
  if ((e.ctrlKey || e.metaKey) && e.key === "k") {
    e.preventDefault();
    const searchInput = document.querySelector(".search-input");
    if (searchInput) {
      searchInput.focus();
    }
  }

  // Escape to close modals
  if (e.key === "Escape") {
    const openModal = document.querySelector('.modal[style*="flex"]');
    if (openModal) {
      openModal.style.display = "none";
    }
  }
});

// Booking filter
document
  .getElementById("bookingFilter")
  .addEventListener("change", function () {
    const filter = this.value;
    const rows = document.querySelectorAll("#bookingsTable tr");

    rows.forEach((row) => {
      if (filter === "all") {
        row.style.display = "";
      } else {
        const status = row.querySelector(".status-badge");
        if (status) {
          const statusClass = status.className;
          const shouldShow =
            (filter === "pending" && statusClass.includes("pending")) ||
            (filter === "in-progress" && statusClass.includes("in-progress")) ||
            (filter === "completed" && statusClass.includes("completed"));
          row.style.display = shouldShow ? "" : "none";
        }
      }
    });
  });
