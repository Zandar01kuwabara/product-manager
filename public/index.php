<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$q = trim((string) ($_GET['q'] ?? ''));

if ($q !== '') {
    $sql = "SELECT id, name, category, price, stock, created_at
            FROM products
            WHERE name LIKE :q OR category LIKE :q
            ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['q' => "%{$q}%"]);
} else {
    $stmt = $pdo->query(
        'SELECT id, name, category, price, stock, created_at
         FROM products
         ORDER BY id DESC'
    );
}

$products = $stmt->fetchAll();
$flash = consume_flash();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">PEMROGRAMAN WEB • PERTEMUAN 3</p>
                <h1>Product Manager</h1>
                <p class="subtitle">Manajemen produk PHP–MySQL dengan CRUD, validasi, PRG, PDO, CSRF, dan UI responsif.</p>
            </div>
            <a class="btn btn-primary" href="create.php">+ Tambah Produk</a>
        </header>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <section class="toolbar card">
            <form method="GET" class="search-form">
                <label for="q">Cari produk</label>
                <div class="search-row">
                    <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Nama atau kategori...">
                    <button class="btn" type="submit">Cari</button>
                    <?php if ($q !== ''): ?>
                        <a class="btn btn-secondary" href="index.php">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section>
            <div class="section-heading">
                <h2>Daftar Produk</h2>
                <span class="count"><?= count($products) ?> produk</span>
            </div>

            <?php if (!$products): ?>
                <div class="empty card">
                    <h3>Belum ada produk</h3>
                    <p>Tambahkan produk pertama untuk mulai menggunakan aplikasi.</p>
                    <a class="btn btn-primary" href="create.php">Tambah Produk</a>
                </div>
            <?php else: ?>
                <div class="products">
                    <?php foreach ($products as $product): ?>
                        <article class="card product-card">
                            <div class="product-top">
                                <span class="badge"><?= e($product['category']) ?></span>
                                <span class="product-id">#<?= e($product['id']) ?></span>
                            </div>

                            <h3><?= e($product['name']) ?></h3>
                            <p class="price">Rp <?= number_format((float) $product['price'], 0, ',', '.') ?></p>

                            <div class="meta-grid">
                                <div>
                                    <span>Stok</span>
                                    <strong><?= e($product['stock']) ?></strong>
                                </div>
                                <div>
                                    <span>ID</span>
                                    <strong><?= e($product['id']) ?></strong>
                                </div>
                            </div>

                            <div class="actions">
                                <a class="btn btn-secondary" href="edit.php?id=<?= e($product['id']) ?>">Edit</a>
                                <form method="POST" action="delete.php" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                    <input type="hidden" name="id" value="<?= e($product['id']) ?>">
                                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                    <button class="btn btn-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <span>Product Manager • PHP + MySQL</span>
            <span>PDO prepared statement • XSS escaping • CSRF</span>
        </footer>
    </main>
</body>
</html>
