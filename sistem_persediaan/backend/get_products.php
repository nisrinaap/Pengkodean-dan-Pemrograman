<?php
include 'config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch products: ' . $e->getMessage()]);
}
?>