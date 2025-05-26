<?php
session_start();
require 'database.php';

// Set CORS headers to allow requests from your domain
header("Access-Control-Allow-Origin: https://artisoria.great-site.net");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// Handle preflight requests for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Check if the user is logged in and is a seller
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access!']);
    exit();
}

// Get the product ID from either GET or DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $product_id = isset($data['id']) ? $data['id'] : null;
} else {
    $product_id = isset($_GET['id']) ? $_GET['id'] : null;
}

// Check if the product ID is provided and valid
if (!$product_id || !is_numeric($product_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID!']);
    exit();
}

$product_id = (int)$product_id;
$seller_id = (int)$_SESSION['user_id'];

// Verify the product belongs to the seller
$verifyStmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND seller_id = ?");
$verifyStmt->bind_param("ii", $product_id, $seller_id);
$verifyStmt->execute();
$verifyStmt->store_result();

if ($verifyStmt->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Product not found or you do not have permission to delete it!']);
    exit();
}
$verifyStmt->close();

// Start transaction
$conn->begin_transaction();

try {
    // Delete related cart items
    $deleteCartStmt = $conn->prepare("DELETE FROM cart WHERE product_id = ?");
    $deleteCartStmt->bind_param("i", $product_id);
    $deleteCartStmt->execute();
    $deleteCartStmt->close();

    // Delete related reviews
    $deleteReviewsStmt = $conn->prepare("DELETE FROM reviews WHERE product_id = ?");
    $deleteReviewsStmt->bind_param("i", $product_id);
    $deleteReviewsStmt->execute();
    $deleteReviewsStmt->close();

    // Delete related order items
    $deleteOrderItemsStmt = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
    $deleteOrderItemsStmt->bind_param("i", $product_id);
    $deleteOrderItemsStmt->execute();
    $deleteOrderItemsStmt->close();

    // Delete the product
    $deleteProductStmt = $conn->prepare("DELETE FROM products WHERE product_id = ? AND seller_id = ?");
    $deleteProductStmt->bind_param("ii", $product_id, $seller_id);
    $deleteProductStmt->execute();
    
    if ($deleteProductStmt->affected_rows === 0) {
        throw new Exception("Failed to delete product.");
    }
    $deleteProductStmt->close();

    // Commit transaction
    $conn->commit();
    
    echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully!']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $e->getMessage()]);
}

$conn->close();
?>