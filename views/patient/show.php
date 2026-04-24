<?php
// views/patient/show.php
$pageTitle   = e($patient['prenom'] . ' ' . $patient['nom']);
$activeRoute = 'patients';
ob_start();
?>
<div class="page-header flex justify-between items-center">
  <div>
    <h1><?= e($patient['prenom'] . ' ' . $patient['nom']) ?></h1>
    <p>Fiche patient <?= $patient['date_naissance'] ? '— Né(e) le ' . date('d/m/Y', strtotime($patient['date_naissance'])) : '' ?></p>
  </div>
  <div class="flex gap-2">
    <?php if (RoleMiddleware::can('modifier_patients')): ?>
    <a href="?route=patient.edit&id=<?= $patient['id'] ?>" class="btn btn-ghost">Modifier</a>
    <?php endif; ?>
    <?php if (RoleMiddleware::can('creer_consultation')): ?>
    <a href="?route=consultation.create&patient_id=<?= $patient['id'] ?>" class="btn btn-primary">Nouvelle consultation</a>
    <?php endif; ?>
  </div>
</div>

<div class="tabs">
  <button class="tab-btn active" data-tab="tab-admin">Informations</button>
  <?php if ($dossier !== null): ?>
  <button class="tab-btn" data-tab="tab-dossier">Dossier médical</button>
  <?php endif; ?>
  <button class="tab-btn" data-tab="tab-rdv">Rendez-vous (<?= count($rdvs) ?>)</button>
  <?php if (!empty($consultations)): ?>
  <button class="tab-btn" data-tab="tab-consult">Consultations (<?= count($consultations) ?>)</button>
  <?php endif; ?>
</div>

<!-- TAB: ADMIN INFO -->
<div class="tab-panel active" id="tab-admin">
  <div class="grid-2" style="gap:20px;">
    <div class="card">
      <div class="card-header"><span class="card-title">Coordonnées</span></div>
      <div class="card-body">
        <table style="font-size:.9rem;"><tbody>
          <tr><td class="text-gray" style="padding:7px 12px 7px 0; width:140px;">Nom</td><td class="font-bold"><?= e($patient['nom'] . ' ' . $patient['prenom']) ?></td></tr>
          <tr><td class="text-gray" style="padding:7px 12px 7px 0;">Email</td><td><?= e($patient['email'] ?? '—') ?></td></tr>
          <tr><td class="text-gray" style="padding:7px 12px 7px 0;">Téléphone</td><td><?= e($patient['telephone'] ?? '—') ?></td></tr>
          <tr><td class="text-gray" style="padding:7px 12px 7px 0;">Adresse</td><td><?= e($patient['adresse'] ?? '—') ?></td></tr>
          <tr><td class="text-gray" style="padding:7px 12px 7px 0;">Naissance</td><td><?= $patient['date_naissance'] ? date('d/m/Y', strtotime($patient['date_naissance'])) : '—' ?></td></tr>
        </tbody></table>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">Compte d'accès</span></div>
      <div class="card-body">
        <?php if ($patient['username']): ?>
        <p><span class="text-gray">Identifiant :</span> <strong><?= e($patient['username']) ?></strong></p>
        <?php else: ?>
        <p class="text-gray">Aucun compte associé à ce patient.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- TAB: DOSSIER MEDICAL -->
<?php if ($dossier !== null): ?>
<div class="tab-panel" id="tab-dossier">
  <div class="card">
    <div class="card-header"><span class="card-title">Dossier médical</span></div>
    <div class="card-body">
      <?php if ($dossier): ?>
      <div class="grid-2" style="gap:20px;">
        <div><div class="form-label">Antécédents</div><p><?= nl2br(e($dossier['antecedents'] ?? '—')) ?></p></div>
        <div><div class="form-label">Allergies</div><p><?= nl2br(e($dossier['allergies'] ?? '—')) ?></p></div>
        <div><div class="form-label">Traitements en cours</div><p><?= nl2br(e($dossier['traitements_en_cours'] ?? '—')) ?></p></div>
        <div><div class="form-label">Groupe sanguin</div><p><?= e($dossier['groupe_sanguin'] ?? '—') ?></p></div>
      </div>
      <div class="mt-4"><div class="form-label">Notes générales</div><p><?= nl2br(e($dossier['notes_generales'] ?? '—')) ?></p></div>
      <?php else: ?>
      <p class="text-gray">Aucun dossier médical créé pour ce patient.</p>
      <a href="?route=patient.edit&id=<?= $patient['id'] ?>" class="btn btn-primary mt-4">Créer le dossier</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- TAB: RDV -->
<div class="tab-panel" id="tab-rdv">
  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="table-wrap">
        <table>
          <thead><tr><th>Date</th><th>Heure</th><th>Praticien</th><th>Motif</th><th>Statut</th></tr></thead>
          <tbody>
          <?php if (empty($rdvs)): ?>
          <tr><td colspan="5" class="text-gray" style="padding:20px; text-align:center;">Aucun rendez-vous.</td></tr>
          <?php else: ?>
          <?php foreach ($rdvs as $r): ?>
          <tr>
            <td><?= date('d/m/Y', strtotime($r['date_heure'])) ?></td>
            <td><?= date('H:i', strtotime($r['date_heure'])) ?></td>
            <td><?= e($r['praticien_nom']) ?></td>
            <td><?= e($r['motif'] ?? '—') ?></td>
            <td><?php
              $badges = ['planifie'=>'badge-blue','confirme'=>'badge-green','annule'=>'badge-red','termine'=>'badge-gray'];
              $labels = ['planifie'=>'Planifié','confirme'=>'Confirmé','annule'=>'Annulé','termine'=>'Terminé'];
            ?><span class="badge <?= $badges[$r['statut']] ?? 'badge-gray' ?>"><?= $labels[$r['statut']] ?? $r['statut'] ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- TAB: CONSULTATIONS -->
<?php if (!empty($consultations)): ?>
<div class="tab-panel" id="tab-consult">
  <?php foreach ($consultations as $c): ?>
  <div class="card mb-4">
    <div class="card-header">
      <span class="card-title"><?= date('d/m/Y H:i', strtotime($c['date_consultation'])) ?> — <?= e($c['praticien_nom']) ?></span>
      <span class="badge badge-purple"><?= e($c['praticien_role']) ?></span>
    </div>
    <div class="card-body">
      <div class="grid-2" style="gap:16px;">
        <?php if ($c['motif']): ?><div><div class="form-label">Motif</div><p><?= e($c['motif']) ?></p></div><?php endif; ?>
        <?php if ($c['diagnostic']): ?><div><div class="form-label">Diagnostic</div><p><?= nl2br(e($c['diagnostic'])) ?></p></div><?php endif; ?>
        <?php if ($c['examen_clinique']): ?><div><div class="form-label">Examen clinique</div><p><?= nl2br(e($c['examen_clinique'])) ?></p></div><?php endif; ?>
        <?php if ($c['traitement_prescrit']): ?><div><div class="form-label">Traitement prescrit</div><p><?= nl2br(e($c['traitement_prescrit'])) ?></p></div><?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php
$extraScript = '<script>
document.querySelectorAll(".tab-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
    document.querySelectorAll(".tab-panel").forEach(p => p.classList.remove("active"));
    btn.classList.add("active");
    document.getElementById(btn.dataset.tab)?.classList.add("active");
  });
});
</script>';
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
