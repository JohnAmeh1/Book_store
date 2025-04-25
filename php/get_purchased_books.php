<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'No order ID provided']);
    exit();
}

// Verify this order belongs to the user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

// Get purchased books with filepaths
$stmt = $pdo->prepare("
    SELECT b.id, b.title, b.file_path as filepath
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'books' => $books,
    'order_id' => $order_id
]);
