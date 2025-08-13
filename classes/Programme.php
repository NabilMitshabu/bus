<?php
require_once(__DIR__ . '/../config/database.php');

class Programme {
    private $db;
    private $table_name = "programmes";

    public $id;
    public $agence_id;
    public $itineraire;
    public $date_depart;
    public $heure_depart;
    public $heure_arrivee;
    public $bus;
    public $prix;
    public $capacite;
    public $places_reservees;
    public $statut;

    public function __construct($db = null) {
        $this->db = $db ? $db : new Database();
    }

    // Récupérer tous les programmes
    public function lireTous($agence_id = null) {
        $sql = "SELECT * FROM programmes";
        $params = [];
        
        if ($agence_id) {
            $sql .= " WHERE agence_id = ?";
            $params[] = $agence_id;
        }
        
        $sql .= " ORDER BY date_depart DESC, heure_depart ASC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Créer un nouveau programme
    public function creer($data) {
        $sql = "INSERT INTO programmes (agence_id, itineraire, date_depart, heure_depart, heure_arrivee, bus, prix, capacite, places_reservees, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 'actif')";
        // Correction : forcer le format de l'heure au format HH:00
        $heure_depart = $data['heure_depart'];
        if (preg_match('/^\\d{1,2}$/', $heure_depart)) {
            $heure_depart = sprintf('%02d:00', (int)$heure_depart);
        }
        $params = [
            $data['agence_id'],
            $data['itineraire'],
            $data['date_depart'],
            $heure_depart,
            $data['heure_arrivee'],
            isset($data['bus']) ? $data['bus'] : null,
            $data['prix'],
            $data['capacite']
        ];
        $stmt = $this->db->query($sql, $params);
        return $stmt !== false;
    }

    // Récupérer un programme par ID
    public function lireParId($id) {
        $sql = "SELECT * FROM programmes WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    // Vérifier si le programme est complet
    public function estComplet($programme_id) {
        $programme = $this->lireParId($programme_id);
        return $programme ? $programme['places_reservees'] >= $programme['capacite'] : false;
    }

    // Mettre à jour le nombre de places réservées
    public function mettreAJourPlaces($programme_id, $nouvelles_places) {
        $programme = $this->lireParId($programme_id);
        if (!$programme) return false;
        
        $statut = $nouvelles_places >= $programme['capacite'] ? 'complet' : 'actif';
        
        $sql = "UPDATE programmes SET places_reservees = ?, statut = ? WHERE id = ?";
        $stmt = $this->db->query($sql, [$nouvelles_places, $statut, $programme_id]);
        return $stmt !== false;
    }

    // Obtenir les statistiques
    public function obtenirStatistiques($agence_id = null) {
        $where_clause = $agence_id ? "WHERE agence_id = ?" : "";
        $params = $agence_id ? [$agence_id] : [];
        
        $sql = "SELECT 
                    COUNT(*) as total_programmes,
                    SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as programmes_actifs,
                    SUM(CASE WHEN statut = 'complet' THEN 1 ELSE 0 END) as programmes_complets,
                    SUM(capacite) as total_places,
                    SUM(places_reservees) as total_reservees
                FROM programmes $where_clause";
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : [];
        
        if ($result) {
            $result['taux_occupation'] = $result['total_places'] > 0 ? 
                round(($result['total_reservees'] / $result['total_places']) * 100, 2) : 0;
        }
        
        return $result ?: [
            'total_programmes' => 0,
            'programmes_actifs' => 0,
            'programmes_complets' => 0,
            'taux_occupation' => 0
        ];
    }

    // Supprimer un programme
    public function supprimer($id) {
        $sql = "DELETE FROM programmes WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt !== false;
    }

    // Mettre à jour un programme
    public function mettreAJour($id, $data) {
        $sql = "UPDATE programmes SET 
                    itineraire = ?, 
                    date_depart = ?, 
                    heure_depart = ?, 
                    heure_arrivee = ?, 
                    bus = ?,
                    prix = ?, 
                    capacite = ?
                WHERE id = ?";
        $params = [
            $data['itineraire'],
            $data['date_depart'],
            $data['heure_depart'],
            $data['heure_arrivee'],
            isset($data['bus']) ? $data['bus'] : null,
            $data['prix'],
            $data['capacite'],
            $id
        ];
        $stmt = $this->db->query($sql, $params);
        return $stmt !== false;
    }
}
?>