<?php
session_start();
require_once './php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

// Get user's orders with items
$stmt = $pdo->prepare("
    SELECT o.id, o.order_date, o.total_amount, 
           GROUP_CONCAT(b.title SEPARATOR ', ') as items
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN books b ON oi.book_id = b.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order History</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
            position: relative; /* For positioning the home button */
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: #3498db;
        }

        .home-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #3498db;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s ease;
        }

        .home-btn:hover {
            background: #2980b9;
        }

        .order-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .order h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .order p {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .order p:last-child {
            margin-bottom: 0;
        }

        .download-btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .download-btn:hover {
            background: #2980b9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            .home-btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            .order h3 {
                font-size: 1.3rem;
            }

            .order p {
                font-size: 0.9rem;
            }

            .download-btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .home-btn {
                top: 10px;
                right: 10px;
                padding: 7px 10px;
                font-size: 0.85rem;
            }

            .order {
                padding: 15px;
            }

            .order h3 {
                font-size: 1.2rem;
            }

            .order p {
                font-size: 0.85rem;
            }

            .download-btn {
                padding: 7px 10px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <!-- Go Home Button -->
    <a href="library.php" class="home-btn">
        🏠 
    </a>

    <h1>Your Order History</h1>

    <?php if (empty($orders)): ?>
        <p style="text-align: center; margin-top: 20px;">You haven't placed any orders yet.</p>
    <?php else: ?>
        <div class="order-container">
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <h3>Order #<?= htmlspecialchars($order['id']) ?></h3>
                    <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                    <p><strong>Total:</strong> ₦<?= number_format($order['total_amount'], 2) ?></p>
                    <p><strong>Items:</strong> <?= htmlspecialchars($order['items']) ?></p>
                    <!-- <a href="./php/download_order.php?order_id=<?= htmlspecialchars($order['id']) ?>" class="download-btn">
                        Download All Books
                    </a> -->
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>

</html>