<?php
// 1. Inclure la connexion
require_once('controller/config.php');

header("Content-Type: application/json");

// 2. Récupérer les données envoyées (via un formulaire ou Postman)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Aucune donnée reçue"]);
    exit;
}

try {
    // 3. Début de la transaction (sécurité héritage)
    $pdo->beginTransaction();

    // Hachage du mot de passe
    $hashedPass = password_hash($data['password'], PASSWORD_BCRYPT);

    // 4. Insertion dans UTILISATEUR
    $sqlUser = "INSERT INTO Utilisateur (id_utilisateur, nom, prenom, password, adresse_mail, id_service) 
                VALUES (:id, :nom, :prenom, :pass, :mail, :service)";
    
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute([
        ':id'      => $data['id_utilisateur'],
        ':nom'     => $data['nom'],
        ':prenom'  => $data['prenom'],
        ':pass'    => $hashedPass,
        ':mail'    => $data['adresse_mail'],
        ':service' => $data['id_service']
    ]);

    // 5. Gestion de l'Héritage (Technicien ou Médical)
    if ($data['type_utilisateur'] === 'technicien') {
        $stmtTech = $pdo->prepare("INSERT INTO Technicien (id_utilisateur, titre) VALUES (:id, :titre)");
        $stmtTech->execute([
            ':id'    => $data['id_utilisateur'],
            ':titre' => $data['titre']
        ]);
    } 
    else if ($data['type_utilisateur'] === 'medical') {
        $stmtMed = $pdo->prepare("INSERT INTO Personnel_medical (id_utilisateur) VALUES (:id)");
        $stmtMed->execute([':id' => $data['id_utilisateur']]);
    }

    // 6. Si tout est OK, on valide
    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "Utilisateur créé avec succès sur le serveur Oracle"]);

} catch (Exception $e) {
    // Si une erreur survient, on annule tout pour ne pas avoir de données incohérentes
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
}
?>