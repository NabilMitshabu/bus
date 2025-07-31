<?php
require_once(__DIR__ . '/classes/Auth.php');

$auth = new Auth();

// Si déjà connecté, rediriger vers l'admin
if ($auth->isLoggedIn()) {
    header('Location: admin/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Lubumbashi - Plateforme de Gestion</title>
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
            animation: fadeIn 0.8s ease-in;
        }
        .animate-slide-up {
            animation: slideUp 0.6s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="h-10 w-10 bg-primary rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-bus text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Transport Lubumbashi</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                    </a>
                    <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>Inscription
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fade-in">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Plateforme de Gestion
                    <span class="block text-blue-200">Transport Lubumbashi</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto">
                    Solution complète pour la gestion des agences de transport, 
                    programmes de voyage et réservations dans la région du Katanga
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="login.php" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-sign-in-alt mr-2"></i>Accéder à mon espace
                    </a>
                    <a href="register.php" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition-all duration-300">
                        <i class="fas fa-user-plus mr-2"></i>Créer un compte agence
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-slide-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Fonctionnalités Principales
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Tout ce dont votre agence de transport a besoin pour gérer efficacement ses activités
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Gestion des Programmes -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-route text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Gestion des Programmes</h3>
                    <p class="text-gray-600 mb-4">
                        Créez, modifiez et gérez vos programmes de voyage avec les itinéraires, 
                        horaires et tarifs.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Création de programmes</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Gestion des itinéraires</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Suivi des places disponibles</li>
                    </ul>
                </div>

                <!-- Gestion des Réservations -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-ticket-alt text-2xl text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Réservations</h3>
                    <p class="text-gray-600 mb-4">
                        Gérez toutes les réservations de vos clients avec validation, 
                        annulation et suivi des paiements.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Validation des réservations</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Gestion des annulations</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Suivi des paiements</li>
                    </ul>
                </div>

                <!-- Tableau de Bord -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Tableau de Bord</h3>
                    <p class="text-gray-600 mb-4">
                        Visualisez les statistiques de votre agence avec des graphiques 
                        et des indicateurs de performance.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Statistiques en temps réel</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Revenus et bénéfices</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Taux d'occupation</li>
                    </ul>
                </div>

                <!-- Gestion des Utilisateurs -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-users text-2xl text-orange-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Gestion des Utilisateurs</h3>
                    <p class="text-gray-600 mb-4">
                        Gérez les comptes utilisateurs de votre agence avec des rôles 
                        et permissions personnalisés.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Rôles et permissions</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Gestion des accès</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Historique des activités</li>
                    </ul>
                </div>

                <!-- Rapports -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-file-alt text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Rapports & Analyses</h3>
                    <p class="text-gray-600 mb-4">
                        Générez des rapports détaillés sur les performances financières 
                        et opérationnelles de votre agence.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Rapports financiers</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Analyses de performance</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Export CSV/PDF</li>
                    </ul>
                </div>

                <!-- Configuration -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-cog text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Configuration</h3>
                    <p class="text-gray-600 mb-4">
                        Personnalisez les paramètres de votre agence et configurez 
                        les options selon vos besoins.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Informations agence</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Paramètres système</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Préférences utilisateur</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-slide-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Prêt à commencer ?
                </h2>
                <p class="text-gray-600 text-lg mb-8 max-w-2xl mx-auto">
                    Rejoignez dès maintenant la plateforme Transport Lubumbashi et 
                    modernisez la gestion de votre agence de transport.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="register.php" class="bg-primary text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-rocket mr-2"></i>Créer mon compte agence
                    </a>
                    <a href="public/index.php" class="border-2 border-primary text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-primary hover:text-white transition-all duration-300">
                        <i class="fas fa-eye mr-2"></i>Voir la version publique
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 bg-primary rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-bus text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold">Transport Lubumbashi</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Plateforme de gestion complète pour les agences de transport 
                        dans la région du Katanga, République Démocratique du Congo.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liens Rapides</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="login.php" class="hover:text-white transition-colors">Connexion</a></li>
                        <li><a href="register.php" class="hover:text-white transition-colors">Inscription</a></li>
                        <li><a href="public/index.php" class="hover:text-white transition-colors">Version Publique</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-envelope mr-2"></i>contact@transportlub.cd</li>
                        <li><i class="fas fa-phone mr-2"></i>+243 990 000 001</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i>Lubumbashi, Katanga</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Transport Lubumbashi. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-slide-up').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });

        // Animation des cartes au hover
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
