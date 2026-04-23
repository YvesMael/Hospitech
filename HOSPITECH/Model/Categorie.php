<?php
//require_once '../config.php';

class Categorie{
    private $pdo;

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
        $stmt = $this->pdo->query("SELECT * FROM categorie ORDER BY nom_categorie ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nom_categorie) {
        $stmt = $this->pdo->prepare("INSERT INTO categorie (nom_categorie) VALUES (:nom)");
        $stmt->execute([':nom' => $nom_categorie]);
        return $this->pdo->lastInsertId();
    }

    // Dans Model/categorie.php (Fonction UPDATE)
    public function update($data) {
    $query = "UPDATE categorie 
              SET nom_categorie = :nom_categorie
              WHERE num_categorie = :num_categorie";

    $stmt = $this->pdo->prepare($query);
    
    return $stmt->execute([
        ':nom_categorie' => $data->nom_categorie,
        ':num_categorie' => $data->num_categorie
    ]);
    }
}
?>