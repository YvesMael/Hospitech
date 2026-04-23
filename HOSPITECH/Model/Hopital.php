<?php
//require_once '../config.php';

class Hopital{
    private static $pdo;

    public function __construct() {
        try {
            // Suppression de charset=utf8mb4 (non supporté par le driver pgsql dans le DSN)
            $dsn = "pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME;
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Forcer l'UTF8 si nécessaire
            self::$pdo->exec("SET NAMES 'UTF8'");
        } catch (\PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getAll() {
        return $this->pdo->query("SELECT * FROM hopital ORDER BY nom_hopital ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = self::$pdo->prepare("INSERT INTO hopital (nom_hopital, adresse, telephone) VALUES (:nom, :adresse, :tel)");
        $stmt->execute([
            ':nom'     => $data['nom_hopital'],
            ':adresse' => $data['adresse'] ?? null,
            ':tel'     => $data['telephone'] ?? null
        ]);
        return self::$pdo->lastInsertId();
    }
   // Dans Model/Hopital.php (Fonction UPDATE)
    public function update($data) {
    $query = "UPDATE hopital 
              SET nom_hopital = :nom_hopital, 
                  adresse = :adresse, 
                  telephone = :telephone 
              WHERE id_hopital = :id_hopital";

    $stmt = $this->pdo->prepare($query);
    
    return $stmt->execute([
        ':nom_hopital' => $data->nom_hopital,
        ':adresse'     => $data->adresse,
        ':telephone'   => $data->telephone,
        ':id_hopital'  => $data->id_hopital
    ]);
}
}
?>