<?php
require "conn.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST["username"]; // If using either username or email for login
    $password = $_POST["password"];

    // Check if user exists in database
    $query = $conn->prepare("SELECT * FROM tbluser WHERE username = ? OR email = ?");
    $query->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            // Successful login
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["user_id"];

            if ($row['usertype'] == 'user') {
                header("location: home.php");
            } elseif ($row['usertype'] == 'employee') {
                header("location: employee.php");
            } elseif ($row['usertype'] == 'admin') {
                header("location: admin.php");
            }
            exit;
        } else {
            echo "<script> alert('Wrong Password'); </script>";
            header("location: index.php");
            exit;
        }
    } else {
        echo "<script> alert('User Not Registered'); </script>";
        header("location: index.php");
        exit;
    }
}
?>
