<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: index.php");
    exit();
}

// Fetch all available products randomly
$stmt = $conn->prepare("SELECT p.*, 
                       (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.product_id) AS average_rating,
                       (SELECT COUNT(r.review_id) FROM reviews r WHERE r.product_id = p.product_id) AS review_count
                       FROM products p 
                       WHERE p.stock > 0 
                       ORDER BY RAND()");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Initialize or reset explore index if needed
// If a specific product_id is passed, find its index
if (isset($_GET['product_id'])) {
    foreach ($products as $i => $product) {
        if ($product['product_id'] == $_GET['product_id']) {
            $_SESSION['explore_index'] = $i;
            break;
        }
    }
}

// Initialize or reset explore index if needed
if (!isset($_SESSION['explore_index']) || $_SESSION['explore_index'] >= count($products)) {
    $_SESSION['explore_index'] = 0;
}
$currentIndex = $_SESSION['explore_index'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Explore - Artisoria</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --black: #000000;
            --white: #ffffff;
            --coral: #FF5A5F;
            --coral-dark: #d6484d;
            --gray: #333333;
            --light-gray: #555555;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            height: 100%;
            font-family: 'Manrope', sans-serif;
            background-color: var(--black);
            color: var(--white);
            overflow: hidden;
            touch-action: pan-y;
        }

        /* Header Styles */
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
            margin-bottom: 0;
        }

        header .tagline {
            font-family: 'Manrope', sans-serif;
            font-size: 0.6rem;
            font-weight: 500;
            color: var(--white);
            margin-top: 0;
        }

        .back-button {
            background: var(--black);
            position: fixed;
            top: 0.7rem;
            right: 0.5rem;
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .back-button:hover {
            background: var(--white);
            color: var(--black);
        }

        /* Main Explore Container */
        .explore-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .product-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.5s ease;
        }

        .product-media {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-image-container {
            width: 100%;
            height: 100%;
            display: flex;
            overflow-x: scroll;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        .product-image-container::-webkit-scrollbar {
            display: none;
        }

        .product-image {
            width: 100%;
            height: 100%;
            flex: 0 0 100%;
            scroll-snap-align: start;
            object-fit: contain;
            background-color: var(--black);
        }

        .product-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            z-index: 100;
            max-height: 80px;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background: linear-gradient(0deg, rgba(0,0,0,0.9) 10%, rgba(0,0,0,0.7) 40%, transparent 100%);
        }

        .product-info.expanded {
            max-height: calc(100vh - 60px);
            overflow-y: auto;
            height: none;
            padding-bottom: 2rem;
        }

        .product-slide.expanded{
            overflow-y: auto;
        }

        .product-slide.product-info.expanded.product-media{
            flex: 0 0 auto;
        }

        .product-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .product-price {
            font-size: 1rem;
            color: var(--coral);
            margin-bottom: 0.5rem;
        }

        .product-description {
            max-width: calc(100% - 60px); /* Add this line to stop before right buttons */
            padding-right: 1rem;          /* Optional: extra visual space from right edge */

            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: none;
            max-height: none;
            overflow-y: visible;
            white-space: pre-wrap; /* Add this line */
            word-wrap: break-word;  /* Add this line */
            overflow-x: hidden;
        }

        .product-rating {
            font-size: 0.9rem;
            display: none;
            margin-bottom: 0.5rem;
        }

        .star-rating {
            color: gold;
            font-size: 1rem;
            letter-spacing: 2px;
        }

        .review-count {
            font-size: 0.8rem;
            color: var(--light-gray);
            margin-left: 5px;
        }

        .product-actions {
            position: fixed;
            right: 1.5rem;
            bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            z-index: 101;
        }

        .action-btn {
            background-color: rgb(36, 36, 36, 0);
            color: var(--white);
            border: 1px solid rgb(36, 36, 36, 0);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .action-btn:hover, .action-btn:active {
            background-color: transparent;
            color: var(--coral);
            border-color: transparent;
        }

        .action-btn.cart {
            color: var(--white);
            border-color: transparent;
        }

        .action-btn.cart:hover, .action-btn.cart:active {
            background-color: transparent;
            color: var(--coral);
            border-color: transparent;
        }

        .image-indicator {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
            z-index: 102;
        }

        .indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.5);
            transition: all 0.3s ease;
        }

        .indicator-dot.active {
            background-color: var(--white);
            transform: scale(1.2);
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid var(--white);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            z-index: 1000;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Footer Styles */
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
            font-size: 0.7rem;
            z-index: 1000;
        }

        footer a {
            color: var(--coral);
            text-decoration: none;
            margin-left: 1rem;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .product-actions {
                right: 0.5rem;
                bottom: 1.5rem;
                gap: 1rem;
            }
            
            .action-btn {
                width: 42px;
                height: 42px;
                font-size: 2rem;
                
            }
            
            .product-name {
                font-size: 1rem;
            }
            
            .product-price {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <h1>Artisoria</h1>
            <p class="tagline">Helping local artisans...</p>
        </div>
        <a href="buyer_home.php" class="back-button"><i class="fas fa-chevron-left"></i></a>
    </header>

    <div class="explore-container" id="exploreContainer">
        <?php if (empty($products)): ?>
            <div class="product-slide" style="display: flex; justify-content: center; align-items: center;">
                <p>No products available to explore.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="product-slide" data-index="<?php echo $index; ?>" 
                     style="transform: translateY(<?php echo ($index - $currentIndex) * 100; ?>%)">
                    <div class="product-media">
                        <div class="product-image-container" id="imageContainer-<?php echo $index; ?>">
                            <?php 
                            $images = array_filter([
                                $product['image_1'], 
                                $product['image_2'], 
                                $product['image_3'],
                                $product['image_4'],
                                $product['image_5'],
                                $product['image_6'],
                                $product['image_7'],
                                $product['image_8'],
                                $product['image_9'],
                                $product['image_10']
                            ], function($img) { return !empty($img); });
                            
                            foreach ($images as $imgIndex => $image): ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-image">
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($images) > 1): ?>
                            <div class="image-indicator">
                                <?php for ($i = 0; $i < count($images); $i++): ?>
                                    <div class="indicator-dot <?php echo $i === 0 ? 'active' : ''; ?>" 
                                         data-index="<?php echo $i; ?>"></div>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info" id="productInfo-<?php echo $index; ?>">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">₹<?php echo number_format($product['price'], 2); ?></p>
                        <div class="product-rating">
                            <?php if ($product['average_rating']): ?>
                                <span class="star-rating">
                                    <?php 
                                    $fullStars = floor($product['average_rating']);
                                    $halfStar = ($product['average_rating'] - $fullStars) >= 0.5 ? 1 : 0;
                                    $emptyStars = 5 - $fullStars - $halfStar;
                                    
                                    echo str_repeat('★', $fullStars);
                                    echo $halfStar ? '½' : '';
                                    echo str_repeat('☆', $emptyStars);
                                    ?>
                                </span>
                                <span class="review-count">(<?php echo $product['review_count']; ?>)</span>
                            <?php else: ?>
                                <span>No ratings yet</span>
                            <?php endif; ?>
                        </div>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                    
                    <div class="product-actions">
                        <button class="action-btn cart" data-product-id="<?php echo $product['product_id']; ?>">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        <button class="action-btn" data-product-id="<?php echo $product['product_id']; ?>">
                            <i class="fas fa-star"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exploreContainer = document.getElementById('exploreContainer');
            const productSlides = Array.from(document.querySelectorAll('.product-slide'));
            let currentSlideIndex = <?php echo $currentIndex; ?>;
            let touchStartY = 0;
            let touchStartX = 0;
            let isDragging = false;
            let currentImageIndex = 0;
            let wheelTimeout = null;
            let wheelDeltaY = 0;
            
            // Initialize all image containers with scroll snap
            document.querySelectorAll('.product-image-container').forEach(container => {
                container.addEventListener('scroll', updateImageIndicators);
            });
            
            // Initialize the slides
            function updateSlides() {
                productSlides.forEach((slide, index) => {
                    const offset = index - currentSlideIndex;
                    slide.style.transform = `translateY(${offset * 100}%)`;
                    
                    // Update session index
                    if (index === currentSlideIndex) {
                        fetch('update_explore_index.php?index=' + index)
                            .catch(err => console.error('Error updating index:', err));
                    }
                });
            }
            
            // Update image indicators based on scroll position
            function updateImageIndicators(e) {
                const container = e.target;
                const slideIndex = parseInt(container.id.split('-')[1]);
                const scrollPosition = container.scrollLeft;
                const containerWidth = container.offsetWidth;
                currentImageIndex = Math.round(scrollPosition / containerWidth);
                
                const dots = container.closest('.product-media').querySelectorAll('.indicator-dot');
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentImageIndex);
                });
            }
            
            // Handle touch/mouse events for navigation
            function handleTouchStart(e) {
                touchStartY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
                touchStartX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
                isDragging = true;
            }
            
            function handleTouchMove(e) {
                if (!isDragging) return;
                
                const touchY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
                const touchX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
                const deltaY = touchY - touchStartY;
                const deltaX = touchX - touchStartX;
                
                // Check if we're scrolling horizontally in an image container
                const currentImageContainer = productSlides[currentSlideIndex].querySelector('.product-image-container');
                if (Math.abs(deltaX) > 5 && currentImageContainer.scrollWidth > currentImageContainer.clientWidth) {
                    // Allow horizontal scrolling for images
                    return;
                }
                
                // Prevent vertical scrolling when expanded
                const productInfo = document.getElementById(`productInfo-${currentSlideIndex}`);
                if (productInfo && productInfo.classList.contains('expanded')) {
                    return;
                }
                
                // Prevent default to avoid page scrolling
                e.preventDefault();
                
                // Vertical swipe for product navigation
                const currentSlide = productSlides[currentSlideIndex];
                currentSlide.style.transform = `translateY(calc(${(currentSlideIndex - currentSlideIndex) * 100}% + ${deltaY}px))`;
                currentSlide.style.transition = 'none';
            }
            
            function handleTouchEnd(e) {
                if (!isDragging) return;
                isDragging = false;
                
                const productInfo = document.getElementById(`productInfo-${currentSlideIndex}`);
                if (productInfo && productInfo.classList.contains('expanded')) {
                    return;
                }

                const touchY = e.type === 'touchend' ? e.changedTouches[0].clientY : e.clientY;
                const touchX = e.type === 'touchend' ? e.changedTouches[0].clientX : e.clientX;
                const deltaY = touchY - touchStartY;
                const deltaX = touchX - touchStartX;
                
                const currentSlide = productSlides[currentSlideIndex];
                currentSlide.style.transition = 'transform 0.3s ease';
                
                // Check if we were scrolling horizontally in an image container
                const currentImageContainer = productSlides[currentSlideIndex].querySelector('.product-image-container');
                if (Math.abs(deltaX) > Math.abs(deltaY) && currentImageContainer.scrollWidth > currentImageContainer.clientWidth) {
                    // Handle horizontal swipe (image navigation)
                    if (Math.abs(deltaX) > 30) { // Minimum swipe distance
                        const containerWidth = currentImageContainer.offsetWidth;
                        const newIndex = deltaX > 0 ? 
                            Math.max(0, currentImageIndex - 1) : 
                            Math.min(currentImageContainer.children.length - 1, currentImageIndex + 1);
                        
                        currentImageContainer.scrollTo({
                            left: newIndex * containerWidth,
                            behavior: 'smooth'
                        });
                    }
                    return;
                }
                
                // Handle vertical swipe (product navigation)
                if (Math.abs(deltaY) > 50) { // Minimum swipe distance
                    if (deltaY > 0) {
                        currentSlideIndex = (currentSlideIndex - 1 + productSlides.length) % productSlides.length;
                    } else if (deltaY < 0) {
                        currentSlideIndex = (currentSlideIndex + 1) % productSlides.length;
                    }
                }
                
                updateSlides();
            }
            
            // Handle wheel events for trackpad/mouse wheel
            function handleWheel(e) {
                // Prevent default to avoid page scrolling
                e.preventDefault();
                
                // Accumulate wheel delta
                wheelDeltaY += e.deltaY;
                
                // Clear previous timeout
                if (wheelTimeout) {
                    clearTimeout(wheelTimeout);
                }
                
                // Set new timeout to process wheel event
                wheelTimeout = setTimeout(() => {
                    // Only proceed if we have significant vertical movement
                    if (Math.abs(wheelDeltaY) > 30) {
                        const productInfo = document.getElementById(`productInfo-${currentSlideIndex}`);
                        if (productInfo && productInfo.classList.contains('expanded')) {
                            return;
                        }
                        
                        if (wheelDeltaY > 0) {
                            currentSlideIndex = (currentSlideIndex + 1) % productSlides.length;
                        } else if (wheelDeltaY < 0) {
                            currentSlideIndex = (currentSlideIndex - 1 + productSlides.length) % productSlides.length;
                        }

                        updateSlides();
                    }
                    
                    // Reset wheel delta
                    wheelDeltaY = 0;
                }, 50);
            }
            
            // Toggle product info expansion
            function toggleProductInfo(slideIndex) {
                const productInfo = document.getElementById(`productInfo-${slideIndex}`);
                if (!productInfo) return;
                
                const isExpanded = productInfo.classList.toggle('expanded');
                const description = productInfo.querySelector('.product-description');
                const rating = productInfo.querySelector('.product-rating');
                
                if (isExpanded) {
                    description.style.display = 'block';
                    rating.style.display = 'block';
                    
                    // Calculate needed height based on content
                    const contentHeight = productInfo.scrollHeight;
                    const maxHeight = window.innerHeight * 0.8;
                    const newHeight = Math.min(contentHeight, maxHeight);
                    
                    productInfo.style.maxHeight = `${newHeight}px`;
                } else {
                    description.style.display = 'none';
                    rating.style.display = 'none';
                    productInfo.style.maxHeight = '80px';
                }
            }
            
            // Add event listeners
            exploreContainer.addEventListener('touchstart', handleTouchStart, { passive: false });
            exploreContainer.addEventListener('mousedown', handleTouchStart);
            
            exploreContainer.addEventListener('touchmove', handleTouchMove, { passive: false });
            exploreContainer.addEventListener('mousemove', handleTouchMove);
            
            exploreContainer.addEventListener('touchend', handleTouchEnd);
            exploreContainer.addEventListener('mouseup', handleTouchEnd);
            exploreContainer.addEventListener('mouseleave', handleTouchEnd);
            
            // Add wheel event listener for trackpad/mouse wheel support
            exploreContainer.addEventListener('wheel', handleWheel, { passive: false });
            
            // Click/tap to expand product info
            exploreContainer.addEventListener('click', function(e) {
                // Ignore if we're clicking on action buttons or image indicators
                if (e.target.closest('.action-btn') || e.target.closest('.indicator-dot')) return;
                
                const slide = e.target.closest('.product-slide');
                if (slide) {
                    const slideIndex = parseInt(slide.dataset.index);
                    toggleProductInfo(slideIndex);
                }
            });
            
            // Click on image indicators to navigate to specific image
            exploreContainer.addEventListener('click', function(e) {
                const dot = e.target.closest('.indicator-dot');
                if (dot) {
                    const slideIndex = parseInt(dot.closest('.product-slide').dataset.index);
                    const imageIndex = parseInt(dot.dataset.index);
                    const container = document.getElementById(`imageContainer-${slideIndex}`);
                    if (container) {
                        container.scrollTo({
                            left: imageIndex * container.offsetWidth,
                            behavior: 'smooth'
                        });
                    }
                }
            });
            
            // Action buttons
            document.querySelectorAll('.action-btn.cart').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    fetch(`add_to_cart.php?product_id=${productId}`)
                        .then(response => response.text())
                        .then(() => {
                            // Visual feedback
                            const icon = this.querySelector('i');
                            icon.classList.add('fa-check');
                            
                            setTimeout(() => {
                                icon.classList.remove('fa-check');
                                icon.classList.add('fa-shopping-cart');
                                this.style.backgroundColor = 'var(--black)';
                                this.style.borderColor = 'transparent';
                            }, 1000);
                        })
                        .catch(err => console.error('Error:', err));
                });
            });
            
            document.querySelectorAll('.action-btn:not(.cart)').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    window.open(`product_reviews.php?product_id=${productId}`, '_blank');
                });
            });
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {

                const productInfo = document.getElementById(`productInfo-${currentSlideIndex}`);
                if (productInfo && productInfo.classList.contains('expanded')) {
                    return;
                }


                const currentSlide = productSlides[currentSlideIndex];
                const currentImageContainer = currentSlide.querySelector('.product-image-container');
                
                switch(e.key) {
                    case 'ArrowUp':
                        currentSlideIndex = (currentSlideIndex - 1 + productSlides.length) % productSlides.length;
                        updateSlides();
                        break;
                    case 'ArrowDown':
                        currentSlideIndex = (currentSlideIndex + 1) % productSlides.length;
                        updateSlides();
                        break;

                    case 'ArrowLeft':
                        if (currentImageContainer && currentImageContainer.scrollWidth > currentImageContainer.clientWidth) {
                            const newIndex = Math.max(0, currentImageIndex - 1);
                            currentImageContainer.scrollTo({
                                left: newIndex * currentImageContainer.offsetWidth,
                                behavior: 'smooth'
                            });
                        }
                        break;
                    case 'ArrowRight':
                        if (currentImageContainer && currentImageContainer.scrollWidth > currentImageContainer.clientWidth) {
                            const newIndex = Math.min(currentImageContainer.children.length - 1, currentImageIndex + 1);
                            currentImageContainer.scrollTo({
                                left: newIndex * currentImageContainer.offsetWidth,
                                behavior: 'smooth'
                            });
                        }
                        break;
                    case ' ':
                    case 'Enter':
                        toggleProductInfo(currentSlideIndex);
                        break;
                }
            });
            
            // Initialize
            updateSlides();
        });
    </script>
</body>
</html>