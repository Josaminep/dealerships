<html>
<head>
  <base href="/" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Motor Shop Login</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
      background: linear-gradient(120deg, #2980b9, #8e44ad);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
    }

    .container {
      text-align: center;
      background: rgba(255, 255, 255, 0.9);
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
      width: 300px;
    }

    h1 {
      color: #333;
      margin-bottom: 20px;
      font-size: 24px;
    }

    .logo {
      margin-bottom: 20px;
    }

    .button {
      display: inline-block; /* Change display to inline-block */
      width: 70%;
      padding: 15px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      text-decoration: none;
      color: white;
      transition: transform 0.2s, background-color 0.2s;
    }

    .button:hover {
      transform: scale(1.05);
    }

    .admin-btn {
      background-color: #e74c3c; /* Red for Admin */
    }

    .customer-btn {
      background-color: #2ecc71; /* Green for Customer */
    }

    .rotating-wrench {
      animation: rotate 3s linear infinite;
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    /* Additional styling for text readability */
    p {
      color: #555;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">
      <svg class="rotating-wrench" width="50" height="50" viewBox="0 0 24 24" fill="#333">
        <path d="M4.17,6.27C4.17,6.27 5.23,6.86 5.23,7.5C5.23,8.14 4.17,8.73 4.17,8.73C4.17,8.73 3.11,8.14 3.11,7.5C3.11,6.86 4.17,6.27 4.17,6.27M16.84,15.47L19.73,12.58L18.32,11.17L16.84,12.65L13.45,9.27L14.93,7.79L13.51,6.38L10.63,9.26L9.21,7.84L7.79,9.26L6.38,7.84L3.75,10.47C3.75,10.47 2.76,10.71 2.76,11.72C2.76,12.73 3.75,12.97 3.75,12.97L11.89,21.11C11.89,21.11 12.13,22.1 13.14,22.1C14.15,22.1 14.39,21.11 14.39,21.11L21.24,14.26V7.84L19.82,6.43L18.41,7.84L16.99,6.42L15.57,7.84L16.84,9.11L16.84,15.47Z"/>
      </svg>
    </div>
    <h1>Ever Sure Shop </h1>
    <a href="dealerships/login.php" class="button admin-btn">Administrator Login</a>
    <a href="dealerships/customer/login.php" class="button customer-btn">Customer Login</a>
    <p>Welcome to the Motor Shop Portal</p>
  </div>
</body>
</html>
