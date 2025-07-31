<?php
$page_title = "Gestion des Réservations - Transport Lubumbashi";

require_once(__DIR__ . '/../classes/Auth.php');
require_once(__DIR__ . '/../classes/Reservation.php');
require_once(__DIR__ . '/../classes/Programme.php');

$auth = new Auth();

require_once(__DIR__ . '/../includes/header.php');

$agence_id = $auth->getAgenceId();
$reservation = new Reservation();
$programme = new Programme();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'valider':
                if ($reservation->valider($_POST['reservation_id'])) {
                    $success = "Réservation validée avec succès!";
                } else {
                    $error = "Erreur lors de la validation.";
                }
                break;
            case 'annuler':
                if ($reservation->annuler($_POST['reservation_id'])) {
                    $success = "Réservation annulée avec succès!";
                } else {
                    $error = "Erreur lors de l'annulation.";
                }
                break;
            case 'nouvelle_reservation':
                $data = [
                    'programme_id' => $_POST['programme_id'],
                    'nom_client' => $_POST['nom_client'],
                    'telephone' => $_POST['telephone'],
                    'email' => $_POST['email'],
                    'nombre_places' => $_POST['nombre_places'],
                    'prix_total' => $_POST['prix_total']
                ];
                if ($reservation->creer($data)) {
                    $success = "Réservation créée avec succès!";
                } else {
                    $error = "Erreur lors de la création de la réservation. Vérifiez le nombre de places disponibles.";
                }
                break;
        }
    }
}

$reservations = $reservation->lireTous($agence_id);
$programmes = $programme->lireTous($agence_id);
$stats = $reservation->obtenirStatistiques($agence_id);

// Filtrer par programme si spécifié
$programme_filtre = isset($_GET['programme_id']) ? $_GET['programme_id'] : null;
if ($programme_filtre) {
    $reservations = array_filter($reservations, function($res) use ($programme_filtre) {
        return $res['programme_id'] == $programme_filtre;
    });
}
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Réservations</h1>
                <p class="text-gray-600">Consultez, validez et gérez toutes les réservations</p>
            </div>
            <button onclick="toggleModal('newReservationModal')" class="bg-secondary hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                <i class="fas fa-plus mr-2"></i>Nouvelle Réservation
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
                        <i class="fas fa-ticket-alt text-primary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Réservations</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_reservations']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-secondary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Confirmées</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['confirmees']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-clock text-warning text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">En Attente</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['en_attente']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-dollar-sign text-secondary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Revenus</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['revenus_total'] ?? 0); ?> FC</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="px-4 sm:px-0 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex flex-wrap items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Filtrer par programme:</label>
                <select onchange="window.location.href = this.value" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="reservations.php">Tous les programmes</option>
                    <?php foreach ($programmes as $prog): ?>
                    <option value="reservations.php?programme_id=<?php echo $prog['id']; ?>" <?php echo $programme_filtre == $prog['id'] ? 'selected' : ''; ?>>
                        <?php echo $prog['itineraire'] . ' - ' . date('d/m/Y', strtotime($prog['date_depart'])); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Liste des Réservations -->
    <div class="px-4 sm:px-0">
        <div class="card-hover bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-list mr-2"></i>Liste des Réservations
                    <?php if ($programme_filtre): ?>
                        <?php
                        $prog_info = array_filter($programmes, function($p) use ($programme_filtre) {
                            return $p['id'] == $programme_filtre;
                        });
                        $prog_info = reset($prog_info);
                        ?>
                        <span class="text-sm text-gray-500">- <?php echo $prog_info['itineraire']; ?></span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programme</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Places</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($reservations as $res): 
                            // Trouver le programme correspondant
                            $prog_reservation = null;
                            foreach ($programmes as $prog) {
                                if ($prog['id'] == $res['programme_id']) {
                                    $prog_reservation = $prog;
                                    break;
                                }
                            }
                            
                            $statut_colors = [
                                'confirme' => 'bg-green-100 text-green-800',
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'annule' => 'bg-red-100 text-red-800'
                            ];
                            $statut_color = $statut_colors[$res['statut']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $res['nom_client']; ?></div>
                                        <div class="text-sm text-gray-500"><?php echo $res['telephone']; ?></div>
                                        <div class="text-xs text-gray-400"><?php echo $res['email']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($prog_reservation): ?>
                                <div class="text-sm text-gray-900"><?php echo $prog_reservation['itineraire']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($prog_reservation['date_depart'])); ?></div>
                                <div class="text-xs text-gray-400"><?php echo $prog_reservation['heure_depart']; ?></div>
                                <?php else: ?>
                                <span class="text-sm text-gray-500">Programme introuvable</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo $res['nombre_places']; ?> place(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo number_format($res['prix_total']); ?> FC
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statut_color; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $res['statut'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y H:i', strtotime($res['date_reservation'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <?php if ($res['statut'] == 'en_attente'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="valider">
                                        <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                        <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($res['statut'] != 'annule'): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Annuler cette réservation ?')">
                                        <input type="hidden" name="action" value="annuler">
                                        <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Annuler">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <button class="text-blue-600 hover:text-blue-900 transition-colors" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
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

<!-- Modal Nouvelle Réservation -->
<div id="newReservationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900">Nouvelle Réservation</h3>
            <button onclick="toggleModal('newReservationModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="nouvelle_reservation">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Programme</label>
                <select name="programme_id" id="programme_select" required onchange="updatePrixTotal()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Sélectionner un programme</option>
                    <?php foreach ($programmes as $prog): 
                        if ($prog['statut'] == 'actif') {
                            $places_disponibles = $prog['capacite'] - $prog['places_reservees'];
                    ?>
                    <option value="<?php echo $prog['id']; ?>" data-prix="<?php echo $prog['prix']; ?>" data-places="<?php echo $places_disponibles; ?>">
                        <?php echo $prog['itineraire']; ?> - <?php echo date('d/m/Y', strtotime($prog['date_depart'])); ?> (<?php echo $places_disponibles; ?> places disponibles)
                    </option>
                    <?php } endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Client</label>
                    <input type="text" name="nom_client" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" name="telephone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de Places</label>
                    <input type="number" name="nombre_places" id="nombre_places" required min="1" onchange="updatePrixTotal()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix Total (FC)</label>
                    <input type="number" name="prix_total" id="prix_total" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="toggleModal('newReservationModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Créer la Réservation
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

function updatePrixTotal() {
    const programmeSelect = document.getElementById('programme_select');
    const nombrePlaces = document.getElementById('nombre_places');
    const prixTotal = document.getElementById('prix_total');
    
    const selectedOption = programmeSelect.options[programmeSelect.selectedIndex];
    const prix = parseInt(selectedOption.getAttribute('data-prix')) || 0;
    const places = parseInt(nombrePlaces.value) || 0;
    const placesDisponibles = parseInt(selectedOption.getAttribute('data-places')) || 0;
    
    if (places > placesDisponibles) {
        alert(`Seulement ${placesDisponibles} place(s) disponible(s) pour ce programme.`);
        nombrePlaces.value = placesDisponibles;
        places = placesDisponibles;
    }
    
    prixTotal.value = prix * places;
}
</script>