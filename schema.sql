-- schema.sql
-- Base de données pour Magic Brush

CREATE DATABASE IF NOT EXISTS magic_brush CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE magic_brush;

-- Utilisateurs (Clients et Admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Catégories de produits
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    espece ENUM('chien', 'chat', 'universel') NOT NULL,
    parent_id INT DEFAULT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Produits
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    prix_barre DECIMAL(10, 2) DEFAULT NULL,
    stock INT DEFAULT 0,
    categorie_id INT,
    espece ENUM('chien', 'chat', 'universel') NOT NULL,
    type_poil ENUM('court', 'long', 'epais', 'tous') DEFAULT 'tous',
    sku VARCHAR(50) UNIQUE,
    image_principale VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Images secondaires des produits
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    statut ENUM('en_attente', 'payee', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    total DECIMAL(10, 2) NOT NULL,
    adresse_livraison TEXT NOT NULL,
    mode_paiement ENUM('stripe', 'livraison') DEFAULT 'stripe',
    stripe_id VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Lignes de commande
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Avis Clients
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    note INT NOT NULL CHECK (note >= 1 AND note <= 5),
    commentaire TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion de données de démonstration

-- Admin par défaut (Mot de passe: admin123)
INSERT INTO users (nom, prenom, email, password_hash, role) VALUES 
('Admin', 'Magic Brush', 'admin@magicbrush.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Catégories
INSERT INTO categories (nom, slug, espece) VALUES 
('Brosses Classiques', 'brosses-classiques', 'chien'),
('Gants de Massage', 'gants-massage', 'chat'),
('Démêloirs', 'demeloirs', 'universel');

-- Produits
INSERT INTO products (nom, slug, description, prix, stock, categorie_id, espece, image_principale) VALUES 
('Brosse Premium Bois & Métal', 'brosse-premium-bois-metal', 'Brosse idéale pour les poils mi-longs.', 24.90, 50, 1, 'chien', 'assets/img/uploads/dog_brush.png'),
('Gant de Massage Silicone', 'gant-massage-silicone', 'Gant doux pour chats.', 19.50, 100, 2, 'chat', 'assets/img/uploads/cat_glove.png'),
('Peigne Démêlant Ergonomique', 'peigne-demelant-ergonomique', 'Peigne universel anti-nœuds.', 15.90, 30, 3, 'universel', 'assets/img/uploads/comb.png');
