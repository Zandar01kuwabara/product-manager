CREATE DATABASE IF NOT EXISTS store_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE store_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL DEFAULT 'Umum',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data uji opsional
INSERT INTO products (name, category, price, stock)
SELECT 'Laptop ASUS', 'Elektronik', 7500000, 5
WHERE NOT EXISTS (
    SELECT 1 FROM products WHERE name = 'Laptop ASUS'
);

INSERT INTO products (name, category, price, stock)
SELECT 'Mouse Wireless', 'Aksesoris', 250000, 12
WHERE NOT EXISTS (
    SELECT 1 FROM products WHERE name = 'Mouse Wireless'
);

INSERT INTO products (name, category, price, stock)
SELECT 'Keyboard Mechanical', 'Aksesoris', 850000, 8
WHERE NOT EXISTS (
    SELECT 1 FROM products WHERE name = 'Keyboard Mechanical'
);
