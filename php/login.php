<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validation
    $errors = [];

    if (empty($username)) {
        $errors[] = "Username is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if ($user['username'] === 'admin') {
                header("Location: ../admin.php");
            } else {
                header("Location: ../library.php");
            }
            exit();
        } else {
            $errors[] = "Invalid username or password";
        }
    }

    // Return to login page with errors
    session_start();
    $_SESSION['login_errors'] = $errors;
    header("Location: ../account.php");
    exit();
}
