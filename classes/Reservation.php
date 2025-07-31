<?php
require_once(__DIR__ . '/../config/database.php');


class Reservation {
    private $db;
    private $table_name = "reservations";

    public $id;
    public $programme_id;
    public $nom_client;
    public $telephone;
    public $email;
    public $nombre_places;
    public $prix_total;
    public $statut;
    public $date_reservation;

    public function __construct($db = null) {
        $this->db = $db ? $db : new Database();
    }

    // Récupérer toutes les réservations
    public function lireTous($agence_id = null) {
        $sql = "SELECT r.*, p.itineraire, p.date_depart, p.heure_depart 
                FROM reservations r 
                JOIN programmes p ON r.programme_id = p.id";
        $params = [];
        
        if ($agence_id) {
            $sql .= " WHERE p.agence_id = ?";
            $params[] = $agence_id;
        }
        
        $sql .= " ORDER BY r.date_reservation DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Créer une nouvelle réservation
    public function creer($data) {
        // Vérifier si le programme a encore des places disponibles
        $programme = $this->obtenirProgramme($data['programme_id']);
        if (!$programme) return false;

        $places_disponibles = $programme['capacite'] - $programme['places_reservees'];
        if ($data['nombre_places'] > $places_disponibles) {
            return false; // Pas assez de places
        }

        try {
            // Commencer une transaction
            $this->db->conn->beginTransaction();
            
            // Insérer la réservation
            $sql = "INSERT INTO reservations (programme_id, nom_client, telephone, email, nombre_places, prix_total, statut) 
                    VALUES (?, ?, ?, ?, ?, ?, 'en_attente')";
            
            $params = [
                $data['programme_id'],
                $data['nom_client'],
                $data['telephone'],
                $data['email'],
                $data['nombre_places'],
                $data['prix_total']
            ];
            
            $stmt = $this->db->query($sql, $params);
            if (!$stmt) {
                $this->db->conn->rollBack();
                return false;
            }
            
            // Mettre à jour le nombre de places réservées dans le programme
            $nouvelles_places = $programme['places_reservees'] + $data['nombre_places'];
            $this->mettreAJourPlacesProgramme($data['programme_id'], $nouvelles_places);
            
            $this->db->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->conn->rollBack();
            return false;
        }
    }

    // Valider une réservation
    public function valider($reservation_id) {
        $sql = "UPDATE reservations SET statut = 'confirme' WHERE id = ?";
        $stmt = $this->db->query($sql, [$reservation_id]);
        return $stmt !== false;
    }

    // Annuler une réservation
    public function annuler($reservation_id) {
        try {
            $this->db->conn->beginTransaction();
            
            // Récupérer les détails de la réservation
            $reservation = $this->lireParId($reservation_id);
            if (!$reservation) {
                $this->db->conn->rollBack();
                return false;
            }
            
            // Mettre à jour le statut de la réservation
            $sql = "UPDATE reservations SET statut = 'annule' WHERE id = ?";
            $stmt = $this->db->query($sql, [$reservation_id]);
            if (!$stmt) {
                $this->db->conn->rollBack();
                return false;
            }
            
            // Libérer les places dans le programme
            $programme = $this->obtenirProgramme($reservation['programme_id']);
            if ($programme) {
                $nouvelles_places = $programme['places_reservees'] - $reservation['nombre_places'];
                $this->mettreAJourPlacesProgrammeTotal($reservation['programme_id'], $nouvelles_places);
            }
            
            $this->db->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->conn->rollBack();
            return false;
        }
    }

    // Récupérer une réservation par ID
    public function lireParId($id) {
        $sql = "SELECT * FROM reservations WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    // Obtenir un programme par ID
    private function obtenirProgramme($programme_id) {
        $sql = "SELECT * FROM programmes WHERE id = ?";
        $stmt = $this->db->query($sql, [$programme_id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    // Mettre à jour les places réservées (nouveau total)
    private function mettreAJourPlacesProgramme($programme_id, $nouveau_total) {
        $programme = $this->obtenirProgramme($programme_id);
        if (!$programme) return false;
        
        $statut = $nouveau_total >= $programme['capacite'] ? 'complet' : 'actif';
        
        $sql = "UPDATE programmes SET places_reservees = ?, statut = ? WHERE id = ?";
        $stmt = $this->db->query($sql, [$nouveau_total, $statut, $programme_id]);
        return $stmt !== false;
    }

    // Mettre à jour le total des places réservées
    private function mettreAJourPlacesProgrammeTotal($programme_id, $nouveau_total) {
        return $this->mettreAJourPlacesProgramme($programme_id, $nouveau_total);
    }

    // Obtenir les statistiques des réservations
    public function obtenirStatistiques($agence_id = null) {
        $where_clause = "";
        $params = [];
        
        if ($agence_id) {
            $where_clause = "WHERE p.agence_id = ?";
            $params[] = $agence_id;
        }
        
        $sql = "SELECT 
                    COUNT(r.id) as total_reservations,
                    SUM(CASE WHEN r.statut = 'confirme' THEN 1 ELSE 0 END) as confirmees,
                    SUM(CASE WHEN r.statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN r.statut = 'annule' THEN 1 ELSE 0 END) as annulees,
                    SUM(CASE WHEN r.statut = 'confirme' THEN r.prix_total ELSE 0 END) as revenus_total
                FROM reservations r
                JOIN programmes p ON r.programme_id = p.id
                $where_clause";
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : [];
        
        return $result ?: [
            'total_reservations' => 0,
            'confirmees' => 0,
            'en_attente' => 0,
            'annulees' => 0,
            'revenus_total' => 0
        ];
    }
}
?>