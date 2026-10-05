<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method tidak diizinkan.');
}

if (!verify_csrf($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Token CSRF tidak valid.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID produk tidak valid.');
}

$stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() > 0) {
    flash('success', 'Produk berhasil dihapus.');
} else {
    flash('error', 'Produk tidak ditemukan atau sudah dihapus.');
}

redirect('index.php?status=deleted');
