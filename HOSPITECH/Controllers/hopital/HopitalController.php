<?php
//require_once '../Model/Hopital.php';

class HopitalController {
    private $model;

    public function __construct() {
        $this->model = new Hopital();
    }

    public function getAll() {
        $hopitaux = $this->model->getAll();
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $hopitaux]);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['nom_hopital'])) {
            try {
                $id = $this->model->create($data);
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Hôpital créé avec succès.", "id_hopital" => $id]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Le nom de l'hôpital est requis."]);
        }
    }

    public function update() {
        // 1. Récupération des données Front-end ou Postman
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        // 2. Sécurité : on exige l'id_hopital
        if (!empty($data) && isset($data->id_hopital)) {
            try {
                // 3. Appel au modèle
                $this->model->update($data);
                
                http_response_code(200);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Hôpital mis à jour avec succès."
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Erreur lors de la mise à jour de l'hôpital : " . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error", 
                "message" => "L'identifiant de l'hôpital (id_hopital) est strictement requis."
            ]);
        }
    }
}
?>