<?php
// controllers/PatientController.php

class PatientController {

    public function index(): void {
        $patients = (new PatientModel())->getAll();
        view('patient/index', compact('patients'));
    }

    public function search(): void {
        $q = trim($_GET['q'] ?? '');
        header('Content-Type: application/json');
        echo json_encode((new PatientModel())->search($q));
        exit;
    }

    public function show(): void {
        $id      = (int)($_GET['id'] ?? 0);
        $patient = (new PatientModel())->findById($id);
        if (!$patient) { flash('error', 'Patient introuvable.'); redirect('patients'); }

        $dossier      = RoleMiddleware::can('voir_dossiers') ? (new DossierModel())->findByPatient($id) : null;
        $consultations= RoleMiddleware::can('voir_dossiers') ? (new ConsultationModel())->getForPatient($id) : [];
        $rdvs         = (new RendezVousModel())->getForPatient($id);

        view('patient/show', compact('patient', 'dossier', 'consultations', 'rdvs'));
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfCheck();
            $um = new UserModel();
            $pm = new PatientModel();

            $nom    = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email  = trim($_POST['email'] ?? '');

            // Créer ou lier un compte patient
            $linkMode = $_POST['link_mode'] ?? 'new'; // 'new' | 'existing' | 'none'
            $userId = null;
            $plainPassword = null;

            if ($linkMode === 'new') {
                $username = $um->generateUsername($prenom, $nom);
                $plainPassword = bin2hex(random_bytes(5));
                $userId = $um->create([
                    'username'         => $username,
                    'password'         => $plainPassword,
                    'nom'              => $nom,
                    'prenom'           => $prenom,
                    'email'            => $email,
                    'role_id'          => (int)$_POST['patient_role_id'],
                    'mot_de_passe_temp'=> 1,
                ]);
            } elseif ($linkMode === 'existing') {
                $userId = (int)($_POST['existing_user_id'] ?? 0) ?: null;
            }

            $patientId = $pm->create([
                'user_id'        => $userId,
                'nom'            => $nom,
                'prenom'         => $prenom,
                'date_naissance' => $_POST['date_naissance'] ?? null,
                'email'          => $email,
                'telephone'      => $_POST['telephone'] ?? null,
                'adresse'        => $_POST['adresse'] ?? null,
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'success'       => true,
                'patient_id'    => $patientId,
                'username'      => $username ?? null,
                'password'      => $plainPassword,
            ]);
            exit;
        }
        $roles = (new RoleModel())->getAll();
        view('patient/create', compact('roles'));
    }

    public function edit(): void {
        $id      = (int)($_GET['id'] ?? 0);
        $patient = (new PatientModel())->findById($id);
        if (!$patient) { flash('error', 'Patient introuvable.'); redirect('patients'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfCheck();
            (new PatientModel())->update($id, $_POST);
            // Si praticien peut aussi modifier le dossier
            if (RoleMiddleware::can('modifier_dossiers') && isset($_POST['antecedents'])) {
                (new DossierModel())->upsert($id, $_POST);
            }
            flash('success', 'Fiche patient mise à jour.');
            redirect('patient.show&id=' . $id);
        }

        $dossier = RoleMiddleware::can('voir_dossiers') ? (new DossierModel())->findByPatient($id) : null;
        view('patient/edit', compact('patient', 'dossier'));
    }
}
