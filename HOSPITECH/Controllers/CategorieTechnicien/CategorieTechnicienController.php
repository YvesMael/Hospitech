<?php
require_once '../Model/CategorieTechnicien.php';

class CategorieTechnicienController {
    private $model;

    public function __construct() {
        $this->model = new CategorieTechnicien();
    }

    public function assigner() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['id_categorie']) && !empty($data['id_technicien'])) {
            try {
                $this->model->create($data['id_categorie'], $data['id_technicien']);
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Spécialité assignée au technicien avec succès."]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Les champs 'id_categorie' et 'id_technicien' sont requis."]);
        }
    }
}
?>