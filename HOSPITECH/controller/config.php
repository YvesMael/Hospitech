<?php
// On force l'affichage en format JSON pour l'API
header('Content-Type: application/json; charset=utf-8');

// Identifiants par défaut de XAMPP (MySQL)
$host = "localhost";    // Ton PC
$db   = "hospitech_bd"; // Le nom de ta base de données
$user = "root";         // Utilisateur par défaut
$pass = "";             // Mot de passe vide par défaut
$charset = "utf8mb4";

// Construction du DSN pour MySQL
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options de PDO pour bien gérer les erreurs
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Tentative de connexion
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Si succès
    echo json_encode([
        "status" => "success",
        "message" => "Connexion à MySQL en local réussie avec root !",
        "base_de_donnees" => $db
    ]);

} catch (\PDOException $e) {
    // Si échec
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Impossible de se connecter à la base de données locale.",
        "erreur" => $e->getMessage()
    ]);
}
?>