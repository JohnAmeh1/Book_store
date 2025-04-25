<?php
session_start();
require_once './php/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: account.php");
  exit();
}

$naira = "&#x20A6;";

// Get user details
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Get all books or search results
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$author = isset($_GET['author']) ? trim($_GET['author']) : '';
$genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($query)) {
  $sql .= " AND (title LIKE ? OR author LIKE ? OR description LIKE ?)";
  $search_term = "%$query%";
  $params[] = $search_term;
  $params[] = $search_term;
  $params[] = $search_term;
}

if (!empty($category)) {
  $sql .= " AND category = ?";
  $params[] = $category;
}

if (!empty($author)) {
  $sql .= " AND author LIKE ?";
  $params[] = "%$author%";
}

if (!empty($genre)) {
  $sql .= " AND genre = ?";
  $params[] = $genre;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart count and total
$cart_stmt = $pdo->prepare("
  SELECT COUNT(*) as count, SUM(b.price * c.quantity) as total 
  FROM cart c
  JOIN books b ON c.book_id = b.id
  WHERE c.user_id = ?
");
$cart_stmt->execute([$_SESSION['user_id']]);
$cart_data = $cart_stmt->fetch(PDO::FETCH_ASSOC);
$cart_count = $cart_data['count'] ?? 0;
$cart_total = $cart_data['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Book Store - library</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #e74c3c;
      --light-color: #ecf0f1;
      --dark-color: #2c3e50;
      --sidebar-width: 300px;
      --success-color: #2ecc71;
      --warning-color: #f39c12;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: #f5f5f5;
      transition: all 0.3s ease;
      position: relative;
      min-height: 100vh;
    }

    body.sidebar-open {
      overflow: hidden;
    }

    .main-header {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 1rem;
      background-color: var(--primary-color);
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-dropdown {
      position: relative;
      cursor: pointer;
      margin-left: 1rem;
    }

    .profile-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--light-color);
      transition: transform 0.3s ease;
    }

    .profile-icon:hover {
      transform: scale(1.1);
    }

    /* Enhanced Sidebar Styles */
    .sidebar {
      height: 100vh;
      width: 0;
      position: fixed;
      z-index: 1000;
      top: 0;
      right: 0;
      background-color: white;
      overflow-x: hidden;
      transition: all 0.3s ease;
      box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
    }

    .sidebar.open {
      width: 50%;
      max-width: var(--sidebar-width);
    }

    .sidebar-content {
      padding: 2rem;
      flex: 1;
      overflow-y: auto;
    }

    .sidebar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background-color: var(--primary-color);
      color: white;
    }

    .sidebar h3 {
      margin-bottom: 1.5rem;
      color: var(--primary-color);
      font-size: 1.5rem;
      border-bottom: 2px solid var(--light-color);
      padding-bottom: 0.5rem;
    }

    .sidebar p {
      margin-bottom: 1rem;
      font-size: 1rem;
      color: var(--dark-color);
    }

    .sidebar p strong {
      color: var(--primary-color);
    }


    .profile-link {
      display: block;
      padding: 0.8rem 1rem;
      margin: 0.5rem 0;
      background-color: var(--light-color);
      color: var(--dark-color);
      text-decoration: none;
      border-radius: 5px;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .profile-link:hover {
      background-color: var(--secondary-color);
      color: white;
      transform: translateX(5px);
    }

    .profile-link i {
      margin-right: 0.5rem;
    }

    .close-btn {
      font-size: 1.8rem;
      cursor: pointer;
      color: white;
      transition: transform 0.3s ease;
    }

    .close-btn:hover {
      transform: rotate(90deg);
    }

    .header {
      text-align: center;
      padding: 6rem 0 2rem;
      background-color: var(--secondary-color);
      color: white;
    }

    .header h1 {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }

    .search-bar {
      display: flex;
      justify-content: center;
      padding: 1rem;
      background-color: var(--light-color);
      flex-wrap: wrap;
      gap: 0.5rem;
      position: sticky;
      top: 70px;
      z-index: 90;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .search-bar input,
    .search-bar select {
      padding: 0.8rem;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
      min-width: 200px;
      transition: all 0.3s ease;
    }

    .search-bar input:focus,
    .search-bar select:focus {
      outline: none;
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }

    .search-bar button {
      padding: 0.8rem 1.5rem;
      background-color: var(--secondary-color);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .search-bar button:hover {
      background-color: var(--primary-color);
      transform: translateY(-2px);
    }

    .store-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 2rem;
      padding: 2rem;
      margin-top: 1rem;
      overflow-y: auto;
    }

    .book {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .book:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .book img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-bottom: 1px solid #eee;
    }

    .book-details {
      padding: 1.5rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .book-title {
      font-weight: 600;
      margin-bottom: 0.5rem;
      font-size: 1.2rem;
      color: var(--dark-color);
    }

    .book-author {
      color: #666;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .book-price {
      font-weight: bold;
      color: var(--accent-color);
      margin-bottom: 1.5rem;
      font-size: 1.3rem;
    }

    .add-to-cart {
      width: 100%;
      padding: 0.8rem;
      background-color: var(--secondary-color);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
      margin-top: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .add-to-cart:hover {
      background-color: var(--primary-color);
      transform: translateY(-2px);
    }

    .add-to-cart:active {
      transform: translateY(0);
    }

    .cart-container {
      position: relative;
    }

    .cart-btn {
      padding: 0.6rem 1.2rem;
      background-color: var(--accent-color);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .cart-btn:hover {
      background-color: #c0392b;
      transform: translateY(-2px);
    }

    .cart-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: calc(100% + 10px);
      width: 350px;
      max-width: 90vw;
      background: white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      padding: 1.5rem;
      z-index: 101;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .cart-dropdown.open {
      display: block;
    }

    .cart-dropdown-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid #eee;
    }

    .cart-dropdown-header h3 {
      color: var(--primary-color);
      font-size: 1.2rem;
    }

    .close-cart-btn {
      background: none;
      border: none;
      font-size: 1.2rem;
      cursor: pointer;
      color: #999;
    }

    .cart-dropdown-items {
      max-height: 300px;
      overflow-y: auto;
      margin-bottom: 1rem;
    }

    .cart-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.8rem 0;
      border-bottom: 1px solid #eee;
    }

    .cart-item-info {
      flex: 1;
    }

    .cart-item-title {
      font-weight: 500;
      margin-bottom: 0.2rem;
    }

    .cart-item-price {
      font-size: 0.9rem;
      color: #666;
    }

    .cart-item-quantity {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-left: 1rem;
    }

    .quantity-btn {
      width: 25px;
      height: 25px;
      border-radius: 50%;
      background-color: var(--light-color);
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .cart-total {
      font-weight: bold;
      margin: 1.5rem 0;
      text-align: right;
      font-size: 1.2rem;
    }

    /* .checkout-btn {
      width: 100%;
      padding: 1rem;
      background-color: var(--success-color);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      font-size: 1rem;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    } */

    .cart-actions {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .view-cart-btn {
      padding: 0.8rem;
      background-color: var(--secondary-color);
      color: white;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      text-align: center;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .view-cart-btn:hover {
      background-color: var(--primary-color);
      transform: translateY(-2px);
    }

    .view-cart-btn:active {
      transform: translateY(0);
    }

    /* Adjust the checkout button to match */
    .checkout-btn {
      width: 100%;
      padding: 0.8rem;
      background-color: var(--success-color);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .checkout-btn:hover {
      background-color: #27ae60;
      transform: translateY(-2px);
    }

    .checkout-btn:active {
      transform: translateY(0);
    }

    .no-books {
      grid-column: 1 / -1;
      text-align: center;
      padding: 2rem;
      font-size: 1.2rem;
      color: #666;
    }

    /* Overlay for sidebar */
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 999;
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .overlay.active {
      display: block;
      opacity: 1;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
      .header {
        padding: 5rem 0 1.5rem;
      }

      .header h1 {
        font-size: 2rem;
      }

      .search-bar {
        top: 60px;
        padding: 0.8rem;
      }

      .search-bar input,
      .search-bar select {
        width: 100%;
        min-width: auto;
      }

      .store-container {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
      }

      .book img {
        height: 200px;
      }

      .sidebar {
        width: 0%;
      }

      .sidebar.open {
        width: 85%;
      }

      .cart-dropdown {
        width: 300px;
        right: -50px;
      }
    }

    @media (max-width: 480px) {
      .header {
        padding: 4rem 0 1rem;
      }

      .store-container {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem;
      }

      .sidebar {
        width: 0%;
      }

      /* .sidebar.open {
        width: 100%;
      } */

      .sidebar.open {
        width: 100%;
        max-width: 100%;
      }

      .sidebar-content {
        padding: 1.5rem;
      }

      .profile-link {
        padding: 1rem;
        font-size: 1.1rem;
      }

      .close-btn {
        font-size: 2rem;
      }

      .cart-dropdown {
        width: 280px;
        right: -80px;
      }
    }
  </style>
</head>

<body>
  <!-- Overlay for sidebar -->
  <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

  <header class="main-header">
    <div class="cart-container">
      <button class="cart-btn" onclick="toggleCartDropdown()">
        <i class="fas fa-shopping-cart"></i> <span id="cart-count"><?= $cart_count ?></span>
      </button>
      <div class="cart-dropdown" id="cart-dropdown">
        <div class="cart-dropdown-header">
          <h3>Your Cart</h3>
          <button class="close-cart-btn" onclick="toggleCartDropdown()">&times;</button>
        </div>
        <div class="cart-dropdown-items" id="cart-dropdown-items"></div>
        <div class="cart-total" style="color: blue;">
          Total: <?php echo $naira ?><span id="cart-dropdown-total"><?= number_format($cart_total, 2) ?></span>
        </div>
        <div class="cart-actions">
          <a href="cart.php" class="view-cart-btn">
            <i class="fas fa-eye"></i> View Full Cart
          </a>
          <button class="checkout-btn" onclick="initiatePaystackPayment()">
            <i class="fas fa-lock"></i> Proceed to Checkout
          </button>
        </div>
      </div>
    </div>

    <div class="profile-dropdown" onclick="toggleSidebar()">
      <img src="images/user-profile-icon-free-vector_prev_ui.png" alt="User" class="profile-icon" />
    </div>
  </header>

  <!-- User Profile Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h3>My Profile</h3>
      <span class="close-btn" onclick="toggleSidebar()">&times;</span>
    </div>
    <div class="sidebar-content">
      <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
      <p><strong>Member Since:</strong> <?= date('F Y', strtotime($user['created_at'])) ?></p>
      <hr>
      <!-- <a href="profile.php" class="profile-link">
        <i class="fas fa-user-edit"></i> Edit Profile
      </a> -->
      <a href="contact.php" class="profile-link">
        <i class="fas fa-phone"></i> Contact Us
      </a>
      <a href="orders.php" class="profile-link">
        <i class="fas fa-history"></i> Order History
      </a>
      <a href="logout.php" class="profile-link">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <div class="header" style="margin-top: 5px;">
    <h1>Online Book Store</h1>
  </div>

  <div class="search-bar">
    <form action="library.php" method="GET">
      <input type="text" name="query" placeholder="Search books..." value="<?= htmlspecialchars($query) ?>">

      <select name="category">
        <option value="">All Categories</option>
        <option value="fiction" <?= $category === 'fiction' ? 'selected' : '' ?>>Fiction</option>
        <option value="non-fiction" <?= $category === 'non-fiction' ? 'selected' : '' ?>>Non-Fiction</option>
        <option value="educational" <?= $category === 'educational' ? 'selected' : '' ?>>Educational</option>
        <option value="children" <?= $genre === 'children' ? 'selected' : '' ?>>Children</option>
        <option value="young-adult" <?= $genre === 'young-adult' ? 'selected' : '' ?>>Young Adult</option>
        <option value="poetry" <?= $genre === 'poetry' ? 'selected' : '' ?>>Poetry</option>
        <option value="drama" <?= $genre === 'drama' ? 'selected' : '' ?>>Drama</option>
        <option value="graphic-novel" <?= $genre === 'graphic-novel' ? 'selected' : '' ?>>Graphic Novel</option>
      </select>

      <select name="genre">
        <option value="">All Genres</option>
        <option value="sci-fi" <?= $genre === 'sci-fi' ? 'selected' : '' ?>>Sci-Fi</option>
        <option value="romance" <?= $genre === 'romance' ? 'selected' : '' ?>>Romance</option>
        <option value="mystery" <?= $genre === 'mystery' ? 'selected' : '' ?>>Mystery</option>
        <option value="fantasy" <?= $genre === 'fantasy' ? 'selected' : '' ?>>Fantasy</option>
        <option value="horror" <?= $genre === 'horror' ? 'selected' : '' ?>>Horror</option>
        <option value="thriller" <?= $genre === 'thriller' ? 'selected' : '' ?>>Thriller</option>
        <option value="memoir" <?= $genre === 'memoir' ? 'selected' : '' ?>>Memoir</option>
        <option value="adventure" <?= $genre === 'adventure' ? 'selected' : '' ?>>Adventure</option>
        <option value="historical" <?= $genre === 'historical' ? 'selected' : '' ?>>Historical</option>
        <option value="self-help" <?= $genre === 'self-help' ? 'selected' : '' ?>>Self-Help</option>
        <option value="biography" <?= $genre === 'biography' ? 'selected' : '' ?>>Biography</option>
        <option value="cookbook" <?= $genre === 'cookbook' ? 'selected' : '' ?>>Cookbook</option>
        <option value="travel" <?= $genre === 'travel' ? 'selected' : '' ?>>Travel</option>
        <option value="health" <?= $genre === 'health' ? 'selected' : '' ?>>Health</option>
        <option value="business" <?= $genre === 'business' ? 'selected' : '' ?>>Business</option>
        <option value="finance" <?= $genre === 'finance' ? 'selected' : '' ?>>Finance</option>
        <option value="technology" <?= $genre === 'technology' ? 'selected' : '' ?>>Technology</option>
        <option value="sports" <?= $genre === 'sports' ? 'selected' : '' ?>>Sports</option>
        <option value="music" <?= $genre === 'music' ? 'selected' : '' ?>>Music</option>
        <option value="art" <?= $genre === 'art' ? 'selected' : '' ?>>Art</option>
      </select>

      <button type="submit" style="width: 100%; margin-top: 8px;">
        <i class="fas fa-search"></i> Search
      </button>
    </form>
  </div>

  <div class="store-container" id="store">
    <?php if (empty($books)): ?>
      <p class="no-books">No books found matching your criteria.</p>
    <?php else: ?>
      <?php foreach ($books as $book): ?>
        <div class="book">
          <img src="<?= htmlspecialchars($book['image_path'] ?: 'https://via.placeholder.com/150') ?>" alt="<?= htmlspecialchars($book['title']) ?>">
          <div class="book-details">
            <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
            <div class="book-author">by <?= htmlspecialchars($book['author']) ?></div>
            <div class="book-price"><?php echo $naira ?><?= number_format($book['price'], 2) ?></div>
            <button class="add-to-cart" onclick="addToCart(<?= $book['id'] ?>, '<?= htmlspecialchars(addslashes($book['title'])) ?>', <?= $book['price'] ?>, event)">
              <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Paystack Payment Modal -->
  <div id="paystack-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 500px; width: 90%;">
      <h2 style="margin-bottom: 1rem;">Complete Your Payment</h2>
      <p style="margin-bottom: 1.5rem;">You will be redirected to Paystack to complete your payment of <?php echo $naira ?><span id="payment-amount"><?= number_format($cart_total, 2) ?></span></p>
      <div style="display: flex; gap: 1rem;">
        <button onclick="document.getElementById('paystack-modal').style.display = 'none'" style="padding: 0.8rem 1.5rem; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
        <button onclick="processPaystackPayment()" style="padding: 0.8rem 1.5rem; background: #2ecc71; color: white; border: none; border-radius: 5px; cursor: pointer;">Proceed</button>
      </div>
    </div>
  </div>

  <script src="https://js.paystack.co/v1/inline.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    const nairaSymbol = '₦'; // Define the naira symbol
    //     const nairaSymbol = '&#x20A6;';
    // // Or if you need the actual symbol:
    // const nairaSymbol = '\u20A6';
    // Toggle sidebar with overlay
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const body = document.body;

      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
      body.classList.toggle('sidebar-open');

      // Prevent scrolling when sidebar is open
      if (sidebar.classList.contains('open')) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
    // Toggle cart dropdown
    function toggleCartDropdown() {
      const dropdown = document.getElementById('cart-dropdown');
      dropdown.classList.toggle('open');
      if (dropdown.classList.contains('open')) {
        loadCartItems();
      }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const cartContainer = document.querySelector('.cart-container');
      const dropdown = document.getElementById('cart-dropdown');

      if (!cartContainer.contains(event.target) && dropdown.classList.contains('open')) {
        dropdown.classList.remove('open');
      }
    });

    // Close sidebar when clicking on overlay
    document.getElementById('overlay').addEventListener('click', function() {
      toggleSidebar();
    });

    // Close sidebar when clicking on a link
    document.querySelectorAll('.profile-link').forEach(link => {
      link.addEventListener('click', function() {
        toggleSidebar();
      });
    });

    // In library.php, update the loadCartItems function:
    function loadCartItems() {
      fetch('./php/get_cart.php')
        .then(response => response.json())
        .then(data => {
          const cartItemsContainer = document.getElementById('cart-dropdown-items');
          const cartTotalElement = document.getElementById('cart-dropdown-total');
          const cartCountElement = document.getElementById('cart-count');

          cartItemsContainer.innerHTML = '';
          let total = 0;
          let itemCount = 0;

          if (data.length === 0) {
            cartItemsContainer.innerHTML = `
                    <div style="text-align: center; padding: 1rem; color: #666;">
                        <i class="fas fa-shopping-cart" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <p>Your cart is empty</p>
                    </div>
                `;
          } else {
            data.forEach(item => {
              // Convert price to number if it's a string
              const price = typeof item.price === 'string' ? parseFloat(item.price) : item.price;
              const quantity = typeof item.quantity === 'string' ? parseInt(item.quantity) : item.quantity;

              const cartItem = document.createElement('div');
              cartItem.className = 'cart-item';
              cartItem.innerHTML = `
                        <div class="cart-item-info">
                            <div class="cart-item-title" style="color: blue;">${item.title}</div>
                            <div class="cart-item-meta">
                                <span class="cart-item-price">${nairaSymbol}${price.toFixed(2)} each</span>
                                <span class="cart-item-subtotal">Subtotal: ${nairaSymbol}${(price * quantity).toFixed(2)}</span>
                            </div>
                        </div>
                        <div class="cart-item-quantity">
                            
                            <a class="remove-btn" style="color: red; text-decoration: none; cursor: pointer;" onclick="removeFromCart(${item.id}, event)">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    `;
              cartItemsContainer.appendChild(cartItem);
              total += price * quantity;
              itemCount += quantity;
            });

            // Add a divider before the total
            const divider = document.createElement('hr');
            divider.style.margin = '1rem 0';
            divider.style.border = 'none';
            divider.style.borderTop = '1px solid #eee';
            cartItemsContainer.appendChild(divider);
          }

          cartTotalElement.textContent = total.toFixed(2);
          cartCountElement.textContent = itemCount;
          document.getElementById('payment-amount').textContent = total.toFixed(2);
        })
        .catch(error => {
          console.error('Error loading cart items:', error);
          cartItemsContainer.innerHTML = `
                <div style="text-align: center; padding: 1rem; color: #666;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                    <p>Error loading cart items</p>
                </div>
            `;
        });
    }

    // Add to cart
    function addToCart(bookId, title, price, event) {
      // If event is not passed (from onclick), use window.event
      const btn = event?.target || window.event?.target;

      fetch('./php/add_to_cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            book_id: bookId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update cart count
            document.getElementById('cart-count').textContent = data.cart_count;
            // Show success message with sweet animation
            if (btn) {
              btn.innerHTML = '<i class="fas fa-check"></i> Added!';
              btn.style.backgroundColor = 'var(--success-color)';
              setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
                btn.style.backgroundColor = 'var(--secondary-color)';
              }, 1500);
            }
          } else {
            alert(data.message || 'Failed to add to cart');
          }
        });
    }

    // Update cart item quantity
    function updateCartItem(cartId, newQuantity) {
      if (newQuantity < 1) {
        // Remove item if quantity is 0
        removeFromCart(cartId);
        return;
      }

      fetch('./php/update_cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            cart_id: cartId,
            quantity: newQuantity
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            loadCartItems();
          } else {
            alert(data.message || 'Failed to update cart');
          }
        });
    }

    // Remove from cart
    function removeFromCart(cartId) {
      fetch('./php/remove_from_cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            cart_id: cartId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            loadCartItems();
          } else {
            alert(data.message || 'Failed to remove from cart');
          }
        });
    }

    // Initiate Paystack payment
    function initiatePaystackPayment() {
      const total = parseFloat(document.getElementById('cart-dropdown-total').textContent);
      if (total <= 0) {
        alert('Your cart is empty');
        return;
      }

      // Show payment modal
      document.getElementById('paystack-modal').style.display = 'flex';
    }

    // Process Paystack payment
    function processPaystackPayment() {
      const total = parseFloat(document.getElementById('cart-dropdown-total').textContent) * 100; // Convert to kobo
      const email = '<?= $user["email"] ?>';

      // Close modal
      document.getElementById('paystack-modal').style.display = 'none';

      // Initialize Paystack
      const handler = PaystackPop.setup({
        key: 'pk_test_fdeb97ce15dc119e28cc589fcb24fac669b14f81', // Replace with your Paystack public key
        email: email,
        amount: total,
        currency: 'NGN',
        ref: 'BOOK_' + Math.floor(Math.random() * 1000000000 + 1),
        callback: function(response) {
          // Payment successful - verify and process order
          verifyPayment(response.reference);
        },
        onClose: function() {
          // User closed payment modal
          console.log('Payment window closed');
        }
      });

      handler.openIframe();
    }

    // Verify Paystack payment
    function verifyPayment(reference) {
      fetch('./php/verify_payment.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            reference: reference
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Payment verified - complete checkout
            completeCheckout();
          } else {
            alert('Payment verification failed: ' + (data.message || 'Unknown error'));
          }
        });
    }

    // Complete checkout after payment verification
    function completeCheckout() {
      fetch('./php/checkout.php', {
          method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show success message with download options
            if (confirm('Payment successful! Would you like to download your books now?')) {
              // Auto-download books
              downloadPurchasedBooks(data.order_id);
            } else {
              // Just update UI
              document.getElementById('cart-count').textContent = '0';
              document.getElementById('cart-dropdown-total').textContent = '0.00';
              document.getElementById('cart-dropdown').classList.remove('open');
              alert('You can download your books anytime from your Order History.');
            }
          } else {
            alert('Checkout failed: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Checkout error:', error);
          alert('An error occurred during checkout');
        });
    }

    // Download purchased books
    function downloadPurchasedBooks(orderId) {
      fetch('./php/get_purchased_books.php?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.books.length > 0) {
            // Download each book with a small delay between them
            data.books.forEach((book, index) => {
              setTimeout(() => {
                if (book.filepath) {
                  // Create a temporary link element
                  const link = document.createElement('a');
                  link.style.display = 'none';
                  link.href = 'download_book.php?book_id=' + book.id;
                  link.download = book.title + '.' + book.filepath.split('.').pop();
                  document.body.appendChild(link);
                  link.click();
                  document.body.removeChild(link);
                }
              }, index * 1000); // 1 second delay between downloads
            });
          } else {
            console.log('No books to download or error:', data);
          }
        })
        .catch(error => {
          console.error('Download error:', error);
        });
    }
  </script>
</body>

</html>