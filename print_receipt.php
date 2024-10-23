<?php
session_start();
require 'db.php'; // Include your database connection file

if (!isset($_GET['id'])) {
    header("Location: branch_records_admin.php");
    exit();
}

$receipt_id = $_GET['id'];

// Fetch the receipt details along with the branch name
$stmt = $db->prepare("SELECT r.id, r.created_at, s.price, p.product_name, r.branch 
                       FROM receipts r
                       JOIN sales s ON r.sale_id = s.id
                       JOIN products p ON s.product_id = p.id
                       WHERE r.id = :id");
$stmt->bindParam(':id', $receipt_id);
$stmt->execute();
$receipt = $stmt->fetch(PDO::FETCH_ASSOC);

// Only proceed if the receipt exists
if (!$receipt) {
    header("Location: branch_records_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .receipt {
            border: 5px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            height: 500px; /* Setting height to 80% */
            max-height: 600px; /* Limit the max height for better control */
            overflow: auto; /* Allows scrolling if content overflows */
            margin: auto; /* Center the receipt horizontally */
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 50px;
        }
        .header p {
            font-size: 16px;
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 100px;
            font-size: 14px;
            color: #777;
        }
    </style>
    <script>
        window.onload = function() {
            window.print(); // Trigger print dialog on load
        };
    </script>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>Ever Sure Shop</h2>
            <p>Receipt ID: <?php echo $receipt['id']; ?></p>
            <br>
            <p><strong>Branch:</strong> <?php echo htmlspecialchars($receipt['branch']); ?></p>
            <p><strong>Product Name:</strong> <?php echo htmlspecialchars($receipt['product_name']); ?></p>
            <p><strong>Total Price:</strong> ₱<?php echo number_format($receipt['price'], 2); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($receipt['created_at']); ?></p>
        </div>

        <div class="footer">
            Thank you for your purchase!
        </div>
    </div>
</body>
</html>
