<?php
session_start();
include "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $user_type = $_POST["user_type"]; // Buyer or Seller

    // First check if email already exists using prepared statement
    $check_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('An account with this email already exists. Please use a different email or login.');</script>";
    } else {
        // Email doesn't exist, proceed with secure signup
        $query = "INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $name, $email, $password, $user_type);
        
        if ($stmt->execute()) {
            echo "<script>alert('Signup Successful! Redirecting to login...'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error during signup. Please try again.');</script>";
        }
    }
}

$bgProductsStmt = $conn->query("SELECT * FROM products LIMIT 30");
$bgProducts = $bgProductsStmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Artisoria - Sign Up</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --black: #000000;
      --dark-gray: #333333;
      --white: #ffffff;
      --coral: #FF5A5F;
      --coral-dark: #d6484d;
      --coral-light: #ffd6d6;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      height: 100%;
    }

    body {
      font-family: 'Manrope', sans-serif;
      color: var(--black);
      min-height: 100%;
      position: relative;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .background-container {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: -1;
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
      position: relative;
      overflow: hidden;
      flex-shrink: 0;
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

    .background-product-title {
      font-size: 0.5rem;
      color: var(--white);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 60%;
    }

    .background-product-price {
      font-size: 0.5rem;
      color: var(--white);
    }

    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: -1;
    }

    .content-wrapper {
      position: relative;
      z-index: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color: rgba(0, 0, 0, 1);
      padding: 0.5rem 0;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    header h1 {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.6rem;
      font-weight: 900;
      color: var(--coral);
      line-height: 0.7;
      gap: 0rem;
      margin-bottom: 0rem;
    }

    header .tagline {
      font-family: 'Manrope', sans-serif;
      font-size: 0.7rem;
      font-weight: 100;
      color: var(--white);
      margin: 0;
    }

    main {
      padding: 4rem 1rem 4rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .form-container {
      background-color: rgba(255, 255, 255, 0.41);
      padding: 1rem;
      border-radius: 10px;
      width: 100%;
      max-width: 340px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
    }

    .form-title {
      font-family: 'Manrope', sans-serif;
      font-size: 1.5rem;
      font-weight: 500;
      margin-bottom: 1rem;
      color: var(--white);
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    input {
      font-family: 'Manrope', sans-serif;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      border-radius: 8px;
      border: 2px solid #ccc;
      color: var(--black);
    }

    input:focus {
      outline: none;
      background-color:rgba(249, 249, 249, 0.63);
    }

    input::placeholder {
      color: #777;
    }

    .toggle-container {
      display: flex;
      gap: 0.5rem;
      justify-content: space-between;
    }

    .toggle-container label {
      flex: 1;
    }

    .toggle-container input[type="radio"] {
      display: none;
    }

    .toggle-label {
      display: block;
      text-align: center;
      padding: 0.5rem 0.75rem;
      font-weight: 600;
      background-color: var(--black);
      color: var(--white);
      border-radius: 8px;
      border: none !important;
      transition: background-color 0.3s ease, color 0.3s ease;
      cursor: pointer;
    }

    .toggle-container input[type="radio"]:checked + .toggle-label {
      background-color: var(--white);
      color: var(--black);
      font-weight: 700;
    }

    .toggle-container input[type="radio"]:not(:checked) + .toggle-label:hover {
      background-color: var(--white);
      color: var(--black);
      border: none;
    }

    button {
      font-family: 'Montserrat', sans-serif;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      font-weight: 600;
      color: var(--white);
      background-color: var(--black);
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    button:hover {
      background-color: var(--white);
      color: var(--coral);
    }

    .login-link {
      text-align: center;
      margin-top: 1.5rem;
      font-weight: 500;
      text-decoration: none;
      color: var(--white);
      cursor: pointer;
      width: 100%;
      display: block;
    }

    .login-link:hover {
      text-decoration: underline;
    }

    footer {
      background-color: var(--black);
      color: var(--white);
      padding: 0.2rem 0.2rem;
      font-family: 'Montserrat', sans-serif;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: 600;
      font-size: 12px;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      width: 100%;
      box-sizing: border-box;
      z-index: 1000;
    }

    footer a {
      color: var(--coral);
      margin-left: 1rem;
      text-decoration: none;
      font-weight: 600;
      font-size: 12px;
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
                <img src="<?= htmlspecialchars($product['image_1']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
              <?php else: ?>
                <div style="width: 100%; height: 100%; background-color: #ddd; display: flex; align-items: center; justify-content: center;">
                  <span style="font-size: 0.6rem;">No Image</span>
                </div>
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

  <header>
    <h1>Artisoria</h1>
    <p class="tagline">Helping local artisans...</p>
  </header>


  <div class="content-wrapper">
    <main>
      <div class="form-container">
        <p class="form-title">Sign Up</p>

        <form method="POST">
          <input type="text" name="name" required placeholder="Enter Name">
          <input type="email" name="email" required placeholder="Enter Email">
          <input type="password" name="password" required placeholder="Enter Password" autocomplete="new-password">

          <div class="toggle-container">
            <label>
              <input type="radio" name="user_type" value="buyer" checked>
              <div class="toggle-label">Buyer</div>
            </label>
            <label>
              <input type="radio" name="user_type" value="seller">
              <div class="toggle-label">Seller</div>
            </label>
          </div>

          <button type="submit">Sign Up</button>
        </form>
      </div>
      
      <a href="index.php" class="login-link">Already have an account? Login</a>
    </main>

    <footer>
      <div>© Artisoria v1.0</div>
      <div>
        <a href="#" class="under-construction">Privacy</a>
        <a href="#" class="under-construction">Terms</a>
        <a href="#" class="under-construction">About</a>
        <a href="#" class="under-construction">Contact</a>
      </div>
    </footer>
  </div>

  <script>
    document.querySelectorAll('.under-construction').forEach(elem => {
      elem.addEventListener('click', function(event) {
        event.preventDefault();
        alert('Under Construction');
      });
    });
  </script>

</body>
</html>