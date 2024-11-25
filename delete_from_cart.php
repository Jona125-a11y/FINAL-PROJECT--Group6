<?php
require 'conn.php';

if (isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);

    $query = "DELETE FROM tblcart WHERE cart_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cart_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Invalid request";
}
?>
