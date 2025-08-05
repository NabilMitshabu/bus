<?php
require_once(__DIR__ . '/classes/Auth.php');

$auth = new Auth();

// Si déjà connecté, rediriger vers l'espace client
if (isset($_SESSION['client_id'])) {
    header('Location: client/dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        // Vérifier les identifiants client
        $db = new Database();
        $query = "SELECT * FROM clients WHERE email = ? AND statut = 'actif'";
        $stmt = $db->query($query, [$email]);
        $client = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        
        if ($client && password_verify($password, $client['mot_de_passe'])) {
            $_SESSION['client_id'] = $client['id'];
            $_SESSION['client_nom_complet'] = $client['nom_complet'];
            $_SESSION['client_email'] = $client['email'];
            $_SESSION['client_telephone'] = $client['telephone'];
            $_SESSION['client_adresse'] = $client['adresse'];
            
            header('Location: client_espace.php');
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client - Transport Lubumbashi</title>
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
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center animate-fade-in">
                <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Espace Client</h2>
                <p class="text-gray-600">Connectez-vous pour gérer vos réservations</p>
            </div>

            <!-- Formulaire de connexion -->
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
                    </div>
                    <?php endif; ?>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-primary"></i>Adresse Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors"
                            placeholder="votre@email.com"
                        >
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-primary"></i>Mot de Passe
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors pr-12"
                                placeholder="••••••••"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Se souvenir de moi -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember" 
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="#" class="text-sm text-primary hover:text-green-700 transition-colors">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- Bouton de connexion -->
                    <button 
                        type="submit" 
                        class="w-full bg-primary hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Se Connecter
                    </button>
                </form>

                <!-- Liens vers autres options -->
                <div class="mt-6 space-y-3">
                    <div class="text-center">
                        <p class="text-gray-600">
                            Pas encore de compte client ? 
                            <a href="client_register.php" class="text-primary hover:text-green-700 font-medium transition-colors">
                                Créer un compte
                            </a>
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600">
                            Vous êtes une agence ? 
                            <a href="login.php" class="text-secondary hover:text-blue-700 font-medium transition-colors">
                                Connexion Agence
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
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
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
