<?php
include 'conn.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_no = $_POST['order_no'];
    $payment_status = $_POST['payment_status'];

    $sql = "UPDATE tblorder SET payment = ? WHERE order_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $payment_status, $order_no);

    if ($stmt->execute()) {
        echo "Payment status updated to " . $payment_status;
    } else {
        echo "Error updating payment status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
