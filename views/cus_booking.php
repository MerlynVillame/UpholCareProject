<!-- My Bookings Section -->
      <div id="bookings-section" class="section">
        <div class="card">
          <h2>My Bookings</h2>
          <div style="display: flex; gap: 10px; margin-bottom: 20px">
            <button class="btn" onclick="showBookingModal()">
              + New Booking
            </button>
            <select id="bookingFilter">
              <option value="all">All Bookings</option>
              <option value="pending">Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <table>
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="bookingsTable">
              <tr>
                <td>#BKG-1023</td>
                <td>Appliance Repair</td>
                <td>2025-09-10</td>
                <td>
                  <span class="status-badge in-progress">In Progress</span>
                </td>
                <td>₱1,500</td>
                <td><button class="btn-small">View Details</button></td>
              </tr>
              <tr>
                <td>#BKG-1022</td>
                <td>Vehicle Upholstery</td>
                <td>2025-09-05</td>
                <td><span class="status-badge completed">Completed</span></td>
                <td>₱2,800</td>
                <td><button class="btn-small">View Details</button></td>
              </tr>
              <tr>
                <td>#BKG-1021</td>
                <td>Furniture Repair</td>
                <td>2025-09-02</td>
                <td><span class="status-badge pending">Pending</span></td>
                <td>₱1,200</td>
                <td><button class="btn-small">View Details</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>