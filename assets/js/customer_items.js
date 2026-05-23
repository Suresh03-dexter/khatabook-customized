document.addEventListener("DOMContentLoaded", () => {
  const customerId = document.getElementById("customerId").value;
  const itemList = document.getElementById("itemList");

  // ✅ Load items
async function loadItems() {
  try {
    const res = await fetch(`../api/customer_transaction/get_customer_items.php?id=${customerId}`);
    const result = await res.json();

    itemList.innerHTML = "";

    // make sure we have the total element
    const totalEl = document.getElementById("totalAmount");

    if (!result.success) {
      itemList.innerHTML = `<li class="list-group-item text-center text-danger">${result.message || "Failed to load items."}</li>`;
      if (totalEl) totalEl.innerText = "0";
      return;
    }

    if (!result.data || result.data.length === 0) {
      itemList.innerHTML = `<li class="list-group-item text-center text-muted">No items added yet.</li>`;
      if (totalEl) totalEl.innerText = "0";
    } else {
      result.data.forEach(item => {
        itemList.innerHTML += `
          <li class="list-group-item d-flex justify-content-between">
            <div>
              <strong>${item.item_name}</strong><br>
              <small>Qty: ${item.quantity}</small><br>
              <small class="text-muted">${item.created_at || ""}</small>
            </div>
            <div class="fw-bold">₹${item.amount}</div>
          </li>
        `;
      });

      // ✅ Update total from API
     const totalEl = document.getElementById('totalAmount');
if (totalEl) {
    totalEl.innerText = result.total_amount;  
}
    }
  } catch (err) {
    console.error("Error loading items:", err);
    itemList.innerHTML = `<li class="list-group-item text-center text-danger">Error loading items</li>`;
    const totalEl = document.getElementById("totalAmount");
    if (totalEl) totalEl.innerText = "0";
  }
}
  loadItems();

  // ✅ Add item via AJAX
  document.getElementById("addItemForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = {
      customer_id: customerId,
      item_name: document.getElementById("itemName").value,
      quantity: document.getElementById("quantity").value,
      amount: document.getElementById("amount").value
    };

    try {
      const res = await fetch("../api/customer_transaction/add_customer_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const result = await res.json();

      if (result.success) {
        document.getElementById("addItemForm").reset();
        bootstrap.Modal.getInstance(document.getElementById("addItemModal")).hide();
        loadItems();
      } else {
        alert(result.message || "Failed to add item");
      }
    } catch (err) {
      console.error("Error adding item:", err);
      alert("Something went wrong. Please try again.");
    }
  });
});
