<?php
// Le Contrôleur inclut son Modèle
//require_once '../Model/Categorie.php';

class CategorieController {
    private $model;

    public function __construct() {
        // Il instancie le modèle tout seul comme un grand !
       $this->model = new Categorie();
    }

    // À ajouter dans la classe CategorieController
    public function getAll() {
        try {
            $model = new Categorie();
            $categories = $model->findAll();
            
            http_response_code(200);
            echo json_encode([
                "status" => "success", 
                "data" => $categories
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error", 
                "message" => "Erreur lors de la récupération des catégories : " . $e->getMessage()
            ]);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['nom_categorie'])) {
            try {
                $id = $this->model->create($data['nom_categorie']);
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Catégorie créée", "num_categorie" => $id]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Le champ 'nom_categorie' est requis."]);
        }
    }
    public function update() {
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        // Sécurité : on exige num_categorie (selon ton SQL)
        if (!empty($data) && isset($data->num_categorie)) {
            try {
                $this->model->update($data);
                
                http_response_code(200);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Catégorie mise à jour avec succès."
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Erreur lors de la mise à jour : " . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error", 
                "message" => "L'identifiant de la catégorie (num_categorie) est strictement requis."
            ]);
        }
    }
}
?>