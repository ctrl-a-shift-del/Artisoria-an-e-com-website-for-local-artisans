<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: index.php");
    exit();
}

include "database.php";

$seller_id = $_SESSION['user_id'];

// Fetch total revenue
$revenue_query = "SELECT SUM(oi.price * oi.quantity) AS total_revenue 
                  FROM order_items oi
                  INNER JOIN products p ON oi.product_id = p.product_id
                  WHERE p.seller_id = $seller_id";
$revenue_result = mysqli_query($conn, $revenue_query);
$revenue_data = mysqli_fetch_assoc($revenue_result);
$total_revenue = $revenue_data['total_revenue'] ?? 0;

// Fetch total products sold
$products_sold_query = "SELECT COUNT(*) AS total_products_sold 
                        FROM order_items oi
                        INNER JOIN products p ON oi.product_id = p.product_id
                        WHERE p.seller_id = $seller_id";
$products_sold_result = mysqli_query($conn, $products_sold_query);
$products_sold_data = mysqli_fetch_assoc($products_sold_result);
$total_products_sold = $products_sold_data['total_products_sold'] ?? 0;

// Fetch top buyers
$buyers_query = "SELECT u.name, u.email, COUNT(oi.order_item_id) AS total_orders
                 FROM order_items oi
                 INNER JOIN orders o ON oi.order_id = o.order_id
                 INNER JOIN users u ON o.buyer_id = u.user_id
                 INNER JOIN products p ON oi.product_id = p.product_id
                 WHERE p.seller_id = $seller_id
                 GROUP BY u.user_id
                 ORDER BY total_orders DESC";
$buyers_result = mysqli_query($conn, $buyers_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - Artisoria</title>
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
      padding: 5rem 1rem 2rem;
      flex: 1;
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 0.75rem;
      text-align: center;
    }

    .dashboard-stats {
      background-color: var(--gray);
      padding: 1rem;
      border-radius: 4px;
      margin-bottom: 1.5rem;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .dashboard-stats p {
      font-size: 0.8rem;
      margin: 0.4rem 0;
    }

    .buyer-table {
      background-color: var(--gray);
      border-radius: 4px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.8rem;
    }

    th, td {
      padding: 0.6rem;
      text-align: center;
      border: 1px solid var(--light-gray);
    }

    th {
      background-color: var(--light-gray);
      color: var(--white);
      font-weight: 600;
      font-family: 'Montserrat', sans-serif;
    }

    tr:hover {
      background-color: var(--light-gray);
    }

    .no-buyers {
      text-align: center;
      font-size: 0.85rem;
      color: var(--text-gray);
      margin-top: 1rem;
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

      table, th, td {
        font-size: 0.75rem;
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
  <h2 class="section-title">Dashboard Overview</h2>

  <div class="dashboard-stats">
    <p><strong>Total Revenue:</strong> ₹<?php echo number_format($total_revenue, 2); ?></p>
    <p><strong>Total Products Sold:</strong> <?php echo $total_products_sold; ?></p>
  </div>

  <h2 class="section-title">Top Buyers</h2>
  <?php if (mysqli_num_rows($buyers_result) > 0): ?>
    <div class="buyer-table">
      <table>
        <thead>
          <tr>
            <th>Buyer Name</th>
            <th>Email</th>
            <th>Total Orders</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($buyer = mysqli_fetch_assoc($buyers_result)): ?>
            <tr>
              <td><?php echo htmlspecialchars($buyer['name']); ?></td>
              <td><?php echo htmlspecialchars($buyer['email']); ?></td>
              <td><?php echo $buyer['total_orders']; ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="no-buyers">No buyer data available yet.</p>
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
