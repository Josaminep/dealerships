<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 2) {
  
}

// Get the receipt ID from the URL
$receipt_id = $_GET['id'];

// Fetch the receipt details
$stmt = $db->prepare("SELECT r.id2 AS receipt_id, r.receipt_details2, r.created_at2, s.total_price2, p.product_name2 
                      FROM receipts2 r
                      JOIN sales2 s ON r.sale_id2 = s.id2
                      JOIN products2 p ON s.product_id2 = p.id2
                      WHERE r.id2 = ?");
$stmt->execute([$receipt_id]);
$receipt = $stmt->fetch();

if (!$receipt) {
    die("Receipt not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo htmlspecialchars($receipt['receipt_id']); ?></title>
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
        <h2>Receipt #<?php echo htmlspecialchars($receipt['receipt_id']); ?></h2>
        <p><strong>List Of your Purchases:</strong> <?php echo htmlspecialchars($receipt['receipt_details2']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($receipt['created_at2']); ?></p>
        <p><strong>Total Price:</strong> Php <?php echo number_format($receipt['total_price2'], 2); ?></p>
    </div>
</body>
</html>
