<?php
// On accepte uniquement la méthode GET et on renvoie du JSON
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// Connexion à la base de données
require_once '../config.php'; 

try {
    // Requête pour récupérer toutes les catégories, triées par ordre alphabétique
    $query = "SELECT num_categorie, nom_categorie FROM Categorie ORDER BY nom_categorie ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Récupération des données sous forme de tableau associatif
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Réponse de succès
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "total" => count($categories),
        "data" => $categories
    ]);
    
} catch (\PDOException $e) {
    // Gestion des erreurs SQL
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
}
?>