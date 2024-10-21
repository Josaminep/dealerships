<?php
session_start();

include 'sidebar.php';

require 'db.php'; // Include your database connection file


// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


// Handle password change for staff members
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $staff_id = $_POST['staff_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->bindParam(':password', $new_password);
    $stmt->bindParam(':id', $staff_id);
    $stmt->execute();

    $success = "Password changed successfully!";
}

// Fetch staff members
$stmt = $db->prepare("SELECT * FROM users WHERE role = 'staff'");
$stmt->execute();
$staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle password change for admin and staff
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_to_update = $_POST['user_to_update'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update the selected user's password
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE username = :username");
    $stmt->bindParam(':password', $new_password);
    $stmt->bindParam(':username', $user_to_update);

    if ($stmt->execute()) {
        $message = "Password updated successfully for $user_to_update.";
    } else {
        $message = "Error updating password.";
    }
}

// Fetch staff usernames for the form
$stmt = $db->prepare("SELECT username FROM users WHERE role != 'admin'");
$stmt->execute();
$staff_users = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Handle receipt deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_receipt'])) {
    $receipt_id = $_POST['receipt_id'];
    $delete_query = $db->prepare("DELETE FROM receipts WHERE id = ?");
    $delete_query->execute([$receipt_id]);

    // Log the activity
    log_activity($db, 'Receipt Deleted', "Receipt with ID $receipt_id has been deleted.");
    echo "Receipt deleted successfully.";
}

// Function to log activities
function log_activity($db, $activity_type, $details) {
    $query = $db->prepare("INSERT INTO activity_log (activity_type, details) VALUES (?, ?)");
    $query->execute([$activity_type, $details]);
}

// Fetch all receipts
$receipts = $db->query("SELECT r.id, r.receipt_details, r.created_at, s.total_price, p.product_name 
                        FROM receipts r
                        JOIN sales s ON r.sale_id = s.id
                        JOIN products p ON s.product_id = p.id
                        ORDER BY r.created_at DESC")->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAIN BRANCH RECORDS</title>

    <style>
 /* Global styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa; /* Light gray background for the body */
    color: #343a40;
    margin: 0;
    padding: 0;
}

/* Content area styling */
.content {
    margin-left: 240px; /* Space for sidebar */
    padding: 20px;
    height: 100vh;
    box-sizing: border-box;
}

/* Header styles */
.header {
    background-color: #003366; /* Dark blue background */
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
}

.header h1 {
    font-size: 28px;
    color: white; /* White text for better contrast */
    margin: 0;
}

/* Scrollable section for receipt output */
.records_data {
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    height: 600px; /* Fixed height for scrollable area */
    overflow-y: auto; /* Enable vertical scroll */
    border: 2px solid black; /* Optional border for clarity */
    padding: 10px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
}

.records_data h1 {
    text-align: center;
    margin-bottom: 20px;
}

/* Table styles */
table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Table shadow */
    border-radius: 10px;
    overflow: hidden;
}

th, td {
    padding: 15px;
    border: 1px solid #dee2e6;
    text-align: left;
    font-size: 16px;
    color: #495057;
}

th {
    background-color: #007bff; /* Table header color */
    color: white;
    text-transform: uppercase;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f9f9f9; /* Zebra striping for rows */
}

tr:hover {
    background-color: #f1f1f1; /* Row hover effect */
}

/* Button styles */
.print-button, .delete-button {
    margin-left: 10px;
    padding: 10px 20px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    color: white;
    transition: background-color 0.3s ease;
}

.print-button {
    background-color: #4CAF50; /* Green */
}

.delete-button {
    background-color: #f44336; /* Red */
}

.print-button:hover {
    background-color: #45a049; /* Darker green on hover */
}

.delete-button:hover {
    background-color: #d32f2f; /* Darker red on hover */
}

/* Alert box styling */
#alert {
    display: none;
    padding: 15px;
    background-color: #f39c12; /* Alert color */
    color: white;
    font-weight: bold;
    border-radius: 5px;
    text-align: center;
    max-width: 600px;
    margin: 10px auto;
}

/* Responsive layout for form */
.form-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin: 20px;
    max-width: 600px;
    width: 100%;
}

/* Additional form styles */
.form-container {
    background-color: #ffffff; /* White background for the form */
    border-radius: 10px; /* Rounded corners */
    padding: 20px; /* Padding around the content */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    max-width: 400px; /* Max width for the form */
    margin: 0 auto; /* Center the form */
}

/* Label styles */
.form-container label {
    font-size: 18px; /* Slightly larger font for labels */
    color: #343a40; /* Dark text for better readability */
    margin-bottom: 10px; /* Space below the label */
    display: block; /* Block display for label */
}

