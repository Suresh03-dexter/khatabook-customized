<?php
// session_start();

// // First check: Is user logged in?
// if (!isset($_SESSION['username'])) {
//     header('Location: http://localhost/khatabook/google-login.php');
//     exit();
// }

// // Second check: Is user an admin?
// if ($_SESSION['role'] !== 'admin') {
//     header("Location: http://localhost/khatabook/pages/bussiness.php");
//     exit();
// }

include 'navbar.php';
include '../config/db.php';
include '../config/csrf.php';
$csrf_token = generateToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo generateToken() ?>">
  <title>User Role Management - PKhatabook UI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .card { border-radius: 15px; }
    .hidden { display: none !important; }
    .header { background: #2d2dff; color: #fff; padding: 15px; border-radius: 15px 15px 0 0; }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="card shadow">
    <div class="header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-person-gear"></i> Admin Dashboard</h5>
      <select id="roleSelect" class="form-select w-auto">
        <option value="admin">Admin</option>
        <option value="subadmin">Sub-admin</option>
      </select>
    </div>

    <div class="card-body">

      <!-- User Management Section -->
      <div id="userManagement">
        <h6><i class="bi bi-person-lines-fill"></i> User Management</h6>
        <button class="btn btn-primary btn-sm my-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
          <i class="bi bi-plus-circle"></i> Add New User
        </button>
        <table class="table table-bordered table-sm">
          <thead class="table-light">
            <tr>
              <th>User</th><th>Role</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="userList">
           <td>
            
            </td>
          </tr>
          </tbody>

        </table>
      </div>

<!-- Financial Reports (Admin Only) -->
<div id="financialReports">
    <h6><i class="bi bi-bar-chart-line-fill"></i> Financial Reports</h6>
    <p>Access to full reports including profit/loss, billing summary, and expense charts.</p>
    
    <!-- Download button calls JS function -->
    <button class="btn btn-success btn-sm" onclick="downloadFinancialReport()">
        <i class="bi bi-file-earmark-arrow-down"></i> Download
    </button>
    
    <button class="btn btn-secondary btn-sm" onclick="window.print();">
        <i class="bi bi-printer"></i> Print
    </button>

    <!-- Hidden download form -->
    <form id="financialReportForm" 
          action="../api/reports/financial_report_pdf.php" 
          method="POST" 
          target="_blank" 
          class="d-none">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="from" value="<?= date('Y-m-01') ?>">
        <input type="hidden" name="to" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="type" value="all">
    </form>
</div>


      <!-- Sales & Purchase Section -->
      <div id="salesPurchases">
        <h6 class="mt-4"><i class="bi bi-cart-check-fill"></i> Sales & Purchase Records</h6>
        <p>Manage sales and purchases easily with export options.</p>
        <button class="btn btn-info btn-sm"><i class="bi bi-cloud-download"></i> Export CSV</button>
      </div>

    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill"></i> Add New User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addUserForm">
          <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $csrf_token ?>">

          <div class="mb-2">
            <label class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="Enter user name" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Role</label>
            <select name="role" id="role" class="form-select">
              <option value="admin">Admin</option>
              <option value="subadmin">Sub-admin</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-success"><i class="bi bi-save2-fill"></i> Save User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin_user.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</body>
</html>
