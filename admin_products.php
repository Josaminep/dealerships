<?php
// Fetch products from the database
$conn = new mysqli("localhost", "root", "", "dealership_shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include 'sidebar.php';

// Prepare the SQL query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$sql = "SELECT product_name, price, quantity, brand, stock, categories FROM products WHERE 1=1";

// Add search condition if a search term is provided
if (!empty($search)) {
    $sql .= " AND (product_name LIKE '%$search%' OR brand LIKE '%$search%' OR categories LIKE '%$search%')";
}

// Add category condition if a category is selected
if (!empty($category)) {
    $sql .= " AND categories = '$category'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Display</title>
    <link rel="stylesheet" href="https://code.jquery.com/jquery-3.6.0.min.js">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }

        .content {
            margin-left: 240px;
            padding: 20px;
            height: 100vh;
            box-sizing: border-box;
        }

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
            background-color: #003366;
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

        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 300px;
            margin-right: 10px;
        }

        button {
            padding: 10px 15px;
            background-color: #003366;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
            <h1>Product List</h1>
        </div>
        
        <!-- Category Filter -->
        <div class="search-container">
            <form method="get">
                <select name="category" onchange="this.form.submit();">
                    <option value="">Select Category</option>
                    <option value="Motors" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Motors') ? 'selected' : ''; ?>>Motors</option>
                    <option value="Gear" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Gear') ? 'selected' : ''; ?>>Gear</option>
                    <option value="Accessories" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Accessories') ? 'selected' : ''; ?>>Accessories</option>
                    <option value="Maintenance" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="Tires" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Tires') ? 'selected' : ''; ?>>Tires</option>
                </select>
                <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

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
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['price']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['brand']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['stock']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['categories']) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center;">No products found.</td></tr>';
            }
            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>
