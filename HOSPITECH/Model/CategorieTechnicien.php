<?php
//require_once '../config.php';

class CategorieTechnicien {
    private static $pdo;

    public function __construct() {
        try {
            // Suppression de charset=utf8mb4 (non supporté par le driver pgsql dans le DSN)
            $dsn = "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME;
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Forcer l'UTF8 si nécessaire
            self::$pdo->exec("SET NAMES 'UTF8'");
        } catch (\PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function create($id_categorie, $id_technicien) {
        $stmt = $this->pdo->prepare("INSERT INTO categorie_technicien (id_categorie, id_technicien) VALUES (:cat, :tech)");
        return $stmt->execute([
            ':cat'  => $id_categorie,
            ':tech' => $id_technicien
        ]);
    }

    public function getCategoriesByTechnicien($id_technicien) {
        $query = "SELECT c.num_categorie, c.nom_categorie 
                  FROM categorie_technicien ct
                  JOIN categorie c ON ct.id_categorie = c.num_categorie
                  WHERE ct.id_technicien = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id_technicien]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Dans Model/CategorieTechnicien.php (Si tu l'utilises)
    public function update($data) {
        $query = "UPDATE categorie_technicien 
              SET id_categorie = :id_categorie,
                  id_technicien = :id_technicien
              WHERE id_cat_tech = :id_cat_tech";

        $stmt = $this->pdo->prepare($query);
    
        return $stmt->execute([
        ':id_categorie'  => $data->id_categorie,
        ':id_technicien' => $data->id_technicien,
        ':id_cat_tech'   => $data->id_cat_tech
         ]);
    }
}
?>