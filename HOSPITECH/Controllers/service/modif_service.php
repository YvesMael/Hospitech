<?php
// En-têtes pour l'API
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST"); // Ou PUT selon tes conventions
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

// Vérification : l'ID et le nouveau nom sont obligatoires
if (!empty($data->id_service) && !empty($data->nom_service)) {
    try {
        $query = "UPDATE Service SET nom_service = :nom WHERE id_service = :id";
        $stmt = $pdo->prepare($query);
        
        $stmt->execute([
            ':nom' => $data->nom_service,
            ':id'  => $data->id_service
        ]);
        
        // On vérifie si une ligne a réellement été modifiée
        if ($stmt->rowCount() > 0) {
            http_response_code(200); // 200 = OK
            echo json_encode(["status" => "success", "message" => "Le nom du service a été mis à jour."]);
        } else {
            // Si l'ID n'existe pas ou que le nom était déjà le même
            http_response_code(404); // 404 = Not Found
            echo json_encode(["status" => "warning", "message" => "Aucune modification effectuée. Vérifiez que l'ID existe."]);
        }
        
    } catch (\PDOException $e) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Les champs 'id_service' et 'nom_service' sont obligatoires."]);
}
?>