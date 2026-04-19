<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nom_categorie)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO Categorie (nom_categorie) VALUES (:nom)");
        $stmt->execute([':nom' => $data->nom_categorie]);
        
        $nouvel_id = $pdo->lastInsertId(); // Récupère le num_categorie généré
        
        http_response_code(201);
        echo json_encode([
            "status" => "success", 
            "message" => "Catégorie ajoutée", 
            "num_categorie" => $nouvel_id
        ]);
    } catch (\PDOException $e) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Le nom de la catégorie est requis."]);
}
?>