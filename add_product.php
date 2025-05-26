<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Product - Artisoria</title>
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
      transition: all 0.3s ease;
    }

    .back-button:hover {
      background: var(--white);
      color: var(--black);
    }

    .content-wrapper {
      padding: 5rem 1rem 2rem;
      max-width: 700px;
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

    .form-container {
      background-color: var(--gray);
      border-radius: 4px;
      padding: 0.5rem;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    label {
      display: block;
      font-size: 0.8rem;
      font-weight: 500;
      margin-bottom: 0.3rem;
      color: var(--white);
    }

    input[type="text"],
    input[type="number"],
    textarea,
    input[type="file"] {
      width: 100%;
      background-color: var(--black);
      color: var(--white);
      border: 1px solid var(--gray);
      border-radius: 4px;
      padding: 0.6rem;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    button {
      width: 100%;
      padding: 0.6rem;
      background-color: var(--black);
      color: var(--white);
      border-radius: 4px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background-color: var(--white);
      color: var(--coral);
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
        padding: 5rem 1rem 2rem;
      }
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header>
  <div class="header-left">
    <h1>Artisoria</h1>
    <p class="tagline">Helping local artisians...</p>
  </div>
  <a href="my_products.php" class="back-button">&#x3c;</a>
</header>

<div class="content-wrapper">
  <h2 class="section-title">Add a New Product</h2>

  <div class="form-container">
    <form id="addProductForm" enctype="multipart/form-data">
      <label>Product Name:</label>
      <input type="text" name="name" required maxlength="50">

      <label>Price:</label>
      <input type="number" name="price" step="0.01" min="0" max="10000000" required>

      <label>Description:</label>
      <textarea name="description" required maxlength="500"></textarea>

      <label>Stock Amount:</label>
      <input type="number" name="stock" min="0" max="999999" required>

      <label><strong>Upload Images:</strong></label>
      <?php for ($i = 1; $i <= 10; $i++): ?>
        <label>Image <?php echo $i; ?>:</label>
        <input type="file" name="image_<?php echo $i; ?>" accept="image/*">
      <?php endfor; ?>

      <button type="submit">Add Product</button>
    </form>
  </div>
</div>

<footer>
  <div>© 2025 Artisoria</div>
  <div>
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
    <a href="#">About</a>
    <a href="#">Contact</a>
  </div>
</footer>

<script>
  $(document).ready(function(){
    $("#addProductForm").submit(function(event){
      event.preventDefault();

      $.ajax({
        url: "add_product_process.php",
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response){
          const trimmedResponse = response.trim();
          if (trimmedResponse === "Product added successfully!") {
            alert(trimmedResponse);
            window.location.href = "my_products.php";
          } else {
            alert(trimmedResponse);
          }
        },

        error: function(){
          alert("An error occurred. Please try again.");
        }
      });
    });
  });
</script>


</body>
</html>
