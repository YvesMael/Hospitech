<?php
// =========================================================================
// FRONT CONTROLLER (api.php) - Point d'entrée unique de l'API Hospitech
// =========================================================================

// 1. En-têtes pour autoriser les requêtes Front-end et renvoyer du JSON
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
// Permet de gérer les requêtes préliminaires (CORS Preflight) des navigateurs
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once 'config.php';

// =========================================================================
// 2. L'AUTOLOADER INTELLIGENT
// =========================================================================
// ==========================================
// L'AUTOLOADER RADAR (POUR LE DÉBUGGAGE)
// ==========================================
// =========================================================================
// 2. L'AUTOLOADER INTELLIGENT (Version propre / Production)
// =========================================================================
spl_autoload_register(function ($nom_de_la_classe) {
    
    // CAS A : C'est un Contrôleur
    if (strpos($nom_de_la_classe, 'Controller') !== false) {
        $nom_table = str_replace('Controller', '', $nom_de_la_classe);
        $dossier = strtolower($nom_table);
        $chemin = 'Controllers/' . $dossier . '/' . $nom_de_la_classe . '.php';
        
        if (file_exists($chemin)) {
            require_once $chemin;
        } else {
            // Si le fichier manque, on lance une exception propre
            throw new Exception("Le fichier Contrôleur est introuvable : " . $chemin);
        }
    } 
    // CAS B : C'est un Modèle
    else {
        $chemin = 'Model/' . $nom_de_la_classe . '.php';
        
        if (file_exists($chemin)) {
            require_once $chemin;
        } else {
            // Si le fichier manque, on lance une exception propre
            throw new Exception("Le fichier Modèle est introuvable : " . $chemin);
        }
    }
});
// ==========================================
// =========================================================================
// 3. LE ROUTEUR (Aiguillage sécurisé des requêtes)
// =========================================================================

// On récupère l'action demandée dans l'URL et la méthode utilisée
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {

        // ---------------------------------------------------
        // LECTURE DES DONNÉES (Méthode GET exigée)
        // ---------------------------------------------------
        case 'getEquipements':
            if ($method !== 'GET') throw new Exception("Méthode GET attendue.", 405);
            $controller = new EquipementController();
            $controller->getAll();
            break;

        case 'getHopitaux':
            if ($method !== 'GET') throw new Exception("Méthode GET attendue.", 405);
            $controller = new HopitalController();
            $controller->getAll();
            break;

        case 'getCategories':
            if ($method !== 'GET') throw new Exception("Méthode GET attendue.", 405);
            $controller = new CategorieController();
            $controller->getAll();
            break;

       /* case 'getSalles':
            if ($method !== 'GET') throw new Exception("Méthode GET attendue.", 405);
            $controller = new SalleController();
            $controller->getAll();
            break;

        case 'getServices':
            if ($method !== 'GET') throw new Exception("Méthode GET attendue.", 405);
            $controller = new ServiceController();
            $controller->getAll();
            break;*/

        // ---------------------------------------------------
        // CRÉATION DE DONNÉES (Méthode POST exigée)
        // ---------------------------------------------------
        case 'addEquipement':
            if ($method !== 'POST') throw new Exception("Méthode POST attendue.", 405);
            $controller = new EquipementController();
            $controller->create();
            break;

        case 'addHopital':
            if ($method !== 'POST') throw new Exception("Méthode POST attendue.", 405);
            $controller = new HopitalController();
            $controller->create();
            break;

        case 'addCategorie':
            if ($method !== 'POST') throw new Exception("Méthode POST attendue.", 405);
            $controller = new CategorieController();
            $controller->create();
            break;

        case 'assignerSpecialite':
            if ($method !== 'POST') throw new Exception("Méthode POST attendue.", 405);
            $controller = new CategorieTechnicienController();
            $controller->assigner();
            break;

        case 'addMaintenance':
            if ($method !== 'POST') throw new Exception("Méthode POST attendue.", 405);
            $controller = new MaintenanceController();
            $controller->create();
            break;

        // ---------------------------------------------------
        // MISE À JOUR DE DONNÉES (Méthode PUT ou POST exigée)
        // ---------------------------------------------------
        case 'updateEquipement':
            if ($method !== 'PUT' && $method !== 'POST') throw new Exception("Méthode PUT ou POST attendue.", 405);
            $controller = new EquipementController();
            $controller->update();
            break;

        // ---------------------------------------------------
        // SI L'ACTION N'EST PAS DANS LA LISTE
        // ---------------------------------------------------
        default:
            // Si $action est vide (l'utilisateur a juste tapé localhost/hospitech/api.php)
            if (empty($action)) {
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Bienvenue sur l'API Hospitech. Spécifiez une action valide."]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Route introuvable. L'action '$action' n'existe pas."]);
            }
            break;
    }

} catch (Exception $e) {
    // Gestion centralisée des erreurs
    $code = ($e->getCode() !== 0) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>