<?php
session_start();
require 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert customer into the database
    $stmt = $db->prepare("INSERT INTO customers (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $password])) {
        $_SESSION['success'] = "Customer registered successfully!";
    } else {
        $_SESSION['error'] = "Error registering customer.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
        <?php 
        if (isset($_SESSION['success'])) {
            echo "<p>{$_SESSION['success']}</p>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<p>{$_SESSION['error']}</p>";
            unset($_SESSION['error']);
        }
        ?>
    </form>
</body>
</html>
