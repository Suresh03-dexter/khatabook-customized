<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Khatabook Style UI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .profile-bar {
      background-color: #003f8a;
      color: #fff;
    }
    .icon-card {
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .bottom-nav {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: #fff;
      border-top: 1px solid #ccc;
      display: flex;
      justify-content: space-around;
      padding: 10px 0;
    }
    .bottom-nav i {
      font-size: 20px;
    }
    .profile-strength-bar {
      height: 5px;
      background-color: #eee;
      position: relative;
    }
    .profile-strength-bar::after {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      height: 5px;
      width: 17%;
      background-color: red;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="profile-bar p-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <i class="bi bi-person-circle fs-3 me-2"></i>
      <span>Saleem Broilers And Mutton Stall</span>
    </div>
  </div>

  <!-- Profile Info -->
  <div class="p-3">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <div class="rounded-circle bg-primary text-white p-3 text-center me-3">MB</div>
        <div>
          <h6 class="mb-0">My Business</h6>
        </div>
      </div>
      <button class="btn btn-outline-primary btn-sm">Edit</button>
    </div>
  </div>

  <!-- Menu Cards -->
  <div class="container mb-5">
    <div class="row g-3">
      <div class="col-4">
        <div class="icon-card bg-light">
          <i class="bi bi-receipt fs-3 text-danger"></i>
          <p class="mt-2 mb-0">Bills</p>
        </div>
      </div>
      <div class="col-4">
        <div class="icon-card bg-light">
          <i class="bi bi-box-seam fs-3 text-purple"></i>
          <p class="mt-2 mb-0">Items</p>
        </div>
      </div>
      <div class="col-4">
        <div class="icon-card bg-light">
          <i class="bi bi-person-plus fs-3 text-warning"></i>
          <p class="mt-2 mb-0">Staff</p>
        </div>
      </div>
      <div class="col-4">
        <div class="icon-card bg-light">
          <i class="bi bi-calendar-event fs-3 text-success"></i>
          <p class="mt-2 mb-0">Collection</p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
