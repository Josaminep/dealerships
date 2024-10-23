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
    SELECT DATE_FORMAT(sale_date, '%Y-%m') AS month, SUM(price) AS total_sales
    FROM (
        SELECT sale_date, price FROM sales
        UNION ALL
        SELECT sale_date2, price FROM sales2
        UNION ALL
        SELECT sale_date3, price FROM sales3
        UNION ALL
        SELECT sale_date4, price FROM sales4
        UNION ALL
        SELECT sale_date5, price FROM sales5
    ) AS all_sales
    GROUP BY month
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch yearly sales data
$salesDataYearly = $db->query("
    SELECT DATE_FORMAT(sale_date, '%Y') AS year, SUM(price) AS total_sales
    FROM (
        SELECT sale_date, price FROM sales
        UNION ALL
        SELECT sale_date2, price FROM sales2
        UNION ALL
        SELECT sale_date3, price FROM sales3
        UNION ALL
        SELECT sale_date4, price FROM sales4
        UNION ALL
        SELECT sale_date5, price FROM sales5
    ) AS all_sales
    GROUP BY year
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch product stock data
$stocks = $db->query("SELECT product_name, price, quantity, brand, stock, categories, created_at FROM products")->fetchAll(PDO::FETCH_ASSOC);

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


<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>MAIN BRANCH ADMIN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .content {
            margin-left: 270px; /* Adjusted for sidebar width */
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #00203f;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header h1 {
            color: white;
            font-size: 24px;
            margin: 0;
            display: inline-block;
            padding: 5px 15px;
        }

        .chart-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        canvas {
            width: 100%;
            height: auto;
            max-width: 400px;
            margin: 10px;
        }

        .table-container {
            margin-top: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        th, td {
            transition: background-color 0.3s;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

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
    </style>
</head>
<body>

    <div class="content">
        <div class="header">
            <h1>IDMS DEALER SYSTEM ANALYTICS</h1>
        </div>

        <div class="main-content">
            <div class="chart-container">
                <div>
                    <h2>Sales per Month</h2>
                    <canvas id="salesChartMonthly"></canvas>
                </div>
                <div>
                    <h2>Sales per Year</h2>
                    <canvas id="salesChartYearly"></canvas>
                </div>
                <div>
                    <h2>Product Stock</h2>
                    <canvas id="stockChartByName"></canvas>
                </div>
            </div>

        <script>
            const salesChartMonthly = new Chart(document.getElementById('salesChartMonthly'), {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($salesMonths); ?>,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: <?php echo json_encode($salesTotalsMonthly); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const salesChartYearly = new Chart(document.getElementById('salesChartYearly'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($salesYears); ?>,
                    datasets: [{
                        label: 'Yearly Sales',
                        data: <?php echo json_encode($salesTotalsYearly); ?>,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // New chart for Product Stock by Name
            const stockChartByName = new Chart(document.getElementById('stockChartByName'), {
                type: 'bar', // Change to bar chart
                data: {
                    labels: <?php echo json_encode($productNames); ?>,
                    datasets: [{
                        label: 'Product Stock',
                        data: <?php echo json_encode($productStocks); ?>,
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
</body>
</html>
