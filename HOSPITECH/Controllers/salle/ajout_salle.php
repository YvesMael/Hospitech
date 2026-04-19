<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nom_salle) && !empty($data->id_service)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO Salle (nom_salle, id_service) VALUES (:nom, :id_serv)");
        $stmt->execute([
            ':nom' => $data->nom_salle,
            ':id_serv' => $data->id_service
        ]);
        
        $nouvel_id = $pdo->lastInsertId(); // Récupère le num_salle généré
        
        http_response_code(201);
        echo json_encode([
            "status" => "success", 
            "message" => "Salle ajoutée", 
            "num_salle" => $nouvel_id
        ]);
    } catch (\PDOException $e) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Le nom de la salle et l'ID du service sont requis."]);
}
?>