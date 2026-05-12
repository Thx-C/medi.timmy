<?php
// models/PatientModel.php

class PatientModel {
    private PDO $pdo;
    public function __construct() { $this->pdo = getPDO(); }

    public function getAll(): array {
        return $this->pdo->query("SELECT p.*, u.username, u.actif FROM patients p LEFT JOIN users u ON u.id=p.user_id ORDER BY p.nom, p.prenom")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT p.*, u.username, u.email as user_email FROM patients p LEFT JOIN users u ON u.id=p.user_id WHERE p.id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE user_id=?");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function search(string $q): array {
        $like = '%' . $q . '%';
        $stmt = $this->pdo->prepare("SELECT p.*, u.username FROM patients p LEFT JOIN users u ON u.id=p.user_id WHERE p.nom LIKE ? OR p.prenom LIKE ? OR p.email LIKE ? ORDER BY p.nom LIMIT 30");
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

   /**
 * Insère un nouveau patient dans la base de données
 * et retourne son ID généré automatiquement.
 *
 * @param array $data  Tableau associatif contenant les infos du patient
 * @return int         L'ID du patient nouvellement créé
 */
public function create(array $data): int {

    // Prépare la requête SQL avec des "?" comme marqueurs de position
    // (évite les injections SQL)
    $stmt = $this->pdo->prepare("
        INSERT INTO patients 
            (user_id, nom, prenom, date_naissance, email, telephone, adresse) 
        VALUES 
            (?,?,?,?,?,?,?)
    ");

    // Exécute la requête en remplaçant chaque "?" par la valeur correspondante
    // "?? null" = si la clé n'existe pas dans $data, on insère NULL en base
    $stmt->execute([
        $data['user_id']        ?? null,  // Lié au compte utilisateur (optionnel)
        $data['nom'],                     // Nom de famille (obligatoire)
        $data['prenom'],                  // Prénom (obligatoire)
        $data['date_naissance'] ?? null,  // Date de naissance (optionnelle)
        $data['email']          ?? null,  // Email (optionnel)
        $data['telephone']      ?? null,  // Téléphone (optionnel)
        $data['adresse']        ?? null,  // Adresse postale (optionnelle)
    ]);

    // Récupère l'ID auto-incrémenté généré par le INSERT
    // et le cast en int (lastInsertId() retourne une string par défaut)
    return (int)$this->pdo->lastInsertId();
}

    public function update(int $id, array $data): void {
        $stmt = $this->pdo->prepare("UPDATE patients SET nom=?, prenom=?, date_naissance=?, email=?, telephone=?, adresse=? WHERE id=?");
        $stmt->execute([$data['nom'], $data['prenom'], $data['date_naissance'] ?? null, $data['email'] ?? null, $data['telephone'] ?? null, $data['adresse'] ?? null, $id]);
    }

    public function linkUser(int $patientId, int $userId): void {
        $this->pdo->prepare("UPDATE patients SET user_id=? WHERE id=?")->execute([$userId, $patientId]);
    }
}
