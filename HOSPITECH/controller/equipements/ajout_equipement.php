<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));


// Les valeurs autorisées par ton ENUM
$etats_valides = ['en panne', 'en maintenance', 'en fonctionnement'];

// On récupère l'état envoyé, ou on met la valeur par défaut
$etat_recu = isset($data->etat_equip) ? $data->etat_equip : 'en fonctionnement';

// On vérifie si l'état est dans la liste
if (!in_array($etat_recu, $etats_valides)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "État invalide. Les valeurs acceptées sont : en panne, en maintenance, en fonctionnement."
    ]);
    exit; // On arrête tout de suite le script !
}

// Vérification des champs requis
if (
    !empty($data->code_equip) && 
    !empty($data->num_equip) && 
    !empty($data->date_ajout) && 
    !empty($data->num_salle) && 
    !empty($data->id_categorie)
) {
    try {
        // Récupération des fréquences (0 par défaut si non fournies)
        $f_ans   = isset($data->freq_ans) ? $data->freq_ans : 0;
        $f_mois  = isset($data->freq_mois) ? $data->freq_mois : 0;
        $f_jours = isset($data->freq_jours) ? $data->freq_jours : 0;

        // Insertion : On ne mentionne PLUS date_prochaine_maintenance, le Trigger s'en charge !
        $query = "INSERT INTO Equipement 
                  (code_equip, num_equip, marque, modele, etat_equip, date_ajout, freq_jours, freq_mois, freq_ans, num_salle, id_categorie) 
                  VALUES 
                  (:code, :num, :marque, :modele, :etat, :date_aj, :fj, :fm, :fa, :salle, :cat)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':code'    => $data->code_equip,
            ':num'     => $data->num_equip,
            ':marque'  => isset($data->marque) ? $data->marque : null,
            ':modele'  => isset($data->modele) ? $data->modele : null,
            ':etat'    => isset($data->etat_equip) ? $data->etat_equip : 'en fonctionnement',
            ':date_aj' => $data->date_ajout,
            ':fj'      => $f_jours,
            ':fm'      => $f_mois,
            ':fa'      => $f_ans,
            ':salle'   => $data->num_salle,       
            ':cat'     => $data->id_categorie     
        ]);

        http_response_code(201);
        echo json_encode([
            "status" => "success", 
            "message" => "Équipement ajouté avec succès. La date de maintenance a été calculée par la base de données."
        ]);

    } catch (\PDOException $e) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Données incomplètes pour l'équipement."]);
}
?>