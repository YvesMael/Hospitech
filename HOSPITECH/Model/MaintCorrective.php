<?php
// On inclut les identifiants et la classe mère abstraite
//require_once '../config.php';
require_once 'Maintenance.php'; 

class MaintCorrective extends Maintenance {

    // 1. L'enfant gère sa propre connexion
    public function __construct() {
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

    // 2. La méthode qui gère la transaction complète (Mère + Fille)
    public function createComplete($data) {
        try {
            // Début de la transaction
            $this->pdo->beginTransaction();

            // A. On appelle la fonction de la classe Mère (MaintenanceModel)
            // Elle insère la date, le diagnostic, etc., et nous renvoie l'ID généré
            $num_maintenance = $this->createMere($data);

            // B. On insère les données spécifiques à la PANNE dans la table Fille
            $query = "INSERT INTO maint_corrective (num_maintenance, date_apparit_panne, description_panne, id_personnel_medical, statut_maint) 
                      VALUES (:num, :date_panne, :desc, :id_perso, :statut)";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':num'        => $num_maintenance, // L'ID hérité de la mère
                ':date_panne' => $data['date_apparit_panne'] ?? null,
                ':desc'       => $data['description_panne'] ?? null,
                ':id_perso'   => $data['id_personnel_medical'] ?? null,
                ':statut'     => $data['statut_maint'] ?? 'non réalisée'
            ]);

            // Si on arrive ici sans erreur, on valide tout dans la base de données !
            $this->pdo->commit();
            return $num_maintenance;

        } catch (Exception $e) {
            // En cas d'erreur (problème de clé étrangère, champ manquant...), on annule tout
            $this->pdo->rollBack();
            throw new Exception("Erreur Maintenance Corrective : " . $e->getMessage());
        }
    }
}
?>