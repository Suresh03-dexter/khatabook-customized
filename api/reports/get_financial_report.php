<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

try {
    // Purchases
    $purchases = [
        'total_purchases' => 0,
        'total_pending' => 0,
        'list' => []
    ];
    $sql = "SELECT * FROM purchases ORDER BY created_at DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $purchases['list'][] = $row;
        $purchases['total_purchases'] += $row['amount'];
        if ($row['status'] === 'pending') {
            $purchases['total_pending'] += $row['pending_amount'];
        }
    }

    // Expenses
    $expenses = [
        'total_expenses' => 0,
        'list' => []
    ];
    $sql = "SELECT * FROM expenses ORDER BY date DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $expenses['list'][] = $row;
        $expenses['total_expenses'] += $row['amount'];
    }

    // Cashbook
    $cashbook = [
        'total_cash_in' => 0,
        'total_cash_out' => 0,
        'list' => []
    ];
    $sql = "SELECT * FROM cashbook ORDER BY entry_date DESC, entry_time DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $cashbook['list'][] = $row;
        if ($row['type'] === 'in') {
            $cashbook['total_cash_in'] += $row['amount'];
        } else {
            $cashbook['total_cash_out'] += $row['amount'];
        }
    }

    // Customers
    $customers = [
        'total_customers' => 0,
        'total_customer_balance' => 0,
        'list' => []
    ];
    $sql = "SELECT * FROM customers ORDER BY created_at DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $customers['list'][] = $row;
        $customers['total_customers']++;
        $customers['total_customer_balance'] += $row['balance'];
    }

    // Suppliers
    $suppliers = [
        'total_suppliers' => 0,
        'total_supplier_balance' => 0,
        'list' => []
    ];
    $sql = "SELECT * FROM suppliers ORDER BY created_at DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $suppliers['list'][] = $row;
        $suppliers['total_suppliers']++;
        $suppliers['total_supplier_balance'] += $row['current_balance'];
    }

    echo json_encode([
        'success' => true,
        'data' => compact('purchases', 'expenses', 'cashbook', 'customers', 'suppliers')
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
