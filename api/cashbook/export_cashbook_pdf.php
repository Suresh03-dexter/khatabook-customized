<?php
require_once '../../config/db.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // For FPDF or TCPDF if using Composer
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}
$user_id = $_SESSION['user_id'];

// Get date filters (optional)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Base query
$sql = "SELECT entry_date, type, note, amount FROM cashbook WHERE user_id = ?";
$params = [$user_id];
$types  = "i";

// Apply date filter if given
if ($start_date && $end_date) {
    $sql .= " AND entry_date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types   .= "ss";
}

$sql .= " ORDER BY entry_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data
$rows = [];
$total_in = 0;
$total_out = 0;

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    if ($row['type'] === 'in') {
        $total_in += $row['amount'];
    } else {
        $total_out += $row['amount'];
    }
}

$balance = $total_in - $total_out;

// Create PDF
$mpdf = new \Mpdf\Mpdf();
$html = "
<h2 style='text-align:center;'>Cashbook Report</h2>
<table border='1' cellpadding='6' cellspacing='0' width='100%'>
<thead>
<tr style='background-color:#f2f2f2;'>
    <th>Date</th>
    <th>Type</th>
    <th>Note</th>
    <th>Amount</th>
</tr>
</thead>
<tbody>
";

foreach ($rows as $r) {
    $html .= "<tr>
        <td>{$r['entry_date']}</td>
        <td>" . ucfirst($r['type']) . "</td>
        <td>{$r['note']}</td>
        <td style='text-align:right;'>" . number_format($r['amount'], 2) . "</td>
    </tr>";
}

$html .= "
</tbody>
</table>
<br><br>
<table border='1' cellpadding='6' cellspacing='0' width='50%' style='margin:auto;'>
<tr><th>Total Cash In</th><td style='text-align:right;'>" . number_format($total_in, 2) . "</td></tr>
<tr><th>Total Cash Out</th><td style='text-align:right;'>" . number_format($total_out, 2) . "</td></tr>
<tr><th>Balance</th><td style='text-align:right;'>" . number_format($balance, 2) . "</td></tr>
</table>
";

$mpdf->WriteHTML($html);
$mpdf->Output("cashbook_report.pdf", "D"); // D = download, I = inline display