<?php
include 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

try {
    // Validate required fields
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $category = $_POST['category'] ?? null;

    if (!$name || !$price || !$quantity || !$category) {
        throw new Exception('All fields (name, price, quantity, category) are required.');
    }

    // Handle image upload
    $image = null;
    $uploadDir = __DIR__ . '/../assets/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            throw new Exception('Failed to upload image.');
        }
        $image = 'assets/' . $imageName;
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO products (name, price, quantity, image, category) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$name, $price, $quantity, $image, $category]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert product into database.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>