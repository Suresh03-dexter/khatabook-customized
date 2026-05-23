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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Parties – My Business</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .floating-btn {
      position: fixed;
      top: 130px;
      right: 20px;
      z-index: 1000;
    }
    .tab-active {
      border-bottom: 3px solid #f1c40f;
      font-weight: 600;
    }
    .search-bar {
      border-radius: 12px;
      background-color: #f5f5f5;
    }
    .customer-card:hover {
      background-color: #f8f9fa;
      transform: translateY(-2px);
      transition: 0.2s;
    }
    .customer-card a {
      text-decoration: none;
      margin-right: 6px;
    }
    .customer-card a:last-child {
      margin-right: 0;
    }
    #filterMenu {
      min-width: 180px;
    }
  </style>
</head>
<body class="bg-light" data-role="<?php echo $_SESSION['role'] ?? 'subadmin'; ?>">

<!-- ✅ Alert Box -->
<div class="container mt-3">
  <div id="alertBox" class="alert alert-success alert-dismissible fade show d-none" role="alert">
    <span id="alertMessage">Customer added successfully.</span>
    <button type="button" class="btn-close" onclick="hideAlert()" aria-label="Close"></button>
  </div>
</div>

<!-- ✅ Top Header -->
<nav class="navbar navbar-dark bg-primary px-3">
  <div class="d-flex justify-content-between w-100 align-items-center">
    <div class="text-white fw-bold">
      <i class="fas fa-book me-2"></i>My Business <i class="fas fa-pen ms-2"></i>
    </div>
    <div></div>
  </div>
</nav>

<!-- ✅ Tabs -->
<div class="d-flex justify-content-around border-bottom pb-2">
  <a href="business.php" class="text-decoration-none text-primary tab-active">CUSTOMERS</a>
  <a href="suppliers.php" class="text-decoration-none text-secondary">SUPPLIERS</a>
</div>

<!-- ✅ Summary Card -->
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
    <button class="btn btn-outline-primary btn-sm" id="showReportsBtn">
      <i class="fas fa-file-pdf me-1"></i> View Reports
    </button>
  </div>
</div>

<!-- ✅ Search Bar + Filter + Cashbook -->
<div class="container mt-4">
  <div class="row align-items-center g-2">
    <div class="col-12 col-md-6">
      <input type="text" id="customerSearch" class="form-control" placeholder="Search customers by name or mobile...">
    </div>
    <div class="col-12 col-md-6 d-flex gap-2 position-relative">
      <button class="btn btn-outline-primary" type="button" id="filterButton">
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
      <a href="../pages/cashbook.php" class="btn btn-primary">Cashbook</a>
    </div>
  </div>
</div>

<!-- ✅ Customer List -->
<div class="container mt-3" id="customerList" style="padding-bottom: 80px;">
  <!-- Customers will load dynamically -->
</div>

<!-- ✅ Bottom Navigation -->
<nav class="navbar navbar-light bg-white fixed-bottom border-top">
  <div class="container-fluid d-flex justify-content-center">
    <button class="btn btn-danger rounded-pill px-4 py-2 shadow d-flex align-items-center justify-content-center" 
            data-bs-toggle="modal" data-bs-target="#addCustomerModal">
      <i class="fas fa-user-plus"></i>
    </button>
  </div>
</nav>

<!-- ✅ Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="addCustomerForm">
      <div class="modal-header">
        <h5 class="modal-title">Add Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Customer Name</label>
          <input type="text" class="form-control" id="customerName" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mobile Number</label>
          <input type="tel" class="form-control" id="mobile">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Customer</button>
      </div>
    </form>
  </div>
</div>

<!-- ✅ Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="editCustomerForm">
      <div class="modal-header">
        <h5 class="modal-title">Edit Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editCustomerId">
        <div class="mb-3">
          <label class="form-label">Customer Name</label>
          <input type="text" class="form-control" id="editCustomerName" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mobile Number</label>
          <input type="tel" class="form-control" id="editMobile" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Initial Balance</label>
          <input type="number" class="form-control" id="editInitialBalance">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Customer</button>
      </div>
    </form>
  </div>
</div>

<!-- ✅ Reports Modal (Matches fetch_report.php & business.js) -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Customer Transaction Reports</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <label>Customer</label>
            <select id="reportCustomerDropdown" class="form-select">
              <option value="">All Customers</option>
            </select>
          </div>
          <div class="col-md-4">
            <label>From Date</label>
            <input type="date" id="fromDate" class="form-control">
          </div>
          <div class="col-md-4">
            <label>To Date</label>
            <input type="date" id="toDate" class="form-control">
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered" id="reportTable">
            <thead>
              <tr>
                <th>Customer Name</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Total</th>
                <th>Date & Time</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="text-end">
          <button class="btn btn-danger" id="downloadPDF">
            <i class="fas fa-file-pdf"></i> Download PDF
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Customer Transactions Modal -->
<div class="modal fade" id="customerTransactionsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerNameTitle">Customer Transactions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- ✅ Add Item Form -->
        <form id="addItemForm" class="row g-3 mb-3">
          <input type="hidden" id="transactionCustomerId">
          <div class="col-md-4">
            <input type="text" class="form-control" id="itemName" placeholder="Item Name" required>
          </div>
          <div class="col-md-3">
            <input type="text" class="form-control" id="quantity" placeholder="e.g. 10 kg, 5 pcs" inputmode="text" required>
          </div>
          <div class="col-md-3">
            <input type="number" class="form-control" id="amount" placeholder="Amount" required>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Save</button>
          </div>
        </form>

        <!-- ✅ Transactions Table -->
        <div class="table-responsive">
          <table class="table table-bordered" id="customerTransactionsTable">
            <thead>
              <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Date & Time</th>
              </tr>
            </thead>
            <tbody>
              <!-- Transactions will load here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Bootstrap + JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="../assets/js/business.js" defer></script>

</body>
</html>
