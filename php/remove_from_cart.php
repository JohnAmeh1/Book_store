<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$cart_id = $data['cart_id'] ?? null;

if (!$cart_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit();
}

// Verify this cart item belongs to the user
$stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ? AND user_id = ?");
$stmt->execute([$cart_id, $_SESSION['user_id']]);
$cart_item = $stmt->fetch();

if (!$cart_item) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit();
}

// Remove item
$stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
$stmt->execute([$cart_id]);

echo json_encode(['success' => true]);
