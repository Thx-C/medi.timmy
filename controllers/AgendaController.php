<?php
// controllers/AgendaController.php

class AgendaController {

    public function index(): void {
        // Patient : listes vides (lecture seule, pas de création de RDV)
        if ($_SESSION['user']['role_nom'] === 'patient') {
            $praticiens = [];
            $patients   = [];
        } else {
            $praticiens = (new UserModel())->getPraticiens();
            $patients   = (new PatientModel())->getAll();
        }
        view('agenda/index', compact('praticiens', 'patients'));
    }

    /** API JSON — liste des events pour FullCalendar */
    public function events(): void {
        header('Content-Type: application/json');
        $model = new RendezVousModel();

        // Patient : uniquement SES propres RDV, jamais ceux des autres
        if ($_SESSION['user']['role_nom'] === 'patient') {
            $patient = (new PatientModel())->findByUserId($_SESSION['user']['id']);
            if (!$patient) { echo json_encode([]); exit; }

            $events = [];
            foreach ($model->getForPatient($patient['id']) as $rv) {
                $events[] = [
                    'id'    => $rv['id'],
                    'title' => $rv['praticien_nom'],
                    'start' => $rv['date_heure'],
                    'end'   => date('Y-m-d H:i:s', strtotime($rv['date_heure']) + $rv['duree_minutes'] * 60),
                    'color' => match($rv['statut']) {
                        'confirme' => '#10B981',
                        'annule'   => '#EF4444',
                        'termine'  => '#6B7280',
                        default    => '#3B82F6'
                    },
                    'extendedProps' => [
                        'motif'     => $rv['motif'],
                        'statut'    => $rv['statut'],
                        'praticien' => $rv['praticien_nom'],
                    ],
                ];
            }
            echo json_encode($events);
            exit;
        }

        // Tous les autres rôles : agenda complet
        echo json_encode($model->getForCalendar());
        exit;
    }

    /** API — drag & drop déplace un RDV */
    public function move(): void {
        header('Content-Type: application/json');
        if (!RoleMiddleware::can('gerer_agenda')) {
            echo json_encode(['success' => false, 'error' => 'Permission refusée']);
            exit;
        }
        $id       = (int)($_POST['id'] ?? 0);
        $newStart = $_POST['start'] ?? '';
        if (!$id || !$newStart) { echo json_encode(['success' => false]); exit; }
        (new RendezVousModel())->move($id, $newStart);
        echo json_encode(['success' => true]);
        exit;
    }

    /** Créer RDV */
    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfCheck();
            $data = [
                'patient_id'    => (int)$_POST['patient_id'],
                'praticien_id'  => (int)$_POST['praticien_id'],
                'date_heure'    => $_POST['date_heure'],
                'duree_minutes' => (int)($_POST['duree_minutes'] ?? 30),
                'motif'         => trim($_POST['motif'] ?? ''),
                'statut'        => $_POST['statut'] ?? 'planifie',
            ];
            $id = (new RendezVousModel())->create($data);

            // Si c'est un nouveau patient sans compte, proposer création
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $id]);
            exit;
        }
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfCheck();
            $id = (int)$_POST['id'];
            (new RendezVousModel())->update($id, $_POST);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
    }

    public function cancel(): void {
        csrfCheck();
        (new RendezVousModel())->cancel((int)$_POST['id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function mesRdv(): void {
        $pm = new PatientModel();
        $patient = $pm->findByUserId($_SESSION['user']['id']);
        $rdvs = $patient ? (new RendezVousModel())->getForPatient($patient['id']) : [];
        view('agenda/mes_rdv', compact('rdvs'));
    }
}
