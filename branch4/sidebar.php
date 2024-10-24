<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Sidebar specific styles */
        .sidebar {
            width: 20%;
            background-color: black;
            height: 100vh;
            position: fixed;
            color: white;
        }

        .sidebar h3 {
            font-size: 25px;
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

        /* Button Link Styling */
        .sidebar ul li a {
            display: flex; /* Use flex for justification */
            align-items: center; /* Center items vertically */
            padding: 20px;
            background-color: #5cb85c; /* Default background color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-left: 20px;
            margin-right: 20px;
            justify-content: flex-start; /* Align items to the left */
        }

        .sidebar ul li a:hover {
            background-color: burlywood; /* Hover color */
        }
        /* Active link styling */
        .sidebar ul li a.active {
            background-color: #f0ad4e; /* Active link color */
            color: black;
        }

        /* Special styling for logout button */
        .sidebar ul li a[href="logout.php"] {
            background-color: #d9534f;
        }

        .sidebar ul li a[href="logout.php"]:hover {
            background-color: burlywood;
        }

        .sidebar ul li a i {
            margin-right: 10px; /* Space between icon and text */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>BRANCH 4</h3>
        <ul>
            <li><a href='./dashboard4.php' class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard4.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href='./view_products4.php' class="<?= basename($_SERVER['PHP_SELF']) == 'view_products4.php' ? 'active' : '' ?>"><i class="fas fa-boxes"></i> View Stock Products</a></li>
            <li><a href='./purchase4.php' class="<?= basename($_SERVER['PHP_SELF']) == 'purchase4.php' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Pending Orders</a></li>
            <li><a href='./print_receipt4.php' class="<?= basename($_SERVER['PHP_SELF']) == 'print_receipt4.php' ? 'active' : '' ?>"><i class="fas fa-clipboard-check"></i> Completed Orders</a></li>
            <li><a href='./view_receipts4.php' class="<?= basename($_SERVER['PHP_SELF']) == 'view_receipts4.php' ? 'active' : '' ?>"><i class="fas fa-receipt"></i> Records of Sales</a></li>
            <li><a href="../logout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : '' ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</body>
</html>