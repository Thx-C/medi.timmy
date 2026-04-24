<?php
// models/RoleModel.php

class RoleModel {
    private PDO $pdo;
    public function __construct() { $this->pdo = getPDO(); }

    public function getAll(): array {
        return $this->pdo->query("SELECT * FROM roles ORDER BY est_systeme DESC, nom")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getAllPermissions(): array {
        return $this->pdo->query("SELECT * FROM permissions ORDER BY label")->fetchAll();
    }

    public function getPermissionsForRole(int $roleId): array {
        $stmt = $this->pdo->prepare("SELECT p.code FROM permissions p JOIN role_permissions rp ON rp.permission_id=p.id WHERE rp.role_id=?");
        $stmt->execute([$roleId]);
        return array_column($stmt->fetchAll(), 'code');
    }

    public function create(string $nom, string $label): int {
        $stmt = $this->pdo->prepare("INSERT INTO roles (nom, label) VALUES (?,?)");
        $stmt->execute([$nom, $label]);
        return (int)$this->pdo->lastInsertId();
    }

    public function setPermissions(int $roleId, array $permCodes): void {
        $this->pdo->prepare("DELETE FROM role_permissions WHERE role_id=?")->execute([$roleId]);
        if (empty($permCodes)) return;
        $perms = $this->pdo->query("SELECT id, code FROM permissions")->fetchAll();
        $map = array_column($perms, 'id', 'code');
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)");
        foreach ($permCodes as $code) {
            if (isset($map[$code])) $stmt->execute([$roleId, $map[$code]]);
        }
    }

    public function delete(int $id): bool {
        $role = $this->findById($id);
        if (!$role || $role['est_systeme']) return false;
        $this->pdo->prepare("DELETE FROM roles WHERE id=?")->execute([$id]);
        return true;
    }
}
