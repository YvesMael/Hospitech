<?php
abstract class Maintenance {
    // PROTECTED : Les classes filles y ont accès, mais pas l'extérieur
    protected $pdo; 

    // Méthode partagée par tous les enfants pour créer la partie "Mère" en base
    protected function createMere($data) {
        $query = "INSERT INTO Maintenance (date_heure, diagnostic, actions_effectuees, date_remise_service, num_equip_ref, id_technicien) 
                  VALUES (:date_h, :diag, :actions, :date_remise, :equip, :tech)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':date_h'      => $data['date_heure'],
            ':diag'        => $data['diagnostic'] ?? null,
            ':actions'     => $data['actions_effectuees'] ?? null,
            ':date_remise' => $data['date_remise_service'] ?? null,
            ':equip'       => $data['num_equip_ref'],
            ':tech'        => $data['id_technicien']
        ]);
        return $this->pdo->lastInsertId();
    }
    // Dans Model/Maintenance.php (Fonction UPDATE de base)
public function update($data) {
    $query = "UPDATE Maintenance 
              SET date_heure = :date_heure,
                  diagnostic = :diagnostic, 
                  actions_effectuees = :actions_effectuees, 
                  date_remise_service = :date_remise_service,
                  num_equip_ref = :num_equip_ref,
                  id_technicien = :id_technicien
              WHERE num_maintenance = :num_maintenance";

    $stmt = $this->pdo->prepare($query);
    
    return $stmt->execute([
        ':date_heure'          => $data->date_heure,
        ':diagnostic'          => $data->diagnostic,
        ':actions_effectuees'  => $data->actions_effectuees,
        ':date_remise_service' => $data->date_remise_service,
        ':num_equip_ref'       => $data->num_equip_ref,
        ':id_technicien'       => $data->id_technicien,
        ':num_maintenance'     => $data->num_maintenance
    ]);
}
}
?>