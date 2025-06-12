CREATE DATABASE tmshop;
USE tmshop;

-- Table des catégories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Table des utilisateurs (clients)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des administrateurs
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Table des produits
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Table des commandes
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product VARCHAR(255) NOT NULL,
    size VARCHAR(50),
    shoe_size VARCHAR(50),
    belt_length VARCHAR(50),
    quantity INT NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 7.00,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('en attente', 'traitée', 'expédiée', 'livrée') DEFAULT 'en attente',
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('espace', 'carte') DEFAULT 'espace',
    FOREIGN KEY (user_id) REFERENCES users(id)
);



-- Table des informations de contact
CREATE TABLE contact_info (
    id INT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL
);

-- Insertion des catégories
INSERT INTO categories (name) VALUES
('pull'), ('ceinture'), ('blouson'), ('chaussures'),
('chemises'), ('pantalon'), ('costumes'), ('cravate');

-- Insertion des produits
INSERT INTO products (category_id, name, price, image) VALUES
((SELECT id FROM categories WHERE name = 'pull'), 'Pull Cachemire', 60.00, 'images/pull cachemire.png'),
((SELECT id FROM categories WHERE name = 'pull'), 'Pull Old Money', 60.00, 'images/polo old.png'),
((SELECT id FROM categories WHERE name = 'ceinture'), 'Ceinture en Cuir', 45.00, 'images/ceinture.png'),
((SELECT id FROM categories WHERE name = 'ceinture'), 'Ceinture Tressée', 50.00, 'images/ceinture tresse.png'),
((SELECT id FROM categories WHERE name = 'blouson'), 'Blazer', 320.00, 'images/blazer.png'),
((SELECT id FROM categories WHERE name = 'blouson'), 'Doudoune', 220.00, 'images/dodoune.png'),
((SELECT id FROM categories WHERE name = 'blouson'), 'Manteau', 250.00, 'images/manteau.png'),
((SELECT id FROM categories WHERE name = 'chaussures'), 'Classiques', 179.00, 'images/chaussure1.png'),
((SELECT id FROM categories WHERE name = 'chaussures'), 'Bottes', 199.00, 'images/chaussure bottes.png'),
((SELECT id FROM categories WHERE name = 'chaussures'), 'Basket', 139.00, 'images/basket.png'),
((SELECT id FROM categories WHERE name = 'chemises'), 'Chemise Classique', 85.00, 'images/chemise classique.png'),
((SELECT id FROM categories WHERE name = 'chemises'), 'Chemise à Rayures', 90.00, 'images/chemise old.png'),
((SELECT id FROM categories WHERE name = 'chemises'), 'Chemise en Lin Courte', 70.00, 'images/chemise en lin courte.png'),
((SELECT id FROM categories WHERE name = 'pantalon'), 'Pantalon Chino', 99.00, 'images/pantalon chino.png'),
((SELECT id FROM categories WHERE name = 'pantalon'), 'Pantalon Old Money', 99.00, 'images/pantalon old.png'),
((SELECT id FROM categories WHERE name = 'pantalon'), 'Jeans Old Money', 99.00, 'images/jean.png'),
((SELECT id FROM categories WHERE name = 'costumes'), 'Costume Classique', 999.00, 'images/costume classique.png'),
((SELECT id FROM categories WHERE name = 'costumes'), 'Costume Old Money', 999.00, 'images/costume old.png'),
((SELECT id FROM categories WHERE name = 'costumes'), 'Costume Rayures', 999.00, 'images/costume rayures.png'),
((SELECT id FROM categories WHERE name = 'cravate'), 'Cravate Classique', 29.00, 'images/cravatte clasique.png'),
((SELECT id FROM categories WHERE name = 'cravate'), 'Cravate Rayée', 29.00, 'images/cravatte raye.png');

-- Insertion des informations de contact
INSERT INTO contact_info (id, email, phone, address) VALUES
(1, 'tmshop@gmail.com', '+21650100100', '123 Avenue de la Mode, Tunis, Tunisie');

-- Insertion d'un administrateur avec mot de passe en clair
INSERT INTO admins (name, email, password, created_at) VALUES
('Bousbia Thamer', 'bousbiathamer@gmail.com', 'tmshop', NOW());

-- Note: Le mot de passe est en clair et est 'tmshop'