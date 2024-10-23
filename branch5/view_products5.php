<?php
session_start();
require '../db.php';

if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 5) {
    header('Location: login.php'); 
    exit;
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

if ($search) {
    if (strtolower($search) == 'lowstocks') {
        $stmt = $db->prepare("SELECT * FROM products WHERE stock < 10");
        $stmt->execute();
    } else {
        $stmt = $db->prepare("SELECT * FROM products WHERE product_name LIKE ? OR brand LIKE ?");
        $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    }
    $products = $stmt->fetchAll();
} else {
    $products = $db->query("SELECT * FROM products")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Available Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightgrey;
            margin: 0;
            display: flex; 
        }
        .main-content {
            flex-grow: 1; 
            padding: 20px;
            margin-left: 250px; 
            border: 3px solid black;
        }

        h1 {
            color: black;
            text-align: center;
        }
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
        table {
            width: 95%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-left: 45px;
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
            margin-right: 8px;
        }
        .go-back-button:hover {
            background-color: #4cae4c;
        }
        .no-products {
            text-align: center;
            color: #888;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="main-content">
        <div>
            <a href="dashboard5.php" class="go-back-button">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>
        <h1>Available Products</h1>

        <form method="GET">
            <input type="text" name="search" placeholder="Search by product name, description or type 'Low Stocks'" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Search">
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><button type="button">Refresh</button></a>
        </form>

        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Description</th>
                <th>Stock</th>
            </tr>
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td>Php <?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['brand']); ?></td>
                    <td class="<?php echo $product['stock'] < 10 ? 'low-stock' : ''; ?>">
                        <?php echo htmlspecialchars($product['stock']); ?> <?php echo $product['stock'] < 10 ? '(Low Stock)' : ''; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-products">No products found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
