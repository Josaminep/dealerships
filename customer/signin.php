<?php
// Start session at the beginning of the script
session_start();

function userLogin($email, $password) {
    // Database connection parameters
    $host = 'localhost';  // Change this if your database is on a different host
    $db_user = 'root';  // Your database username
    $db_password = '';  // Your database password
    $db_name = 'dealership_shop';  // Your database name

    // Create a database connection
    $con = new mysqli($host, $db_user, $db_password, $db_name);

    // Check connection
    if ($con->connect_error) {
        die('Connection failed: ' . $con->connect_error);
    }

    // Sanitize user input
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    $password = trim($password);

    // Query to fetch the user based on email
    $stmt = $con->prepare('SELECT password FROM customers WHERE email = ?');
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Password is correct, start a session
                $_SESSION['email'] = $email; // Store the user's email in session

                // Close the statement and database connection
                $stmt->close();
                $con->close();

                // Redirect to the customer dashboard
                header('Location: customer_dashboard.php');
                exit; // Stop execution after redirect
            }
        }
        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $con->close();
    return false; // Login failed
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Call the userLogin function
    if (!userLogin($email, $password)) {
        $login_error = 'Invalid email or password. Please try again.';
    }
}
?>
