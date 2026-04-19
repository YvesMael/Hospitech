<?php

class Service {
    private $id_service;
    private $nom_service;
    private $id_hopital;
    private static $pdo = null; 

    private static function getConnexion() {
        // Si la connexion n'existe pas encore, on la crée
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO("mysql:host=localhost;dbname=gestion_stock;charset=utf8", "root", "");
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                die("Erreur connexion : " . $e->getMessage());
            }
        }
        // On renvoie la connexion existante
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