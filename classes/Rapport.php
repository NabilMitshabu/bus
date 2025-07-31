<?php
require_once(__DIR__ . '/../config/database.php');

class Rapport {
    private $conn;

    public function __construct($db = null) {
        $this->conn = $db;
    }

    // Générer un rapport financier
    public function genererRapportFinancier($agence_id, $date_debut, $date_fin) {
        $rapport = [
            'periode' => [
                'debut' => $date_debut,
                'fin' => $date_fin
            ],
            'revenus' => [
                'total' => 0,
                'confirmes' => 0,
                'en_attente' => 0
            ],
            'reservations' => [
                'total' => 0,
                'confirmees' => 0,
                'en_attente' => 0,
                'annulees' => 0
            ],
            'programmes' => [
                'total' => 0,
                'actifs' => 0,
                'complets' => 0
            ],
            'details_par_itineraire' => []
        ];

        // Analyser les programmes de l'agence dans la période
        foreach (MockDatabase::$programmes as $programme) {
            if ($programme['agence_id'] == $agence_id) {
                $date_programme = $programme['date_depart'];
                if ($date_programme >= $date_debut && $date_programme <= $date_fin) {
                    $rapport['programmes']['total']++;
                    if ($programme['statut'] == 'actif') $rapport['programmes']['actifs']++;
                    if ($programme['statut'] == 'complet') $rapport['programmes']['complets']++;

                    // Initialiser les détails par itinéraire
                    if (!isset($rapport['details_par_itineraire'][$programme['itineraire']])) {
                        $rapport['details_par_itineraire'][$programme['itineraire']] = [
                            'programmes' => 0,
                            'reservations' => 0,
                            'revenus' => 0,
                            'taux_occupation' => 0,
                            'capacite_totale' => 0,
                            'places_vendues' => 0
                        ];
                    }

                    $itineraire = &$rapport['details_par_itineraire'][$programme['itineraire']];
                    $itineraire['programmes']++;
                    $itineraire['capacite_totale'] += $programme['capacite'];
                    $itineraire['places_vendues'] += $programme['places_reservees'];

                    // Analyser les réservations pour ce programme
                    foreach (MockDatabase::$reservations as $reservation) {
                        if ($reservation['programme_id'] == $programme['id']) {
                            $rapport['reservations']['total']++;
                            $itineraire['reservations']++;

                            switch ($reservation['statut']) {
                                case 'confirme':
                                    $rapport['reservations']['confirmees']++;
                                    $rapport['revenus']['confirmes'] += $reservation['prix_total'];
                                    $itineraire['revenus'] += $reservation['prix_total'];
                                    break;
                                case 'en_attente':
                                    $rapport['reservations']['en_attente']++;
                                    $rapport['revenus']['en_attente'] += $reservation['prix_total'];
                                    break;
                                case 'annule':
                                    $rapport['reservations']['annulees']++;
                                    break;
                            }
                        }
                    }

                    // Calculer le taux d'occupation
                    if ($itineraire['capacite_totale'] > 0) {
                        $itineraire['taux_occupation'] = round(($itineraire['places_vendues'] / $itineraire['capacite_totale']) * 100, 2);
                    }
                }
            }
        }

        $rapport['revenus']['total'] = $rapport['revenus']['confirmes'] + $rapport['revenus']['en_attente'];

        return $rapport;
    }

