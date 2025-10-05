<div class="container-fluid px-4">
  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="card shadow-sm">
        <div class="card-body ">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-primary">
                <i class="fas fa-tools"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                <p class="card-category">Upcoming Repairs</p>
                <h4 class="card-title">2</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow-sm">
        <div class="card-body ">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-info">
                <i class="fas fa-file-invoice"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                <p class="card-category">Pending Quotations</p>
                <h4 class="card-title">1</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow-sm">
        <div class="card-body ">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-success">
                <i class="fas fa-wallet"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                <p class="card-category">Total Spent</p>
                <h4 class="card-title">₱12,500</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Removed User Statistics and Daily Sales section as requested -->

  <div class="row mt-3">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header "><div class="card-title">Recent Bookings</div></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#1023</td>
                  <td>Appliance</td>
                  <td><span class="badge bg-warning">In Progress</span></td>
                  <td>2025-09-10</td>
                </tr>
                <tr>
                  <td>#1022</td>
                  <td>Vehicle Upholstery</td>
                  <td><span class="badge bg-success">Completed</span></td>
                  <td>2025-09-05</td>
                </tr>
                <tr>
                  <td>#1021</td>
                  <td>Furniture</td>
                  <td><span class="badge bg-secondary">Pending</span></td>
                  <td>2025-09-02</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
 </div>

<!-- New Booking Modal -->
<div class="modal fade" id="newBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select">
              <option>Furniture</option>
              <option>Vehicle Upholstery</option>
              <option>Appliance</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="3" placeholder="Describe the work"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Preferred Date</label>
            <input type="date" class="form-control"/>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
 </div>

<!-- Removed unused demo chart scripts -->