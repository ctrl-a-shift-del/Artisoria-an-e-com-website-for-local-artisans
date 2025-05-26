<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact - Artisoria</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --black: #000000;
      --white: #ffffff;
      --coral: #FF5A5F;
      --gray: #1a1a1a;
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
      padding: 0.1rem 0.5rem;
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
      text-align: center;
      padding: 5rem 1rem 2rem;
      flex: 1;
      max-width: 600px;
      margin: 0 auto;
      font-size: 0.9rem;
      line-height: 1.7;
    }

    h2.section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 1rem;
      text-align: center;
    }
    


    @media (max-width: 600px) {
      .content-wrapper {
        padding: 5rem 1rem 2rem;
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
  <h2 class="section-title">Let's connect!</h2>

  <p style="margin-bottom: 2.5rem;">Have a question? Idea? Feedback? Or just want to say hi?<br> We’re all ears, only one click away:</p>

  <p>Email us at <a href="mailto:artisoria@gmail.com">artisoria@gmail.com</a></p>

  <p>Or connect with the creator on<a href="https://www.linkedin.com/in/shailendrachandrasekaran/" target="_blank">LinkedIn</a>.</p>

</div>

</body>
</html>
