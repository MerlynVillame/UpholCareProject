<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'admin_sidebar.php'; ?>
<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'topbar.php'; ?>

<?php
// Get current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// Sample pending applicants data for this month
$pendingApplicants = [
    [
        'id' => 1,
        'applicant_name' => 'Mark Anthony Garcia',
        'position' => 'Upholsterer',
        'email' => 'mark.garcia@email.com',
        'phone' => '0917-234-5678',
        'date_applied' => '2024-10-10',
        'age' => 28,
        'experience' => '3 years',
        'documents_uploaded' => 4,
        'documents_total' => 5,
        'status' => 'Pending Review'
    ],
    [
        'id' => 4,
        'applicant_name' => 'Angela Rose Diaz',
        'position' => 'Customer Service',
        'email' => 'angela.diaz@email.com',
        'phone' => '0920-567-8901',
        'date_applied' => '2024-10-03',
        'age' => 24,
        'experience' => '1 year',
        'documents_uploaded' => 3,
        'documents_total' => 5,
        'status' => 'Pending Review'
    ],
    [
        'id' => 6,
        'applicant_name' => 'Christine Marie Bautista',
        'position' => 'Upholsterer',
        'email' => 'christine.bautista@email.com',
        'phone' => '0925-123-4567',
        'date_applied' => '2024-10-15',
        'age' => 31,
        'experience' => '6 years',
        'documents_uploaded' => 5,
        'documents_total' => 5,
        'status' => 'Documents Complete'
    ],
    [
        'id' => 7,
        'applicant_name' => 'Jose Miguel Santos',
        'position' => 'Upholsterer',
        'email' => 'jose.santos@email.com',
        'phone' => '0922-345-6789',
        'date_applied' => '2024-10-08',
        'age' => 29,
        'experience' => '4 years',
        'documents_uploaded' => 5,
        'documents_total' => 5,
        'status' => 'Documents Complete'
    ],
];

// Calculate statistics
$totalPending = count($pendingApplicants);
$completeDocuments = 0;
$incompleteDocuments = 0;

