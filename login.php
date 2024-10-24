<?php
session_start(); // Start the session at the very beginning
require 'db.php'; // Ensure the database connection is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($user && password_verify($password, $user['password'])) {
        // Determine the role and set session variables
        switch ($user['role']) {
            case 'admin':
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_branch_id'] = $user['branch_id'];
                header("Location: admin_dashboard_final.php");
                exit();

            case 'staff':
                $_SESSION['staff_user_id'] = $user['id'];
                $_SESSION['staff_role'] = $user['role'];
                $_SESSION['staff_branch_id'] = $user['branch_id'];
                header("Location: ./branch" . $user['branch_id'] . "/dashboard{$user['branch_id']}.php");
                exit();

            case 'customer':
                $_SESSION['customer_user_id'] = $user['id'];
                $_SESSION['customer_role'] = $user['role'];
                header("Location: customer_dashboard.php");
                exit();

            default:
                $error = "Invalid role!";
                break;
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin & Staff Login Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(45deg, #1a2980, #26d0ce);
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 300px;
            transition: all 0.3s ease;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #1a2980;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #1a2980;
            outline: none;
            box-shadow: 0 0 10px rgba(26, 41, 128, 0.1);
        }

        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, #1a2980, #26d0ce);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 41, 128, 0.3);
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: block;
        }

        .alert-error {
            background-color: #ffe6e6;
            border: 1px solid #ff9999;
            color: #cc0000;
        }
        .customer{
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form id="adminLogin" method="POST" onsubmit="return validateLogin(event, 'admin');">
        <h2>Login</h2>
        <div class="form-group">
            <input type="text" placeholder="Username" id="adminUsername" name="username" required>
        </div>
        <div class="form-group">
            <input type="password" placeholder="Password" id="adminPassword" name="password" required>
        </div>
        <button type="submit">Login</button>
        <div class="customer">
            <a href="customer/login.php">Customer</a>
        </div>
    </form>

</div>

<script>
    function showTab(tab) {
        document.getElementById('adminLogin').style.display = tab === 'admin' ? 'block' : 'none';
        document.getElementById('staffLogin').style.display = tab === 'staff' ? 'block' : 'none';

        // Update tab styles
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(t => {
            t.classList.remove('active');
        });
        document.querySelector(.tab.active).classList.remove('active');
        document.querySelector(.tab.${tab}).classList.add('active');
    }

    function validateLogin(event, role) {
        const username = role === 'admin' ? document.getElementById('adminUsername').value : document.getElementById('staffUsername').value;

        // Check if user is trying to access admin while logged in as staff
        if (role === 'staff' && username !== "") {
            alert("You are not authorized to log in as an admin!");
            event.preventDefault(); // Prevent form submission
            return false; // Stop execution
        }

        return true; // Allow form submission
    }
</script>

</body>
</html>
