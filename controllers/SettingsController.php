<?php
// controllers/SettingsController.php
class SettingsController {
    public function index(): void {
        view('settings/index');
    }
    public function save(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('settings'); }
        csrfCheck();
        $um = new UserModel();
        $id = $_SESSION['user']['id'];

        if (!empty($_POST['new_password'])) {
            $current = $_POST['current_password'] ?? '';
            $user = $um->findById($id);
            if (!password_verify($current, $user['password_hash'])) {
                flash('error', 'Mot de passe actuel incorrect.');
                redirect('settings');
            }
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                flash('error', 'Les mots de passe ne correspondent pas.');
                redirect('settings');
            }
            $um->updatePassword($id, $_POST['new_password']);
            $_SESSION['user']['temp_pass'] = false;
            flash('success', 'Mot de passe modifié avec succès.');
        } else {
            flash('info', 'Aucun changement effectué.');
        }
        redirect('settings');
    }
}
