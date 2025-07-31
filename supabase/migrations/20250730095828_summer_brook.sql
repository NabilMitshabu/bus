-- Base de données pour l'application de transport Lubumbashi
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS transport_lubumbashi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE transport_lubumbashi;

-- Table des agences
CREATE TABLE IF NOT EXISTS agences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(100),
    proprietaire VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif'
);

-- Table des programmes de transport
CREATE TABLE IF NOT EXISTS programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agence_id INT NOT NULL,
    itineraire VARCHAR(100) NOT NULL,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    heure_arrivee TIME NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    capacite INT NOT NULL,
    places_reservees INT DEFAULT 0,
    statut ENUM('actif', 'complet', 'annule') DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE CASCADE,
    INDEX idx_date_depart (date_depart),
    INDEX idx_itineraire (itineraire),
    INDEX idx_statut (statut)
);

-- Table des réservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    programme_id INT NOT NULL,
    nom_client VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    nombre_places INT NOT NULL,
    prix_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'confirme', 'annule') DEFAULT 'en_attente',
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    INDEX idx_statut (statut),
    INDEX idx_date_reservation (date_reservation)
);

-- Table des utilisateurs (pour l'authentification des agences)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agence_id INT NOT NULL,
    nom_utilisateur VARCHAR(50) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom_complet VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'gestionnaire', 'operateur') DEFAULT 'operateur',
    derniere_connexion TIMESTAMP NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE CASCADE
);

-- Table des notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agence_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    titre VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    lue BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE CASCADE,
    INDEX idx_lue (lue),
    INDEX idx_date_creation (date_creation)
);

-- Insertion des données de test
INSERT INTO agences (id, nom, adresse, telephone, email, proprietaire) VALUES
(1, 'Transport Express Lubumbashi', 'Avenue Mobutu, Centre-ville Lubumbashi', '+243990123456', 'contact@transportexpress.cd', 'Jean Baptiste Mukamba');

INSERT INTO utilisateurs (agence_id, nom_utilisateur, mot_de_passe, nom_complet, email, role) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur Transport', 'admin@transportexpress.cd', 'admin');

-- Triggers pour gérer les notifications automatiques
DELIMITER //

CREATE TRIGGER after_reservation_insert 
AFTER INSERT ON reservations
FOR EACH ROW
BEGIN
    DECLARE programme_capacite INT;
    DECLARE total_reservees INT;
    DECLARE agence_programme INT;
    
    -- Obtenir les informations du programme
    SELECT p.capacite, p.places_reservees, p.agence_id 
    INTO programme_capacite, total_reservees, agence_programme
    FROM programmes p 
    WHERE p.id = NEW.programme_id;
    
    -- Vérifier si le programme est maintenant complet
    IF total_reservees >= programme_capacite THEN
        INSERT INTO notifications (agence_id, type, titre, message) VALUES
        (agence_programme, 'programme_complet', 'Programme Complet', 
         CONCAT('Le programme ID ', NEW.programme_id, ' a atteint sa capacité maximale.'));
    END IF;
    
    -- Notification pour nouvelle réservation
    INSERT INTO notifications (agence_id, type, titre, message) VALUES
    (agence_programme, 'nouvelle_reservation', 'Nouvelle Réservation', 
     CONCAT('Nouvelle réservation de ', NEW.nom_client, ' pour ', NEW.nombre_places, ' place(s).'));
END//

CREATE TRIGGER after_programme_update 
AFTER UPDATE ON programmes
FOR EACH ROW
BEGIN
    -- Notification si le programme devient complet
    IF NEW.statut = 'complet' AND OLD.statut != 'complet' THEN
        INSERT INTO notifications (agence_id, type, titre, message) VALUES
        (NEW.agence_id, 'programme_complet', 'Programme Complet', 
         CONCAT('Le programme ', NEW.itineraire, ' du ', NEW.date_depart, ' est maintenant complet.'));
    END IF;
END//

DELIMITER ;

-- Vues pour les statistiques
CREATE VIEW vue_statistiques_programmes AS
SELECT 
    agence_id,
    COUNT(*) as total_programmes,
    SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as programmes_actifs,
    SUM(CASE WHEN statut = 'complet' THEN 1 ELSE 0 END) as programmes_complets,
    SUM(capacite) as total_places,
    SUM(places_reservees) as total_reservees,
    ROUND((SUM(places_reservees) / SUM(capacite)) * 100, 2) as taux_occupation
FROM programmes 
GROUP BY agence_id;

CREATE VIEW vue_statistiques_reservations AS
SELECT 
    p.agence_id,
    COUNT(*) as total_reservations,
    SUM(CASE WHEN r.statut = 'confirme' THEN 1 ELSE 0 END) as confirmees,
    SUM(CASE WHEN r.statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
    SUM(CASE WHEN r.statut = 'annule' THEN 1 ELSE 0 END) as annulees,
    SUM(CASE WHEN r.statut = 'confirme' THEN r.prix_total ELSE 0 END) as revenus_total
FROM reservations r
JOIN programmes p ON r.programme_id = p.id
GROUP BY p.agence_id;