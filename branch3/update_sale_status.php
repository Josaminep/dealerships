<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input data from the request
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $status = $data['status'];

    // Validate input data
    if (isset($id) && isset($status)) {
        // Update the status in the database
        $stmt = $db->prepare("UPDATE receipts SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $id]);

        if ($result) {
            // Successful update
            http_response_code(200); // OK
        } else {
            // Update failed
            http_response_code(500); // Internal Server Error
        }
    } else {
        // Invalid input
        http_response_code(400); // Bad Request
    }
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
}
?>
