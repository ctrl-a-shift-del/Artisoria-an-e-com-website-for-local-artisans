<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: index.php");
    exit();
}

include "database.php";

$seller_id = $_SESSION['user_id'];

// Fetch all seller products
$product_query = "SELECT * FROM products";
$product_result = mysqli_query($conn, $product_query);
$allProducts = [];
while ($row = mysqli_fetch_assoc($product_result)) {
    $allProducts[] = $row;
}

// Get new products (simply reverse chronological order)
$newProducts = array_reverse($allProducts);

// Get hot products (random products from all sellers)
$hotProducts = [];
$hot_query = "SELECT * FROM products ORDER BY RAND()";
$hot_result = mysqli_query($conn, $hot_query);
while ($row = mysqli_fetch_assoc($hot_result)) {
    $hotProducts[] = $row;
}

// Get trending products (based on popularity score)
$trendingProducts = [];

// Calculate popularity score for each product (orders + cart additions)
$trending_query = "SELECT p.*, 
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.product_id) * 3 AS order_count,
                   (SELECT COUNT(*) FROM cart c WHERE c.product_id = p.product_id) AS cart_count
                   FROM products p ORDER BY (order_count + cart_count) DESC";
$trending_result = mysqli_query($conn, $trending_query);

while ($row = mysqli_fetch_assoc($trending_result)) {
    $trendingProducts[] = $row;
}

