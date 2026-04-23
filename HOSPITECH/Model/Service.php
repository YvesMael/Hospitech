<?php

class Service {
    private $id_service;
    private $nom_service;
    private $id_hopital;
    private static $pdo = null; 

    private static function getConnexion() {
        if (self::$pdo === null) {
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
        return self::$pdo;
    }

    /**
     * Créer un technicien (après avoir créé l'utilisateur)
     */
    public static function create($nom_service, $id_hopital) {
        $db = self::getConnexion();
        $req = $db->prepare("INSERT INTO service (nom_service, id_hopital) VALUES (?, ?)");
        return $req->execute([$nom_service, $id_hopital]);
    }

    /**
     * Récupérer tous les services d'un hopital
     */
    public static function findAll($id_hopital) {
        $db = self::getConnexion();
        $sql = "SELECT * 
                FROM service
                WHERE id_hopital = ?";
        $req = $db->prepare($sql);
        $req->execute([$id_hopital]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $db = self::getConnexion();
        $req = $db->prepare("DELETE FROM service WHERE id_service = ?");
        return $req->execute([$id]);
    }
}

?>