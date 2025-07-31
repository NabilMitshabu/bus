<?php
$page_title = "Rapports et Analyses - Transport Lubumbashi";

require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../classes/Rapport.php');
require_once(__DIR__ . '/../classes/Agence.php');
require_once(__DIR__ . '/../classes/Auth.php');

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$rapport = new Rapport();
$agence = new Agence();
$agence_id = $_SESSION['agence_id'];

// Traitement des actions
$type_rapport = $_GET['type'] ?? 'financier';
$date_debut = $_GET['date_debut'] ?? date('Y-m-01');
$date_fin = $_GET['date_fin'] ?? date('Y-m-t');

$donnees_rapport = [];
switch ($type_rapport) {
    case 'financier':
        $donnees_rapport = $rapport->genererRapportFinancier($agence_id, $date_debut, $date_fin);
        break;
    case 'performance':
        $donnees_rapport = $rapport->genererRapportPerformance($agence_id);
        break;
    case 'clients':
        $donnees_rapport = $rapport->genererRapportClients($agence_id);
        break;
    case 'occupation':
        $donnees_rapport = $rapport->genererRapportOccupation($agence_id, $date_debut, $date_fin);
        break;
}
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <div class="animate-fade-in">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Rapports et Analyses</h1>
            <p class="text-gray-600">Analysez les performances de votre agence</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="px-4 sm:px-0 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de Rapport</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="financier" <?php echo $type_rapport == 'financier' ? 'selected' : ''; ?>>Rapport Financier</option>
                        <option value="performance" <?php echo $type_rapport == 'performance' ? 'selected' : ''; ?>>Performance</option>
                        <option value="clients" <?php echo $type_rapport == 'clients' ? 'selected' : ''; ?>>Analyse Clients</option>
                        <option value="occupation" <?php echo $type_rapport == 'occupation' ? 'selected' : ''; ?>>Taux d'Occupation</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Début</label>
                    <input type="date" name="date_debut" value="<?php echo $date_debut; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Fin</label>
                    <input type="date" name="date_fin" value="<?php echo $date_fin; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Générer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenu du rapport -->
    <div class="px-4 sm:px-0">
        <?php if ($type_rapport == 'financier' && !empty($donnees_rapport)): ?>
            <!-- Rapport Financier -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Résumé Financier -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-chart-line mr-2 text-primary"></i>Résumé Financier
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Revenus Confirmés:</span>
                                <span class="font-bold text-green-600"><?php echo number_format($donnees_rapport['revenus']['confirmes']); ?> FC</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">En Attente:</span>
                                <span class="font-bold text-yellow-600"><?php echo number_format($donnees_rapport['revenus']['en_attente']); ?> FC</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-900 font-medium">Total:</span>
                                <span class="font-bold text-gray-900"><?php echo number_format($donnees_rapport['revenus']['total']); ?> FC</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-ticket-alt mr-2 text-secondary"></i>Réservations
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Confirmées:</span>
                                <span class="font-bold text-green-600"><?php echo $donnees_rapport['reservations']['confirmees']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">En Attente:</span>
                                <span class="font-bold text-yellow-600"><?php echo $donnees_rapport['reservations']['en_attente']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Annulées:</span>
                                <span class="font-bold text-red-600"><?php echo $donnees_rapport['reservations']['annulees']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Détails par Itinéraire -->
                <div class="lg:col-span-2">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-route mr-2 text-primary"></i>Performance par Itinéraire
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Itinéraire</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programmes</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Réservations</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenus</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Occupation</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($donnees_rapport['details_par_itineraire'] as $itineraire => $details): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $itineraire; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $details['programmes']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $details['reservations']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo number_format($details['revenus']); ?> FC</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-primary h-2 rounded-full" style="width: <?php echo $details['taux_occupation']; ?>%"></div>
                                                </div>
                                                <span><?php echo $details['taux_occupation']; ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($type_rapport == 'clients' && !empty($donnees_rapport)): ?>
            <!-- Rapport Clients -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-users mr-2 text-primary"></i>Statistiques Clients
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-primary"><?php echo $donnees_rapport['statistiques']['total_clients']; ?></div>
                            <div class="text-sm text-gray-600">Total Clients</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-secondary"><?php echo $donnees_rapport['statistiques']['clients_fideles']; ?></div>
                            <div class="text-sm text-gray-600">Clients Fidèles</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-warning"><?php echo $donnees_rapport['statistiques']['nouveaux_clients']; ?></div>
                            <div class="text-sm text-gray-600">Nouveaux (30j)</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600"><?php echo number_format(array_sum(array_column($donnees_rapport['statistiques']['revenus_par_client'], 'revenus')) / count($donnees_rapport['statistiques']['revenus_par_client'])); ?></div>
                            <div class="text-sm text-gray-600">Revenu Moyen</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top Clients</h3>
                    <div class="space-y-3">
                        <?php foreach (array_slice($donnees_rapport['details_clients'], 0, 10) as $index => $client): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900"><?php echo $client['nom']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $client['reservations']; ?> réservation(s)</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-900"><?php echo number_format($client['revenus']); ?> FC</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($type_rapport == 'occupation' && !empty($donnees_rapport)): ?>
            <!-- Rapport d'Occupation -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-6">
                    <i class="fas fa-chart-pie mr-2 text-primary"></i>Analyse d'Occupation
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-primary"><?php echo $donnees_rapport['taux_occupation_global']; ?>%</div>
                        <div class="text-sm text-gray-600">Taux Global</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-3xl font-bold text-secondary"><?php echo $donnees_rapport['places_vendues']; ?></div>
                        <div class="text-sm text-gray-600">Places Vendues</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-3xl font-bold text-warning"><?php echo $donnees_rapport['capacite_totale']; ?></div>
                        <div class="text-sm text-gray-600">Capacité Totale</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-3xl font-bold text-danger"><?php echo $donnees_rapport['programmes_complets']; ?></div>
                        <div class="text-sm text-gray-600">Programmes Complets</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programmes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendues</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($donnees_rapport['details_par_jour'] as $date => $details): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($date)); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $details['programmes']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $details['capacite']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $details['vendues']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-primary h-2 rounded-full" style="width: <?php echo $details['taux']; ?>%"></div>
                                        </div>
                                        <span><?php echo $details['taux']; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <button onclick="window.print()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
            <button onclick="exporterCSV()" class="bg-secondary hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                <i class="fas fa-download mr-2"></i>Exporter CSV
            </button>
        </div>
    </div>
</div>

<script>
function exporterCSV() {
    // Simulation de l'export CSV
    alert('Fonctionnalité d\'export CSV à implémenter');
}
</script>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
