<?php
// Include database connection
include 'conn.php'; // Adjust this to your actual database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form input
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password for security
    $usertype = 'employee';

    // Prepare the SQL statement
    $sql = "INSERT INTO tbluser (user_id, username, email, contact, address, password, usertype) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("sssssss", $user_id, $username, $email, $contact, $address, $password, $usertype);

        // Execute the statement
        if ($stmt->execute()) {
            echo "User inserted successfully.";
            header('location: users.php');
        } else {
            echo "Error inserting user: " . $conn->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
