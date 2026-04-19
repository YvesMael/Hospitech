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
}
?>