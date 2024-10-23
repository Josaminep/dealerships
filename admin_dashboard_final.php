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

// Fetch monthly sales data from the receipts table grouped by branch
$salesDataMonthly = $db->query("
    SELECT branch, DATE_FORMAT(sale_date, '%M %Y') AS month, SUM(price) AS total_sales
    FROM receipts
    WHERE branch IN ('Branch 1', 'Branch 2', 'Branch 3', 'Branch 4', 'Branch 5')
    GROUP BY branch, month
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch yearly sales data from the receipts table grouped by branch
$salesDataYearly = $db->query("
    SELECT branch, DATE_FORMAT(sale_date, '%Y') AS year, SUM(price) AS total_sales
    FROM receipts
    WHERE branch IN ('Branch 1', 'Branch 2', 'Branch 3', 'Branch 4', 'Branch 5')
    GROUP BY branch, year
")->fetchAll(PDO::FETCH_ASSOC);

// Prepare monthly sales data for the chart
$salesMonths = [];
$salesTotalsMonthly = [];
$branchColors = [
    'Branch 1' => 'rgba(255, 99, 132, 0.2)', // Red
    'Branch 2' => 'rgba(54, 162, 235, 0.2)', // Blue
    'Branch 3' => 'rgba(255, 206, 86, 0.2)', // Yellow
    'Branch 4' => 'rgba(75, 192, 192, 0.2)', // Teal
    'Branch 5' => 'rgba(153, 102, 255, 0.2)', // Purple
];

foreach ($salesDataMonthly as $data) {
    $salesMonths[$data['branch']][] = $data['month'];
    $salesTotalsMonthly[$data['branch']][] = (float)$data['total_sales'];
}

// Prepare yearly sales data for the chart
$salesYears = [];
$dataSetsYearly = [];

// Initialize arrays for chart data
foreach ($salesDataYearly as $data) {
    $branch = $data['branch'];
    $year = $data['year'];
    $total_sales = (float)$data['total_sales'];

    if (!isset($salesYears[$year])) {
        $salesYears[$year] = true; // Keep track of unique years
    }

    if (!isset($dataSetsYearly[$branch])) {
        $dataSetsYearly[$branch] = [
            'label' => $branch,
            'data' => array_fill(0, count($salesYears), 0),
            'backgroundColor' => $branchColors[$branch], // Set unique background color
            'borderColor' => str_replace('0.2', '1', $branchColors[$branch]), // Make border color more opaque
            'borderWidth' => 1,
        ];
    }

    // Set sales data for the corresponding year
    $yearIndex = array_search($year, array_keys($salesYears));
    if ($yearIndex !== false) {
        $dataSetsYearly[$branch]['data'][$yearIndex] += $total_sales;
    }
}

// Prepare product stock data
$stocks = $db->query("SELECT product_name, price, quantity, brand, stock FROM products")->fetchAll(PDO::FETCH_ASSOC);

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
        flex-wrap: wrap; /* Allow wrapping of items */
        margin: 20px 0;
    }
    .chart-item {
        flex: 1 1 50%; /* Allow each chart item to take 50% of the width */
        box-sizing: border-box; /* Include padding and border in width */
        padding: 10px; /* Add some padding around the chart boxes */
        height: 300px; /* Set a fixed height for chart items */
    }
    canvas {
        width: 100%;
        height: 100%; /* Set height to 100% of the chart item */
    }
    /* Modal styles remain unchanged */
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
                <div class="chart-item">
                    <h2>Sales per Month</h2>
                    <canvas id="salesChartMonthly"></canvas>
                </div>
                <div class="chart-item">
                    <h2>Sales per Year</h2>
                    <canvas id="salesChartYearly"></canvas>
                </div>
                <div class="chart-item">
                    <h2>Product Stock</h2>
                    <canvas id="stockChartByName"></canvas>
                </div>
            </div>

            <script>
                const salesChartMonthly = new Chart(document.getElementById('salesChartMonthly'), {
                    type: 'bar', // Change from 'line' to 'bar'
                    data: {
                        labels: <?php echo json_encode(array_values($salesMonths)); ?>,
                        datasets: [
                            <?php foreach ($salesTotalsMonthly as $branch => $sales): ?>
                                {
                                    label: '<?php echo $branch; ?>',
                                    data: <?php echo json_encode($sales); ?>,
                                    backgroundColor: '<?php echo $branchColors[$branch]; ?>', // Use the same colors
                                    borderColor: '<?php echo str_replace('0.2', '1', $branchColors[$branch]); ?>', // Make border color more opaque
                                    borderWidth: 1
                                },
                            <?php endforeach; ?>
                        ]
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
                        labels: <?php echo json_encode(array_keys($salesYears)); ?>,
                        datasets: [
                            <?php foreach ($dataSetsYearly as $dataset): ?>
                                {
                                    label: '<?php echo $dataset['label']; ?>',
                                    data: <?php echo json_encode($dataset['data']); ?>,
                                    backgroundColor: '<?php echo $dataset['backgroundColor']; ?>',
                                    borderColor: '<?php echo $dataset['borderColor']; ?>',
                                    borderWidth: 1
                                },
                            <?php endforeach; ?>
                        ]
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

                const stockChartByName = new Chart(document.getElementById('stockChartByName'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($productNames); ?>,
                        datasets: [{
                            label: 'Stock',
                            data: <?php echo json_encode($productStocks); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
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
    </div>
</body>
</html>