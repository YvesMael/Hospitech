<?php

class Personel {
    private $id_utilisateur;
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
     * Créer un personnel medical (après avoir créé l'utilisateur)
     */
    public static function create($id_utilisateur) {
        $db = self::getConnexion();
        $req = $db->prepare("INSERT INTO personnel_medical (id_utilisateur) VALUES (?)");
        return $req->execute([$id_utilisateur]);
    }

    /**
     * Trouver un personnel medical par son mail
     */
    public static function findByEmail($email) {
        $db = self::getConnexion();
        // On récupère les infos de l'utilisateur ET du personnel_medical en une fois
        $sql = "SELECT u.* 
                FROM utilisateurs u 
                JOIN personnel_medical t ON u.id_utilisateur = t.id_utilisateur 
                WHERE u.adresse_mail = ?";
        
        $req = $db->prepare($sql);
        $req->execute([$email]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les personnel_medicaux avec leurs noms
     */
    public static function findAll() {
        $db = self::getConnexion();
        $sql = "SELECT u.nom, u.prenom, t.id_utilisateur 
                FROM personnel_medical t
                JOIN utilisateurs u ON t.id_utilisateur = u.id_utilisateur
                ORDER BY u.nom ASC";
        
        $req = $db->query($sql);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $db = self::getConnexion();
        // Attention : supprimer dans personnel_medical d'abord, puis utilisateur (contraintes d'intégrité)
        $req = $db->prepare("DELETE FROM personnel_medical WHERE id_utilisateur = ?");
        $req->execute([$id]);
        
        $req2 = $db->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
        return $req2->execute([$id]);
    }
}
?>