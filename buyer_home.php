<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}

include "database.php";

$buyer_id = $_SESSION['user_id'];

// Get all products
$product_query = "SELECT * FROM products";
$product_result = mysqli_query($conn, $product_query);
$allProducts = [];
while ($row = mysqli_fetch_assoc($product_result)) {
    $allProducts[] = $row;
}

// New Products (most recently added)
$newProducts = array_reverse($allProducts);

// Recommended Products (based on past buyer orders + cart → recommend other products from those sellers)
$recommendedProducts = [];

// Get sellers from buyer's cart and orders
$seller_ids = [];

$cart_sellers = mysqli_query($conn, "
    SELECT DISTINCT p.seller_id
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.buyer_id = $buyer_id
");
while ($row = mysqli_fetch_assoc($cart_sellers)) {
    $seller_ids[] = $row['seller_id'];
}

$order_sellers = mysqli_query($conn, "
    SELECT DISTINCT p.seller_id
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.buyer_id = $buyer_id
");
while ($row = mysqli_fetch_assoc($order_sellers)) {
    $seller_ids[] = $row['seller_id'];
}

$seller_ids = array_unique($seller_ids);

if (!empty($seller_ids)) {
    $seller_ids_str = implode(',', $seller_ids);

    // Fetch other products from those sellers
    $recommended_query = "SELECT * FROM products WHERE seller_id IN ($seller_ids_str) LIMIT 10";
    $recommended_result = mysqli_query($conn, $recommended_query);
    while ($row = mysqli_fetch_assoc($recommended_result)) {
        $recommendedProducts[] = $row;
    }
}

// Fallback: shuffle if no recommendation
if (empty($recommendedProducts)) {
    shuffle($allProducts);
    $recommendedProducts = array_slice($allProducts, 0, min(10, count($allProducts)));
}

// Trending Products (based on orders and cart)
$trendingProducts = [];
$trending_query = "
    SELECT p.*, 
    (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.product_id) * 3 AS order_count,
    (SELECT COUNT(*) FROM cart c WHERE c.product_id = p.product_id) AS cart_count
    FROM products p
    ORDER BY (order_count + cart_count) DESC
";
$trending_result = mysqli_query($conn, $trending_query);
while ($row = mysqli_fetch_assoc($trending_result)) {
    $trendingProducts[] = $row;
}

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
  <!-- Viewport meta tag for responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Artisoria - Buyer Home</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
    /* CSS variables for easy color management */
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
      --coral-dark: #d6484d;
      --coral-light: #ffd6d6;
      --bg-color: var(--black); /* Background color */
    }

    /* Reset default styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    /* Base styles for html and body */
    html, body {
      height: 100%;
      font-family: 'Manrope', sans-serif;
      background-color: var(--bg-color);
      position: relative;
      overflow-x: hidden;/* Prevent horizontal scroll */
      scrollbar-width: none;          /* Firefox */
      -ms-overflow-style: none;       /* IE 10+ */
      overflow-y: scroll;
    }

    body::-webkit-scrollbar {
        display: none;                  /* Chrome, Safari */
    }

    /* Header styles - completely redesigned */
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
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
      color: #FF5A5F;
      margin-bottom: 0rem;
      text-align: left;
    }

    header .tagline {
      font-family: 'Manrope', sans-serif;
      font-size: 0.6rem;
      font-weight: 500;
      color: var(--white);
      margin-top: 0rem;
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    /* Explore button in header */
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

    /* Menu button in header */
    .menu-button {
      background: var(--black);
      color: var(--white);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 1.2rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .menu-button:hover {
      background: var(--white);
      color: var(--black);
    }

    /* Slide menu styles */
    .slide-menu {
      position: fixed;
      top: 0;
      right: -260px; /* Changed from left to right */
      width: 260px;
      height: 100%;
      background-color: rgb(36, 36, 36, 0.9);
      padding: 4rem 1rem 1rem;
      transition: right 0.3s ease; /* Changed from left to right */
      z-index: 1002;
    }

    .slide-menu.open {
      right: 0; /* Changed from left to right */
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
      text-decoration: none;
      font-family: 'Montserrat', sans-serif;
    }

    .slide-menu a:hover {
      background-color: var(--white);
      color: black;
    }

    /* Close button styles */
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
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Backdrop styles for menu */
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

    /* Main content wrapper */
    .content-wrapper {
      padding-top: 4rem; /* Space for fixed header */
      padding-bottom: 2rem;
      position: relative;
      z-index: 1;
    }

    /* Products container */
    .products-section {
      padding: 0.3rem;
    }

    /* Section title */
    .section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--white);
      margin: 0rem 0rem 0rem 0rem;
    }

    /* Product container with horizontal scroll */
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

    /* Hide scrollbar in WebKit browsers */
    .product-container::-webkit-scrollbar {
      display: none;
    }

    /* Product card styles - UPDATED with unified bottom bar */
    .product-box {
      position: relative;
      
      width: 180px;
      height: 188px; /* Fixed height */
      background-color: rgb(86, 86, 90);
      border: 0px solid #ccc;
      border-radius: 4px;
      padding: 0rem;
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
      border-radius: 4px;
    }

    /* New unified bottom bar container */
    .product-info-bar {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: rgba(0, 0, 0, 1); /* Semi-transparent black */
      padding: 2px 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* Product name styles */
    .product-title {
      font-size: 0.6rem;
      font-weight: 400;
      color: var(--white);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 70%; /* Prevent price from being pushed off */
    }

    /* Product price styles */
    .product-price {
      font-size: 0.6rem;
      font-weight: 400;
      color: var(--white);
      white-space: nowrap;
      margin-left: 8px; /* Add some space between name and price */
    }

    .product-box:hover {
      transform: translateY(-5px);
    }

    /* Footer styles */
    footer {
      background-color: var(--black);
      color: var(--white);
      padding: 0.3rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.75rem;
      position: relative;
      margin-top: 0rem;
    }

    footer a {
      color: var(--coral);
      text-decoration: none;
      margin-left: 1rem;
      font-size: 0.7rem;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<!-- Slide-out menu (now opens from right) -->
<div class="slide-menu" id="menu">
  <button class="close-btn" onclick="toggleMenu()">×</button>
  <a href="buyer_cart.php">Cart</a>
  <a href="buyer_orders.php">Orders</a>
  <a href="account.php">Account</a>
</div>

<!-- Backdrop for menu -->
<div class="backdrop" id="backdrop" onclick="toggleMenu()"></div>

<!-- Redesigned fixed header -->
<header>
  <div class="header-left">
    <h1>Artisoria</h1>
    <p class="tagline">Discover art and support talented local artisans.</p>
  </div>
  <div class="header-right">
    <a href="buyer_explore.php" class="explore-button">Explore</a>
    <button class="menu-button" onclick="toggleMenu()">☰</button>
  </div>
</header>

<!-- Main content area -->
<div class="content-wrapper">
  <!-- Recommended Products section -->
  <section class="products-section">
    <h2 class="section-title">Recommended</h2>
    <div class="product-container" id="recommendedContainer">
      <?php foreach ($recommendedProducts as $product): ?>
        <div class="product-box" onclick="window.location.href='buyer_explore.php?product_id=<?= $product['product_id'] ?>'">
          <?php if ($product['image_1']): ?>
            <img src="<?= $product['image_1'] ?>" alt="<?= $product['name'] ?>" class="product-image">
          <?php else: ?>
            <div class="product-image" style="background-color: #ddd; display: flex; align-items: center; justify-content: center;">
              <span>No Image</span>
            </div>
          <?php endif; ?>
          <!-- Unified info bar at bottom -->
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

  <!-- Trending Products section -->
  <section class="products-section">
    <h2 class="section-title">Trending</h2>
    <div class="product-container" id="trendingContainer">
      <?php foreach ($trendingProducts as $product): ?>
        <div class="product-box" onclick="window.location.href='buyer_explore.php?product_id=<?= $product['product_id'] ?>'">
          <?php if ($product['image_1']): ?>
            <img src="<?= $product['image_1'] ?>" alt="<?= $product['name'] ?>" class="product-image">
          <?php else: ?>
            <div class="product-image" style="background-color: #ddd; display: flex; align-items: center; justify-content: center;">
              <span>No Image</span>
            </div>
          <?php endif; ?>
          <!-- Unified info bar at bottom -->
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

  <!-- New Products section -->
  <section class="products-section">
    <h2 class="section-title">New</h2>
    <div class="product-container" id="newContainer">
      <?php foreach ($newProducts as $product): ?>
        <div class="product-box" onclick="window.location.href='buyer_explore.php?product_id=<?= $product['product_id'] ?>'">
          <?php if ($product['image_1']): ?>
            <img src="<?= $product['image_1'] ?>" alt="<?= $product['name'] ?>" class="product-image">
          <?php else: ?>
            <div class="product-image" style="background-color: #ddd; display: flex; align-items: center; justify-content: center;">
              <span>No Image</span>
            </div>
          <?php endif; ?>
          <!-- Unified info bar at bottom -->
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

<!-- Footer with About and Contact links -->
<footer>
  <div>© Artisoria v1.0</div>
  <div>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <a href="privacy.php">Privacy</a>
    <a href="terms.php" >Terms</a>
  </div>
</footer>

<script>
  // Toggle slide menu function
  function toggleMenu() {
    const menu = document.getElementById('menu');
    const backdrop = document.getElementById('backdrop');
    const isOpen = menu.classList.contains('open');
    if (isOpen) {
      menu.classList.remove('open');
      backdrop.classList.remove('show');
    } else {
      menu.classList.add('open');
      backdrop.classList.add('show');
    }
  }


  // Auto-scroll products every 2 seconds for each container
  function setupAutoScroll(containerId) {
    const container = document.getElementById(containerId);
    const productBoxes = container.querySelectorAll('.product-box');
    if (productBoxes.length === 0) return;
    
    const scrollAmount = productBoxes[0].offsetWidth + 5; // Width + gap
    let currentIndex = 0;
    
    function scrollToNextSet() {
      currentIndex = (currentIndex + 1) % (productBoxes.length - 2); // Show 3 at a time
      container.scrollTo({
        left: currentIndex * scrollAmount,
        behavior: 'smooth'
      });
    }

    // Start auto-scroll
    let scrollInterval = setInterval(scrollToNextSet, 2000);

    // Pause auto-scroll on hover/touch
    container.addEventListener('mouseenter', () => clearInterval(scrollInterval));
    container.addEventListener('mouseleave', () => {
      scrollInterval = setInterval(scrollToNextSet, 2000);
    });
    
    // Touch events for mobile
    container.addEventListener('touchstart', () => clearInterval(scrollInterval));
    container.addEventListener('touchend', () => {
      scrollInterval = setInterval(scrollToNextSet, 2000);
    });
  }

  // Initialize auto-scroll for all containers
  setTimeout(() => setupAutoScroll('recommendedContainer'), 8);       // starts immediately
  setTimeout(() => setupAutoScroll('trendingContainer'), 1600);       // starts after 0.8s
  setTimeout(() => setupAutoScroll('newContainer'), 900);
</script>

</body>
</html>