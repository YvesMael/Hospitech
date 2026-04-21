<?php
//require_once '../config.php';

class Hopital{
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Erreur HopitalModel : " . $e->getMessage());
        }
    }

    public function getAll() {
        return $this->pdo->query("SELECT * FROM Hopital ORDER BY nom_hopital ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO Hopital (nom_hopital, adresse, telephone) VALUES (:nom, :adresse, :tel)");
        $stmt->execute([
            ':nom'     => $data['nom_hopital'],
            ':adresse' => $data['adresse'] ?? null,
            ':tel'     => $data['telephone'] ?? null
        ]);
        return $this->pdo->lastInsertId();
    }
   // Dans Model/Hopital.php (Fonction UPDATE)
    public function update($data) {
    $query = "UPDATE Hopital 
              SET nom_hopital = :nom_hopital, 
                  adresse = :adresse, 
                  telephone = :telephone 
              WHERE id_hopital = :id_hopital";

    $stmt = $this->pdo->prepare($query);
    
    return $stmt->execute([
        ':nom_hopital' => $data->nom_hopital,
        ':adresse'     => $data->adresse,
        ':telephone'   => $data->telephone,
        ':id_hopital'  => $data->id_hopital
    ]);
}
}
?>