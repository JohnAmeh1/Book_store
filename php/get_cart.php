<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$stmt = $pdo->prepare("
    SELECT c.id, b.id as book_id, b.title, b.price, c.quantity 
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($cart_items);
