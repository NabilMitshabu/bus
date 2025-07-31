<?php
if (session_status() == PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_start();
    }
}

class Database {
    private $host = 'localhost';
    private $db_name = 'bus_reservation';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function __construct() {
        $this->getConnection();
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }
        return $this->conn;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $exception) {
            echo "Erreur de requête: " . $exception->getMessage();
            return false;
        }
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}

// Simulation de données pour la démo
class MockDatabase {
    public static $programmes = [
        [
            'id' => 1,
            'agence_id' => 1,
            'itineraire' => 'Lubumbashi - Kolwezi',
            'date_depart' => '2024-01-15',
            'heure_depart' => '08:00',
            'heure_arrivee' => '12:30',
            'prix' => 25000,
            'capacite' => 25,
            'places_reservees' => 18,
            'statut' => 'actif'
        ],
        [
            'id' => 2,
            'agence_id' => 1,
            'itineraire' => 'Lubumbashi - Likasi',
            'date_depart' => '2024-01-15',
            'heure_depart' => '07:00',
            'heure_arrivee' => '09:15',
            'prix' => 15000,
            'capacite' => 30,
            'places_reservees' => 30,
            'statut' => 'complet'
        ],
        [
            'id' => 3,
            'agence_id' => 1,
            'itineraire' => 'Lubumbashi - Kipushi',
            'date_depart' => '2024-01-15',
            'heure_depart' => '10:00',
            'heure_arrivee' => '11:30',
            'prix' => 10000,
            'capacite' => 20,
            'places_reservees' => 12,
            'statut' => 'actif'
        ]
    ];

    public static $reservations = [
        [
            'id' => 1,
            'programme_id' => 1,
            'nom_client' => 'Jean Mukamba',
            'telephone' => '+243990123456',
            'email' => 'jean@email.com',
            'nombre_places' => 2,
            'prix_total' => 50000,
            'statut' => 'confirme',
            'date_reservation' => '2024-01-10 14:30:00'
        ],
        [
            'id' => 2,
            'programme_id' => 1,
            'nom_client' => 'Marie Kabila',
            'telephone' => '+243990654321',
            'email' => 'marie@email.com',
            'nombre_places' => 1,
            'prix_total' => 25000,
            'statut' => 'en_attente',
            'date_reservation' => '2024-01-11 09:15:00'
        ],
        [
            'id' => 3,
            'programme_id' => 2,
            'nom_client' => 'Paul Tshisekedi',
            'telephone' => '+243990789123',
            'email' => 'paul@email.com',
            'nombre_places' => 1,
            'prix_total' => 15000,
            'statut' => 'confirme',
            'date_reservation' => '2024-01-12 16:45:00'
        ]
    ];

    public static $agences = [
        [
            'id' => 1,
            'nom' => 'Transport Express Lubumbashi',
            'adresse' => 'Avenue Mobutu, Lubumbashi',
            'telephone' => '+243990000001',
            'email' => 'contact@transportexpress.cd',
            'licence' => 'LIC-2024-001',
            'statut' => 'actif',
            'date_creation' => '2024-01-01 00:00:00'
        ]
    ];

    public static $utilisateurs = [
        [
            'id' => 1,
            'agence_id' => 1,
            'nom_complet' => 'Admin Transport',
            'email' => 'admin@transportexpress.cd',
            'mot_de_passe' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'admin',
            'statut' => 'actif',
            'date_creation' => '2024-01-01 00:00:00'
        ],
        [
            'id' => 2,
            'agence_id' => 1,
            'nom_complet' => 'Manager Operations',
            'email' => 'manager@transportexpress.cd',
            'mot_de_passe' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'manager',
            'statut' => 'actif',
            'date_creation' => '2024-01-01 00:00:00'
        ]
    ];
}
?>