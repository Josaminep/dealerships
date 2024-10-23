<?php
session_start();
require '../db.php'; // Include your database connection fil

// Check if user is logged in and is staff
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 5) {
    // handle unauthorized access
}

// Check if admin is logged in

// Function to log activities
function log_activity($db, $activity_type, $details) {
    $query = $db->prepare("INSERT INTO activity_log (activity_type, details) VALUES (?, ?)");
    $query->execute([$activity_type, $details]);
}

// Fetch Daily, Weekly, Monthly, and Yearly Income
$today = date('Y-m-d');
$this_week = date('Y-m-d', strtotime('-7 days'));
$this_month = date('Y-m-01');
$this_year = date('Y-01-01');

$daily_income_query = $db->prepare("SELECT SUM(price) FROM sales5 WHERE sale_date5 >= ?");
$weekly_income_query = $db->prepare("SELECT SUM(price) FROM sales5 WHERE sale_date5 >= ?");
$monthly_income_query = $db->prepare("SELECT SUM(price) FROM sales5 WHERE sale_date5 >= ?");
$yearly_income_query = $db->prepare("SELECT SUM(price) FROM sales5 WHERE sale_date5 >= ?");

$daily_income_query->execute([$today]);
$weekly_income_query->execute([$this_week]);
$monthly_income_query->execute([$this_month]);
$yearly_income_query->execute([$this_year]);

$daily_income = $daily_income_query->fetchColumn() ?: 0;
$weekly_income = $weekly_income_query->fetchColumn() ?: 0;
$monthly_income = $monthly_income_query->fetchColumn() ?: 0;
$yearly_income = $yearly_income_query->fetchColumn() ?: 0;

// Handle income reset
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset_all'])) {
        // Reset daily, weekly, and monthly income
        $db->exec("UPDATE sales5 SET price = 0 WHERE sale_date5 >= '$today'");
    } elseif (isset($_POST['reset_daily'])) {
        $db->exec("UPDATE sales5 SET price = 0 WHERE sale_date5 >= '$today'");
    } elseif (isset($_POST['reset_weekly'])) {
        $db->exec("UPDATE sales5 SET price = 0 WHERE sale_date5 >= '$this_week'");
    } elseif (isset($_POST['reset_monthly'])) {
        $db->exec("UPDATE sales5 SET price = 0 WHERE sale_date5 >= '$this_month'");
    }

    // Re-fetch income values after reset
    $daily_income_query->execute([$today]);
    $weekly_income_query->execute([$this_week]);
    $monthly_income_query->execute([$this_month]);
    $yearly_income_query->execute([$this_year]);

    $daily_income = $daily_income_query->fetchColumn() ?: 0;
    $weekly_income = $weekly_income_query->fetchColumn() ?: 0;
    $monthly_income = $monthly_income_query->fetchColumn() ?: 0;
    $yearly_income = $yearly_income_query->fetchColumn() ?: 0;
}

// Fetch monthly income for the past 12 months dynamically
$monthly_sales = [];
$monthly_labels = [];
for ($i = 0; $i < 12; $i++) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_label = date('M Y', strtotime("-$i months"));
    $monthly_sales_query = $db->prepare("SELECT SUM(price) FROM sales5 WHERE sale_date5 >= ? AND sale_date5 < DATE_ADD(?, INTERVAL 1 MONTH)");
    $monthly_sales_query->execute([$month_start, $month_start]);
    $monthly_sales[] = $monthly_sales_query->fetchColumn() ?: 0;
    $monthly_labels[] = $month_label;
}
$monthly_sales = array_reverse($monthly_sales);
$monthly_labels = array_reverse($monthly_labels);

// Fetch yearly income for the past 5 years
$yearly_sales = [];
$yearly_labels = [];
for ($i = 0; $i < 5; $i++) {
    $year_start = date('Y-01-01', strtotime("-$i years"));
    $year_label = date('Y', strtotime("-$i years"));
    $yearly_sales_query = $db->prepare("SELECT SUM(price) FROM sales2 WHERE sale_date2 >= ? AND sale_date2 < DATE_ADD(?, INTERVAL 1 YEAR)");
    $yearly_sales_query->execute([$year_start, $year_start]);
    $yearly_sales[] = $yearly_sales_query->fetchColumn() ?: 0;
    $yearly_labels[] = $year_label;
}
$yearly_sales = array_reverse($yearly_sales);
$yearly_labels = array_reverse($yearly_labels);

// Fetch product stock levels
$product_stocks = $db->query("SELECT product_name, stock FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Fetch activity log records
$activity_log_query = $db->query("SELECT * FROM activity_log ORDER BY activity_time DESC LIMIT 20");
$activity_log = $activity_log_query->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $stock = $_POST['stock'];

    // Add product to the database
    $query = $db->prepare("INSERT INTO products (product_name, stock) VALUES (?, ?)");
    $query->execute([$product_name, $stock]);

    // Log the activity
    log_activity($db, 'Product Added', "Product '$product_name' was added with stock $stock.");
}

