<?php

class CategorieTechnicien {
    private static $pdo = null;

    private static function getConnexion() {
        if (self::$pdo === null) {
            try {
                $dsn = "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME;
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->exec("SET NAMES 'UTF8'");
            } catch (\PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public function create($data) {
        $db = self::getConnexion();
        $query = "INSERT INTO Categorie_technicien (id_categorie, id_technicien) 
                  VALUES (:id_categorie, :id_technicien)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':id_categorie'  => $data->id_categorie,
            ':id_technicien' => $data->id_technicien
        ]);
        
        return $db->lastInsertId();
    }

    public function update($data) {
        $db = self::getConnexion();
        $query = "UPDATE Categorie_technicien 
                  SET id_categorie = :id_categorie,
                      id_technicien = :id_technicien
                  WHERE id_cat_tech = :id_cat_tech";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':id_categorie'  => $data->id_categorie,
            ':id_technicien' => $data->id_technicien,
            ':id_cat_tech'   => $data->id_cat_tech
        ]);
    }
    
    // Au cas où tu voudrais simplement retirer une spécialité à un technicien
    public function delete($id_cat_tech) {
        $db = self::getConnexion();
        $query = "DELETE FROM Categorie_technicien WHERE id_cat_tech = :id_cat_tech";
        $stmt = $db->prepare($query);
        return $stmt->execute([':id_cat_tech' => $id_cat_tech]);
    }
}
?>