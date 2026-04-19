<?php
// On accepte uniquement le format JSON et la méthode POST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// On remonte d'un dossier pour trouver config.php
require_once '../config.php'; 

// Récupération des données envoyées par Postman ou le front-end
$data = json_decode(file_get_contents("php://input"));

// Vérification : le nom du service est obligatoire
if (!empty($data->nom_service)) {
    try {
        $query = "INSERT INTO Service (nom_service) VALUES (:nom)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':nom' => $data->nom_service]);
        
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