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
  <title>PKhatabook Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card i {
      font-size: 1.5rem;
    }
    .chart-container {
      height: 300px;
    }
  </style>
</head>
<body>
  <div style="margin-top: 60px;"></div>
  <div class="container-fluid p-4">
    <h2 class="mb-4 text-center">📊 PKhatabook Dashboard</h2>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card p-3 text-white bg-success">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5>Weekly Profit</h5>
              <p class="mb-0" id="weeklyProfit">Loading...</p>
            </div>
            <i class="fas fa-chart-line"></i>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3 text-white bg-primary">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5>Monthly Profit</h5>
              <p class="mb-0" id="monthlyProfit">Loading...</p>
            </div>
            <i class="fas fa-sack-dollar"></i>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3 text-white bg-warning">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5>Weekly Expenses</h5>
              <p class="mb-0" id="weeklyExpenses">Loading...</p>
            </div>
            <i class="fas fa-wallet"></i>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3 text-white bg-danger">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5>Monthly Expenses</h5>
              <p class="mb-0" id="monthlyExpenses">Loading...</p>
            </div>
            <i class="fas fa-money-bill-wave"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Customers -->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="card p-3">
          <h5 class="mb-2">People Customers</h5>
          <div class="d-flex align-items-center">
            <i class="fas fa-user-friends text-info me-2"></i>
            <span id="peopleCount">Loading...</span>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card p-3">
          <h5 class="mb-2">Hotel Customers</h5>
          <div class="d-flex align-items-center">
            <i class="fas fa-hotel text-secondary me-2"></i>
            <span id="hotelCount">Loading...</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Section (Unchanged) -->
    <div class="row">
      <div class="col-md-12">
        <div class="card p-3">
          <h5>Sales vs Expenses</h5>
          <div class="chart-container">
            <canvas id="salesChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Chart remains static
  const ctx = document.getElementById('salesChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
      datasets: [
        {
          label: 'Sales',
          data: [12000, 15000, 17000, 20000],
          borderColor: '#28a745',
          tension: 0.4,
          fill: false
        },
        {
          label: 'Expenses',
          data: [6000, 8000, 9000, 9500],
          borderColor: '#dc3545',
          tension: 0.4,
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'top' } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // Function to fetch live dashboard data
  function loadDashboardData() {
    fetch('../api/get_dashboard_data.php')
      .then(res => res.json())
      .then(data => {
        document.getElementById('weeklyProfit').innerText = '₹' + data.weeklyProfit.toLocaleString();
        document.getElementById('monthlyProfit').innerText = '₹' + data.monthlyProfit.toLocaleString();
        document.getElementById('weeklyExpenses').innerText = '₹' + data.weeklyExpenses.toLocaleString();
        document.getElementById('monthlyExpenses').innerText = '₹' + data.monthlyExpenses.toLocaleString();
        document.getElementById('peopleCount').innerText = 'Total: ' + data.peopleCount;
        document.getElementById('hotelCount').innerText = 'Total: ' + data.hotelCount;
      })
      .catch(err => console.error('Dashboard load error:', err));
  }

  // Load initially and every 10 seconds
  loadDashboardData();
  setInterval(loadDashboardData, 10000);

  // Listen for live update events from other pages
  document.addEventListener('dashboardUpdate', () => {
    loadDashboardData();
  });
</script>
</body>
</html>
