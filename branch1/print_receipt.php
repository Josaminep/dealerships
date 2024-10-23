<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 1) {
    // Redirect or handle unauthorized access
    exit("Unauthorized access.");
}

// Fetch sales data for branch 1
$stmt = $db->prepare("SELECT id, product_name, price, sale_date, branch FROM receipts WHERE branch = ?");
$stmt->execute(['Branch 1']);
$sales = $stmt->fetchAll();

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

    <h1>Completed Order</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
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
                <td colspan="6" style="text-align: center;">No sales records found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($sales as $sale): ?>
                <tr id="row_<?php echo htmlspecialchars($sale['id']); ?>">
                    <td><?php echo htmlspecialchars($sale['id']); ?></td>
                    <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                    <td>Php <?php echo number_format($sale['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                    <td><?php echo htmlspecialchars($sale['branch']); ?></td>
                    <td>
                        <button type="button" style="background-color: green; color: white;" class="accept-button" onclick="printReceipt(<?php echo htmlspecialchars($sale['id']); ?>)">Print Receipt</button>
                        <button type="button" style="background-color: red; color: white;" class="done-button" onclick="markAsDone(<?php echo htmlspecialchars($sale['id']); ?>)">Done</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
function printReceipt(saleId) {
    const sale = <?php echo json_encode($sales); ?>.find(s => s.id === saleId);
    if (sale) {
        const receipt = `
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        width: 250px;
                        margin: auto;
                        border: 1px solid #000;
                        padding: 10px;
                        text-align: center;
                    }
                    .shop-name {
                        font-weight: bold;
                    }
                    .total {
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <p class='shop-name'>Ever Sure</p>
                <h4><strong>Sale Date:</strong> ${sale.sale_date}</h4>
                <p><strong>Product Name:</strong> ${sale.product_name}</p>
                <p><strong>Price:</strong> Php ${parseFloat(sale.price).toFixed(2)}</p>
                <p><strong>Branch:</strong> ${sale.branch}</p>
                <p class='total'>Thank you for your purchase!</p>
            </body>
        </html>`;

        const newWindow = window.open('', '', 'width=1000,height=600');
        newWindow.document.write(receipt);
        newWindow.document.close();
        newWindow.print();
        newWindow.close();
    } else {
        alert('Receipt not found.');
    }
}

function markAsDone(saleId) {
    if (confirm("Are you sure you want to mark this order as done?")) {
        // AJAX request to update the status in the database
        fetch('update_sale_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: saleId, status: 'done' })
        })
        .then(response => {
            if (response.ok) {
                // Remove the row from the table
                const row = document.getElementById(`row_${saleId}`);
                if (row) {
                    row.remove();
                }
            } else {
                alert('Failed to update the sale status. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    }
}
</script>
</div>
</body>
</html>
