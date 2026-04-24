<?php
// config/app.php

define('APP_NAME', 'MediTimmy');
define('APP_VERSION', '1.0');
define('BASE_URL', '/meditimmy');
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Autoload simple
spl_autoload_register(function (string $class) {
    $dirs = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../middleware/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helper redirect
function redirect(string $route): void {
    header('Location: ' . BASE_URL . '/index.php?route=' . $route);
    exit;
}

// Helper view
function view(string $path, array $data = []): void {
    extract($data);
    require __DIR__ . '/../views/' . $path . '.php';
}

// Helper flash message
function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// CSRF
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfCheck(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF validation failed.');
    }
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
