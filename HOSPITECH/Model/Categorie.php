<?php

class Categorie {
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
        $query = "INSERT INTO Categorie (nom_categorie) VALUES (:nom_categorie)";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':nom_categorie' => $data->nom_categorie
        ]);
    }

    public function update($data) {
        $db = self::getConnexion();
        $query = "UPDATE Categorie 
                  SET nom_categorie = :nom_categorie
                  WHERE num_categorie = :num_categorie";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':nom_categorie' => $data->nom_categorie,
            ':num_categorie' => $data->num_categorie
        ]);
    }
}
?>