<?php
// models/DossierModel.php

class DossierModel {
    private PDO $pdo;
    public function __construct() { $this->pdo = getPDO(); }

    public function getAll(): array {
        return $this->pdo->query("
            SELECT d.*, CONCAT(p.nom,' ',p.prenom) as patient_nom, p.date_naissance
            FROM dossiers d JOIN patients p ON p.id=d.patient_id
            ORDER BY p.nom
        ")->fetchAll();
    }

    public function findByPatient(int $patientId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM dossiers WHERE patient_id=?");
        $stmt->execute([$patientId]);
        return $stmt->fetch() ?: null;
    }

    public function upsert(int $patientId, array $data): void {
        $existing = $this->findByPatient($patientId);
        if ($existing) {
            $stmt = $this->pdo->prepare("UPDATE dossiers SET antecedents=?, allergies=?, traitements_en_cours=?, groupe_sanguin=?, notes_generales=? WHERE patient_id=?");
            $stmt->execute([$data['antecedents'] ?? null, $data['allergies'] ?? null, $data['traitements_en_cours'] ?? null, $data['groupe_sanguin'] ?? null, $data['notes_generales'] ?? null, $patientId]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO dossiers (patient_id, antecedents, allergies, traitements_en_cours, groupe_sanguin, notes_generales) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$patientId, $data['antecedents'] ?? null, $data['allergies'] ?? null, $data['traitements_en_cours'] ?? null, $data['groupe_sanguin'] ?? null, $data['notes_generales'] ?? null]);
        }
    }
}
