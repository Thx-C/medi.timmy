<?php
// =============================================================
//  index.php — POINT D'ENTRÉE UNIQUE (Front Controller)
//  BTS SIO SLAM — Séquence MVC
// =============================================================
//
//  ┌─────────────────────────────────────────────────────────┐
//  │  PRINCIPE DU FRONT CONTROLLER                           │
//  │  Toutes les requêtes passent par ce fichier.            │
//  │  Le paramètre GET 'action' détermine ce qui s'exécute.  │
//  │                                                         │
//  │  Exemples :                                             │
//  │    index.php                  → action = 'dashboard'    │
//  │    index.php?action=produits  → liste des produits      │
//  │    index.php?action=create    → formulaire de création  │
//  │    index.php?action=edit&id=3 → formulaire édition      │
//  └─────────────────────────────────────────────────────────┘

use controllers\ProduitController;

session_start();

require_once __DIR__ . '/controllers/ProduitController.php';
require_once __DIR__ . '/controllers/DashboardController.php';

// ── Routage simple basé sur le paramètre ?action= ──────────
$action     = $_GET['action'] ?? 'index';
$methode    = $_SERVER['REQUEST_METHOD'];

$produitController   = new ProduitController();
$dashboardController = new DashboardController();

// Table de routage : action → [méthode HTTP attendue, méthode du contrôleur]
// Les actions 'store', 'update', 'delete' exigent POST
$routes = [
    'index'     => ['GET',  [$dashboardController, 'index']],
    'dashboard' => ['GET',  [$dashboardController, 'index']],

    'produits'  => ['GET',  [$produitController,   'index']],
    'create'    => ['GET',  [$produitController,   'create']],
    'store'     => ['POST', [$produitController,   'store']],
    'edit'      => ['GET',  [$produitController,   'edit']],
    'update'    => ['POST', [$produitController,   'update']],
    'delete'    => ['POST', [$produitController,   'delete']],
    'show'      => ['GET',  [$produitController,   'show']],
];

if (isset($routes[$action])) {
    [$methodeAttendue, $callable] = $routes[$action];

    // Sécurité : on vérifie que la méthode HTTP est correcte
    if ($methode !== $methodeAttendue) {
        http_response_code(405);
        die('Méthode HTTP non autorisée pour cette action.');
    }

    // Appel dynamique de la méthode du contrôleur
    [$controller, $nomMethode] = $callable;
    $controller->$nomMethode();
} else {
    // Action inconnue → page index par défaut
    $dashboardController->index();
}