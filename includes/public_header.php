<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'TransportLub - Transport à Lubumbashi'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1E40AF',
                        'secondary': '#059669',
                        'warning': '#EA580C',
                        'danger': '#DC2626'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .animate-fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-bus text-2xl text-primary mr-3"></i>
                        <h1 class="text-xl font-bold text-gray-900">TransportLub</h1>
                    </div>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
    <a href="../public/index.php" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">
        <i class="fas fa-home mr-1"></i>Accueil
    </a>
    <a href="#programmes" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">
        <i class="fas fa-route mr-1"></i>Programmes
    </a>
    <a href="#contact" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">
        <i class="fas fa-phone mr-1"></i>Contact
    </a>
</div>
                </div>

                <!-- Menu de connexion/inscription -->
<div class="flex items-center space-x-4">
    <!-- Dropdown Inscription -->
    <div class="relative">
        <button onclick="toggleDropdown('registerDropdown')" class="flex items-center text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors focus:outline-none">
            <i class="fas fa-user-plus mr-2"></i>S'inscrire
            <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </button>
        <div id="registerDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <div class="py-2">
                <a href="../register.php" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors">
                    <i class="fas fa-building mr-3 text-blue-600"></i>
                    <div>
                        <div class="font-medium">Agence de Transport</div>
                        <div class="text-xs text-gray-500">Créer un compte agence</div>
                    </div>
                </a>
                <a href="../client_register.php" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors">
                    <i class="fas fa-user mr-3 text-green-600"></i>
                    <div>
                        <div class="font-medium">Client</div>
                        <div class="text-xs text-gray-500">Créer un compte client</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Dropdown Connexion -->
    <div class="relative">
        <button onclick="toggleDropdown('loginDropdown')" class="flex items-center text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors focus:outline-none">
            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
            <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </button>
        <div id="loginDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <div class="py-2">
                <a href="../login.php" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors">
                    <i class="fas fa-user-shield mr-3 text-blue-600"></i>
                    <div>
                        <div class="font-medium">Espace Agence</div>
                        <div class="text-xs text-gray-500">Accès administration</div>
                    </div>
                </a>
                <a href="../client_login.php" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors">
                    <i class="fas fa-user mr-3 text-green-600"></i>
                    <div>
                        <div class="font-medium">Espace Client</div>
                        <div class="text-xs text-gray-500">Mes réservations</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Menu mobile -->
    <button onclick="toggleMobileMenu()" class="md:hidden text-gray-700 hover:text-primary focus:outline-none">
        <i class="fas fa-bars text-xl"></i>
    </button>
</div>
            </div>

            <!-- Menu mobile -->
            <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="../public/index.php" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md transition-colors">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    <a href="#programmes" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md transition-colors">
                        <i class="fas fa-route mr-2"></i>Programmes
                    </a>
                    <a href="#contact" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md transition-colors">
                        <i class="fas fa-phone mr-2"></i>Contact
                    </a>
                    <div class="border-t border-gray-200 my-2"></div>
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">S'inscrire</div>
                    <a href="../register.php" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md transition-colors">
                        <i class="fas fa-building mr-2"></i>Agence de Transport
                    </a>
                    <a href="../client_register.php" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md transition-colors">
                        <i class="fas fa-user mr-2"></i>Client
                    </a>
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Se connecter</div>
                    <a href="../login.php" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md transition-colors">
                        <i class="fas fa-user-shield mr-2"></i>Espace Agence
                    </a>
                    <a href="../client_login.php" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md transition-colors">
                        <i class="fas fa-user mr-2"></i>Espace Client
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="pt-16">