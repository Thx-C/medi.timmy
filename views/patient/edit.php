<?php
// views/patient/edit.php
$pageTitle   = 'Modifier — ' . e($patient['prenom'] . ' ' . $patient['nom']);
$activeRoute = 'patients';
ob_start();
?>
<div class="page-header">
  <h1>Modifier le patient</h1>
  <p><?= e($patient['prenom'] . ' ' . $patient['nom']) ?></p>
</div>
<form method="POST" action="?route=patient.edit&id=<?= $patient['id'] ?>">
  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
  <div class="grid-2" style="gap:24px; align-items:start;">
    <div class="card">
      <div class="card-header"><span class="card-title">Informations administratives</span></div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= e($patient['nom']) ?>" required></div>
          <div class="form-group"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= e($patient['prenom']) ?>" required></div>
        </div>
        <div class="form-group"><label class="form-label">Date de naissance</label><input type="date" name="date_naissance" class="form-control" value="<?= e($patient['date_naissance'] ?? '') ?>"></div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($patient['email'] ?? '') ?>"></div>
        <div class="form-group"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control" value="<?= e($patient['telephone'] ?? '') ?>"></div>
        <div class="form-group"><label class="form-label">Adresse</label><textarea name="adresse" class="form-control"><?= e($patient['adresse'] ?? '') ?></textarea></div>
      </div>
    </div>
    <?php if (RoleMiddleware::can('modifier_dossiers')): ?>
    <div class="card">
      <div class="card-header"><span class="card-title">Dossier médical</span></div>
      <div class="card-body">
        <div class="form-group"><label class="form-label">Antécédents</label><textarea name="antecedents" class="form-control"><?= e($dossier['antecedents'] ?? '') ?></textarea></div>
        <div class="form-group"><label class="form-label">Allergies</label><textarea name="allergies" class="form-control"><?= e($dossier['allergies'] ?? '') ?></textarea></div>
        <div class="form-group"><label class="form-label">Traitements en cours</label><textarea name="traitements_en_cours" class="form-control"><?= e($dossier['traitements_en_cours'] ?? '') ?></textarea></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Groupe sanguin</label><select name="groupe_sanguin" class="form-control"><?php foreach (['','A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gs): ?><option value="<?= $gs ?>" <?= ($dossier['groupe_sanguin'] ?? '') === $gs ? 'selected' : '' ?>><?= $gs ?: '—' ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="form-group"><label class="form-label">Notes générales</label><textarea name="notes_generales" class="form-control"><?= e($dossier['notes_generales'] ?? '') ?></textarea></div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <div class="flex gap-2 mt-4" style="justify-content:flex-end;">
    <a href="?route=patient.show&id=<?= $patient['id'] ?>" class="btn btn-ghost">Annuler</a>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
