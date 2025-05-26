<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid product ID.";
    exit();
}

$product_id = $_GET['id'];
$seller_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Product - Artisoria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
      --gray: #1a1a1a;
      --text-gray: #cccccc;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      font-family: 'Manrope', sans-serif;
      background-color: var(--black);
      color: var(--white);
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color: rgb(36,36,36);
      padding: 0.3rem 0.5rem;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-left {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    header h1 {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.5rem;
      font-weight: 900;
      color: var(--coral);
    }

    .tagline {
      font-size: 0.6rem;
    }

    .back-button {
      background-color: var(--black);
      color: var(--white);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .back-button:hover {
      background: var(--white);
      color: var(--black);
    }

    .content-wrapper {
      padding: 5rem 1rem 2rem;
      max-width: 700px;
      margin: 0 auto;
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 1rem;
      text-align: center;
    }

    .form-container {
      background-color: var(--gray);
      border-radius: 4px;
      padding: 0.5rem;
      
    }

    label {
      display: block;
      font-size: 0.8rem;
      font-weight: 500;
      margin-bottom: 0.3rem;
      color: var(--white);
    }

    input[type="text"],
    input[type="number"],
    textarea {
      width: 100%;
      background-color: var(--black);
      color: var(--white);
      border: 1px solid var(--gray);
      border-radius: 4px;
      padding: 0.5rem;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    button {
      width: 100%;
      padding: 0.7rem;
      background-color: var(--black);
      color: var(--white);
      border-radius: 4px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background-color: var(--white);
      color: var(--coral);
    }

    footer {
      background-color: var(--black);
      color: var(--white);
      padding: 0.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.7rem;
      margin-top: 2rem;
    }

    footer a {
      color: var(--coral);
      text-decoration: none;
      margin-left: 1rem;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header>
  <div class="header-left">
    <h1>Artisoria</h1>
    <p class="tagline">Helping local artisians...</p>
  </div>
  <a href="my_products.php" class="back-button">&#x3c;</a>
</header>

<div class="content-wrapper">
  <h2 class="section-title">Edit Product</h2>

  <div class="form-container">
    <form id="editProductForm">
      <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

      <label>Product Name:</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required maxlength="50">

      <label>Price:</label>
      <input type="number" name="price" step="0.01" max="10000000" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>

      <label>Description:</label>
      <textarea name="description" required maxlength="500"><?php echo htmlspecialchars($product['description']); ?></textarea>

      <label>Stock:</label>
      <input type="number" name="stock" max="999999" min="0" value="<?php echo htmlspecialchars($product['stock']); ?>" required>

      <button type="submit">Update Product</button>
    </form>
  </div>
</div>

<footer>
  <div>© Artisoria v1.0</div>
  <div>
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
    <a href="#">About</a>
    <a href="#">Contact</a>
  </div>
</footer>

<script>
  $(document).ready(function(){
    $("#editProductForm").submit(function(event){
      event.preventDefault();
      $.ajax({
        url: "update_product.php",
        type: "POST",
        data: $(this).serialize(),
        success: function(response){
          alert("Product updated successfully!");
          window.location.href = "my_products.php";
        },
        error: function(){
          alert("Error updating product.");
        }
      });
    });
  });
</script>

</body>
</html>
