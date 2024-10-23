<?php
session_start();
require '../db.php';

if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 5) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sale_id'])) {
    $sale_id = $_POST['sale_id'];

    $stmt = $db->prepare("SELECT id, product_name, price, sale_date, branch FROM sales WHERE id = ?");
    $stmt->execute([$sale_id]);
    $sale = $stmt->fetch();

    if ($sale) {
        $stmt = $db->prepare("INSERT INTO receipts (sale_id, product_name, price, sale_date, branch, status) VALUES (?, ?, ?, ?, ?, 'done')");
        $stmt->execute([
            $sale['id'],               
            $sale['product_name'],      
            $sale['price'],             
            $sale['sale_date'],         
            $sale['branch']           
        ]);
        $stmt = $db->prepare("UPDATE sales SET status = 'done' WHERE id = ?");
        $stmt->execute([$sale_id]);

        echo json_encode(['success' => true, 'message' => 'Order accepted successfully!']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Sale not found.']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}
?>