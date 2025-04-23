<?php
require 'vendor/autoload.php';

use App\Controllers\CharacterController;

echo "<h1>Jeu de rôle</h1>";
    $controller = new CharacterController();
    $action = $_GET['action'] ?? 'attack';
try {
    echo $controller->handleAction($action);
} catch (Throwable $e) {
    echo "Erreur : " . $e->getMessage();
}
