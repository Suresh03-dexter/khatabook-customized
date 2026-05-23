<?php
session_start();

// First check: Is user logged in?
if (!isset($_SESSION['username'])) {
    header('Location: http://localhost/khatabook/google-login.php');
    exit();
}

// Second check: Is user an admin?
if ($_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/khatabook/pages/bussiness.php");
    exit();
}
?>

<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Record Modification Tracker - PKhatabook UI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .card-custom {
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      border-radius: 15px;
    }
    .table th {
      background-color: #e9ecef;
    }
  </style>
</head>
<body>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-primary"><i class="bi bi-clock-history"></i> Auto Modification Tracker</h4>

  </div>

  <div class="card card-custom">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Record Name</th>
              <th>Modified By</th>
              <th>Role</th>
              <th>Date & Time</th>
            </tr>
          </thead>
          <tbody id="historyTable">
              <!-- Dynamic content here -->
          </tbody>

          <script>
          document.addEventListener('DOMContentLoaded', loadTracker);

          function loadTracker() {
              fetch('../api/tracker/load_tracker.php') // adjust path as per your folder structure
                  .then(res => res.json())
                  .then(data => {
                      const tbody = document.getElementById('historyTable');
                      tbody.innerHTML = '';
                      data.forEach((row, index) => {
                          tbody.innerHTML += `
                              <tr>
                                  <td>${index + 1}</td>
                                  <td>${row.record_name}</td>
                                  <td>${row.modified_by}</td>
                                  <td>${row.role}</td>
                                  <td>${row.modified_at}</td>
                              </tr>
                          `;
                      });
                  })
                  .catch(err => console.error('Error loading tracker:', err));
          }
          </script>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
