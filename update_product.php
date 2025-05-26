<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    if (empty($name) || empty($description) || !isset($price) || !isset($stock)) {
        echo "All fields are required.";
        exit();
    }

    $updateStmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, stock = ? WHERE product_id = ?");
    $updateStmt->bind_param("sdsii", $name, $price, $description, $stock, $product_id);

    if ($updateStmt->execute()) {
        echo "success";
    } else {
        echo "Error updating product: " . $updateStmt->error;
    }

    $updateStmt->close();
    $conn->close();
}
?>
