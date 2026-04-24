<?php

class Hopital {
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
        $query = "INSERT INTO Hopital (nom_hopital, adresse, telephone) VALUES (:nom_hopital, :adresse, :telephone)";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':nom_hopital' => $data->nom_hopital,
            ':adresse'     => $data->adresse,
            ':telephone'   => $data->telephone
        ]);
    }
    public function findAll() {
        $db = self::getConnexion();
        $query = "SELECT * FROM Hopital";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        // PDO::FETCH_ASSOC permet de renvoyer un tableau JSON propre 
        // avec seulement les noms des colonnes (sans les numéros d'index)
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    public function update($data) {
        $db = self::getConnexion();
        $query = "UPDATE Hopital 
                  SET nom_hopital = :nom_hopital, 
                      adresse = :adresse, 
                      telephone = :telephone 
                  WHERE id_hopital = :id_hopital";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':nom_hopital' => $data->nom_hopital,
            ':adresse'     => $data->adresse,
            ':telephone'   => $data->telephone,
            ':id_hopital'  => $data->id_hopital
        ]);
    }
}
?>