<?php
require 'conn.php';

if (isset($_POST['cart_id']) && isset($_POST['action'])) {
    $cart_id = intval($_POST['cart_id']);
    $action = $_POST['action'];
    $quantity_change = ($action === 'add') ? 1 : -1;

    $query = "UPDATE tblcart SET qt = GREATEST(qt + ?, 1) WHERE cart_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $quantity_change, $cart_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Invalid request";
}
?>
