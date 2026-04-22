<?php
    class SalleController{
        public function create(){
            $data = json_decode(file_get_contents("php://input"), true);
            if(!empty($data['nom_salle']) && !empty($data['id_service'])){
                try{
                    $etat = Salle::create($data['nom_salle'],$data['id_service']);
                    if($etat){
                        http_response_code(201);
                        echo json_encode(["status" => "success", "message" => "Salle : ". $data['nom_salle']. " a ete ajoutee"]);
                    }else{
                        throw new Exception("Erreur lors de l'insertion en base de données.");
                    }
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
?>