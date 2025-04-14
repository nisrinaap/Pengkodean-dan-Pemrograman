<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'inventory_system';

try {
    // First, connect to MySQL without specifying a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the database exists, create it if it doesn't
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    // Create the products table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            quantity INT NOT NULL,
            image VARCHAR(255),
            category VARCHAR(255) NOT NULL
        )
    ");
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}
?>