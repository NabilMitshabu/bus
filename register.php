<?php
require_once(__DIR__ . '/classes/Auth.php');
require_once(__DIR__ . '/classes/Agence.php');
require_once(__DIR__ . '/classes/Utilisateur.php');

$auth = new Auth();

// Si déjà connecté, rediriger vers l'admin
if ($auth->isLoggedIn()) {
    header('Location: admin/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et nettoyer les données
    $nom_agence = trim($_POST['nom_agence'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email_agence = trim($_POST['email_agence'] ?? '');
    $licence = trim($_POST['licence'] ?? '');
    
    $nom_admin = trim($_POST['nom_admin'] ?? '');
    $email_admin = trim($_POST['email_admin'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($nom_agence) || empty($adresse) || empty($telephone) || empty($email_agence) || 
        empty($licence) || empty($nom_admin) || empty($email_admin) || empty($password)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif (!filter_var($email_admin, FILTER_VALIDATE_EMAIL) || !filter_var($email_agence, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez saisir des adresses email valides.';
    } else {
        try {
            $db = new Database();
            $db->conn->beginTransaction();
            
            // Vérifier si l'email admin existe déjà
            $check_email = $db->query("SELECT id FROM utilisateurs WHERE email = ?", [$email_admin]);
            if ($check_email && $check_email->fetch()) {
                $error = 'Cette adresse email est déjà utilisée.';
            } else {
                // Créer l'agence
                $agence = new Agence($db);
                $agence_data = [
                    'nom' => $nom_agence,
                    'adresse' => $adresse,
                    'telephone' => $telephone,
                    'email' => $email_agence,
                    'licence' => $licence
                ];
                
                $sql_agence = "INSERT INTO agences (nom, adresse, telephone, email, licence, statut) VALUES (?, ?, ?, ?, ?, 'actif')";
                $stmt = $db->query($sql_agence, [
                    $agence_data['nom'],
                    $agence_data['adresse'],
                    $agence_data['telephone'],
                    $agence_data['email'],
                    $agence_data['licence']
                ]);
                
                if (!$stmt) {
                    throw new Exception('Erreur lors de la création de l\'agence');
                }
                
                $agence_id = $db->lastInsertId();
                
                // Créer l'utilisateur administrateur
                $utilisateur = new Utilisateur($db);
                $user_data = [
                    'agence_id' => $agence_id,
                    'nom_complet' => $nom_admin,
                    'email' => $email_admin,
                    'mot_de_passe' => $password,
                    'role' => 'admin'
                ];
                
                $user_id = $utilisateur->creer($user_data);
                
                if (!$user_id) {
                    throw new Exception('Erreur lors de la création de l\'utilisateur');
                }
                
                $db->conn->commit();
                $success = 'Votre compte agence a été créé avec succès ! Vous pouvez maintenant vous connecter.';
                
                // Optionnel : connexion automatique
                // $auth->loginAgence($email_admin, $password);
                // header('Location: admin/index.php');
                // exit;
            }
            
        } catch (Exception $e) {
            $db->conn->rollBack();
            $error = 'Erreur lors de la création du compte : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Agence - Transport Lubumbashi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444'
                    }
                }
            }
        }
    </script>
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center animate-fade-in">
                <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bus text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Créer un Compte Agence</h2>
                <p class="text-gray-600">Rejoignez la plateforme Transport Lubumbashi</p>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="card-hover bg-white rounded-lg shadow-lg p-8 animate-fade-in">
                <form method="POST" action="" class="space-y-6">
                    <!-- Messages d'erreur/succès -->
                    <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span><?php echo htmlspecialchars($success); ?></span>
                        </div>
                        <div class="mt-2">
                            <a href="login.php" class="text-green-800 font-medium hover:underline">
                                → Se connecter maintenant
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Section Informations de l'Agence -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-building mr-2 text-primary"></i>Informations de l'Agence
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nom de l'agence -->
                            <div class="md:col-span-2">
                                <label for="nom_agence" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom de l'Agence *
                                </label>
                                <input 
                                    type="text" 
                                    id="nom_agence" 
                                    name="nom_agence" 
                                    value="<?php echo htmlspecialchars($_POST['nom_agence'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Transport Express Lubumbashi"
                                >
                            </div>

                            <!-- Adresse -->
                            <div class="md:col-span-2">
                                <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adresse Complète *
                                </label>
                                <textarea 
                                    id="adresse" 
                                    name="adresse" 
                                    rows="3"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Avenue Mobutu, Commune de Lubumbashi, Haut-Katanga"
                                ><?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?></textarea>
                            </div>

                            <!-- Téléphone -->
                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone *
                                </label>
                                <input 
                                    type="tel" 
                                    id="telephone" 
                                    name="telephone" 
                                    value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="+243990000001"
                                >
                            </div>

                            <!-- Email agence -->
                            <div>
                                <label for="email_agence" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email de l'Agence *
                                </label>
                                <input 
                                    type="email" 
                                    id="email_agence" 
                                    name="email_agence" 
                                    value="<?php echo htmlspecialchars($_POST['email_agence'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="contact@votre-agence.cd"
                                >
                            </div>

                            <!-- Numéro de licence -->
                            <div class="md:col-span-2">
                                <label for="licence" class="block text-sm font-medium text-gray-700 mb-2">
                                    Numéro de Licence de Transport *
                                </label>
                                <input 
                                    type="text" 
                                    id="licence" 
                                    name="licence" 
                                    value="<?php echo htmlspecialchars($_POST['licence'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="LIC-2024-001"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Section Administrateur -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-user-shield mr-2 text-secondary"></i>Compte Administrateur
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nom administrateur -->
                            <div>
                                <label for="nom_admin" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom Complet *
                                </label>
                                <input 
                                    type="text" 
                                    id="nom_admin" 
                                    name="nom_admin" 
                                    value="<?php echo htmlspecialchars($_POST['nom_admin'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Jean Mukamba"
                                >
                            </div>

                            <!-- Email administrateur -->
                            <div>
                                <label for="email_admin" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email de Connexion *
                                </label>
                                <input 
                                    type="email" 
                                    id="email_admin" 
                                    name="email_admin" 
                                    value="<?php echo htmlspecialchars($_POST['email_admin'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="admin@votre-agence.cd"
                                >
                            </div>

                            <!-- Mot de passe -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mot de Passe *
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        required 
                                        minlength="6"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent pr-12"
                                        placeholder="••••••••"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="togglePassword('password')" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    >
                                        <i class="fas fa-eye" id="toggleIcon1"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Minimum 6 caractères</p>
                            </div>

                            <!-- Confirmation mot de passe -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmer le Mot de Passe *
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        id="confirm_password" 
                                        name="confirm_password" 
                                        required 
                                        minlength="6"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent pr-12"
                                        placeholder="••••••••"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="togglePassword('confirm_password')" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    >
                                        <i class="fas fa-eye" id="toggleIcon2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conditions d'utilisation -->
                    <div class="flex items-start">
                        <input 
                            type="checkbox" 
                            id="terms" 
                            name="terms" 
                            required
                            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-1"
                        >
                        <label for="terms" class="ml-2 block text-sm text-gray-700">
                            J'accepte les <a href="#" class="text-primary hover:text-blue-700 font-medium">conditions d'utilisation</a> 
                            et la <a href="#" class="text-primary hover:text-blue-700 font-medium">politique de confidentialité</a> *
                        </label>
                    </div>

                    <!-- Bouton d'inscription -->
                    <button 
                        type="submit" 
                        class="w-full bg-secondary hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                    >
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer mon Compte Agence
                    </button>
                </form>

                <!-- Lien vers la connexion -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Vous avez déjà un compte ? 
                        <a href="login.php" class="text-primary hover:text-blue-700 font-medium transition-colors">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="text-center text-sm text-gray-500 animate-fade-in">
                <p>© 2024 Transport Lubumbashi. Tous droits réservés.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const iconId = fieldId === 'password' ? 'toggleIcon1' : 'toggleIcon2';
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Validation en temps réel des mots de passe
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });

        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.animate-fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>
