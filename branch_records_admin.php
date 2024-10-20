<?php
session_start();
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
        
  
      

/* Scrollable section for receipt output */
.records_data {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    height: 600px; /* Fixed height for scrollable area */
    overflow-y: scroll; /* Enable vertical scroll */
    border: 2px solid black; /* Optional border for clarity */
    padding: 10px;
    background-color: white;
}

.records_data h1 {
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ccc;
    font-size: 18px;
}

th {
    background-color: #e0e0e0; /* Slightly darker grey for header */
}

tr:nth-child(even) {
    background-color: #f9f9f9; /* Zebra striping for readability */
}

.print-button, .delete-button {
    margin-left: 10px;
    padding: 5px 10px;
    cursor: pointer;
    border: none;
    color: white;
    border-radius: 4px;
}

.print-button {
    background-color: #4CAF50; /* Green */
}

.delete-button {
    background-color: #f44336; /* Red */
}

/* Sidebar */
.sidebar {
    width: 20%;
    background-color: black;
    height: 100vh;
    position: fixed;
    color: white;
}

.sidebar h2 {
    font-size: 40px;
    color: white;
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
    padding-top: 40px;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
   
}

.sidebar ul li {
    margin-bottom: 15px;
    margin-top: 20px;
}

/* Button Link Styling */
.sidebar ul li a {
    display: block;
    padding: 20px;
    background-color: #5cb85c;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 5px;
    font-size: 16px;
    margin-left: 20px;
    margin-right: 20px;
    
}

.sidebar ul li a:hover {
    background-color: burlywood;
}

/* Special styling for logout button */
.sidebar ul li a[href="logout.php"] {
    background-color: #d9534f;
}

.sidebar ul li a[href="logout.php"]:hover {
    background-color: burlywood;
}
.content {
    margin-left: 20%;
    padding: 20px;
}

.header {
    text-align: center;
    padding: 20px 0;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.header h1 {
    color: black;
    font-size: 24px;
    border: 3px solid #a76df0;
    display: inline-block;
    padding: 5px 15px;
}

.main-content {
    background-color: #e0e0e0;
    min-height: 50vh;
    border: 3px solid black;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

/* Ensure scroll inside content */
.main-content::-webkit-scrollbar {
    width: 8px;
}

.main-content::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.main-content::-webkit-scrollbar-thumb {
    background: #888;
}

.main-content::-webkit-scrollbar-thumb:hover {
    background: #555;
}


* {
margin: 0;
padding: 0;
box-sizing: border-box;
}

body {
font-family: Arial, sans-serif;
background-color: lightgrey;
}

.sidebar {
width: 100%;
max-width: 250px;
background-color: black;
height: 100vh;
position: fixed;
color: white;
overflow-y: auto;
}

.sidebar h2 {
font-size: 24px;
color: white;
text-align: center;
margin-bottom: 20px;
padding-bottom: 10px;
border-bottom: 1px solid #ddd;
padding-top: 40px;
}

.sidebar ul {
list-style-type: none;
padding: 0;
}

.sidebar ul li {
margin-bottom: 15px;
margin-top: 20px;
}

/* Button Link Styling */
.sidebar ul li a {
display: block;
padding: 15px;
background-color: #5cb85c;
color: white;
text-decoration: none;
text-align: center;
border-radius: 5px;
font-size: 16px;
margin-left: 10px;
margin-right: 10px;
}

.sidebar ul li a:hover {
background-color: burlywood;
}

/* Special styling for logout button */
.sidebar ul li a[href="logout.php"] {
background-color: #d9534f;
}

.sidebar ul li a[href="logout.php"]:hover {
background-color: burlywood;
}

.content {
margin-left: 260px;
padding: 20px;
flex: 1;
}

.header {
text-align: center;
padding: 20px 0;
background-color: #fff;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.header h1 {
color: black;
font-size: 24px;
border: 3px solid #a76df0;
display: inline-block;
padding: 5px 15px;
}

.main-content {
background-color: #e0e0e0;
border: 3px solid black;
display: flex;
justify-content: center;
align-items: center;
font-size: 24px;
color: #666;
padding: 20px;
}

/* Responsive grid layout for form */
.form-container {
display: flex;
flex-direction: column;
gap: 15px;
margin: 20px;
max-width: 600px;
width: 100%;
}

.form-container input,
.form-container select,
.form-container button {
padding: 10px;
border-radius: 5px;
border: 1px solid #ccc;
font-size: 16px;
width: 100%;
}

/* Table styles for responsiveness */
table {
width: 100%;
border-collapse: collapse;
margin-top: 20px;
background-color: white;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
overflow-x: auto;
}

th, td {
padding: 10px;
text-align: left;
border-bottom: 1px solid #ddd;
}

th {
background-color: #f8f8f8;
color: #333;
font-weight: bold;
}

tr:hover {
background-color: #f1f1f1;
}

td:last-child {
text-align: center;
}

/* Flexbox layout for the reset buttons */
.reset {
display: flex;
justify-content: center;
gap: 15px;
flex-wrap: wrap;
margin-top: 20px;
}

.reset-button {
padding: 10px 15px;
border: none;
border-radius: 5px;
color: white;
cursor: pointer;
transition: background-color 0.3s;
}

.reset-button:nth-child(1) {
background-color: #4CAF50;
}

.reset-button:nth-child(2) {
background-color: #2196F3;
}

.reset-button:nth-child(3) {
background-color: #FF9800;
}

.reset-button:nth-child(4) {
background-color: #f44336;
}

.reset-button:hover {
opacity: 0.8;
}

/* Responsive layout for branch container */
.branch1_add_stocks {
margin: 20px auto;
width: 90%;
max-width: 600px;
}

.manage_products {
margin: 20px;
}

/* Adjust layout for smaller screens */
@media (max-width: 768px) {
.sidebar {
    position: static;
    width: 100%;
    height: auto;
}

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

.reset {
    flex-direction: column;
    gap: 10px;
}
}



h1 {
    color: black;
    text-align: center;
    margin-bottom: 20px;
}

/* Form Container */
.form-container {
    max-width: 600px;
    margin: 0 auto;
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

form {
    display: flex;
    flex-direction: column;
}

input[type="text"], 
input[type="number"], 
select {
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    width: 100%;
}

input[type="submit"], 
button {
    padding: 10px;
    background-color: #5cb85c;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

button {
    background-color: #337ab7;
}

input[type="submit"]:hover, 
button:hover {
    background-color: #4cae4c;
}

/* Go Back Button */
.go-back {
    text-align: center;
    margin-top: 20px;
    text-decoration: none;
    padding: 10px 20px;
    background-color: #337ab7;
    color: white;
    border-radius: 5px;
}

.go-back:hover {
    background-color: #286090;
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
    background-color: #4cae4c;
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

<div class="sidebar">
    <h2>Dashboard</h2>
    <ul>
        <li><a id="viewProductsBtn" href='./admin_dashboard_final.php'>DASHBOARD</a></li>
        <li><a id="addProductBtn" href='./add_stocks_admin.php'>ADD STOCKS PER BRANCH</a></li>
        <li><a id="addProductBtn" href='./add_product5.php'>PRODUCTS</a></li>
        <li><a id="addProductBtn" href='./branch_records_admin.php'>COMPLETED ORDERS</a></li>
        <li><a href="../logout.php" >Logout</a></li>
    </ul>
</div>

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
                        <button class="print-button" onclick="printReceipt(<?php echo $receipt['id']; ?>)">Print</button>
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
