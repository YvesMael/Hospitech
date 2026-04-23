<?php
// config.php

// 1. Définition des paramètres du serveur
define('DB_HOST', getenv('DB_HOST'));

// 2. Nom de ta base de données (celle qu'on a créée dans le script SQL)
define('DB_NAME', getenv('DB_NAME'));

// 3. Identifiants MySQL 
// (Par défaut sur XAMPP/WAMP, l'utilisateur est 'root' et le mot de passe est vide)
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS')); 
define('DB_PORT', getenv('DB_PORT')); 


?>