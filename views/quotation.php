<div class="container-fluid px-4">
  <div class="row g-3">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="card-title mb-0">Quotations</div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newQuotationModal">
            <i class="fas fa-plus"></i> Request Quotation
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Quote ID</th>
                  <th>Service Type</th>
                  <th>Description</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#QT-20250901</td>
                  <td>Vehicle Upholstery</td>
                  <td>Honda Civic front seat reupholstery</td>
                  <td>₱2,450</td>
                  <td><span class="badge bg-warning">Pending</span></td>
                  <td>2025-09-01</td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary">View</button>
                    <button class="btn btn-sm btn-success">Accept</button>
                  </td>
                </tr>
                <tr>
                  <td>#QT-20250825</td>
                  <td>Furniture Repair</td>
                  <td>Office chair seat replacement</td>
                  <td>₱1,200</td>
                  <td><span class="badge bg-success">Accepted</span></td>
                  <td>2025-08-25</td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary">View</button>
                    <button class="btn btn-sm btn-info">Download</button>
                  </td>
                </tr>
                <tr>
                  <td>#QT-20250815</td>
                  <td>Appliance Repair</td>
                  <td>Washing machine motor replacement</td>
                  <td>₱1,500</td>
                  <td><span class="badge bg-secondary">Expired</span></td>
                  <td>2025-08-15</td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary">View</button>
                    <button class="btn btn-sm btn-warning">Renew</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Quotation Request Modal -->
<div class="modal fade" id="newQuotationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Request New Quotation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Service Type</label>
              <select class="form-select" required>
                <option value="">Select Service Type</option>
                <option value="vehicle">Vehicle Upholstery</option>
                <option value="furniture">Furniture Repair</option>
                <option value="appliance">Appliance Repair</option>
                <option value="motorcycle">Motorcycle Upholstery</option>
                <option value="bedding">Bedding & Cushions</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Priority</label>
              <select class="form-select">
                <option value="normal">Normal</option>
                <option value="urgent">Urgent</option>
                <option value="low">Low Priority</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Item Description</label>
            <textarea class="form-control" rows="3" placeholder="Describe the item and work needed" required></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Preferred Contact Method</label>
              <select class="form-select">
                <option value="phone">Phone Call</option>
                <option value="email">Email</option>
                <option value="sms">SMS</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Preferred Date</label>
              <input type="date" class="form-control"/>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Additional Notes</label>
            <textarea class="form-control" rows="2" placeholder="Any special requirements or notes"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary">Submit Request</button>
      </div>
    </div>
  </div>
</div>
