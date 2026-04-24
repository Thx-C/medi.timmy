<?php
// =============================================================
//  models/Categorie.php
//  COUCHE MODEL — Gestion des catégories
//  BTS SIO SLAM — Séquence MVC
// =============================================================

namespace models;
require_once __DIR__ . '/../config/database.php';

class Categorie
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnexion();
    }

    /**
     * Retourne toutes les catégories (pour alimenter les <select>).
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY nom ASC");
        return $stmt->fetchAll();
    }
}
