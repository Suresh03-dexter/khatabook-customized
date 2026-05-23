let currentPage = 1;
let isLoading = false;
let hasMoreSuppliers = true;
let currentSearch = "";
let currentFilter = "";

document.addEventListener("DOMContentLoaded", function () {
    loadSuppliers();
    loadSupplierSummary();

        // ✅ Add Supplier Form (JSON-based to match PHP)
    const addSupplierForm = document.getElementById("addSupplierForm");
    if (addSupplierForm) {
        addSupplierForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const supplierData = {
                supplier_name: document.getElementById("supplierName").value.trim(),
                mobile: document.getElementById("supplierMobile").value.trim(),
                product_type: document.getElementById("productType").value,
                opening_balance: parseFloat(document.getElementById("youWillGive").value) || 0,  // ✅ Fixed ID
                current_balance: parseFloat(document.getElementById("youWillGet").value) || 0,  // ✅ Fixed ID
                address: document.getElementById("supplierAddress").value.trim()
            };

            fetch("../api/add_supplier.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(supplierData)
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    const modal = bootstrap.Modal.getInstance(document.getElementById("addSupplierModal"));
                    modal.hide();
                    addSupplierForm.reset();
                    resetSuppliers();
                    loadSuppliers(currentSearch, currentFilter);
                    loadSupplierSummary();
                }
            });
        });
    }
    // ✅ Search
    const searchInput = document.getElementById("supplierSearch");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            currentSearch = this.value;
            resetSuppliers();
            loadSuppliers(currentSearch, currentFilter);
        });
    }

    // ✅ Filter toggle
    const filterButton = document.getElementById("supplierFilterButton");
    const filterMenu = document.getElementById("filterMenu");

    if (filterButton && filterMenu) {
        filterButton.addEventListener("click", () => filterMenu.classList.toggle("d-none"));
        filterMenu.querySelectorAll("a").forEach(item => {
            item.addEventListener("click", function (e) {
                e.preventDefault();
                currentFilter = this.dataset.filter;
                resetSuppliers();
                loadSuppliers(currentSearch, currentFilter);
                filterMenu.classList.add("d-none");
            });
        });
    }

    // ✅ Report filter
    document.getElementById("reportSearchBtn")?.addEventListener("click", () => {
        const name = document.getElementById("reportSupplierNameFilter").value;
        const date = document.getElementById("reportDateFilter").value;
        loadSupplierReport(name, date);
    });

    // ✅ Report download
    document.getElementById("downloadReportPDF")?.addEventListener("click", () => {
        const name = document.getElementById("reportSupplierNameFilter").value;
        const date = document.getElementById("reportDateFilter").value;
        window.open(`../api/download_supplier_report.php?name=${encodeURIComponent(name)}&date=${encodeURIComponent(date)}`, "_blank");
    });

    // ✅ Infinite scroll
    window.addEventListener("scroll", function () {
        if (!isLoading && hasMoreSuppliers && window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
            loadSuppliers(currentSearch, currentFilter);
        }
    });
});

// ✅ Reset supplier list
function resetSuppliers() {
    currentPage = 1;
    hasMoreSuppliers = true;
    document.getElementById("supplierList").innerHTML = "";
}

