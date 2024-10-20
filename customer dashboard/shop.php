<?php
session_start();
if (!isset($_SESSION['customer_user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Customer dashboard content
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <style>
      body{
        background-color: aqua;
      }
        .container-fluid{
            display: flex;
    justify-content: space-between; /* Align items with space between */
    align-items: center; /* Center items vertically */
    padding: 10px;
    background-color: #022337; /* Example background */
    height: 120px;

        }
        .navbar-collapse {
            margin: 0 40px; /* Add space between navbar items */
    color: white;
    text-decoration: none;
        }
        .d-flex {
           margin: 100 0px;
           margin-left: 300px;
        }
        .navbar-nav{
            margin-left: 600px;
        }
        .nav-link{
            color: white;
            font-size: 30px;
            margin: 25px;
        }
        .navbar-brand{
            color: white;
            
        }
    
       
     
        .log-out{
          
          color: white;
         
          border-radius: 40px;
          position: relative;
          bottom: 3px;
          left: 15px;
        }.logout-btn{
          
          position: relative;
          bottom: 40px;
          right: 5px;
          top: 10px;
          border-radius: 60px;
          width: 80px;
         
          
        }
        .logout-btn:hover{
          transition-duration: 0.4s;
          background-color: #04AA6D; /* Green */
  color: white;
        }



        /* Container to hold the cards */
.card-container {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    padding: 20px;
}

/* Style each card */
.card {
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    width: 220px;
    text-align: center;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    margin: 10px;
}

/* Style for the images */
.card img {
    width: 100%;
    height: auto;
    border-radius: 5px;
}

/* Style the titles */
.card h3 {
    font-size: 18px;
    font-weight: bold;
    margin: 15px 0 5px 0;
    color: #333;
}

/* Style the prices */
.card .price {
    font-size: 20px;
    color: red;
    font-weight: bold;
}

/* Style the button */
.view-btn {
    background-color: #002855;
    color: white;
    border: none;
    padding: 10px;
    margin-top: 10px;
    cursor: pointer;
    border-radius: 4px;
    display: inline-block;
    font-size: 14px;
}

.view-btn i {
    margin-right: 5px;
}

/* Add hover effect for the button */
.view-btn:hover {
    background-color: #004b85;
}



    </style>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
      <a class="navbar-brand" href="#">Hidden brand</a>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
        <a class="nav-link" href="./customer_dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Shop</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="#">About</a>
        </li>
       
      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
    <li class="logout-btn" type="button">

        <a class="log-out" href="../logout.php">Logout</a>
        </li>
  </div>
</nav>


<div class="card-container">
    <div class="card">
        <img src="kawasaki-raider.jpg" alt="Kawasaki Raider 150">
        <h3>KAWASAKI RAIDER 150</h3>
        <p class="price">PHP 120,000</p>
        <button class="view-btn">
            <i class="fa fa-shopping-cart"></i> View Requirements
        </button>
    </div>

    <div class="card">
        <img src="honda-click.jpg" alt="Honda Click 125i">
        <h3>HONDA CLICK 125i</h3>
        <p class="price">PHP 80,000</p>
        <button class="view-btn">
            <i class="fa fa-shopping-cart"></i> View Requirements
        </button>
    </div>

    <div class="card">
        <img src="kawasaki-ninja.jpg" alt="Kawasaki Ninja">
        <h3>KAWASAKI NINJA</h3>
        <p class="price">PHP 350,000</p>
        <button class="view-btn">
            <i class="fa fa-shopping-cart"></i> View Requirements
        </button>
    </div>

    <div class="card">
        <img src="ninja-650.jpg" alt="Ninja 650">
        <h3>NINJA 650</h3>
        <p class="price">PHP 350,000</p>
        <button class="view-btn">
            <i class="fa fa-shopping-cart"></i> View Requirements
        </button>
    </div>
</div>












<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


    <div class="logout">
</div>
</body>
</html>
