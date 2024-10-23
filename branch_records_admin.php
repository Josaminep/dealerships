<?php
session_start();
include 'sidebar.php';
require 'db.php'; // Include your database connection file

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize messages
$message = '';
$delete_message = '';

// Handle password change for users
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['change_password']) || isset($_POST['user_to_update']))) {
    $user_to_update = isset($_POST['user_to_update']) ? $_POST['user_to_update'] : $_POST['staff_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id OR username = :username");
    $stmt->bindParam(':password', $new_password);
    $stmt->bindParam(':id', $user_to_update);
    $stmt->bindParam(':username', $user_to_update);
    
    if ($stmt->execute()) {
        $message = "Password updated successfully for $user_to_update.";
    } else {
        $message = "Error updating password.";
    }
}

// Handle receipt deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_receipt'])) {
    $receipt_id = $_POST['receipt_id'];
    $delete_query = $db->prepare("DELETE FROM receipts WHERE id = :id");
    $delete_query->bindParam(':id', $receipt_id);
    
    if ($delete_query->execute()) {
        log_activity($db, 'Receipt Deleted', "Receipt with ID $receipt_id has been deleted.");
        $delete_message = "Receipt deleted successfully.";
    } else {
        $delete_message = "Error deleting receipt.";
    }
}

// Function to log activities
function log_activity($db, $activity_type, $details) {
    $query = $db->prepare("INSERT INTO activity_log (activity_type, details) VALUES (?, ?)");
    $query->execute([$activity_type, $details]);
}

// Fetch all receipts for the selected branch
$branch_name = isset($_GET['branch']) ? $_GET['branch'] : 'Branch 1'; // Default to Branch 1
$receipts = $db->prepare("SELECT r.id, r.created_at, s.price, p.product_name 
                           FROM receipts r
                           JOIN sales s ON r.sale_id = s.id
                           JOIN products p ON s.product_id = p.id
                           WHERE r.branch = :branch_name
                           ORDER BY r.created_at DESC");
$receipts->bindParam(':branch_name', $branch_name);
$receipts->execute();
$receipts = $receipts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .content {
            margin-left: 270px; /* Adjusted for sidebar width */
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #00203f;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .header h1 {
            color: white;
            font-size: 24px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #00203f;
            color: white;
        }
        .print-button, .delete-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-button {
            background-color: red;
        }
        .print-button:hover, .delete-button:hover {
            opacity: 0.8;
        }
        /* Popup styles */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
        .popup.active {
            display: block;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .overlay.active {
            display: block;
        }
    </style>
    <script>
        function printReceipt(receiptId) {
            window.location.href = 'print_receipt.php?id=' + receiptId; // Redirect to print page
        }

        function redirectToPage() {
            var selectedOption = document.getElementById("options").value;
            window.location.href = "branch_records_admin.php?branch=" + selectedOption;
        }

        function showPopup(message) {
            document.getElementById('popupMessage').innerText = message;
            document.querySelector('.popup').classList.add('active');
            document.querySelector('.overlay').classList.add('active');
        }

        function closePopup() {
            document.querySelector('.popup').classList.remove('active');
            document.querySelector('.overlay').classList.remove('active');
            location.reload(); // Reload page to refresh data
        }
    </script>
</head>
<body>
<div class="content">
    <div class="header">
        <h1>Branch Records of Sales for <?php echo htmlspecialchars($branch_name); ?></h1>
    </div>

    <form id="selectionForm">
        <label for="options">Choose a branch:</label>
        <select name="option" id="options" onchange="redirectToPage()">
            <option value="" disabled>Select an option</option>
            <option value="Branch 1" <?php echo $branch_name == 'Branch 1' ? 'selected' : ''; ?>>Branch 1</option>
            <option value="Branch 2" <?php echo $branch_name == 'Branch 2' ? 'selected' : ''; ?>>Branch 2</option>
            <option value="Branch 3" <?php echo $branch_name == 'Branch 3' ? 'selected' : ''; ?>>Branch 3</option>
            <option value="Branch 4" <?php echo $branch_name == 'Branch 4' ? 'selected' : ''; ?>>Branch 4</option>
            <option value="Branch 5" <?php echo $branch_name == 'Branch 5' ? 'selected' : ''; ?>>Branch 5</option>
        </select>
    </form>

    <div class="records_data">
        <h1>Completed Orders</h1>
        <?php if ($delete_message): ?>
            <script>showPopup("<?php echo htmlspecialchars($delete_message); ?>");</script>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Total Price</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($receipts)): ?>
                    <tr>
                        <td colspan="5">No receipts found for this branch.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($receipts as $receipt): ?>
                        <tr>
                            <td><?php echo $receipt['id']; ?></td>
                            <td><?php echo htmlspecialchars($receipt['product_name']); ?></td>
                            <td><?php echo number_format($receipt['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($receipt['created_at']); ?></td>
                            <td>
                                <button class="print-button" onclick="printReceipt(<?php echo $receipt['id']; ?>)">Print</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="receipt_id" value="<?php echo $receipt['id']; ?>">
                                    <button type="submit" name="delete_receipt" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Popup Message -->
<div class="overlay"></div>
<div class="popup">
    <p id="popupMessage"></p>
    <button onclick="closePopup()">Close</button>
</div>

</body>
</html>