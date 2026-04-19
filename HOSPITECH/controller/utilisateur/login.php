<?php
// En-têtes pour accepter le JSON en méthode POST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

// On a besoin de l'email et du mot de passe pour connecter quelqu'un
if (!empty($data->adresse_mail) && !empty($data->password)) {
    try {
        // La requête "Détective" : on cherche l'utilisateur et on regarde s'il existe dans les tables filles
        $query = "SELECT 
                    u.id_utilisateur, 
                    u.password, 
                    u.nom, 
                    u.prenom,
                    t.id_utilisateur AS est_technicien,
                    p.id_utilisateur AS est_medical
                  FROM Utilisateur u
                  LEFT JOIN Technicien t ON u.id_utilisateur = t.id_utilisateur
                  LEFT JOIN Personnel_medical p ON u.id_utilisateur = p.id_utilisateur
                  WHERE u.adresse_mail = :mail";

        $stmt = $pdo->prepare($query);
        $stmt->execute([':mail' => $data->adresse_mail]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si l'utilisateur existe ET que le mot de passe correspond au hash enregistré
        if ($user && password_verify($data->password, $user['password'])) {
            
            // On déduit le rôle en regardant quelle jointure a fonctionné
            $role_detecte = "Utilisateur simple";
            if ($user['est_technicien'] !== null) {
                $role_detecte = "technicien";
            } elseif ($user['est_medical'] !== null) {
                $role_detecte = "medical";
            }

            http_response_code(200); // 200 = OK
            echo json_encode([
                "status" => "success", 
                "message" => "Connexion réussie.",
                "utilisateur" => [
                    "id" => $user['id_utilisateur'],
                    "nom" => $user['nom'],
                    "prenom" => $user['prenom'],
                    "role" => $role_detecte
                ]
            ]);

        } else {
            // Si l'email n'existe pas ou que le mot de passe est faux
            // Par sécurité, on donne toujours le même message pour ne pas aider les pirates
            http_response_code(401); // 401 = Unauthorized
            echo json_encode(["status" => "error", "message" => "Adresse e-mail ou mot de passe incorrect."]);
        }

    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "L'adresse e-mail et le mot de passe sont requis."]);
}
?>