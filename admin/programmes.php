<?php
$page_title = "Gestion des Programmes - Transport Lubumbashi";

require_once(__DIR__ . '/../classes/Auth.php');
require_once(__DIR__ . '/../classes/Programme.php');
require_once(__DIR__ . '/../classes/HoraireJournalier.php');

$auth = new Auth();

require_once(__DIR__ . '/../includes/header.php');

$agence_id = $auth->getAgenceId();
$programme = new Programme();
$horaireJournalier = new HoraireJournalier();

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
            case 'publier_horaire_journalier':
                $itineraire = $_POST['itineraire'];
                $date_debut = $_POST['date_debut'];
                $horaire = $_POST['horaire'];
                $prix = $_POST['prix'];
                $capacite = $_POST['capacite'];

                // Nettoyer le tableau horaire pour ne garder que les heures renseignées
                $horaireNettoye = [];
                foreach ($horaire as $heure => $horaireItem) {
                    if (trim($horaireItem['depart']) !== '' && trim($horaireItem['arrivee']) !== '' && trim($horaireItem['bus']) !== '') {
                        $horaireNettoye[$heure] = $horaireItem;
                    }
                }

                // DEBUG : log des données envoyées à publier
                error_log('DEBUG PUBLISH: '.print_r([
                    'itineraire' => $itineraire,
                    'date_debut' => $date_debut,
                    'horaire' => $horaireNettoye,
                    'prix' => $prix,
                    'capacite' => $capacite
                ], true));

                if (empty($horaireNettoye)) {
                    $error = "Veuillez renseigner au moins un départ (heure de départ, heure d'arrivée et bus).";
                } else {
                    $horaireJson = json_encode($horaireNettoye);
                    if ($horaireJournalier->publier($agence_id, $itineraire, $horaireJson, $date_debut, $prix, $capacite)) {
                        $success = "Horaire journalier publié pour la semaine !";
                    } else {
                        $error = "Erreur lors de la publication de l'horaire journalier.";
                    }
                }
                break;
        }
    }
}

$programmes = $programme->lireTous($agence_id);
$stats = $programme->obtenirStatistiques($agence_id);
$horairesJournaliers = $horaireJournalier->lireTous($agence_id);

// Charger la liste complète des programmes
$programmes = $programme->lireTous($agence_id);

// DEBUG : Afficher tous les programmes chargés
error_log('DEBUG PROGRAMMES EN BASE:');
foreach ($programmes as $prog) {
    error_log("itineraire={$prog['itineraire']} date={$prog['date_depart']} heure={$prog['heure_depart']}");
}

// Indexer les programmes par itinéraire, date et heure pour accès rapide
$programmesIndex = [];
foreach ($programmes as $prog) {
    $itineraireProg = $prog['itineraire'];
    $dateProg = $prog['date_depart'];
    $heureProg = $prog['heure_depart'];
    $programmesIndex[$itineraireProg][$dateProg][$heureProg][] = $prog;
}