// ✅ Load suppliers
function loadSuppliers(search = "", filter = "") {
    if (!hasMoreSuppliers) return;
    isLoading = true;

    fetch(`../api/get_suppliers.php?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}&page=${currentPage}&limit=10`)
        .then(res => res.json())
        .then(response => {
            const supplierList = document.getElementById("supplierList");
            const data = response.suppliers || [];
            hasMoreSuppliers = response.hasMore;

            if (currentPage === 1 && !data.length) {
                supplierList.innerHTML = `
                    <div class="text-center mt-5 pt-5">
                        <p class="text-muted mt-3">No suppliers found</p>
                    </div>
                `;
                isLoading = false;
                return;
            }

            const userRole = document.body.dataset.role || "subadmin";

            data.forEach(supplier => {
                const phone = supplier.mobile || "";
                const card = document.createElement("div");
                card.className = "card mb-2 shadow-sm supplier-card";
                card.dataset.supplierId = supplier.id;
                card.innerHTML = `
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <a href="javascript:void(0);" class="supplier-name" data-id="${supplier.id}" style="cursor: pointer; text-decoration: none;">
                                    ${supplier.supplier_name}
                                </a>
                                (${supplier.product_type || "N/A"})
                            </h6>
                            <small class="text-muted">
                                Mobile: ${phone || "-"} |
                                Give: ₹${supplier.opening_balance} |
                                Get: ₹${supplier.current_balance}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            ${phone ? `
                                <a href="tel:${phone}" class="btn btn-sm btn-outline-success" title="Call Supplier">
                                    <i class="fas fa-phone-alt"></i>
                                </a>
                                <a href="https://wa.me/${phone}" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>` : ""
                            }
                            <button class="btn btn-sm btn-primary" onclick="editSupplier(${supplier.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${userRole === "admin" ? `
                                <button class="btn btn-sm btn-danger" onclick="deleteSupplier(${supplier.id})">
                                    <i class="fas fa-trash"></i>
                                </button>` : ""
                            }
                        </div>
                    </div>
                `;
                supplierList.appendChild(card);

                // Attach view details click
                card.querySelector(".supplier-name").addEventListener("click", function () {
                    viewSupplierDetails(this.dataset.id);
                });
            });

            if (hasMoreSuppliers) currentPage++;
            isLoading = false;
        });
}

// ✅ Load supplier summary
function loadSupplierSummary() {
    fetch("../api/get_supplier_summary.php")
        .then(res => res.json())
        .then(data => {
            document.getElementById("youWillGive").textContent = `₹${parseFloat(data.you_will_give).toFixed(2)}`;
            document.getElementById("youWillGet").textContent = `₹${parseFloat(data.you_will_get).toFixed(2)}`;
        });
}

// ✅ Load report for all suppliers
function loadSupplierReport(search = "", date = "") {
    const tbody = document.getElementById("supplierReportBody");
    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>`;

    fetch(`../api/get_all_suppliers_report.php?search=${encodeURIComponent(search)}&date=${encodeURIComponent(date)}`)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = "";

            if (!data.suppliers || data.suppliers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No data found</td></tr>`;
                return;
            }

            data.suppliers.forEach((supplier, index) => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${supplier.supplier_name}</td>
                    <td>${supplier.product_type || 'N/A'}</td>
                    <td>${supplier.mobile || '-'}</td>
                    <td>${supplier.address || '-'}</td>
                    <td>₹${supplier.opening_balance}</td>
                    <td>₹${supplier.current_balance}</td>
                    <td>${supplier.created_at}</td>
                `;
                tbody.appendChild(tr);
            });
        });
}
function filterSupplierReport() {
  const search = document.getElementById("supplierSearchInput").value;
  const from = document.getElementById("supplierFromDate").value;
  const to = document.getElementById("supplierToDate").value;

  const url = `../api/get_all_suppliers_report.php?search=${encodeURIComponent(search)}&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      const tbody = document.getElementById("supplierReportBody");
      tbody.innerHTML = "";

      if (data.suppliers && data.suppliers.length > 0) {
        data.suppliers.forEach((supplier, index) => {
          const row = `<tr>
            <td>${index + 1}</td>
            <td>${supplier.supplier_name}</td>
            <td>${supplier.product_type}</td>
            <td>${supplier.mobile}</td>
            <td>${supplier.address}</td>
            <td>${supplier.opening_balance}</td>
            <td>${supplier.current_balance}</td>
            <td>${supplier.created_at}</td>
          </tr>`;
          tbody.insertAdjacentHTML("beforeend", row);
        });
      } else {
        const row = `<tr><td colspan="8" class="text-center text-muted">No records found</td></tr>`;
        tbody.innerHTML = row;
      }
    })
    .catch(error => {
      console.error("Filter Error:", error);
    });
}

