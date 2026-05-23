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
  <title>Khatabook - Cashbook</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f2f2f2;
      margin: 0;
      padding: 20px;
    }

    .cashbook-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fff;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .cashbook-header h2 {
      margin: 0;
      font-size: 24px;
      color: #333;
    }

    .cashbook-actions button {
      margin-left: 10px;
      padding: 8px 15px;
      font-size: 14px;
      cursor: pointer;
      border: none;
      background: #1976d2;
      color: #fff;
      border-radius: 4px;
    }

    .cashbook-filter {
      background: #fff;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .cashbook-filter label {
      margin-right: 10px;
      font-weight: bold;
    }

    .cashbook-filter input[type="date"] {
      margin-right: 20px;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .cashbook-summary {
      background: #fff3cd;
      padding: 15px 20px;
      border-left: 5px solid #ffecb5;
      border-radius: 8px;
      margin-bottom: 20px;
      display: none;
    }

    .cashbook-summary div {
      margin-bottom: 8px;
      font-size: 16px;
    }

    .cashbook-table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .cashbook-table th,
    .cashbook-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .cashbook-table th {
      background-color: #f9fafb;
      font-weight: bold;
      color: #333;
    }

    .cashbook-table td button {
      margin-right: 5px;
      padding: 4px 8px;
      font-size: 14px;
      cursor: pointer;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: #fff;
      padding: 25px;
      border-radius: 8px;
      width: 400px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .modal-content h3 {
      margin-top: 0;
    }

    .modal-content label {
      display: block;
      margin-top: 10px;
    }

    .modal-content input,
    .modal-content select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .modal-content button {
      margin-top: 15px;
      padding: 8px 12px;
      background-color: #1976d2;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .modal-content button:last-child {
      background-color: #9e9e9e;
      margin-left: 10px;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <div class="cashbook-header">
    <h2>📘 Cashbook</h2>
    <div class="cashbook-actions">
      <button onclick="openAddEntryModal()">➕ Add Entry</button>
      <button onclick="toggleSummaryView()">📊 View Summary</button>
      <button onclick="exportCashbookPDF()">📤 Export PDF</button>
    </div>
  </div>

  <!-- Date Filter -->
 <div class="cashbook-filter">
    <label for="fromDate">From:</label>
    <input type="date" id="fromDate">
    <label for="toDate">To:</label>
    <input type="date" id="toDate">
</div>

  <!-- Summary Report -->
  <div class="cashbook-summary" id="cashbookSummary">
    <div>💰 Opening Balance: ₹<span id="openingBalance">0</span></div>
    <div>🟢 Cash In: ₹<span id="cashInTotal">0</span></div>
    <div>🔴 Cash Out: ₹<span id="cashOutTotal">0</span></div>
    <div>📘 Closing Balance: ₹<span id="closingBalance">0</span></div>
  </div>

  <!-- Cashbook Table -->
  <table class="cashbook-table" id="cashbookTable">
    <thead>
      <tr>
        <th>Date</th>
        <th>Time</th>
        <th>Type</th>
        <th>Amount</th>
        <th>Reason/Note</th>
        <th>Balance</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="cashbookEntries"></tbody>
      <!-- Entries will be dynamically loaded here -->
    </tbody>
  </table>
  
  <!-- Add Entry Modal -->
  <div id="addEntryModal" class="modal">
    <div class="modal-content">
      <h3>Add Cash Entry</h3>
      <label>Type:</label>
      <select id="type">
        <option value="in">🟢 Cash In</option>
        <option value="out">🔴 Cash Out</option>
      </select>

      <label>Amount:</label>
      <input type="number" id="amount" min="0" step="1">

      <label>Note:</label>
      <input type="text" id="note">

      <label>Date:</label>
      <input type="date" id="entry_date">

      <button onclick="saveCashEntry()">Save</button>
      <button onclick="closeAddEntryModal()">Cancel</button>
    </div>
  </div>

  <!-- JS Scripts -->
   <script>
  const USER_ROLE = "<?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role'], ENT_QUOTES) : ''; ?>";
</script>

  <script>
    function openAddEntryModal() {
      document.getElementById("addEntryModal").style.display = "flex";
    }

    function closeAddEntryModal() {
      document.getElementById("addEntryModal").style.display = "none";
    }

  // --- Helper: nicely format number to ₹ with 2 decimals ---
function formatCurrency(num) {
  if (num === null || num === undefined || isNaN(num)) return '₹0.00';
  return '₹' + Number(num).toFixed(2);
}

// --- Fetch summary from API for the selected date range ---
async function fetchSummary(from = '', to = '') {
  const api = `../api/cashbook/get_cashbook_summary.php?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`;

  // show temporary "loading" text
  document.getElementById('openingBalance').textContent = 'Loading...';
  document.getElementById('cashInTotal').textContent = 'Loading...';
  document.getElementById('cashOutTotal').textContent = 'Loading...';
  document.getElementById('closingBalance').textContent = 'Loading...';

  try {
    const res = await fetch(api, { cache: 'no-store' });
    if (!res.ok) {
      // server returned 4xx/5xx
      console.error('Summary API returned', res.status);
      throw new Error('Server error: ' + res.status);
    }

    const summary = await res.json();

    // defensive: ensure keys exist (fallback to 0)
    const opening = summary.opening_balance ?? summary.openingBalance ?? 0;
    const cashIn = summary.cash_in ?? summary.cashIn ?? 0;
    const cashOut = summary.cash_out ?? summary.cashOut ?? 0;
    const closing = summary.closing_balance ?? summary.closingBalance ?? (opening + cashIn - cashOut);

    // update UI
    document.getElementById('openingBalance').textContent = formatCurrency(opening);
    document.getElementById('cashInTotal').textContent = formatCurrency(cashIn);
    document.getElementById('cashOutTotal').textContent = formatCurrency(cashOut);
    document.getElementById('closingBalance').textContent = formatCurrency(closing);

    return { opening, cashIn, cashOut, closing }; // useful if needed
  } catch (err) {
    console.error('Failed to load summary:', err);
    document.getElementById('openingBalance').textContent = '—';
    document.getElementById('cashInTotal').textContent = '—';
    document.getElementById('cashOutTotal').textContent = '—';
    document.getElementById('closingBalance').textContent = '—';
    return null;
  }
}

// It toggles the panel; when opening, it fetches and updates summary.
async function toggleSummaryView() {
  const panel = document.getElementById('cashbookSummary');

  // If currently hidden -> open and fetch
  if (panel.style.display === 'none' || panel.style.display === '') {
    // get date filters (if any)
    const from = document.getElementById('fromDate').value || '';
    const to = document.getElementById('toDate').value || '';

    // fetch & populate
    await fetchSummary(from, to);

    // show the panel
    panel.style.display = 'block';
  } else {
    // hide the panel
    panel.style.display = 'none';
  }
}

function filterByDate() {
  loadSummary();
  loadCashbook();
}


function saveCashEntry() {
  const type = document.getElementById('type').value.trim();
  const amount = document.getElementById('amount').value.trim();
  const note = document.getElementById('note').value.trim();
  const date = document.getElementById('entry_date').value.trim();

  if (!type || !amount || !date || isNaN(amount)) {
    alert('Please enter valid type, amount, and date.');
    return;
  }

  fetch('../api/cashbook/add_cashbook_entry.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      type: type,
      amount: amount,
      note: note,
      entry_date: date
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Entry saved successfully.');

      // Reset fields
      document.getElementById('type').value = 'in';
      document.getElementById('amount').value = '';
      document.getElementById('note').value = '';
      document.getElementById('entry_date').value = '';

      // Close modal
      document.getElementById('addEntryModal').style.display = 'none';

      // Reload data
      loadCashbook();
      loadSummary();
    } else {
      alert('Failed to save entry: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Save entry error:', error);
    alert('Error occurred while saving entry.');
  });
}

function loadCashbook() {
  const from = document.getElementById("fromDate").value;
  const to = document.getElementById("toDate").value;

  fetch(`../api/cashbook/get_cashbook.php?from=${from}&to=${to}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById("cashbookEntries");
      tbody.innerHTML = ""; // Clear existing rows

      data.forEach(row => {
        const tr = document.createElement("tr");

        const cashIn = row.type === 'in' ? `₹${parseFloat(row.amount).toFixed(2)}` : '-';
        const cashOut = row.type === 'out' ? `₹${parseFloat(row.amount).toFixed(2)}` : '-';
        const typeDisplay = row.type === 'in' ? '🟢 In' : '🔴 Out';

        // show delete only for admin
        const deleteButton = (typeof USER_ROLE !== 'undefined' && USER_ROLE === 'admin')
          ? `<button onclick="deleteEntry(${row.id})">🗑️</button>`
          : '';

        tr.innerHTML = `
          <td>${row.entry_date}</td>
          <td>${row.entry_time}</td>
          <td>${typeDisplay}</td>
          <td>₹${parseFloat(row.amount).toFixed(2)}</td>
          <td>${row.note ?? ''}</td>
          <td>${row.balance ? `₹${parseFloat(row.balance).toFixed(2)}` : '-'}</td>
          <td>
            <button onclick="editEntry(${row.id})">✏️</button>
            ${deleteButton}
          </td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch(err => console.error("loadCashbook error:", err));
}

function loadSummary() {
  const from = document.getElementById("fromDate").value;
  const to = document.getElementById("toDate").value;
  fetchSummary(from, to); // update summary live
  fetch(`../api/cashbook/get_cashbook_summary.php?from=${from}&to=${to}`)
    .then(res => res.json())
    .then(summary => {
      document.getElementById("openingBalance").textContent = parseFloat(summary.opening_balance).toFixed(2);
      document.getElementById("cashInTotal").textContent = parseFloat(summary.cash_in).toFixed(2);
      document.getElementById("cashOutTotal").textContent = parseFloat(summary.cash_out).toFixed(2);
      document.getElementById("closingBalance").textContent = parseFloat(summary.closing_balance).toFixed(2);
    })
    .catch(err => console.error("loadSummary error:", err));
}


    function editEntry(id) {
      alert("Edit entry ID: " + id);
      // Load data into modal and open it
    }

  function deleteEntry(id) {
  if (!confirm("Delete this entry permanently?")) return;

  fetch('../api/cashbook/delete_cashbook_entry.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  })
  .then(res => {
    // handle non-JSON server errors gracefully
    return res.text().then(text => {
      try { return JSON.parse(text); } 
      catch(e) { throw new Error('Invalid server response: ' + text); }
    });
  })
  .then(json => {
    if (json.success) {
      // refresh table and summary
      loadCashbook();
      if (typeof loadSummary === 'function') loadSummary();
    } else {
      alert(json.message || 'Failed to delete entry.');
    }
  })
  .catch(err => {
    console.error('Delete Entry Error:', err);
    alert('Error deleting entry. Check console for details.');
  });
}

    document.getElementById('fromDate').addEventListener('change', filterByDate);
document.getElementById('toDate').addEventListener('change', filterByDate);

function filterByDate() {
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    if (!fromDate || !toDate) {
        return; // Don't fetch if both dates aren't selected
    }

    fetch(`../api/cashbook/filter_cashbook.php?from_date=${fromDate}&to_date=${toDate}`)
        .then(response => response.json())
        .then(data => {
            renderCashbookTable(data);
        })
        .catch(error => console.error('Error fetching data:', error));
}

function renderCashbookTable(data) {
    const tableContainer = document.getElementById('cashbookTable');
    let html = `<table border="1" cellpadding="5">
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Note</th>
            <th>Amount</th>
        </tr>`;

    data.forEach(row => {
        html += `
        <tr>
            <td>${row.entry_date}</td>
            <td>${row.type}</td>
            <td>${row.note || ''}</td>
            <td>${row.amount}</td>
        </tr>`;
    });

    html += `</table>`;
    tableContainer.innerHTML = html;
}

function exportCashbookPDF() {
    const fromDate = document.getElementById('fromDate')?.value || '';
    const toDate = document.getElementById('toDate')?.value || '';

    const url = `../api/cashbook/export_cashbook_pdf.php?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}`;
    window.open(url, '_blank'); // Direct download
}

    window.onload = () => {
  fetchSummary();
  loadCashbook();
};

  </script>

</body>
</html>
