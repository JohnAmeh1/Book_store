<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Book Library | Your Gateway to Knowledge</title>
  <meta name="description" content="Explore thousands of books across all genres. Your digital library for students, researchers, and book lovers.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2c3e50;
      --secondary: #3498db;
      --accent: #e74c3c;
      --light: #ecf0f1;
      --dark: #2c3e50;
      --text: #34495e;
      --white: #ffffff;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      color: var(--text);
      line-height: 1.6;
      background-color: var(--light);
    }

    .head {
      background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
      color: var(--white);
      min-height: 100vh;
      position: relative;
      overflow: hidden;
    }

    .head::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
      opacity: 0.15;
      z-index: 0;
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem 5%;
      position: relative;
      z-index: 1;
    }

    .mylogo {
      display: flex;
      align-items: center;
    }

    .logo1 {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .logo1 img {
      width: 50px;
      height: 50px;
      object-fit: contain;
    }

    .logo1 h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 700;
      background: linear-gradient(to right, var(--white), var(--secondary));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .nav-links {
      display: flex;
      gap: 2rem;
    }

    .nav-links a {
      color: var(--white);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
      position: relative;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -5px;
      left: 0;
      background-color: var(--secondary);
      transition: var(--transition);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .header_description {
      position: relative;
      z-index: 1;
      padding: 5% 10%;
      max-width: 1200px;
      margin: 0 auto;
    }

    .header1 {
      max-width: 800px;
    }

    .head_description {
      font-family: 'Playfair Display', serif;
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      line-height: 1.2;
    }

    .head2 {
      font-size: 1.8rem;
      font-weight: 400;
      margin-bottom: 2rem;
      color: var(--secondary);
    }

    .para {
      font-size: 1.2rem;
      margin-bottom: 3rem;
      opacity: 0.9;
    }

    .view-books-btn {
      display: inline-block;
      padding: 1rem 2.5rem;
      background-color: var(--accent);
      color: var(--white);
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: var(--transition);
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      border: none;
      cursor: pointer;
    }

    .view-books-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      background-color: #c0392b;
    }

    .view-books-btn::after {
      content: "→";
      margin-left: 10px;
      transition: var(--transition);
    }

    .view-books-btn:hover::after {
      transform: translateX(5px);
    }

    /* Floating books animation */
    .floating-books {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 0;
      overflow: hidden;
    }

    .book {
      position: absolute;
      font-size: 2rem;
      color: rgba(255, 255, 255, 0.3);
      animation: float 15s infinite linear;
    }

    @keyframes float {
      0% {
        transform: translateY(0) rotate(0deg);
      }
      100% {
        transform: translateY(-100vh) rotate(360deg);
      }
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      .head_description {
        font-size: 2.8rem;
      }
      
      .head2 {
        font-size: 1.5rem;
      }
      
      .para {
        font-size: 1.1rem;
      }
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        gap: 1.5rem;
      }
      
      .nav-links {
        gap: 1.5rem;
      }
      
      .head_description {
        font-size: 2.2rem;
      }
      
      .head2 {
        font-size: 1.3rem;
      }
      
      .para br {
        display: none;
      }
    }

    @media (max-width: 576px) {
      .header_description {
        padding: 5%;
      }
      
      .head_description {
        font-size: 1.8rem;
      }
      
      .head2 {
        font-size: 1.1rem;
      }
      
      .para {
        font-size: 1rem;
      }
      
      .view-books-btn {
        padding: 0.8rem 1.8rem;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <header class="head">
    <div class="floating-books">
      <div class="book" style="top: 10%; left: 5%; animation-delay: 0s;">📘</div>
      <div class="book" style="top: 20%; left: 80%; animation-delay: 2s;">📕</div>
      <div class="book" style="top: 60%; left: 15%; animation-delay: 4s;">📗</div>
      <div class="book" style="top: 30%; left: 60%; animation-delay: 6s;">📙</div>
      <div class="book" style="top: 70%; left: 75%; animation-delay: 8s;">📓</div>
    </div>
    
    <nav>
      <div class="mylogo">
        <div class="logo1">
          <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" alt="Book Store Logo">
          <h2>Online Book Store</h2>
        </div>
      </div>
      
      <div class="nav-links">
        <!-- <a href="./library.php">Browse Books</a>
        <a href="./account.php">My Account</a>
        <a href="#features">Features</a> -->
        <a href="contact.html">Contact</a>
      </div>
    </nav>

    <div class="header_description">
      <div class="header1">
        <h1 class="head_description">Welcome to the Online Book Library</h1>
        <h2 class="head2">Your Gateway to Endless Knowledge & Imagination</h2>
        <p class="para">
          Whether you're a student, a researcher, or a casual reader, 
          our library has something for everyone. Browse through thousands of titles, explore curated 
          recommendations, and dive into a universe of imagination and intellect—all from the comfort of 
          your screen.
        </p>

        <a href="./account.php" class="view-books-btn">Get Started</a>
      </div>
    </div>
  </header>

  <script>
    // Add floating books dynamically
    document.addEventListener('DOMContentLoaded', function() {
      const floatingBooks = document.querySelector('.floating-books');
      const bookIcons = ['📘', '📕', '📗', '📙', '📓', '📔', '📒'];
      
      for (let i = 0; i < 10; i++) {
        const book = document.createElement('div');
        book.className = 'book';
        book.textContent = bookIcons[Math.floor(Math.random() * bookIcons.length)];
        book.style.top = Math.random() * 100 + '%';
        book.style.left = Math.random() * 100 + '%';
        book.style.animationDelay = Math.random() * 10 + 's';
        book.style.animationDuration = 10 + Math.random() * 20 + 's';
        book.style.fontSize = (1 + Math.random() * 2) + 'rem';
        book.style.opacity = 0.2 + Math.random() * 0.3;
        floatingBooks.appendChild(book);
      }
      
      // Smooth scroll for navigation links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();
          document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
          });
        });
      });
    });
  </script>
</body>
</html>