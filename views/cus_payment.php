<!-- Payments Section -->
      <div id="payments-section" class="section">
        <div class="card">
          <h2>Payments</h2>
          <div
            style="
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 20px;
              margin-bottom: 20px;
            "
          >
            <div class="payment-card">
              <h3>Outstanding Balance</h3>
              <div
                style="font-size: 28px; font-weight: bold; color: var(--accent)"
              >
                ₱2,450
              </div>
              <p class="muted">Quotation #QT-20250901</p>
            </div>
            <div class="payment-card">
              <h3>Payment Methods</h3>
              <div style="display: flex; flex-direction: column; gap: 10px">
                <button class="btn" onclick="makePayment('online')">
                  💳 Online Payment
                </button>
                <button class="btn-secondary" onclick="makePayment('cod')">
                  💰 Cash on Delivery
                </button>
                <button class="btn-ghost" onclick="makePayment('bank')">
                  🏦 Bank Transfer
                </button>
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
              <tr>
                <td>2025-09-05</td>
                <td>₱2,800</td>
                <td>Online Payment</td>
                <td><span class="status-badge completed">Completed</span></td>
                <td>#PAY-20250905</td>
              </tr>
              <tr>
                <td>2025-08-30</td>
                <td>₱1,200</td>
                <td>Cash on Delivery</td>
                <td><span class="status-badge completed">Completed</span></td>
                <td>#PAY-20250830</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>