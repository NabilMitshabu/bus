<?php
header('Content-Type: application/json');
require_once 'classes/Reservation.php';
require_once 'classes/Programme.php';
require_once 'includes/email_functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'nouvelle_reservation') {
    $reservation = new Reservation();
    $programme = new Programme();
    
    try {
        // Validation des données
        $required_fields = ['programme_id', 'nom_client', 'telephone', 'email', 'nombre_places', 'prix_total'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ $field est requis");
            }
        }
        
        // Vérifier la disponibilité
        $programmes = $programme->lireTous();
        $programme_selectionne = null;
        foreach ($programmes as $prog) {
            if ($prog['id'] == $_POST['programme_id']) {
                $programme_selectionne = $prog;
                break;
            }
        }
        
        if (!$programme_selectionne) {
            throw new Exception("Programme non trouvé");
        }
        
        $places_disponibles = $programme_selectionne['capacite'] - $programme_selectionne['places_reservees'];
        if ($_POST['nombre_places'] > $places_disponibles) {
            throw new Exception("Seulement $places_disponibles place(s) disponible(s)");
        }
        
        // Créer la réservation
        $data = [
            'programme_id' => $_POST['programme_id'],
            'nom_client' => trim($_POST['nom_client']),
            'telephone' => trim($_POST['telephone']),
            'email' => trim($_POST['email']),
            'nombre_places' => intval($_POST['nombre_places']),
            'prix_total' => floatval($_POST['prix_total'])
        ];
        
        if ($reservation->creer($data)) {
            // Générer le code de réservation
            $code_reservation = 'TL' . date('Ymd') . str_pad($data['programme_id'], 3, '0', STR_PAD_LEFT) . rand(100, 999);
            
            // Envoyer l'email de confirmation
            $email_sent = sendConfirmationEmail(
                $data['email'],
                $data['nom_client'],
                $programme_selectionne,
                $data,
                $code_reservation
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Réservation créée avec succès',
                'code_reservation' => $code_reservation,
                'email_sent' => $email_sent
            ]);
        } else {
            throw new Exception("Erreur lors de la création de la réservation");
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requête invalide'
    ]);
}
?>