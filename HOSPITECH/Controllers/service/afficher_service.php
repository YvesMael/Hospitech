<?php
// On accepte uniquement la méthode GET
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

require_once '../config.php'; 

try {
    // Requête pour tout sélectionner
    $query = "SELECT * FROM Service ORDER BY nom_service ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // fetchAll() récupère toutes les lignes sous forme de tableau
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200); // 200 = OK
    echo json_encode([
        "status" => "success", 
        "total" => count($services),
        "data" => $services
    ]);
} catch (\PDOException $e) {
    http_response_code(500); // 500 = Erreur interne du serveur
    echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
}
?>