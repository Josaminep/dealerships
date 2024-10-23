<?php
// Database connection (adjust the credentials as per your configuration)
$host = 'localhost';
$dbname = 'dealership_shop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Get the product ID from the URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the product details based on the product ID
$query = "SELECT product_name, price FROM products WHERE id = :id"; 
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "No product found.";
    exit();
}

// Handle form submission to save to sales table
$insertSuccess = false; // Initialize the success flag
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $branch = $_POST['branch'];

    if (empty($branch)) {
        echo "<script>alert('Please select a branch before submitting.');</script>";
    } else {
        // Insert the sales data into the sales table
        $insertQuery = "INSERT INTO sales (product_id, product_name, price, branch, status) 
                        VALUES (:id, :product_name, :price, :branch, :status)";
        $insertStmt = $pdo->prepare($insertQuery);

        // Execute the query and check for success
        $insertSuccess = $insertStmt->execute([
            'id' => $product_id,
            'product_name' => $product_name,
            'price' => $price,
            'branch' => $branch,
            'status' => 'pending' // Set status to pending
        ]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EverSure Motorcycle Purchase</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e0f7fa, #b3e5fc);
        }
        header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #0c3b60;
        padding: 10px 20px;
    }
        .logo img {
            width: 100px;
            height: auto;
        }
        nav ul {
            display: flex;
            gap: 20px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .nav-item {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .search input {
            padding: 5px;
            border-radius: 5px;
            border: none;
        }
        .logout-btn {
            margin-left: 20px;
        }
        .logout-btn a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .content {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative; /* For absolute positioning of the branch select */
        }
        .motorcycle-image {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
        }
        .motorcycle-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .requirements {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .requirement {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
        }
        .upload-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            background-color: #2196f3;
            color: white;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .branch-select {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .branch-select select {
            padding: 10px;
            border-radius: 25px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-weight: bold;
            color: #0c3b60;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        .branch-select select:hover,
        .branch-select select:focus {
            border-color: #0c3b60;
            outline: none;
        }
        .product-select {
            margin: 20px 0;
        }
        .product-select select {
            padding: 10px;
            border-radius: 25px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-weight: bold;
            color: #0c3b60;
            cursor: pointer;
            transition: border-color 0.3s;
            width: 100%;
        }
        .product-select select:hover,
        .product-select select:focus {
            border-color: #0c3b60;
            outline: none;
        }
    </style>
</head>
<body>
    <header>
        <a href="javascript:history.back()" class="nav-item back-btn">Back</a>
        <div class="logout-btn">
            <a class="log-out" href="../logout.php">Logout</a>
        </div>
    </header>

    <main class="content">
        <div class="branch-select">
            <select id="branch-dropdown">
                <option value="" disabled selected>Select Branch</option>
                <option value="Branch 1">Branch 1</option>
                <option value="Branch 2">Branch 2</option>
                <option value="Branch 3">Branch 3</option>
                <option value="Branch 4">Branch 4</option>
                <option value="Branch 5">Branch 5</option>
            </select>
        </div>

        <div class="motorcycle-info">
            <!-- Display the name and price here -->
            <h2 id="motorcycle-name"><?php echo htmlspecialchars($product['product_name']); ?></h2>
            <p id="motorcycle-price">PHP <?php echo number_format($product['price'], 2); ?></p>
        </div>

        <!-- Form to submit the purchase and upload requirements -->
        <form id="purchase-form" action="" method="POST" enctype="multipart/form-data">
            <!-- Hidden fields to send the product details -->
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">

            <!-- Selected branch -->
            <input type="hidden" name="branch" id="branch-input">

            <!-- Your other form elements here -->

            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </main>

    <script>
        document.getElementById('branch-dropdown').addEventListener('change', function() {
            const selectedBranch = this.value;
            document.getElementById('branch-input').value = selectedBranch;
        });

        document.getElementById('purchase-form').addEventListener('submit', function(event) {
            const branchInput = document.getElementById('branch-input').value;
            if (!branchInput) {
                alert('Please select a branch before submitting.');
                event.preventDefault(); // Prevent form submission
            }
        });

        // Show alert popup based on PHP response
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php if ($insertSuccess): ?>
                echo "alert('Sale recorded successfully!');";
            <?php else: ?>
                echo "alert('Failed to record the sale.');";
            <?php endif; ?>
        <?php endif; ?>
    </script>
</body>
</html>