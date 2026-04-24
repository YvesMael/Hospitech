<?php

class CategorieTechnicienController {

    public function getAll() {
        try {
            $model = new CategorieTechnicien();
            $liaisons = $model->findAll();
            
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $liaisons]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // On remet le nom métier "assigner" !
    public function assigner() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        if (!empty($data->id_categorie) && !empty($data->id_technicien)) {
            try {
                $model = new CategorieTechnicien();
                $id = $model->create($data); // Le modèle fait toujours un INSERT, donc "create"
                
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Spécialité assignée avec succès.", "id_cat_tech" => $id]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_categorie et id_technicien sont requis."]);
        }
    }

    public function update() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        if (!empty($data) && isset($data->id_cat_tech) && isset($data->id_categorie) && isset($data->id_technicien)) {
            try {
                $model = new CategorieTechnicien();
                $model->update($data);
                
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Liaison mise à jour avec succès."]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_cat_tech, id_categorie et id_technicien sont requis."]);
        }
    }
}
?>