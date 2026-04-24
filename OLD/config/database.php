<?php
// =============================================================
//  config/database.php
//  COUCHE CONFIGURATION — Paramètres de connexion à la base
//  BTS SIO SLAM — Séquence MVC
// =============================================================

/**
 * Retourne une connexion PDO à la base de données.
 * PDO est utilisé pour :
 *   - Sécuriser les requêtes (requêtes préparées)
 *   - Être indépendant du SGBD (MySQL, SQLite, PostgreSQL…)
 *   - Gérer les erreurs proprement (exceptions)
 *
 * @return PDO
 */
function getConnexion(): PDO
{
    // ── Paramètres de connexion ────────────────────────────────
    $host   = 'localhost';
    $dbname = 'meditimmy';
    $user   = 'timmy';
    $pass   = 'Shaun123!';          // Adapter selon votre installation WAMP/XAMPP
    $charset = 'utf8mb4';
    // ──────────────────────────────────────────────────────────

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lève une exception en cas d'erreur SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Résultats sous forme de tableaux associatifs
        PDO::ATTR_EMULATE_PREPARES   => false,                   // Vraies requêtes préparées (sécurité)
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // En production : logger l'erreur, ne pas afficher les détails
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }
}
