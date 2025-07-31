<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../classes/Reservation.php';
require_once '../classes/Programme.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Méthode non autorisée';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['action']) || $_POST['action'] !== 'nouvelle_reservation') {
    $response['message'] = 'Action non valide';
    echo json_encode($response);
    exit;
}

try {
    $db = new Database();
    $reservation = new Reservation($db);
    $programme = new Programme($db);
    
    // Récupérer les données du formulaire
    $programme_id = intval($_POST['programme_id'] ?? 0);
    $nom_client = trim($_POST['nom_client'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nombre_places = intval($_POST['nombre_places'] ?? 0);
    $prix_total = floatval($_POST['prix_total'] ?? 0);
    
    // Validation des données
    if (empty($programme_id) || empty($nom_client) || empty($telephone) || empty($email) || empty($nombre_places)) {
        $response['message'] = 'Tous les champs sont obligatoires';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Adresse email non valide';
        echo json_encode($response);
        exit;
    }
    
    if ($nombre_places <= 0 || $nombre_places > 10) {
        $response['message'] = 'Nombre de places non valide (1-10)';
        echo json_encode($response);
        exit;
    }
    
    // Vérifier si le programme existe et a des places disponibles
    $prog_data = $programme->lireParId($programme_id);
    if (!$prog_data) {
        $response['message'] = 'Programme non trouvé';
        echo json_encode($response);
        exit;
    }
    
    $places_disponibles = $prog_data['capacite'] - $prog_data['places_reservees'];
    if ($nombre_places > $places_disponibles) {
        $response['message'] = 'Pas assez de places disponibles';
        echo json_encode($response);
        exit;
    }
    
    // Vérifier le prix
    $prix_attendu = $prog_data['prix'] * $nombre_places;
    if (abs($prix_total - $prix_attendu) > 0.01) {
        $response['message'] = 'Prix incorrect';
        echo json_encode($response);
        exit;
    }
    
    // Créer la réservation
    $reservation_data = [
        'programme_id' => $programme_id,
        'nom_client' => $nom_client,
        'telephone' => $telephone,
        'email' => $email,
        'nombre_places' => $nombre_places,
        'prix_total' => $prix_total
    ];
    
    $result = $reservation->creer($reservation_data);
    
    if ($result) {
        // Récupérer les informations de l'agence pour notification
        $query = "SELECT a.nom as agence_nom, a.email as agence_email, a.telephone as agence_telephone,
                         p.itineraire, p.date_depart, p.heure_depart
                  FROM programmes p 
                  JOIN agences a ON p.agence_id = a.id 
                  WHERE p.id = ?";
        $stmt = $db->query($query, [$programme_id]);
        $info = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        
        $response['success'] = true;
        $response['message'] = 'Réservation créée avec succès! L\'agence ' . ($info ? $info['agence_nom'] : '') . ' va traiter votre demande.';
        $response['data'] = [
            'reservation_id' => $db->lastInsertId(),
            'agence_info' => $info
        ];
        
        // Ici on pourrait ajouter l'envoi d'email de confirmation
        // et de notification à l'agence
        
    } else {
        $response['message'] = 'Erreur lors de la création de la réservation';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Erreur serveur: ' . $e->getMessage();
}

echo json_encode($response);
?>
