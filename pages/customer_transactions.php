<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: http://localhost/khatabook/google-login.php');
    exit();
}
require_once '../config/db.php';

$customerId = $_GET['customer_id'] ?? null;
$customerName = 'Unknown Customer';

if ($customerId) {
    $stmt = $conn->prepare("SELECT name FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $stmt->bind_result($customerName);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($customerName); ?> – Items</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- ✅ Top Navbar -->
<nav class="navbar navbar-dark bg-primary">
  <div class="container-fluid justify-content-center">
    <span class="navbar-brand mb-0 h5">
      <h2>Transactions for: <?php echo htmlspecialchars($customerName); ?></h2>
    </span>
  </div>
</nav>

<!-- ✅ Items List -->
<div class="container mt-3" style="padding-bottom:80px;">
  <ul class="list-group" id="itemList">
    <!-- Items will be loaded dynamically -->
  </ul>
  <div class="mt-3 text-end fw-bold">
  Total: ₹<span id="totalAmount">0</span>
</div>
</div>

<!-- ✅ Bottom Navigation -->
<nav class="navbar navbar-light bg-white fixed-bottom border-top">
  <div class="container-fluid d-flex justify-content-center">
    <button class="btn btn-danger rounded-pill px-4 py-2 shadow d-flex align-items-center justify-content-center" 
            data-bs-toggle="modal" data-bs-target="#addItemModal">
      <i class="fas fa-plus"></i>
    </button>
  </div>
</nav>

<!-- ✅ Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="addItemForm">
      <div class="modal-header">
        <h5 class="modal-title">Add Item for <?php echo htmlspecialchars($customerName); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="customerId" value="<?php echo $customerId; ?>">
        <div class="mb-3">
          <label for="itemName" class="form-label">Item Name</label>
          <input type="text" class="form-control" id="itemName" required>
        </div>
        <div class="mb-3">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="text" class="form-control" id="quantity" placeholder="e.g. 5 kg, 10 pcs, 2 ltr" required>
        </div>
        <div class="mb-3">
          <label for="amount" class="form-label">Amount</label>
          <input type="number" class="form-control" id="amount" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Item</button>
      </div>
    </form>
  </div>
</div>
<script>
  const customerId = "<?php echo $customerId; ?>";
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/customer_items.js"></script>
</body>
</html>
