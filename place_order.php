<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: billing.php");
    exit();
}

// Collect POST data
$buyer_id = $_SESSION['user_id'];
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$total_price = 0;

if (empty($address) || empty($phone_number)) {
    $_SESSION['message'] = "Address and phone number are required.";
    header("Location: billing.php");
    exit();
}

$conn->begin_transaction();

try {
    // Fetch cart items
    $cart_stmt = $conn->prepare("
        SELECT c.product_id, c.quantity, p.stock, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.buyer_id = ?
    ");
    $cart_stmt->bind_param("i", $buyer_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    $cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);
    $cart_stmt->close();

    if (empty($cart_items)) {
        throw new Exception("Your cart is empty.");
    }

    // Check stock and calculate total
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Product {$item['product_id']} has insufficient stock.");
        }
        $total_price += $item['price'] * $item['quantity'];
    }

    // Decrement stock
    foreach ($cart_items as $item) {
        $new_stock = $item['stock'] - $item['quantity'];
        $update_stock_stmt = $conn->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
        $update_stock_stmt->bind_param("ii", $new_stock, $item['product_id']);

        if (!$update_stock_stmt->execute()) {
            throw new Exception("Failed to update stock for product {$item['product_id']}");
        }
        $update_stock_stmt->close();
    }

    // Insert order
    $order_stmt = $conn->prepare("INSERT INTO orders (buyer_id, total_price, status, address, phone_number) VALUES (?, ?, 'pending', ?, ?)");
    $order_stmt->bind_param("idss", $buyer_id, $total_price, $address, $phone_number);

    if (!$order_stmt->execute()) {
        throw new Exception("Failed to create order!");
    }

    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    // Insert order items
    foreach ($cart_items as $item) {
        $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $price = $item['price'];
        $order_item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $price);

        if (!$order_item_stmt->execute()) {
            throw new Exception("Failed to add order items!");
        }
        $order_item_stmt->close();
    }

    // Clear cart
    $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE buyer_id = ?");
    $clear_cart_stmt->bind_param("i", $buyer_id);

    if (!$clear_cart_stmt->execute()) {
        throw new Exception("Failed to clear cart!");
    }

    $conn->commit();
    $_SESSION['message'] = "Order placed successfully!";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = $e->getMessage();
}

$conn->close();
header("Location: buyer_orders.php");
exit();
