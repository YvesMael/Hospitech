<?php
// api.php
// Point d'entrée unique de l'API Hospitech

// 1. En-têtes pour autoriser les requêtes Front-end et renvoyer du JSON
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// ==========================================
// 2. L'AUTOLOADER INTELLIGENT
// ==========================================
spl_autoload_register(function ($nom_de_la_classe) {
    
    // CAS A : C'est un Contrôleur
    if (strpos($nom_de_la_classe, 'Controller') !== false) {
        // Ex: "CategorieTechnicienController" devient "categorietechnicien"
        $nom_table = str_replace('Controller', '', $nom_de_la_classe);
        $dossier = strtolower($nom_table);
        $chemin = 'Controllers/' . $dossier . '/' . $nom_de_la_classe . '.php';
        
        if (file_exists($chemin)) {
            require_once $chemin;
        }
    } 
    // CAS B : C'est un Modèle
    elseif (strpos($nom_de_la_classe, 'Model') !== false) {
        $chemin = 'Model/' . $nom_de_la_classe . '.php';
        if (file_exists($chemin)) {
            require_once $chemin;
        }
    }
});
// ==========================================


// ==========================================
// 3. LE ROUTEUR (Aiguillage des requêtes)
// ==========================================

// On récupère l'action demandée dans l'URL (ex: api.php?action=getCategories)
// ... [Ton autoloader reste exactement le même en haut] ...

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD']; // Va contenir 'GET', 'POST', 'PUT', etc.

try {
    switch ($action) {

        // ==========================================
        // ROUTES EN LECTURE (Doivent être en GET)
        // ==========================================
        case 'getEquipements':
            if ($method !== 'GET') throw new Exception("Méthode GET attendue.", 405);
            $controller = new EquipementController();
            $controller->getAll();
            break;

        case 'getCategories':
            if ($method !== 'GET') throw new Exception("Méthode GET attendue.", 405);
            $controller = new CategorieController();
            $controller->getAll();
            break;

        // ==========================================
        // ROUTES EN ÉCRITURE (Doivent être en POST)
        // ==========================================
        case 'addEquipement':
            if ($method !== 'POST') throw new Exception("Méthode POST attendue.", 405);
            $controller = new EquipementController();
            $controller->create();
            break;

        case 'addCategorie':
            if ($method !== 'POST') throw new Exception("Méthode POST attendue.", 405);
            $controller = new CategorieController();
            $controller->create();
            break;

        // ==========================================
        // ROUTES EN MODIFICATION (Doivent être en PUT ou POST)
        // ==========================================
        case 'updateEquipement':
            // Souvent, les API acceptent PUT ou POST pour les mises à jour
            if ($method !== 'PUT' && $method !== 'POST') throw new Exception("Méthode PUT ou POST attendue.", 405);
            $controller = new EquipementController();
            $controller->update();
            break;

        // ... [Tes autres routes ici] ...

        default:
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Route introuvable : '$action'."]);
            break;
    }

} catch (Exception $e) {
    // Si l'exception a un code 405, on l'utilise, sinon 500
    $code = ($e->getCode() !== 0) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>