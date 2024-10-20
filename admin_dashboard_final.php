<?php
session_start();
require 'db.php'; // Include your database connection file

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle password change for staff members
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $staff_id = $_POST['staff_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->bindParam(':password', $new_password);
    $stmt->bindParam(':id', $staff_id);
    $stmt->execute();

    $success = "Password changed successfully!";
}

// Fetch staff members
$stmt = $db->prepare("SELECT * FROM users WHERE role = 'staff'");
$stmt->execute();
$staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch monthly sales data
$salesDataMonthly = $db->query("
    SELECT DATE_FORMAT(sale_date, '%Y-%m') AS month, SUM(total_price) AS total_sales
    FROM (
        SELECT sale_date, total_price FROM sales
        UNION ALL
        SELECT sale_date2, total_price2 FROM sales2
        UNION ALL
        SELECT sale_date3, total_price3 FROM sales3
        UNION ALL
        SELECT sale_date4, total_price4 FROM sales4
        UNION ALL
        SELECT sale_date5, total_price5 FROM sales5
    ) AS all_sales
    GROUP BY month
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch yearly sales data
$salesDataYearly = $db->query("
    SELECT DATE_FORMAT(sale_date, '%Y') AS year, SUM(total_price) AS total_sales
    FROM (
        SELECT sale_date, total_price FROM sales
        UNION ALL
        SELECT sale_date2, total_price2 FROM sales2
        UNION ALL
        SELECT sale_date3, total_price3 FROM sales3
        UNION ALL
        SELECT sale_date4, total_price4 FROM sales4
        UNION ALL
        SELECT sale_date5, total_price5 FROM sales5
    ) AS all_sales
    GROUP BY year
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch product stock data
$stocks = $db->query("
    SELECT product_name, stock FROM (
        SELECT product_name, stock FROM products
        UNION ALL
        SELECT product_name2, stock2 FROM products2
        UNION ALL
        SELECT product_name3, stock3 FROM products3
        UNION ALL
        SELECT product_name4, stock4 FROM products4
        UNION ALL
        SELECT product_name5, stock5 FROM products5
    ) AS all_products
")->fetchAll(PDO::FETCH_ASSOC);

// Prepare monthly sales data for the chart
$salesMonths = [];
$salesTotalsMonthly = [];
foreach ($salesDataMonthly as $data) {
    $salesMonths[] = $data['month'];
    $salesTotalsMonthly[] = (float)$data['total_sales'];
}

// Prepare yearly sales data for the chart
$salesYears = [];
$salesTotalsYearly = [];
foreach ($salesDataYearly as $data) {
    $salesYears[] = $data['year'];
    $salesTotalsYearly[] = (float)$data['total_sales'];
}

// Prepare product stock data for the chart
$productNames = [];
$productStocks = [];
foreach ($stocks as $product) {
    $productNames[] = $product['product_name'];
    $productStocks[] = (int)$product['stock'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>MAIN BRANCH ADMIN</title>
    <style>

.chart-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            max-width: 1300px;
            margin: auto;
        }
        canvas {
    width: 100%; /* Allow the canvas to take full width */
    height: auto; /* Height will adjust automatically */
    max-width: 600px; /* Maintain the maximum width */
    max-height: 400px; /* You can adjust this as needed */
    margin: 20px;
}

        .log-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        .log-table th, .log-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .log-table th {
            background-color: #f2f2f2;
        }
    
        .chart-container1{
            position: relative;
            width: 700px;
            margin-top: 40vh;
            right: 350px;
        }
        .chart-container2{
            position: relative;
            width: 700px;
            left: 400px;
            bottom: 390px;
            
            margin-top: 3vh;
        }
        .chart-container3{
            position: relative;
            width: 700px;
            right: 350px;
            bottom: 350px;
           
        }




        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: lightgrey;
        }

        .sidebar {
        width: 100%;
        max-width: 250px;
        background: rgb(200, 230, 200);
        height: 100vh;
        position: fixed;
        color: white;
        overflow-y: auto;
    }

    .sidebar h2 {
        font-size: 24px;
        color: #00203f;
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
        color: white;
        
    }

    /* Button Link Styling */
    .sidebar ul li a {
        display: block;
        padding: 15px;
        background-color: #00203f;
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

    /* Special styling for logout button */
    .sidebar ul li a[href="logout.php"] {
        background-color: #d9534f;
    }

    .sidebar ul li a[href="logout.php"]:hover {
        background-color: burlywood;
    }
        .content {
            margin-left: 20%;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            background-color:#00203f ;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: white;
            font-size: 24px;
            border: 3px solid white;
            display: inline-block;
            padding: 5px 15px;
        }

        .main-content {
            background-color: #e0e0e0;
            height: 900px;
            border: 3px solid black;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            color: #666;

        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
            border-radius: 10px;
            text-align: center;
            
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .modal p {
            font-size: 18px;
        }

        .reset-button {
        padding: 10px 15px;
        margin: 10px; /* Adds space between buttons */
        border: none;
        border-radius: 5px; /* Rounded corners */
        color: white;
        cursor: pointer;
        transition: background-color 0.3s;
         /* Smooth transition for hover effect */
    }
    .reset-button:nth-child(1) {
        background-color: #4CAF50; /* Green for daily reset */
    }
    .reset-button:nth-child(2) {
        background-color: #2196F3; /* Blue for weekly reset */
    }
    .reset-button:nth-child(3) {
        background-color: #FF9800; /* Orange for monthly reset */
    }
    .reset-button:nth-child(4) {
        background-color: #f44336; /* Red for all reset */
    }
    .reset-button:hover {
        opacity: 0.8; /* Slightly transparent on hover */
    }
   
    .reset{
        position: relative;
        right: 195px;
        top: 50px;
        
    }

    .count {
        background-color: #f8f9fa; /* Light background */
        border: 1px solid #ccc; /* Light border */
        border-radius: 8px; /* Rounded corners */
        padding: 20px; /* Inner spacing */
        margin: 20px 0; /* Outer spacing */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        position: relative;
    }

    .count p {
        font-size: 18px; /* Font size */
        color: #333; /* Text color */
        margin: 10px 0; /* Spacing between paragraphs */
    }

    .count p:first-child {
        font-weight: bold; /* Bold for the first income */
        color: #4CAF50; /* Green color for daily income */
    }

    .count p:nth-child(2) {
        color: #2196F3; /* Blue for weekly income */
    }

    .count p:nth-child(3) {
        color: #FF9800; /* Orange for monthly income */
    }

    .count p:nth-child(4) {
        color: #f44336; /* Red for yearly income */
    }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a id="viewProductsBtn" href='./admin_dashboard_final.php'>DASHBOARD</a></li>
            <li><a id="addProductBtn" href='./add_stocks_admin.php'>ADD STOCKS PER BRANCH</a></li>
            <li><a id="addProductBtn" href='./add_product5.php'>PRODUCTS</a></li>
            <li><a id="addProductBtn" href='./branch_records_admin.php'>COMPLETED ORDERS</a></li>
            <li><a href="./logout.php" >Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1>IDMS DEALER SYSTEM ANALYTICS</h1>
        </div>

        <div class="main-content">
            <div>
                
            </div>
    <div class="container">

        <div class="chart-container1">
            <h2>Sales per Month</h2>
            <canvas id="salesChartMonthly"></canvas>
        </div>

        <div class="chart-container2">
            <h2>Sales per Year</h2>
            <canvas id="salesChartYearly"></canvas>
        </div>

        <div class="chart-container3">
            <h2>Product Stock</h2>
            <canvas id="stockChart"></canvas>
        </div>
    </div>
            </div>

            <div>
            <h1>Admin Dashboard</h1>
    
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    
    <h2>Change Staff Password</h2>
    <form method="POST" action="">
        <select name="staff_id" required>
            <option value="">Select Staff Member</option>
            <?php foreach ($staff_members as $staff): ?>
                <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['username']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit" name="change_password">Change Password</button>
    </form>
    
    <h2>Staff Members</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Branch</th>
        </tr>
        <?php foreach ($staff_members as $staff): ?>
            <tr>
                <td><?php echo htmlspecialchars($staff['username']); ?></td>
                <td><?php echo htmlspecialchars($staff['role']); ?></td>
                <td><?php echo htmlspecialchars($staff['branch_id']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
            </div>

    <script>
         const salesCtxMonthly = document.getElementById('salesChartMonthly').getContext('2d');
        const salesCtxYearly = document.getElementById('salesChartYearly').getContext('2d');
        const stockCtx = document.getElementById('stockChart').getContext('2d');

        // Monthly Sales Chart
        const salesChartMonthly = new Chart(salesCtxMonthly, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($salesMonths); ?>,
                datasets: [{
                    label: 'Total Sales (Monthly)',
                    data: <?php echo json_encode($salesTotalsMonthly); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    hoverBackgroundColor: 'rgba(75, 192, 192, 1)',
                    hoverBorderColor: 'rgba(75, 192, 192, 1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.7)', // Tooltip background
                        titleColor: '#fff', // Tooltip title color
                        bodyColor: '#fff' // Tooltip body color
                    },
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount'
                        },
                        grid: {
                            display: true, // Show grid lines
                            color: 'rgba(200, 200, 200, 0.5)' // Grid line color
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        },
                        grid: {
                            display: false // Hide grid lines for x-axis
                        }
                    }
                }
            }
        });

        // Yearly Sales Chart
        const salesChartYearly = new Chart(salesCtxYearly, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($salesYears); ?>,
                datasets: [{
                    label: 'Total Sales (Yearly)',
                    data: <?php echo json_encode($salesTotalsYearly); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    hoverBackgroundColor: 'rgba(153, 102, 255, 1)',
                    hoverBorderColor: 'rgba(153, 102, 255, 1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.7)', // Tooltip background
                        titleColor: '#fff', // Tooltip title color
                        bodyColor: '#fff' // Tooltip body color
                    },
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount'
                        },
                        grid: {
                            display: true, // Show grid lines
                            color: 'rgba(200, 200, 200, 0.5)' // Grid line color
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        },
                        grid: {
                            display: false // Hide grid lines for x-axis
                        }
                    }
                }
            }
        });

        // Product Stock Chart
        const stockChart = new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productNames); ?>,
                datasets: [{
                    label: 'Product Stock',
                    data: <?php echo json_encode($productStocks); ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.7)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 2,
                    hoverBackgroundColor: 'rgba(255, 206, 86, 1)',
                    hoverBorderColor: 'rgba(255, 206, 86, 1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.7)', // Tooltip background
                        titleColor: '#fff', // Tooltip title color
                        bodyColor: '#fff' // Tooltip body color
                    },
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Stock Amount'
                        },
                        grid: {
                            display: true, // Show grid lines
                            color: 'rgba(200, 200, 200, 0.5)' // Grid line color
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Products'
                        },
                        grid: {
                            display: false // Hide grid lines for x-axis
                        }
                    }
                }
            }
        });






        // selecting  branch

        document.addEventListener("DOMContentLoaded", () => {
            // Close the modals when close buttons are clicked
            document.querySelectorAll(".close").forEach(closeButton => {
                closeButton.addEventListener("click", () => {
                    const modalId = closeButton.getAttribute("data-modal");
                    document.getElementById(modalId).style.display = "none";
                    document.getElementById("branchSelect").selectedIndex = 0; // Reset selection
                });
            });

            // Close the modals when clicking outside of the modal content
            window.onclick = function(event) {
                const modals = document.querySelectorAll(".modal");
                modals.forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = "none";
                        document.getElementById("branchSelect").selectedIndex = 0; // Reset selection
                    }
                });
            };
        });

        // Open modal based on the selected branch
        function openModal() {
            const select = document.getElementById("branchSelect");
            const modalId = select.value;
            if (modalId) {
                document.getElementById(modalId).style.display = "block";
            }
        }
    </script>
    


   

   