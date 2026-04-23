<?php

class Salle {
    private $num_salle;
    private $nom_salle;
    private $id_service;
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
    public static function create($nom_salle, $id_service) {
        $db = self::getConnexion();
        $req = $db->prepare("INSERT INTO salle (nom_salle, id_service) VALUES (?, ?)");
        return $req->execute([$nom_salle, $id_service]);
    }

    /**
     * Récupérer toutes les salles d'un service
     */
    public static function findAll($id_service) {
        $db = self::getConnexion();
        $sql = "SELECT * 
                FROM salle
                WHERE id_service = ?";
        $req = $db->prepare($sql);
        $req->execute([$id_service]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $db = self::getConnexion();
        $req = $db->prepare("DELETE FROM salle WHERE num_salle = ?");
        return $req->execute([$id]);
    }
}

?>