<?php
// controllers/AuthController.php

class AuthController {

    public function login(): void {
        if (isset($_SESSION['user'])) { redirect('dashboard'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfCheck();
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $model = new UserModel();
            $user = $model->findByUsername($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                $perms = RoleMiddleware::loadPermissions($user['role_id']);
                $_SESSION['user'] = [
                    'id'          => $user['id'],
                    'username'    => $user['username'],
                    'nom'         => $user['nom'],
                    'prenom'      => $user['prenom'],
                    'email'       => $user['email'],
                    'role_nom'    => $user['role_nom'],
                    'role_label'  => $user['role_label'],
                    'role_id'     => $user['role_id'],
                    'permissions' => $perms,
                    'temp_pass'   => (bool)$user['mot_de_passe_temp'],
                ];
                $_SESSION['last_activity'] = time();

                if ($user['mot_de_passe_temp']) {
                    flash('warning', 'Votre mot de passe est temporaire. Veuillez le modifier.');
                    redirect('settings');
                }
                redirect('dashboard');
            } else {
                flash('error', 'Identifiant ou mot de passe incorrect.');
                redirect('login');
            }
        }

        view('auth/login');
    }

    public function logout(): void {
        session_unset();
        session_destroy();
        redirect('login');
    }
}
