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

// Fetch products from the database
$query = "SELECT id, product_name, price FROM products"; // Ensure to select the product ID
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Customer Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #e0f7fa;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0c3b60;
            padding: 10px 20px;
        }
        .logo img {
            height: 50px;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin: 0 10px;
        }
        nav ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            padding: 8px 16px;
            display: block;
            transition: background-color 0.3s, color 0.3s; /* Smooth transition for hover */
        }
        nav ul li:hover a {
            background-color: #fff;
            color: #0c3b60;
            border-radius: 4px;
        }
        .search input {
            padding: 6px;
            border-radius: 4px;
            border: none;
        }
        .card-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            padding: 20px;
        }
        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            width: 220px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin: 10px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .card h3 {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 5px 0;
            color: #0c3b60;
        }
        .card .price {
            font-size: 20px;
            color: red;
            font-weight: bold;
        }
        .view-btn {
            background-color: #0c3b60;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
            display: inline-block;
            font-size: 14px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .view-btn i {
            margin-right: 5px;
        }
        .view-btn:hover {
            background-color: #002855;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="./pictures/logo.png" alt="Eversure Logo">
        </div>
        <nav>
            <ul>
                <li><a href="./customer_dashboard.php" class="active">Home</a></li>
                <li><a href="./shop.php">Shop</a></li>
                <li><a href="#">About</a></li>
            </ul>
        </nav>
        <div class="search">
            <input type="text" placeholder="Search...">
        </div>
        <li class="logout-btn" type="button">
            <a class="log-out" href="../logout.php">Logout</a>
        </li>
    </header>

    <div class="card-container">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                <p class="price">PHP <?php echo number_format($product['price'], 2); ?></p>
                <button class="view-btn" onclick="viewRequirements(<?php echo $product['id']; ?>)">
                    <i class="fa fa-shopping-cart"></i> View Requirements
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function viewRequirements(productId) {
            // Redirect to the requirements page with the product ID
            window.location.href = "./view_requirements.php?id=" + productId; 
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
