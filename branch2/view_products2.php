<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 2) {
   
}

// Handle delete request
if (isset($_POST['delete_product'])) {
    $productId = $_POST['product_id'];
    $db->prepare("DELETE FROM products2 WHERE id2 = ?")->execute([$productId]);
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
    exit();
}

// Handle search request
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Fetch products based on search query
if ($search) {
    $stmt = $db->prepare("SELECT * FROM products2 WHERE product_name2 LIKE ? OR brand2 LIKE ?");
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $products = $stmt->fetchAll();
} else {
    $products = $db->query("SELECT * FROM products2")->fetchAll();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Available Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightgrey;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: black;
            text-align: center;
        }

        /* Search Form */
        form {
            text-align: center;
            margin-bottom: 30px;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"], button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button {
            background-color: #337ab7;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #4cae4c;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
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

        /* Low Stock Styling */
        .low-stock {
            color: red;
            font-weight: bold;
        }

        .go-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #337ab7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
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
</head>
<body><div><br><br>
<a href="./dashboard2.php" class="go-back-button">
        <i class="fas fa-arrow-left"></i> Go Back
    </a>
    </div>
    <h1>Available Products</h1>

    <!-- Search Form -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search by product name or Description" value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Search">
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><button type="button">Refresh</button></a>
    </form>

    <!-- Products Table -->
    <table>
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Description</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $product): ?>
    <tr>
        <td><?php echo htmlspecialchars($product['product_name2']); ?></td>
        <td>Php <?php echo number_format($product['price2'], 2); ?></td>
        <td><?php echo htmlspecialchars($product['quantity2']); ?></td>
        <td><?php echo htmlspecialchars($product['brand2']); ?></td>
        <td class="<?php echo $product['stock2'] < 10 ? 'low-stock' : ''; ?>">
            <?php echo htmlspecialchars($product['stock2']); ?> <?php echo $product['stock2'] < 10 ? '(Low Stock)' : ''; ?>
        </td>
        <td>
            <form method="post" action="">
                <input type="hidden" name="product_id" value="<?php echo $product['id2']; ?>">
                <input type="submit" name="delete_product" value="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
            </form>
        </td>
    </tr>
<?php endforeach; ?>

    </table>
    


</body>
</html>