foreach ($pendingApplicants as $applicant) {
    if ($applicant['documents_uploaded'] === $applicant['documents_total']) {
        $completeDocuments++;
    } else {
        $incompleteDocuments++;
    }
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Staff Applicants</h1>
        <p class="mb-0 text-muted"><?php echo date('F Y'); ?> - Applications Under Review</p>
    </div>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addApplicantModal">
        <i class="fas fa-plus"></i> Add Staff
    </a>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalPending; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Complete Documents</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completeDocuments; ?>/<?php echo $totalPending; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Incomplete Documents</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $incompleteDocuments; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-exclamation fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Positions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" id="applicantSearch" placeholder="Search by name, position, or email...">
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <select class="form-control" id="positionFilter">
                    <option value="all">All Positions</option>
                    <option value="Upholsterer">Upholsterer</option>
                    <option value="Customer Service">Customer Service</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select class="form-control" id="documentFilter">
                    <option value="all">All Applicants</option>
                    <option value="complete">Complete Documents</option>
                    <option value="incomplete">Incomplete Documents</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Applicants Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Applicants List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="applicantsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>Experience</th>
                        <th>Date Applied</th>
                        <th>Documents</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="applicantsTableBody">
                    <?php if (count($pendingApplicants) > 0): ?>
                        <?php foreach ($pendingApplicants as $applicant): 
                            $dateApplied = new DateTime($applicant['date_applied']);
                            $formattedDate = $dateApplied->format('M d, Y');
                        ?>
                        <tr>
                            <td>#<?php echo $applicant['id']; ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #865f1c, #a67c30); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;">
                                        <?php echo strtoupper(substr($applicant['applicant_name'], 0, 1)); ?>
                                    </div>
                                    <strong><?php echo htmlspecialchars($applicant['applicant_name']); ?></strong>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($applicant['position']); ?></td>
                            <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                            <td><?php echo $applicant['phone']; ?></td>
                            <td><?php echo $applicant['age']; ?></td>
                            <td><?php echo htmlspecialchars($applicant['experience']); ?></td>
                            <td><?php echo $formattedDate; ?></td>
                            <td>
                                <?php $docsPercentage = ($applicant['documents_uploaded'] / $applicant['documents_total']) * 100; ?>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 60px; height: 6px; background: #e5e7eb; border-radius: 10px; overflow: hidden;">
                                        <div style="height: 100%; background: linear-gradient(90deg, #10b981, #059669); width: <?php echo $docsPercentage; ?>%; border-radius: 10px;"></div>
                                    </div>
                                    <span style="font-size: 11px; font-weight: 600; color: #6b7280;"><?php echo $applicant['documents_uploaded']; ?>/<?php echo $applicant['documents_total']; ?></span>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $badgeClass = ($applicant['documents_uploaded'] === $applicant['documents_total']) ? 'badge-success' : 'badge-warning';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $applicant['status']; ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" title="View" onclick="viewApplicant(<?php echo $applicant['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" title="Approve" onclick="approveApplicant(<?php echo $applicant['id']; ?>)">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Reject" onclick="rejectApplicant(<?php echo $applicant['id']; ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted py-5">
                                <i class="fas fa-user-times fa-3x mb-3"></i>
                                <p>No pending applicants found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addApplicantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #865f1c; color: white; border: none;">
                <h5 class="modal-title" style="color: white; font-weight: 600;">Add New Staff</h5>
                <button type="button" class="close" style="color: white; opacity: 0.8;" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addStaffForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter full name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" placeholder="Enter email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" placeholder="Enter phone number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Position <span class="text-danger">*</span></label>
                                <select class="form-control" required>
                                    <option value="">Select position...</option>
                                    <option value="Upholsterer">Upholsterer</option>
                                    <option value="Customer Service">Customer Service</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Supervisor">Supervisor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Age</label>
                                <input type="number" class="form-control" placeholder="Enter age">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Experience</label>
                                <input type="text" class="form-control" placeholder="e.g., 3 years">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" rows="2" placeholder="Enter address"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" style="background-color: #865f1c; border-color: #865f1c;">Add Staff</button>
            </div>
        </div>
    </div>
</div>

<!-- View Applicant Details Modal -->
<div class="modal fade" id="viewApplicantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #17a2b8; color: white; border: none;">
                <h5 class="modal-title" style="color: white; font-weight: 600;">Applicant Details</h5>
                <button type="button" class="close" style="color: white; opacity: 0.8;" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewApplicantBody">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="approveFromModal()">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectFromModal()">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Applicant Confirmation Modal -->
<div class="modal fade" id="approveApplicantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #28a745; color: white; border: none;">
                <h5 class="modal-title" style="color: white; font-weight: 600;">
                    <i class="fas fa-check-circle"></i> Approve Applicant
                </h5>
                <button type="button" class="close" style="color: white; opacity: 0.8;" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this applicant and add them as a staff member?</p>
                <div id="approveApplicantInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmApprove()">
                    <i class="fas fa-check"></i> Yes, Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Applicant Confirmation Modal -->
<div class="modal fade" id="rejectApplicantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #dc3545; color: white; border: none;">
                <h5 class="modal-title" style="color: white; font-weight: 600;">
                    <i class="fas fa-times-circle"></i> Reject Applicant
                </h5>
                <button type="button" class="close" style="color: white; opacity: 0.8;" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject this applicant?</p>
                <div id="rejectApplicantInfo"></div>
                <div class="form-group mt-3">
                    <label><strong>Reason for Rejection (Optional)</strong></label>
                    <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Enter reason for rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">
                    <i class="fas fa-times"></i> Yes, Reject
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let applicantsData = <?php echo json_encode($pendingApplicants); ?>;
let currentApplicantId = null;

function viewApplicant(id) {
    const applicant = applicantsData.find(a => a.id === id);
    if (!applicant) return;
    
    currentApplicantId = id;
    
    const docsPercentage = (applicant.documents_uploaded / applicant.documents_total) * 100;
    const dateApplied = new Date(applicant.date_applied);
    const formattedDate = dateApplied.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    
    const modalBody = `
        <div class="row">
            <div class="col-md-3 text-center mb-3">
                <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #865f1c, #a67c30); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 36px; margin: 0 auto;">
                    ${applicant.applicant_name.substring(0, 1).toUpperCase()}
                </div>
                <h5 class="mt-3 mb-0">${applicant.applicant_name}</h5>
                <p class="text-muted mb-0">${applicant.position}</p>
            </div>
            <div class="col-md-9">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Email:</strong></p>
                        <p class="text-muted">${applicant.email}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Phone:</strong></p>
                        <p class="text-muted">${applicant.phone}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Age:</strong></p>
                        <p class="text-muted">${applicant.age} years</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Experience:</strong></p>
                        <p class="text-muted">${applicant.experience}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Date Applied:</strong></p>
                        <p class="text-muted">${formattedDate}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Status:</strong></p>
                        <span class="badge badge-${applicant.documents_uploaded === applicant.documents_total ? 'success' : 'warning'}">
                            ${applicant.status}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Document Submission Progress:</strong></p>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${docsPercentage}%;" aria-valuenow="${docsPercentage}" aria-valuemin="0" aria-valuemax="100">
                            ${applicant.documents_uploaded}/${applicant.documents_total} (${docsPercentage.toFixed(0)}%)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('viewApplicantBody').innerHTML = modalBody;
    $('#viewApplicantModal').modal('show');
}

function approveFromModal() {
    $('#viewApplicantModal').modal('hide');
    setTimeout(() => approveApplicant(currentApplicantId), 300);
}

function rejectFromModal() {
    $('#viewApplicantModal').modal('hide');
    setTimeout(() => rejectApplicant(currentApplicantId), 300);
}

function approveApplicant(id) {
    const applicant = applicantsData.find(a => a.id === id);
    if (!applicant) return;
    
    currentApplicantId = id;
    
    const infoHtml = `
        <div class="alert alert-info mt-3">
            <strong>Applicant:</strong> ${applicant.applicant_name}<br>
            <strong>Position:</strong> ${applicant.position}<br>
            <strong>Email:</strong> ${applicant.email}
        </div>
    `;
    
    document.getElementById('approveApplicantInfo').innerHTML = infoHtml;
    $('#approveApplicantModal').modal('show');
}

function confirmApprove() {
    if (currentApplicantId) {
        // Add your approval logic here
        alert('Applicant ID: ' + currentApplicantId + ' has been approved and added as staff');
        
        // Close modal and optionally refresh the page or update the table
        $('#approveApplicantModal').modal('hide');
        // window.location.reload();
    }
}

function rejectApplicant(id) {
    const applicant = applicantsData.find(a => a.id === id);
    if (!applicant) return;
    
    currentApplicantId = id;
    
    const infoHtml = `
        <div class="alert alert-warning mt-3">
            <strong>Applicant:</strong> ${applicant.applicant_name}<br>
            <strong>Position:</strong> ${applicant.position}<br>
            <strong>Email:</strong> ${applicant.email}
        </div>
    `;
    
    document.getElementById('rejectApplicantInfo').innerHTML = infoHtml;
    $('#rejectApplicantModal').modal('show');
}

function confirmReject() {
    if (currentApplicantId) {
        const reason = document.getElementById('rejectionReason').value;
        
        // Add your rejection logic here
        alert('Applicant ID: ' + currentApplicantId + ' has been rejected' + (reason ? ' with reason: ' + reason : ''));
        
        // Reset rejection reason field
        document.getElementById('rejectionReason').value = '';
        
        // Close modal and optionally refresh the page or update the table
        $('#rejectApplicantModal').modal('hide');
        // window.location.reload();
    }
}

// Search functionality
document.getElementById('applicantSearch')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const filtered = applicantsData.filter(applicant =>
        applicant.applicant_name.toLowerCase().includes(searchTerm) ||
        applicant.email.toLowerCase().includes(searchTerm) ||
        applicant.position.toLowerCase().includes(searchTerm)
    );
    // Render filtered results (you can implement a render function)
});

// Position filter
document.getElementById('positionFilter')?.addEventListener('change', function() {
    const filterValue = this.value;
    const filtered = filterValue === 'all' ? applicantsData : applicantsData.filter(applicant => applicant.position === filterValue);
    // Render filtered results
});

// Document filter
document.getElementById('documentFilter')?.addEventListener('change', function() {
    const filterValue = this.value;
    let filtered = applicantsData;
    if (filterValue === 'complete') {
        filtered = applicantsData.filter(applicant => applicant.documents_uploaded === applicant.documents_total);
    } else if (filterValue === 'incomplete') {
        filtered = applicantsData.filter(applicant => applicant.documents_uploaded < applicant.documents_total);
    }
    // Render filtered results
});

// Initialize DataTable
$(document).ready(function() {
    $('#applicantsTable').DataTable();
});
</script>

<?php require_once ROOT . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
