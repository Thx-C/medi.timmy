<?php
// views/dossier/index.php
$pageTitle   = 'Dossiers médicaux';
$activeRoute = 'dossiers';
ob_start();
?>
<div class="page-header"><h1>Dossiers médicaux</h1><p>Accès aux dossiers de tous les patients</p></div>
<div class="card">
  <div class="card-body" style="padding-bottom:8px;">
    <input type="text" id="search-dossiers" class="form-control" placeholder="🔍  Rechercher un patient..." style="max-width:360px; margin-bottom:16px;">
  </div>
  <div class="card-body" style="padding:0;">
    <div class="table-wrap">
      <table id="dossiers-table">
        <thead><tr><th>Patient</th><th>Date de naissance</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($patients as $p): ?>
        <tr>
          <td class="font-bold"><?= e($p['nom'] . ' ' . $p['prenom']) ?></td>
          <td><?= $p['date_naissance'] ? date('d/m/Y', strtotime($p['date_naissance'])) : '—' ?></td>
          <td>
            <div class="flex gap-2">
              <a href="?route=dossier.show&id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Ouvrir le dossier</a>
              <?php if (RoleMiddleware::can('creer_consultation')): ?>
              <a href="?route=consultation.create&patient_id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">Nouvelle consultation</a>
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
$extraScript = '<script>
document.getElementById("search-dossiers").addEventListener("input", function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll("#dossiers-table tbody tr").forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? "" : "none";
  });
});
</script>';
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
