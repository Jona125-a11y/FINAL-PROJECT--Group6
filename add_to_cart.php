<?php
require "conn.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the POST data
    $productId = $_POST['product_id'];
    $userId = $_POST['user_id'];
    $qt = 1;
    // Insert data into tblcart
    $sql = "INSERT INTO tblcart (product_id, user_id, qt) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iii", $productId, $userId, $qt); // 'ii' means two integers
        if ($stmt->execute()) {
            echo "Product added to cart successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
