<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: index.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch orders for seller's products
$stmt = $conn->prepare("
    SELECT o.order_id, o.buyer_id, o.status, o.total_price, o.address, o.phone_number, 
           oi.product_id, oi.quantity, oi.price, p.name AS product_name, u.name AS buyer_name, p.image_1
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    JOIN users u ON o.buyer_id = u.user_id
    WHERE p.seller_id = ?
    ORDER BY o.order_id DESC
");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

// Group orders by order_id
$orders = [];
while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'order_id' => $order_id,
            'buyer_name' => $row['buyer_name'],
            'status' => $row['status'],
            'total_price' => $row['total_price'],
            'address' => $row['address'],
            'phone_number' => $row['phone_number'],
            'items' => []
        ];
    }
    $orders[$order_id]['items'][] = [
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'image_1' => $row['image_1']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Orders - Artisoria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
      --coral-dark: #d6484d;
      --gray: #1a1a1a;
      --light-gray: #2a2a2a;
      --lighter-gray: #3a3a3a;
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
      position: relative;
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
      position: fixed;
      top: 0.7rem;
      right: 0.5rem;
      background-color: var(--black);
      color: var(--white);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 1.5rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      z-index: 1001;
      transition: all 0.3s ease;
    }

    .back-button:hover {
      background: var(--white);
      color: var(--black);
    }

    .content-wrapper {
      padding: 5rem 0rem 2rem;
      width: 100%;
      max-width: 450px;
      margin: 0 auto;
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      color: var(--white);
      margin-bottom: 0.75rem;
      text-align: center;
    }

    .orders-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .order-item {
      background-color: var(--gray);
      border-radius: 4px;
      padding: 0.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .order-header {
      display: flex;
      justify-content: flex-start;
      margin-bottom: 0rem;
      flex-direction: column;
    }

    .order-id, .order-total, .order-status {
      font-size: 0.8rem;
      margin-bottom: 0rem;
    }

    .order-id{
      color: var(--coral);
      font-size: 0.8rem;
      font-weight: 600;
    }

    .order-total{
      color: var(--coral);
      font-size: 0.8rem;
    }
    .order-status {
      color: var(--white);
      font-size: 0.8rem;
    }

    .order-meta {
      width: 100%;
      margin-top: 0.5rem;
      font-size: 0.8rem;
      color: var(--white);
    }

    .order-items-title {
      font-family: 'Manrope', sans-serif;
      font-size: 0.8rem;
      margin: 1.5rem 0 0rem;
      color: var(--white);
      padding-bottom: 0.5rem;
    }

    .product-item {
      display: flex;
      flex-wrap: wrap;
      background-color: var(--light-gray);
      border-radius: 4px;
      padding: 0.5rem;
      margin-bottom: 0.5rem;
      gap:1rem;
    }

    .product-image {
      width: 100px;
      height: 100px;
      border-radius: 4px;
      object-fit: cover;
      background-color: var(--lighter-gray);
    }

    .product-info {
      min-width: 0px;
      font-size: 0.8rem;
    }

    .product-name {
      font-size: 0.8rem;
      margin-bottom: 0rem;
      color: var(--white);
    }

    .product-details {
      font-size: 0.8rem;
      color: var(--white);
      margin-bottom: 0.5rem;
    }

    .complete-btn {
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

    .complete-btn:hover {
      background-color: var(--white);
      color: var(--coral);
    }

    .complete-btn:disabled {
      background-color: var(--gray);
      cursor: not-allowed;
      opacity: 0.5;
    }

    .no-orders {
      text-align: center;
      font-size: 0.8rem;
      margin-top: 2rem;
      color: var(--text-gray);
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

    @media (max-width: 600px) {
      .content-wrapper {
        padding: 5rem 1.5rem 2rem;
      }
      
      .order-item {
        padding: 0.5rem;
      }
      
      .product-item {
        flex-direction: row;
        gap: 0.5rem;
      }
      
      .product-image {
        width: 50px;
        height: 50px;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="header-left">
    <h1>Artisoria</h1>
    <p class="tagline">Helping local artisians...</p>
  </div>
  <a href="seller_home.php" class="back-button">&#x3c;</a>
</header>

<div class="content-wrapper">
  <h2 class="section-title">My Orders</h2>

  <?php if (!empty($orders)): ?>
    <ul class="orders-list">
      <?php foreach ($orders as $order): ?>
        <li class="order-item">
          <div class="order-header">
            <span class="order-id">Order ID:<?php echo $order['order_id']; ?></span>
            <span class="order-status">Status:<?php echo ucfirst($order['status']); ?></span>
            <span class="order-total">Price: ₹<?php echo number_format($order['total_price'], 2); ?></span>
          </div>
          
          <div class="order-meta">
            <div><strong>Buyer:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?></div>
            <div><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></div>
            <div><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></div>
          </div>

          <h4 class="order-items-title">Order Items</h4>
          <?php foreach ($order['items'] as $item): ?>
            <div class="product-item">
              <img src="<?php echo htmlspecialchars($item['image_1'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-image">
              
              <div class="product-info">
                <div class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                <div class="product-details">
                  <div>Quantity: <?php echo $item['quantity']; ?></div>
                  <div>Price: ₹<?php echo number_format($item['price'], 2); ?></div>
                  <div>Subtotal: ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if ($order['status'] === 'pending'): ?>
            <form method="POST" action="update_order_status.php">
              <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
              <button type="submit" class="complete-btn">Mark as Completed</button>
            </form>
          
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="no-orders">No orders found for your products.</p>
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

<?php
$stmt->close();
$conn->close();
?>
</body>
</html>