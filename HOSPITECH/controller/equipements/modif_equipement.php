<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../config.php'; 

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->code_equip)) {
    try {
        $query = "UPDATE Equipement SET 
                    marque = :marque, 
                    modele = :modele, 
                    etat_equip = :etat, 
                    freq_jours = :fj, 
                    freq_mois = :fm, 
                    freq_ans = :fa, 
                    num_salle = :salle, 
                    id_categorie = :cat 
                  WHERE code_equip = :code";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':marque' => $data->marque,
            ':modele' => $data->modele,
            ':etat'   => $data->etat_equip,
            ':fj'     => $data->freq_jours,
            ':fm'     => $data->freq_mois,
            ':fa'     => $data->freq_ans,
            ':salle'  => $data->num_salle,
            ':cat'    => $data->id_categorie,
            ':code'   => $data->code_equip
        ]);

        echo json_encode(["status" => "success", "message" => "Équipement mis à jour"]);
    } catch (\PDOException $e) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Le code de l'équipement est requis."]);
}
?>