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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PKhataBook - Purchase Records</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    body {
      background-color: #f9f9f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .header {
      background-color: #fff;
      padding: 1rem;
      border-bottom: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .tab-btn {
      border: none;
      background: none;
      font-weight: bold;
      color: #555;
    }
    .tab-btn.active {
      border-bottom: 3px solid #ffc107;
      color: #000;
    }
    .purchase-card {
      background-color: #fff;
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      box-shadow: 0 0 6px rgba(0,0,0,0.1);
    }
    .status-paid {
      color: green;
      font-weight: bold;
    }
    .status-pending {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>

<!-- 🔝 Top Header -->
<div class="header">
  <h5>My Business <i class="fas fa-pen ms-2 text-primary"></i></h5>
  <div>
    <button class="btn btn-outline-secondary me-2" onclick="openReport()">
      <i class="fas fa-file-alt"></i> Reports
    </button>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPurchaseModal"><i class="fas fa-plus"></i> Add Purchase</button>
  </div>
</div>

<!-- 🧾 Tabs -->
<div class="d-flex justify-content-around mt-3 mb-2">
  <button class="tab-btn active" onclick="showTab('purchaseTab')">Purchases</button>
  <button class="tab-btn" onclick="showTab('summaryTab')">Summary</button>
</div>

<!-- 📦 Purchase List -->
<div class="container" id="purchaseTab">
  <div id="purchaseList">
    <!-- AJAX will load purchases here -->
  </div>
</div>

<!-- 📊 Summary Tab -->
<div class="container d-none" id="summaryTab">
  <div class="alert alert-info mt-3">Summary feature under development.</div>
</div>

<!-- ➕ Add Purchase Modal -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-labelledby="addPurchaseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addPurchaseForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addPurchaseModalLabel">Add Purchase</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Select Customer</label>
            <select class="form-select" name="customer_id" id="customer" required>
              <option value="">-- Select Customer --</option>
              <?php
              require_once '../config/db.php';
              $user_id = $_SESSION['user_id'];
              $result = mysqli_query($conn, "SELECT id, name FROM customers WHERE user_id = $user_id");
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Customer Type</label>
            <select class="form-select" id="customerType" required>
              <option value="">-- Select Type --</option>
              <option value="people">People</option>
              <option value="hotel">Hotel</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customerName" placeholder="People/Hotel Name" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" id="amount" placeholder="₹" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Pending Amount</label>
            <input type="number" class="form-control" id="pendingAmount" placeholder="₹" />
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="status">
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Purchase</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 🧾 Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">📈 Purchase Summary Report</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <li class="list-group-item">💰 Total Purchases: ₹<span id="r_total_purchase">0</span></li>
          <li class="list-group-item">⏳ Total Pending: ₹<span id="r_total_pending">0</span></li>
          <li class="list-group-item text-success">✔️ Paid Purchases: <span id="r_paid_count">0</span></li>
          <li class="list-group-item text-danger">❗ Pending Purchases: <span id="r_pending_count">0</span></li>
          <li class="list-group-item">👥 Frequent Customer: <span id="r_top_customer">N/A</span></li>
          <li class="list-group-item">🕒 Last Purchase: <span id="r_last_purchase">N/A</span></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- ✏️ Edit Purchase Modal -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1" aria-labelledby="editPurchaseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editPurchaseForm">
        <input type="hidden" id="editId" />
        <div class="modal-header">
          <h5 class="modal-title" id="editPurchaseModalLabel">Edit Purchase</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="editCustomerName" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Customer Type</label>
            <select class="form-select" id="editCustomerType" required>
              <option value="people">People</option>
              <option value="hotel">Hotel</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" id="editAmount" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Pending Amount</label>
            <input type="number" class="form-control" id="editPendingAmount" />
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="editStatus">
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Purchase</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ✅ JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showTab(tabId) {
  document.getElementById('purchaseTab').classList.add('d-none');
  document.getElementById('summaryTab').classList.add('d-none');
  document.getElementById(tabId).classList.remove('d-none');
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
}

document.getElementById("addPurchaseForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const data = {
    customer_id: document.getElementById("customer").value,
    customer_name: document.getElementById("customerName").value,
    customer_type: document.getElementById("customerType").value, // ✅ added
    amount: document.getElementById("amount").value,
    pending_amount: document.getElementById("pendingAmount").value,
    status: document.getElementById("status").value
  };

  fetch("../api/add_purchase.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      document.getElementById("addPurchaseForm").reset();
      bootstrap.Modal.getInstance(document.getElementById("addPurchaseModal")).hide();
      alert("✅ Purchase added successfully!");
      document.dispatchEvent(new Event('dashboardUpdate'));
      loadPurchases();
    } else {
      alert("❌ Failed to save purchase: " + response.message);
    }
  });
});

