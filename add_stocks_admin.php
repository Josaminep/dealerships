<?php
session_start();
require 'db.php'; // Include your database connection file

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize message variable
$message = "";

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Adding product logic
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $brand = $_POST['brand'];
        $stock = $_POST['stock'];

        $stmt = $db->prepare("INSERT INTO products (product_name, price, quantity, brand, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_name, $price, $quantity, $brand, $stock]);

        $message = "Product added successfully!";
        
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['add_stock'])) {
        // Adding stock logic
        $product_id = $_POST['product_id'];
        $additional_stock = $_POST['additional_stock'];

        $stmt = $db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$additional_stock, $product_id]);

        $message = "Stock added successfully!";
        
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['modify_price'])) {
        // Modifying price logic
        $product_id = $_POST['product_id'];
        $new_price = $_POST['new_price'];

        $stmt = $db->prepare("UPDATE products SET price = ? WHERE id = ?");
        $stmt->execute([$new_price, $product_id]);

        $message = "Price updated successfully!";
        
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch all products for stock addition and price modification
$products = $db->query("SELECT * FROM products")->fetchAll();

// Handle delete request
// Handle delete request
if (isset($_POST['delete_product'])) {
    $productId = $_POST['product_id'];

    // First delete any sales associated with the product
    $db->prepare("DELETE FROM sales WHERE product_id = ?")->execute([$productId]);

    // Now delete the product
    $db->prepare("DELETE FROM products WHERE id = ?")->execute([$productId]);
    
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
        $stmt = $db->prepare("SELECT * FROM products WHERE stock < 10");
        $stmt->execute();
    } else {
        // Fetch products based on product name or description (brand)
        $stmt = $db->prepare("SELECT * FROM products WHERE product_name LIKE ? OR brand LIKE ?");
        $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    }
    $products = $stmt->fetchAll();
} else {
    // Fetch all products if no search query
    $products = $db->query("SELECT * FROM products")->fetchAll();
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
    #alert {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
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

</head>
    <body>

    <div class="sidebar">
    <h2>Dashboard</h2>
    <ul>
        <li><a href='./admin_dashboard_final.php'>DASHBOARD</a></li>
        <li><a href='./add_stocks_admin.php'>ADD STOCKS PER BRANCH</a></li>
        <li><a href='./add_product5.php'>PRODUCTS</a></li>
        <li><a href='./branch_records_admin.php'>COMPLETED ORDERS</a></li>
        <li><a href="./logout.php">Logout</a></li>
    </ul>
</div>

<body>
    <div class="content" style="display: flex; flex-direction: column; align-items: center; margin: 0; padding: 8px; border: 1px solid #ccc; border-radius: 10px; background-color: white; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 80%; height: 100vh; box-sizing: border-box; margin-left: 250px">
        <div class="header">
            <h1>MANAGING STOCKS</h1>
        </div>

        <!-- Alert message container -->
        <div id="alert" style="display: none; margin-bottom: 10px; padding: 10px; border: 1px solid #4CAF50; background-color: #dff0d8; color: #3c763d; border-radius: 5px; width: 100%;"></div>

        <form action="" method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column; align-items: stretch; gap: 10px; width: 100%;">
            <input type="file" name="csv_file" accept=".csv" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%;">
            <button type="submit" name="upload" style="padding: 10px 15px; border: none; border-radius: 5px; background-color: #4CAF50; color: white; cursor: pointer; width: 100%;">Upload CSV</button>
            <button type="submit" name="save" <?php if (empty($_SESSION['csv_data'])) { echo 'disabled'; } ?> style="padding: 10px 15px; border: none; border-radius: 5px; background-color: #008CBA; color: white; cursor: pointer; <?php if (empty($_SESSION['csv_data'])) { echo 'opacity: 0.6; cursor: not-allowed;'; } ?> width: 100%;">Save to DB</button>
        </form>

        <!-- Display table header -->
        <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Brand</th>
                <th>Stock</th>
                <th>Categories</th>
            </tr>

            <?php
            // Initialize an array to hold the CSV data
            if (!isset($_SESSION['csv_data'])) {
                $_SESSION['csv_data'] = []; // Store CSV data temporarily in the session
            }

            // Handle CSV upload and display
            if (isset($_POST['upload'])) {
                // Check if a file is uploaded
                if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
                    // Open the CSV file
                    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');

                    // Read the first row (header) and skip it
                    fgetcsv($file);

                    // Fetch data from the CSV file and store it in session for later use
                    $_SESSION['csv_data'] = []; // Clear any previous CSV data
                    while (($row = fgetcsv($file)) !== false) {
                        $_SESSION['csv_data'][] = $row;
                        echo '<tr>';
                        foreach ($row as $data) {
                            echo '<td>' . htmlspecialchars($data) . '</td>';
                        }
                        echo '</tr>';
                    }

                    fclose($file);
                    echo '<script>showAlert("Data loaded successfully! Click \"Save to DB\" to save the data.");</script>';
                } else {
                    echo '<script>showAlert("Error uploading the file. Please try again.");</script>';
                }
            }

            if (!empty($_SESSION['csv_data']) && !isset($_POST['upload'])) {
                foreach ($_SESSION['csv_data'] as $row) {
                    echo '<tr>';
                    foreach ($row as $data) {
                        echo '<td>' . htmlspecialchars($data) . '</td>';
                    }
                    echo '</tr>';
                }
            }

            // Handle saving to the database
            if (isset($_POST['save']) && !empty($_SESSION['csv_data'])) {
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "dealership_shop"; // Replace with your actual database name

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $successful_inserts = 0; // Counter for successful insertions
                $errors = []; // Array to collect errors

                // Insert each row of CSV data into the database
                foreach ($_SESSION['csv_data'] as $row) {
                    $product_name = $conn->real_escape_string($row[0]);
                    $price = $conn->real_escape_string($row[1]);
                    $quantity = $conn->real_escape_string($row[2]);
                    $brand = $conn->real_escape_string($row[3]);
                    $stock = $conn->real_escape_string($row[4]);
                    $categories = $conn->real_escape_string($row[5]);

                    $sql = "INSERT INTO products (product_name, price, quantity, brand, stock, categories)
                            VALUES ('$product_name', '$price', '$quantity', '$brand', '$stock', '$categories')";

                    if ($conn->query($sql) === TRUE) {
                        $successful_inserts++;
                    } else {
                        $errors[] = "Error for product $product_name: " . $conn->error;
                    }
                }

                // Show messages after processing all rows
                if ($successful_inserts > 0) {
                    echo "<script>showAlert(\"Successfully saved $successful_inserts rows to the database!\");</script>";
                }

                if (!empty($errors)) {
                    echo "<script>showAlert(\"There were errors:\");</script><ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo "</ul>";
                }

                // Clear the session data after saving
                unset($_SESSION['csv_data']);

                // Close the database connection
                $conn->close();
            }
            ?>
        </table>
    </div>

    <script>
        function showAlert(message) {
            const alertDiv = document.getElementById('alert');
            alertDiv.innerHTML = message;
            alertDiv.style.display = 'block'; // Show the alert
            setTimeout(() => {
                alertDiv.style.opacity = 1; // Start with full opacity
                alertDiv.style.transition = 'opacity 0.3s'; // Transition for fading out
                alertDiv.style.opacity = 0; // Start fading out
            }, 2000); // Delay before fading out
            setTimeout(() => {
                alertDiv.style.display = 'none'; // Hide the alert after fading out
            }, 2500); // Total time before hiding
        }
    </script>
</body>
</html>