// Organiser les horaires journaliers par itinéraire et par jour selon la structure BlocTrajet
$horairesOrganises = [];
foreach ($horairesJournaliers as $horaire) {
    $itineraire = $horaire['itineraire'];
    $date_debut = $horaire['date_debut_validite'];
    $date_fin = $horaire['date_fin_validite'];
    $blocTrajet = $horaire['horaire_json']; // déjà décodé
    if (!isset($horairesOrganises[$itineraire])) {
        $horairesOrganises[$itineraire] = [];
    }
    foreach ($blocTrajet['jours'] ?? [] as $jourNom => $blocJour) {
        if (!isset($horairesOrganises[$itineraire][$jourNom])) {
            $horairesOrganises[$itineraire][$jourNom] = [];
        }
        $horairesAvecProgrammes = [];
        foreach ($blocJour['horaires'] ?? [] as $k => $v) {
            // Extraire l'heure de départ selon la structure (nouveau ou ancien format)
            if (is_array($v) && isset($v['depart'])) {
                $heure = $v['depart'];
                $bus = $v['bus'] ?? null;
            } elseif (is_array($blocJour['horaires']) && array_keys($blocJour['horaires']) !== range(0, count($blocJour['horaires']) - 1)) {
                $heure = $k;
                $bus = $v;
            } else {
                $heure = $v;
                $bus = null;
            }
            // Forcer le format HH:MM:SS pour la clé
            if (preg_match('/^\d{2}:\d{2}$/', $heure)) {
                $heureRecherche = $heure . ':00';
            } else {
                $heureRecherche = $heure;
            }
            // Pour chaque jour de validité
            $dateCursor = new DateTime($date_debut);
            $dateFin = new DateTime($date_fin);
            while ($dateCursor <= $dateFin) {
                $dateStr = $dateCursor->format('Y-m-d');
                $horairesAvecProgrammes[] = [
                    'heure' => $heure,
                    'bus' => $bus,
                    'programmes' => $programmesIndex[$itineraire][$dateStr][$heureRecherche] ?? []
                ];
                
                $dateCursor->modify('+1 day');
            }
        }
        if (!empty($horairesAvecProgrammes)) {
            $horairesOrganises[$itineraire][$jourNom][] = [
                'horaires' => $horairesAvecProgrammes,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'prix' => $horaire['prix'] ?? null,
                'capacite' => $horaire['capacite'] ?? null
            ];
        }
    }
}
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Programmes</h1>
                <p class="text-gray-600">Gérez vos programmes de transport mensuel</p>
            </div>
            <button onclick="toggleModal('addDailyScheduleModal')" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg ml-4">
                <i class="fas fa-calendar-day mr-2"></i>Publier l'horaire journalier
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

    <!-- Section Horaires Journaliers -->
    <div class="px-4 sm:px-0 mt-8">
        <div class="card-hover bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-calendar-day mr-2"></i>Horaires Journaliers
                </h3>
            </div>
            
            <form method="get" class="mb-4">
                <label for="itineraire" class="font-semibold mr-2">Filtrer par itinéraire :</label>
                <select name="itineraire" id="itineraire" onchange="this.form.submit()" class="border rounded px-2 py-1">
                    <?php 
                    $listeItineraires = array_keys($horairesOrganises);
                    $itineraireFiltre = isset($_GET['itineraire']) ? $_GET['itineraire'] : (count($listeItineraires) > 0 ? $listeItineraires[0] : null);
                    ?>
                    <?php foreach ($listeItineraires as $itin): ?>
                        <option value="<?php echo htmlspecialchars($itin); ?>" <?php if ($itineraireFiltre === $itin) echo 'selected'; ?>><?php echo htmlspecialchars($itin); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            
            <div class="p-6">
                <?php if ($itineraireFiltre && isset($horairesOrganises[$itineraireFiltre])): ?>

                    <!-- Affichage des horaires pour l'itinéraire filtré -->
                    <?php $jours = $horairesOrganises[$itineraireFiltre]; ?>
                  
                    <?php $joursNormalises = [];
                    $mapFrEn = [
                        'lundi' => 'Monday',
                        'mardi' => 'Tuesday',
                        'mercredi' => 'Wednesday',
    'jeudi' => 'Thursday',
    'vendredi' => 'Friday',
    'samedi' => 'Saturday',
    'dimanche' => 'Sunday'
];
foreach ($jours as $jourFr => $data) {
    $jourEn = $mapFrEn[strtolower($jourFr)] ?? $jourFr;
    $joursNormalises[$jourEn] = $data;
}
$jours = $joursNormalises;

                    ?>
                    <div class="mb-8 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-primary text-white px-6 py-3">
                            <h4 class="font-bold text-lg">
                                <i class="fas fa-route mr-2"></i><?php echo $itineraireFiltre; ?>
                            </h4>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4 p-4">
                            <?php 
                            $joursSemaine = [
                                'Monday' => 'Lundi',
                                'Tuesday' => 'Mardi',
                                'Wednesday' => 'Mercredi',
                                'Thursday' => 'Jeudi',
                                'Friday' => 'Vendredi',
                                'Saturday' => 'Samedi',
                                'Sunday' => 'Dimanche'
                            ];
                            
                            foreach ($joursSemaine as $jourEn => $jourFr): 
                                $hasHoraires = isset($jours[$jourEn]);
                            ?>
                                <div class="border rounded-lg <?php echo $hasHoraires ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50'; ?>">
                                    <div class="p-3 border-b <?php echo $hasHoraires ? 'bg-blue-100' : 'bg-gray-100'; ?>">
                                        <h5 class="font-medium text-center <?php echo $hasHoraires ? 'text-blue-800' : 'text-gray-500'; ?>">
                                            <?php echo $jourFr; ?>
                                        </h5>
                                    </div>
                                    
                                    <div class="p-3">
                                        <?php if ($hasHoraires): ?>
                                            <?php foreach ($jours[$jourEn] as $horaire): ?>
                                                <div class="mb-3">
                                                    <div class="text-xs text-gray-500 mb-1">Prix: <?php echo number_format($horaire['prix']); ?> FC</div>
                                                    <div class="text-xs text-gray-500 mb-2">Capacité: <?php echo $horaire['capacite']; ?> places</div>
                                                    
                                                    <div class="space-y-2">
                                                        <?php foreach ($horaire['horaires'] as $horaireItem): ?>
                                                            <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-200">
                                                                <span class="font-medium"><?php echo $horaireItem['heure']; ?></span>
                                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Bus <?php echo $horaireItem['bus']; ?></span>
                                                                <?php if (!empty($horaireItem['programmes'])): ?>
    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
        Programmes: <?php echo count($horaireItem['programmes']); ?>
    </span>
<?php else: ?>
    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">
        Aucun programme
    </span>
<?php endif; ?>

                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center py-4 text-gray-400">
                                                <i class="fas fa-ban"></i>
                                                <p class="text-xs mt-1">Pas de départ</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-red-500">Aucun horaire disponible pour cet itinéraire.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout d'horaire journalier -->
<div id="addDailyScheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900">Horaire journalier pour la semaine</h3>
            <button onclick="toggleModal('addDailyScheduleModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="publier_horaire_journalier">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de début (lundi ou aujourd'hui)</label>
                    <input type="date" name="date_debut" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Heures de départ, arrivée et bus pour chaque programme</label>
                <div class="overflow-x-auto">
                    <table class="min-w-full border divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-2 py-1 text-xs font-medium text-gray-500 uppercase">Heure de départ</th>
                                <th class="px-2 py-1 text-xs font-medium text-gray-500 uppercase">Heure d'arrivée</th>
                                <th class="px-2 py-1 text-xs font-medium text-gray-500 uppercase">Bus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($h = 6; $h <= 22; $h++): ?>
                                <tr>
                                    <td class="px-2 py-1">
                                        <input type="time" name="horaire[<?php echo $h; ?>][depart]" value="<?php printf('%02d:00', $h); ?>" class="border rounded px-2 py-1 w-24">
                                    </td>
                                    <td class="px-2 py-1">
                                        <input type="time" name="horaire[<?php echo $h; ?>][arrivee]" class="border rounded px-2 py-1 w-24">
                                    </td>
                                    <td class="px-2 py-1">
                                        <input type="text" name="horaire[<?php echo $h; ?>][bus]" class="border rounded px-2 py-1 w-24">
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
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
                <button type="button" onclick="toggleModal('addDailyScheduleModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Publier pour la semaine
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

function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>