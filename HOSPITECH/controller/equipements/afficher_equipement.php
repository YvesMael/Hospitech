<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

require_once '../config.php'; 

try {
    // La jointure pour récupérer des noms lisibles au lieu des identifiants numériques
    $query = "SELECT 
                e.code_equip, 
                e.marque, 
                e.modele, 
                e.etat_equip, 
                e.date_prochaine_maintenance,
                c.nom_categorie, 
                s.nom_salle,
                serv.nom_service
              FROM Equipement e
              LEFT JOIN Categorie c ON e.id_categorie = c.num_categorie
              LEFT JOIN Salle s ON e.num_salle = s.num_salle
              LEFT JOIN Service serv ON s.id_service = serv.id_service
              ORDER BY e.date_prochaine_maintenance ASC"; 
              // On trie pour voir les maintenances les plus urgentes en premier

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success", 
        "total" => count($equipements),
        "data" => $equipements
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
}
?>