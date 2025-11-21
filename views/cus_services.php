<!-- Services & Designs Section -->
      <div id="services-section" class="section">
        <div class="card">
          <div class="shop-header">
            <div>
              <h2 style="margin: 0; color: white">Services & Design Catalog</h2>
              <p style="margin: 5px 0 0 0; opacity: 0.9">
                Browse our services, designs, and pricing
              </p>
            </div>
            <div class="shop-stats">
              <div class="shop-stat">
                <span class="number" id="totalServices">12</span>
                <span class="label">Services</span>
              </div>
              <div class="shop-stat">
                <span class="number" id="totalDesigns">45</span>
                <span class="label">Designs</span>
              </div>
            </div>
          </div>

          <div class="shop-filters">
            <button
              class="filter-btn active"
              data-category="all"
              onclick="filterServices('all')"
            >
              All Services
            </button>
            <button
              class="filter-btn"
              data-category="vehicle"
              onclick="filterServices('vehicle')"
            >
              Vehicles
            </button>
            <button
              class="filter-btn"
              data-category="motorcycle"
              onclick="filterServices('motorcycle')"
            >
              Motorcycles
            </button>
            <button
              class="filter-btn"
              data-category="furniture"
              onclick="filterServices('furniture')"
            >
              Furniture
            </button>
            <button
              class="filter-btn"
              data-category="bedding"
              onclick="filterServices('bedding')"
            >
              Bedding
            </button>
          </div>

          <!-- Vehicle/Motorcycle Model Selection -->
          <div
            id="modelSelection"
            style="
              display: none;
              margin-bottom: 20px;
              padding: 15px;
              background: #f8f9fa;
              border-radius: 8px;
            "
          >
            <h4>Select Your Vehicle/Motorcycle Model</h4>
            <div
              style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px"
            >
              <div>
                <label>Brand</label>
                <select
                  id="vehicleBrand"
                  onchange="updateModels()"
                  style="
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                  "
                >
                  <option value="">Select Brand</option>
                  <option value="honda">Honda</option>
                  <option value="yamaha">Yamaha</option>
                  <option value="suzuki">Suzuki</option>
                  <option value="kawasaki">Kawasaki</option>
                  <option value="toyota">Toyota</option>
                  <option value="mitsubishi">Mitsubishi</option>
                </select>
              </div>
              <div>
                <label>Model</label>
                <select
                  id="vehicleModel"
                  style="
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                  "
                >
                  <option value="">Select Model</option>
                </select>
              </div>
            </div>
          </div>

          <div class="products-grid" id="servicesGrid">
            <!-- Services will be populated by JavaScript -->
          </div>
        </div>
      </div>