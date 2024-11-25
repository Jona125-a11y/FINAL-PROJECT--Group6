<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['status']) && isset($_POST['order_date'])) {
        $status = trim($_POST['status']);
        $order_date = trim($_POST['order_date']);

        // Validate and sanitize input
        $allowed_statuses = ['Ongoing', 'Delivered', 'Complete'];
        if (in_array($status, $allowed_statuses)) {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("UPDATE tblorder SET status = ? WHERE dateandtime = ?");
            $stmt->bind_param("ss", $status, $order_date);
            if ($stmt->execute()) {
                echo "Order status updated successfully.";
            } else {
                echo "Error updating order status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Invalid status.";
        }
    } else {
        echo "Missing required data.";
    }
}
?>
