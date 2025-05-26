<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Fetch cart items along with stock information
$stmt = $conn->prepare("
    SELECT cart.cart_id, cart.quantity, products.product_id, products.name, products.price, products.image_1, products.stock 
    FROM cart 
    JOIN products ON cart.product_id = products.product_id 
    WHERE cart.buyer_id = ?
");
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Cart - Artisoria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
      --coral-dark: #d6484d;
      --gray: #333333;
      --light-gray: #555555;
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
      display: flex;
      flex-direction: column;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color:rgb(36,36,36);
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
      margin-bottom: 0rem;
    }

    header .tagline {
      font-family: 'Manrope', sans-serif;
      font-size: 0.6rem;
      font-weight: 500;
      color: var(--white);
      margin-top: 0rem;
    }

    .back-button {
      background: var(--black);
      position: fixed;
      top: 0.7rem;
      right: 0.5rem;
      color: var(--white);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 1.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      
    }

    .back-button:hover {
      background: var(--white);
      color: var(--black);
    }

    .content-wrapper {
      flex: 1; /* fills remaining space */
      padding-top: 4rem;
      padding-bottom: 2rem;
      width: 100%;
      max-width: 450px;
      margin: 0 auto;
    }


    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 0.8rem;
      text-align: center;
    }

    .cart-items {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .cart-item {
      display: flex;
      background-color: rgb(36,36,36);
      border-radius: 4px;
      padding: 0.5rem;
      margin-bottom: 0.5rem;
    }

    .cart-item-image {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 1rem;
    }

    .cart-item-details {
      flex: 1;
    }

    .cart-item-name {
      font-size: 0.8rem;
      margin-bottom: 0.5rem;
    }

    .cart-item-price {
      color: #ccc;
      font-size: 0.8rem;
      margin-bottom: 0.5rem;
    }

    .stock-warning {
      color: var(--coral);
      font-size: 0.8rem;
      margin-bottom: 0.5rem;
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .quantity-btn {
      background-color: var(--black);
      color: var(--white);
      border: 1px solid var(--white);
      border-radius: 4px;
      width: 30px;
      height: 30px;
      font-size: 0.8rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .quantity-btn:hover {
      background-color: var(--white);
      color: var(--black);
    }

    .quantity-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .quantity-value {
      min-width: 20px;
      text-align: center;
    }

    .total-section {
      background-color:rgba(36,36,36);
      border-radius: 4px;
      padding: 0.5rem;
      margin-top: 1.5rem;
      text-align: center;
    }

    .total-price {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 0.8rem;
    }

    .checkout-btn {
      background-color: var(--black);
      color: var(--white);
      border: none;
      padding: 0.5rem;
      border-radius: 4px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
    }

    .checkout-btn:hover {
      background-color: var(--white);
      color: var(--coral);
    }

    .empty-cart {
      text-align: center;
      font-size: 0.8rem;
      margin-top: 2rem;
    }

    footer {
      background-color: var(--black);
      color: var(--white);
      padding: 0.5rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.7rem;
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
</head>
<body>

<header>
  <div class="header-left">
    <h1>Artisoria</h1>
    <p class="tagline">Helping local artisians...</p>
  </div>
  <a href="buyer_home.php" class="back-button">&#x3c;</a>
</header>

<div class="content-wrapper">
  <h2 class="section-title">My Cart</h2>

  <?php if (empty($cart_items)): ?>
    <p class="empty-cart">Uh Oh! Your Cart is empty   :(</p>
  <?php else: ?>
    <ul class="cart-items">
      <?php foreach ($cart_items as $item): ?>
        <li class="cart-item">
          <img src="<?php echo htmlspecialchars($item['image_1']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
          <div class="cart-item-details">
            <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
            <div class="cart-item-price">₹<?php echo number_format($item['price'], 2); ?></div>
            
            <?php if ($item['stock'] < $item['quantity']): ?>
              <div class="stock-warning">Not enough stock. Available: <?php echo $item['stock']; ?></div>
            <?php endif; ?>

            <div class="quantity-controls">
              <form action="update_cart.php" method="post" style="display:inline;">
                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                <input type="hidden" name="action" value="decrease">
                <button type="submit" class="quantity-btn">-</button>
              </form>
              
              <span class="quantity-value"><?php echo $item['quantity']; ?></span>
              
              <form action="update_cart.php" method="post" style="display:inline;">
                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                <input type="hidden" name="action" value="increase">
                <button type="submit" class="quantity-btn" <?php if ($item['stock'] <= $item['quantity']) echo 'disabled'; ?>>+</button>
              </form>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="total-section">
      <div class="total-price">Total: ₹<?php echo number_format($total_price, 2); ?></div>
      <form action="billing.php" method="post">
        <button type="submit" class="checkout-btn">Proceed to Checkout</button>
      </form>
    </div>
  <?php endif; ?>
</div>

<footer>
  <div>© Artisoria v1.0</div>
  <div>
    <a href="privacy.php">Privacy</a>
    <a href="terms.php">Terms</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
  </div>
</footer>

</body>
</html>