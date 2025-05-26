<?php
session_start();
include "database.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Privacy Policy - Artisoria</title>
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
      padding: 4.2rem 0.5rem 2rem;
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
      margin-bottom: 0.75rem;
      text-align: center;
    }

    .privacy-content {
      background-color: var(--black);
      padding: 1rem;
      border-radius: 4px;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      font-size: 0.8rem;
      line-height: 1.6;
      text-align: justify;
    }

    .privacy-content h3 {
      font-family: 'Montserrat', sans-serif;
      color: var(--white);
      margin: 1.5rem 0 0.5rem;
      font-size: 1rem;
    }

    .privacy-content p {
      margin-bottom: 0.8rem;
    }

    .privacy-content ul {
      margin-left: 1.5rem;
      margin-bottom: 0.8rem;
    }

    .privacy-content li {
      margin-bottom: 0.3rem;
    }



    @media (max-width: 600px) {
      .content-wrapper {
        padding: 5rem 1rem 2rem;
      }
      
      .privacy-content {
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
  <h2 class="section-title">Privacy Policy</h2>

  <div class="privacy-content">
    <p><strong>Effective Date:</strong> May 25, 2025<br>
    <strong>Last Updated:</strong> May 25, 2025<br>
    <strong>Version:</strong> 1.0</p>

    <h3>Introduction</h3>
    <p>This Privacy Policy ("Policy") describes how Artisoria collects, uses, stores, discloses, and protects the personal information of individual users who visit, access, or make use of the website located at <a href="https://artisoria.great-site.net">https://artisoria.great-site.net</a> (the "Website").
    Artisoria is a web-based platform designed to support local artisans by providing them with a digital space to showcase and sell their handmade products. Although the Platform was developed as part of a student innovation project, it is made publicly accessible and operates in a manner similar to small-scale e-commerce platforms. As such, we are committed to ensuring that the privacy of our users is protected in accordance with applicable laws and best practices.
    By using or accessing the Platform, you acknowledge that you have read, understood, and agree to be bound by the terms of this Privacy Policy.</p>

    <h3>Scope and Application</h3>
    <p>This Privacy Policy applies to all individuals who:<br>
    • Visit the Website as a guest<br>
    • Register as a buyer or seller<br>
    • Submit personal information via forms, registration fields, or contact interfaces<br>
    • Engage in activities such as browsing, uploading products, or placing orders</p>
    <p>This Policy applies regardless of the method of access or use (e.g., via desktop or mobile browser). It also applies to the collection of data through automated technologies such as cookies and server logs.
    This Privacy Policy does not govern third-party websites, tools, or platforms that may be linked from our Website. We are not responsible for the privacy practices of such external entities, and users are encouraged to review their policies separately.</p>

    <h3>Definitions</h3>
    <p>• <strong>Personal Data:</strong> Any information that relates to an identified or identifiable individual. Examples include your name, email address, IP address, and shipping details.<br>
    • <strong>Processing:</strong> Any operation performed on personal data, including collection, storage, use, disclosure, or deletion.<br>
    • <strong>Controller:</strong> The party that determines the purposes and means of processing personal data. For purposes of this Privacy Policy, Artisoria acts as the controller of your data.</p>

    <h3>Information We Collect</h3>
    <p><strong>Information You Provide Voluntarily</strong><br>
    We collect information that you voluntarily provide when you interact with the Website, including but not limited to:<br>
    • <strong>Account Information:</strong> Full name, email address, password (stored in hashed format), and user role (buyer or seller).<br>
    • <strong>Profile and Contact Details:</strong> Phone number, shipping address, and billing information, where applicable.<br>
    • <strong>Product Listings (for Sellers):</strong> Product images, titles, prices, descriptions, and availability.<br>
    • <strong>Order and Transaction Information:</strong> Products added to cart, confirmed orders, delivery preferences, and order history.<br>
    • <strong>User-Generated Content:</strong> Comments, messages sent through contact forms, and feedback submitted via the Website.</p>
    <p><strong>Information Collected Automatically</strong><br>
    When you access the Website, we may automatically collect certain technical and usage data, such as:<br>
    • IP Address and general geolocation<br>
    • Browser type and version<br>
    • Operating system and device specifications<br>
    • Pages visited, time spent on the site, and navigation patterns<br>
    • Referring URLs or search engines used to access the site<br>
    • Date and time of access<br>
    This information is used to monitor performance, ensure site security, and enhance the user experience.</p>
    <p><strong>Cookies and Tracking Technologies</strong><br>
    We use session-based and strictly necessary cookies to maintain user login sessions, enable functionality such as the shopping cart, and enhance security features. These cookies do not contain personally identifiable information and are not used for advertising or behavioral tracking.
    Users may configure their browser settings to decline cookies. However, disabling cookies may impair certain functionalities of the Website.
    <em>Note: While we currently do not offer a cookie consent mechanism, we aim to implement one in future updates to align with evolving compliance standards.</em></p>

    <h3>Legal Basis for Processing</h3>
    <p>Depending on your jurisdiction, our processing of your personal data may rely on one or more of the following legal grounds:<br>
    • <strong>Consent:</strong> When you explicitly consent to the collection or processing of data (e.g., registration or form submission).<br>
    • <strong>Contractual Necessity:</strong> When processing is required to fulfill a transaction or deliver a requested service (e.g., order fulfillment).<br>
    • <strong>Legitimate Interests:</strong> Where processing is necessary for purposes such as site security, troubleshooting, and service improvement, provided that such interests are not overridden by your fundamental rights and freedoms.<br>
    • <strong>Legal Obligation:</strong> Where processing is required to comply with applicable law, regulation, or legal proceedings.</p>

    <h3>Purpose of Data Collection and Use</h3>
    <p>We collect and process personal data to support the following activities:<br>
    • To register and manage user accounts<br>
    • To verify user identity and authenticate login credentials<br>
    • To enable buyers to place orders and sellers to list products<br>
    • To facilitate order processing, shipping, and customer communication<br>
    • To maintain transaction records for auditing and user reference<br>
    • To respond to user inquiries, technical support requests, and complaints<br>
    • To prevent misuse, fraud, or other illegal or abusive activity<br>
    • To monitor server performance and troubleshoot issues<br>
    • To comply with legal and contractual obligations</p>
    <p>We do not use your personal data for:<br>
    • Targeted advertising<br>
    • Data monetization<br>
    • Automated profiling or decision-making</p>

    <h3>Data Storage and Retention</h3>
    <p><strong>Data Storage</strong><br>
    User data is stored in a MySQL database hosted on servers provided by InfinityFree (or similar hosting providers). Data is stored in encrypted or hashed format where applicable, especially for sensitive fields such as passwords.
    Server access is limited to authorized scripts and controlled by strict database credentials. The hosting provider may implement additional safeguards, subject to their published policies.</p>
    <p><strong>Data Retention</strong><br>
    We retain your personal data for the duration necessary to fulfill the purposes outlined in this Policy. Specifically:<br>
    • Account-related data is retained until the user deletes their account or requests removal.<br>
    • Order and transactional records may be retained for audit, legal, or academic documentation for a reasonable period, typically up to seven years.<br>
    • Contact and feedback submissions are retained until resolved or no longer necessary.<br>
    • Technical logs and error reports may be retained for 30 to 90 days.<br>
    You may request deletion of your account and associated data at any time, subject to verification and applicable exceptions.</p>

    <h3>Data Sharing and Third-Party Access</h3>
    <p>We do not sell, rent, or exchange your personal data with third parties for commercial or advertising purposes.<br>
    However, we may disclose your data to the following categories of third parties:<br>
    • <strong>Service Providers:</strong> Such as hosting providers (e.g., InfinityFree), domain registrars, or backup utilities, solely to the extent necessary to operate and maintain the Website.<br>
    • <strong>Academic Supervisors and Internal Evaluators:</strong> In the context of project validation, under confidentiality obligations.<br>
    • <strong>Legal Authorities:</strong> When required to comply with a subpoena, court order, law enforcement request, or other legal process.<br>
    • <strong>Security Partners:</strong> For the investigation of technical faults, malicious activity, or data breach response, under strict controls.<br>
    In each case, we ensure that data is handled securely, only for the intended purpose, and in accordance with applicable data protection regulations.</p>

    <h3>User Rights and Control</h3>
    <p>Depending on your jurisdiction, you may have the following rights concerning your personal data:<br>
    • Right to Access<br>
    • Right to Rectification<br>
    • Right to Erasure<br>
    • Right to Restriction<br>
    • Right to Data Portability<br>
    • Right to Object<br>
    • Right to Withdraw Consent</p>
    <p>To exercise any of the above rights, please contact us at: <a href="mailto:artisoria@gmail.com">artisoria@gmail.com</a>. We may require verification of your identity before fulfilling your request. We will respond within a reasonable period, not exceeding thirty (30) days unless otherwise permitted by law.</p>

    <h3>Children's Privacy</h3>
    <p>The Website is not directed to children under the age of 13. We do not knowingly collect, use, or store personal data from children without verifiable parental consent. We rely on users to provide accurate information during registration.
    If we become aware that a child has submitted personal data, we will take immediate steps to delete such information from our records.
    If you believe that we may have collected data from a minor without proper consent, please contact us immediately.</p>

    <h3>Data Transfers and International Processing</h3>
    <p>As the Website is hosted on global servers, your data may be processed or stored outside of your country of residence, including in jurisdictions that may not offer the same level of data protection as your home country.
    By using the Website, you consent to such transfers, provided that appropriate safeguards are in place to ensure your data is treated securely and in accordance with this Privacy Policy.</p>

    <h3>Security Measures</h3>
    <p>We implement technical, administrative, and physical safeguards to protect personal data against unauthorized access, loss, misuse, alteration, or disclosure. These include:<br>
    • Strong password hashing algorithms<br>
    • Input validation and sanitization<br>
    • Session management using secure tokens<br>
    • HTTPS (where supported)<br>
    • Access control for backend systems and databases</p>
    <p>Although we take reasonable steps to protect your data, no website or electronic transmission is completely secure. Users are advised to take additional precautions such as choosing strong passwords and logging out after use.</p>

    <h3>Changes to This Policy</h3>
    <p>We reserve the right to update or modify this Privacy Policy at any time. When we do, we will revise the "Last Updated" date at the top of this page. In the case of significant changes, we may also notify users via email or in-site notification.
    Continued use of the Website following the publication of an updated Privacy Policy constitutes acceptance of the changes.</p>

    <h3>Contact Information</h3>
    <p>If you have any questions, requests, concerns, or complaints about this Privacy Policy or the handling of your data, you may 
    <a href="https://artisoria.great-site.net/contact.php">contact us</a> 
    or email us at <a href="mailto:artisoria@gmail.com">artisoria@gmail.com.</a></p>
    <p>
    We welcome and value your feedback and will respond in a timely and professional manner.
    </p>


    <h3>Governing Law</h3>
    <p>This Privacy Policy and any dispute arising from the use of the Website shall be governed by and construed in accordance with the applicable data protection and privacy laws of the Republic of India. By using this Website, User consents to the exclusive jurisdiction of the courts located in India for the resolution of any disputes arising out of or relating to this Privacy Policy.</p>
  </div>
</div>


</body>
</html>