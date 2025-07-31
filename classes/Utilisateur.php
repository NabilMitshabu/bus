<?php
require_once(__DIR__ . '/../config/database.php');

class Utilisateur {
    private $db;
    private $table_name = "utilisateurs";

    public $id;
    public $agence_id;
    public $nom_complet;
    public $email;
    public $mot_de_passe;
    public $role;
    public $statut;
    public $date_creation;

    public function __construct($db = null) {
        $this->db = $db ? $db : new Database();
    }

    // Créer un nouvel utilisateur
    public function creer($data) {
        $sql = "INSERT INTO utilisateurs (agence_id, nom_complet, email, mot_de_passe, role, statut) 
                VALUES (?, ?, ?, ?, ?, 'actif')";
        
        $params = [
            $data['agence_id'],
            $data['nom_complet'],
            $data['email'],
            password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            $data['role'] ?? 'employe'
        ];
        
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $this->db->lastInsertId() : false;
    }

    // Récupérer tous les utilisateurs d'une agence
    public function lireTousParAgence($agence_id) {
        $sql = "SELECT * FROM utilisateurs WHERE agence_id = ? ORDER BY nom_complet ASC";
        $stmt = $this->db->query($sql, [$agence_id]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Récupérer un utilisateur par ID
    public function lireParId($user_id) {
        foreach (MockDatabase::$utilisateurs as $user) {
            if ($user['id'] == $user_id) {
                return $user;
            }
        }
        return null;
    }

    // Mettre à jour un utilisateur
    public function mettreAJour($user_id, $data) {
        foreach (MockDatabase::$utilisateurs as &$user) {
            if ($user['id'] == $user_id) {
                $user['nom_complet'] = $data['nom_complet'] ?? $user['nom_complet'];
                $user['email'] = $data['email'] ?? $user['email'];
                $user['role'] = $data['role'] ?? $user['role'];
                $user['statut'] = $data['statut'] ?? $user['statut'];
                
                if (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
                    $user['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
                }
                return true;
            }
        }
        return false;
    }

    // Supprimer un utilisateur (désactiver)
    public function supprimer($user_id) {
        foreach (MockDatabase::$utilisateurs as &$user) {
            if ($user['id'] == $user_id) {
                $user['statut'] = 'inactif';
                return true;
            }
        }
        return false;
    }

    // Vérifier les permissions
    public function aPermission($user_id, $permission) {
        $user = $this->lireParId($user_id);
        if (!$user) return false;

        $permissions = [
            'admin' => ['gerer_programmes', 'gerer_reservations', 'voir_rapports', 'gerer_utilisateurs', 'configurer_agence'],
            'manager' => ['gerer_programmes', 'gerer_reservations', 'voir_rapports'],
            'employe' => ['gerer_reservations'],
            'caissier' => ['gerer_reservations', 'voir_rapports_financiers']
        ];

        return in_array($permission, $permissions[$user['role']] ?? []);
    }

    // Obtenir les statistiques des utilisateurs
    public function obtenirStatistiques($agence_id) {
        $utilisateurs = $this->lireTousParAgence($agence_id);
        $stats = [
            'total' => count($utilisateurs),
            'actifs' => 0,
            'inactifs' => 0,
            'par_role' => []
        ];

        foreach ($utilisateurs as $user) {
            if ($user['statut'] == 'actif') {
                $stats['actifs']++;
            } else {
                $stats['inactifs']++;
            }

            if (!isset($stats['par_role'][$user['role']])) {
                $stats['par_role'][$user['role']] = 0;
            }
            $stats['par_role'][$user['role']]++;
        }

        return $stats;
    }

    // Obtenir l'activité récente des utilisateurs
    public function obtenirActiviteRecente($agence_id, $limite = 10) {
        // Simuler l'activité récente basée sur les réservations
        $activites = [];
        
        foreach (MockDatabase::$reservations as $reservation) {
            // Vérifier si la réservation appartient à cette agence
            foreach (MockDatabase::$programmes as $programme) {
                if ($programme['id'] == $reservation['programme_id'] && $programme['agence_id'] == $agence_id) {
                    $activites[] = [
                        'type' => 'reservation',
                        'description' => "Nouvelle réservation de {$reservation['nom_client']} pour {$programme['itineraire']}",
                        'date' => $reservation['date_reservation'],
                        'utilisateur' => 'Système', // Dans un vrai système, on aurait l'ID de l'utilisateur
                        'statut' => $reservation['statut']
                    ];
                    break;
                }
            }
        }

        // Trier par date décroissante
        usort($activites, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activites, 0, $limite);
    }
}
?>
