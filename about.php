<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - Artisoria</title>
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

    a, a:visited {
      color: var(--white);
      text-decoration: underline;
    }

    a:hover, a:active {
      color: var(--coral);
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

    .content-wrapper {
      padding: 5rem 0.5rem 2rem;
      flex: 1;
      width: 100%;
      max-width: 1000px;
      margin: 0 auto;
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 0.2rem;
      text-align: center;
    }

    .about-content {
      background-color: var(--black);
      padding: 0.9rem;
      border-radius: 4px;
      margin-bottom: 1rem;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      font-size: 0.8rem;
      line-height: 1.5;
      text-align: justify;
    }

    .about-content h3 {
      font-family: 'Montserrat', sans-serif;
      color: var(--white);
      margin: 1.5rem 0 0.5rem;
      font-size: 1rem;
    }

    .about-content p {
      margin-bottom: 0.8rem;
    }

    .about-content ul {
      margin-left: 1.5rem;
      margin-bottom: 0.8rem;
    }

    .about-content li {
      margin-bottom: 0.3rem;
    }

    .mission-statement {
      
      color: var(--coral);
      text-align: left;
      margin: 1rem 0;
    }

    .highlight-box {
      background-color: var(--gray);
      border-left: 3px solid var(--coral);
      padding: 1rem;
      margin: 1rem 0;
    }

    @media (max-width: 600px) {
      .content-wrapper {
        padding: 5rem 1rem 2rem;
      }
      
      .about-content {
        padding: 1rem;
        font-size: 0.8rem;
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
  <h2 class="section-title">About Artisoria</h2>

  <div class="about-content">
    <h3 style="text-align: center;">Crafted with Purpose. Powered by Passion. Built for Artisans.</h3>

    <h3>Who We Are</h3>
    <p>Artisoria is more than just a website --- it's a mission-driven platform built independently with one goal: to empower local artisans and bring their craft into the digital world. Conceived, designed, and developed from scratch, Artisoria was created to reimagine how handmade products are showcased, discovered, and purchased online.</p>
    <p>Every feature, from its immersive, mobile-first shopping experience to its intuitive seller tools --- was thoughtfully engineered to support creators and connect them with people who value authenticity and artistry.</p>

    <h3>What We Do</h3>
    <p>Artisoria is a curated digital marketplace that bridges the gap between <strong>artisans</strong> and <strong>authentic product seekers</strong>. We enable verified sellers to upload their handmade products, manage inventory, and connect with buyers --- all through a clean, mobile-optimized, and reels-inspired user experience.</p>
    <p>With an intuitive interface and simplified workflows, our platform removes the technical barriers that often prevent local artisans from entering the e-commerce world.</p>

    <h3>Our Mission</h3>
    <p class="mission-statement">"To empower every artisan with the digital tools and visibility they deserve --- and to bring authentic handmade creations to customers who value craftsmanship, culture, and community."</p>

    <h3>Our Story</h3>
    <p>Local artisans often possess immense talent but lack access to effective digital selling platforms. They're hidden behind intermediaries, overwhelmed by complicated platforms, and often underpaid for their work.</p>
    <p>Determined to make a change, our team of developers and designers collaborated to build a solution that was:</p>
    <ul>
      <li><strong>Affordable</strong> (hosted on free-tier platforms)</li>
      <li><strong>Accessible</strong> (minimal learning curve for sellers)</li>
      <li><strong>Aesthetic and Functional</strong> (inspired by social media UIs like Instagram Reels)</li>
    </ul>
    <p>We began with wireframes, moved through full-stack development using PHP and MySQL, and launched our MVP on <a href="https://artisoria.great-site.net">https://artisoria.great-site.net</a>. What started as a classroom concept became a functional marketplace recognized for its vision and purpose.</p>

    <h3>Why Artisoria?</h3>
    <div class="highlight-box">
      <p><strong>🛍️ For Buyers:</strong></p>
      <ul>
        <li>A fresh and immersive way to <strong>discover handmade products</strong></li>
        <li>A guarantee of <strong>authenticity, uniqueness, and ethical shopping</strong></li>
        <li>Seamless browsing with a <strong>Reels-style vertical feed</strong>, optimized for mobile</li>
      </ul>
    </div>
    <div class="highlight-box">
      <p><strong>🧵 For Sellers:</strong></p>
      <ul>
        <li>No commissions or platform fees</li>
        <li>Easy-to-use seller dashboard for product uploads and order tracking</li>
        <li>A community-first platform built around <strong>real craftsmanship</strong>, not mass production</li>
      </ul>
    </div>

    <h3>Core Values</h3>
    <ul>
      <li><strong>Empowerment</strong>: We put creators first --- always.</li>
      <li><strong>Integrity</strong>: No fake products. No hidden costs. No middlemen.</li>
      <li><strong>Accessibility</strong>: Tech should serve people, not scare them away.</li>
      <li><strong>Creativity</strong>: We celebrate the individuality of every product and every maker.</li>
      <li><strong>Transparency</strong>: Clear policies, ethical practices, and open communication.</li>
    </ul>

    <h3>Built With</h3>
    <ul>
      <li><strong>Frontend</strong>: HTML, CSS, JavaScript (with emphasis on dark minimalist design)</li>
      <li><strong>Backend</strong>: PHP and MySQL</li>
      <li><strong>Hosting</strong>: InfinityFree (free hosting tier)</li>
      <li><strong>Version Control</strong>: Git & GitHub</li>
      <li><strong>Security</strong>: Hashed passwords, input validations, and user session protection</li>
      <li><strong>Planned Integrations</strong>: Payment gateways, logistics tracking, and mobile apps</li>
    </ul>

    <h3>What's Next</h3>
    <p>We're only getting started. Artisoria is evolving --- with plans to:</p>
    <ul>
      <li>Add UPI & card-based payment gateways</li>
      <li>Launch a dedicated mobile app</li>
      <li>Enable delivery tracking</li>
      <li>Introduce AI-powered product suggestions</li>
      <li>Scale into a nationwide artisan network</li>
    </ul>

    <h3>Join the Movement</h3>
    <p>Whether you're an artisan, a buyer, or someone who simply loves the story behind every handmade piece --- Artisoria welcomes you.
    <a href="https://artisoria.great-site.net/contact.php">Contact us</a> 
    or email us at <a href="mailto:artisoria@gmail.com">artisoria@gmail.com.</a></p>
    <p>Follow us. Share us. Grow with us.<br>
    Let's build a future where tradition meets technology.</p>
  </div>
</div>

</body>
</html>