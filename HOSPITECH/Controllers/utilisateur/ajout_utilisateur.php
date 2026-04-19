<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

// Vérification des champs de base (Classe Mère)
if (!empty($data->nom) && !empty($data->password) && !empty($data->role)) {
    try {
        $pdo->beginTransaction();

        // 1. Hachage du mot de passe (Sécurité vitale)
        $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

        // 2. Création de la classe Mère (Utilisateur)
        $queryUser = "INSERT INTO Utilisateur (nom, prenom, password, adresse_mail, id_service) 
                      VALUES (:nom, :prenom, :pass, :mail, :service)";
        $stmtUser = $pdo->prepare($queryUser);
        $stmtUser->execute([
            ':nom'     => $data->nom,
            ':prenom'  => isset($data->prenom) ? $data->prenom : null,
            ':pass'    => $hashed_password,
            ':mail'    => isset($data->adresse_mail) ? $data->adresse_mail : null,
            ':service' => isset($data->id_service) ? $data->id_service : null
        ]);

        // On récupère l'ID généré pour la mère
        $id_nouvel_utilisateur = $pdo->lastInsertId();

        // 3. Création de la classe Fille selon le rôle
        if ($data->role === 'technicien') {
            $queryTech = "INSERT INTO Technicien (id_utilisateur, titre) VALUES (:id, :titre)";
            $stmtTech = $pdo->prepare($queryTech);
            $stmtTech->execute([
                ':id'    => $id_nouvel_utilisateur,
                ':titre' => isset($data->titre) ? $data->titre : 'Technicien Standard'
            ]);
            $message_role = "Technicien ajouté";

        } else if ($data->role === 'medical') {
            $queryMed = "INSERT INTO Personnel_medical (id_utilisateur) VALUES (:id)";
            $stmtMed = $pdo->prepare($queryMed);
            $stmtMed->execute([':id' => $id_nouvel_utilisateur]);
            $message_role = "Personnel médical ajouté";
            
        } else {
            // Si le rôle n'est pas reconnu, on annule la création de l'utilisateur mère
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Rôle invalide. Utilisez 'technicien' ou 'medical'."]);
            exit;
        }

        // Si on arrive ici, tout s'est bien passé, on valide la transaction !
        $pdo->commit();
        
        http_response_code(201);
        echo json_encode([
            "status" => "success", 
            "message" => $message_role . " avec succès.",
            "id_utilisateur" => $id_nouvel_utilisateur
        ]);

    } catch (\PDOException $e) {
        $pdo->rollBack(); // En cas d'erreur, on efface tout
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Nom, password et rôle sont obligatoires."]);
}
?>