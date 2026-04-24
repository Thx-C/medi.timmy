<?php
// models/UserModel.php

class UserModel {
    private PDO $pdo;

    public function __construct() { $this->pdo = getPDO(); }

    public function findByUsername(string $username): ?array {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nom as role_nom, r.label as role_label FROM users u JOIN roles r ON r.id=u.role_id WHERE u.username=? AND u.actif=1");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nom as role_nom, r.label as role_label FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array {
        return $this->pdo->query("SELECT u.*, r.nom as role_nom, r.label as role_label FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.nom, u.prenom")->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password_hash, nom, prenom, email, telephone, role_id, mot_de_passe_temp) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            $data['nom'], $data['prenom'],
            $data['email'] ?? null, $data['telephone'] ?? null,
            $data['role_id'],
            $data['mot_de_passe_temp'] ?? 0
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updatePassword(int $id, string $newPassword): void {
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash=?, mot_de_passe_temp=0 WHERE id=?");
        $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    public function resetPassword(int $id): string {
        $plain = bin2hex(random_bytes(5));
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash=?, mot_de_passe_temp=1 WHERE id=?");
        $stmt->execute([password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
        return $plain;
    }

    public function toggleActif(int $id): void {
        $this->pdo->prepare("UPDATE users SET actif = NOT actif WHERE id=?")->execute([$id]);
    }

    public function updateRole(int $userId, int $roleId): void {
        $this->pdo->prepare("UPDATE users SET role_id=? WHERE id=?")->execute([$roleId, $userId]);
    }

    public function usernameExists(string $username): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM users WHERE username=?");
        $stmt->execute([$username]);
        return (bool)$stmt->fetch();
    }

    /** Génère un username unique à partir de prénom.nom */
    public function generateUsername(string $prenom, string $nom): string {
        $base = strtolower(substr($this->sanitize($prenom), 0, 1) . '.' . $this->sanitize($nom));
        $username = $base;
        $i = 1;
        while ($this->usernameExists($username)) {
            $username = $base . $i;
            $i++;
        }
        return $username;
    }

    private function sanitize(string $s): string {
        return preg_replace('/[^a-z0-9]/', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $s)));
    }

    public function getPraticiens(): array {
        return $this->pdo->query("SELECT u.id, u.nom, u.prenom, r.label as role_label FROM users u JOIN roles r ON r.id=u.role_id WHERE r.nom IN ('medecin','infirmier','praticien') AND u.actif=1 ORDER BY u.nom")->fetchAll();
    }
}
