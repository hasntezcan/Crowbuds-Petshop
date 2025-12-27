<?php
// db_connect.php

$host = 'localhost'; // Usually 'localhost' for local development and FastPanel
$dbname = 'group5_db'; // Database name provided by user
$username = 'root'; // Username provided by user
$password = ''; // Password provided by user

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, stop execution and show error
    die("Database connection failed: " . $e->getMessage());
}
?>