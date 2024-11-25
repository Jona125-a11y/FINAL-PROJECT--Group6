<?php
if (isset($_POST['submit'])) {
    // Database connection
    require "conn.php";
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $cate = $_POST['category'];

    // Handle image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Basic validation for image upload
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image = $target_file;

        // Insert data into tblproduct
        $sql = "INSERT INTO tblproduct (product_id, productname, price, category, image) 
                VALUES ('$product_id', '$product_name', '$price', '$cate', '$image')";

        if ($conn->query($sql) === TRUE) {
            echo "New product inserted successfully!";
            header("location: product.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

    // Close the connection
    $conn->close();
}
?>
