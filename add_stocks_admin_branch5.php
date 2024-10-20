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

// Initialize message variable
$message = "";

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Adding product logic
        $product_name5 = $_POST['product_name5'];
        $price5 = $_POST['price5'];
        $quantity5 = $_POST['quantity5'];
        $brand5 = $_POST['brand5'];
        $stock5 = $_POST['stock5'];

        $stmt = $db->prepare("INSERT INTO products5 (product_name5, price5, quantity5, brand5, stock5) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$product_name5, $price5, $quantity5, $brand5, $stock5])) {
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
        $product_id5 = $_POST['product_id5'];
        $additional_stock5 = $_POST['additional_stock5'];

        $stmt = $db->prepare("UPDATE products5 SET stock5 = stock5 + ? WHERE id5 = ?");
        if ($stmt->execute([$additional_stock5, $product_id5])) {
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
        $product_id5 = $_POST['product_id5'];
        $new_price5 = $_POST['new_price5'];

        $stmt = $db->prepare("UPDATE products5 SET price5 = ? WHERE id5 = ?");
        if ($stmt->execute([$new_price5, $product_id5])) {
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
$products5 = $db->query("SELECT * FROM products5")->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if (isset($_POST['delete_product'])) {
    $product_id5 = $_POST['product_id5'];
    $db->prepare("DELETE FROM products5 WHERE id5 = ?")->execute([$product_id5]);
    
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
        $stmt = $db->prepare("SELECT * FROM products5 WHERE stock5 < 10");
        $stmt->execute();
    } else {
        // Fetch products based on product name or description (brand)
        $stmt = $db->prepare("SELECT * FROM products5 WHERE product_name5 LIKE ? OR brand5 LIKE ?");
        $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    }
    $products5 = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch all products if no search query
    $products5 = $db->query("SELECT * FROM products5")->fetchAll(PDO::FETCH_ASSOC);
}
?>


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
    .branch3_add_stocks {
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
<div class="sidebar">
    <h2>Dashboard</h2>
    <ul>
        <li><a href='/admin_dashboard_final.php'>DASHBOARD</a></li>
        <li><a href='./add_stocks_admin.php'>ADD STOCKS PER BRANCH</a></li>
        <li><a href='./add_product5.php'>PRODUCTS</a></li> <!-- Changed to `add_product5.php` -->
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
            <h3>Branch 5</h3> <!-- Updated to reflect Branch 5 -->
            <div class="branch5_add_stocks">
                <!-- Add New Product Form -->
                <div class="form-container">
                    <h3>Add New Product in Branch 5</h3> <!-- Changed to Branch 5 -->
                    <form method="POST" action="">
                        <input type="text" name="product_name5" placeholder="Product Name" required> <!-- Changed to `product_name5` -->
                        <input type="number" step="0.01" name="price5" placeholder="Price" required> <!-- Changed to `price5` -->
                        <input type="number" name="quantity5" placeholder="Quantity" required> <!-- Changed to `quantity5` -->
                        <input type="text" name="brand5" placeholder="Brand" required> <!-- Changed to `brand5` -->
                        <input type="number" name="stock5" placeholder="Stock" required> <!-- Changed to `stock5` -->
                        <button type="submit" name="add_product">Add Product</button>
                    </form>
                </div>

                <form method="POST" class="form-container">
                    <select name="product_id5" required> <!-- Changed to `product_id5` -->
                        <h3>Add Stocks</h3>
                        <option value="">Select Product</option>
                        <?php foreach ($products5 as $product): ?> <!-- Changed to `products5` -->
                            <option value="<?php echo $product['id5']; ?>"><?php echo $product['product_name5']; ?></option> <!-- Updated for `products5` -->
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="additional_stock5" placeholder="Additional Stock" required> <!-- Changed to `additional_stock5` -->
                    <input type="submit" name="add_stock" value="Add Stock">
                </form>

                <form method="POST" class="form-container">
                    <h3>Modify Price</h3>
                    <select name="product_id5" required> <!-- Changed to `product_id5` -->
                        <option value="">Select Product</option>
                        <?php foreach ($products5 as $product): ?> <!-- Changed to `products5` -->
                            <option value="<?php echo $product['id5']; ?>"><?php echo $product['product_name5']; ?></option> <!-- Updated for `products5` -->
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="new_price5" placeholder="New Price" required> <!-- Changed to `new_price5` -->
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
        <?php foreach ($products5 as $product): ?> <!-- Changed to `products5` -->
            <tr>
                <td><?php echo htmlspecialchars($product['product_name5']); ?></td> <!-- Changed to `product_name5` -->
                <td>Php <?php echo number_format($product['price5'], 2); ?></td> <!-- Changed to `price5` -->
                <td><?php echo htmlspecialchars($product['quantity5']); ?></td> <!-- Changed to `quantity5` -->
                <td><?php echo htmlspecialchars($product['brand5']); ?></td> <!-- Changed to `brand5` -->
                <td><?php echo htmlspecialchars($product['stock5']); ?></td> <!-- Changed to `stock5` -->
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="product_id5" value="<?php echo $product['id5']; ?>"> <!-- Changed to `product_id5` -->
                        <input type="submit" name="delete_product" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
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
