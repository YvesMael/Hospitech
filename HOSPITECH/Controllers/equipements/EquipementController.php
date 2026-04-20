<?php
//require_once '../Model/Equipement.php';

class EquipementController {
    private $model;

    public function __construct() {
        $this->model = new Equipement();
    }

    public function getAll() {
        $equipements = $this->model->getAllWithDetails();
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $equipements]);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Vérification des champs obligatoires (id_hopital inclus)
        if (!empty($data['code_equip']) && !empty($data['num_equip']) && !empty($data['id_hopital']) && !empty($data['date_ajout'])) {
            try {
                $this->model->create($data);
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Équipement ajouté avec succès."]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Données obligatoires manquantes (code_equip, num_equip, id_hopital, date_ajout)."]);
        }
    }

    public function update() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!empty($data['code_equip'])) {
            try {
                $success = $this->model->update($data);
                if ($success) {
                    http_response_code(200);
                    echo json_encode(["status" => "success", "message" => "Équipement mis à jour. La date d'ajout a été préservée."]);
                } else {
                    http_response_code(200);
                    echo json_encode(["status" => "warning", "message" => "Aucune modification apportée (données identiques ou code introuvable)."]);
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Le champ 'code_equip' est requis pour la modification."]);
        }
    }
}
?>