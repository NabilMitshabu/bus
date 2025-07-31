<?php
require_once 'includes/email_functions.php';

if (isset($_GET['code'])) {
    $code_reservation = $_GET['code'];
    
    // Générer le contenu du ticket
    $ticket_html = generateTicketPDF($code_reservation);
    
    // Pour la démo, on affiche le HTML
    // En production, convertissez en PDF avec une librairie appropriée
    
    header('Content-Type: text/html; charset=utf-8');
    echo $ticket_html;
    
    /* Code pour générer un vrai PDF avec TCPDF:
    require_once 'vendor/tcpdf/tcpdf.php';
    
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML($ticket_html);
    
    $pdf->Output('ticket_' . $code_reservation . '.pdf', 'D');
    */
} else {
    header('HTTP/1.0 404 Not Found');
    echo 'Code de réservation manquant';
}
?>