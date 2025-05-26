<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "database.php";

$user_id = $_SESSION["user_id"];
$query = "SELECT name, email FROM users WHERE user_id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$bgProductsStmt = $conn->query("SELECT * FROM products LIMIT 30");
$bgProducts = $bgProductsStmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Account - Artisoria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
      --coral-dark: #d6484d;
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
      overflow: hidden;
      position: relative;
    }

    .background-container {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      z-index: -2;
      background-color: var(--black);
      overflow: hidden;
    }

    .background-scroll {
      position: absolute;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      gap: 20px;
      padding: 20px 0;
    }

    .background-row {
      display: flex;
      gap: 10px;
      width: max-content;
    }

    .background-row:nth-child(odd) {
      animation: scrollRight 60s linear infinite;
    }
    .background-row:nth-child(even) {
      animation: scrollLeft 60s linear infinite;
    }

    @keyframes scrollRight {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }

    @keyframes scrollLeft {
      0% { transform: translateX(-50%); }
      100% { transform: translateX(0); }
    }

    .background-product {
      width: 225px;
      height: 225px;
      background-color: rgba(86, 86, 90, 0.8);
      border-radius: 4px;
      overflow: hidden;
      flex-shrink: 0;
      position: relative;
    }

    .background-product img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .background-info-bar {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: rgba(0, 0, 0, 0.8);
      padding: 2px 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .background-product-title, .background-product-price {
      font-size: 0.5rem;
      color: var(--white);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: -1;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color:  rgb(36,36,36);
      padding: 0.5rem 0;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
    }

    header h1 {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.5rem;
      font-weight: 900;
      color: var(--coral);
      line-height: 1;
      gap: 0.9rem;
      margin-bottom: 0rem;
      margin-left: 0.5rem;
    }

    header .tagline {
      font-family: 'Manrope', sans-serif;
      font-size: 0.6rem;
      font-weight: 100;
      color: var(--white);
      margin: 0;
      margin-left: 0.5rem;
    }

    .back-button {
      position: fixed;
      top: 0.8rem;
      right: 0.6rem;
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
      background-color: var(--white);
      color: var(--black);
    }

    .content-wrapper {
      padding-top: 5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      color: var(--white);
      font-size: 1rem;
      margin-bottom: 0.75rem;
    }

    .gray-box {
      background-color: rgba(255, 255, 255, 0.1);
      padding: 0.7rem;
      border-radius: 4px;
      width: 90%;
      max-width: 350px;
      margin-bottom: 4rem;
    }

    .gray-box p {
      font-size: 0.8rem;
      font-family: 'Manrope', sans-serif;
      color: var(--white);
      margin-bottom: 0rem;
    }

    .button-group {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    button {
      font-family: 'Montserrat', sans-serif;
      padding: 0.5rem;
      font-size: 1rem;
      border-radius: 4px;
      border: none;
      background-color: var(--black);
      color: var(--white);
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
    }

    button:hover {
      background-color: var(--white);
      color: var(--black);
    }

    button.delete {
      border: 1px coral;
      color: var(--coral);
    }

    button.delete:hover {
      background-color: var(--coral);
      color: black;
      box-shadow: 0 0 10px var(--coral);
    }

    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: var(--black);
      color: var(--white);
      padding: 0.5rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.75rem;
    }

    footer a {
      color: var(--coral);
      margin-left: 1rem;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="background-container">
  <div class="background-scroll">
    <?php for ($i = 0; $i < 4; $i++): ?>
      <div class="background-row">
        <?php for ($j = 0; $j < 3; $j++): foreach ($bgProducts as $product): ?>
          <div class="background-product">
            <?php if ($product['image_1']): ?>
              <img src="<?= $product['image_1'] ?>" alt="<?= $product['name'] ?>">
            <?php else: ?>
              <div style="display:flex;align-items:center;justify-content:center;height:100%;color:white;font-size:0.6rem;">No Image</div>
            <?php endif; ?>
            <div class="background-info-bar">
              <div class="background-product-title"><?= htmlspecialchars($product['name']) ?></div>
              <div class="background-product-price">₹<?= number_format($product['price']) ?></div>
            </div>
          </div>
        <?php endforeach; endfor; ?>
      </div>
    <?php endfor; ?>
  </div>
</div>

<div class="overlay"></div>

<a href="<?= $_SESSION["user_type"] === 'buyer' ? 'buyer_home.php' : 'seller_home.php' ?>" class="back-button">&#x3c;</a>

<header>
  <h1>Artisoria</h1>
  <p class="tagline">Helping local artisans...</p>
</header>

<div class="content-wrapper">
  <h2 class="section-title">Account details</h2>
  <div class="gray-box">
    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
  </div>

  <h2 class="section-title">Actions</h2>
  <div class="gray-box button-group">
    <form method="POST" action="logout.php">
      <button type="submit">Logout</button>
    </form>
    <form method="POST" action="delete_account.php" onsubmit="return confirm('Are you sure you want to delete your account?');">
      <button type="submit" name="delete_account" class="delete">Delete Account</button>
    </form>
  </div>
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
