

CREATE DATABASE IF NOT EXISTS ecommerce_db CHARACTER SET utf8 COLLATE utf8_general_ci;
USE ecommerce_db;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: products (liee a categories)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    categorie_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin','client') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donnees de test
INSERT INTO categories (nom) VALUES ('Electronique'), ('Vetements'), ('Livres'), ('Maison');

INSERT INTO products (nom, description, prix, stock, categorie_id) VALUES
('Smartphone Samsung', 'Un smartphone puissant avec grand ecran', 3500.00, 10, 1),
('T-shirt Blanc', 'T-shirt en coton 100%', 120.00, 50, 2),
('Roman Harry Potter', 'Livre populaire pour tous ages', 85.00, 20, 3),
('Lampe de bureau', 'Lampe LED moderne economique', 200.00, 15, 4),
('Laptop Dell', 'Ordinateur portable performant', 8500.00, 5, 1),
('Pantalon Jean', 'Jean bleu classique coupe droite', 350.00, 30, 2);


