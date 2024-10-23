<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if the user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Check if sale_id is sent from the form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sale_id'])) {
    $sale_id = $_POST['sale_id'];

    // Fetch the sale details from the sales table
    $stmt = $db->prepare("SELECT id, product_name, price, sale_date, branch FROM sales WHERE id = ?");
    $stmt->execute([$sale_id]);
    $sale = $stmt->fetch();

    if ($sale) {
        // Insert the sale details into the receipts table
        $stmt = $db->prepare("INSERT INTO receipts (sale_id, product_name, price, sale_date, branch, status) VALUES (?, ?, ?, ?, ?, 'done')");
        $stmt->execute([
            $sale['id'],               // sale_id
            $sale['product_name'],      // product_name
            $sale['price'],             // price
            $sale['sale_date'],         // sale_date
            $sale['branch']             // branch
        ]);

        // Update the status of the sale in the sales table to 'done'
        $stmt = $db->prepare("UPDATE sales SET status = 'done' WHERE id = ?");
        $stmt->execute([$sale_id]);

        // Return success message
        echo json_encode(['success' => true, 'message' => 'Order accepted, status updated to done, and saved to receipts successfully!']);
        exit();
    } else {
        // Handle case where the sale is not found in the sales table
        echo json_encode(['success' => false, 'message' => 'Sale not found.']);
        exit();
    }
} else {
    // Handle case where no sale_id was sent
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}
?>
