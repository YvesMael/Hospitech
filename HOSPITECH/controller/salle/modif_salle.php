<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->num_salle) && !empty($data->nom_salle) && !empty($data->id_service)) {
    try {
        $query = "UPDATE Salle SET nom_salle = :nom, id_service = :serv WHERE num_salle = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':nom'  => $data->nom_salle,
            ':serv' => $data->id_service,
            ':id'   => $data->num_salle
        ]);
        
        echo json_encode(["status" => "success", "message" => "Salle mise à jour"]);
    } catch (\PDOException $e) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Données incomplètes pour la salle."]);
}
?>