    // Générer un rapport de performance
    public function genererRapportPerformance($agence_id, $periode = 'mois') {
        $rapport = [
            'periode' => $periode,
            'evolution_revenus' => [],
            'evolution_reservations' => [],
            'top_itineraires' => [],
            'indicateurs_cles' => []
        ];

        // Selon la période, générer les données
        if ($periode == 'mois') {
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $revenus = 0;
                $reservations = 0;

                foreach (MockDatabase::$reservations as $reservation) {
                    if (date('Y-m', strtotime($reservation['date_reservation'])) == $date) {
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

                $rapport['evolution_revenus'][] = [
                    'periode' => $date,
                    'revenus' => $revenus
                ];
                $rapport['evolution_reservations'][] = [
                    'periode' => $date,
                    'reservations' => $reservations
                ];
            }
        }

        // Top itinéraires
        $itineraires = [];
        foreach (MockDatabase::$programmes as $programme) {
            if ($programme['agence_id'] == $agence_id) {
                if (!isset($itineraires[$programme['itineraire']])) {
                    $itineraires[$programme['itineraire']] = 0;
                }
                $itineraires[$programme['itineraire']] += $programme['places_reservees'];
            }
        }
        arsort($itineraires);
        $rapport['top_itineraires'] = array_slice($itineraires, 0, 5, true);

        return $rapport;
    }

    // Générer un rapport de clients
    public function genererRapportClients($agence_id) {
        $clients = [];
        $stats = [
            'total_clients' => 0,
            'clients_fideles' => 0,
            'nouveaux_clients' => 0,
            'revenus_par_client' => []
        ];

        foreach (MockDatabase::$reservations as $reservation) {
            // Vérifier si c'est une réservation de cette agence
            foreach (MockDatabase::$programmes as $programme) {
                if ($programme['id'] == $reservation['programme_id'] && $programme['agence_id'] == $agence_id) {
                    $telephone = $reservation['telephone'];
                    
                    if (!isset($clients[$telephone])) {
                        $clients[$telephone] = [
                            'nom' => $reservation['nom_client'],
                            'telephone' => $telephone,
                            'email' => $reservation['email'],
                            'reservations' => 0,
                            'revenus_total' => 0,
                            'premiere_reservation' => $reservation['date_reservation'],
                            'derniere_reservation' => $reservation['date_reservation']
                        ];
                    }

                    $clients[$telephone]['reservations']++;
                    if ($reservation['statut'] == 'confirme') {
                        $clients[$telephone]['revenus_total'] += $reservation['prix_total'];
                    }
                    
                    if ($reservation['date_reservation'] > $clients[$telephone]['derniere_reservation']) {
                        $clients[$telephone]['derniere_reservation'] = $reservation['date_reservation'];
                    }
                    break;
                }
            }
        }

        $stats['total_clients'] = count($clients);
        
        foreach ($clients as $client) {
            if ($client['reservations'] > 1) {
                $stats['clients_fideles']++;
            }
            
            // Nouveau client si première réservation dans les 30 derniers jours
            if (strtotime($client['premiere_reservation']) > strtotime('-30 days')) {
                $stats['nouveaux_clients']++;
            }
            
            $stats['revenus_par_client'][] = [
                'nom' => $client['nom'],
                'revenus' => $client['revenus_total'],
                'reservations' => $client['reservations']
            ];
        }

        // Trier par revenus décroissants
        usort($stats['revenus_par_client'], function($a, $b) {
            return $b['revenus'] - $a['revenus'];
        });

        return [
            'statistiques' => $stats,
            'details_clients' => array_slice($stats['revenus_par_client'], 0, 20) // Top 20
        ];
    }

    // Exporter un rapport en CSV
    public function exporterCSV($donnees, $nom_fichier) {
        $chemin = __DIR__ . '/../exports/' . $nom_fichier . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Créer le dossier exports s'il n'existe pas
        $dossier = dirname($chemin);
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        $fichier = fopen($chemin, 'w');
        
        if (!empty($donnees)) {
            // Écrire les en-têtes
            fputcsv($fichier, array_keys($donnees[0]));
            
            // Écrire les données
            foreach ($donnees as $ligne) {
                fputcsv($fichier, $ligne);
            }
        }
        
        fclose($fichier);
        return $chemin;
    }

    // Générer un rapport d'occupation
    public function genererRapportOccupation($agence_id, $date_debut, $date_fin) {
        $rapport = [
            'taux_occupation_global' => 0,
            'capacite_totale' => 0,
            'places_vendues' => 0,
            'programmes_complets' => 0,
            'programmes_total' => 0,
            'details_par_jour' => []
        ];

        foreach (MockDatabase::$programmes as $programme) {
            if ($programme['agence_id'] == $agence_id && 
                $programme['date_depart'] >= $date_debut && 
                $programme['date_depart'] <= $date_fin) {
                
                $rapport['programmes_total']++;
                $rapport['capacite_totale'] += $programme['capacite'];
                $rapport['places_vendues'] += $programme['places_reservees'];
                
                if ($programme['statut'] == 'complet') {
                    $rapport['programmes_complets']++;
                }

                $date = $programme['date_depart'];
                if (!isset($rapport['details_par_jour'][$date])) {
                    $rapport['details_par_jour'][$date] = [
                        'programmes' => 0,
                        'capacite' => 0,
                        'vendues' => 0,
                        'taux' => 0
                    ];
                }

                $jour = &$rapport['details_par_jour'][$date];
                $jour['programmes']++;
                $jour['capacite'] += $programme['capacite'];
                $jour['vendues'] += $programme['places_reservees'];
                $jour['taux'] = $jour['capacite'] > 0 ? round(($jour['vendues'] / $jour['capacite']) * 100, 2) : 0;
            }
        }

        $rapport['taux_occupation_global'] = $rapport['capacite_totale'] > 0 ? 
            round(($rapport['places_vendues'] / $rapport['capacite_totale']) * 100, 2) : 0;

        return $rapport;
    }
}
?>
