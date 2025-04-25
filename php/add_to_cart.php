<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$book_id = $data['book_id'] ?? null;

if (!$book_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid book ID']);
    exit();
}

// Check if book already in cart
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND book_id = ?");
$stmt->execute([$_SESSION['user_id'], $book_id]);
$existing = $stmt->fetch();

if ($existing) {
    // Update quantity
    $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
    $stmt->execute([$existing['id']]);
} else {
    // Add new item
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, book_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $book_id]);
}

// Get updated cart count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_count = $stmt->fetch()['count'];

echo json_encode(['success' => true, 'cart_count' => $cart_count]);
