<?php
// controllers/DashboardController.php

class DashboardController {
    public function index(): void {
        $rvModel = new RendezVousModel();
        $stats = $rvModel->getStats();

        // 7 prochains RDV
        $prochains = array_filter($rvModel->getAll(), fn($r) => strtotime($r['date_heure']) >= time() && $r['statut'] !== 'annule');
        usort($prochains, fn($a, $b) => strtotime($a['date_heure']) - strtotime($b['date_heure']));
        $prochains = array_slice(array_values($prochains), 0, 7);

        view('dashboard/index', compact('stats', 'prochains'));
    }
}
