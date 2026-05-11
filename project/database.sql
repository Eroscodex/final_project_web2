CREATE DATABASE IF NOT EXISTS mini_store;
USE mini_store;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    receipt_code VARCHAR(50) NOT NULL UNIQUE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

INSERT IGNORE INTO users (username, password, role) VALUES
('admin', '1234', 'admin'),
('staff', '1234', 'staff');

INSERT IGNORE INTO products (product_name, price, stock) VALUES
('Neon Energy Drink', 2.50, 100),
('Cyber Snack', 1.75, 50),
('Matrix Keyboard', 45.00, 10);