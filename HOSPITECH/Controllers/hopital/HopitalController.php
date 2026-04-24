<?php

class HopitalController {



    // À ajouter dans la classe HopitalController
    public function getAll() {
        try {
            $model = new Hopital();
            // On suppose que tu as une fonction findAll() dans ton modèle
            $hopitaux = $model->findAll(); 
            
            http_response_code(200);
            echo json_encode([
                "status" => "success", 
                "data" => $hopitaux
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error", 
                "message" => "Erreur lors de la récupération des hôpitaux : " . $e->getMessage()
            ]);
        }
    }
    public function create() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        if (!empty($data->nom_hopital) && !empty($data->adresse) && !empty($data->telephone)) {
            try {
                // On instancie le modèle localement à la volée
                $model = new Hopital();
                $model->create($data);
                
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Hôpital ajouté avec succès."]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Données incomplètes."]);
        }
    }

    public function update() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        if (!empty($data) && isset($data->id_hopital)) {
            try {
                // On instancie le modèle localement à la volée
                $model = new Hopital();
                $model->update($data);
                
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Hôpital mis à jour avec succès."]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "L'identifiant (id_hopital) est requis."]);
        }
    }
}
?>