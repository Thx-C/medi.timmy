<?php
// views/dossier/show.php
$pageTitle   = 'Dossier — ' . e($patient['prenom'] . ' ' . $patient['nom']);
$activeRoute = 'dossiers';
ob_start();
?>
<div class="page-header flex justify-between items-center">
  <div>
    <h1>Dossier de <?= e($patient['prenom'] . ' ' . $patient['nom']) ?></h1>
    <?php if ($patient['date_naissance']): ?><p>Né(e) le <?= date('d/m/Y', strtotime($patient['date_naissance'])) ?></p><?php endif; ?>
  </div>
  <div class="flex gap-2">
    <a href="?route=patient.edit&id=<?= $patient['id'] ?>" class="btn btn-ghost">Modifier</a>
    <?php if (RoleMiddleware::can('creer_consultation')): ?>
    <a href="?route=consultation.create&patient_id=<?= $patient['id'] ?>" class="btn btn-primary">Nouvelle consultation</a>
    <?php endif; ?>
  </div>
</div>

<div class="grid-2" style="gap:24px; margin-bottom:24px;">
  <div class="card">
    <div class="card-header"><span class="card-title">Dossier médical</span></div>
    <div class="card-body">
      <?php if ($dossier): ?>
      <div style="display:flex; flex-direction:column; gap:16px;">
        <div><div class="form-label">Antécédents</div><p><?= nl2br(e($dossier['antecedents'] ?? '—')) ?></p></div>
        <div><div class="form-label">Allergies</div><p><?= nl2br(e($dossier['allergies'] ?? '—')) ?></p></div>
        <div><div class="form-label">Traitements en cours</div><p><?= nl2br(e($dossier['traitements_en_cours'] ?? '—')) ?></p></div>
        <div><div class="form-label">Groupe sanguin</div>
          <?php if ($dossier['groupe_sanguin']): ?>
          <span class="badge badge-red" style="font-size:.9rem;"><?= e($dossier['groupe_sanguin']) ?></span>
          <?php else: ?><p>—</p><?php endif; ?>
        </div>
        <?php if ($dossier['notes_generales']): ?>
        <div><div class="form-label">Notes</div><p><?= nl2br(e($dossier['notes_generales'])) ?></p></div>
        <?php endif; ?>
      </div>
      <?php else: ?>
      <p class="text-gray">Aucun dossier médical. <a href="?route=patient.edit&id=<?= $patient['id'] ?>">Créer le dossier</a></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Coordonnées</span></div>
    <div class="card-body">
      <table style="font-size:.9rem; width:100%;"><tbody>
        <tr><td class="text-gray" style="padding:6px 12px 6px 0; width:120px;">Email</td><td><?= e($patient['email'] ?? '—') ?></td></tr>
        <tr><td class="text-gray" style="padding:6px 12px 6px 0;">Téléphone</td><td><?= e($patient['telephone'] ?? '—') ?></td></tr>
        <tr><td class="text-gray" style="padding:6px 12px 6px 0;">Adresse</td><td><?= e($patient['adresse'] ?? '—') ?></td></tr>
      </tbody></table>
    </div>
  </div>
</div>

<!-- CONSULTATIONS -->
<div class="card">
  <div class="card-header"><span class="card-title">Historique des consultations (<?= count($consultations) ?>)</span></div>
  <div class="card-body" style="padding:0;">
    <?php if (empty($consultations)): ?>
    <p style="padding:20px; color:var(--gray-400); text-align:center;">Aucune consultation enregistrée.</p>
    <?php else: ?>
    <?php foreach ($consultations as $c): ?>
    <div style="padding:16px 22px; border-bottom:1px solid var(--gray-100);">
      <div class="flex justify-between items-center mb-4">
        <div>
          <span class="font-bold"><?= date('d/m/Y H:i', strtotime($c['date_consultation'])) ?></span>
          <span class="text-gray text-sm ml-2">— <?= e($c['praticien_nom']) ?></span>
          <span class="badge badge-purple text-xs ml-2"><?= e($c['praticien_role']) ?></span>
        </div>
      </div>
      <div class="grid-2" style="gap:14px; font-size:.9rem;">
        <?php if ($c['motif']): ?><div><span class="text-gray">Motif :</span> <?= e($c['motif']) ?></div><?php endif; ?>
        <?php if ($c['diagnostic']): ?><div><span class="text-gray">Diagnostic :</span> <?= nl2br(e($c['diagnostic'])) ?></div><?php endif; ?>
        <?php if ($c['examen_clinique']): ?><div><span class="text-gray">Examen :</span> <?= nl2br(e($c['examen_clinique'])) ?></div><?php endif; ?>
        <?php if ($c['traitement_prescrit']): ?><div><span class="text-gray">Traitement :</span> <?= nl2br(e($c['traitement_prescrit'])) ?></div><?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
