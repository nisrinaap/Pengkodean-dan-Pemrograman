<?php
include 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Log the request for debugging
$logFile = __DIR__ . '/delete_log.txt';
file_put_contents($logFile, "Request received at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents($logFile, "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $error = 'Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD'];
    file_put_contents($logFile, "Error: $error\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

try {
    // Get the product ID from the POST body
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    if (!$id) {
        throw new Exception('Product ID is required.');
    }
    file_put_contents($logFile, "Product ID: $id\n", FILE_APPEND);

    // Fetch the product to get the image path (if any)
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception('Product not found.');
    }
    file_put_contents($logFile, "Product found: " . print_r($product, true) . "\n", FILE_APPEND);

    // Delete the image file if it exists
    if ($product['image']) {
        $imagePath = __DIR__ . '/../' . $product['image'];
        file_put_contents($logFile, "Image path: $imagePath\n", FILE_APPEND);

        if (file_exists($imagePath)) {
            if (!is_writable($imagePath)) {
                throw new Exception('Image file is not writable: ' . $product['image']);
            }
            if (!unlink($imagePath)) {
                throw new Exception('Failed to delete product image: ' . $product['image']);
            }
            file_put_contents($logFile, "Image deleted: $imagePath\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "Image file not found: $imagePath\n", FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "No image associated with product.\n", FILE_APPEND);
    }

    // Delete the product from the database
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        file_put_contents($logFile, "Product deleted successfully.\n", FILE_APPEND);
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to delete product from database.');
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    file_put_contents($logFile, "Error: $error\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $error]);
}
?>