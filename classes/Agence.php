<?php
require_once(__DIR__ . '/../config/database.php');

class Agence {
    private $conn;
    private $table_name = "agences";

    public $id;
    public $nom;
    public $adresse;
    public $telephone;
    public $email;
    public $licence;
    public $statut;
    public $date_creation;

    public function __construct($db = null) {
        $this->conn = $db;
    }

    // Récupérer les informations de l'agence connectée
    public function obtenirInfos($agence_id) {
        foreach (MockDatabase::$agences as $agence) {
            if ($agence['id'] == $agence_id) {
                return $agence;
            }
        }
        return null;
    }

    // Mettre à jour les informations de l'agence
    public function mettreAJour($agence_id, $data) {
        foreach (MockDatabase::$agences as &$agence) {
            if ($agence['id'] == $agence_id) {
                $agence['nom'] = $data['nom'] ?? $agence['nom'];
                $agence['adresse'] = $data['adresse'] ?? $agence['adresse'];
                $agence['telephone'] = $data['telephone'] ?? $agence['telephone'];
                $agence['email'] = $data['email'] ?? $agence['email'];
                $agence['licence'] = $data['licence'] ?? $agence['licence'];
                return true;
            }
        }
        return false;
    }

    // Obtenir les statistiques de l'agence
    public function obtenirStatistiques($agence_id) {
        $programmes = 0;
        $reservations = 0;
        $revenus_total = 0;
        $clients_uniques = [];

        // Compter les programmes de l'agence
        foreach (MockDatabase::$programmes as $programme) {
            if ($programme['agence_id'] == $agence_id) {
                $programmes++;
            }
        }

        // Compter les réservations et revenus
        foreach (MockDatabase::$reservations as $reservation) {
            // Trouver le programme correspondant
            foreach (MockDatabase::$programmes as $programme) {
                if ($programme['id'] == $reservation['programme_id'] && $programme['agence_id'] == $agence_id) {
                    $reservations++;
                    if ($reservation['statut'] == 'confirme') {
                        $revenus_total += $reservation['prix_total'];
                    }
                    $clients_uniques[$reservation['telephone']] = true;
                    break;
                }
            }
        }

        return [
            'total_programmes' => $programmes,
            'total_reservations' => $reservations,
            'revenus_total' => $revenus_total,
            'clients_uniques' => count($clients_uniques),
            'taux_conversion' => $reservations > 0 ? round((array_filter(MockDatabase::$reservations, function($r) { return $r['statut'] == 'confirme'; }) ? count(array_filter(MockDatabase::$reservations, function($r) { return $r['statut'] == 'confirme'; })) : 0) / $reservations * 100, 2) : 0
        ];
    }

    // Obtenir les performances mensuelles
    public function obtenirPerformancesMensuelles($agence_id, $annee = null) {
        $annee = $annee ?? date('Y');
        $performances = [];

        for ($mois = 1; $mois <= 12; $mois++) {
            $revenus = 0;
            $reservations = 0;

            foreach (MockDatabase::$reservations as $reservation) {
                $date_reservation = new DateTime($reservation['date_reservation']);
                if ($date_reservation->format('Y') == $annee && $date_reservation->format('n') == $mois) {
                    // Vérifier si c'est une réservation de cette agence
                    foreach (MockDatabase::$programmes as $programme) {
                        if ($programme['id'] == $reservation['programme_id'] && $programme['agence_id'] == $agence_id) {
                            $reservations++;
                            if ($reservation['statut'] == 'confirme') {
                                $revenus += $reservation['prix_total'];
                            }
                            break;
                        }
                    }
                }
            }

            $performances[] = [
                'mois' => $mois,
                'nom_mois' => date('F', mktime(0, 0, 0, $mois, 1)),
                'revenus' => $revenus,
                'reservations' => $reservations
            ];
        }

        return $performances;
    }

    // Obtenir les itinéraires les plus populaires
    public function obtenirItinerairesPopulaires($agence_id) {
        $itineraires = [];

        foreach (MockDatabase::$programmes as $programme) {
            if ($programme['agence_id'] == $agence_id) {
                if (!isset($itineraires[$programme['itineraire']])) {
                    $itineraires[$programme['itineraire']] = [
                        'itineraire' => $programme['itineraire'],
                        'programmes' => 0,
                        'reservations' => 0,
                        'revenus' => 0
                    ];
                }
                $itineraires[$programme['itineraire']]['programmes']++;

                // Compter les réservations pour ce programme
                foreach (MockDatabase::$reservations as $reservation) {
                    if ($reservation['programme_id'] == $programme['id']) {
                        $itineraires[$programme['itineraire']]['reservations']++;
                        if ($reservation['statut'] == 'confirme') {
                            $itineraires[$programme['itineraire']]['revenus'] += $reservation['prix_total'];
                        }
                    }
                }
            }
        }

        // Trier par nombre de réservations
        usort($itineraires, function($a, $b) {
            return $b['reservations'] - $a['reservations'];
        });

        return array_slice($itineraires, 0, 5); // Top 5
    }
}
?>
