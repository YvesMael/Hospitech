<?php
require_once '../config.php';

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

    public function update($num_categorie, $nom_categorie) {
        $stmt = $this->pdo->prepare("UPDATE Categorie SET nom_categorie = :nom WHERE num_categorie = :id");
        return $stmt->execute([
            ':nom' => $nom_categorie,
            ':id'  => $num_categorie
        ]);
    }
}
?>