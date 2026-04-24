<?php
// middleware/RoleMiddleware.php

class RoleMiddleware {

    /**
     * Vérifie qu'une permission est accordée à l'utilisateur connecté
     */
    public static function require(string $permission, callable $action): void {
        if (!isset($_SESSION['user'])) {
            flash('error', 'Connexion requise.');
            redirect('login');
        }
        $_SESSION['last_activity'] = time();

        if (!self::can($permission)) {
            http_response_code(403);
            view('layouts/403');
            exit;
        }
        $action();
    }

    /**
     * Vérifie si l'utilisateur connecté possède une permission
     */
    public static function can(string $permission): bool {
        return in_array($permission, $_SESSION['user']['permissions'] ?? []);
    }

    /**
     * Charge les permissions depuis la BDD pour l'user connecté
     */
    public static function loadPermissions(int $roleId): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare("
            SELECT p.code
            FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = ?
        ");
        $stmt->execute([$roleId]);
        return array_column($stmt->fetchAll(), 'code');
    }
}
