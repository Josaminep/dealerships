<?php
session_start();
require '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $status = $data['status'];

    if (isset($id) && isset($status)) {
        $stmt = $db->prepare("UPDATE receipts SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $id]);

        if ($result) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(400); 
    }
} else {
    http_response_code(405);
}
?>
