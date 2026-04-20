<?php
// Le Contrôleur inclut son Modèle
//require_once '../Model/Categorie.php';

class CategorieController {
    private $model;

    public function __construct() {
        // Il instancie le modèle tout seul comme un grand !
       $this->model = new Categorie();
    }

    public function getAll() {
        $categories = $this->model->getAll();
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $categories]);
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
}
?>