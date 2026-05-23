document.addEventListener("DOMContentLoaded", () => {
  fetchUsers();

  // Handle Add User form submission
  document.getElementById("addUserForm").addEventListener("submit", function (e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch("../api/add_user.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.text())
      .then(response => {
        alert(response.trim());
        fetchUsers();
        // Close modal after saving
        const modal = bootstrap.Modal.getInstance(document.getElementById("addUserModal"));
        modal.hide();
        this.reset();
      })
      .catch(err => console.error("Add user error:", err));
  });
});

// Fetch and render all users
function fetchUsers() {
  fetch("../api/get_users.php")
    .then(res => res.json())
    .then(data => {
      const userList = document.getElementById("userList");
      userList.innerHTML = "";

      data.users.forEach(user => {
        const row = document.createElement("tr");

        row.innerHTML = `
          <td>${user.name}</td>
          <td>${capitalize(user.role)}</td>
          <td>
            <button class="btn btn-warning btn-sm edit-btn" data-id="${user.id}">
              <i class="bi bi-pencil-square"></i> Edit
            </button>
            <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}">
              <i class="bi bi-trash-fill"></i> Delete
            </button>
          </td>`;
        userList.appendChild(row);
      });

      // Attach click handlers
      document.querySelectorAll(".edit-btn").forEach(button =>
        button.addEventListener("click", handleEdit)
      );
      document.querySelectorAll(".delete-btn").forEach(button =>
        button.addEventListener("click", handleDelete)
      );
    })
    .catch(err => console.error("Fetch users error:", err));
}

// Edit button handler
function handleEdit() {
  const id = this.dataset.id;
  const currentUsername = this.dataset.username;
  const currentEmail = this.dataset.email;
  const currentRole = this.dataset.role;

  const newUsername = prompt("Enter new username:", currentUsername);
  if (!newUsername) return;

  const newEmail = prompt("Enter new email:", currentEmail);
  if (!newEmail) return;

  const newRole = prompt("Enter new role (admin/subadmin):", currentRole);
  if (!newRole || !['admin', 'subadmin'].includes(newRole.toLowerCase())) {
    alert("Role must be 'admin' or 'subadmin'");
    return;
  }

  const formData = new FormData();
  formData.append("csrf_token", document.querySelector('meta[name="csrf-token"]').content);
  formData.append("id", id);
  formData.append("username", newUsername);
  formData.append("email", newEmail);
  formData.append("role", newRole.toLowerCase());

  fetch("../api/edit_user.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(response => {
      alert(response.trim());
      fetchUsers();
    })
    .catch(err => console.error("Edit user error:", err));
}

// Delete button handler
function handleDelete() {
  const id = this.dataset.id;
  if (!confirm("Are you sure you want to delete this user?")) return;

  const formData = new FormData();
  formData.append("id", id);
  formData.append("csrf_token", document.querySelector('meta[name="csrf-token"]').content);
  fetch("../api/delete_user.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(response => {
      alert(response.trim());
      fetchUsers();
    })
    .catch(err => console.error("Delete user error:", err));
}


// Helper to capitalize roles
function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
async function downloadFinancialReport() {
  document.getElementById('financialReportForm').submit();
    try {
        const res = await fetch('../api/reports/get_financial_report.php');
        const result = await res.json();

        if (!result.success) {
            alert(result.message || 'Failed to load report');
            return;
        }

        const { purchases, expenses, cashbook, customers, suppliers } = result.data;

        // Generate PDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.setFontSize(16);
        doc.text("Financial Report", 10, 10);

        doc.setFontSize(12);
        doc.text(`Total Purchases: Rs.${purchases.total_purchases || 0}`, 10, 20);
        doc.text(`Pending Purchases: Rs.${purchases.total_pending || 0}`, 10, 30);
        doc.text(`Total Expenses: Rs.${expenses.total_expenses || 0}`, 10, 40);
        doc.text(`Cash In: Rs.${cashbook.total_cash_in || 0}`, 10, 50);
        doc.text(`Cash Out: Rs.${cashbook.total_cash_out || 0}`, 10, 60);
        doc.text(`Customers: ${customers.total_customers || 0}`, 10, 70);
        doc.text(`Customer Balance: Rs.${customers.total_customer_balance || 0}`, 10, 80);
        doc.text(`Suppliers: ${suppliers.total_suppliers || 0}`, 10, 90);
        doc.text(`Supplier Balance: Rs.${suppliers.total_supplier_balance || 0}`, 10, 100);

        doc.save('financial_report.pdf');
    } catch (err) {
        console.error(err);
        alert("Error generating report");
    }
}


