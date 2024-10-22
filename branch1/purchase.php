<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 1) {
    // Redirect or handle unauthorized access
    exit("Unauthorized access.");
}

// Fetch sales data for branch 1
$stmt = $db->prepare("SELECT id, product_id, product_name, price, sale_date, branch FROM sales WHERE branch = ?");
$stmt->execute(['Branch 1']);
$sales = $stmt->fetchAll();

// Prepare success message if it exists
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Sales Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 250px;
            border: 3px solid black;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #eaeaea;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
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
            margin-bottom: 20px;
        }
        .go-back-button i {
            margin-right: 8px;
        }
        .go-back-button:hover {
            background-color: #4cae4c;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <?php include 'sidebar.php'; // Sidebar content goes here ?>
</div>

<div class="content">
    <div>
        <a href="dashboard.php" class="go-back-button">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>

    <h1>Pending Order</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Sale Date</th>
                <th>Branch</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sales)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No sales records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sale['id']); ?></td>
                        <td><?php echo htmlspecialchars($sale['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                        <td>Php <?php echo number_format($sale['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                        <td><?php echo htmlspecialchars($sale['branch']); ?></td>
                        <td>
                            <form action="accept_order.php" method="POST" style="display: inline;">
                                <input type="hidden" name="sale_id" value="<?php echo htmlspecialchars($sale['id']); ?>">
                                <button type="submit" style="background-color: green; color: white;" class="accept-button">Accept</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal Structure -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('successModal').style.display='none'">&times;</span>
            <p><?php echo $successMessage; ?></p>
        </div>
    </div>

    <script>
        // Show modal if there is a success message
        window.onload = function() {
            <?php if ($successMessage): ?>
                document.getElementById('successModal').style.display = 'block';
            <?php endif; ?>
        }
    </script>
</div>

</body>
</html>
