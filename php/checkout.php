<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Get cart items with book details
    $stmt = $pdo->prepare("
        SELECT c.book_id, c.quantity, b.price, b.title, b.file_path
        FROM cart c
        JOIN books b ON c.book_id = b.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        throw new Exception('Cart is empty');
    }

    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, order_date, total_amount, status)
        VALUES (?, NOW(), 0, 'completed')
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $order_id = $pdo->lastInsertId();

    $total = 0;

    // Add order items
    foreach ($cart_items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;

        $order_item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, book_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $order_item_stmt->execute([
            $order_id,
            $item['book_id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    // Update order total
    $update_stmt = $pdo->prepare("
        UPDATE orders SET total_amount = ? WHERE id = ?
    ");
    $update_stmt->execute([$total, $order_id]);

    // Clear cart
    $delete_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $delete_stmt->execute([$_SESSION['user_id']]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order completed successfully'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// session_start();
// require_once 'db.php';

// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Not logged in']);
//     exit();
// }

// try {
//     $pdo->beginTransaction();

//     // Get cart items
//     $stmt = $pdo->prepare("
//         SELECT book_id, quantity FROM cart WHERE user_id = ?
//     ");
//     $stmt->execute([$_SESSION['user_id']]);
//     $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     if (empty($cart_items)) {
//         throw new Exception('Cart is empty');
//     }

//     // Create order
//     $stmt = $pdo->prepare("
//         INSERT INTO orders (user_id, order_date, total_amount)
//         VALUES (?, NOW(), 0)
//     ");
//     $stmt->execute([$_SESSION['user_id']]);
//     $order_id = $pdo->lastInsertId();

//     $total = 0;

//     // Add order items
//     foreach ($cart_items as $item) {
//         // Get book price
//         $book_stmt = $pdo->prepare("SELECT price FROM books WHERE id = ?");
//         $book_stmt->execute([$item['book_id']]);
//         $book = $book_stmt->fetch();

//         $subtotal = $book['price'] * $item['quantity'];
//         $total += $subtotal;

//         $order_item_stmt = $pdo->prepare("
//             INSERT INTO order_items (order_id, book_id, quantity, price)
//             VALUES (?, ?, ?, ?)
//         ");
//         $order_item_stmt->execute([
//             $order_id,
//             $item['book_id'],
//             $item['quantity'],
//             $book['price']
//         ]);
//     }

//     // Update order total
//     $update_stmt = $pdo->prepare("
//         UPDATE orders SET total_amount = ? WHERE id = ?
//     ");
//     $update_stmt->execute([$total, $order_id]);

//     // Clear cart
//     $delete_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
//     $delete_stmt->execute([$_SESSION['user_id']]);

//     $pdo->commit();

//     echo json_encode(['success' => true]);
// } catch (Exception $e) {
//     $pdo->rollBack();
//     echo json_encode(['success' => false, 'message' => $e->getMessage()]);
// }
