<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Configuration email (à adapter selon votre fournisseur)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@gmail.com');
define('SMTP_PASSWORD', 'votre-mot-de-passe');
define('FROM_EMAIL', 'noreply@transportlub.cd');
define('FROM_NAME', 'TransportLub');

function sendConfirmationEmail($to_email, $client_name, $programme, $reservation, $code_reservation) {
    // Pour cette démo, on simule l'envoi d'email
    // En production, utilisez PHPMailer ou un service comme SendGrid
    
    $subject = "Confirmation de réservation - TransportLub";
    $message = generateEmailTemplate($client_name, $programme, $reservation, $code_reservation);
    
    // Simulation d'envoi d'email (remplacez par un vrai envoi)
    error_log("Email envoyé à: $to_email");
    error_log("Sujet: $subject");
    error_log("Code de réservation: $code_reservation");
    
    return true; // Retourne true pour la démo
    
    /* Code pour un vrai envoi avec PHPMailer:
    try {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to_email, $client_name);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur envoi email: " . $mail->ErrorInfo);
        return false;
    }
    */
}

function generateEmailTemplate($client_name, $programme, $reservation, $code_reservation) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1E40AF, #3B82F6); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .ticket { background: white; border: 2px dashed #1E40AF; padding: 20px; margin: 20px 0; border-radius: 10px; }
            .code { font-size: 24px; font-weight: bold; color: #1E40AF; text-align: center; margin: 15px 0; }
            .info-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 10px 0; border-bottom: 1px solid #eee; }
            .button { background: #1E40AF; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚌 TransportLub</h1>
                <h2>Confirmation de Réservation</h2>
            </div>
            
            <div class="content">
                <p>Bonjour <strong>' . htmlspecialchars($client_name) . '</strong>,</p>
                
                <p>Votre réservation a été confirmée avec succès ! Voici les détails de votre voyage :</p>
                
                <div class="ticket">
                    <div class="code">Code de réservation: ' . $code_reservation . '</div>
                    
                    <div class="info-row">
                        <span><strong>Itinéraire:</strong></span>
                        <span>' . htmlspecialchars($programme['itineraire']) . '</span>
                    </div>
                    
                    <div class="info-row">
                        <span><strong>Date de départ:</strong></span>
                        <span>' . date('d/m/Y', strtotime($programme['date_depart'])) . '</span>
                    </div>
                    
                    <div class="info-row">
                        <span><strong>Heure de départ:</strong></span>
                        <span>' . $programme['heure_depart'] . '</span>
                    </div>
                    
                    <div class="info-row">
                        <span><strong>Nombre de places:</strong></span>
                        <span>' . $reservation['nombre_places'] . '</span>
                    </div>
                    
                    <div class="info-row">
                        <span><strong>Prix total:</strong></span>
                        <span>' . number_format($reservation['prix_total']) . ' FC</span>
                    </div>
                </div>
                
                <h3>Instructions importantes :</h3>
                <ul>
                    <li>Présentez-vous 30 minutes avant le départ</li>
                    <li>Munissez-vous de votre pièce d\'identité</li>
                    <li>Conservez ce code de réservation : <strong>' . $code_reservation . '</strong></li>
                    <li>Le paiement se fait à bord ou à notre bureau</li>
                </ul>
                
                <p>Pour télécharger votre ticket PDF, cliquez sur le bouton ci-dessous :</p>
                <a href="' . $_SERVER['HTTP_HOST'] . '/download_ticket.php?code=' . $code_reservation . '" class="button">
                    📄 Télécharger le ticket PDF
                </a>
                
                <p>Pour toute question, contactez-nous :</p>
                <p>📞 +243 990 123 456<br>
                   📧 info@transportlub.cd</p>
                
                <p>Merci de votre confiance !</p>
                <p><strong>L\'équipe TransportLub</strong></p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

function generateTicketPDF($code_reservation) {
    // Cette fonction génère un PDF du ticket
    // Pour la démo, on retourne un contenu HTML simple
    // En production, utilisez une librairie comme TCPDF ou FPDF
    
    require_once 'classes/Reservation.php';
    require_once 'classes/Programme.php';
    
    $reservation = new Reservation();
    $programme = new Programme();
    
    // Trouver la réservation par code (simulation)
    $reservations = $reservation->lireTous();
    $programmes = $programme->lireTous();
    
    // Pour la démo, on génère un HTML qui peut être converti en PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
            .ticket { border: 3px solid #1E40AF; padding: 30px; max-width: 400px; margin: 0 auto; }
            .header { text-align: center; border-bottom: 2px dashed #1E40AF; padding-bottom: 20px; margin-bottom: 20px; }
            .code { font-size: 20px; font-weight: bold; color: #1E40AF; }
            .info { margin: 10px 0; }
            .qr-placeholder { width: 100px; height: 100px; border: 1px solid #ccc; margin: 20px auto; display: flex; align-items: center; justify-content: center; }
        </style>
    </head>
    <body>
        <div class="ticket">
            <div class="header">
                <h1>🚌 TransportLub</h1>
                <div class="code">TICKET DE TRANSPORT</div>
                <div class="code">' . $code_reservation . '</div>
            </div>
            
            <div class="info"><strong>Passager:</strong> [Nom du client]</div>
            <div class="info"><strong>Itinéraire:</strong> [Itinéraire]</div>
            <div class="info"><strong>Date:</strong> [Date]</div>
            <div class="info"><strong>Heure:</strong> [Heure]</div>
            <div class="info"><strong>Places:</strong> [Nombre]</div>
            <div class="info"><strong>Prix:</strong> [Prix] FC</div>
            
            <div class="qr-placeholder">QR CODE</div>
            
            <p style="text-align: center; font-size: 12px; margin-top: 20px;">
                Présentez ce ticket au chauffeur<br>
                Bon voyage !
            </p>
        </div>
    </body>
    </html>';
    
    return $html;
}
?>