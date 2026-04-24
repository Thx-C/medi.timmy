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

    public function create(array $data): int {
        $stmt = $this->pdo->prepare("INSERT INTO patients (user_id, nom, prenom, date_naissance, email, telephone, adresse) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['user_id'] ?? null, $data['nom'], $data['prenom'],
            $data['date_naissance'] ?? null, $data['email'] ?? null,
            $data['telephone'] ?? null, $data['adresse'] ?? null
        ]);
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
