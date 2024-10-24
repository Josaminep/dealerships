<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Sidebar specific styles */
        .sidebar {
            width: 100%;
            max-width: 250px;
            background-color: black;
            height: 100vh;
            position: fixed;
            color: white;
            overflow-y: auto;
        }

        .sidebar h2 {
            font-size: 24px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-top: 40px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
            margin-top: 20px;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #5cb85c; /* Default background color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li a:hover {
            background-color: burlywood; /* Hover color */
        }

        .sidebar ul li a.active {
            background-color: #e8d102; /* Active link color */
            color: black;
        }

        .sidebar ul li a[href="logout.php"] {
            background-color: #d9534f;
        }

        .sidebar ul li a[href="logout.php"]:hover {
            background-color: burlywood;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Main Branch<br>Admin</h2>
        <ul>
            <li><a href='./admin_dashboard_final.php' class="<?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard_final.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> DASHBOARD</a></li>
            <li><a href='./add_stocks_admin.php' class="<?= basename($_SERVER['PHP_SELF']) == 'add_stocks_admin.php' ? 'active' : '' ?>"><i class="fas fa-boxes"></i> ADD STOCKS/PRODUCTS</a></li>
            <li><a href='./admin_products.php' class="<?= basename($_SERVER['PHP_SELF']) == 'admin_products.php' ? 'active' : '' ?>"><i class="fas fa-list"></i> PRODUCTS</a></li>
            <li><a href='./branch_records_admin.php' class="<?= basename($_SERVER['PHP_SELF']) == 'branch_records_admin.php' ? 'active' : '' ?>"><i class="fas fa-clipboard-check"></i> COMPLETED ORDERS</a></li>
            <li><a href='./add_branch_user.php' class="<?= basename($_SERVER['PHP_SELF']) == 'add_branch_user.php' ? 'active' : '' ?>"><i class="fas fa-user-plus"></i> ADD BRANCH USER</a></li>
            <li><a href="./logout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : '' ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</body>
</html>
