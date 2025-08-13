-- Schéma de base de données pour le système de transport
CREATE DATABASE IF NOT EXISTS bus_reservation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bus_reservation;

-- Table des agences
CREATE TABLE agences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    adresse TEXT NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    licence VARCHAR(100) NOT NULL,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agence_id INT NOT NULL,
    nom_complet VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'employe', 'caissier') DEFAULT 'employe',
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE CASCADE
);

-- Table des programmes
CREATE TABLE programmes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agence_id INT NOT NULL,
    itineraire VARCHAR(255) NOT NULL,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    heure_arrivee TIME NOT NULL,
    bus VARCHAR(100) DEFAULT NULL,
    prix DECIMAL(10,2) NOT NULL,
    capacite INT NOT NULL,
    places_reservees INT DEFAULT 0,
    statut ENUM('actif', 'complet', 'annule') DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE CASCADE,
    INDEX idx_agence_date (agence_id, date_depart),
    INDEX idx_statut (statut)
);

-- Table des réservations
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    programme_id INT NOT NULL,
    nom_client VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    nombre_places INT NOT NULL,
    prix_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'confirme', 'annule') DEFAULT 'en_attente',
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    INDEX idx_programme (programme_id),
    INDEX idx_statut (statut),
    INDEX idx_client (telephone)
);

-- Table des paiements
CREATE TABLE paiements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservation_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    methode_paiement ENUM('especes', 'mobile_money', 'virement', 'carte') NOT NULL,
    reference_transaction VARCHAR(255),
    statut ENUM('en_attente', 'valide', 'echec') DEFAULT 'en_attente',
    date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
);

-- Table des logs d'activité
CREATE TABLE logs_activite (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    agence_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE CASCADE,
    INDEX idx_agence_date (agence_id, date_action)
);

-- Insertion des données de test
INSERT INTO agences (nom, adresse, telephone, email, licence) VALUES
('Transport Express Lubumbashi', 'Avenue Mobutu, Lubumbashi', '+243990000001', 'contact@transportexpress.cd', 'LIC-2024-001');

INSERT INTO utilisateurs (agence_id, nom_complet, email, mot_de_passe, role) VALUES
(1, 'Admin Transport', 'admin@transportexpress.cd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(1, 'Manager Operations', 'manager@transportexpress.cd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager');

INSERT INTO programmes (agence_id, itineraire, date_depart, heure_depart, heure_arrivee, bus, prix, capacite, places_reservees) VALUES
(1, 'Lubumbashi - Kolwezi', '2024-01-15', '08:00:00', '12:30:00', NULL, 25000.00, 25, 18),
(1, 'Lubumbashi - Likasi', '2024-01-15', '07:00:00', '09:15:00', NULL, 15000.00, 30, 30),
(1, 'Lubumbashi - Kipushi', '2024-01-15', '10:00:00', '11:30:00', NULL, 10000.00, 20, 12);

UPDATE programmes SET statut = 'complet' WHERE places_reservees >= capacite;

INSERT INTO reservations (programme_id, nom_client, telephone, email, nombre_places, prix_total, statut) VALUES
(1, 'Jean Mukamba', '+243990123456', 'jean@email.com', 2, 50000.00, 'confirme'),
(1, 'Marie Kabila', '+243990654321', 'marie@email.com', 1, 25000.00, 'en_attente'),
(2, 'Paul Tshisekedi', '+243990789123', 'paul@email.com', 1, 15000.00, 'confirme');
