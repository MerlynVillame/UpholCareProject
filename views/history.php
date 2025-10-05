<div class="container-fluid px-4">
  <div class="row g-3">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">
          <div class="card-title mb-0">Service History</div>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="row">
              <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Search history..." id="historySearch">
              </div>
              <div class="col-md-4">
                <select class="form-select" id="historyTypeFilter">
                  <option value="all">All Services</option>
                  <option value="repair">Repairs</option>
                  <option value="upholstery">Upholstery</option>
                  <option value="maintenance">Maintenance</option>
                </select>
              </div>
              <div class="col-md-2">
                <button class="btn btn-primary" onclick="filterHistory()">Filter</button>
              </div>
            </div>
          </div>
          
          <!-- History Timeline -->
          <div class="history-timeline">
            <div class="timeline-item">
              <div class="timeline-date">Sep 5, 2025</div>
              <div class="timeline-content">
                <h4>Vehicle Upholstery - Honda Civic</h4>
                <p>Front seat reupholstery completed</p>
                <div class="timeline-meta">
                  <span class="badge bg-success">Completed</span>
                  <span class="amount">₱2,800</span>
                </div>
              </div>
            </div>
            <div class="timeline-item">
              <div class="timeline-date">Aug 30, 2025</div>
              <div class="timeline-content">
                <h4>Furniture Repair - Office Chair</h4>
                <p>Seat cushion replacement and frame repair</p>
                <div class="timeline-meta">
                  <span class="badge bg-success">Completed</span>
                  <span class="amount">₱1,200</span>
                </div>
              </div>
            </div>
            <div class="timeline-item">
              <div class="timeline-date">Aug 15, 2025</div>
              <div class="timeline-content">
                <h4>Appliance Repair - Washing Machine</h4>
                <p>Motor replacement and belt repair</p>
                <div class="timeline-meta">
                  <span class="badge bg-success">Completed</span>
                  <span class="amount">₱1,500</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function filterHistory() {
  const searchTerm = document.getElementById('historySearch').value.toLowerCase();
  const filterType = document.getElementById('historyTypeFilter').value;
  const timelineItems = document.querySelectorAll('.timeline-item');
  
  timelineItems.forEach(item => {
    const content = item.querySelector('.timeline-content').textContent.toLowerCase();
    const type = item.querySelector('.timeline-content h4').textContent.toLowerCase();
    
    let showItem = true;
    
    // Filter by search term
    if (searchTerm && !content.includes(searchTerm)) {
      showItem = false;
    }
    
    // Filter by type
    if (filterType !== 'all') {
      if (filterType === 'repair' && !type.includes('repair')) {
        showItem = false;
      } else if (filterType === 'upholstery' && !type.includes('upholstery')) {
        showItem = false;
      } else if (filterType === 'maintenance' && !type.includes('maintenance')) {
        showItem = false;
      }
    }
    
    item.style.display = showItem ? 'block' : 'none';
  });
}
</script>