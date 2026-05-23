<?php
function logModification($conn, $recordName) {
    if (!isset($_SESSION['username'], $_SESSION['role'])) {
        return;
    }

    $modifiedBy = $_SESSION['username'];
    $role = $_SESSION['role'];

    $sql = "INSERT INTO modification_tracker (record_name, modified_by, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $recordName, $modifiedBy, $role);
    $stmt->execute();
}
