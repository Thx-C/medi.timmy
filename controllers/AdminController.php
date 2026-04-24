<?php
// controllers/AdminController.php

class AdminController {

    public function accounts(): void {
        $users = (new UserModel())->getAll();
        $roles = (new RoleModel())->getAll();
        view('admin/accounts', compact('users', 'roles'));
    }

    public function createAccount(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin.accounts'); }
        csrfCheck();
        $um = new UserModel();
        $plainPassword = bin2hex(random_bytes(6));
        $id = $um->create([
            'username'         => $um->generateUsername($_POST['prenom'], $_POST['nom']),
            'password'         => $plainPassword,
            'nom'              => trim($_POST['nom']),
            'prenom'           => trim($_POST['prenom']),
            'email'            => trim($_POST['email'] ?? ''),
            'telephone'        => trim($_POST['telephone'] ?? ''),
            'role_id'          => (int)$_POST['role_id'],
            'mot_de_passe_temp'=> 1,
        ]);
        $user = $um->findById($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'username' => $user['username'], 'password' => $plainPassword]);
        exit;
    }

    public function toggleUser(): void {
        csrfCheck();
        (new UserModel())->toggleActif((int)$_POST['id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function resetPassword(): void {
        csrfCheck();
        $plain = (new UserModel())->resetPassword((int)$_POST['id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'password' => $plain]);
        exit;
    }

    public function changeRole(): void {
        csrfCheck();
        (new UserModel())->updateRole((int)$_POST['user_id'], (int)$_POST['role_id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function roles(): void {
        $roles = (new RoleModel())->getAll();
        $permissions = (new RoleModel())->getAllPermissions();
        view('admin/roles', compact('roles', 'permissions'));
    }

    public function createRole(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin.roles'); }
        csrfCheck();
        $rm  = new RoleModel();
        $nom = strtolower(preg_replace('/[^a-z0-9_]/i', '_', trim($_POST['nom'])));
        $id  = $rm->create($nom, trim($_POST['label']));
        $rm->setPermissions($id, $_POST['permissions'] ?? []);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    public function updateRolePerms(): void {
        csrfCheck();
        $rm = new RoleModel();
        $role = $rm->findById((int)$_POST['role_id']);
        if ($role) {
            // Admin peut modifier les permissions de tous les rôles, y compris système
            $rm->setPermissions((int)$_POST['role_id'], $_POST['permissions'] ?? []);
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function deleteRole(): void {
        csrfCheck();
        $ok = (new RoleModel())->delete((int)$_POST['id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
        exit;
    }
}