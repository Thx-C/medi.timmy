<?php
// =============================================================
//  models/Produit.php
//  COUCHE MODEL — Accès aux données, logique métier
//  BTS SIO SLAM — Séquence MVC
// =============================================================
//
//  ┌─────────────────────────────────────────────────────────┐
//  │  RÔLE DU MODEL                                          │
//  │  • Communiquer avec la base de données (SQL)            │
//  │  • Encapsuler la logique métier (validation, calculs…)  │
//  │  • NE PAS connaître l'affichage (HTML) ni les URL (GET) │
//  └─────────────────────────────────────────────────────────┘

namespace models;
require_once __DIR__ . '/../config/database.php';

class Produit
{
    private PDO $db;

    public function __construct()
    {
        // Le Model obtient sa propre connexion PDO
        $this->db = getConnexion();
    }

    // ─────────────────────────────────────────────────────────
    //  READ — Lecture des données
    // ─────────────────────────────────────────────────────────

    /**
     * Retourne tous les produits avec leur catégorie.
     * La jointure LEFT JOIN conserve les produits sans catégorie.
     */
    public function findAll(): array
    {
        $sql = "SELECT p.*, c.nom AS categorie_nom, c.couleur AS categorie_couleur
                FROM produits p
                LEFT JOIN categories c ON p.id_categorie = c.id
                ORDER BY p.nom ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Retourne un produit par son identifiant.
     * Utilise une requête préparée pour éviter les injections SQL.
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT p.*, c.nom AS categorie_nom
                 FROM produits p
                 LEFT JOIN categories c ON p.id_categorie = c.id
                 WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Recherche des produits par mot-clé (nom ou description).
     * LIKE avec % de chaque côté → recherche "contient".
     */
    public function search(string $terme): array
    {
        $sql = "SELECT p.*, c.nom AS categorie_nom, c.couleur AS categorie_couleur
                 FROM produits p
                 LEFT JOIN categories c ON p.id_categorie = c.id
                 WHERE p.nom LIKE :terme OR p.description LIKE :terme
                 ORDER BY p.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':terme' => '%' . $terme . '%']);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    //  CREATE — Insertion
    // ─────────────────────────────────────────────────────────

    /**
     * Insère un nouveau produit.
     * Retourne l'ID auto-incrémenté du produit créé.
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO produits (nom, description, prix, stock, id_categorie)
                VALUES (:nom, :description, :prix, :stock, :id_categorie)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':description' => $data['description'] ?? null,
            ':prix' => $data['prix'],
            ':stock' => $data['stock'],
            ':id_categorie' => $data['id_categorie'] ?: null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────
    //  UPDATE — Modification
    // ─────────────────────────────────────────────────────────

    /**
     * Met à jour un produit existant.
     * Retourne true si au moins une ligne a été modifiée.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE produits
                SET nom          = :nom,
                    description  = :description,
                    prix         = :prix,
                    stock        = :stock,
                    id_categorie = :id_categorie
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nom' => $data['nom'],
            ':description' => $data['description'] ?? null,
            ':prix' => $data['prix'],
            ':stock' => $data['stock'],
            ':id_categorie' => $data['id_categorie'] ?: null,
        ]);

        return $stmt->rowCount() > 0;
    }

    // ─────────────────────────────────────────────────────────
    //  DELETE — Suppression
    // ─────────────────────────────────────────────────────────

    /**
     * Supprime un produit par son ID.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM produits WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // ─────────────────────────────────────────────────────────
    //  VALIDATION — Logique métier
    // ─────────────────────────────────────────────────────────

    /**
     * Valide les données avant insertion / mise à jour.
     * Retourne un tableau d'erreurs (vide = données valides).
     */
    public function valider(array $data): array
    {
        $erreurs = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $erreurs[] = 'Le nom du produit est obligatoire.';
        }

        if (!is_numeric($data['prix'] ?? '') || $data['prix'] < 0) {
            $erreurs[] = 'Le prix doit être un nombre positif.';
        }

        if (!is_numeric($data['stock'] ?? '') || $data['stock'] < 0 || !ctype_digit((string)$data['stock'])) {
            $erreurs[] = 'Le stock doit être un entier positif ou nul.';
        }

        return $erreurs;
    }
}
