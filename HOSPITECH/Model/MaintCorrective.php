<?php

class MaintCorrective extends Maintenance {

    public function createComplete($data) {
        $db = self::getConnexion();
        
        try {
            $db->beginTransaction();

            // 1. Insertion dans la mère
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

            $num_maintenance = $db->lastInsertId();

            // 2. Insertion dans la fille avec ses attributs spécifiques
            $queryFille = "INSERT INTO Maint_Corrective (num_maintenance, date_apparit_panne, description_panne, id_personnel_medical, statut_maint) 
                           VALUES (:num_maintenance, :date_apparit_panne, :description_panne, :id_personnel_medical, :statut_maint)";
            $stmtFille = $db->prepare($queryFille);
            $stmtFille->execute([
                ':num_maintenance'      => $num_maintenance,
                ':date_apparit_panne'   => $data->date_apparit_panne,
                ':description_panne'    => $data->description_panne,
                ':id_personnel_medical' => $data->id_personnel_medical,
                ':statut_maint'         => $data->statut_maint
            ]);

            $db->commit();
            return $num_maintenance;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function update($data) {
        $db = self::getConnexion();
        
        try {
            $db->beginTransaction();

            // 1. On met à jour la mère en appelant la fonction du parent !
            parent::update($data);

            // 2. On met à jour les données spécifiques de la fille
            $queryFille = "UPDATE Maint_Corrective 
                           SET date_apparit_panne = :date_apparit_panne, 
                               description_panne = :description_panne, 
                               id_personnel_medical = :id_personnel_medical, 
                               statut_maint = :statut_maint 
                           WHERE num_maintenance = :num_maintenance";
            $stmtFille = $db->prepare($queryFille);
            $stmtFille->execute([
                ':date_apparit_panne'   => $data->date_apparit_panne,
                ':description_panne'    => $data->description_panne,
                ':id_personnel_medical' => $data->id_personnel_medical,
                ':statut_maint'         => $data->statut_maint,
                ':num_maintenance'      => $data->num_maintenance
            ]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
?>