function loadPurchases() {
  fetch("../api/get_purchases.php?user_id=<?= $_SESSION['user_id'] ?>")
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById("purchaseTab");
      container.innerHTML = "";
      data.forEach(p => {
        const card = document.createElement("div");
        card.className = "purchase-card";
        card.innerHTML = `
          <div class="d-flex justify-content-between">
            <div>
              <h6>${p.customer_name} <small class="text-muted">(${p.customer_type})</small></h6>
              <p class="mb-0 text-muted">Amount: ₹${p.amount}</p>
              <p class="mb-0">${
                p.status === "pending"
                  ? `<span class="status-pending">Pending</span> - ₹${p.pending_amount}`
                  : `<span class="status-paid">Paid</span>`
              }</p>
            </div>
            <div class="text-end">
              <button class="btn btn-outline-primary btn-sm" onclick='openEditModal(${JSON.stringify(p)})'><i class="fas fa-pen"></i></button>
              <button class="btn btn-outline-success btn-sm" onclick="window.open('https://wa.me/91${p.mobile}', '_blank')"><i class="fab fa-whatsapp"></i></button>
              <button class="btn btn-outline-secondary btn-sm" onclick="window.location.href='tel:${p.mobile}'"><i class="fas fa-phone"></i></button>
            </div>
          </div>`;
        container.appendChild(card);
      });
    });
}
window.onload = loadPurchases;

function openEditModal(p) {
  document.getElementById("editId").value = p.id;
  document.getElementById("editCustomerName").value = p.customer_name;
  document.getElementById("editCustomerType").value = p.customer_type; // ✅ added
  document.getElementById("editAmount").value = p.amount;
  document.getElementById("editPendingAmount").value = p.pending_amount;
  document.getElementById("editStatus").value = p.status;
  new bootstrap.Modal(document.getElementById("editPurchaseModal")).show();
}

document.getElementById("editPurchaseForm").addEventListener("submit", function(e) {
  e.preventDefault();
  const data = {
    id: document.getElementById("editId").value,
    customer_name: document.getElementById("editCustomerName").value,
    customer_type: document.getElementById("editCustomerType").value, // ✅ added
    amount: document.getElementById("editAmount").value,
    pending_amount: document.getElementById("editPendingAmount").value,
    status: document.getElementById("editStatus").value
  };

  fetch("../api/update_purchase.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      bootstrap.Modal.getInstance(document.getElementById("editPurchaseModal")).hide();
      alert("✅ Purchase updated!");
      document.dispatchEvent(new Event('dashboardUpdate'));
      loadPurchases();
    } else {
      alert("❌ Update failed: " + response.message);
    }
  });
});

function openReport() {
  const modal = new bootstrap.Modal(document.getElementById("reportModal"));
  modal.show();
  fetch("../api/fetch_purchase_report.php", { credentials: "include" })
    .then(res => res.json())
    .then(data => {
      document.getElementById("r_total_purchase").textContent = data.total_purchase;
      document.getElementById("r_total_pending").textContent = data.total_pending;
      document.getElementById("r_paid_count").textContent = data.paid_count;
      document.getElementById("r_pending_count").textContent = data.pending_count;
      document.getElementById("r_top_customer").textContent = data.top_customer;
      document.getElementById("r_last_purchase").textContent = data.last_purchase;
    })
    .catch(() => { alert("⚠️ Failed to load report."); });
}
</script>
</body>
</html>
