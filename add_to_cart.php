<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['product_id'])) {
    $buyer_id = $_SESSION['user_id'];
    $product_id = $_GET['product_id'];

    // Check available stock
    $check_stock_stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
    $check_stock_stmt->bind_param("i", $product_id);
    $check_stock_stmt->execute();
    $check_stock_result = $check_stock_stmt->get_result();
    $stock_row = $check_stock_result->fetch_assoc();
    $current_stock = $stock_row['stock'];
    $check_stock_stmt->close();

    if ($current_stock < 1) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Product out of stock']);
        exit();
    }

    // Check if item is already in the cart
    $check_stmt = $conn->prepare("SELECT quantity FROM cart WHERE buyer_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $buyer_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        $existing_quantity = $row['quantity'];

        if ($existing_quantity >= $current_stock) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
            exit();
        }

        $update_stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE buyer_id = ? AND product_id = ?");
        $update_stmt->bind_param("ii", $buyer_id, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO cart (buyer_id, product_id, quantity) VALUES (?, ?, 1)");
        $insert_stmt->bind_param("ii", $buyer_id, $product_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>
