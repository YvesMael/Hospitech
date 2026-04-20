<?php
//require_once '../config.php';
require_once 'Maintenance.php'; // On inclut la classe mère

// La classe hérite de la classe abstraite
class MaintPreventive extends Maintenance {

    public function __construct() {
        try {
            // L'enfant crée la connexion et remplit l'attribut protected du parent
            $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Erreur Preventive : " . $e->getMessage());
        }
    }

    public function createComplete($data) {
        try {
            // 1. On démarre la transaction ici (car on est connecté)
            $this->pdo->beginTransaction();

            // 2. On appelle la méthode de la classe mère abstraite
            $num_maintenance = $this->createMere($data);

            // 3. On insère dans la table fille
            $stmt = $this->pdo->prepare("INSERT INTO Maint_Preventive (num_maintenance) VALUES (:num)");
            $stmt->execute([':num' => $num_maintenance]);

            // 4. On valide le tout !
            $this->pdo->commit();
            return $num_maintenance;

        } catch (Exception $e) {
            // En cas de problème (soit mère, soit fille), on annule tout
            $this->pdo->rollBack();
            throw new Exception("Erreur lors de la création de la maintenance préventive : " . $e->getMessage());
        }
    }
}
?>