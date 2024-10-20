<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 3) {
   
}
// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $brand = $_POST['brand'];
    $stock = $_POST['stock'];

    $stmt = $db->prepare("INSERT INTO products3 (product_name3, price3, quantity3, brand3, stock3) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$product_name, $price, $quantity, $brand, $stock]);

    echo "Product added successfully!";
}

// Handle adding stock to an existing product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_stock'])) {
    $product_id = $_POST['product_id'];
    $additional_stock = $_POST['additional_stock'];

    $stmt = $db->prepare("UPDATE products3 SET stock3 = stock3 + ? WHERE id3 = ?");
    $stmt->execute([$additional_stock, $product_id]);

    echo "Stock added successfully!";
}

// Handle modifying product price
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modify_price'])) {
    $product_id = $_POST['product_id'];
    $new_price = $_POST['new_price'];

    $stmt = $db->prepare("UPDATE products3 SET price3 = ? WHERE id3 = ?");
    $stmt->execute([$new_price, $product_id]);

    echo "Price updated successfully!";
}

// Fetch all products for the stock addition and price modification
$products = $db->query("SELECT * FROM products3")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Manage Products</title>
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
</head>
<body>
    <div><br><br>
        <a href="./dashboard3.php" class="go-back-button">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>

    <h1>Manage Products</h1>

    <!-- Add New Product Form -->
    <div class="form-container">
        <h1>Add New Product</h1>
        <form method="POST">
            <input type="text" name="product_name" placeholder="Product Name" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="text" name="brand" placeholder="Brand" required>
            <input type="number" name="stock" placeholder="Stock" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>

    <!-- Add Stock to Existing Product Form -->
    <div class="form-container">
        <h1>Add Stock to Existing Product</h1>
        <form method="POST">
            <select name="product_id" required>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id3']; ?>">
                        <?php echo $product['product_name3'] . " (Current Stock: " . $product['stock3'] . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="additional_stock" placeholder="Additional Stock" required>
            <button type="submit" name="add_stock">Add Stock</button>
        </form>
    </div>

    <!-- Modify Product Price Form -->
    <div class="form-container">
        <h1>Modify Product Price</h1>
        <form method="POST">
            <select name="product_id" required>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id3']; ?>">
                        <?php echo $product['product_name3'] . " (Current Price: Php" . number_format($product['price3'], 2) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="new_price" placeholder="New Price" required>
            <button type="submit" name="modify_price">Modify Price</button>
        </form>
    </div>

</body>
</html>
