<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout - Artisoria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
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

    header .tagline {
      font-size: 0.6rem;
    }

    .back-button {
      background: var(--black);
      color: var(--white);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 1.5rem;
      text-align: center;
      line-height: 32px;
      text-decoration: none;
    }

    .back-button:hover {
      background: var(--white);
      color: var(--black);
    }

    .content-wrapper {
      flex: 1;
      padding-top: 6rem;
      padding-bottom: 2rem;
      padding-left: 1rem;
      padding-right: 1rem;
      width: 100%;
      max-width: 450px;
      margin: 0 auto;
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 1rem;
    }

    .form-container {
      background-color: var(--gray);
      border-radius: 4px;
      padding: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    label {
      display: block;
      font-size: 0.8rem;
      margin-bottom: 0.5rem;
    }

    input[type="text"],
    input[type="tel"] {
      width: 100%;
      padding: 0.8rem;
      background-color: var(--black);
      border: 1px solid var(--light-gray);
      border-radius: 4px;
      color: var(--white);
      font-family: 'Manrope', sans-serif;
    }

    .checkout-btn {
      background-color: var(--black);
      color: var(--white);
      border: none;
      padding: 0.8rem;
      border-radius: 4px;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 1rem;
    }

    .checkout-btn:hover {
      background-color: var(--white);
      color: var(--coral);
    }

    footer {
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

    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: var(--gray);
      padding: 1rem 2rem;
      border-radius: 8px;
      color: white;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      z-index: 10000;
      box-shadow: 0 4px 10px rgba(0,0,0,0.6);
    }
  </style>
</head>
<body>

<header>
  <div class="header-left">
    <h1>Artisoria</h1>
    <p class="tagline">Helping local artisians...</p>
  </div>
  <a href="buyer_cart.php" class="back-button">&#x3c;</a>
</header>

<div class="content-wrapper">
  <h2 class="section-title">Shipping Details</h2>
  <form id="checkoutForm" action="place_order.php" method="post" class="form-container">
    <div class="form-group">
      <label>Flat, House no., Building, Company, Apartment*</label>
      <input type="text" id="address1" required>
    </div>
    <div class="form-group">
      <label>Area, Street, Sector, Village*</label>
      <input type="text" id="address2" required>
    </div>
    <div class="form-group">
      <label>Landmark (Optional)</label>
      <input type="text" id="landmark" placeholder="E.g. Near apollo hospital">
    </div>
    <div class="form-group">
      <label>Pincode*</label>
      <input type="text" id="pincode" required pattern="\d{5,6}" maxlength="6">
    </div>
    <div class="form-group">
      <label>Town/City*</label>
      <input type="text" id="city" name="city" required>
    </div>
    <div class="form-group">
      <label>State*</label>
      <input type="text" id="state" name="state" required>
    </div>

    <div class="form-group">
      <label>Phone Number*</label>
      <input type="tel" id="phone" name="phone_number" required pattern="[0-9]{10}" placeholder="10-digit number">
    </div>

    <input type="hidden" name="address" id="addressField">
    <p style="font-size: 0.85rem; margin-top: 1rem;">Payment:  <strong>Cash on Delivery (COD)</strong><br></p>
    <button type="submit" class="checkout-btn">Place Order</button>
    <p style="font-size: 0.7rem; color: var(--text-gray); margin-top: 1rem;">Online payment methods like UPI and cards will be available in future updates</p>
    <p style="font-size: 0.7rem; color: var(--text-gray); margin-top: 0.5rem;">* Required field.</p>
  </form>
</div>

<div class="popup" id="popupMsg">Order Successful!</div>

<footer>
  <div>© Artisoria v1.0</div>
  <div>
    <a href="#" class="under-construction">Privacy</a>
    <a href="#" class="under-construction">Terms</a>
    <a href="#" class="under-construction">About</a>
    <a href="#" class="under-construction">Contact</a>
  </div>
</footer>

<script>
  document.getElementById("checkoutForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const a1 = document.getElementById("address1").value.trim();
    const a2 = document.getElementById("address2").value.trim();
    const a3 = document.getElementById("landmark").value.trim();
    const city = document.getElementById("city").value.trim();
    const state = document.getElementById("state").value.trim();
    const pin = document.getElementById("pincode").value.trim();

    const fullAddress = `${a1}, ${a2}${a3 ? ', ' + a3 : ''}, ${city}, ${state}, ${pin}`;
    document.getElementById("addressField").value = fullAddress;

    // Show popup
    const popup = document.getElementById("popupMsg");
    popup.style.display = "block";

    setTimeout(() => {
      popup.style.display = "none";
      this.submit(); // submit form after showing popup briefly
    }, 500);
  });

  document.querySelectorAll('.under-construction').forEach(el => {
    el.addEventListener('click', function(e) {
      e.preventDefault();
      alert('Under Construction');
    });
  });
</script>

</body>
</html>