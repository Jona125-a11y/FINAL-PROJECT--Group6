<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data
    $status = $_POST['status'] ?? '';
    $order_date = $_POST['order_date'] ?? '';

    // Check if data is received
    if (empty($status) || empty($order_date)) {
        echo "Missing status or order_date.";
        exit;
    }

    // Example of updating the database (ensure $conn is properly initialized)
    $sql = "UPDATE tblorder SET status = ? WHERE dateandtime = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $status, $order_date);
        if ($stmt->execute()) {
            echo "Status updated successfully.";
        } else {
            echo "Error updating status: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>