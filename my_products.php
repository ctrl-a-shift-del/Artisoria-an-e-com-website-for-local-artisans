<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header("Location: index.php");
    exit();
}

$seller_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Products - Artisoria</title>
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

    .header-right {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .back-button {
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
      transition: all 0.3s ease;
    }

    .back-button:hover {
      background: var(--white);
      color: var(--black);
    }

    .add-product {
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

    .add-product:hover {
      background-color: var(--white);
      color: var(--coral);
    }

    .content-wrapper {
      padding: 5rem 1rem 2rem;
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      min-height: calc(100vh - 120px);
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      color: var(--white);
      margin-bottom: 0.75rem;
      text-align: center;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .product-item {
      background-color: var(--gray);
      border-radius: 4px;
      padding: 0.5rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .product-name {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--coral);
      margin-bottom: 0.5rem;
    }

    .product-content {
      display: flex;
      gap: 1rem;
      margin-bottom: 0.5rem;
    }

    .product-image-main {
      width: 100px;
      height: 100px;
      border-radius: 4px;
      object-fit: cover;
      background-color: var(--lighter-gray);
    }

    .product-details {
      flex: 1;
      font-size: 0.8rem;
      color: var(--white);
    }

    .product-details p {
      margin: 0.1rem 0;
    }

    .product-images {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin: 0.5rem 0;
    }

    .product-image {
      width: 50px;
      height: 50px;
      border-radius: 4px;
      object-fit: cover;
      background-color: var(--lighter-gray);
    }

    .product-actions {
      display: flex;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }

    .action-btn {
      flex: 1;
      background-color: var(--black);
      color: var(--white);
      border: none;
      padding: 0.5rem;
      border-radius: 4px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
      font-size: 0.7rem;
      transition: all 0.3s ease;
    }

    .action-btn:hover {
      background-color: var(--white);
      color: var(--black);
    }

    .no-products {
      text-align: center;
      font-size: 0.8rem;
      margin-top: 2rem;
      color: var(--text-gray);
      grid-column: 1 / -1;
    }

    .stars {
      color: var(--coral);
      font-size: 0.85rem;
      letter-spacing: 0.1rem;
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
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
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
      .products-grid {
        grid-template-columns: 1fr;
      }
      
      .product-image-main {
        width: 80px;
        height: 80px;
      }
      
      .product-image {
        width: 40px;
        height: 40px;
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
  <div class="header-right">
    <a href="add_product.php" class="add-product">Add Product</a>
    <a href="seller_home.php" class="back-button">&#x3c;</a>
  </div>
</header>

<div class="content-wrapper">
  <h2 class="section-title">My Products</h2>

  <?php if ($result->num_rows > 0): ?>
    <ul class="products-grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <li class="product-item" id="product-<?php echo $row['product_id']; ?>">
          <div class="product-name"><?php echo htmlspecialchars($row['name']); ?></div>
          
          <div class="product-content">
            <?php if (!empty($row['image_1'])): ?>
              <img src="<?php echo htmlspecialchars($row['image_1']); ?>" alt="Product Image" class="product-image-main">
            <?php else: ?>
              <div class="product-image-main"></div>
            <?php endif; ?>
            
            <div class="product-details">
              <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($row['price']); ?></p>
              
              <p><strong>Description:</strong> 
                <?php 
                  $shortDesc = mb_substr($row['description'], 0, 20);
                  echo htmlspecialchars($shortDesc) . (mb_strlen($row['description']) > 20 ? '...' : '');
                ?>
              </p>
              <p><strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?></p>
              <?php
              // Fetch average rating for this product
              $stmt_rating = $conn->prepare("SELECT AVG(rating) AS average_rating FROM reviews WHERE product_id = ?");
              $stmt_rating->bind_param("i", $row['product_id']);
              $stmt_rating->execute();
              $stmt_rating->bind_result($average_rating);
              $stmt_rating->fetch();
              $stmt_rating->close();

              if ($average_rating !== null) {
                $rounded = round($average_rating);
                echo '<p><strong>Rating:</strong> <span class="stars">';
                for ($i = 1; $i <= 5; $i++) {
                  echo $i <= $rounded ? '★' : '☆';
                }
                echo '</span></p>';
              } else {
                echo '<p><strong>Rating:</strong> No ratings yet</p>';
              }
              ?>
            </div>
          </div>
          
          <div class="product-images">
            <?php
            for ($i = 2; $i <= 10; $i++) {
                $imageColumn = "image_$i";
                if (!empty($row[$imageColumn])) {
                    echo '<img src="' . htmlspecialchars($row[$imageColumn]) . '" alt="Product Image" class="product-image">';
                }
            }
            ?>
          </div>
          
          <div class="product-actions">
            <a href="product_reviews.php?product_id=<?php echo $row['product_id']; ?>" class="action-btn" target="_blank">Reviews</a>
            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="action-btn">Edit</a>
            <a href="#" class="action-btn" onclick="deleteProduct(<?php echo $row['product_id']; ?>); return false;">Delete</a>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="no-products">No products found. Add your first product!</p>
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

<script>
  function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
      fetch(`delete_product.php?id=${productId}`, {
        method: 'POST', // Changed from DELETE to POST
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        credentials: 'include' // Include cookies/session
      })
      .then(response => {
        if (!response.ok) {
          return response.text().then(text => { throw new Error(text || 'Request failed') });
        }
        return response.json();
      })
      .then(data => {
        if (data.status === 'success') {
          alert(data.message);
          document.getElementById(`product-${productId}`).remove();
        
          // If no products left, show the "no products" message
          if (document.querySelectorAll('.product-item').length === 0) {
            document.querySelector('.products-grid').innerHTML = 
              '<p class="no-products">No products found. Add your first product!</p>';
          }
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Failed to delete product. Please try again.');
      });
    }
  }
</script>

<?php
$stmt->close();
$conn->close();
?>
</body>
</html>