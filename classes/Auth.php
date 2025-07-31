<?php
class Auth {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->db = new Database();
    }

    public function loginAgence($email, $password) {
        $query = "SELECT a.*, u.id as user_id, u.nom_complet, u.role, u.mot_de_passe 
                  FROM agences a
                  JOIN utilisateurs u ON a.id = u.agence_id
                  WHERE u.email = ? AND u.statut = 'actif'";
        
        $stmt = $this->db->query($query, [$email]);
        $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['agence_id'] = $user['id'];
            $_SESSION['agence_nom'] = $user['nom'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_nom'] = $user['nom_complet'];
            $_SESSION['user_email'] = $user['email'];
            
            // Log de connexion
            $this->logActivity($user['user_id'], $user['id'], 'connexion', 'Connexion utilisateur');
            
            return true;
        }
        
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['agence_id']) && isset($_SESSION['user_id']);
    }

    public function getAgenceId() {
        return $_SESSION['agence_id'] ?? null;
    }

    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }

    public function hasPermission($permission) {
        $role = $this->getUserRole();
        
        $permissions = [
            'admin' => ['gerer_programmes', 'gerer_reservations', 'voir_rapports', 'gerer_utilisateurs', 'configurer_agence'],
            'manager' => ['gerer_programmes', 'gerer_reservations', 'voir_rapports'],
            'employe' => ['gerer_reservations'],
            'caissier' => ['gerer_reservations', 'voir_rapports_financiers']
        ];

        return in_array($permission, $permissions[$role] ?? []);
    }

    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logActivity($this->getUserId(), $this->getAgenceId(), 'deconnexion', 'Déconnexion utilisateur');
        }
        session_destroy();
    }

    private function logActivity($user_id, $agence_id, $action, $description) {
        $sql = "INSERT INTO logs_activite (utilisateur_id, agence_id, action, description, ip_address) 
                VALUES (?, ?, ?, ?, ?)";
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->db->query($sql, [$user_id, $agence_id, $action, $description, $ip]);
    }
}