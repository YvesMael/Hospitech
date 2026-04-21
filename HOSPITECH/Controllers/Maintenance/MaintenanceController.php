<?php
class MaintenanceController {

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!empty($data['date_heure']) && !empty($data['num_equip_ref']) && !empty($data['id_technicien']) && !empty($data['type_maintenance'])) {
            try {
                // Aiguillage basé sur le JSON reçu
                if ($data['type_maintenance'] === 'preventive') {
                    // On instancie UNIQUEMENT l'enfant
                    $model = new MaintPreventive();
                    $num_maintenance = $model->createComplete($data);
                    $type_msg = "préventive";

                } elseif ($data['type_maintenance'] === 'corrective') {
                    // On instancie UNIQUEMENT l'enfant
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
            echo json_encode(["status" => "error", "message" => "Données incomplètes."]);
        }
    }
}
?>