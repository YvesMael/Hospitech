<?php
    class SalleController{
        public function create(){
            $data = json_decode(file_get_contents("php://input"), true);
            if(!empty($data['nom_salle']) && !empty($data['id_service'])){
                try{
                    $salle = new Salle();
                    $LaSalle = $salle->create($data);
                    http_response_code(201);
                    echo json_encode(["status" => "success", "message" => "Salle : $LaSalle->nom_salle a ete ajoutee"]);
                } catch(Exception $e){
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            }
            else{
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Veuillez fournir toutes les infos"]);
            }
        }


    }

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    require_once '../config.php';
    $data = json_decode(file_get_contents("php://input"), true);
?>