// Handle purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_purchase'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product info
    $product_query = $db->prepare("SELECT product_name FROM products WHERE id = ?");
    $product_query->execute([$product_id]);
    $product = $product_query->fetch();

    // Add purchase to sales table
    $price = 100; // example price, change accordingly
    $insert_sale = $db->prepare("INSERT INTO sales (product_id, quantity, sale_date, price) VALUES (?, ?, ?, ?)");
    $insert_sale->execute([$product_id, $quantity, date('Y-m-d'), $price]);

    // Log the activity
    log_activity($db, 'Purchase', "Purchased $quantity of '{$product['product_name']}'.");

    echo "Purchase successful.";

    // Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $brand = $_POST['brand'];
    $stock = $_POST['stock'];

    $stmt = $db->prepare("INSERT INTO products (product_name, price, quantity, brand, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$product_name, $price, $quantity, $brand, $stock]);

    echo "Product added successfully!";
}

// Handle adding stock to an existing product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_stock'])) {
    $product_id = $_POST['product_id'];
    $additional_stock = $_POST['additional_stock'];

    $stmt = $db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    $stmt->execute([$additional_stock, $product_id]);

    echo "Stock added successfully!";
}

// Handle modifying product price
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modify_price'])) {
    $product_id = $_POST['product_id'];
    $new_price = $_POST['new_price'];

    $stmt = $db->prepare("UPDATE products SET price = ? WHERE id = ?");
    $stmt->execute([$new_price, $product_id]);

    echo "Price updated successfully!";
}

// Fetch all products for the stock addition and price modification
$products = $db->query("SELECT * FROM products")->fetchAll();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>INVENTORY 1</title>
    <style>

.chart-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: auto;
        }
        canvas {
            max-width: 400px;
            max-height: 200px;
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: lightgrey;
        }
        .content {
    flex-grow: 1; /* Allow content area to take remaining space */
    padding: 20px;
    margin-left: 250px; /* Set a left margin equal to the sidebar width */
}


        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: black;
            font-size: 24px;
            border: 3px solid #a76df0;
            display: inline-block;
            padding: 5px 15px;
        }

        .main-content {
            background-color: #e0e0e0;
            height: 500px;
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
    <?php include 'sidebar.php'; // Sidebar content goes here ?>
</div>
    <div class="content">
        <div class="header">
            <h1>SARI SARI INVENTORY SYSTEM</h1>
        </div>

        <div class="main-content">
            
        <div class="chart-container">
        <canvas id="monthlyIncomeChart"></canvas>
        <canvas id="yearlyIncomeChart"></canvas>
        <canvas id="productStockChart"></canvas>
<div class="count">
    <p>Daily Income: Php <?php echo number_format($daily_income, 2); ?></p>
    <p>Weekly Income: Php <?php echo number_format($weekly_income, 2); ?></p>
    <p>Monthly Income: Php <?php echo number_format($monthly_income, 2); ?></p>
    <p>Yearly Income: Php <?php echo number_format($yearly_income, 2); ?></p>
</div>
        <div class="reset">
    <form method="POST">
    <button type="submit" name="reset_daily" class="reset-button">Reset Daily Income</button>
    <button type="submit" name="reset_weekly" class="reset-button">Reset Weekly Income</button>
    <button type="submit" name="reset_monthly" class="reset-button">Reset Monthly Income</button>
    <button type="submit" name="reset_all" class="reset-button">Reset All Incomes</button>
</form>
</div>
    
    </div>
    
   


        </div>
    </div>

    
<body>


    <script>
        const monthlyIncomeCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
        const yearlyIncomeCtx = document.getElementById('yearlyIncomeChart').getContext('2d');
        const productStockCtx = document.getElementById('productStockChart').getContext('2d');

        // Monthly Income Chart
        const monthlyIncomeChart = new Chart(monthlyIncomeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($monthly_labels); ?>,
                datasets: [{
                    label: 'Monthly Income',
                    data: <?php echo json_encode($monthly_sales); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Yearly Income Chart
        const yearlyIncomeChart = new Chart(yearlyIncomeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($yearly_labels); ?>,
                datasets: [{
                    label: 'Yearly Income',
                    data: <?php echo json_encode($yearly_sales); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Product Stock Chart
        const productLabels = <?php echo json_encode(array_column($product_stocks, 'product_name')); ?>;
        const productStocks = <?php echo json_encode(array_column($product_stocks, 'stock')); ?>;

        const productStockChart = new Chart(productStockCtx, {
            type: 'bar',
            data: {
                labels: productLabels,
                datasets: [{
                    label: 'Product Stocks',
                    data: productStocks,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>



