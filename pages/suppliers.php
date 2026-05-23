<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: http://localhost/khatabook/google-login.php');
    exit();
}
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers – My Business</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- jsPDF autoTable plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <style>
    body { background-color: #f7f9fc; }
    .tab-active { border-bottom: 3px solid #f1c40f; font-weight: 600; }
    .supplier-card:hover { background-color: #f8f9fa; transform: translateY(-2px); transition: 0.2s; }
    .floating-btn { position: fixed; bottom: 90px; right: 20px; z-index: 1000; }
    #filterMenu { min-width: 180px; }
  </style>
</head>

<body class="bg-light" data-role="<?php echo $_SESSION['role'] ?? 'subadmin'; ?>">

<!-- ✅ Top Tabs -->
<div class="container mt-3">
  <div class="d-flex justify-content-around border-bottom pb-2">
    <a href="bussiness.php" class="text-decoration-none text-secondary">CUSTOMERS</a>
    <a href="suppliers.php" class="text-decoration-none text-primary tab-active">SUPPLIERS</a>
  </div>
</div>

<!-- ✅ Summary Box -->
<div class="container mt-3">
  <div class="bg-white rounded shadow-sm p-3 d-flex justify-content-between text-center">
    <div>
      <div class="text-muted small">You will give</div>
      <div id="youWillGive" class="text-success fw-bold">₹0</div>
    </div>
    <div>
      <div class="text-muted small">You will get</div>
      <div id="youWillGet" class="text-danger fw-bold">₹0</div>
    </div>
  </div>
  <div class="text-center mt-2">
    <button class="btn btn-outline-primary btn-sm" onclick="loadSupplierReport(); $('#supplierReportModal').modal('show');">
      <i class="fas fa-file-pdf me-1"></i> View Reports
    </button>
  </div>

<!-- ✅ Search + Filter -->
<div class="container mt-4">
  <div class="row align-items-center g-2">
    <div class="col-12 col-md-6">
      <input type="text" id="supplierSearch" class="form-control" placeholder="Search suppliers by name or mobile...">
    </div>
    <div class="col-12 col-md-6 d-flex gap-2 position-relative">
      <button class="btn btn-outline-primary" type="button" id="supplierFilterButton">
        <i class="fas fa-filter"></i> Filter
      </button>
      <div id="filterMenu" class="d-none position-absolute bg-white border rounded shadow-sm mt-2 p-2" style="z-index: 1050;">
        <a class="dropdown-item" href="#" data-filter="recent">Most Recent</a>
        <a class="dropdown-item" href="#" data-filter="highest">Highest Amount</a>
        <a class="dropdown-item" href="#" data-filter="az">By Name (A-Z)</a>
        <a class="dropdown-item" href="#" data-filter="oldest">Oldest</a>
        <a class="dropdown-item" href="#" data-filter="least">Least Amount</a>
        <a class="dropdown-item" href="#" data-filter="you_get">You Will Get</a>
        <a class="dropdown-item" href="#" data-filter="you_give">You Will Give</a>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Supplier List -->
<div class="container mt-2" id="supplierList">
  <!-- Suppliers will load dynamically via AJAX -->
</div>
<!-- ✅ Bottom Navigation with Add Supplier Center -->
<nav class="navbar navbar-light bg-white fixed-bottom border-top">
  <div class="container-fluid d-flex justify-content-center">
    <button class="btn btn-success rounded-pill px-4 py-2 shadow d-flex align-items-center"
            data-bs-toggle="modal" data-bs-target="#addSupplierModal">
      <i class="fas fa-user-plus me-2"></i>
    </button>
  </div>
</nav>


<!-- ✅ Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="addSupplierForm">
      <div class="modal-header">
        <h5 class="modal-title">Add Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="supplierName" class="form-label">Supplier Name</label>
          <input type="text" class="form-control" id="supplierName" required>
        </div>
        <div class="mb-3">
          <label for="supplierMobile" class="form-label">Mobile Number</label>
          <input type="tel" class="form-control" id="supplierMobile" maxlength="10" pattern="[0-9]{10}">
        </div>
        <div class="mb-3">
          <label for="productType" class="form-label">Product Type</label>
          <select class="form-select" id="productType">
            <option value="Chicken">Chicken</option>
            <option value="Mutton">Mutton</option>
            <option value="Fish">Fish</option>
          </select>
        </div>
        <div class="mb-3">
        <label for="addyouWillGive" class="form-label">You Will Give (₹)</label>
        <input type="number" class="form-control" id="addyouWillGive" placeholder="0">
        </div>
        <div class="mb-3">
        <label for="addyouWillGet" class="form-label">You Will Get (₹)</label>
        <input type="number" class="form-control" id="addyouWillGet" placeholder="0">
        </div>
        <div class="mb-3">
          <label for="supplierAddress" class="form-label">Address</label>
          <input type="text" class="form-control" id="supplierAddress">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Supplier</button>
      </div>
    </form>
  </div>
</div>
<!-- ✅ Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="editSupplierForm">
      <div class="modal-header">
        <h5 class="modal-title">Edit Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editSupplierId">

        <div class="mb-3">
          <label for="editSupplierName" class="form-label">Supplier Name</label>
          <input type="text" class="form-control" id="editSupplierName" required>
        </div>
        <div class="mb-3">
          <label for="editSupplierMobile" class="form-label">Mobile Number</label>
          <input type="tel" class="form-control" id="editSupplierMobile">
        </div>
        <div class="mb-3">
          <label for="editProductType" class="form-label">Product Type</label>
          <select class="form-select" id="editProductType">
            <option value="Chicken">Chicken</option>
            <option value="Mutton">Mutton</option>
            <option value="Fish">Fish</option>
          </select>
        </div>
        <div class="mb-3">
            <label for="editYouWillGive" class="form-label">You Will Give (₹)</label>
            <input type="number" step="0.01" class="form-control" id="editYouWillGive" name="editYouWillGive">
        </div>
        <div class="mb-3">
            <label for="editYouWillGet" class="form-label">You Will Get (₹)</label>
            <input type="number" step="0.01" class="form-control" id="editYouWillGet" name="editYouWillGet">
        </div>
        <div class="mb-3">
          <label for="editSupplierAddress" class="form-label">Address</label>
          <input type="text" class="form-control" id="editSupplierAddress">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Supplier</button>
      </div>
    </form>
  </div>
</div>
<!-- ✅ Supplier Details Modal -->
<div class="modal fade" id="supplierDetailsModal" tabindex="-1" aria-labelledby="supplierDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Supplier Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="supplierDetailsBody">
        <!-- Content will be loaded dynamically via JS -->
      </div>
    </div>
  </div>
</div>
<!-- ✅ Supplier Report Modal -->
<div class="modal fade" id="supplierReportModal" tabindex="-1" aria-labelledby="supplierReportLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="supplierReportLabel">Supplier Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <!-- 🔍 Filters -->
        <div class="row g-2 mb-3">
          <div class="col-md-4">
            <input type="text" class="form-control" id="supplierSearchInput" placeholder="Search Supplier">
          </div>
          <div class="col-md-4">
            <input type="date" class="form-control" id="supplierFromDate">
          </div>
          <div class="col-md-4">
            <input type="date" class="form-control" id="supplierToDate">
          </div>
          <div class="col-md-4">
            <button class="btn btn-success w-100" onclick="filterSupplierReport()">Apply Filters</button>
          </div>
        </div>

        <!-- 📋 Report Table -->
        <div class="table-responsive">
          <table class="table table-bordered" id="supplierReportTable">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Product Type</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Amount Give (₹)</th>
                <th>Amount Get (₹)</th>
                <th>Date & Time</th>
              </tr>
            </thead>
            <tbody id="supplierReportBody">
              <!-- Dynamically filled -->
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-danger" onclick="downloadSupplierReportPDF()">Download PDF</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/suppliers.js" defer></script>

</body>
</html>
