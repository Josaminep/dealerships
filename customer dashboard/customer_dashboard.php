<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kawasaki Motors</title>
    <link rel="stylesheet" href="styles.css">
</head>
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

.hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 40px;
    background: linear-gradient(135deg, #c6e6ff, #e0f7fa);
}

.content {
    max-width: 50%;
}

.content h1 {
    font-size: 48px;
    color: #333;
    margin-bottom: 20px;
}

.content h1 strong {
    font-weight: bold;
    color: #000;
}

.buttons {
    display: flex;
    gap: 20px;
}

.btn {
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 20px;
    font-size: 16px;
    background-color: #fff;
    color: #0c3b60;
    border: 2px solid #0c3b60;
    transition: background-color 0.3s, color 0.3s, transform 0.3s; /* Smooth transition for hover */
    display: inline-block;
}

.btn:hover {
    background-color: #0c3b60; /* Darker background on hover */
    color: #fff; /* Text changes to white */
    transform: translateY(-3px); /* Slight lift effect */
}


.product-image {
    max-width: 40%;
    text-align: center;
    margin-top: 200px;
    margin-right: 100px;
}

.product-image img {
    max-width: 100%;
    border: 5px solid #d1effc;
    border-radius: 10px;
}
.new-technology-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 50px;
    background: linear-gradient(135deg, #a8d5f0, #dfeefb); /* Gradient background similar to the website */
}

.new-technology-text {
    flex: 1;
    padding-right: 20px;
}

.new-technology-text h2 {
    font-size: 32px;
    margin-bottom: 15px;
    color: #0c3b60;
}

.new-technology-text p {
    font-size: 16px;
    color: #333;
    line-height: 1.5;
}

.new-technology-image {
    flex: 1;
    text-align: right;
}

.new-technology-image img {
    max-width: 100%;
    border-radius: 10px;
}


</style>
<body>
    <header>
        <div class="logo">
            <img src="./pictures/logo.png" alt="Eversure Logo">
        </div>
        <nav>
            <ul>
                <li><a href="#" class="active">Home</a></li>
                <li><a href="#">Shop</a></li>
                <li><a href="#">About</a></li>
            </ul>
        </nav>
        <div class="search">
            <input type="text" placeholder="Search...">
        </div>
    </header>

    <section class="hero">
        <div class="content">
            <h1>The Kawasaki <strong>motors</strong></h1>
            <div class="buttons">
                <a href="#" class="btn btn-primary">Buy Now</a>
                <a href="#" class="btn btn-secondary">Shop All</a>
            </div>
        </div>
        <div class="product-image">
            <img src="./pictures/kawasaki.png" alt="Kawasaki Motorcycle">
        </div>
    </section>


    <div class="new-technology-section">
        <div class="new-technology-text">
            <h2>All-new technology</h2>
            <p>All new technology and very user-friendly to use. You can order and purchase as you wish. We have all you want if you're a rider.</p>
        </div>
        <div class="new-technology-image">
            <img src="./pictures/motor2.png" alt="All-new technology bike" />
        </div>
    </div>
    
</body>
</html>