async function downloadSupplierReportPDF() {
  if (!window.jspdf) {
    console.error("jsPDF library not loaded");
    return;
  }

  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const table = document.getElementById("supplierReportTable");
  if (!table) {
    alert("No report data found");
    return;
  }

  doc.text("Supplier Report", 14, 15);
  doc.autoTable({
    html: "#supplierReportTable",
    startY: 20,
    theme: "grid",
    headStyles: { fillColor: [0, 123, 255] },
    styles: { fontSize: 8 },
  });

  doc.save("Supplier_Report.pdf");
}


// ✅ Show all supplier reports modal
function openSupplierReportModal() {
    loadSupplierReport();
    const modal = new bootstrap.Modal(document.getElementById("supplierReportModal"));
    modal.show();
}

// ✅ View supplier details
function viewSupplierDetails(id) {
    fetch(`../api/get_supplier_details.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            const html = `
                <p><strong>Name:</strong> ${data.supplier_name}</p>
                <p><strong>Mobile:</strong> ${data.mobile || 'N/A'}</p>
                <p><strong>Address:</strong> ${data.address || 'N/A'}</p>
                <p><strong>Product Type:</strong> ${data.product_type || 'N/A'}</p>
                <p><strong>Status:</strong> ${data.status}</p>
                <p><strong>You Will Give:</strong> ₹${parseFloat(data.opening_balance).toFixed(2)}</p>
                <p><strong>You Will Get:</strong> ₹${parseFloat(data.current_balance).toFixed(2)}</p>
                <p><strong>Created On:</strong> ${data.created_at}</p>
            `;
            document.getElementById("supplierDetailsBody").innerHTML = html;
            new bootstrap.Modal(document.getElementById("supplierDetailsModal")).show();
        });
}

// ✅ Delete supplier
function deleteSupplier(id) {
    if (!confirm("Are you sure you want to delete this supplier?")) return;

    fetch(`../api/delete_supplier.php?id=${id}`, { method: "GET" })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            resetSuppliers();
            loadSuppliers(currentSearch, currentFilter);
            loadSupplierSummary();
        });
}

// ✅ Edit supplier (fill modal)
function editSupplier(id) {
    fetch(`../api/get_single_supplier.php?id=${id}`)
        .then(res => res.json())
        .then(supplier => {
            if (!supplier || !supplier.id) {
                alert("Supplier not found!");
                return;
            }

            document.getElementById("editSupplierId").value = supplier.id;
            document.getElementById("editSupplierName").value = supplier.supplier_name;
            document.getElementById("editSupplierMobile").value = supplier.mobile || "";
            document.getElementById("editProductType").value = supplier.product_type || "Chicken";
            document.getElementById("editYouWillGive").value = supplier.opening_balance || 0;
            document.getElementById("editYouWillGet").value = supplier.current_balance || 0;
            document.getElementById("editSupplierAddress").value = supplier.address || "";

            new bootstrap.Modal(document.getElementById("editSupplierModal")).show();
        });
}

// ✅ Submit Edit form
document.getElementById("editSupplierForm")?.addEventListener("submit", function (e) {
    e.preventDefault();

    const payload = {
        id: document.getElementById("editSupplierId").value,
        supplier_name: document.getElementById("editSupplierName").value,
        mobile: document.getElementById("editSupplierMobile").value,
        product_type: document.getElementById("editProductType").value,
        opening_balance: document.getElementById("editYouWillGive").value || 0,
        current_balance: document.getElementById("editYouWillGet").value || 0,
        address: document.getElementById("editSupplierAddress").value
    };

    fetch("../api/update_supplier.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                bootstrap.Modal.getInstance(document.getElementById("editSupplierModal")).hide();
                resetSuppliers();
                loadSuppliers(currentSearch, currentFilter);
                loadSupplierSummary();
            }
        });
});
