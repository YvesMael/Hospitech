<?php
//require_once '../config.php';

class CategorieTechnicien {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Erreur CategorieTechnicienModel : " . $e->getMessage());
        }
    }

    public function create($id_categorie, $id_technicien) {
        $stmt = $this->pdo->prepare("INSERT INTO Categorie_technicien (id_categorie, id_technicien) VALUES (:cat, :tech)");
        return $stmt->execute([
            ':cat'  => $id_categorie,
            ':tech' => $id_technicien
        ]);
    }

    public function getCategoriesByTechnicien($id_technicien) {
        $query = "SELECT c.num_categorie, c.nom_categorie 
                  FROM Categorie_technicien ct
                  JOIN Categorie c ON ct.id_categorie = c.num_categorie
                  WHERE ct.id_technicien = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id_technicien]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>