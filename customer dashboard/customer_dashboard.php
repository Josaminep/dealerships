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
        h1{
            display: flex;
            margin-top: 90px;
            width: 200px;
            margin-left: 200px;
            font-size: 100px;
            font-family: Arial, Helvetica, sans-serif;       
         }
       
        .buttons1{
            position: sticky;
            left: 150px;
            padding-left: 200px;    
        }
        .btn btn-info {
            border-radius: 60px;
        }
        .thumbnail{
          display: flex;
          bottom: 10px;
          display: flex;
            float:right;
            
            margin-right: 150px; 
            height: 400px;
            position: relative;
           bottom: 250px;
          

        }
        .slide-images{
          margin-top: 200px;
          width: 800px;
          position: relative;
          left: 500px;
          top: 100px;
          margin-bottom: 150px;
        }
        .carousel-inner{
          
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
        <a class="nav-link" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./shop.php">Shop</a>
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
<div class=" ">
<h1>THE KAWASAKI MOTORS</h1>
</div>
<div class="buttons1">
<button type="button" class="btn btn-info">Buy now</button>
<button type="button" class="btn btn-info">Shop All</button>
</div>


<div class="thumbnail">

<img src="../images_customer/thumbnail.png" class="rounded mx-auto d-block" alt="thumbnail">
</div>




<div class="slide-images">
<div id="carouselExampleDark" class="carousel carousel-dark slide">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active" data-bs-interval="10000">
      <img src="../images_customer/slide-image1.png" class="d-block w-100" alt="...">
     
    </div>
    <div class="carousel-item" data-bs-interval="2000">
      <img src="../images_customer/slide-image2.png" class="d-block w-100" alt="...">
     
    </div>
    <div class="carousel-item">
      <img src="../images_customer/slide-image3.png" class="d-block w-100" alt="...">
      
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
</div>








<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


    <div class="logout">
</div>
</body>
</html>
