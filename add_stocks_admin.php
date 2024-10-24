<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Add Products</title>

    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }

        /* Content area styling */
        .content {
            margin-left: 240px;
            padding: 20px;
            height: 100vh;
            box-sizing: border-box;
        }

        /* Header styles */
        .header {
            background-color: #003366;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            color: white;
            margin: 0;
        }

        /* Form styles */
        form {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        input[type="file"],
        input[type="text"],
        input[type="number"],
        select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            margin-bottom: 10px;
            width: 100%;
            font-size: 16px;
        }

        button {
            padding: 8px 15px; /* Reduced padding */
            width: auto; /* Allow width to fit content */
            background-color: #e8d102;
            color: black;
            border: none;
            border-radius: 5px;
            font-size: 12px; /* Reduced font size */
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block; /* Change to inline-block for alignment */
            margin: 0 5px; /* Adjust margin for spacing between buttons */
        }

        button:hover {
            background-color: #003366;
            color: white;
        }

        button:disabled {
            background-color: #6c757d;
        }

        /* Table styles */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            border: 1px solid #dee2e6;
            text-align: left;
            font-size: 16px;
            color: #495057;
        }

        th {
            background-color: #003366; /* Changed to dark blue */
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* Zebra striping for rows */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Alert box styling */
        #alert {
            display: none;
            padding: 15px;
            background-color: #f39c12;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            max-width: 600px;
            margin: 10px auto;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
            <h1>Managing Stocks</h1>
        </div>

        <div id="alert"></div>

        <!-- CSV Upload Form -->
        <form action="" method="post" enctype="multipart/form-data" style="text-align: center;">
            <input type="file" name="csv_file" accept=".csv" required style="margin-bottom: 10px;">
            <div style="display: flex; justify-content: center; gap: 10px;">
                <button type="submit" name="upload_save">Upload and Add Products</button>
                <button type="submit" name="refresh">Refresh Table</button>
            </div>
        </form>


        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Brand</th>
                <th>Stock</th>
                <th>Categories</th>
            </tr>

            <?php
            // Check for uploaded CSV data and save to session
            if (!isset($_SESSION['csv_data'])) {
                $_SESSION['csv_data'] = [];
            }

            // Clear the table when refresh button is clicked
            if (isset($_POST['refresh'])) {
                $_SESSION['csv_data'] = []; // Clear the session data
            }

            // Upload and process CSV file
            if (isset($_POST['upload_save'])) {
                if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
                    fgetcsv($file); // Skip the header row

                    $_SESSION['csv_data'] = []; // Clear previous data
                    $conn = new mysqli("localhost", "root", "", "dealership_shop");

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $successful_inserts = 0;
                    $errors = [];

                    while (($row = fgetcsv($file)) !== false) {
                        // Ensure all fields are set
                        if (count($row) < 6) {
                            $errors[] = "Row does not contain enough data.";
                            continue;
                        }

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
                            // Store the row in the session to display it
                            $_SESSION['csv_data'][] = $row; 
                        } else {
                            $errors[] = "Error inserting product $product_name: " . $conn->error;
                        }
                    }

                    fclose($file);
                    $conn->close(); // Close the database connection

                    // Display alert messages
                    if ($successful_inserts > 0) {
                        echo "<script>showAlert('Successfully saved $successful_inserts rows to the database!');</script>";
                    }
                    if (!empty($errors)) {
                        echo "<script>showAlert('Errors occurred: " . implode(', ', $errors) . "');</script>";
                    }
                } else {
                    echo '<script>showAlert("Error uploading the file. Please try again.");</script>';
                }
            }

            // Display uploaded data if present
            if (!empty($_SESSION['csv_data'])) {
                foreach ($_SESSION['csv_data'] as $row) {
                    echo '<tr>';
                    foreach ($row as $data) {
                        echo '<td>' . htmlspecialchars($data) . '</td>';
                    }
                    echo '</tr>';
                }
            }
            ?>

        </table>

        <!-- Modify Price Form -->
        <form action="" method="post" style="margin-top: 20px;">
            <h3>Modify Product Price</h3>
            <select name="product_name" required>
                <option value="" disabled selected>Select a product</option>
                <?php
                $conn = new mysqli("localhost", "root", "", "dealership_shop");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $result = $conn->query("SELECT product_name FROM products");
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['product_name']) . '">' . htmlspecialchars($row['product_name']) . '</option>';
                }
                $conn->close();
                ?>
            </select>
            <input type="number" name="new_price" placeholder="Enter new price" required>
            <div style="text-align: center;">
                <button type="submit" name="modify_price">Modify Price</button>
            </div>
        </form>

        <?php
        // Modify price action
        if (isset($_POST['modify_price'])) {
            $conn = new mysqli("localhost", "root", "", "dealership_shop");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $product_name = $conn->real_escape_string($_POST['product_name']);
            $new_price = $conn->real_escape_string($_POST['new_price']);
            $sql = "UPDATE products SET price='$new_price' WHERE product_name='$product_name'";

            if ($conn->query($sql) === TRUE) {
                echo "<script>showAlert('Price updated successfully!');</script>";
            } else {
                echo "<script>showAlert('Error updating price: " . $conn->error . "');</script>";
            }
            $conn->close();
        }
        ?>

    </div>
    <script>
        function showAlert(message) {
            var alertBox = document.getElementById("alert");
            alertBox.innerHTML = message;
            alertBox.style.display = "block";
            setTimeout(() => {
                alertBox.style.display = "none";
            }, 5000);
        }
    </script>
</body>
</html>
