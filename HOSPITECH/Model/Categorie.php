<?php
//require_once '../config.php';

class Categorie{
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Erreur CategorieModel : " . $e->getMessage());
        }
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nom_categorie) {
        $stmt = $this->pdo->prepare("INSERT INTO Categorie (nom_categorie) VALUES (:nom)");
        $stmt->execute([':nom' => $nom_categorie]);
        return $this->pdo->lastInsertId();
    }

    // Dans Model/Categorie.php (Fonction UPDATE)
    public function update($data) {
    $query = "UPDATE Categorie 
              SET nom_categorie = :nom_categorie
              WHERE num_categorie = :num_categorie";

    $stmt = $this->pdo->prepare($query);
    
    return $stmt->execute([
        ':nom_categorie' => $data->nom_categorie,
        ':num_categorie' => $data->num_categorie
    ]);
    }
}
?>