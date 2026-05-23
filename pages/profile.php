<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: http://localhost/khatabook/google-login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Profile - PkhataBook</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <!-- Header -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-arrow-left me-2"></i>Book Profile</a>
    </div>
  </nav>

  <div class="container my-4">
    <!-- Profile Photo -->
    <div class="text-center">
      <div class="position-relative d-inline-block">
        <img src="https://via.placeholder.com/100" class="rounded-circle border" alt="Profile" width="100" height="100">
        <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle">
          <i class="fas fa-camera"></i>
        </button>
      </div>
      <p class="mt-2 text-primary">Add photo</p>
    </div>

    <!-- Profile Strength -->
    <div class="my-3">
      <p class="mb-1">Profile strength: <span class="text-danger fw-bold">Weak</span></p>
      <div class="progress" style="height: 5px;">
        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%;"></div>
      </div>
    </div>

    <!-- Call to Action -->
    <div class="card mb-4 shadow-sm">
      <div class="card-body text-center">
        <p class="card-text">Fill missing details for a <strong>FREE Business Card</strong></p>
        <button class="btn btn-outline-primary">PROCEED</button>
      </div>
    </div>

    <!-- Personal Info -->
    <div class="mb-4">
      <h6 class="text-muted">Personal Info</h6>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-user me-2 text-secondary"></i> Name</div>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalName">Add Details</button>
        </li>
        <li class="list-group-item">
          <i class="fas fa-phone me-2 text-secondary"></i> Registered number: <strong class="ms-1">9042107909</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-store me-2 text-secondary"></i> Business name: <strong>My Business</strong></div>
          <a href="#" class="text-primary">Edit</a>
        </li>
      </ul>
    </div>

    <!-- Business Info -->
    <div class="mb-4">
      <h6 class="text-muted">Business Info</h6>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-map-marker-alt me-2 text-secondary"></i> Business Address</div>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddress">Add Details</button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-layer-group me-2 text-secondary"></i> Business Category</div>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCategory">Add Details</button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-briefcase me-2 text-secondary"></i> Business Type</div>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalType">Add Details</button>
        </li>
      </ul>
    </div>

    <!-- Financial Info -->
    <div class="mb-4">
      <h6 class="text-muted">Financial Info</h6>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-file-invoice me-2 text-secondary"></i> GSTIN</div>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalGST">Add Details</button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-university me-2 text-secondary"></i> Bank Account</div>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBank">Add Details</button>
        </li>
      </ul>
    </div>

    <!-- Staff Info -->
    <div class="mb-4">
      <h6 class="text-muted">Staff Info</h6>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><i class="fas fa-users me-2 text-secondary"></i> Staff Details</div>
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalStaff">Add Details</button>
        </li>
      </ul>
    </div>
  </div>

  <!-- Modals -->
  <div class="modal fade" id="modalName" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title">Add Name</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="text" class="form-control" placeholder="Enter Name">
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Copy this modal and change IDs for each field -->
  <div class="modal fade" id="modalAddress" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title">Business Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="text" class="form-control" placeholder="Enter address">
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Other Modals (Category, Type, GSTIN, Bank, Staff) - replicate above structure -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
