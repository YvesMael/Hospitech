<?php

class Salle {
    private $num_salle;
    private $nom_salle;
    private $id_service;
    private static $pdo = null; 

    private static function getConnexion() {
        // Si la connexion n'existe pas encore, on la crée
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                die("Erreur HopitalModel : " . $e->getMessage());
            }
        }
        // On renvoie la connexion existante
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