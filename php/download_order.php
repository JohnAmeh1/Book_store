<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('HTTP/1.0 400 Bad Request');
    exit('No order ID provided');
}

// Verify this order belongs to the user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('HTTP/1.0 404 Not Found');
    exit('Order not found');
}

// Get purchased books
$stmt = $pdo->prepare("
    SELECT b.id, b.title, b.file_path
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($books)) {
    header('HTTP/1.0 404 Not Found');
    exit('No books found in this order');
}

// Create a zip file if there are multiple books
if (count($books) > 1) {
    $zip = new ZipArchive();
    $zip_name = "order_{$order_id}_books.zip";
    $zip_path = sys_get_temp_dir() . '/' . $zip_name;

    if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
        exit("Cannot create zip file");
    }

    foreach ($books as $book) {
        $filepath = __DIR__ . '/' . $book['file_path'];
        if (file_exists($filepath)) {
            $extension = pathinfo($filepath, PATHINFO_EXTENSION);
            $zip->addFile($filepath, $book['title'] . '.' . $extension);
        }
    }

    $zip->close();

    // Download the zip file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zip_name . '"');
    header('Content-Length: ' . filesize($zip_path));
    readfile($zip_path);

    // Delete the temporary zip file
    unlink($zip_path);
    exit;
} else {
    // For single book, redirect to the normal download
    header("Location: ../download_book.php?book_id=" . $books[0]['id']);
    exit;
}
