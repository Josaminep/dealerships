<?php
// customer_signup.php

// Database connection
$servername = "localhost"; // Change if your server is different
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "dealership_shop"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$signup_error = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $signup_error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $signup_error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $signup_error = "Password must be at least 8 characters.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect or show success message
            header("Location: login.php"); // Redirect to a success page
            exit();
        } else {
            $signup_error = "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>
