<?php
session_start();
require 'db.php';

include 'sidebar.php';

if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $branch_id = $_POST['branch_id'];
    $role = 'staff'; // Default role for branch users

    // Validate form data
    if (!empty($username) && !empty($password) && !empty($branch_id)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $conn = new mysqli("localhost", "root", "", "dealership_shop");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO users (username, password, role, branch_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $hashed_password, $role, $branch_id);

        if ($stmt->execute()) {
            echo "<script>alert('User added successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Branch User</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }
        .content {
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
            box-sizing: border-box;
        }
        .header {
            background-color: #003366;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 28px;
            color: white;
            margin: 0;
        }
        form {
            display: flex;
            flex-direction: column;
            background-color: #ffffff; /* White background for the form */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }
        label {
            margin-bottom: 5px;
            font-weight: bold; /* Bold labels for clarity */
        }
        input {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 16px; /* Consistent font size */
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px; /* Consistent font size */
            transition: background-color 0.3s; /* Smooth transition */
        }
        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
            <h1>Add Branch User</h1>
        </div>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="branch_id">Branch ID:</label>
            <input type="number" name="branch_id" id="branch_id" required>

            <button type="submit">Add User</button>
        </form>
    </div>
</body>
</html>
