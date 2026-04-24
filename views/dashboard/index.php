<?php
$pageTitle  = 'Tableau de bord';
$activeRoute= 'dashboard';
ob_start();
?>

<div class="page-header">
  <h1>Bonjour, <?= e($_SESSION['user']['prenom']) ?> 👋</h1>
  <p>Voici un aperçu de l'activité du cabinet — <?= date('l d F Y') ?></p>
</div>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['aujourdhui'] ?></div>
      <div class="stat-label">RDV aujourd'hui</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon teal">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['semaine'] ?></div>
      <div class="stat-label">RDV cette semaine</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['confirmes'] ?></div>
      <div class="stat-label">RDV confirmés</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['planifies'] ?></div>
      <div class="stat-label">RDV planifiés</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['patients'] ?></div>
      <div class="stat-label">Patients enregistrés</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
      <div class="stat-value"><?= $stats['annules'] ?></div>
      <div class="stat-label">RDV annulés (total)</div>
    </div>
  </div>
</div>

<!-- UPCOMING RDV -->
<div class="grid-2" style="gap:24px;">
  <div class="card">
    <div class="card-header">
      <span class="card-title">Prochains rendez-vous</span>
      <?php if (RoleMiddleware::can('voir_agenda')): ?>
      <a href="?route=agenda" class="btn btn-ghost btn-sm">Voir agenda</a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <?php if (empty($prochains)): ?>
        <p class="text-gray text-sm">Aucun rendez-vous à venir.</p>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Date</th><th>Patient</th><th>Praticien</th><th>Statut</th></tr></thead>
          <tbody>
          <?php foreach ($prochains as $rdv): ?>
            <tr>
              <td>
                <div class="font-bold text-sm"><?= date('d/m', strtotime($rdv['date_heure'])) ?></div>
                <div class="text-xs text-gray"><?= date('H:i', strtotime($rdv['date_heure'])) ?></div>
              </td>
              <td class="font-bold"><?= e($rdv['patient_nom']) ?></td>
              <td class="text-sm text-gray"><?= e($rdv['praticien_nom']) ?></td>
              <td><?php
                $badges = ['planifie'=>'badge-blue','confirme'=>'badge-green','annule'=>'badge-red','termine'=>'badge-gray'];
                $labels = ['planifie'=>'Planifié','confirme'=>'Confirmé','annule'=>'Annulé','termine'=>'Terminé'];
              ?><span class="badge <?= $badges[$rdv['statut']] ?? 'badge-gray' ?>"><?= $labels[$rdv['statut']] ?? $rdv['statut'] ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- QUICK ACTIONS -->
  <div class="card">
    <div class="card-header"><span class="card-title">Actions rapides</span></div>
    <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
      <?php if (RoleMiddleware::can('gerer_agenda')): ?>
      <button class="btn btn-primary" onclick="document.getElementById('modal-rdv').classList.add('open')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau rendez-vous
      </button>
      <?php endif; ?>
      <?php if (RoleMiddleware::can('modifier_patients')): ?>
      <a href="?route=patient.create" class="btn btn-ghost">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Nouveau patient
      </a>
      <?php endif; ?>
      <?php if (RoleMiddleware::can('voir_patients')): ?>
      <a href="?route=patients" class="btn btn-ghost">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Rechercher un patient
      </a>
      <?php endif; ?>
      <?php if (RoleMiddleware::can('voir_dossiers')): ?>
      <a href="?route=dossiers" class="btn btn-ghost">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Dossiers médicaux
      </a>
      <?php endif; ?>
      <?php if (RoleMiddleware::can('gerer_comptes')): ?>
      <a href="?route=admin.accounts" class="btn btn-ghost">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Gestion des comptes
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
