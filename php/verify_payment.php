<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Set your Paystack secret key
$paystackSecretKey = 'sk_test_b91e0557f6dca556b24425e6f6683cba1e86c25b';

$data = json_decode(file_get_contents('php://input'), true);
$reference = $data['reference'] ?? null;

if (!$reference) {
    echo json_encode(['success' => false, 'message' => 'No reference provided']);
    exit();
}

// Initialize cURL session for Paystack verification
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . rawurlencode($reference));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $paystackSecretKey
]);

// Execute the cURL request
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['success' => false, 'message' => 'cURL Error: ' . $err]);
    exit();
}

$result = json_decode($response, true);

if (!$result || !isset($result['status']) || !$result['status']) {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    exit();
}

// Check if payment was successful
if ($result['data']['status'] !== 'success') {
    echo json_encode(['success' => false, 'message' => 'Payment not successful']);
    exit();
}

// Verify the amount matches what's in the cart
$cart_stmt = $pdo->prepare("
    SELECT SUM(b.price * c.quantity) as total 
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$cart_stmt->execute([$_SESSION['user_id']]);
$cart_total = $cart_stmt->fetch(PDO::FETCH_ASSOC)['total'] * 100; // Convert to kobo

if ($result['data']['amount'] != $cart_total) {
    echo json_encode(['success' => false, 'message' => 'Amount mismatch']);
    exit();
}

// Payment is verified and valid
echo json_encode([
    'success' => true,
    'data' => [
        'amount' => $result['data']['amount'] / 100,
        'reference' => $result['data']['reference'],
        'paid_at' => $result['data']['paid_at']
    ]
]);




// session_start();
// require_once './php/db.php';

// if (!isset($_SESSION['user_id'])) {
// echo json_encode(['success' => false, 'message' => 'Not logged in']);
// exit();
// }

// $data = json_decode(file_get_contents('php://input'), true);
// $reference = $data['reference'] ?? null;

// if (!$reference) {
// echo json_encode(['success' => false, 'message' => 'No reference provided']);
// exit();
// }

// // In a real application, you would verify with Paystack API here
// // This is a simplified version for demonstration

// // Mock verification - in production, use Paystack's API
// $verified = true; // Assume payment is verified

// if ($verified) {
// echo json_encode(['success' => true]);
// } else {
// echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
// }