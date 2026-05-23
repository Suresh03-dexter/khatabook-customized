document.addEventListener('DOMContentLoaded', function () {
  const expenseForm = document.querySelector('#addExpenseModal form');
  const editForm = document.getElementById('editExpenseForm');

  // ✅ Submit Add Expense

if (expenseForm) {
  expenseForm.addEventListener('submit', function (e) {
    e.preventDefault();
    
    fetch('../api/add_expenses.php', {
      method: 'POST',
      body: new FormData(expenseForm)
    })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (data.status === 'success') {
        // Close modal and refresh
        bootstrap.Modal.getInstance(document.getElementById('addExpenseModal')).hide();
        expenseForm.reset();
          // ✅ Show success message
        alert('Expense added successfully!');
        document.dispatchEvent(new Event('dashboardUpdate'));
        location.reload(); // Reload the page to show the new expense
      } else {
        throw new Error(data.message || 'Unknown error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to save expense: ' + error.message);
    });
  });
}

  // Edit button click handler
document.querySelectorAll(".edit-expense-btn").forEach(btn => {
  btn.addEventListener("click", function() {
    // Populate the form fields
    document.getElementById("editExpenseId").value = this.dataset.id;
    document.getElementById("editExpenseDate").value = this.dataset.date;
    document.getElementById("editExpenseCategory").value = this.dataset.category;
    document.getElementById("editExpenseDesc").value = this.dataset.description;
    document.getElementById("editExpenseAmount").value = this.dataset.amount;
    
    // Show the modal
    const editModal = new bootstrap.Modal(document.getElementById("editExpenseModal"));
    editModal.show();
  });
});

// Edit form submission
document.getElementById("editExpenseForm")?.addEventListener("submit", function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  fetch("../api/edit_expense.php", {
    method: "POST",
    body: formData
  })
  .then(response => {
    if (!response.ok) throw new Error("Network response was not ok");
    return response.json();
  })
  .then(data => {
    if (data.status === "success") {
      alert("Expense updated successfully!");
      document.dispatchEvent(new Event('dashboardUpdate'));
      location.reload();
    } else {
      throw new Error(data.message || "Update failed");
    }
  })
  .catch(error => {
    console.error("Error:", error);
    alert("Error: " + error.message);
  });
});

// Delete button click handler
document.querySelectorAll(".delete-expense-btn").forEach(btn => {
  btn.addEventListener("click", function() {
    const expenseId = this.dataset.id;
    const expenseRow = this.closest("tr");
    
    if (!confirm("Are you sure you want to delete this expense?")) return;
    
    const formData = new FormData();
    formData.append("id", expenseId);
    
    fetch("../api/delete_expense.php", {
      method: "POST",
      body: formData
    })
    .then(response => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then(data => {
      if (data.status === "deleted") {
        // Remove the row from the table without reloading
        expenseRow.remove();
        alert("Expense deleted successfully!");
        document.dispatchEvent(new Event('dashboardUpdate'));
      } else {
        throw new Error(data.message || "Delete failed");
      }
    })
    .catch(error => {
      console.error("Error:", error);
      alert("Error: " + error.message);
    });
  });
});
})