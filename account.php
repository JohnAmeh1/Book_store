<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BookStore | Account Login & Signup</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --accent: #f72585;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #4cc9f0;
      --danger: #ef233c;
      --warning: #f8961e;
      --border-radius: 8px;
      --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      color: var(--dark);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-image: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      position: relative;
      padding: 1rem; /* Added padding for small devices */
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      z-index: 0;
    }

    .error-message {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background-color: var(--danger);
      color: white;
      padding: 15px 25px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      z-index: 100;
      max-width: calc(100% - 2rem); /* Better for small screens */
      width: auto;
      animation: slideDown 0.5s ease-out;
    }

    .error-message p {
      margin: 5px 0;
      font-size: 0.9rem;
    }

    @keyframes slideDown {
      from {
        top: -100px;
        opacity: 0;
      }
      to {
        top: 20px;
        opacity: 1;
      }
    }

    .account-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 900px;
      margin: 0 auto; /* Center the container */
      background-color: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      overflow: hidden;
      display: flex;
      min-height: 550px;
    }

    .form {
      padding: 2rem 1.5rem; /* Reduced padding for mobile */
      width: 50%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      transition: var(--transition);
      position: relative;
    }

    .form.active {
      width: 50%;
    }

    .form:not(.active) {
      width: 50%;
      opacity: 0;
      pointer-events: none;
      position: absolute;
      right: 0;
    }

    .form h2 {
      font-size: clamp(1.5rem, 2.5vw, 2rem); /* Responsive font size */
      margin-bottom: 1.5rem;
      color: var(--primary);
      text-align: center;
    }

    .form input {
      width: 100%;
      padding: 0.8rem 1rem;
      margin-bottom: 1rem;
      border: 1px solid #ddd;
      border-radius: var(--border-radius);
      font-size: 1rem;
      transition: var(--transition);
    }

    .form input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
    }

    .form button[type="submit"] {
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 0.8rem;
      border-radius: var(--border-radius);
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 0.5rem;
    }

    .form button[type="submit"]:hover {
      background-color: var(--secondary);
      transform: translateY(-2px);
    }

    .toggle-text {
      text-align: center;
      margin-top: 1.5rem;
      color: #666;
      font-size: 0.9rem;
    }

    .toggle-text button {
      background: none;
      border: none;
      color: var(--primary);
      font-weight: 600;
      cursor: pointer;
      padding: 0;
      font-size: 0.9rem;
      transition: var(--transition);
    }

    .toggle-text button:hover {
      color: var(--secondary);
      text-decoration: underline;
    }

    .form-image {
      width: 50%;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.5rem; /* Reduced padding */
      color: white;
      text-align: center;
      transition: var(--transition);
    }

    .form-image h2 {
      font-size: clamp(1.5rem, 2.5vw, 2rem); /* Responsive font size */
      margin-bottom: 1rem;
    }

    .form-image p {
      margin-bottom: 1.5rem;
      opacity: 0.9;
      font-size: clamp(0.9rem, 1.5vw, 1rem); /* Responsive font size */
    }

    .form-image img {
      width: clamp(100px, 15vw, 200px); /* Responsive image size */
      margin-bottom: 1.5rem;
    }

    .password-container {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 10px;
      top: 10px;
      cursor: pointer;
      color: #777;
      font-size: 1rem;
    }

    /* Responsive Design - Tablet */
    @media (max-width: 768px) {
      .account-container {
        flex-direction: column;
        min-height: auto;
        margin: 0 auto;
      }

      .form,
      .form.active,
      .form:not(.active) {
        width: 100%;
        padding: 1.5rem 1rem; /* Further reduced padding */
      }

      .form:not(.active) {
        opacity: 0;
        height: 0;
        padding: 0;
        overflow: hidden;
      }

      .form.active {
        height: auto;
      }

      .form-image {
        width: 100%;
        padding: 1.5rem;
        order: -1; /* Move image to top on mobile */
      }

      .form-image img {
        width: 120px;
      }
    }

    /* Responsive Design - Small Mobile */
    @media (max-width: 480px) {
      body {
        padding: 0.5rem;
        display: block; /* Change to block layout for very small screens */
        min-height: 100vh;
      }

      .account-container {
        margin: 0.5rem auto;
        min-height: calc(100vh - 1rem); /* Full height minus padding */
      }

      .form {
        padding: 1.5rem 1rem;
      }

      .form h2 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
      }

      .form input {
        padding: 0.7rem 0.9rem;
        font-size: 0.9rem;
      }

      .form button[type="submit"] {
        padding: 0.7rem;
        font-size: 0.9rem;
      }

      .toggle-text {
        font-size: 0.8rem;
      }

      .form-image {
        padding: 1rem;
      }

      .form-image h2 {
        font-size: 1.3rem;
        margin-bottom: 0.8rem;
      }

      .form-image p {
        font-size: 0.9rem;
        margin-bottom: 1rem;
      }

      .form-image img {
        width: 100px;
        margin-bottom: 1rem;
      }

      .password-toggle {
        font-size: 0.9rem;
        top: 8px;
      }
    }

    /* Very small devices (e.g., iPhone 5/SE) */
    @media (max-width: 320px) {
      .form {
        padding: 1rem 0.8rem;
      }

      .form h2 {
        font-size: 1.3rem;
      }

      .form input {
        padding: 0.6rem 0.8rem;
      }

      .form-image {
        padding: 0.8rem;
      }
    }

    /* Landscape orientation for mobile */
    @media (max-height: 500px) and (orientation: landscape) {
      body {
        display: flex;
        padding: 0.5rem;
      }

      .account-container {
        min-height: auto;
        max-height: 100vh;
        overflow-y: auto;
      }

      .form-image {
        display: none; /* Hide image in landscape to save space */
      }

      .form,
      .form.active {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <!-- Error Messages -->
  <?php
  if (isset($_SESSION['signup_errors'])) {
    echo '<div class="error-message">';
    foreach ($_SESSION['signup_errors'] as $error) {
      echo '<p><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['signup_errors']);
  }

  if (isset($_SESSION['login_errors'])) {
    echo '<div class="error-message">';
    foreach ($_SESSION['login_errors'] as $error) {
      echo '<p><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['login_errors']);
  }
  ?>

  <div class="account-container">
    <!-- Login Form -->
    <form id="loginForm" class="form active" action="./php/login.php" method="POST">
      <h2>Welcome Back</h2>
      <input type="text" name="username" placeholder="Username or Email" required />
      <div class="password-container">
        <input type="password" name="password" id="loginPassword" placeholder="Password" required />
        <i class="fas fa-eye password-toggle" onclick="togglePassword('loginPassword', this)"></i>
      </div>
      <button type="submit">Login</button>
      <p class="toggle-text">
        Don't have an account yet?
        <button type="button" onclick="showSignup()">Sign Up</button>
      </p>
    </form>

    <!-- Signup Form -->
    <form id="signupForm" class="form" action="./php/signup.php" method="POST">
      <h2>Create Account</h2>
      <input type="text" name="username" placeholder="Username" required />
      <input type="email" name="email" placeholder="Email" required />
      <div class="password-container">
        <input type="password" name="password" id="signupPassword" placeholder="Password" required />
        <i class="fas fa-eye password-toggle" onclick="togglePassword('signupPassword', this)"></i>
      </div>
      <div class="password-container">
        <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required />
        <i class="fas fa-eye password-toggle" onclick="togglePassword('confirmPassword', this)"></i>
      </div>
      <button type="submit">Sign Up</button>
      <p class="toggle-text">
        Already have an account?
        <button type="button" onclick="showLogin()">Login</button>
      </p>
    </form>

    <!-- Decorative Image Section -->
    <div class="form-image">
      <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" alt="Book Icon">
      <h2>Join Our Book Community</h2>
      <p>Discover thousands of books and connect with fellow readers</p>
    </div>
  </div>

  <script>
    function showSignup() {
      document.getElementById('loginForm').classList.remove('active');
      document.getElementById('signupForm').classList.add('active');
    }

    function showLogin() {
      document.getElementById('signupForm').classList.remove('active');
      document.getElementById('loginForm').classList.add('active');
    }

    function togglePassword(inputId, icon) {
      const input = document.getElementById(inputId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }

    // Auto-hide error messages after 5 seconds
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(message => {
      setTimeout(() => {
        message.style.animation = 'fadeOut 0.5s ease-out';
        setTimeout(() => message.remove(), 500);
      }, 5000);
    });

    // Add fadeOut animation to styles
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
      }
    `;
    document.head.appendChild(style);

    // Handle form switching for mobile
    function handleFormSwitch() {
      if (window.innerWidth <= 768) {
        const activeForm = document.querySelector('.form.active');
        const inactiveForm = document.querySelector('.form:not(.active)');
        
        if (activeForm) {
          activeForm.style.height = 'auto';
          activeForm.style.opacity = '1';
        }
        
        if (inactiveForm) {
          inactiveForm.style.height = '0';
          inactiveForm.style.opacity = '0';
        }
      }
    }

    // Initialize and handle resize
    window.addEventListener('load', handleFormSwitch);
    window.addEventListener('resize', handleFormSwitch);

    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
      setTimeout(handleFormSwitch, 300);
    });
  </script>
</body>

</html>