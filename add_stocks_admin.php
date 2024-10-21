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
    <title>MAIN BRANCH ADMIN</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffff; /* Light background for the body */
            color: #343a40;
            margin: 0;
            padding: 0;
        }

        /* Content area styling */
        .content {
            margin-left: 240px; /* Space for sidebar */
            padding: 20px;
            height: 100vh;
            box-sizing: border-box;
            background-color: #ffffff; /* Clean white background */
            border-left: 4px solid #007bff; /* Blue border for style */
        }

        /* Header styles */
        h1 {
            font-size: 32px;
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            padding: 20px;
            border: 2px solid #007bff; /* Border around the header */
            border-radius: 10px; /* Rounded corners */
            background-color: #f8f9fa; /* Light background color for the header */
            max-width: 600px; /* Optional: to limit header width */
            margin: 0 auto 20px auto; /* Center the header */
        }

        /* Form styles */
        form {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Soft shadow */
            margin-bottom: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto; /* Centering form */
        }

        input[type="file"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            margin-bottom: 10px;
            width: 100%;
            font-size: 16px;
        }

        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 10px; /* Space between buttons */
        }

        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Table shadow */
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
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* Zebra striping for rows */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1; /* Row hover effect */
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

        /* Additional design refinements */
        .content .box {
            margin-top: 20px;
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #dedede;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for content box */
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
            <h1>Managing Stocks</h1>
        </div>

        <div id="alert"></div>

        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv" required>
            <div style="text-align: center;">
                <button type="submit" name="upload">Upload CSV</button>
                <button type="submit" name="save" <?= empty($_SESSION['csv_data']) ? 'disabled' : ''; ?>>Save to DB</button>
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
            if (!isset($_SESSION['csv_data'])) {
                $_SESSION['csv_data'] = [];
            }

            if (isset($_POST['upload'])) {
                if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
                    fgetcsv($file); // Skip the header row

                    $_SESSION['csv_data'] = [];
                    while (($row = fgetcsv($file)) !== false) {
                        $_SESSION['csv_data'][] = $row;
                        echo '<tr>';
                        foreach ($row as $data) {
                            echo '<td>' . htmlspecialchars($data) . '</td>';
                        }
                        echo '</tr>';
                    }
                    fclose($file);
                    echo '<script>showAlert("Data loaded successfully! Click \'Save to DB\' to save the data.");</script>';
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

            if (isset($_POST['save']) && !empty($_SESSION['csv_data'])) {
                $conn = new mysqli("localhost", "root", "", "dealership_shop");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $successful_inserts = 0;
                $errors = [];

                foreach ($_SESSION['csv_data'] as $row) {
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
                    } else {
                        $errors[] = "Error inserting product $product_name: " . $conn->error;
                    }
                }

                if ($successful_inserts > 0) {
                    echo "<script>showAlert('Successfully saved $successful_inserts rows to the database!');</script>";
                }
                if (!empty($errors)) {
                    echo "<script>showAlert('Errors occurred:<br>" . implode('<br>', $errors) . "');</script>";
                }

                unset($_SESSION['csv_data']);
                $conn->close();
            }
            ?>
        </table>
    </div>

    <script>
        function showAlert(message) {
            const alertBox = document.getElementById('alert');
            alertBox.innerHTML = message;
            alertBox.style.display = 'block';
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 5000);
        }
    </script>

</body>
</html>