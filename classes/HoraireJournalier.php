<?php
require_once(__DIR__ . '/../config/database.php');

class HoraireJournalier {
    private $db;
    private $table_name = "horaires_journaliers";

    public function __construct($db = null) {
        $this->db = $db ? $db : new Database();
    }

    // Publier un horaire pour toute la semaine (jusqu'au dimanche) et générer les programmes individuels
    public function publier($agence_id, $itineraire, $horaire_json, $date_debut = null, $prix = null, $capacite = null) {
        $date_debut = $date_debut ?: date('Y-m-d');
        $date_fin = date('Y-m-d', strtotime('next sunday', strtotime($date_debut)));

        // --- Réorganisation des horaires dans la structure BlocTrajet ---
        $jours_semaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        $blocTrajet = new BlocTrajet();
        $horaire_array = is_array($horaire_json) ? $horaire_json : json_decode($horaire_json, true);

        require_once(__DIR__ . '/Programme.php');
        $programme = new Programme($this->db);
        $programmes = $programme->lireTous($agence_id);

        // Indexer les programmes par itineraire, date et jour
        $programmesIndex = [];
        foreach ($programmes as $prog) {
            $itin = $prog['itineraire'];
            $date = $prog['date_depart'];
            $jour = strtolower((new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'EEEE'))->format(strtotime($date)));
            $programmesIndex[$itin][$jour][] = $prog;
        }

        // Répartir les horaires sur tous les jours si besoin
        foreach ($jours_semaine as $jour) {
            $blocJour = new BlocJourSemaine($jour);
            if (isset($horaire_array[$jour])) {
                foreach ($horaire_array[$jour] as $horaire) {
                    $blocJour->ajouterHoraire($horaire);
                }
            } elseif (!empty($horaire_array) && is_array($horaire_array)) {
                foreach ($horaire_array as $horaire) {
                    $blocJour->ajouterHoraire($horaire);
                }
            }
            $blocJour->programmes = $programmesIndex[$itineraire][$jour] ?? [];
            $blocTrajet->ajouterJour($blocJour);
        }
        $horaire_json = json_encode($blocTrajet);
        // ---

        // Supprimer les anciens horaires actifs sur cette période
        $sql = "DELETE FROM {$this->table_name} 
                WHERE agence_id = ? AND itineraire = ? AND date_fin_validite >= ?";
        $this->db->query($sql, [$agence_id, $itineraire, $date_debut]);

        // Insérer le nouvel horaire AVEC prix et capacité enregistrés
        $sql = "INSERT INTO {$this->table_name} 
                (agence_id, itineraire, horaire_json, prix, capacite, date_publication, date_debut_validite, date_fin_validite)
                VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?)";
        $params = [
            $agence_id,
            $itineraire,
            $horaire_json,
            $prix ?? 0,
            $capacite ?? 0,
            $date_debut,
            $date_fin
        ];
        $stmt = $this->db->query($sql, $params);

        // Générer les programmes individuels si prix et capacité sont fournis
        if ($prix !== null && $capacite !== null) {
            $jours = [];
            $d = new DateTime($date_debut);
            $fin = new DateTime($date_fin);
            while ($d <= $fin) {
                $jours[] = $d->format('Y-m-d');
                $d->modify('+1 day');
            }

            foreach ($jours as $jour_date) {
                $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'EEEE');
                $jour_nom = strtolower($formatter->format(strtotime($jour_date)));
                if (isset($blocTrajet->jours[$jour_nom])) {
                    foreach ($blocTrajet->jours[$jour_nom]->horaires as $horaireItem) {
                        if (is_array($horaireItem) && isset($horaireItem['depart'], $horaireItem['arrivee'])) {
                            $heure_depart = $horaireItem['depart'];
                            $heure_arrivee = $horaireItem['arrivee'];
                            $bus_num = $horaireItem['bus'] ?? null;
                        } else {
                            $heure_depart = is_array($horaireItem) ? array_key_first($horaireItem) : $horaireItem;
                            $heure_arrivee = $heure_depart;
                            $bus_num = is_array($horaireItem) ? array_shift($horaireItem) : null;
                        }
                        if (preg_match('/^\d{1,2}$/', $heure_depart)) {
                            $heure_depart = sprintf('%02d:00', (int)$heure_depart);
                        }
                        if (preg_match('/^\d{1,2}$/', $heure_arrivee)) {
                            $heure_arrivee = sprintf('%02d:00', (int)$heure_arrivee);
                        }
                        $data = [
                            'agence_id' => $agence_id,
                            'itineraire' => $itineraire,
                            'date_depart' => $jour_date,
                            'heure_depart' => $heure_depart,
                            'heure_arrivee' => $heure_arrivee,
                            'bus' => $bus_num,
                            'prix' => $prix,
                            'capacite' => $capacite
                        ];
                        $programme->creer($data);
                    }
                }
            }
        }

        return $stmt !== false;
    }

    public function lirePourDate($agence_id, $itineraire, $date) {
        $sql = "SELECT horaire_json FROM {$this->table_name}
                WHERE agence_id = ? AND itineraire = ? AND date_debut_validite <= ? AND date_fin_validite >= ?
                ORDER BY date_publication DESC LIMIT 1";
        $stmt = $this->db->query($sql, [$agence_id, $itineraire, $date, $date]);
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        return $row ? json_decode($row['horaire_json'], true) : null;
    }

    public function lireTous($agence_id) {
        $sql = "SELECT * FROM {$this->table_name} WHERE agence_id = ? ORDER BY date_publication DESC";
        $stmt = $this->db->query($sql, [$agence_id]);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['horaire_json'] = json_decode($row['horaire_json'], true);
            $result[] = $row;
        }
        return $result;
    }
}

// --- Structures pour bloc trajet et jours de la semaine ---
class BlocJourSemaine {
    public $nom;
    public $horaires = [];
    public $programmes = [];

    public function __construct($nom) {
        $this->nom = $nom;
    }
    public function ajouterHoraire($horaire) {
        $this->horaires[] = $horaire;
    }
}

class BlocTrajet {
    public $jours = [];
    public function ajouterJour(BlocJourSemaine $jour) {
        $this->jours[$jour->nom] = $jour;
    }
    public function getJours() {
        return $this->jours;
    }
}
?>
