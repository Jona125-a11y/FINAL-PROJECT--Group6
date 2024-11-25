<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $location = $_POST['location'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>
        alert('Passwords do not match!');
        window.location.href = 'index.php';
      </script>";
        exit;
    }

    require "conn.php";

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Hash the password before storing it (recommended for security)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO tbluser (username, email, contact, address, password, usertype) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $usertype = "user"; // Setting usertype value
    $stmt->bind_param('ssssss', $username, $email, $contact, $location, $hashed_password, $usertype);

    if ($stmt->execute()) {
        echo "<script>
                alert('Signup successful!');
                window.location.href = 'index.php'; // Redirect after showing the alert
              </script>";
        exit;
    } else {
        echo "<script> alert('Error: " . $stmt->error .  " ')</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
