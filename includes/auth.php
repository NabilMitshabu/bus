<?php
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'login') {
        $userType = $_POST['userType'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        if ($userType == 'agence') {
            $loggedIn = $auth->loginAgence($email, $password);
        } else {
            // Gestion clients si nécessaire
        }
        
        if ($loggedIn) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        }
    }
}