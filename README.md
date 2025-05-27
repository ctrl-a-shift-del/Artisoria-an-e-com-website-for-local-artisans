# Artisoria: E-Commerce Platform for Local Artisans

Welcome to **Artisoria** – a mission-driven e-commerce platform empowering local artisans to showcase and sell their handmade creations directly to buyers.

---

## 📑 Table of Contents

- [Features](#features)
- [Usage & Screenshots](#usage--screenshots)
- [Technologies Used](#technologies-used)
- [Installation / Setup Instructions](#installation--setup-instructions)

---

## Features

- **Reels-Style Product Explorer** for buyers with swipeable product UI
- **Add-to-Cart & Checkout System** with address input and order summary
- **Seller Dashboard** with product upload and inventory management
- **Order Tracking** for both buyers and sellers
- **Mobile-Responsive Design** inspired by modern social platforms
- **Secure User Authentication** with session handling and validations

---

## Usage & Screenshots

**Artisoria** is designed for two types of users: **local artisans (sellers)** and **customers (buyers)**. Sellers can sign up to upload handmade product listings, manage inventory, and track customer orders using an intuitive dashboard. Buyers can explore products through a reels-style vertical feed, add items to their cart, and place orders with a smooth, mobile-first checkout experience.  

Explore the platform at: [https://artisoria.great-site.net](https://artisoria.great-site.net)

### Screenshots:

> ![screenshot1](https://raw.githubusercontent.com/ctrl-a-shift-del/Artisoria-an-e-com-website-for-local-artisans/main/screenshot1.jpg)
> ![screenshot2](https://raw.githubusercontent.com/ctrl-a-shift-del/Artisoria-an-e-com-website-for-local-artisans/main/screenshot2.jpg)
> ![screenshot3](https://raw.githubusercontent.com/ctrl-a-shift-del/Artisoria-an-e-com-website-for-local-artisans/main/screenshot3.jpg)

---

## Technologies Used

### Frontend
- HTML, CSS, JavaScript 

### Backend
- PHP 

### Database
- MySQL 

---

## Installation / Setup Instructions

Follow the steps below to set up and run the Artisoria project on your local machine using **XAMPP** and **Visual Studio Code**:

### 1. Install Required Software
- Download and install **[XAMPP](https://www.apachefriends.org/index.html)** (includes Apache, PHP, and MySQL).
- Download and install **[Visual Studio Code](https://code.visualstudio.com/)** or any code editor of your choice.

### 2. Download the Project Files
- Visit this repository on GitHub.
- Click on the green **"Code"** button and select **"Download ZIP"** or clone it using Git:
  ```bash
  git clone https://github.com/ctrl-a-shift-del/Artisoria-an-e-com-website-for-local-artisans.git
  ```

### 3. Set Up XAMPP Directory

* Open the **XAMPP Control Panel** and start both the **Apache** and **MySQL** services.
* Navigate to your `C:\xampp\htdocs` directory. (default installation of xamp in C drive)
* Create a new folder `ecommerce`.
* Copy and paste the contents of the downloaded GitHub project into this folder.

### 4. Set Up the Database

* In your browser, go to:

  ```
  http://localhost/phpmyadmin
  ```
* Click **"New"** to create a new database. Name it:
  ```
  ecommerce
  ```
* After the database is created, go to the **"Import"** tab.
* Click **"Choose File"** and select the `ecommerce.sql` file from the GitHub repository.
* Click **"Go"** to import the database structure and sample data.


### 5. Launch the Project in Browser

* Open your web browser.

* If an `index.php` file already exists in `htdocs`, you may need to rename it to avoid conflicts. By visiting the original (renamed) index.php you can access phpMyAdmin. The modified index.php is the entry point to the website.
  
* Visit:

  ```
  http://localhost/ecommerce/
  ```

You should now see the **Artisoria** homepage (login page) running on your local server.

### 6. Optional: Hosting Online

* You can deploy the project to free hosting platforms by updating your `database.php` credentials with the appropriate remote MySQL hostname, username, password, and database name provided by your hosting provider.

---
