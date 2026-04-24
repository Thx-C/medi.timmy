<?php
// views/patient/index.php
$pageTitle   = 'Patients';
$activeRoute = 'patients';
ob_start();
?>
<div class="page-header flex justify-between items-center">
  <div><h1>Patients</h1><p>Recherche et gestion des fiches patients</p></div>
  <?php if (RoleMiddleware::can('modifier_patients')): ?>
  <a href="?route=patient.create" class="btn btn-primary">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Nouveau patient
  </a>
  <?php endif; ?>
</div>

<div class="card mb-4">
  <div class="card-body">
    <input type="text" id="search-input" class="form-control" placeholder="🔍  Rechercher par nom, prénom ou email..." style="max-width:400px;">
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    <div class="table-wrap">
      <table id="patients-table">
        <thead>
          <tr><th>Patient</th><th>Email</th><th>Téléphone</th><th>Identifiant</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($patients as $p): ?>
          <tr>
            <td>
              <div class="font-bold"><?= e($p['nom'] . ' ' . $p['prenom']) ?></div>
              <?php if ($p['date_naissance'] ?? null): ?>
              <div class="text-xs text-gray"><?= date('d/m/Y', strtotime($p['date_naissance'])) ?></div>
              <?php endif; ?>
            </td>
            <td><?= e($p['email'] ?? '—') ?></td>
            <td><?= e($p['telephone'] ?? '—') ?></td>
            <td>
              <?php if ($p['username']): ?>
              <span class="badge badge-blue"><?= e($p['username']) ?></span>
              <?php else: ?>
              <span class="text-gray text-xs">Sans compte</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="flex gap-2">
                <a href="?route=patient.show&id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Voir</a>
                <?php if (RoleMiddleware::can('modifier_patients')): ?>
                <a href="?route=patient.edit&id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Modifier</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
$extraScript = <<<JS
<script>
document.getElementById('search-input').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#patients-table tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
JS;
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
