<?php
session_start();
require_once './php/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: account.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: account.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        }
    }

    // Handle book file upload
    $file_path = '';
    if (isset($_FILES['book_file']) && $_FILES['book_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'book_files/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = uniqid() . '_' . basename($_FILES['book_file']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['book_file']['tmp_name'], $target_path)) {
            $file_path = $target_path;
        }
    }

    // Insert book into database
    $stmt = $pdo->prepare("INSERT INTO books (title, author, price, category, genre, image_path, file_path, description) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $author, $price, $category, $genre, $image_path, $file_path, $description]);

    $success = "Book added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | BookStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fa;
            min-height: 100vh;
            padding: 1rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .admin-header h1 {
            color: var(--primary);
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .logout-btn {
            background-color: var(--danger);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background-color: #d90429;
            transform: translateY(-2px);
        }

        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .file-upload {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .file-upload-label {
            display: inline-block;
            padding: 0.8rem;
            background-color: var(--primary);
            color: white;
            border-radius: var(--border-radius);
            cursor: pointer;
            text-align: center;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .file-upload-label:hover {
            background-color: var(--secondary);
        }

        .file-name {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.3rem;
        }

        button[type="submit"] {
            width: 100%;
            padding: 1rem;
            background-color: var(--success);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        button[type="submit"]:hover {
            background-color: #3a86ff;
            transform: translateY(-2px);
        }

        .success-message {
            background-color: var(--success);
            color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-container {
                padding: 1.5rem;
            }

            .admin-header h1 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }

            .admin-container {
                padding: 1.2rem;
            }

            .admin-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .logout-btn {
                width: 100%;
                justify-content: center;
            }

            input,
            textarea,
            select {
                padding: 0.7rem;
            }

            .file-upload-label {
                padding: 0.7rem;
            }
        }

        @media (max-width: 400px) {
            .admin-container {
                padding: 1rem;
            }

            .admin-header h1 {
                font-size: 1.3rem;
            }

            .form-group {
                margin-bottom: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-book"></i> Add New Book</h1>
            <a href="?logout=1" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <form action="admin.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="fiction">Fiction</option>
                    <option value="non-fiction">Non-Fiction</option>
                    <option value="educational">Educational</option>
                    <option value="children">Children</option>
                    <option value="young-adult">Young Adult</option>
                    <option value="poetry">Poetry</option>
                    <option value="drama">Drama</option>
                    <option value="graphic-novel">Graphic Novel</option>
                </select>
            </div>

            <div class="form-group">
                <label for="genre">Genre</label>
                <select id="genre" name="genre" required>
                    <option value="sci-fi">Sci-Fi</option>
                    <option value="romance">Romance</option>
                    <option value="mystery">Mystery</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="horror">Horror</option>
                    <option value="adventure">Adventure</option>
                    <option value="historical">Historical</option>
                    <option value="self-help">Self-Help</option>
                    <option value="biography">Biography</option>
                    <option value="cookbook">Cookbook</option>
                    <option value="horror">Horror</option>
                    <option value="thriller">Thriller</option>
                    <option value="memoir">Memoir</option>
                    <option value="travel">Travel</option>
                    <option value="health">Health</option>
                    <option value="business">Business</option>
                    <option value="finance">Finance</option>
                    <option value="technology">Technology</option>
                    <option value="sports">Sports</option>
                    <option value="music">Music</option>
                    <option value="art">Art</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>Book Cover Image</label>
                <div class="file-upload">
                    <label for="image" class="file-upload-label">
                        <i class="fas fa-image"></i> Choose Image
                    </label>
                    <input type="file" id="image" name="image" accept="image/*" style="display: none;" onchange="displayFileName(this, 'image-name')">
                    <span id="image-name" class="file-name">No image selected</span>
                </div>
            </div>

            <div class="form-group">
                <label>Book File (PDF/DOC/ZIP etc.)</label>
                <div class="file-upload">
                    <label for="book_file" class="file-upload-label">
                        <i class="fas fa-file-upload"></i> Choose Book File
                    </label>
                    <input type="file" id="book_file" name="book_file" accept=".pdf,.doc,.docx,.epub,.mobi,.zip" style="display: none;" onchange="displayFileName(this, 'file-name')">
                    <span id="file-name" class="file-name">No file selected</span>
                </div>
            </div>

            <button type="submit">
                <i class="fas fa-plus-circle"></i> Add Book
            </button>
        </form>
    </div>

    <script>
        function displayFileName(input, spanId) {
            const fileNameSpan = document.getElementById(spanId);
            if (input.files.length > 0) {
                fileNameSpan.textContent = input.files[0].name;
                fileNameSpan.style.color = getComputedStyle(document.documentElement).getPropertyValue('--dark').trim();
            } else {
                fileNameSpan.textContent = 'No file selected';
                fileNameSpan.style.color = '#666';
            }
        }
    </script>
</body>

</html>