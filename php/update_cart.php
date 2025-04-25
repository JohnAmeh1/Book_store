<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';
// Your processing code

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$cart_id = $data['cart_id'] ?? null;
$quantity = $data['quantity'] ?? null;

if (!$cart_id || !$quantity) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Verify this cart item belongs to the user
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    $cart_item = $stmt->fetch();

    if (!$cart_item) {
        throw new Exception('Cart item not found');
    }

    // Update quantity
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$quantity, $cart_id]);

    // Get updated cart count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $count_stmt->execute([$_SESSION['user_id']]);
    $cart_count = $count_stmt->fetch()['count'];

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'cart_count' => $cart_count
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
echo json_encode(['success' => true, 'message' => 'Quantity updated']);



// session_start();
// require_once './php/db.php';

// if (!isset($_SESSION['user_id'])) {
// echo json_encode(['success' => false, 'message' => 'Not logged in']);
// exit();
// }

// $data = json_decode(file_get_contents('php://input'), true);
// $cart_id = $data['cart_id'] ?? null;
// $quantity = $data['quantity'] ?? null;

// if (!$cart_id || !$quantity) {
// echo json_encode(['success' => false, 'message' => 'Invalid data']);
// exit();
// }

// // Verify this cart item belongs to the user
// $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ? AND user_id = ?");
// $stmt->execute([$cart_id, $_SESSION['user_id']]);
// $cart_item = $stmt->fetch();

// if (!$cart_item) {
// echo json_encode(['success' => false, 'message' => 'Cart item not found']);
// exit();
// }

// // Update quantity
// $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
// $stmt->execute([$quantity, $cart_id]);

// echo json_encode(['success' => true]);