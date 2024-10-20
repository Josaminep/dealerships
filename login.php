<?php
session_start();
require 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user is an admin or staff
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user is not found, check if they are a customer
    if (!$user) {
        $stmt = $db->prepare("SELECT * FROM customers WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verify the password
    if ($user && password_verify($password, $user['password'])) {
        if (isset($user['role']) && $user['role'] === 'admin') {
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_branch_id'] = $user['branch_id'];
            header("Location: admin_dashboard_final.php");
        } else if (isset($user['role']) && $user['role'] === 'staff') {
            $_SESSION['staff_user_id'] = $user['id'];
            $_SESSION['staff_role'] = $user['role'];
            $_SESSION['staff_branch_id'] = $user['branch_id'];

            // Redirect based on branch ID for staff
            switch ($user['branch_id']) {
                case 1:
                    header("Location: ./branch1/dashboard.php");
                    break;
                case 2:
                    header("Location: ./branch2/dashboard2.php");
                    break;
                case 3:
                    header("Location: ./branch3/dashboard3.php");
                    break;
                case 4:
                    header("Location: ./branch4/dashboard4.php");
                    break;
                case 5:
                    header("Location: ./branch5/dashboard5.php");
                    break;
                default:
                    header("Location: login.php"); // Handle unexpected branch
            }
        } else {
            // Handle customer login
            $_SESSION['customer_user_id'] = $user['id'];
            header("Location: ./customer dashboard/customer_dashboard.php"); // Redirect to customer dashboard
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>
</body>
</html>
