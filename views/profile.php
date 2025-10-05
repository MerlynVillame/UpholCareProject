<div class="container-fluid px-4">
  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mb-3">
            <img src="views/assets/img/undraw/undraw_Hello_qnas.svg" alt="Profile" class="rounded-circle" width="120" height="120">
          </div>
          <h5 class="card-title">John Doe</h5>
          <p class="text-muted">john.doe@email.com</p>
          <p class="text-muted">+63 912 345 6789</p>
          <button class="btn btn-outline-primary btn-sm">Edit Profile</button>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-8">
      <div class="card shadow-sm">
        <div class="card-header">
          <div class="card-title mb-0">Account Information</div>
        </div>
        <div class="card-body">
          <form>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" value="John" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" value="Doe" readonly>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" value="john.doe@email.com" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control" value="+63 912 345 6789" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea class="form-control" rows="3" readonly>123 Main Street, Barangay Example, City, Province 1234</textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Member Since</label>
                <input type="text" class="form-control" value="January 2024" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Customer ID</label>
                <input type="text" class="form-control" value="CUST-2024-001" readonly>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-primary" onclick="toggleEdit()">Edit Information</button>
              <button type="button" class="btn btn-outline-secondary">Change Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row mt-3">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">
          <div class="card-title mb-0">Account Statistics</div>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-3">
              <div class="border-end">
                <h4 class="text-primary">12</h4>
                <p class="text-muted mb-0">Total Bookings</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="border-end">
                <h4 class="text-success">₱15,600</h4>
                <p class="text-muted mb-0">Total Spent</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="border-end">
                <h4 class="text-info">8</h4>
                <p class="text-muted mb-0">Completed Services</p>
              </div>
            </div>
            <div class="col-md-3">
              <h4 class="text-warning">2</h4>
              <p class="text-muted mb-0">Pending Services</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function toggleEdit() {
  const inputs = document.querySelectorAll('form input[readonly], form textarea[readonly]');
  const button = event.target;
  
  if (button.textContent === 'Edit Information') {
    inputs.forEach(input => {
      input.removeAttribute('readonly');
      input.classList.add('border-primary');
    });
    button.textContent = 'Save Changes';
    button.classList.remove('btn-primary');
    button.classList.add('btn-success');
  } else {
    inputs.forEach(input => {
      input.setAttribute('readonly', 'readonly');
      input.classList.remove('border-primary');
    });
    button.textContent = 'Edit Information';
    button.classList.remove('btn-success');
    button.classList.add('btn-primary');
    // Here you would typically save the data to the server
    alert('Changes saved successfully!');
  }
}
</script>
