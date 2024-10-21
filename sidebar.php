<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        display: block;
        padding: 15px;
        background-color: #5cb85c;
        color: white;
        text-decoration: none;
        text-align: center;
        border-radius: 5px;
        font-size: 16px;
        margin-left: 10px;
        margin-right: 10px;
    }

    .sidebar ul li a:hover {
        background-color: burlywood;
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
        <h2>Dashboard</h2>
        <ul>
            <li><a href='./admin_dashboard_final.php'>DASHBOARD</a></li>
            <li><a href='./add_stocks_admin.php'>ADD STOCKS PER BRANCH</a></li>
            <li><a href='./add_product5.php'>PRODUCTS</a></li>
            <li><a href='./branch_records_admin.php'>COMPLETED ORDERS</a></li>
            <li><a href="./logout.php">Logout</a></li>
        </ul>
    </div>
</body>
</html>
