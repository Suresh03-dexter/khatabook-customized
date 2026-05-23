// ---------- Utility Functions ----------
function timeAgo(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  const now = new Date();
  const seconds = Math.floor((now - date) / 1000);

  if (seconds < 60) return 'Just now';
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return minutes + ' min ago';
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return hours + ' hr ago';
  const days = Math.floor(hours / 24);
  if (days < 30) return days + ' days ago';
  return date.toLocaleDateString();
}

function showAlert(message) {
  const alertBox = document.getElementById('alertBox');
  const alertMessage = document.getElementById('alertMessage');
  if (alertBox && alertMessage) {
    alertMessage.textContent = message;
    alertBox.classList.remove('d-none');
    setTimeout(hideAlert, 3000);
  }
}

function hideAlert() {
  const alertBox = document.getElementById('alertBox');
  if (alertBox) alertBox.classList.add('d-none');
}

// ---------- Global Navigation Function ----------
function goToCustomerItems(customerId, customerName = '') {
  window.location.href = `customer_transactions.php?customer_id=${customerId}&name=${encodeURIComponent(customerName)}`;
}

// ---------- Load Customers ----------
function loadCustomers(searchQuery = '', filter = '') {
  fetch(`../api/fetch_customer.php?search=${encodeURIComponent(searchQuery)}&filter=${encodeURIComponent(filter)}`)
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('customerList');
      if (!container) return;

      container.innerHTML = '';

      if (!data || data.length === 0) {
        container.innerHTML = '<div class="text-center text-muted">No customers found.</div>';
        return;
      }

      const userRole = window.currentUserRole || document.body.dataset.role || 'subadmin';

      data.forEach(customer => {
        const card = document.createElement('div');
        card.className =
          'bg-white rounded shadow-sm p-3 mb-3 d-flex justify-content-between align-items-center customer-card';
        card.style.cursor = 'pointer';

        // ✅ Proper navigation
        card.addEventListener("click", function() {
          goToCustomerItems(customer.id, customer.name);
        });

        const deleteIcon =
          userRole === 'admin'
            ? `
            <a href="javascript:void(0)" class="ms-2 text-danger delete-customer" data-id="${customer.id}" title="Delete">
              <i class="fas fa-trash"></i>
            </a>`
            : '';

        card.innerHTML = `
          <div class="d-flex align-items-center">
            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
              ${customer.name.charAt(0).toUpperCase()}
            </div>
            <div>
              <div class="fw-bold">${customer.name}</div>
              <div class="small text-muted">${timeAgo(customer.created_at)}</div>
            </div>
          </div>
          <div class="text-end">
            <div class="fw-bold mb-1">₹ ${customer.balance}</div>
            <a href="tel:${customer.mobile}" class="me-2 text-success" title="Call">
              <i class="fas fa-phone"></i>
            </a>
            <a href="https://wa.me/91${customer.mobile}" target="_blank" class="me-2 text-success" title="WhatsApp">
              <i class="fab fa-whatsapp"></i>
            </a>
            <a href="#" class="text-primary edit-customer" data-id="${customer.id}" title="Edit">
              <i class="fas fa-edit"></i>
            </a>
            ${deleteIcon}
          </div>
        `;

        // ----- Edit customer -----
        card.querySelector('.edit-customer').addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();

          document.getElementById('editCustomerId').value = customer.id;
          document.getElementById('editCustomerName').value = customer.name;
          document.getElementById('editMobile').value = customer.mobile;
          document.getElementById('editInitialBalance').value = customer.balance;

          new bootstrap.Modal(document.getElementById('editCustomerModal')).show();
        });

        // ----- Delete customer (admin only) -----
        const delBtn = card.querySelector('.delete-customer');
        if (delBtn) {
          delBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (confirm(`Are you sure you want to delete "${customer.name}"?`)) {
              fetch(`../api/delete_customer.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: customer.id })
              })
                .then(res => res.json())
                .then(resp => {
                  if (resp.status === 'success') {
                    showAlert('Customer deleted successfully!');
                    loadCustomers();
                    loadSummary(); // ✅ Update totals immediately
                  } else {
                    alert(resp.message || 'Failed to delete customer');
                  }
                });
            }
          });
        }

        container.appendChild(card);
      });
    });
}

// ---------- Supplier Details (jQuery used intentionally) ----------
function loadSupplierDetails(supplierId) {
  $.ajax({
    url: '../api/get_supplier_details.php',
    method: 'POST',
    data: { supplier_id: supplierId },
    dataType: 'json',
    success: function (data) {
      if (data.success) {
        const s = data.details;

        const modalHtml = `
        <div class="modal" id="supplierModal" style="display:block;">
          <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">${s.name} Details</h5>
                <button type="button" class="close" onclick="closeSupplierModal()">&times;</button>
              </div>
              <div class="modal-body">
                <table class="table table-bordered">
                  <tr><th>Name</th><td>${s.name}</td></tr>
                  <tr><th>Mobile No</th><td>${s.mobile}</td></tr>
                  <tr><th>Address</th><td>${s.address}</td></tr>
                  <tr><th>You Will Give</th><td>₹${s.you_will_give}</td></tr>
                  <tr><th>You Will Get</th><td>₹${s.you_will_get}</td></tr>
                  <tr><th>Product Type</th><td>${s.product_type}</td></tr>
                  <tr><th>Status</th><td>${s.status}</td></tr>
                  <tr><th>Date & Time</th><td>${s.date_time}</td></tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        `;

        $('body').append(modalHtml);
      } else {
        alert('Supplier not found!');
      }
    },
    error: function () {
      alert('Error loading supplier details.');
    }
  });
}

function closeSupplierModal() {
  $('#supplierModal').remove();
}

// ---------- Debounce Helper ----------
function debounce(func, delay) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

// ---------- Load Summary ----------
function loadSummary() {
  fetch('../api/fetch_summary.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('youWillGive').textContent = `₹${data.you_will_give || 0}`;
      document.getElementById('youWillGet').textContent = `₹${data.you_will_get || 0}`;
    })
    .catch(err => console.error('Summary load failed', err));
}

// ---------- DOM READY ----------
let currentFilter = '';
document.addEventListener('DOMContentLoaded', function () {
  loadCustomers();
  loadSummary(); // ✅ Load summary initially

  // Search Customers
  const searchInput = document.getElementById('customerSearch');
  if (searchInput) {
    const debouncedSearch = debounce(() => loadCustomers(searchInput.value.trim(), currentFilter), 300);
    searchInput.addEventListener('input', debouncedSearch);
  }

  // ---------- Dynamic Filter ----------
  const filterButton = document.getElementById('filterButton');
  const filterMenu = document.getElementById('filterMenu');

  if (filterButton && filterMenu) {
    filterButton.addEventListener('click', () => {
      filterMenu.classList.toggle('d-none');
    });

    filterMenu.querySelectorAll('a[data-filter]').forEach(option => {
      option.addEventListener('click', (e) => {
        e.preventDefault();
        currentFilter = option.dataset.filter;
        loadCustomers(document.getElementById('customerSearch')?.value || '', currentFilter);
        loadSummary();
        filterMenu.classList.add('d-none');
      });
    });
  }

  // Add Customer
  document.getElementById('addCustomerForm')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const name = document.getElementById('customerName').value.trim();
    const mobile = document.getElementById('mobile').value.trim();
    // Safe read (input may not exist in your modal)
    const initialBalanceEl = document.getElementById('initialBalance');
    const balance = parseFloat(initialBalanceEl && initialBalanceEl.value ? initialBalanceEl.value : 0);

    if (!name) return alert("Customer name is required");

    fetch('../api/add_customer.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, mobile, balance })
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          showAlert('Customer added successfully!');
          loadCustomers();
          loadSummary(); // ✅ Update summary after add
          bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
          this.reset();
        } else {
          alert(data.message || 'Failed to add customer');
        }
      });
  });

  // Edit Customer
  document.getElementById('editCustomerForm')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const id = document.getElementById('editCustomerId').value;
    const name = document.getElementById('editCustomerName').value.trim();
    const mobile = document.getElementById('editMobile').value.trim();
    const balance = parseFloat(document.getElementById('editInitialBalance').value || 0);

    if (!name || !mobile) return alert("Name and mobile required");

    fetch('../api/update_customer.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, name, mobile, balance })
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          showAlert('Customer updated successfully!');
          loadCustomers();
          loadSummary(); // ✅ Update summary after edit
          bootstrap.Modal.getInstance(document.getElementById('editCustomerModal')).hide();
        } else {
          alert(data.message || 'Update failed');
        }
      });
  });

 // Reports Modal + PDF
const fromDate = document.getElementById('fromDate');
const toDate = document.getElementById('toDate');
const reportCustomerDropdown = document.getElementById('reportCustomerDropdown');
const showReportsBtn = document.getElementById('showReportsBtn');

function loadReports() {
  const customer_id = reportCustomerDropdown?.value || '';
  const from = fromDate?.value || '';
  const to = toDate?.value || '';

  fetch(`../api/fetch_report.php?customer_id=${customer_id}&from=${from}&to=${to}`)
    .then(res => res.json())
    .then(result => {
  const tbody = document.querySelector('#reportTable tbody');
  if (!tbody) return;
  tbody.innerHTML = '';

  const rows = result.data || [];   // <-- use result.data
  if (!Array.isArray(rows) || rows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No records found</td></tr>';
    return;
  }

  rows.forEach(row => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.name}</td>  
      <td>${row.item_name || '-'}</td>
      <td>${row.quantity || '-'}</td>
      <td>₹${row.amount || 0}</td>
      <td>${row.created_at ? new Date(row.created_at).toLocaleString() : '-'}</td>
    `;
    tbody.appendChild(tr);
  });
});

}

if (showReportsBtn) {
  showReportsBtn.addEventListener('click', () => {
    fetch('../api/fetch_customer.php')
      .then(res => res.json())
      .then(data => {
        reportCustomerDropdown.innerHTML = '<option value="">All Customers</option>';
        data.forEach(customer => {
          const option = document.createElement('option');
          option.value = customer.id;
          option.textContent = customer.name;
          reportCustomerDropdown.appendChild(option);
        });
      });

    new bootstrap.Modal(document.getElementById('reportModal')).show();
    setTimeout(loadReports, 300);
  });
}

[fromDate, toDate, reportCustomerDropdown].forEach(el => {
  if (el) el.addEventListener('change', loadReports);
});

// Download PDF
document.getElementById('downloadPDF')?.addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  html2canvas(document.getElementById('reportTable')).then(canvas => {
    const imgData = canvas.toDataURL('image/png');
    doc.addImage(imgData, 'PNG', 10, 10, 190, 0);
    doc.save('customer-report.pdf');
  });
});
});
