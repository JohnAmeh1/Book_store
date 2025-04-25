<?php
session_start();
require_once './php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

$naira = "&#x20A6;";

// Get cart items with book details
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, b.id as book_id, b.title, b.price, b.image_path, c.quantity
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart | BookStore</title>
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: var(--dark);
        }

        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 2rem;
        }

        .cart-header h1 {
            font-size: 2rem;
            color: var(--primary);
            margin: 0;
        }

        .continue-shopping {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .continue-shopping:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        .empty-cart {
            text-align: center;
            padding: 3rem;
        }

        .empty-cart p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }

        .cart-items {
            margin: 2rem 0;
        }

        .cart-item {
            display: flex;
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            align-items: center;
            transition: var(--transition);
            border-radius: var(--border-radius);
        }

        .cart-item:hover {
            background-color: rgba(67, 97, 238, 0.05);
            transform: translateY(-2px);
        }

        .cart-item-image {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-right: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .cart-item-price {
            color: var(--accent);
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            margin: 1rem 0;
        }

        .quantity-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--light);
            border: 1px solid #ddd;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: var(--transition);
        }

        .quantity-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .quantity-display {
            margin: 0 1rem;
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        .remove-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            margin-left: 1rem;
            font-size: 0.9rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remove-btn:hover {
            background: #d90429;
            transform: translateY(-2px);
        }

        .cart-summary {
            margin-top: 3rem;
            padding: 2rem;
            background: var(--light);
            border-radius: var(--border-radius);
            text-align: right;
        }

        .cart-total {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .checkout-btn {
            padding: 1rem 2.5rem;
            background: var(--success);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 1rem;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
        }

        .checkout-btn:hover {
            background: #3a86ff;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(58, 134, 255, 0.4);
        }

        #paystack-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            max-width: 500px;
            width: 90%;
            box-shadow: var(--box-shadow);
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .modal-btn {
            padding: 0.8rem 1.8rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-btn-cancel {
            background: var(--danger);
            color: white;
        }

        .modal-btn-proceed {
            background: var(--success);
            color: white;
        }

        .modal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .cart-container {
                padding: 1rem;
                margin: 1rem auto;
            }

            .cart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding-bottom: 1rem;
            }

            .cart-header h1 {
                font-size: 1.5rem;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem;
            }

            .cart-item-image {
                width: 80px;
                height: 120px;
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .quantity-controls {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .remove-btn {
                margin-left: 0;
                margin-top: 0.5rem;
                width: 100%;
                justify-content: center;
            }

            .cart-summary {
                padding: 1.5rem;
                text-align: center;
            }

            .cart-total {
                font-size: 1.5rem;
            }

            .checkout-btn {
                width: 100%;
                justify-content: center;
                padding: 1rem;
            }

            .modal-content {
                padding: 1.5rem;
            }

            .modal-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }

            .modal-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .cart-container {
                padding: 0.5rem;
            }

            .cart-item-details {
                width: 100%;
            }

            .cart-item-title {
                font-size: 1rem;
            }

            .cart-item-price {
                font-size: 1rem;
            }

            .quantity-btn {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .quantity-display {
                margin: 0 0.5rem;
            }

            .modal-content {
                width: 95%;
                padding: 1rem;
            }

            .modal-content h2 {
                font-size: 1.2rem;
            }

            .modal-content p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Your Shopping Cart</h1>
            <a href="library.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="library.php" class="btn">
                    <i class="fas fa-book"></i> Browse Books
                </a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image_path'] ?: 'https://via.placeholder.com/100') ?>"
                            alt="<?= htmlspecialchars($item['title']) ?>"
                            class="cart-item-image">
                        <div class="cart-item-details">
                            <h3 class="cart-item-title"><?= htmlspecialchars($item['title']) ?></h3>
                            <p class="cart-item-price"><?= $naira ?><?= number_format($item['price'], 2) ?></p>
                            <div class="quantity-controls">
                                
                                <button class="remove-btn" onclick="removeItem(<?= $item['cart_id'] ?>)">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2 class="cart-total">Total: <?= $naira ?><?= number_format($total, 2) ?></h2>
                <button class="checkout-btn" onclick="initiatePaystackPayment()">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </button>
            </div>
        <?php endif; ?>
    </div>

    <div id="paystack-modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 1rem;">Complete Your Payment</h2>
            <p style="margin-bottom: 1.5rem; font-size: 1.1rem;">
                You will be redirected to Paystack to complete your payment of
                <strong><?php echo $naira ?><?= number_format($total, 2) ?></strong>
            </p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="document.getElementById('paystack-modal').style.display = 'none'">
                    Cancel
                </button>
                <button class="modal-btn modal-btn-proceed" onclick="processPaystackPayment()">
                    Proceed to Payment
                </button>
            </div>
        </div>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) {
                removeItem(cartId);
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update quantity'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating quantity');
                });
        }

        function removeItem(cartId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('./php/remove_from_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            cart_id: cartId
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to remove item'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing item');
                    });
            }
        }

        function initiatePaystackPayment() {
            const total = <?= $total ?>;
            if (total <= 0) {
                alert('Your cart is empty');
                return;
            }

            // Show payment modal
            document.getElementById('paystack-modal').style.display = 'flex';
        }

        function processPaystackPayment() {
            const total = <?= $total * 100 ?>; // Convert to kobo
            const email = '<?= $_SESSION['email'] ?? 'customer@example.com' ?>';

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
                    verifyPayment(response.reference);
                },
                onClose: function() {
                    console.log('Payment window closed');
                }
            });

            handler.openIframe();
        }

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
                        alert('Payment successful! Your order is being processed.');
                        // Redirect to order confirmation page
                        window.location.href = 'order_confirmation.php';
                    } else {
                        alert('Payment verification failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred during payment verification');
                });
        }
    </script>
</body>

</html>