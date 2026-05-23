<?php
require_once '../config/db.php';
require_once '../vendor/autoload.php'; // ✅ Make sure you installed dompdf

use Dompdf\Dompdf;

session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['supplier_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];
$supplier_id = intval($_GET['supplier_id']);
$name_filter = isset($_GET['name']) ? '%' . $conn->real_escape_string($_GET['name']) . '%' : '%';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// ✅ Fetch data
$sql = "SELECT supplier_name, product_type, mobile, address, opening_balance, current_balance, created_at 
        FROM suppliers 
        WHERE id = ? AND user_id = ? AND supplier_name LIKE ?";

if (!empty($date_filter)) {
    $sql .= " AND DATE(created_at) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $supplier_id, $user_id, $name_filter, $date_filter);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $supplier_id, $user_id, $name_filter);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No report data found";
    exit;
}

// ✅ Start building HTML
$html = '<h2>Supplier Report</h2><table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Product Type</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Amount Give (₹)</th>
            <th>Amount Get (₹)</th>
            <th>Date & Time</th>
        </tr>
    </thead>
    <tbody>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['supplier_name']) . '</td>
        <td>' . htmlspecialchars($row['product_type']) . '</td>
        <td>' . htmlspecialchars($row['mobile']) . '</td>
        <td>' . htmlspecialchars($row['address']) . '</td>
        <td>' . htmlspecialchars($row['opening_balance']) . '</td>
        <td>' . htmlspecialchars($row['current_balance']) . '</td>
        <td>' . date("d-m-Y h:i A", strtotime($row['created_at'])) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// ✅ Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream("supplier_report.pdf", ["Attachment" => true]);
exit;
