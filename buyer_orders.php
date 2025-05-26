<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Handle adding a new review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    // Check if the user has already reviewed this product
    $check_stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ? AND buyer_id = ?");
    $check_stmt->bind_param("ii", $product_id, $buyer_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        // Insert the review into the database
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, buyer_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $product_id, $buyer_id, $rating, $review_text);
        $stmt->execute();
        $stmt->close();
    }

    $check_stmt->close();

    // Redirect to the same page to see the updated orders
    header("Location: buyer_orders.php");
    exit();
}

// Fetch all orders for the buyer
$stmt = $conn->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY order_id DESC");
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
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
      --gray: #1a1a1a; /* Darker gray for better contrast */
      --light-gray: #2a2a2a; /* Slightly lighter gray */
      --lighter-gray: #3a3a3a; /* For lighter elements */
      --text-gray: #cccccc; /* Light gray for text */
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      display: flex;
      flex-direction: column;
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
      padding: 5rem 1rem 2rem; /* Added more top padding for header */
      width: 100%;
      max-width: 550px; /* Limiting max width for better readability */
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
      background-color: var(--lighter-gray); /* Placeholder color if no image */
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

    .review-section {
      width: 150px;
      height: 100%;
      margin-top: 0rem;
      margin-left: auto;
    }

    .review-form {
      background-color: var(--lighter-gray);
      padding: 0.2rem;
      border-radius: 4px;
      margin-top: 0rem;
    }

    .form-group {
      margin-bottom: 0rem;
    }

    label {
      display: block;
      margin-bottom: 0rem;
      font-size: 0.8rem;
      color: var(--white);
    }

    .star-rating {
      display: flex;
      flex-direction: row-reverse;
      justify-content: flex-end;
    }

    .star-rating input {
      display: none;
    }

    .star-rating label {
      font-size: 1.5rem;
      color: var(--gray);
      cursor: pointer;
      padding: 0 0.1rem;
    }

    .star-rating input:checked ~ label,
    .star-rating input:hover ~ label {
      color: var(--coral);
    }

    textarea {
      width: 100%;
      padding: 0.5rem;
      border-radius: 4px;
      border: 1px solid var(--lighter-gray);
      background-color: var(--light-gray);
      color: var(--white);
      font-family: 'Manrope', sans-serif;
      resize: vertical;
      min-height: 50px;
    }

    button[type="submit"] {
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

    button[type="submit"]:hover {
      background-color: var(--white);
      color: coral;
    }

    .review-display {
      background-color: var(--lighter-gray);
      padding: 0.5rem;
      border-radius: 4px;
      margin-top: 0rem;
    }

    .review-rating {
      color: var(--white);
      margin-bottom: 0rem;
      font-size: 0.8rem;
    }

    .review-text {
      color: var(--white);
      font-size: 0.8rem;
      line-height: 1.2;
    }

    .no-orders {
      text-align: center;
      font-size: 0.8rem;
      margin-top: 2rem;
      color: var(--text-gray);
    }

    body > .content-wrapper {
      flex: 1 0 auto;
    }
    footer {
      flex-shrink: 0;
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

    /* Responsive adjustments */
    @media (max-width: 600px) {
      .content-wrapper {
        padding: 5rem 1rem 2rem;
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
  <a href="buyer_home.php" class="back-button">&#x3c;</a>
</header>

<div class="content-wrapper">
  <h2 class="section-title">My Orders</h2>

  <?php if ($result->num_rows > 0): ?>
    <ul class="orders-list">
      <?php while ($row = $result->fetch_assoc()): ?>
        <li class="order-item">
          <div class="order-header">
            <span class="order-id">Order ID:<?php echo $row['order_id']; ?></span>
            <span class="order-status"><?php echo ucfirst($row['status']); ?></span>
            <span class="order-total">₹<?php echo number_format($row['total_price'], 2); ?></span>
          </div>
          
          <div class="order-meta">
            <div><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></div>
            <div><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone_number']); ?></div>
          </div>

          <h4 class="order-items-title">Order Items</h4>
          <?php
          $order_id = $row['order_id'];
          $items_stmt = $conn->prepare("SELECT oi.*, p.name, p.image_1 FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
          $items_stmt->bind_param("i", $order_id);
          $items_stmt->execute();
          $items_result = $items_stmt->get_result();

          if ($items_result->num_rows > 0):
            while ($item = $items_result->fetch_assoc()): ?>
              <div class="product-item">
                <!-- Product image - added this new element -->
                <img src="<?php echo htmlspecialchars($item['image_1'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
                
                <div class="product-info">
                  <div class="product-name"><?php echo htmlspecialchars(mb_strimwidth($item['name'], 0, 20, "...")); ?></div>

                  <div class="product-details">
                    <div>Quantity: <?php echo $item['quantity']; ?></div>
                    <div>Price: ₹<?php echo number_format($item['price'], 2); ?></div>
                    <div>Subtotal: ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                  </div>
                </div>
                
                <?php
                // Check if the user has already reviewed this product
                $review_stmt = $conn->prepare("SELECT rating, review_text FROM reviews WHERE product_id = ? AND buyer_id = ?");
                $review_stmt->bind_param("ii", $item['product_id'], $buyer_id);
                $review_stmt->execute();
                $review_result = $review_stmt->get_result();
                $review = $review_result->fetch_assoc();
                $review_stmt->close();

                if ($review): ?>
                  <div class="review-section">
                    <div class="review-display">
                      <div class="review-rating">
                        Your Rating: 
                        <?php 
                        // Display star rating
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $review['rating'] ? '★' : '☆';
                        }
                        ?>
                      </div>
                      <div class="review-text">
                        <?php echo nl2br(htmlspecialchars(mb_strimwidth($review['review_text'], 0, 20, "..."))); ?>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="review-section">
                    <form class="review-form" action="buyer_orders.php" method="POST">
                      <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                      
                      <div class="form-group">
                        <label>Rating:</label>
                        <div class="star-rating">
                          <input type="radio" id="star5-<?php echo $item['product_id']; ?>" name="rating" value="5" required />
                          <label for="star5-<?php echo $item['product_id']; ?>">★</label>
                          <input type="radio" id="star4-<?php echo $item['product_id']; ?>" name="rating" value="4" />
                          <label for="star4-<?php echo $item['product_id']; ?>">★</label>
                          <input type="radio" id="star3-<?php echo $item['product_id']; ?>" name="rating" value="3" />
                          <label for="star3-<?php echo $item['product_id']; ?>">★</label>
                          <input type="radio" id="star2-<?php echo $item['product_id']; ?>" name="rating" value="2" />
                          <label for="star2-<?php echo $item['product_id']; ?>">★</label>
                          <input type="radio" id="star1-<?php echo $item['product_id']; ?>" name="rating" value="1" />
                          <label for="star1-<?php echo $item['product_id']; ?>">★</label>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="review_text-<?php echo $item['product_id']; ?>">Review:</label>
                        <textarea id="review_text-<?php echo $item['product_id']; ?>" name="review_text" required></textarea>
                      </div>
                      
                      <button type="submit">Submit Review</button>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            <?php endwhile;
          else:
            echo "<p>No items found in this order.</p>";
          endif;
          $items_stmt->close();
          ?>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="no-orders">No orders yet.</p>
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