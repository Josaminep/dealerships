<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 1) {
    // Redirect or handle unauthorized access
    exit("Unauthorized access.");
}

// Check if the sale_id is set in the POST request
if (isset($_POST['sale_id'])) {
    $sale_id = $_POST['sale_id'];

    // Fetch sale details
    $stmt = $db->prepare("SELECT product_name, price, sale_date, branch FROM sales WHERE id = ?");
    $stmt->execute([$sale_id]);
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sale) {
        // Insert the sale details into the receipts table
        $stmt = $db->prepare("INSERT INTO receipts (sale_id, product_name, price, sale_date, branch) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sale_id, $sale['product_name'], $sale['price'], $sale['sale_date'], $sale['branch']]);

        // Optionally, redirect back to the sales records page or display a success message
        header("Location: sales_records.php?success=Order accepted successfully.");
        exit();
    } else {
        die("Sale not found.");
    }
} else {
    die("Invalid request.");
}
?>
