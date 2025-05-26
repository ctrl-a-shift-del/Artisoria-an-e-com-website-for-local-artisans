<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}

$cart_id = $_POST['cart_id'];
$action = $_POST['action'];

// Get current quantity
$stmt = $conn->prepare("SELECT quantity, product_id FROM cart WHERE cart_id = ?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$current_quantity = $row['quantity'];
$product_id = $row['product_id'];

if ($action == 'decrease') {
    if ($current_quantity == 1) {
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
        $delete_stmt->bind_param("i", $cart_id);
        $delete_stmt->execute();
    } else {
        $new_quantity = $current_quantity - 1;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $cart_id);
        $update_stmt->execute();
    }
} elseif ($action == 'increase') {
    $product_stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $stock_row = $product_result->fetch_assoc();
    $current_stock = $stock_row['stock'];

    if ($current_quantity + 1 > $current_stock) {
        $_SESSION['error'] = "Cannot add more than available stock.";
    } else {
        $new_quantity = $current_quantity + 1;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $cart_id);
        $update_stmt->execute();
    }
}

header("Location: buyer_cart.php");
exit();
