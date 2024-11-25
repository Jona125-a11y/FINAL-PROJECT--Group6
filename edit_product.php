<?php
// Include your database connection file
include 'conn.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values from the form
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_path = '';

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Define upload directory
        $upload_dir = 'uploads/';

        // Sanitize and generate unique file name
        $image_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . uniqid() . '_' . $image_name;

        // Move uploaded file to the desired directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        } else {
            echo "Error uploading the image.";
            exit;
        }
    }

    // Build the SQL query to update the product
    if (!empty($image_path)) {
        // If a new image is uploaded, include it in the update
        $sql = "UPDATE tblproduct SET productname = ?, price = ?, category = ?, image = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssi", $product_name, $price, $category, $image_path, $product_id);
    } else {
        // If no new image is uploaded, update other fields only
        $sql = "UPDATE tblproduct SET productname = ?, price = ?, category = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsi", $product_name, $price, $category, $product_id);
    }

    // Execute the query
    if ($stmt->execute()) {
        echo "Product updated successfully.";
        // Redirect or display a success message as needed
        header("Location: product.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
}

$conn->close();
?>