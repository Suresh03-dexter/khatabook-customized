<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: http://localhost/khatabook/google-login.php');
    exit();
}
?>

<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staffs - Khatabook</title>

  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    h4 { font-weight: 600; color: #333; }
    .card { border-radius: 12px; }
    .table thead th { font-weight: 600; background-color: #f1f3f5; }
    .btn-primary { background-color: #ff4d4d; border-color: #ff4d4d; }
    .btn-primary:hover { background-color: #e04343; border-color: #e04343; }
    .modal-content { border-radius: 12px; }
    .form-label { font-weight: 500; }
    .btn-info { background-color: #0dcaf0; border-color: #0dcaf0; color: white; }
    .btn-info:hover { background-color: #0bb1d6; border-color: #0bb1d6; }
    .btn-danger { background-color: #ff4d4d; border-color: #ff4d4d; }
    .btn-danger:hover { background-color: #e04343; border-color: #e04343; }
    .spinner-border-sm { width: 1rem; height: 1rem; }
    .alert { border-radius: 8px; }
  </style>
</head>
<body>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Staffs</h4>
    <button class="btn btn-primary" onclick="openAddStaffModal()">
      <i class="bi bi-plus-circle"></i> Add Staff
    </button>
  </div>

  <!-- Alert for messages -->
  <div id="alertContainer"></div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Staff Name (Subadmin)</th>
              <th>Role</th>
              <th>Mobile</th>
              <th>Address</th>
              <th>Date Added</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="staffTableBody">
            <tr>
              <td colspan="5" class="text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                Loading staffs...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStaffModalLabel">Add Staff</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addStaffForm">
          <div class="mb-3">
            <label class="form-label">Select Subadmin <span class="text-danger">*</span></label>
            <select id="subadminDropdown" name="subadminId" class="form-select" required>
              <option value="">Loading subadmins...</option>
            </select>
            <div class="form-text text-danger" id="subadminError"></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Staff Role <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="staffRole" placeholder="e.g., Cashier, Manager, Sales Associate" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" name="staffMobile" placeholder="10-digit mobile number" pattern="[0-9]{10}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="staffAddress" rows="3" placeholder="Complete address (optional)"></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-100" id="saveStaffBtn">
            <span class="btn-text">Save Staff</span>
            <span class="btn-spinner d-none">
              <span class="spinner-border spinner-border-sm" role="status"></span>
              Saving...
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editStaffForm">
          <input type="hidden" name="staffId">
          <div class="mb-3">
            <label class="form-label">Select Subadmin <span class="text-danger">*</span></label>
            <select name="subadminId" class="form-select" required>
              <!-- Options will be loaded here -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Staff Role <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="staffRole" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" name="staffMobile" pattern="[0-9]{10}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="staffAddress" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-100" id="updateStaffBtn">
            <span class="btn-text">Update Staff</span>
            <span class="btn-spinner d-none">
              <span class="spinner-border spinner-border-sm" role="status"></span>
              Updating...
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this staff member?</p>
        <p class="text-muted"><strong id="deleteStaffName"></strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
          <span class="btn-text">Delete</span>
          <span class="btn-spinner d-none">
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Deleting...
          </span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Global variables
let staffToDelete = null;
let addStaffModal = null;
let editStaffModal = null;
let deleteConfirmModal = null;

// Initialize modals when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    addStaffModal = new bootstrap.Modal(document.getElementById('addStaffModal'));
    editStaffModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
    deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    
    loadStaffTable();
    setupFormHandlers();
});

// Show alert messages
function showAlert(message, type = 'danger') {
    const alertContainer = document.getElementById('alertContainer');
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    alertContainer.innerHTML = alertHtml;
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);
}

// Load staff table
function loadStaffTable() {
    const tbody = document.getElementById('staffTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading staffs...
            </td>
        </tr>
    `;

    fetch('../api/staffs/get_staffs.php')
    .then(response => response.json())
    .then(data => {
        tbody.innerHTML = '';
        if (data.length > 0) {
            data.forEach((staff, index) => {
                const dateAdded = staff.joined_at ? new Date(staff.joined_at).toLocaleDateString() : 'N/A';
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(staff.staff_name)} <br><small class="text-muted">(@${escapeHtml(staff.subadmin_name || 'N/A')})</small></td>
                        <td><span class="badge bg-secondary">${escapeHtml(staff.staff_role || 'N/A')}</span></td>
                        <td>${escapeHtml(staff.staff_mobile || 'N/A')}</td>
                        <td><small>${escapeHtml(staff.staff_address || 'N/A')}</small></td>
                        <td>${escapeHtml(staff.date_added || 'N/A')}</td>
                        <td>
                            <button class="btn btn-sm btn-info me-1" onclick="editStaff(${staff.id})" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDeleteStaff(${staff.id}, '${escapeHtml(staff.staff_name)}')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">No staffs found. Click "Add Staff" to get started.</td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading staffs:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-danger">Error loading staffs. Please refresh the page.</td>
            </tr>
        `;
    });
}

// Load subadmins dropdown
function loadSubadminsDropdown(selectElement) {
    selectElement.innerHTML = '<option value="">Loading...</option>';
    selectElement.disabled = true;

    fetch('../api/staffs/get_subadmins.php')
    .then(response => response.json())
    .then(data => {
        selectElement.innerHTML = '';
        if (data.length > 0) {
            selectElement.innerHTML = '<option value="">Select Subadmin</option>';
            data.forEach(subadmin => {
                const option = document.createElement('option');
                option.value = subadmin.id;
                option.textContent = subadmin.username;
                selectElement.appendChild(option);
            });
            selectElement.disabled = false;
        } else {
            selectElement.innerHTML = '<option value="">No subadmins found</option>';
            document.getElementById('subadminError').textContent = 'No subadmins found. Please add subadmins first.';
        }
    })
    .catch(error => {
        console.error('Error loading subadmins:', error);
        selectElement.innerHTML = '<option value="">Error loading subadmins</option>';
        document.getElementById('subadminError').textContent = 'Error loading subadmins!';
    });
}

// Open add staff modal
function openAddStaffModal() {
    document.getElementById('addStaffForm').reset();
    document.getElementById('subadminError').textContent = '';
    loadSubadminsDropdown(document.getElementById('subadminDropdown'));
    addStaffModal.show();
}

// Setup form handlers
function setupFormHandlers() {
    // Add Staff Form
    document.getElementById('addStaffForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveStaff();
    });

    // Edit Staff Form
    document.getElementById('editStaffForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateStaff();
    });

    // Delete confirmation
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (staffToDelete) {
            deleteStaff(staffToDelete);
        }
    });
}

