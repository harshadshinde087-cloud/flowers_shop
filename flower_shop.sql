-- Online Flower & Greeting Delivery System Database Structure
-- Created for XAMPP MySQL

CREATE DATABASE IF NOT EXISTS flower_shop;
USE flower_shop;

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES 
('Flowers', 'Fresh and beautiful flowers for every occasion'),
('Bouquets', 'Artistically arranged flower bouquets'),
('Greeting Cards', 'Beautiful greeting cards for all occasions'),
('Combos', 'Special flower and card combinations');

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    stock_quantity INT DEFAULT 0,
    image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admin (username, email, password) VALUES 
('admin', 'admin@flowershop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    shipping_address TEXT,
    payment_method VARCHAR(50) DEFAULT 'Cash on Delivery',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Cart table (for guest users and temporary storage)
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

-- Insert sample products
INSERT INTO products (name, description, price, category_id, stock_quantity, image, is_featured) VALUES 
('Red Rose Bouquet', 'Beautiful arrangement of fresh red roses perfect for expressing love', 45.99, 2, 50, 'red_rose_bouquet.jpg', TRUE),
('White Lily Flowers', 'Elegant white lilies that bring peace and serenity', 35.99, 1, 30, 'white_lily.jpg', TRUE),
('Birthday Greeting Card', 'Colorful birthday card with heartfelt message', 5.99, 3, 100, 'birthday_card.jpg', FALSE),
('Anniversary Combo', 'Special combo of flowers and card for anniversary celebrations', 55.99, 4, 25, 'anniversary_combo.jpg', TRUE),
('Pink Carnations', 'Soft pink carnations that symbolize gratitude and love', 28.99, 1, 40, 'pink_carnations.jpg', FALSE),
('Thank You Card', 'Elegant thank you card for expressing gratitude', 4.99, 3, 80, 'thank_you_card.jpg', FALSE),
('Mixed Flower Bouquet', 'Vibrant mix of seasonal flowers in beautiful arrangement', 52.99, 2, 35, 'mixed_bouquet.jpg', TRUE),
('Get Well Soon Card', 'Cheerful get well soon card to brighten someone\'s day', 6.99, 3, 60, 'get_well_card.jpg', FALSE),
('Sunflower Bundle', 'Bright and cheerful sunflowers that bring happiness', 32.99, 1, 45, 'sunflower_bundle.jpg', FALSE),
('Wedding Combo', 'Premium wedding combo with exotic flowers and card', 89.99, 4, 15, 'wedding_combo.jpg', TRUE);
