<?php
session_start();
require 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role']; // Assuming you have a role field
    $branch_id = $_POST['branch_id']; // Assuming you have a branch_id field

    // Prepare the SQL statement to insert user into the database
    $stmt = $db->prepare("INSERT INTO users (username, password, role, branch_id) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$username, $password, $role, $branch_id])) {
        // Registration successful
        header("Location: login.php");
    } else {
        $error = "Registration failed!";
    }
}
?>

<!-- Registration Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
        </select>
        <input type="number" name="branch_id" placeholder="Branch ID" required>
        <button type="submit">Register</button>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>
</body>
</html>
