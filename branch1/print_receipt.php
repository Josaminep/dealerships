<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 1) {
  
}

// Get the receipt ID from the URL
$receipt_id = $_GET['id'];

// Fetch the receipt details
$stmt = $db->prepare("SELECT r.id, r.receipt_details, r.created_at, s.total_price, p.product_name 
                      FROM receipts r
                      JOIN sales s ON r.sale_id = s.id
                      JOIN products p ON s.product_id = p.id
                      WHERE r.id = ?");
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
    <title>Receipt #<?php echo $receipt['id']; ?></title>
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
        <h2>Receipt #<?php echo $receipt['id']; ?></h2>
        <!-- <p><strong>Product:</strong> <?php echo $receipt['product_name']; ?></p> -->
        <p><strong>List Of your </strong> <?php echo $receipt['receipt_details']; ?></p>
        <p><strong>Date:</strong> <?php echo $receipt['created_at']; ?></p>
    </div>
</body>
</html>
