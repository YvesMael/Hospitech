<?php

class Utilisateur {
    private $id_utilisateur;
    private $nom;
    private $prenom;
    private $password;
    private $adresse_mail;
    private $id_service;
    private static $pdo = null; 

    // J'utilise ceci dans les select, pour ne pas lister a chaq fois si je souhaite exclure un champ
    const COLUMNS = "id_utilisateur, nom, prenom, adresse_mail, id_service";

    private static function getConnexion() {
        // Si la connexion n'existe pas encore, on la crée
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
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
        
        $req = $db->prepare("INSERT INTO utilisateur (nom, prenom, password, adresse_mail, id_service) VALUES (?, ?, ?, ?, ?)");
        $etat = $req->execute([$nom, $prenom, $hash, $email, $id_service]);
        if($etat){
            return $db->lastInsertId();
        }
        else{return 0;}
    }

    /**
     * Trouver un utilisateur par son mail (Utile pour la connexion/Login)
     */
    public static function findByEmail($email) {
        $db = self::getConnexion();
        $req = $db->prepare("SELECT * FROM utilisateur WHERE adresse_mail = ?");
        $req->execute([$email]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les utilisateur (READ)
     */
    public static function findAll() {
        $db = self::getConnexion();
        $req = $db->query("SELECT ". self::COLUMNS ." FROM utilisateur ORDER BY nom ASC");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprimer un utilisateur (DELETE)
     */
    public static function delete($id) {
        $db = self::getConnexion();
        $req = $db->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
        return $req->execute([$id]);
    }

    public static function findWithRole($email) {
    $db = self::getConnexion();
    $sql = "SELECT u.id_utilisateur, u.password, u.nom, u.prenom, 
                   t.id_utilisateur AS est_technicien, 
                   p.id_utilisateur AS est_medical
            FROM utilisateur u
            LEFT JOIN Technicien t ON u.id_utilisateur = t.id_utilisateur
            LEFT JOIN Personnel_medical p ON u.id_utilisateur = p.id_utilisateur
            WHERE u.adresse_mail = ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}