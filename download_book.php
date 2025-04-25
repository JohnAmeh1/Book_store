<?php
session_start();
require_once './php/db.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

$book_id = $_GET['book_id'] ?? null;

if (!$book_id) {
    header('HTTP/1.0 400 Bad Request');
    exit('No book ID provided');
}

// Verify user has purchased this book
$stmt = $pdo->prepare("
    SELECT b.id, b.title, b.file_path 
    FROM books b
    JOIN order_items oi ON b.id = oi.book_id
    JOIN orders o ON oi.order_id = o.id
    WHERE b.id = ? AND o.user_id = ?
    LIMIT 1
");
$stmt->execute([$book_id, $_SESSION['user_id']]);
$book = $stmt->fetch();

if (!$book || !$book['file_path']) {
    header('HTTP/1.0 404 Not Found');
    exit('Book not found, not purchased, or no file available');
}

$filepath = __DIR__ . '/' . $book['file_path'];

// Check if file exists
if (!file_exists($filepath)) {
    header('HTTP/1.0 404 Not Found');
    exit('File not found');
}

// Get file extension
$extension = pathinfo($filepath, PATHINFO_EXTENSION);

// Set headers for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($book['title']) . '.' . $extension . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

readfile($filepath);
exit;
