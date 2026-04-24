<?php
// models/RendezVousModel.php

class RendezVousModel {
    private PDO $pdo;
    public function __construct() { $this->pdo = getPDO(); }

    public function getAll(): array {
        return $this->pdo->query("
            SELECT rv.*, CONCAT(p.nom,' ',p.prenom) as patient_nom,
                   CONCAT(u.nom,' ',u.prenom) as praticien_nom, r.label as praticien_role
            FROM rendez_vous rv
            JOIN patients p ON p.id=rv.patient_id
            JOIN users u ON u.id=rv.praticien_id
            JOIN roles r ON r.id=u.role_id
            ORDER BY rv.date_heure
        ")->fetchAll();
    }

    public function getForCalendar(): array {
        $rows = $this->getAll();
        $events = [];
        foreach ($rows as $rv) {
            $color = match($rv['statut']) {
                'planifie'  => '#3B82F6',
                'confirme'  => '#10B981',
                'annule'    => '#EF4444',
                'termine'   => '#6B7280',
                default     => '#3B82F6'
            };
            $events[] = [
                'id'    => $rv['id'],
                'title' => $rv['patient_nom'] . ' — ' . $rv['praticien_nom'],
                'start' => $rv['date_heure'],
                'end'   => date('Y-m-d H:i:s', strtotime($rv['date_heure']) + $rv['duree_minutes'] * 60),
                'color' => $color,
                'extendedProps' => [
                    'motif'    => $rv['motif'],
                    'statut'   => $rv['statut'],
                    'patient'  => $rv['patient_nom'],
                    'praticien'=> $rv['praticien_nom'],
                ]
            ];
        }
        return $events;
    }

    public function getForPatient(int $patientId): array {
        $stmt = $this->pdo->prepare("
            SELECT rv.*, CONCAT(u.nom,' ',u.prenom) as praticien_nom, r.label as praticien_role
            FROM rendez_vous rv
            JOIN users u ON u.id=rv.praticien_id
            JOIN roles r ON r.id=u.role_id
            WHERE rv.patient_id=?
            ORDER BY rv.date_heure DESC
        ");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT rv.*, CONCAT(p.nom,' ',p.prenom) as patient_nom,
                   CONCAT(u.nom,' ',u.prenom) as praticien_nom
            FROM rendez_vous rv
            JOIN patients p ON p.id=rv.patient_id
            JOIN users u ON u.id=rv.praticien_id
            WHERE rv.id=?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int {
        $stmt = $this->pdo->prepare("INSERT INTO rendez_vous (patient_id, praticien_id, date_heure, duree_minutes, motif, statut, created_by) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$d['patient_id'], $d['praticien_id'], $d['date_heure'], $d['duree_minutes'] ?? 30, $d['motif'] ?? null, $d['statut'] ?? 'planifie', $_SESSION['user']['id']]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $d): void {
        $stmt = $this->pdo->prepare("UPDATE rendez_vous SET patient_id=?, praticien_id=?, date_heure=?, duree_minutes=?, motif=?, statut=?, notes=? WHERE id=?");
        $stmt->execute([$d['patient_id'], $d['praticien_id'], $d['date_heure'], $d['duree_minutes'] ?? 30, $d['motif'] ?? null, $d['statut'] ?? 'planifie', $d['notes'] ?? null, $id]);
    }

    public function move(int $id, string $newDateHeure): void {
        $this->pdo->prepare("UPDATE rendez_vous SET date_heure=? WHERE id=?")->execute([$newDateHeure, $id]);
    }

    public function cancel(int $id): void {
        $this->pdo->prepare("UPDATE rendez_vous SET statut='annule' WHERE id=?")->execute([$id]);
    }

    public function getStats(): array {
        $pdo = $this->pdo;
        return [
            'total'      => (int)$pdo->query("SELECT COUNT(*) FROM rendez_vous")->fetchColumn(),
            'planifies'  => (int)$pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut='planifie'")->fetchColumn(),
            'confirmes'  => (int)$pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut='confirme'")->fetchColumn(),
            'annules'    => (int)$pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut='annule'")->fetchColumn(),
            'termines'   => (int)$pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut='termine'")->fetchColumn(),
            'aujourdhui' => (int)$pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE DATE(date_heure)=CURDATE()")->fetchColumn(),
            'semaine'    => (int)$pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE YEARWEEK(date_heure,1)=YEARWEEK(NOW(),1)")->fetchColumn(),
            'patients'   => (int)$pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn(),
            'praticiens' => (int)$pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON r.id=u.role_id WHERE r.nom IN ('medecin','infirmier','praticien') AND u.actif=1")->fetchColumn(),
        ];
    }
}
