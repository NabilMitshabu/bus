<?php
require_once 'config/database.php';
require_once 'classes/Reservation.php';

session_start();
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Erreur inconnue'];

if (!isset($_SESSION['client_id'], $_SESSION['client_email'])) {
    $response['message'] = 'Non autorisé';
    echo json_encode($response); exit;
}

$reservation_id = isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : 0;
$email = $_SESSION['client_email'];

if (!$reservation_id) {
    $response['message'] = 'ID réservation manquant';
    echo json_encode($response); exit;
}

$reservationObj = new Reservation();
$res = $reservationObj->lireParId($reservation_id);
if (!$res || $res['email'] !== $email) {
    $response['message'] = 'Réservation introuvable ou non autorisée';
    echo json_encode($response); exit;
}
if ($res['statut'] === 'annule') {
    $response['message'] = 'Réservation déjà annulée';
    echo json_encode($response); exit;
}

$ok = $reservationObj->annuler($reservation_id);
if ($ok) {
    $response['success'] = true;
    $response['message'] = 'Réservation annulée avec succès';
} else {
    $response['message'] = 'Erreur lors de l\'annulation';
}
echo json_encode($response);
