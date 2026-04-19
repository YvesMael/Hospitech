<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST"); // Ou PUT
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->num_categorie) && !empty($data->nom_categorie)) {
    try {
        $query = "UPDATE Categorie SET nom_categorie = :nom WHERE num_categorie = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':nom' => $data->nom_categorie,
            ':id'  => $data->num_categorie
        ]);
        
        echo json_encode(["status" => "success", "message" => "Catégorie mise à jour"]);
    } catch (\PDOException $e) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID et nom de catégorie requis."]);
}
?>