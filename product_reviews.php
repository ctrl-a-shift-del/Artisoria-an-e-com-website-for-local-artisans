<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

$stmt = $conn->prepare("SELECT r.rating, r.review_text, u.name AS buyer_name FROM reviews r INNER JOIN users u ON r.buyer_id = u.user_id WHERE r.product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Product Reviews - Artisoria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
      --gray: #1a1a1a;
      --light-gray: #2a2a2a;
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
      display: flex;
      flex-direction: column;
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
      flex: 1;
      padding: 5rem 1rem 2rem;
      width: 100%;
      max-width: 600px;
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

    .reviews-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      align-items: center;
    }

    .review-item {
      width: 100%;
      background-color: var(--gray);
      border-radius: 4px;
      padding: 1rem;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      word-wrap: break-word;
    }

    .review-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
    }

    .buyer-name {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--coral);
    }

    .star-rating {
      font-size: 1rem;
      color: var(--coral);
      letter-spacing: 1px;
    }

    .review-text {
      font-size: 0.8rem;
      color: var(--white);
      margin-top: 0.5rem;
      line-height: 1.3;
      white-space: pre-wrap;
    }

    .no-reviews {
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
        padding: 5rem 1rem 2rem;
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
  
</header>

<div class="content-wrapper">
  <h2 class="section-title">Product Reviews</h2>
  <?php if (!empty($reviews)): ?>
    <div class="reviews-list">
      <?php foreach ($reviews as $review): ?>
        <div class="review-item">
          <div class="review-header">
            <span class="buyer-name"><?php echo htmlspecialchars($review['buyer_name']); ?></span>
            <span class="star-rating">
              <?php
                $rating = (int)$review['rating'];
                for ($i = 1; $i <= 5; $i++) {
                  echo $i <= $rating ? '★' : '☆';
                }
              ?>
            </span>
          </div>
          <div class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="no-reviews">No reviews yet for this product.</p>
  <?php endif; ?>
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

</body>
</html>
