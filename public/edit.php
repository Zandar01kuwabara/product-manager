<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID produk tidak valid.');
}

$stmt = $pdo->prepare('SELECT id, name, category, price, stock FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? '')) ?: 'Umum';
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama minimal 3 karakter.';
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = 'Nama maksimal 100 karakter.';
    }

    if (mb_strlen($category) > 50) {
        $errors['category'] = 'Kategori maksimal 50 karakter.';
    }

    if ($price === false || $price <= 0) {
        $errors['price'] = 'Harga harus lebih dari 0.';
    }

    if ($stock === false || $stock < 0) {
        $errors['stock'] = 'Stok tidak boleh negatif.';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE products
                 SET name = :name, category = :category, price = :price, stock = :stock
                 WHERE id = :id'
            );
            $stmt->execute([
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'id' => $id,
            ]);

            flash('success', 'Produk berhasil diperbarui.');
            redirect('index.php?status=updated');
        } catch (PDOException $e) {
            if ((int) $e->errorInfo[1] === 1062) {
                $errors['name'] = 'Nama produk sudah digunakan. Gunakan nama lain.';
            } else {
                $errors['general'] = 'Terjadi kesalahan saat memperbarui produk.';
            }
        }
    }

    $product['name'] = $name;
    $product['category'] = $category;
    $product['price'] = $price !== false ? $price : $product['price'];
    $product['stock'] = $stock !== false ? $stock : $product['stock'];
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="container narrow">
        <header class="page-header compact">
            <div>
                <p class="eyebrow">UPDATE • #<?= e($product['id']) ?></p>
                <h1>Edit Produk</h1>
                <p class="subtitle">Perbarui data produk berdasarkan ID.</p>
            </div>
            <a class="btn btn-secondary" href="index.php">← Kembali</a>
        </header>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" class="card form-card" novalidate>
            <div class="form-group">
                <label for="name">Nama produk</label>
                <input id="name" name="name" type="text" value="<?= e($product['name']) ?>" minlength="3" maxlength="100" required autofocus>
                <?php if (isset($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input id="category" name="category" type="text" value="<?= e($product['category']) ?>" maxlength="50">
                <?php if (isset($errors['category'])): ?><small class="field-error"><?= e($errors['category']) ?></small><?php endif; ?>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="price">Harga</label>
                    <input id="price" name="price" type="number" value="<?= e($product['price']) ?>" min="0.01" step="0.01" required>
                    <?php if (isset($errors['price'])): ?><small class="field-error"><?= e($errors['price']) ?></small><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="stock">Stok</label>
                    <input id="stock" name="stock" type="number" value="<?= e($product['stock']) ?>" min="0" step="1" required>
                    <?php if (isset($errors['stock'])): ?><small class="field-error"><?= e($errors['stock']) ?></small><?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="index.php">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </main>
</body>
</html>
