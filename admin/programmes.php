<?php
$page_title = "Gestion des Programmes - Transport Lubumbashi";

require_once(__DIR__ . '/../classes/Auth.php');
require_once(__DIR__ . '/../classes/Programme.php');

$auth = new Auth();



require_once(__DIR__ . '/../includes/header.php');

$agence_id = $auth->getAgenceId();
$programme = new Programme();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter':
                $data = [
                    'agence_id' => $agence_id,
                    'itineraire' => $_POST['itineraire'],
                    'date_depart' => $_POST['date_depart'],
                    'heure_depart' => $_POST['heure_depart'],
                    'heure_arrivee' => $_POST['heure_arrivee'],
                    'prix' => $_POST['prix'],
                    'capacite' => $_POST['capacite']
                ];
                if ($programme->creer($data)) {
                    $success = "Programme ajouté avec succès!";
                } else {
                    $error = "Erreur lors de l'ajout du programme.";
                }
                break;
        }
    }
}

$programmes = $programme->lireTous($agence_id);
$stats = $programme->obtenirStatistiques($agence_id);
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Programmes</h1>
                <p class="text-gray-600">Gérez vos programmes de transport mensuel</p>
            </div>
            <button onclick="toggleModal('addProgramModal')" class="bg-primary hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                <i class="fas fa-plus mr-2"></i>Nouveau Programme
            </button>
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

    <!-- Stats -->
    <div class="px-4 sm:px-0 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-route text-primary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Programmes</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_programmes']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-secondary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Actifs</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['programmes_actifs']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-users text-danger text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Complets</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['programmes_complets']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-percentage text-warning text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Taux d'Occupation</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['taux_occupation']; ?>%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Programmes -->
    <div class="px-4 sm:px-0">
        <div class="card-hover bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-list mr-2"></i>Liste des Programmes
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itinéraire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($programmes as $prog): 
                            $pourcentage_occupation = ($prog['places_reservees'] / $prog['capacite']) * 100;
                            $statut_colors = [
                                'actif' => 'bg-green-100 text-green-800',
                                'complet' => 'bg-red-100 text-red-800',
                                'annule' => 'bg-gray-100 text-gray-800'
                            ];
                            $statut_color = $statut_colors[$prog['statut']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                        <i class="fas fa-bus text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $prog['itineraire']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div><?php echo date('d/m/Y', strtotime($prog['date_depart'])); ?></div>
                                <div class="text-gray-500"><?php echo $prog['heure_depart']; ?> - <?php echo $prog['heure_arrivee']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo number_format($prog['prix']); ?> FC
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo $prog['places_reservees']; ?>/<?php echo $prog['capacite']; ?></div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="<?php echo $pourcentage_occupation >= 90 ? 'bg-red-500' : ($pourcentage_occupation >= 70 ? 'bg-yellow-500' : 'bg-green-500'); ?> h-2 rounded-full transition-all duration-300" 
                                         style="width: <?php echo $pourcentage_occupation; ?>%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statut_color; ?>">
                                    <?php echo ucfirst($prog['statut']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="return confirmDelete('Supprimer ce programme ?')" class="text-red-600 hover:text-red-900 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a href="reservations.php?programme_id=<?php echo $prog['id']; ?>" class="text-green-600 hover:text-green-900 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Programme -->
<div id="addProgramModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900">Nouveau Programme</h3>
            <button onclick="toggleModal('addProgramModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="ajouter">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Itinéraire</label>
                    <select name="itineraire" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Sélectionner un itinéraire</option>
                        <option value="Lubumbashi - Kolwezi">Lubumbashi - Kolwezi</option>
                        <option value="Lubumbashi - Likasi">Lubumbashi - Likasi</option>
                        <option value="Lubumbashi - Kipushi">Lubumbashi - Kipushi</option>
                        <option value="Lubumbashi - Fungurume">Lubumbashi - Fungurume</option>
                        <option value="Lubumbashi - Kasumbalesa">Lubumbashi - Kasumbalesa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de Départ</label>
                    <input type="date" name="date_depart" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Heure de Départ</label>
                    <input type="time" name="heure_depart" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Heure d'Arrivée</label>
                    <input type="time" name="heure_arrivee" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix (FC)</label>
                    <input type="number" name="prix" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Capacité</label>
                    <input type="number" name="capacite" required min="1" max="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="toggleModal('addProgramModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden');
}
</script>