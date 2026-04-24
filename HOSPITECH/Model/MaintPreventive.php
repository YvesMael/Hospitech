<?php

class MaintPreventive extends Maintenance {

    public function createComplete($data) {
        $db = self::getConnexion();
        
        try {
            // On démarre la transaction : on bloque les écritures temporairement
            $db->beginTransaction();

            // 1. Insertion dans la mère (Maintenance)
            $queryMere = "INSERT INTO Maintenance (date_heure, diagnostic, actions_effectuees, date_remise_service, num_equip_ref, id_technicien) 
                          VALUES (:date_heure, :diagnostic, :actions_effectuees, :date_remise_service, :num_equip_ref, :id_technicien)";
            $stmtMere = $db->prepare($queryMere);
            $stmtMere->execute([
                ':date_heure'          => $data->date_heure,
                ':diagnostic'          => $data->diagnostic,
                ':actions_effectuees'  => $data->actions_effectuees,
                ':date_remise_service' => $data->date_remise_service,
                ':num_equip_ref'       => $data->num_equip_ref,
                ':id_technicien'       => $data->id_technicien
            ]);

            // On récupère l'ID généré par la mère
            $num_maintenance = $db->lastInsertId();

            // 2. Insertion dans la fille (Maint_Preventive)
            $queryFille = "INSERT INTO Maint_Preventive (num_maintenance) VALUES (:num_maintenance)";
            $stmtFille = $db->prepare($queryFille);
            $stmtFille->execute([
                ':num_maintenance' => $num_maintenance
            ]);

            // Tout s'est bien passé, on valide définitivement l'enregistrement !
            $db->commit();
            
            return $num_maintenance;

        } catch (Exception $e) {
            // Si la moindre chose plante, on annule tout
            $db->rollBack();
            throw $e; // On renvoie l'erreur au contrôleur
        }
    }

    // 💡 Astuce : Pas besoin d'écrire une fonction update() ici !
    // Puisque Maint_Preventive n'a pas de colonnes à elle, si le contrôleur fait $model->update(), 
    // PHP appellera automatiquement le update() de la classe mère Maintenance. C'est la magie de l'héritage.
}
?>