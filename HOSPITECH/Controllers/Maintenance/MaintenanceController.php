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

<?php
// On accepte uniquement le format JSON et la méthode POST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// On remonte d'un dossier pour trouver config.php
require_once '../config.php'; 

// Récupération des données envoyées par Postman ou le front-end
$data = json_decode(file_get_contents("php://input"));


// Vérification : le nom du service est obligatoire
if (!empty($data)) {
    try {
        $query = "INSERT INTO Maintenance (date_heure, diagnostic, actions_effectuees, date_remise_service, num_equip_ref, id_technicien) VALUES (:date_heure, :diagnostic, :actions_effectuees, :date_remise_service, :num_equip_ref, :id_technicien)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            :date_heure, 
            :diagnostic, :actions_effectuees, :date_remise_service, :num_equip_ref, :id_technicien
            ':nom' => $data->nom_service
            ]);
        
        $nouvel_id = $pdo->lastInsertId(); // Récupération de l'ID généré
        
        http_response_code(201); // 201 = Created
        echo json_encode([
            "status" => "success", 
            "message" => "Service ajouté avec succès", 
            "id_service" => $nouvel_id
        ]);
    } catch (\PDOException $e) {
        http_response_code(400); // 400 = Bad Request
        echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Le champ 'nom_service' est obligatoire."]);
}
?>