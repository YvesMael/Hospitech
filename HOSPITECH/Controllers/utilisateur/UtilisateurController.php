<?php
// Technicien et Personnel Medical sont geres dans Utilisateur en fonction du role
    class UtilisateurController{
        public function create(){
            $data = json_decode(file_get_contents("php://input"), true);
            if(!empty($data['nom']) && !empty($data['password']) && !empty($data['id_service']) && !empty($data['role'])){
                try{
                    switch($data['role']){
                        case "technicien":
                            $state = Technicien::create($data['nom'], $data['prenom'], $data['password'],$data['email'],$data['id_service'],$data['titre']);
                            break;
                        case "personnel":
                            $state = Personnel::create($data['nom'], $data['prenom'], $data['password'],$data['email'],$data['id_service']);
                            break;
                        default:
                            http_response_code(400);
                            echo json_encode(["status"=>"echec","message"=>"veuillez preciser le role"]);
                            break;
                    }
                    if($state){
                        http_response_code(201);
                        echo json_encode(["status" => "success", "message" => "Utilisateur : ". $data['nom']. " a ete ajoutee"]);
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

        public function login() {
            $data = json_decode(file_get_contents("php://input"));

            if (!empty($data->email) && !empty($data->password)) {
                try {
                    $userData = Utilisateur::findWithRole($data->email);

                    if ($userData && password_verify($data->password, $userData['password'])) {
                        // Déduction du rôle
                        $role = "RAS";
                        if ($userData['est_technicien'] !== null) {
                            $role = "technicien";
                        } elseif ($userData['est_medical'] !== null) {
                            $role = "personnel";
                        }

                        http_response_code(200);
                        echo json_encode([
                            "status" => "success",
                            "utilisateur" => [
                                "id" => $userData['id_utilisateur'],
                                "nom" => $userData['nom'],
                                "prenom" => $userData['prenom'],
                                "role" => $role
                            ]
                        ]);
                    } else {
                        http_response_code(401);
                        echo json_encode(["status" => "error", "message" => "Identifiants incorrects."]);
                    }
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Données incomplètes."]);
            }
        }

        public function getAll(){
            try{
                $users = Utilisateur::findAll();
                if($users){
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success", 
                        "total" => count($users),
                        "data" => $users
                    ]);
                }
            }catch(\PDOException $e){
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
            }
        }
    }
?>