/* Select box styles */
.form-container select {
    padding: 10px; /* Padding inside the select box */
    border-radius: 5px; /* Rounded corners */
    border: 1px solid #ced4da; /* Light border */
    margin-bottom: 20px; /* Space below the select box */
    width: 100%; /* Full width of the form */
    font-size: 16px; /* Font size for options */
}

/* Submit button styles */
.submit-button {
    padding: 12px 20px; /* Padding for the button */
    background-color: #007bff; /* Primary button color */
    color: white; /* White text */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    font-size: 16px; /* Font size */
    cursor: pointer; /* Pointer on hover */
    transition: background-color 0.3s ease; /* Smooth transition */
}

.submit-button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

/* Go Back Button */
.go-back {
    text-align: center;
    margin-top: 20px;
    text-decoration: none;
    padding: 10px 20px;
    background-color: #007bff; /* Blue */
    color: white;
    border-radius: 5px;
}

.go-back:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.go-back-button {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    background-color: #5cb85c;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.go-back-button i {
    margin-right: 8px; /* Space between the icon and text */
}

.go-back-button:hover {
    background-color: #4cae4c; /* Darker green on hover */
}

/* Adjust layout for smaller screens */
@media (max-width: 768px) {
    .content {
        margin-left: 0;
    }

    table {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .form-container {
        width: 100%;
    }

    table th, table td {
        padding: 10px 5px;
    }
}
/* Form styles */
#selectionForm {
    margin: 20px; /* Margin around the form */
    float: left; /* Align form to the left */
}

/* Label styles */
#selectionForm label {
    font-size: 18px; /* Slightly larger font for labels */
    color: #343a40; /* Dark text for better readability */
    margin-bottom: 10px; /* Space below the label */
    display: block; /* Block display for label */
}

/* Select box styles */
#selectionForm select {
    padding: 10px; /* Padding inside the select box */
    border-radius: 5px; /* Rounded corners */
    border: 1px solid #ced4da; /* Light border */
    margin-top: 5px; /* Space above the select box */
    margin-bottom: 20px; /* Space below the select box */
    width: 100%; /* Full width of the form */
    font-size: 16px; /* Font size for options */
    appearance: none; /* Remove default styling */
    background-color: #f8f9fa; /* Light background color */
}

/* Placeholder styling */
#selectionForm option:disabled {
    color: #999; /* Gray color for the placeholder */
}

</style>

    <script>
        function printReceipt(receiptId) {
            var printWindow = window.open('print_receipt.php?id=' + receiptId, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        }

        // SELECTION OF BRANCH
        function redirectToPage() {
            // Get the selected option value
            var selectedOption = document.getElementById("options").value;

            // Redirect based on the selected option
            if (selectedOption === "Option 1") {
                window.location.href = "branch_records_admin.php";
            } else if (selectedOption === "Option 2") {
                window.location.href = "branch2_records.php";
            } else if (selectedOption === "Option 3") {
                window.location.href = "branch3_records.php";
            } else if (selectedOption === "Option 4") {
                window.location.href = "branch4_records.php";
            } else if (selectedOption === "Option 5") {
                window.location.href = "branch5_records.php";
            }
        }
        
    </script>
</head>

<body>
<div class="content">
    <div class="header">
        
        <h1>BRANCH RECORDS OF SALES FOR BRANCH 1</h1>
    </div>
<div>

<form id="selectionForm">
        <label for="options">Choose an option:</label>
        <select name="option" id="options" onchange="redirectToPage()">
            <option value="" disabled selected>Select an option</option> <!-- Placeholder -->
            <option value="Option 1">BRANCH 1</option>
            <option value="Option 2">BRANCH 2</option>
            <option value="Option 3">BRANCH 3</option>
            <option value="Option 4">BRANCH 4</option>
            <option value="Option 5">BRANCH 5</option>
        </select>
    </form>
</div>

    <div class="main-content">
        <div class="records_data">
            <h1>Records of sales</h1>
            <table>
                <tr>
                    <th>Receipt ID</th>
                    <th>Product</th>
                    <th>Receipt Details</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($receipts as $receipt): ?>
                <tr>
                    <td><?php echo htmlspecialchars($receipt['id']); ?></td>
                    <td><?php echo htmlspecialchars($receipt['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($receipt['receipt_details']); ?></td>
                    <td><?php echo htmlspecialchars($receipt['created_at']); ?></td>
                    <td>
                        <button class="print-button" style="margin-bottom: 5px;" onclick="printReceipt(<?php echo $receipt['id']; ?>)">Print</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="receipt_id" value="<?php echo $receipt['id']; ?>">
                            <button type="submit" name="delete_receipt" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>



</body>
</html>
