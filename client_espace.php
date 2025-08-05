<?php
require_once 'config/database.php';

// Vérifier si le client est connecté
if (!isset($_SESSION['client_id'])) {
    header('Location: client_login.php');
    exit;
}

$nom_complet = $_SESSION['client_nom_complet'] ?? '';
$email = $_SESSION['client_email'] ?? '';
$telephone = $_SESSION['client_telephone'] ?? '';
$adresse = $_SESSION['client_adresse'] ?? '';

$page_title = 'Espace Client';
require_once 'includes/public_header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
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
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .content-wrapper {
            flex: 1;
            padding-top: 4rem; /* Compensation pour la navbar fixe */
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
                </div>
                <div class="flex items-center">
                    <div class="relative">
                        <button onclick="toggleDropdown('profileDropdown')" class="flex items-center text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white mr-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="font-semibold"><?php echo htmlspecialchars($nom_complet); ?></span>
                        </button>
                        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="client_espace.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-circle mr-2"></i> Mon profil
                            </a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="content-wrapper">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- En-tête profil -->
                <div class="bg-gradient-to-r from-primary to-primary-dark p-6 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-2xl">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($nom_complet); ?></h1>
                            <p class="text-white text-opacity-90"><?php echo htmlspecialchars($email); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Informations client -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-phone-alt text-primary mt-1"></i>
                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <p class="font-medium"><?php echo htmlspecialchars($telephone); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                        <div>
                            <p class="text-sm text-gray-500">Adresse</p>
                            <p class="font-medium"><?php echo htmlspecialchars($adresse); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Réservations -->
                <div class="border-t border-gray-200 px-6 py-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Mes réservations</h2>
                    
                    <?php
                    require_once 'classes/Reservation.php';
                    $reservationObj = new Reservation();
                    $reservations = $reservationObj->lireTousParClient($email);
                    ?>

                    <?php if (empty($reservations)): ?>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <i class="fas fa-ticket-alt text-4xl text-blue-300 mb-3"></i>
                            <p class="text-gray-600 font-medium">Aucune réservation trouvée</p>
                            <p class="text-gray-500 text-sm">Vous n'avez pas encore effectué de réservation</p>
                            <a href="index.php" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">
                                Réserver maintenant
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itinéraire</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Heure</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Places</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($reservations as $res): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($res['itineraire']); ?></div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div><?php echo date('d/m/Y', strtotime($res['date_depart'])); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($res['heure_depart']); ?></div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <?php echo $res['nombre_places']; ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <?php echo number_format($res['prix_total'], 0, ',', ' '); ?> FC
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <?php if ($res['statut'] == 'confirme'): ?>
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Confirmée
                                                </span>
                                            <?php elseif ($res['statut'] == 'en_attente'): ?>
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                            <?php elseif ($res['statut'] == 'annule'): ?>
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Annulée
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleDropdown(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[onclick*="toggleDropdown"]')) {
            document.getElementById('profileDropdown').classList.add('hidden');
        }
    });
    </script>
</body>
</html>