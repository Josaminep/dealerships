<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    /* Sidebar specific styles */
    .sidebar {
            width: 20%;
            background-color: black;
            height: 100vh;
            position: fixed;
            color: white;
        }

        .sidebar h2 {
            font-size: 40px;
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
            display: block;
            padding: 20px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            font-size: 16px;
            margin-left: 20px;
            margin-right: 20px;
            
        }

        .sidebar ul li a:hover {
            background-color: burlywood;
        }

        /* Special styling for logout button */
        .sidebar ul li a[href="logout.php"] {
            background-color: #d9534f;
        }

        .sidebar ul li a[href="logout.php"]:hover {
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
        <h2>BRANCH 1</h2>
        <ul>
            <li><a id="viewProductsBtn" href='./dashboard.php'>Dashboard</a></li>
            <li><a id="viewProductsBtn" href='./view_products.php'>View Stock Products</a></li>
            <li><a id="purchaseBtn" href='./purchase.php'>Pending Orders</a></li>
            <li><a id="viewReceiptsBtn" href='./print_receipt.php'>Completed Orders</a></li>
            <li><a id="viewReceiptsBtn" href='./view_receipts.php'>Records of Sales</a></li>
            <li><a href="../logout.php" >Logout</a></li>
        </ul>
    </div>
</body>
</html>
