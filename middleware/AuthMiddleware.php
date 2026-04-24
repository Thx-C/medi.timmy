<?php
// middleware/AuthMiddleware.php

class AuthMiddleware {
    public static function require(callable $action): void {
        if (!isset($_SESSION['user'])) {
            flash('error', 'Vous devez être connecté pour accéder à cette page.');
            redirect('login');
        }
        // Session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            redirect('login');
        }
        $_SESSION['last_activity'] = time();
        $action();
    }
}
