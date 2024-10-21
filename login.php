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

    // Verify the password
    if ($user && password_verify($password, $user['password'])) {
        // Determine the role and branch, and set session variables
        if (isset($user['role'])) {
            // Admin login
            if ($user['role'] === 'admin') {
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_branch_id'] = $user['branch_id'];
                header("Location: admin_dashboard_final.php");
                exit();
            } 
            // Staff login
            elseif ($user['role'] === 'staff') {
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
                exit();
            }
        }
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        input[type="text"],
        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 50%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;          /* Set display to block */
            margin: 20px auto;     /* Center the button horizontally */
        }
        button:hover {
            background-color: #218838;
        }
        p {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <form method="post">
        <h2>Login</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>
</body>
</html>
