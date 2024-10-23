<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 1) {
    // Redirect or handle unauthorized access
    exit("Unauthorized access.");
}

/**
 * Update the sale status in the database.
 *
 * @param PDO $db Database connection.
 * @param int $saleId The ID of the sale to update.
 * @param string $status The new status to set.
 * @return bool Returns true on success, false on failure.
 */
function updateSaleStatus($db, $saleId, $status) {
    // Prepare the SQL statement to update the status
    $stmt = $db->prepare("UPDATE receipts SET status = ? WHERE id = ?");
    
    // Execute the statement and return the result
    return $stmt->execute([$status, $saleId]);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input data
    $data = json_decode(file_get_contents('php://input'), true);
    $saleId = $data['id'] ?? null; // Use null coalescing to avoid undefined index notice
    $status = $data['status'] ?? null;

    // Validate input
    if ($saleId === null || $status === null) {
        http_response_code(400); // Bad request
        echo json_encode(['message' => 'Invalid input.']);
        exit;
    }

    // Update the sale status
    if (updateSaleStatus($db, $saleId, $status)) {
        http_response_code(200); // Success
        echo json_encode(['message' => 'Sale status updated successfully.']);
    } else {
        // Log error information
        error_log("Failed to update sale status for ID: $saleId. Error: " . print_r($db->errorInfo(), true)); // Log error info to server log
        http_response_code(500); // Internal server error
        echo json_encode(['message' => 'Failed to update the sale status. Please try again.']);
    }
} else {
    http_response_code(405); // Method not allowed
    echo json_encode(['message' => 'Method not allowed.']);
}
?>