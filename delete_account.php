<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$user_id = $_SESSION["user_id"];
$user_type = $_SESSION["user_type"];

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Delete all dependent records first
    
    // If user is a seller, delete their products and related data
    if ($user_type === 'seller') {
        // Get all product IDs for this seller
        $product_ids = [];
        $query = "SELECT product_id FROM products WHERE seller_id='$user_id'";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $product_ids[] = $row['product_id'];
        }
        
        if (!empty($product_ids)) {
            $product_ids_str = implode(",", $product_ids);
            
            // Delete from cart
            $query = "DELETE FROM cart WHERE product_id IN ($product_ids_str)";
            mysqli_query($conn, $query);
            
            // Delete from order_items
            $query = "DELETE FROM order_items WHERE product_id IN ($product_ids_str)";
            mysqli_query($conn, $query);
            
            // Delete from reviews
            $query = "DELETE FROM reviews WHERE product_id IN ($product_ids_str)";
            mysqli_query($conn, $query);
            
            // Delete products
            $query = "DELETE FROM products WHERE seller_id='$user_id'";
            mysqli_query($conn, $query);
        }
    }
    
    // Delete user's cart items
    $query = "DELETE FROM cart WHERE buyer_id='$user_id'";
    mysqli_query($conn, $query);
    
    // Delete user's reviews
    $query = "DELETE FROM reviews WHERE buyer_id='$user_id'";
    mysqli_query($conn, $query);
    
    // Delete user's order items (via orders)
    $query = "SELECT order_id FROM orders WHERE buyer_id='$user_id'";
    $result = mysqli_query($conn, $query);
    $order_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $order_ids[] = $row['order_id'];
    }
    
    if (!empty($order_ids)) {
        $order_ids_str = implode(",", $order_ids);
        $query = "DELETE FROM order_items WHERE order_id IN ($order_ids_str)";
        mysqli_query($conn, $query);
    }
    
    // Delete user's orders
    $query = "DELETE FROM orders WHERE buyer_id='$user_id'";
    mysqli_query($conn, $query);
    
    // Finally, delete the user
    $query = "DELETE FROM users WHERE user_id='$user_id'";
    mysqli_query($conn, $query);
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Destroy session
    session_destroy();
    
    // Redirect to login page
    header("Location: index.php");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    // Log error (you might want to implement proper error logging)
    error_log("Error deleting account: " . $e->getMessage());
    
    // Redirect back with error message
    $_SESSION['error'] = "Could not delete account. Please try again.";
    header("Location: account.php");
    exit();
}
?>