<?php

require_once 'Utilisateur.php';

class Personnel {
    private $id_utilisateur;
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
     * Créer un personnel medical (après avoir créé l'utilisateur)
     */
    public static function create($nom, $prenom, $password, $email, $id_service) {
        $db = self::getConnexion();
        try {
            $db->beginTransaction();
            $id = Utilisateur::create($nom, $prenom, $password, $email, $id_service);
            if($id){
                // 2. Insérer dans la table technicien
                $req = $db->prepare("INSERT INTO personnel_medical (id_utilisateur) VALUES (?)");
                $req->execute([$id]);
            }
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Trouver un personnel medical par son mail
     */
    public static function findByEmail($email) {
        $db = self::getConnexion();
        // On récupère les infos de l'utilisateur ET du personnel_medical en une fois
        $sql = "SELECT u.* 
                FROM utilisateur u 
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
                JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
                ORDER BY u.nom ASC";
        
        $req = $db->query($sql);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $db = self::getConnexion();
        // Attention : supprimer dans personnel_medical d'abord, puis utilisateur (contraintes d'intégrité)
        $req = $db->prepare("DELETE FROM personnel_medical WHERE id_utilisateur = ?");
        $req->execute([$id]);
        
        $req2 = $db->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
        return $req2->execute([$id]);
    }
}
?>