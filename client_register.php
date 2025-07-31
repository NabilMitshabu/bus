<?php
require_once(__DIR__ . '/config/database.php');

// Si déjà connecté en tant que client, rediriger
if (isset($_SESSION['client_id'])) {
    header('Location: client/dashboard.php');
    exit;
}

require_once(__DIR__ . '/config/database.php');


$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et nettoyer les données
    $nom_complet = trim($_POST['nom_complet'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($nom_complet) || empty($telephone) || empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez saisir une adresse email valide.';
    } else {
        try {
            // Vérifier si l'email existe déjà
            $db = new Database();
            $check_email = $db->query("SELECT id FROM clients WHERE email = ?", [$email]);
            if ($check_email && $check_email->fetch()) {
                $error = 'Cette adresse email est déjà utilisée.';
            } else {
                // Créer le compte client
                $sql = "INSERT INTO clients (nom_complet, telephone, email, adresse, mot_de_passe, statut) 
                        VALUES (?, ?, ?, ?, ?, 'actif')";
                
                $params = [
                    $nom_complet,
                    $telephone,
                    $email,
                    $adresse,
                    password_hash($password, PASSWORD_DEFAULT)
                ];
                
                $stmt = $db->query($sql, $params);
                
                if ($stmt) {
                    $success = 'Votre compte client a été créé avec succès ! Vous pouvez maintenant vous connecter.';
                } else {
                    $error = 'Erreur lors de la création du compte. Veuillez réessayer.';
                }
            }
        } catch (Exception $e) {
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
    <title>Inscription Client - Transport Lubumbashi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10B981',
                        secondary: '#3B82F6',
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
<body class="bg-gradient-to-br from-green-50 to-blue-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center animate-fade-in">
                <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Créer un Compte Client</h2>
                <p class="text-gray-600">Rejoignez Transport Lubumbashi pour réserver vos voyages</p>
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
                            <a href="client_login.php" class="text-green-800 font-medium hover:underline">
                                → Se connecter maintenant
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>



                    <!-- Informations personnelles -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-user mr-2 text-secondary"></i>Informations Personnelles
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nom complet -->
                            <div class="md:col-span-2">
                                <label for="nom_complet" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom Complet 
                                </label>
                                <input 
                                    type="text" 
                                    id="nom_complet" 
                                    name="nom_complet" 
                                    value="<?php echo htmlspecialchars($_POST['nom_complet'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Jean Mukamba Tshisekedi"
                                >
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

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email *
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="jean@email.com"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-lock mr-2 text-warning"></i>Sécurité du Compte
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            J'accepte les <a href="#" class="text-primary hover:text-green-700 font-medium">conditions d'utilisation</a> 
                            et la <a href="#" class="text-primary hover:text-green-700 font-medium">politique de confidentialité</a> *
                        </label>
                    </div>

                    <!-- Bouton d'inscription -->
                    <button 
                        type="submit" 
                        class="w-full bg-primary hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                    >
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer mon Compte Client
                    </button>
                </form>

                <!-- Liens vers autres options -->
                <div class="mt-6 space-y-3">
                    <div class="text-center">
                        <p class="text-gray-600">
                            Vous avez déjà un compte ? 
                            <a href="client_login.php" class="text-primary hover:text-green-700 font-medium transition-colors">
                                Se connecter
                            </a>
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600">
                            Vous êtes une agence ? 
                            <a href="register.php" class="text-secondary hover:text-blue-700 font-medium transition-colors">
                                Inscription Agence
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="text-center text-sm text-gray-500 animate-fade-in">
                <p>© 2024 Transport Lubumbashi. Tous droits réservés.</p>
                <p class="mt-1">
                    <a href="index.php" class="hover:text-gray-700 transition-colors">← Retour à l'accueil</a>
                </p>
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

        function updateAgenceInfo() {
            const select = document.getElementById('agence_id');
            const infoDiv = document.getElementById('agenceInfo');
            const adresseDiv = document.getElementById('agenceAdresse');
            const telephoneDiv = document.getElementById('agenceTelephone');
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                const adresse = option.getAttribute('data-adresse');
                const telephone = option.getAttribute('data-telephone');
                
                adresseDiv.innerHTML = '<i class="fas fa-map-marker-alt mr-2"></i>' + adresse;
                telephoneDiv.innerHTML = '<i class="fas fa-phone mr-2"></i>' + telephone;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
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
