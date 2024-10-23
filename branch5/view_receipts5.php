<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 5) {
    // Redirect or handle unauthorized access
    exit("Unauthorized access.");
}

// Fetch sales data for branch 1 with status = 'done'
$stmt = $db->prepare("SELECT id, product_name, price, sale_date, branch FROM receipts WHERE branch = ? AND status = ?");
$stmt->execute(['Branch 5', 'completed']);
$sales = $stmt->fetchAll();

// Initialize total price variable
$totalPrice = 0;

// Calculate total price
foreach ($sales as $sale) {
    $totalPrice += $sale['price']; // Add each sale's price to total
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <title>Sales Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 250px;
            border: 3px solid black;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #eaeaea;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .go-back-button {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        .go-back-button i {
            margin-right: 8px;
        }
        .go-back-button:hover {
            background-color: #4cae4c;
        }
        .total-price {
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <?php include 'sidebar.php'; // Sidebar content goes here ?>
</div>

<div class="content">
    <div>
        <a href="dashboard5.php" class="go-back-button">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>

    <h1>Sales Record</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Sale Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sales)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No sales records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($sales as $sale): ?>
                    <tr id="row_<?php echo htmlspecialchars($sale['id']); ?>">
                        <td><?php echo htmlspecialchars($sale['id']); ?></td>
                        <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                        <td>Php <?php echo number_format($sale['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="total-price">
        Total Price: Php <?php echo number_format($totalPrice, 2); ?>
    </div>
</div>

</body>
</html>
