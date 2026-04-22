<?php
// AUCUN require_once

class MaintenanceController {

    // On supprime le constructeur ! 
    // Le contrôleur ne doit pas deviner quelle classe instancier à l'avance.
    
    public function create() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);
        
        if (!empty($data->date_heure) && !empty($data->num_equip_ref) && !empty($data->id_technicien) && !empty($data->type_maintenance)) {
            try {
                // AIGUILLAGE
                if ($data->type_maintenance === 'preventive') {
                    $model = new MaintPreventive();
                    $num_maintenance = $model->createComplete($data);
                    $type_msg = "préventive";
                } elseif ($data->type_maintenance === 'corrective') {
                    $model = new MaintCorrective();
                    $num_maintenance = $model->createComplete($data);
                    $type_msg = "corrective";
                } else {
                    throw new Exception("Type de maintenance invalide.");
                }

                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Maintenance $type_msg enregistrée.", "num_maintenance" => $num_maintenance]);

            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Données incomplètes. date_heure, num_equip_ref, id_technicien et type_maintenance sont requis."]);
        }
    }

    public function update() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        // Sécurité : On exige l'ID ET le type pour savoir quelle fille appeler !
        if (!empty($data) && isset($data->num_maintenance) && isset($data->type_maintenance)) {
            try {
                
                // AIGUILLAGE POUR L'UPDATE
                if ($data->type_maintenance === 'preventive') {
                    $model = new MaintPreventive();
                    $model->update($data); 
                } elseif ($data->type_maintenance === 'corrective') {
                    $model = new MaintCorrective();
                    $model->update($data); 
                } else {
                    throw new Exception("Type de maintenance invalide pour la modification.");
                }
                
                http_response_code(200);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Rapport de maintenance mis à jour avec succès."
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Erreur lors de la mise à jour : " . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error", 
                "message"  => "L'identifiant (num_maintenance) ET le type (type_maintenance) sont strictement requis."
            ]);
        }
    }
}
?>