<?php

// On suppose que Utilisateur est dans le même dossier ou inclus
require_once 'Utilisateur.php';

class Technicien {
    private $id_utilisateur;
    private $titre;
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
     * CREATE : Création avec Transaction
     * On crée l'utilisateur d'abord, puis le technicien.
     */
    public static function create($nom, $prenom, $password, $email, $id_service, $titre) {
        $db = self::getConnexion();
        try {
            $db->beginTransaction();
            $id = Utilisateur::create($nom, $prenom, $password, $email, $id_service);
            if($id){
                // 2. Insérer dans la table technicien
                $req = $db->prepare("INSERT INTO technicien (id_utilisateur, titre) VALUES (?, ?)");
                $req->execute([$id, $titre]);
            }
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    /**
     * FIND BY EMAIL : Utilise la jointure
     * Cette méthode est déjà très bien dans ton code initial.
     */
    public static function findByEmail($email) {
        $db = self::getConnexion();
        $sql = "SELECT u.*, t.titre 
                FROM utilisateur u 
                JOIN technicien t ON u.id_utilisateur = t.id_utilisateur 
                WHERE u.adresse_mail = ?";
        
        $req = $db->prepare($sql);
        $req->execute([$email]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * FIND ALL : Liste complète
     */
    public static function findAll() {
        $db = self::getConnexion();
        $sql = "SELECT u.nom, u.prenom, u.adresse_mail, t.titre, t.id_utilisateur 
                FROM technicien t
                JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
                ORDER BY u.nom ASC";
        
        $req = $db->query($sql);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * DELETE : Suppression sécurisée
     * Si tu as mis "ON DELETE CASCADE" en SQL, tu n'as besoin que d'une ligne.
     * Sinon, on garde la double suppression.
     */
    public static function delete($id) {
        $db = self::getConnexion();
        try {
            $db->beginTransaction();
            
            // Supprimer l'enfant d'abord
            $req1 = $db->prepare("DELETE FROM technicien WHERE id_utilisateur = ?");
            $req1->execute([$id]);
            
            // Supprimer le parent ensuite
            $req2 = $db->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
            $req2->execute([$id]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}