// Save new staff
function saveStaff() {
    const form = document.getElementById('addStaffForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveStaffBtn');
    
    // Show loading state
    saveBtn.querySelector('.btn-text').classList.add('d-none');
    saveBtn.querySelector('.btn-spinner').classList.remove('d-none');
    saveBtn.disabled = true;

    fetch('../api/staffs/add_staffs.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addStaffModal.hide();
            loadStaffTable();
            showAlert('Staff added successfully!', 'success');
            form.reset();
        } else {
            showAlert(data.message || 'Failed to add staff');
        }
    })
    .catch(error => {
        console.error('Error saving staff:', error);
        showAlert('An error occurred while saving staff');
    })
    .finally(() => {
        // Reset loading state
        saveBtn.querySelector('.btn-text').classList.remove('d-none');
        saveBtn.querySelector('.btn-spinner').classList.add('d-none');
        saveBtn.disabled = false;
    });
}

// Edit staff
function editStaff(staffId) {
    // Load subadmins for edit form
    const editSelect = document.querySelector('#editStaffForm select[name="subadminId"]');
    loadSubadminsDropdown(editSelect);
    
    // Fetch staff data
    fetch(`../api/staffs/edit_staff.php?id=${staffId}`)
    .then(response => response.json())
    .then(staff => {
        if (staff.error) {
            showAlert(staff.error);
            return;
        }
        
        document.querySelector('#editStaffForm input[name="staffId"]').value = staff.id;
        document.querySelector('#editStaffForm input[name="staffRole"]').value = staff.staff_role || '';
        document.querySelector('#editStaffForm input[name="staffMobile"]').value = staff.staff_mobile || '';
        document.querySelector('#editStaffForm textarea[name="staffAddress"]').value = staff.staff_address || '';
        
        // Set subadmin after dropdown loads
        setTimeout(() => {
            document.querySelector('#editStaffForm select[name="subadminId"]').value = staff.subadmin_id;
        }, 500);
        
        editStaffModal.show();
    })
    .catch(error => {
        console.error('Error fetching staff:', error);
        showAlert('Error loading staff data');
    });
}

// Update staff
function updateStaff() {
    const form = document.getElementById('editStaffForm');
    const formData = new FormData(form);
    const updateBtn = document.getElementById('updateStaffBtn');
    
    // Show loading state
    updateBtn.querySelector('.btn-text').classList.add('d-none');
    updateBtn.querySelector('.btn-spinner').classList.remove('d-none');
    updateBtn.disabled = true;

    fetch('../api/staffs/update_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            editStaffModal.hide();
            loadStaffTable();
            showAlert(data.message || 'Staff updated successfully!', 'success');
            form.reset();
        } else {
            showAlert(data.message || 'Failed to update staff');
        }
    })
    .catch(error => {
        console.error('Error updating staff:', error);
        showAlert('An error occurred while updating staff');
    })
    .finally(() => {
        // Reset loading state
        updateBtn.querySelector('.btn-text').classList.remove('d-none');
        updateBtn.querySelector('.btn-spinner').classList.add('d-none');
        updateBtn.disabled = false;
    });
}

// Confirm delete staff
function confirmDeleteStaff(staffId, staffName) {
    staffToDelete = staffId;
    document.getElementById('deleteStaffName').textContent = staffName;
    deleteConfirmModal.show();
}

// Delete staff
function deleteStaff(staffId) {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    
    // Show loading state
    deleteBtn.querySelector('.btn-text').classList.add('d-none');
    deleteBtn.querySelector('.btn-spinner').classList.remove('d-none');
    deleteBtn.disabled = true;

    const formData = new FormData();
    formData.append('staffId', staffId);

    fetch('../api/staffs/delete_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        deleteConfirmModal.hide();
        if (data.success) {
            loadStaffTable();
            showAlert(data.message || 'Staff deleted successfully!', 'success');
        } else {
            showAlert(data.message || 'Failed to delete staff');
        }
    })
    .catch(error => {
        console.error('Error deleting staff:', error);
        showAlert('An error occurred while deleting staff');
        deleteConfirmModal.hide();
    })
    .finally(() => {
        // Reset loading state
        deleteBtn.querySelector('.btn-text').classList.remove('d-none');
        deleteBtn.querySelector('.btn-spinner').classList.add('d-none');
        deleteBtn.disabled = false;
        staffToDelete = null;
    });
}

// Utility function to escape HTML
function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');
}

</script>

</body>
</html>