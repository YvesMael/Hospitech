<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
require_once '../config.php'; 

try {
    // La magie du LEFT JOIN : on récupère tout le monde et on regarde ce qui correspond
    $query = "SELECT 
                u.id_utilisateur, 
                u.nom, 
                u.prenom, 
                u.adresse_mail, 
                s.nom_service,
                t.titre,
                -- Cette condition détermine dynamiquement le rôle de la personne
                CASE 
                    WHEN t.id_utilisateur IS NOT NULL THEN 'Technicien'
                    WHEN p.id_utilisateur IS NOT NULL THEN 'Personnel Médical'
                    ELSE 'Utilisateur Non Assigné'
                END as type_profil
              FROM Utilisateur u
              LEFT JOIN Service s ON u.id_service = s.id_service
              LEFT JOIN Technicien t ON u.id_utilisateur = t.id_utilisateur
              LEFT JOIN Personnel_medical p ON u.id_utilisateur = p.id_utilisateur
              ORDER BY type_profil, u.nom";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success", 
        "total" => count($utilisateurs),
        "data" => $utilisateurs
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
}
?>