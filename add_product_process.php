<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seller_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description']; // Ensure this is correctly retrieved
    $stock = $_POST['stock'];

    // Ensure price is a valid number
    if (!is_numeric($price) || $price <= 0 || $price > 10000000) {
    echo "Price must be between 0 and 10,000,000.";
    exit();
    }

    if (!is_numeric($stock) || $stock < 0 || $stock > 999999) {
    echo "Stock must be between 0 and 999,999.";
    exit();
    }

    // Image upload settings
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Allowed image types
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    // Image columns in the database
    $imageColumns = [
        "image_1", "image_2", "image_3", "image_4", "image_5",
        "image_6", "image_7", "image_8", "image_9", "image_10"
    ];
    $imageValues = array_fill(0, 10, NULL); // Default all to NULL

    // Ensure at least one image is uploaded
    $hasAtLeastOneImage = false;
    foreach ($imageColumns as $columnName) {
        if (!empty($_FILES[$columnName]['name'])) {
            $hasAtLeastOneImage = true;
            break;
        }
    }
    
    if (!$hasAtLeastOneImage) {
        echo "Please upload at least one image.";
        exit();
    }


    // Upload images and assign them to respective columns
    foreach ($imageColumns as $index => $columnName) {
        if (!empty($_FILES[$columnName]['name'])) {
            $fileType = $_FILES[$columnName]['type'];

            // Validate image type
            if (in_array($fileType, $allowedTypes)) {
                $fileName = uniqid() . "_" . basename($_FILES[$columnName]['name']);
                $targetFilePath = $uploadDir . $fileName;

                // Move file and store path
                if (move_uploaded_file($_FILES[$columnName]['tmp_name'], $targetFilePath)) {
                    $imageValues[$index] = $targetFilePath;
                }
            }
        }
    }

    // Insert product into database
    $stmt = $conn->prepare("
        INSERT INTO products (seller_id, name, price, description, stock, 
        image_1, image_2, image_3, image_4, image_5, 
        image_6, image_7, image_8, image_9, image_10) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // Bind parameters correctly
    $stmt->bind_param("isdsissssssssss", 
        $seller_id, 
        $name, 
        $price, 
        $description, 
        $stock, 
        $imageValues[0], 
        $imageValues[1], 
        $imageValues[2], 
        $imageValues[3], 
        $imageValues[4], 
        $imageValues[5], 
        $imageValues[6], 
        $imageValues[7], 
        $imageValues[8], 
        $imageValues[9]
    );

    if ($stmt->execute()) {
        echo "Product added successfully!";
    } else {
        echo "Error: ". $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>