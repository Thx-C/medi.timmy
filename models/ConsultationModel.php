<?php
// models/ConsultationModel.php

class ConsultationModel {
    private PDO $pdo;
    public function __construct() { $this->pdo = getPDO(); }

    public function getForPatient(int $patientId): array {
        $stmt = $this->pdo->prepare("
            SELECT c.*, CONCAT(u.nom,' ',u.prenom) as praticien_nom, r.label as praticien_role
            FROM consultations c JOIN users u ON u.id=c.praticien_id JOIN roles r ON r.id=u.role_id
            WHERE c.patient_id=? ORDER BY c.date_consultation DESC
        ");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function create(array $d): int {
        $stmt = $this->pdo->prepare("INSERT INTO consultations (rendez_vous_id, patient_id, praticien_id, date_consultation, motif, examen_clinique, diagnostic, traitement_prescrit, notes) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['rendez_vous_id'] ?? null, $d['patient_id'], $_SESSION['user']['id'], $d['date_consultation'] ?? date('Y-m-d H:i:s'), $d['motif'] ?? null, $d['examen_clinique'] ?? null, $d['diagnostic'] ?? null, $d['traitement_prescrit'] ?? null, $d['notes'] ?? null]);
        return (int)$this->pdo->lastInsertId();
    }
}
