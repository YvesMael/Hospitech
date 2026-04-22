<?php

class ServiceController {
    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['nom_service']) && !empty($data['id_hopital'])) {
            try {
                // Appel statique direct sans instancier "new Service()"
                $success = Service::create($data["nom_service"], $data["id_hopital"]);

                if ($success) {
                    http_response_code(201);
                    // On utilise la variable $data directement pour le message
                    echo json_encode([
                        "status" => "success", 
                        "message" => "Le service : " . $data['nom_service'] . " a été ajouté"
                    ]);
                } else {
                    throw new Exception("Erreur lors de l'insertion en base de données.");
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Veuillez fournir toutes les infos"]);
        }
    }
}
?>