// Add some randomness to the trending products (shuffle top 5)
if (count($trendingProducts) > 5) {
    $topTrending = array_slice($trendingProducts, 0, 5);
    shuffle($topTrending);
    $restTrending = array_slice($trendingProducts, 5);
    $trendingProducts = array_merge($topTrending, $restTrending);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Artisoria - Seller Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      height: 100%;
      font-family: 'Manrope', sans-serif;
      background-color: var(--black);
      color: var(--white);
      overflow-x: hidden;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color: rgb(36, 36, 36);
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

    .header-right {
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .explore-button {
      padding: 0.5rem 1rem;
      background-color: var(--black);
      border-color: white;
      color: var(--white);
      font-weight: 600;
      border-radius: 4px;
      text-decoration: none;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .explore-button:hover {
      background-color: var(--white);
      color: var(--coral);
    }

    .menu-button {
      background: var(--black);
      color: var(--white);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .menu-button:hover {
      background: var(--white);
      color: var(--black);
    }

    .slide-menu {
      position: fixed;
      top: 0;
      right: -260px;
      width: 260px;
      height: 100%;
      background-color: rgba(36, 36, 36, 0.95);
      padding: 4rem 1rem 1rem;
      transition: right 0.3s ease;
      z-index: 1002;
    }

    .slide-menu.open {
      right: 0;
    }

    .slide-menu a {
      display: block;
      margin: 0.5rem 0;
      padding: 0.75rem;
      background-color: var(--black);
      color: white;
      font-weight: 600;
      border-radius: 4px;
      text-align: center;
      font-family: 'Montserrat', sans-serif;
      text-decoration: none;
    }

    .slide-menu a:hover {
      background-color: var(--white);
      color: black;
    }

    .close-btn {
      position: absolute;
      top: 0.7rem;
      right: 0.6rem;
      background: var(--black);
      color: var(--white);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .backdrop {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.3);
      z-index: 1001;
      display: none;
    }

    .backdrop.show {
      display: block;
    }

    .content-wrapper {
      padding-top: 4rem;
      padding-bottom: 2rem;
    }

    .products-section {
      padding: 0.3rem;
    }

    .section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--white);
      margin: 0rem 0rem 0rem 0rem;
    }

    .product-container {
      margin: 0;
      padding: 0.35rem 0rem 0rem 0rem;
      width: 100%;
      display: flex;
      overflow-x: auto;
      gap: 5px;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }

    .product-container::-webkit-scrollbar {
      display: none;
    }

    .product-box {
      position: relative;
      width: 180px;
      height: 188px;
      background-color: rgb(86, 86, 90);
      border-radius: 4px;
      padding: 0;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      cursor: pointer;
      flex-shrink: 0;
      overflow: hidden;
    }

    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-info-bar {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: rgba(0, 0, 0, 1);
      padding: 2px 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .product-title, .product-price {
      font-size: 0.6rem;
      font-weight: 400;
      color: var(--white);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .product-title {
      max-width: 70%;
    }

    .product-box:hover {
      transform: translateY(-5px);
    }

    footer {
      background-color: var(--black);
      color: var(--white);
      padding: 0.3rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.7rem;
      position: relative;
      bottom: 0;
      width: 100%;
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

<!-- Slide-out menu -->
<div class="slide-menu" id="menu">
  <button class="close-btn" onclick="toggleMenu()">×</button>
  <a href="dashboard.php">Dashboard</a>
  <a href="my_products.php">My Products</a>
  <a href="my_orders.php">My Orders</a>
  <a href="account.php">Account</a>
</div>

<div class="backdrop" id="backdrop" onclick="toggleMenu()"></div>

<!-- Header -->
<header>
  <div class="header-left">
    <h1>Artisoria</h1>
    <p class="tagline">Helping local artisians...</p>
  </div>
  <div class="header-right">
    <a href="seller_explore.php" class="explore-button">Explore</a>
    <button class="menu-button" onclick="toggleMenu()">☰</button>
  </div>
</header>

<!-- Main content -->
<div class="content-wrapper">
  <section class="products-section">
    <h2 class="section-title">Hot</h2>
    <div class="product-container" id="hotContainer">
      <?php foreach ($hotProducts as $product): ?>
        <div class="product-box" onclick="window.location.href='seller_explore.php?product_id=<?= $product['product_id'] ?>'">
          <?php if ($product['image_1']): ?>
            <img src="<?= $product['image_1'] ?>" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
          <?php else: ?>
            <div class="product-image" style="background-color: #ccc;"></div>
          <?php endif; ?>
          <div class="product-info-bar">
            <div class="product-title">
              <?= htmlspecialchars(mb_strlen($product['name']) > 22 ? mb_substr($product['name'], 0, 22) . '...' : $product['name']) ?>
            </div>
            <div class="product-price">₹<?= number_format($product['price']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="products-section">
    <h2 class="section-title">Trending</h2>
    <div class="product-container" id="trendingContainer">
      <?php foreach ($trendingProducts as $product): ?>
        <div class="product-box" onclick="window.location.href='seller_explore.php?product_id=<?= $product['product_id'] ?>'">
          <?php if ($product['image_1']): ?>
            <img src="<?= $product['image_1'] ?>" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
          <?php else: ?>
            <div class="product-image" style="background-color: #ccc;"></div>
          <?php endif; ?>
          <div class="product-info-bar">
            <div class="product-title">
              <?= htmlspecialchars(mb_strlen($product['name']) > 22 ? mb_substr($product['name'], 0, 22) . '...' : $product['name']) ?>
            </div>
            <div class="product-price">₹<?= number_format($product['price']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="products-section">
    <h2 class="section-title">New</h2>
    <div class="product-container" id="newContainer">
      <?php foreach ($newProducts as $product): ?>
        <div class="product-box" onclick="window.location.href='seller_explore.php?product_id=<?= $product['product_id'] ?>'">
          <?php if ($product['image_1']): ?>
            <img src="<?= $product['image_1'] ?>" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
          <?php else: ?>
            <div class="product-image" style="background-color: #ccc;"></div>
          <?php endif; ?>
          <div class="product-info-bar">
            <div class="product-title">
              <?= htmlspecialchars(mb_strlen($product['name']) > 22 ? mb_substr($product['name'], 0, 22) . '...' : $product['name']) ?>
            </div>
            <div class="product-price">₹<?= number_format($product['price']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<!-- Footer -->
<footer>
  <div>© Artisoria v1.0</div>
  <div>
    <a href="privacy.php">Privacy</a>
    <a href="terms.php">Terms</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
  </div>
</footer>

<script>
  function toggleMenu() {
    document.getElementById('menu').classList.toggle('open');
    document.getElementById('backdrop').classList.toggle('show');
  }

  function setupAutoScroll(containerId) {
    const container = document.getElementById(containerId);
    const boxes = container.querySelectorAll('.product-box');
    if (boxes.length === 0) return;

    const scrollAmount = boxes[0].offsetWidth + 5;
    let index = 0;

    function scrollNext() {
      index = (index + 1) % (boxes.length - 2);
      container.scrollTo({
        left: index * scrollAmount,
        behavior: 'smooth'
      });
    }

    let scrollInterval = setInterval(scrollNext, 2000);

    container.addEventListener('mouseenter', () => clearInterval(scrollInterval));
    container.addEventListener('mouseleave', () => {
      scrollInterval = setInterval(scrollNext, 2000);
    });

    container.addEventListener('touchstart', () => clearInterval(scrollInterval));
    container.addEventListener('touchend', () => {
      scrollInterval = setInterval(scrollNext, 2000);
    });
  }

  setTimeout(() => setupAutoScroll('hotContainer'), 10);       // starts immediately
  setTimeout(() => setupAutoScroll('trendingContainer'), 1500);       // starts after 0.8s
  setTimeout(() => setupAutoScroll('newContainer'), 700);           // starts after 1.6s

</script>

</body>
</html>