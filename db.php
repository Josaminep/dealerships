<?php

$db_name = "mysql:host=localhost;dbname=dealership_shop";
$username = "root";
$password = "";

try {
    $db = new PDO($db_name, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}





?>
