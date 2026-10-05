<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

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
                'INSERT INTO products (name, category, price, stock)
                 VALUES (:name, :category, :price, :stock)'
            );
            $stmt->execute([
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
            ]);

            flash('success', 'Produk berhasil disimpan.');
            redirect('index.php?status=created');
        } catch (PDOException $e) {
            if ((int) $e->errorInfo[1] === 1062) {
                $errors['name'] = 'Nama produk sudah digunakan. Gunakan nama lain.';
            } else {
                $errors['general'] = 'Terjadi kesalahan saat menyimpan produk.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="container narrow">
        <header class="page-header compact">
            <div>
                <p class="eyebrow">CREATE</p>
                <h1>Tambah Produk</h1>
                <p class="subtitle">Masukkan data produk lalu simpan.</p>
            </div>
            <a class="btn btn-secondary" href="index.php">← Kembali</a>
        </header>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" class="card form-card" novalidate>
            <div class="form-group">
                <label for="name">Nama produk</label>
                <input id="name" name="name" type="text" value="<?= old_value('name') ?>" minlength="3" maxlength="100" required autofocus>
                <?php if (isset($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input id="category" name="category" type="text" value="<?= old_value('category') ?>" maxlength="50" placeholder="Contoh: Elektronik">
                <?php if (isset($errors['category'])): ?><small class="field-error"><?= e($errors['category']) ?></small><?php endif; ?>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="price">Harga</label>
                    <input id="price" name="price" type="number" value="<?= old_value('price') ?>" min="0.01" step="0.01" required>
                    <?php if (isset($errors['price'])): ?><small class="field-error"><?= e($errors['price']) ?></small><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="stock">Stok</label>
                    <input id="stock" name="stock" type="number" value="<?= old_value('stock') ?>" min="0" step="1" required>
                    <?php if (isset($errors['stock'])): ?><small class="field-error"><?= e($errors['stock']) ?></small><?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="index.php">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan Produk</button>
            </div>
        </form>
    </main>
</body>
</html>
