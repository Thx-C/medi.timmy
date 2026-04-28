<?php
// controllers/ConsultationController.php
class ConsultationController {
    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfCheck();
            $id = (new ConsultationModel())->create($_POST);
            if (!empty($_POST['rendez_vous_id'])) {
                $rv = (new RendezVousModel())->findById((int)$_POST['rendez_vous_id']);
                if ($rv) {
                    $rv['statut'] = 'termine';
                    (new RendezVousModel())->update((int)$_POST['rendez_vous_id'], $rv);
                }
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $id]);
            exit;
        }
        $patientId = (int)($_GET['patient_id'] ?? 0);
        $rdvId     = (int)($_GET['rdv_id'] ?? 0);
        $patient   = (new PatientModel())->findById($patientId);
        $rdv       = $rdvId ? (new RendezVousModel())->findById($rdvId) : null;
        view('consultation/create', compact('patient', 'rdv'));
    }
}
