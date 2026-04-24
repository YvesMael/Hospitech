<?php

class Maintenance {
    protected static $pdo = null; // En "protected" pour que les classes filles puissent l'utiliser !

    protected static function getConnexion() {
        if (self::$pdo === null) {
            try {
                $dsn = "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME;
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->exec("SET NAMES 'UTF8'");
            } catch (\PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public function update($data) {
        $db = self::getConnexion();
        $query = "UPDATE Maintenance 
                  SET date_heure = :date_heure,
                      diagnostic = :diagnostic, 
                      actions_effectuees = :actions_effectuees, 
                      date_remise_service = :date_remise_service,
                      num_equip_ref = :num_equip_ref,
                      id_technicien = :id_technicien
                  WHERE num_maintenance = :num_maintenance";

        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':date_heure'          => $data->date_heure,
            ':diagnostic'          => $data->diagnostic,
            ':actions_effectuees'  => $data->actions_effectuees,
            ':date_remise_service' => $data->date_remise_service,
            ':num_equip_ref'       => $data->num_equip_ref,
            ':id_technicien'       => $data->id_technicien,
            ':num_maintenance'     => $data->num_maintenance
        ]);
    }
}
?>