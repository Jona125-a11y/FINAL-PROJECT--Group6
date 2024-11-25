<?php
// Include database connection
include 'conn.php'; // Adjust this to your actual database connection file

if (isset($_GET['id'])) {
    // Get user ID from query string
    $user_id = $_GET['id'];

    // Prepare the SQL statement
    $sql = "DELETE FROM tbluser WHERE user_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("s", $user_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo "User deleted successfully.";
            header('location: users.php');
        } else {
            echo "Error deleting user: " . $conn->error;
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
