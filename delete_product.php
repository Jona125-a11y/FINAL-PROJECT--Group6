<?php
require "conn.php";
// Assuming you have already established a connection in $conn
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM tblproduct WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "<p>Product successfully deleted.</p>";
        header("location: product.php");
        exit;
    } else {
        echo "<p>Error deleting product: " . $conn->error . "</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid product ID.</p>";
}

$conn->close();
?>
