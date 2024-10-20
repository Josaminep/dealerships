<?php
session_start();
require 'db.php'; // Include your database connection file

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}




// Fetch staff usernames for the form
$stmt = $db->prepare("SELECT username FROM users WHERE role != 'admin'");
$stmt->execute();
$staff_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize message variable
$message = "";

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Adding product logic
        $product_name2 = $_POST['product_name2'];
        $price2 = $_POST['price2'];
        $quantity2 = $_POST['quantity2'];
        $brand2 = $_POST['brand2'];
        $stock2 = $_POST['stock2'];

        $stmt = $db->prepare("INSERT INTO products2 (product_name2, price2, quantity2, brand2, stock2) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$product_name2, $price2, $quantity2, $brand2, $stock2])) {
            $message = "Product added successfully!";
        } else {
            $message = "Error adding product.";
        }

        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['add_stock'])) {
        // Adding stock logic
        $product_id2 = $_POST['product_id2'];
        $additional_stock2 = $_POST['additional_stock2'];

        $stmt = $db->prepare("UPDATE products2 SET stock2 = stock2 + ? WHERE id2 = ?");
        if ($stmt->execute([$additional_stock2, $product_id2])) {
            $message = "Stock added successfully!";
        } else {
            $message = "Error adding stock.";
        }

        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['modify_price'])) {
        // Modifying price logic
        $product_id2 = $_POST['product_id2'];
        $new_price2 = $_POST['new_price2'];

        $stmt = $db->prepare("UPDATE products2 SET price2 = ? WHERE id2 = ?");
        if ($stmt->execute([$new_price2, $product_id2])) {
            $message = "Price updated successfully!";
        } else {
            $message = "Error updating price.";
        }

        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch all products for stock addition and price modification
$products2 = $db->query("SELECT * FROM products2")->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if (isset($_POST['delete_product'])) {
    $product_id2 = $_POST['product_id2'];
    $db->prepare("DELETE FROM products2 WHERE id2 = ?")->execute([$product_id2]);
    
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle search request
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Fetch products based on search query
if ($search) {
    if (strtolower($search) == 'lowstocks') {
        // Fetch products with low stock (less than 10)
        $stmt = $db->prepare("SELECT * FROM products2 WHERE stock2 < 10");
        $stmt->execute();
    } else {
        // Fetch products based on product name or description (brand)
        $stmt = $db->prepare("SELECT * FROM products2 WHERE product_name2 LIKE ? OR brand2 LIKE ?");
        $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    }
    $products2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch all products if no search query
    $products2 = $db->query("SELECT * FROM products2")->fetchAll(PDO::FETCH_ASSOC);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>MAIN BRANCH ADMIN</title>
    



    <style>
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

        .view_stocks {
    background-color: #e0e0e0; /* Same background color as .main-content */
    border: 3px solid black;   /* Same border as .main-content */
    padding: 20px;             /* Same padding as .main-content */
    margin: 20px auto;         /* Center the div horizontally */
    width: 90%;                /* Adjust width to your desired percentage */
    max-width: 1300px; 
    margin-left: 400px;         /* Optional: to maintain a maximum width */
}

</style>
<script>
     function redirectToPage() {
            // Get the selected option value
            var selectedOption = document.getElementById("options").value;

            // Redirect based on the selected option
            if (selectedOption === "Option 1") {
                window.location.href = "add_stocks_admin.php";
            } else if (selectedOption === "Option 2") {
                window.location.href = "add_stocks_admin_branch2.php";
            } else if (selectedOption === "Option 3") {
                window.location.href = "add_stocks_admin_branch3.php";
            } else if (selectedOption === "Option 4") {
                window.location.href = "add_stocks_admin_branch4.php";
            } else if (selectedOption === "Option 5") {
                window.location.href = "add_stocks_admin_branch5.php";
            }
        }
</script>

</head>
    <body>

    <div class="sidebar">
    <h2>Dashboard</h2>
    <ul>
        <li><a href='/admin_dashboard_final.php'>DASHBOARD</a></li>
        <li><a href='./add_stocks_admin.php'>ADD STOCKS PER BRANCH</a></li>
        <li><a href='./add_product5.php'>PRODUCTS</a></li>
        <li><a href='./branch_records_admin.php'>COMPLETED ORDERS</a></li>
        <li><a href="./logout.php">Logout</a></li>
    </ul>
</div>




<div class="content">
    <div class="header">
        <h1>MANAGING STOCKS</h1> 
    </div>
    <div>
        <form id="selectionForm">
        <label for="options">Choose Branch:</label>
        <select name="option" id="options" onchange="redirectToPage()">
                <option value="" disabled selected>Select Branch</option> <!-- Placeholder -->
                <option value="Option 1">Branch 1</option>
                <option value="Option 2">Branch 2</option>
                <option value="Option 3">Branch 3</option>
                <option value="Option 4">Branch 4</option>
                <option value="Option 5">Branch 5</option>
            </select>
    </form>
 </div>

     <div class="main-content">
        <div class="manage_products">
    <h3>Branch 2</h3>
    <div class="branch2_add_stocks">
    <!-- Add New Product Form -->
        <div class="form-container">
            <h3>Add New Product in Branch 2</h3>
            <form method="POST" action="">
                <input type="text" name="product_name2" placeholder="Product Name" required>
                <input type="number" step="0.01" name="price2" placeholder="Price" required>
                <input type="number" name="quantity2" placeholder="Quantity" required>
                <input type="text" name="brand2" placeholder="Brand" required>
                <input type="number" name="stock2" placeholder="Stock" required>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>
        
       
        <form method="POST" class="form-container">
        <h3>Add Stocks</h3>
            <select name="product_id2" required>
                <option value="">Select Product</option>
                <?php foreach ($products2 as $product): ?>
                    <option value="<?php echo $product['id2']; ?>"><?php echo $product['product_name2']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="additional_stock2" placeholder="Additional Stock" required>
            <input type="submit" name="add_stock" value="Add Stock">
        </form>

        
        <form method="POST" class="form-container">
        <h>Modify Price</h>
            <select name="product_id2" required>
                <option value="">Select Product</option>
                <?php foreach ($products2 as $product): ?>
                    <option value="<?php echo $product['id2']; ?>"><?php echo $product['product_name2']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="new_price2" placeholder="New Price" required>
            <input type="submit" name="modify_price" value="Modify Price">
        </form>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    

    </div>
</div>

</div>
</div>
<div class="view_stocks">

    
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
    <?php foreach ($products2 as $product): ?>
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


    <script>// Add Product
$("#addProductForm").submit(function (e) {
    e.preventDefault();
    $.ajax({
        url: 'add_stocks_admin_branch2.php', // Adjust path
        type: 'POST',
        data: $(this).serialize() + '&add_product=true', // Adding 'add_product' flag
        success: function (response) {
            $('#addProductResponse').html(response);
            // Optionally, refresh product list here or update the DOM
        },
        error: function () {
            $('#addProductResponse').html('Error adding product');
        }
    });
});

// Add Stock
$("#addStockForm").submit(function (e) { 
    e.preventDefault();
    $.ajax({
        url: 'add_stocks_admin_branch2.php',// Adjust path
        type: 'POST',
        data: $(this).serialize() + '&add_stock=true', // Adding 'add_stock' flag
        success: function (response) {
            $('#addStockResponse').html(response);
            // Optionally, refresh product list or update the DOM
        },
        error: function () {
            $('#addStockResponse').html('Error adding stock');
        }
    });
});

// Modify Price
$("#modifyPriceForm").submit(function (e) {
    e.preventDefault();
    $.ajax({
        url: 'add_stocks_admin_branch2.php', // Adjust path
        type: 'POST',
        data: $(this).serialize() + '&modify_price=true', // Adding 'modify_price' flag
        success: function (response) {
            $('#modifyPriceResponse').html(response);
            // Optionally, refresh product list or update the DOM
        },
        error: function () {
            $('#modifyPriceResponse').html('Error modifying price');
        }
    });
});
</script>
</div>
</body>
</html>