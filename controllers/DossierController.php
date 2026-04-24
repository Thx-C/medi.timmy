<?php
// controllers/DossierController.php
class DossierController {
    public function index(): void {
        $patients = (new PatientModel())->getAll();
        view('dossier/index', compact('patients'));
    }
    public function show(): void {
        $id = (int)($_GET['id'] ?? 0);
        $patient = (new PatientModel())->findById($id);
        if (!$patient) { flash('error', 'Patient introuvable.'); redirect('dossiers'); }
        $dossier = (new DossierModel())->findByPatient($id);
        $consultations = (new ConsultationModel())->getForPatient($id);
        view('dossier/show', compact('patient', 'dossier', 'consultations'));
    }
}
