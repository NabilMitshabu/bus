<?php
$page_title = "Tableau de Bord - Transport Lubumbashi";
require_once(__DIR__ . '/../classes/Auth.php');
$auth = new Auth();



require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../classes/Programme.php');
require_once(__DIR__ . '/../classes/Reservation.php');

$agence_id = $auth->getAgenceId();
$programme = new Programme();
$reservation = new Reservation();

$stats_programmes = $programme->obtenirStatistiques($agence_id);
$stats_reservations = $reservation->obtenirStatistiques($agence_id);
$programmes = $programme->lireTous($agence_id);
$reservations_recentes = array_slice($reservation->lireTous($agence_id), -5, 5, true);
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <div class="animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Tableau de Bord</h1>
                    <p class="text-gray-600">Aperçu de votre agence de transport à Lubumbashi</p>
                </div>
                <a href="/logout.php" class="inline-block px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition ml-4">Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="px-4 sm:px-0 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Programmes -->
            <div class="card-hover bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-route text-2xl text-primary"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Programmes</dt>
                                <dd class="text-2xl font-bold text-gray-900"><?php echo $stats_programmes['total_programmes']; ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-sm text-green-600 font-medium">
                            <?php echo $stats_programmes['programmes_actifs']; ?> actifs
                        </span>
                    </div>
                </div>
            </div>

            <!-- Réservations -->
            <div class="card-hover bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-ticket-alt text-2xl text-secondary"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Réservations</dt>
                                <dd class="text-2xl font-bold text-gray-900"><?php echo $stats_reservations['total_reservations']; ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-sm text-yellow-600 font-medium">
                            <?php echo $stats_reservations['en_attente']; ?> en attente
                        </span>
                    </div>
                </div>
            </div>

            <!-- Taux d'Occupation -->
            <div class="card-hover bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-2xl text-warning"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Taux d'Occupation</dt>
                                <dd class="text-2xl font-bold text-gray-900"><?php echo $stats_programmes['taux_occupation']; ?>%</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-warning h-2 rounded-full" style="width: <?php echo $stats_programmes['taux_occupation']; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenus -->
            <div class="card-hover bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-2xl text-secondary"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Revenus Total</dt>
                                <dd class="text-2xl font-bold text-gray-900"><?php echo number_format($stats_reservations['revenus_total'] ?? 0); ?> FC</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-sm text-green-600 font-medium">
                            +12% ce mois
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Programmes du Jour -->
            <div class="card-hover bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-calendar-day mr-2 text-primary"></i>
                        Programmes d'Aujourd'hui
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach (array_slice($programmes, 0, 5) as $prog): 
                            $statut_color = $prog['statut'] == 'complet' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                            $places_restantes = $prog['capacite'] - $prog['places_reservees'];
                        ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                        <i class="fas fa-bus text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo $prog['itineraire']; ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo $prog['heure_depart']; ?> - <?php echo $prog['heure_arrivee']; ?>
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            <?php echo $prog['places_reservees']; ?>/<?php echo $prog['capacite']; ?> places
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statut_color; ?>">
                                    <?php echo ucfirst($prog['statut']); ?>
                                </span>
                                <p class="text-sm font-bold text-gray-900 mt-1"><?php echo number_format($prog['prix']); ?> FC</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Réservations Récentes -->
            <div class="card-hover bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-clock mr-2 text-secondary"></i>
                        Réservations Récentes
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($reservations_recentes as $res): 
                            $statut_colors = [
                                'confirme' => 'bg-green-100 text-green-800',
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'annule' => 'bg-red-100 text-red-800'
                            ];
                            $statut_color = $statut_colors[$res['statut']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo $res['nom_client']; ?></p>
                                        <p class="text-xs text-gray-500"><?php echo $res['telephone']; ?></p>
                                        <p class="text-xs text-gray-600"><?php echo $res['nombre_places']; ?> place(s)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statut_color; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $res['statut'])); ?>
                                </span>
                                <p class="text-sm font-bold text-gray-900 mt-1"><?php echo number_format($res['prix_total']); ?> FC</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    <div class="px-4 sm:px-0 mt-8">
        <div class="card-hover bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                    Alertes et Notifications
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <?php
                    $alertes = [];
                    foreach ($programmes as $prog) {
                        if ($prog['statut'] == 'complet') {
                            $alertes[] = [
                                'type' => 'complet',
                                'message' => "Bus {$prog['itineraire']} du {$prog['date_depart']} à {$prog['heure_depart']} est COMPLET",
                                'icon' => 'fas fa-users',
                                'color' => 'text-red-600 bg-red-100'
                            ];
                        }
                        $places_restantes = $prog['capacite'] - $prog['places_reservees'];
                        if ($places_restantes <= 3 && $places_restantes > 0) {
                            $alertes[] = [
                                'type' => 'presque_complet',
                                'message' => "Bus {$prog['itineraire']} - Plus que {$places_restantes} place(s) disponible(s)",
                                'icon' => 'fas fa-exclamation-triangle',
                                'color' => 'text-yellow-600 bg-yellow-100'
                            ];
                        }
                    }
                    
                    if (empty($alertes)) {
                        echo '<div class="text-center py-8 text-gray-500">
                                <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                                <p>Aucune alerte pour le moment</p>
                              </div>';
                    } else {
                        foreach ($alertes as $alerte) {
                            echo '<div class="flex items-center p-3 rounded-lg ' . $alerte['color'] . '">
                                    <i class="' . $alerte['icon'] . ' mr-3"></i>
                                    <span>' . $alerte['message'] . '</span>
                                  </div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
