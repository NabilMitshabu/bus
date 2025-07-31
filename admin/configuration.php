<?php
$page_title = "Configuration de l'Agence - Transport Lubumbashi";

require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../classes/Agence.php');
require_once(__DIR__ . '/../classes/Auth.php');

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$agence = new Agence();
$agence_id = $_SESSION['agence_id'];

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'modifier_agence':
                $data = [
                    'nom' => $_POST['nom'],
                    'adresse' => $_POST['adresse'],
                    'telephone' => $_POST['telephone'],
                    'email' => $_POST['email'],
                    'licence' => $_POST['licence']
                ];
                if ($agence->mettreAJour($agence_id, $data)) {
                    $success = "Informations de l'agence mises à jour avec succès!";
                } else {
                    $error = "Erreur lors de la mise à jour.";
                }
                break;
        }
    }
}

$infos_agence = $agence->obtenirInfos($agence_id);
$stats_agence = $agence->obtenirStatistiques($agence_id);
$itineraires_populaires = $agence->obtenirItinerairesPopulaires($agence_id);
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <div class="animate-fade-in">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Configuration de l'Agence</h1>
            <p class="text-gray-600">Gérez les paramètres et informations de votre agence</p>
        </div>
    </div>

    <!-- Messages -->
    <?php if (isset($success)): ?>
    <div class="px-4 sm:px-0 mb-6">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded animate-fade-in">
            <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
    <div class="px-4 sm:px-0 mb-6">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded animate-fade-in">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="px-4 sm:px-0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations de l'agence -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-building mr-2 text-primary"></i>Informations de l'Agence
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="" class="space-y-6">
                            <input type="hidden" name="action" value="modifier_agence">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'Agence</label>
                                <input type="text" name="nom" value="<?php echo htmlspecialchars($infos_agence['nom']); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                                <textarea name="adresse" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($infos_agence['adresse']); ?></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                    <input type="tel" name="telephone" value="<?php echo htmlspecialchars($infos_agence['telephone']); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($infos_agence['email']); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de Licence</label>
                                <input type="text" name="licence" value="<?php echo htmlspecialchars($infos_agence['licence']); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-save mr-2"></i>Enregistrer les Modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Paramètres Système -->
                <div class="bg-white shadow rounded-lg mt-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-cogs mr-2 text-primary"></i>Paramètres Système
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Notifications Email</h4>
                                    <p class="text-sm text-gray-500">Recevoir des notifications par email pour les nouvelles réservations</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Confirmation Automatique</h4>
                                    <p class="text-sm text-gray-500">Confirmer automatiquement les réservations après paiement</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapports Automatiques</h4>
                                    <p class="text-sm text-gray-500">Générer et envoyer des rapports mensuels automatiquement</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques et Informations -->
            <div class="lg:col-span-1">
                <!-- Statistiques Générales -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-chart-bar mr-2 text-primary"></i>Statistiques Générales
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Programmes:</span>
                                <span class="font-bold text-gray-900"><?php echo $stats_agence['total_programmes']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Réservations:</span>
                                <span class="font-bold text-gray-900"><?php echo $stats_agence['total_reservations']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Revenus Total:</span>
                                <span class="font-bold text-green-600"><?php echo number_format($stats_agence['revenus_total']); ?> FC</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Clients Uniques:</span>
                                <span class="font-bold text-gray-900"><?php echo $stats_agence['clients_uniques']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Taux de Conversion:</span>
                                <span class="font-bold text-blue-600"><?php echo $stats_agence['taux_conversion']; ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Itinéraires Populaires -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-star mr-2 text-warning"></i>Itinéraires Populaires
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <?php foreach ($itineraires_populaires as $index => $itineraire): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo $itineraire['itineraire']; ?></div>
                                        <div class="text-xs text-gray-500"><?php echo $itineraire['reservations']; ?> réservations</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-gray-900"><?php echo number_format($itineraire['revenus']); ?> FC</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Informations Système -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-info-circle mr-2 text-primary"></i>Informations Système
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Version:</span>
                                <span class="font-medium text-gray-900">1.0.0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dernière Sauvegarde:</span>
                                <span class="font-medium text-gray-900"><?php echo date('d/m/Y H:i'); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Statut:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-green-400 mr-1"></i>Opérationnel
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
