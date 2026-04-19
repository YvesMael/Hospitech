<?php
require_once '../config.php';

class Equipement {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Erreur EquipementModel : " . $e->getMessage());
        }
    }

    public function getAllWithDetails() {
        $query = "SELECT e.*, c.nom_categorie, s.nom_salle, h.nom_hopital
                  FROM Equipement e
                  LEFT JOIN Categorie c ON e.id_categorie = c.num_categorie
                  LEFT JOIN Salle s ON e.num_salle = s.num_salle
                  LEFT JOIN Hopital h ON e.id_hopital = h.id_hopital
                  ORDER BY e.date_prochaine_maintenance ASC";
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO Equipement 
                  (code_equip, num_equip, marque, modele, etat_equip, date_ajout, freq_jours, freq_mois, freq_ans, num_salle, id_categorie, id_hopital) 
                  VALUES (:code, :num, :marque, :modele, :etat, :date_aj, :fj, :fm, :fa, :salle, :cat, :hopital)";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':code'    => $data['code_equip'],
            ':num'     => $data['num_equip'],
            ':marque'  => $data['marque'] ?? null,
            ':modele'  => $data['modele'] ?? null,
            ':etat'    => $data['etat_equip'] ?? 'en fonctionnement',
            ':date_aj' => $data['date_ajout'],
            ':fj'      => $data['freq_jours'] ?? 0,
            ':fm'      => $data['freq_mois'] ?? 0,
            ':fa'      => $data['freq_ans'] ?? 0,
            ':salle'   => $data['num_salle'],
            ':cat'     => $data['id_categorie'],
            ':hopital' => $data['id_hopital']
        ]);
    }

    public function update($data) {
        // La date_ajout n'est PAS ici par mesure de sécurité
        $query = "UPDATE Equipement SET 
                    marque = :marque, modele = :modele, etat_equip = :etat, 
                    freq_jours = :fj, freq_mois = :fm, freq_ans = :fa, 
                    num_salle = :salle, id_categorie = :cat, id_hopital = :hopital
                  WHERE code_equip = :code";
                  
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':marque'  => $data['marque'] ?? null,
            ':modele'  => $data['modele'] ?? null,
            ':etat'    => $data['etat_equip'] ?? 'en fonctionnement',
            ':fj'      => $data['freq_jours'] ?? 0,
            ':fm'      => $data['freq_mois'] ?? 0,
            ':fa'      => $data['freq_ans'] ?? 0,
            ':salle'   => $data['num_salle'],
            ':cat'     => $data['id_categorie'],
            ':hopital' => $data['id_hopital'],
            ':code'    => $data['code_equip']
        ]);
        return $stmt->rowCount() > 0;
    }
}
?>