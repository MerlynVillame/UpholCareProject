<!-- My Bookings Section -->
      <div id="bookings-section" class="section">
        <div class="card">
          <h2>My Bookings</h2>
          <div style="display: flex; gap: 10px; margin-bottom: 20px">
            <button class="btn" onclick="document.getElementById('bookingModal').style.display='flex'">
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
              <?php if (!empty($bookings)) : ?>
                <?php foreach ($bookings as $booking) : ?>
                  <tr>
                    <td>#<?php echo htmlspecialchars($booking['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['booking_date']); ?> <?php echo htmlspecialchars($booking['booking_time']); ?></td>
                    <td><span class="status-badge <?php echo htmlspecialchars(str_replace('_','-',$booking['status'])); ?>"><?php echo ucfirst(str_replace('_',' ',$booking['status'])); ?></span></td>
                    <td>₱<?php echo number_format((float)$booking['total_amount'], 2); ?></td>
                    <td><button class="btn-small">View Details</button></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="6" style="text-align:center; color:#6b7280">No bookings yet</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Create Booking Modal -->
        <div id="bookingModal" class="modal" style="display:none;">
          <div class="modal-content" style="max-width:520px;">
            <span class="close" onclick="document.getElementById('bookingModal').style.display='none'">&times;</span>
            <h2>Create New Booking</h2>
            <form id="createBookingForm">
              <div class="form-group">
                <label>Service</label>
                <select id="service_id" name="service_id" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
                  <option value="">Select a service</option>
                  <?php if (!empty($services)) : ?>
                    <?php foreach ($services as $service) : ?>
                      <option value="<?php echo (int)$service['service_id']; ?>" data-price="<?php echo htmlspecialchars($service['price']); ?>">
                        <?php echo htmlspecialchars($service['service_name']); ?> (₱<?php echo number_format((float)$service['price'],2); ?>)
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Date</label>
                <input type="date" id="booking_date" name="booking_date" required>
              </div>
              <div class="form-group">
                <label>Time</label>
                <input type="time" id="booking_time" name="booking_time" required>
              </div>
              <div class="form-group">
                <label>Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Describe the service details..."></textarea>
              </div>
              <div class="form-group">
                <label>Estimated Amount</label>
                <input type="number" id="total_amount" name="total_amount" step="0.01" placeholder="0.00" required>
              </div>
              <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" class="btn-secondary" onclick="document.getElementById('bookingModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn">Save Booking</button>
              </div>
              <input type="hidden" name="action" value="create_booking">
              <input type="hidden" name="customer_id" value="1">
            </form>
          </div>
        </div>
      </div>

      <script>
        (function(){
          var serviceSelect = document.getElementById('service_id');
          var amountInput = document.getElementById('total_amount');
          if (serviceSelect) {
            serviceSelect.addEventListener('change', function(){
              var price = this.options[this.selectedIndex].getAttribute('data-price');
              if (price && amountInput) amountInput.value = parseFloat(price).toFixed(2);
            });
          }

          var form = document.getElementById('createBookingForm');
          if (form) {
            form.addEventListener('submit', function(e){
              e.preventDefault();
              var formData = new FormData(form);
              fetch('controller/booking.php', { method: 'POST', body: formData })
                .then(function(r){ return r.json(); })
                .then(function(res){
                  if(res && res.success){
                    alert('Booking saved! ID: ' + res.booking_id);
                    window.location.reload();
                  } else {
                    alert('Failed to save booking: ' + (res && res.message ? res.message : 'Unknown error'));
                  }
                })
                .catch(function(err){ alert('Request error: ' + err); });
            });
          }
        })();
      </script>