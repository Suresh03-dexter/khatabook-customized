<?php
session_start();
require_once '../config/db.php'; // adjust this path

if (!isset($_SESSION['user_id'])) {
  header('Location: http://localhost/khatabook/google-login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

// Get current date parts
date_default_timezone_set('Asia/Kolkata');
$refreshedAt = date('d M Y, h:i A');
$year = date('Y');
$month = date('m');
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));

// Query totals
function getExpenseTotal($conn, $user_id, $startDate, $endDate = null) {
  $query = "SELECT SUM(amount) AS total FROM expenses WHERE user_id = ? AND date >= ?";
  if ($endDate) $query .= " AND date <= ?";

  $stmt = $conn->prepare($query);
  if ($endDate) {
    $stmt->bind_param('iss', $user_id, $startDate, $endDate);
  } else {
    $stmt->bind_param('is', $user_id, $startDate);
  }

  $stmt->execute();
  $stmt->bind_result($total);
  $stmt->fetch();
  return $total ?? 0;
}

$yearlyTotal = getExpenseTotal($conn, $user_id, "$year-01-01");
$monthlyTotal = getExpenseTotal($conn, $user_id, "$year-$month-01");
$weeklyTotal = getExpenseTotal($conn, $user_id, $week_start, $week_end);
?>

<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Expenses Management | Pkhatabook UI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <style>
    body { background-color: #f8f9fa; }
    .pkh-header { background-color: #4b0082; color: white; padding: 15px; text-align: center; }
    .card-header { background-color: #6a1b9a; color: white; }
    .btn-purple { background-color: #6a1b9a; color: white; }
    .btn-purple:hover { background-color: #4b0082; color: #fff; }
    .fa { margin-right: 5px; }
  </style>
</head>
<body>

<div class="pkh-header">
  <h4><i class="fas fa-money-bill-wave"></i> Expenses Management</h4>
</div>

<div class="container py-4">

  <!-- Summary Cards -->
  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card text-white bg-success mb-3">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-coins"></i> Total Year</h5>
          <p class="card-text fs-4">₹<?= number_format($yearlyTotal, 2) ?></p>
          <p class="text-muted small mt-2">Last updated: <?= $refreshedAt ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-primary mb-3">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-calendar-day"></i> This Week</h5>
          <p class="card-text fs-4">₹<?= number_format($weeklyTotal, 2) ?></p>
          <p class="text-muted small mt-2">Last updated: <?= $refreshedAt ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-warning mb-3">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-calendar-alt"></i> This Month</h5>
          <p class="card-text fs-4">₹<?= number_format($monthlyTotal, 2) ?></p>
          <p class="text-muted small mt-2">Last updated: <?= $refreshedAt ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Expense List -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5>All Expenses</h5>
    <button class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
      <i class="fas fa-plus-circle"></i> Add Expense
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle bg-white">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Category</th>
          <th>Description</th>
          <th>Amount (₹)</th>
          <th>Action</th>
        </tr>
      </thead>
      <?php
include '../config/db.php'; // or wherever your DB connection file is

// Fetch expenses from DB
$query = "SELECT * FROM expenses ORDER BY date DESC";
$result = mysqli_query($conn, $query);
?>
<tbody>
<?php $i = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
  <tr>
    <td><?= $i++ ?></td>
    <td><?= $row['date'] ?></td>
    <td><?= $row['category'] ?></td>
    <td><?= $row['description'] ?></td>
    <td><?= $row['amount'] ?></td>
    <td>
      <!-- Edit Button -->
      <button 
        class="btn btn-sm btn-outline-secondary edit-expense-btn" 
        data-bs-toggle="modal" 
        data-bs-target="#editExpenseModal"
        data-id="<?= $row['id'] ?>"
        data-date="<?= $row['date'] ?>"
        data-category="<?= $row['category'] ?>"
        data-description="<?= htmlspecialchars($row['description']) ?>"
        data-amount="<?= $row['amount'] ?>"
      >
        <i class="fas fa-edit"></i>
      </button>

      <!-- Delete Button -->
      <button 
        class="btn btn-sm btn-outline-danger delete-expense-btn" 
        data-id="<?= $row['id'] ?>"
      >
        <i class="fas fa-trash-alt"></i>
      </button>
    </td>
  </tr>
<?php } ?>
</tbody>

    </table>
  </div>
</div>

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="editExpenseForm">
  <input type="hidden" name="id" id="editExpenseId">
  <div class="modal-body">
    <div class="mb-3">
      <label for="editExpenseDate" class="form-label">Date</label>
      <input type="date" class="form-control" name="date" id="editExpenseDate" required>
    </div>
    <div class="mb-3">
      <label for="editExpenseCategory" class="form-label">Category</label>
      <select class="form-select" name="category" id="editExpenseCategory" required>
        <option value="Travel">Travel</option>
        <option value="Food">Food</option>
        <option value="Office Supplies">Office Supplies</option>
        <option value="Utilities">Utilities</option>
        <option value="Others">Others</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="editExpenseDesc" class="form-label">Description</label>
      <input type="text" class="form-control" name="description" id="editExpenseDesc" required>
    </div>
    <div class="mb-3">
      <label for="editExpenseAmount" class="form-label">Amount (₹)</label>
      <input type="number" class="form-control" name="amount" id="editExpenseAmount" required>
    </div>
    <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-purple">Save Changes</button>
        </div>
  </div>
</form>
  </div>
</div>
<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="addExpenseForm" method="POST" action="add_expenses.php">
      <div class="modal-header">
        <h5 class="modal-title" id="addExpenseLabel"><i class="fas fa-plus-circle"></i> Add Expense</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="expenseDate" class="form-label">Date</label>
          <input type="date" class="form-control" name="date" id="expenseDate" required>
        </div>
        <div class="mb-3">
          <label for="expenseCategory" class="form-label">Category</label>
          <select class="form-select" name="category" id="expenseCategory" required>
            <option>Travel</option>
            <option>Food</option>
            <option>Office Supplies</option>
            <option>Utilities</option>
            <option>Others</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="expenseDesc" class="form-label">Description</label>
          <input type="text" class="form-control" name="description" id="expenseDesc" required>
        </div>
        <div class="mb-3">
          <label for="expenseAmount" class="form-label">Amount (₹)</label>
          <input type="number" class="form-control" name="amount" id="expenseAmount" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-purple"><i class="fas fa-save"></i> Add Expense</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/expenses.js"></script>
</body>
</html>