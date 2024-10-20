<?php
session_start();
require '../db.php'; // Include your database connection file

// Check if user is logged in and is staff from branch 1
if (!isset($_SESSION['staff_user_id']) || $_SESSION['staff_role'] !== 'staff' || $_SESSION['staff_branch_id'] !== 4) {
   
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $total_price = 0;
    $sales_data = [];
    $products_info = [];

    foreach ($_POST['product_id'] as $index => $product_id) {
        $quantity_sold = $_POST['quantity_sold'][$index];

        // Fetch product information
        $stmt = $db->prepare("SELECT * FROM products4 WHERE id4 = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $product['stock4'] >= $quantity_sold) {
            // Calculate total price for this product
            $price = $quantity_sold * $product['price4'];
            $total_price += $price;

            // Store sales data for later insertion
            $sales_data[] = [
                'product_id' => $product_id,
                'quantity_sold' => $quantity_sold,
                'total_price' => $price
            ];
            $products_info[] = "{$quantity_sold} {$product['product_name4']} at Php {$product['price4']} each"; // Added price
        } else {
            echo "Insufficient stock for the product: {$product['product_name4']}!";
            exit();
        }
    }

    // Insert sale records into the sales table and collect sale IDs
    $sale_ids = [];
    foreach ($sales_data as $data) {
        $stmt = $db->prepare("INSERT INTO sales4 (product_id4, quantity_sold4, total_price4, sale_date4) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$data['product_id'], $data['quantity_sold'], $data['total_price']]);
        $sale_ids[] = $db->lastInsertId();
    }

    // Insert receipt record with the aggregated total price
    $receipt_details = "Purchased:<br>------------------------------------------<br>" . implode("<br>------------------------------------------<br>", $products_info) . "<br>------------------------------------------<br><br><br> TOTAL PRICE = Php {$total_price}";
    $stmt = $db->prepare("INSERT INTO receipts4 (sale_id4, receipt_details4) VALUES (?, ?)");
    $stmt->execute([$sale_ids[0], $receipt_details]); // Use the first sale ID for the receipt

    // Update product stock
    foreach ($sales_data as $data) {
        $stmt = $db->prepare("UPDATE products4 SET stock4 = stock4 - ? WHERE id4 = ?");
        $stmt->execute([$data['quantity_sold'], $data['product_id']]);
    }

    // Record income in income_records
    $stmt = $db->prepare("INSERT INTO income_records4 (period4, amount4) VALUES ('daily', ?) ON DUPLICATE KEY UPDATE amount4 = amount4 + ?");
    $stmt->execute([$total_price, $total_price]);

    $stmt = $db->prepare("INSERT INTO income_records4 (period4, amount4) VALUES ('weekly', ?) ON DUPLICATE KEY UPDATE amount4 = amount4 + ?");
    $stmt->execute([$total_price, $total_price]);

    $stmt = $db->prepare("INSERT INTO income_records4 (period4, amount4) VALUES ('monthly', ?) ON DUPLICATE KEY UPDATE amount4 = amount4 + ?");
    $stmt->execute([$total_price, $total_price]);

    echo "Purchase successful! Receipt generated.";
}

// Fetch all products for the purchase form
$products = $db->query("SELECT * FROM products4")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Make a Purchase</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightgrey;
            margin: 0;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        h1 {
            text-align: center;
            color: black;
            margin-bottom: 30px;
        }

        /* Form Container */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Label Styles */
        label {
            display: inline-block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
        }

        /* Select and Input Styles */
        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        /* Add/Remove Product Button Styles */
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .remove-btn {
            background-color: #d9534f;
        }

        .remove-btn:hover {
            background-color: #c9302c;
        }

        /* Add Product Button */
        .add-product-btn {
            margin-bottom: 20px;
            background-color: #337ab7;
        }

        .add-product-btn:hover {
            background-color: #286090;
        }

        /* Go Back Button */
        .go-back {
            text-align: center;
            margin-top: 30px;
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
        function addProduct() {
            const productRow = document.createElement('div');
            productRow.innerHTML = `
                <label for="product_id">Select Product:</label>
                <select name="product_id[]" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>">
                            <?php echo $product['product_name'] . " (Php " . number_format($product['price'], 2) . ", Stock: " . $product['stock'] . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="quantity_sold">Quantity:</label>
                <input type="number" name="quantity_sold[]" min="1" required>
                <button type="button" class="remove-btn" onclick="removeProduct(this)">Remove</button>
                <br><br>
            `;
            document.getElementById('products-container').appendChild(productRow);
        }

        function removeProduct(button) {
            const productRow = button.parentNode;
            productRow.parentNode.removeChild(productRow);
        }
    </script>
</head>
<body>
<div><br><br>
<a href="./dashboard4.php" class="go-back-button">
        <i class="fas fa-arrow-left"></i> Go Back
    </a></div>



    <h1>Make a Purchase</h1>

    <div class="form-container">
        <form method="POST">
            <div id="products-container">
                <div>
                    <label for="product_id">Select Product:</label>
                    <select name="product_id[]" required>
    <?php foreach ($products as $product): ?>
        <option value="<?php echo $product['id4']; ?>">
            <?php echo $product['product_name4'] . " (Php " . number_format($product['price4'], 2) . ", Stock: " . $product['stock4'] . ")"; ?>
        </option>
    <?php endforeach; ?>
</select>


                    <label for="quantity_sold">Quantity:</label>
                    <input type="number" name="quantity_sold[]" min="1" required>
                    <br><br>
                </div>
            </div>

            <button type="button" class="add-product-btn" onclick="addProduct()">Add Another Product</button>
            <br><br>
            <button type="submit">Submit Purchase</button>
        </form>
    </div>


</body>
</html>


