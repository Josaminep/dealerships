<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 1) {
    exit("Unauthorized access."); // Handle unauthorized access
}

// Get the receipt ID from the URL
$sale_id = $_GET['id']; // Assuming you meant sale_id instead of receipt_id

// Fetch the sale details from the sales table
$stmt = $db->prepare("SELECT id, product_id, product_name, price, sale_date, branch 
                      FROM sales 
                      WHERE id = ?");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the data as an associative array

// Debugging: Check if the sale was found
if (!$sale) {
    die("Sale not found for ID: " . htmlspecialchars($sale_id)); // Output the sale_id being searched for
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Receipt #<?php echo $sale['id']; ?></title>
    <style>
        /* Simple styling for receipt */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .receipt {
            border: 1px solid #000;
            padding: 20px;
            width: 300px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h2>Sale Receipt #<?php echo $sale['id']; ?></h2>
        <p><strong>Product ID:</strong> <?php echo htmlspecialchars($sale['product_id']); ?></p>
        <p><strong>Product Name:</strong> <?php echo htmlspecialchars($sale['product_name']); ?></p>
        <p><strong>Price:</strong> Php <?php echo number_format($sale['price'], 2); ?></p>
        <p><strong>Sale Date:</strong> <?php echo htmlspecialchars($sale['sale_date']); ?></p>
        <p><strong>Branch:</strong> <?php echo htmlspecialchars($sale['branch']); ?></p>
    </div>
</body>
</html>
