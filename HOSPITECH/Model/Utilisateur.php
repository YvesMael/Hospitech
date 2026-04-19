<?php

class Utilisateur {
    private $id_utilisateur;
    private $nom;
    private $prenom;
    private $password;
    private $adresse_mail;
    private $id_service;
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
     * Inscription d'un nouvel utilisateur (CREATE)
     */
    public static function create($nom, $prenom, $password, $email, $id_service) {
        $db = self::getConnexion();
        
        // On hache le mot de passe avant de l'envoyer en base
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $req = $db->prepare("INSERT INTO utilisateurs (nom, prenom, password, adresse_mail, id_service) VALUES (?, ?, ?, ?, ?)");
        return $req->execute([$nom, $prenom, $hash, $email, $id_service]);
    }

    /**
     * Trouver un utilisateur par son mail (Utile pour la connexion/Login)
     */
    public static function findByEmail($email) {
        $db = self::getConnexion();
        $req = $db->prepare("SELECT * FROM utilisateurs WHERE adresse_mail = ?");
        $req->execute([$email]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les utilisateurs (READ)
     */
    public static function findAll() {
        $db = self::getConnexion();
        $req = $db->query("SELECT * FROM utilisateurs ORDER BY nom ASC");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprimer un utilisateur (DELETE)
     */
    public static function delete($id) {
        $db = self::getConnexion();
        $req = $db->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
        return $req->execute([$id]);
    }
}