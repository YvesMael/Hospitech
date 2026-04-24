<?php

class CategorieController {

    public function getAll() {
        try {
            $model = new Categorie();
            $categories = $model->findAll();
            
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $categories]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function create() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        // Sécurité : on vérifie que $data est bien un objet JSON valide et que le nom est présent
        if (is_object($data) && !empty($data->nom_categorie)) {
            try {
                $model = new Categorie();
                $model->create($data);
                
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Catégorie ajoutée avec succès."]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Format JSON invalide ou nom de la catégorie manquant."]);
        }
    }

    public function update() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        // Sécurité : on exige l'objet JSON et num_categorie
        if (is_object($data) && !empty($data) && isset($data->num_categorie)) {
            try {
                $model = new Categorie();
                $model->update($data);
                
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Catégorie mise à jour avec succès."]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Format JSON invalide ou identifiant (num_categorie) manquant."]);
        }
    }
}
?>