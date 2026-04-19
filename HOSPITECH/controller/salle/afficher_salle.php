<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

require_once '../config.php'; 

try {
    // Jointure pour avoir le nom du service associé à la salle
    $query = "SELECT s.num_salle, s.nom_salle, serv.nom_service, s.id_service 
              FROM Salle s
              JOIN Service serv ON s.id_service = serv.id_service
              ORDER BY serv.nom_service, s.nom_salle";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $salles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "total" => count($salles),
        "data" => $salles
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
}
?>