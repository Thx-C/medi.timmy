<?php
// controllers/ConsultationController.php
class ConsultationController {
    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            try {
                csrfCheck();
                // Convertir rendez_vous_id en null si absent ou 0 pour respecter la FK
                if (empty($_POST['rendez_vous_id']) || (int)$_POST['rendez_vous_id'] === 0) {
                    $_POST['rendez_vous_id'] = null;
                }
                $id = (new ConsultationModel())->create($_POST);
                if (!empty($_POST['rendez_vous_id'])) {
                    $rv = (new RendezVousModel())->findById((int)$_POST['rendez_vous_id']);
                    if ($rv) {
                        $rv['statut'] = 'termine';
                        (new RendezVousModel())->update((int)$_POST['rendez_vous_id'], $rv);
                    }
                }
                echo json_encode(['success' => true, 'id' => $id]);
            } catch (\Throwable $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
        $patientId = (int)($_GET['patient_id'] ?? 0);
        $rdvId     = (int)($_GET['rdv_id'] ?? 0);
        $patient   = (new PatientModel())->findById($patientId);
        $rdv       = $rdvId ? (new RendezVousModel())->findById($rdvId) : null;
        view('consultation/create', compact('patient', 'rdv'));
    }
}