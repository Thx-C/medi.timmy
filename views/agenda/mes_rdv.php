<?php
// views/agenda/mes_rdv.php
$pageTitle   = 'Mes rendez-vous';
$activeRoute = 'mes-rdv';
ob_start();
?>
<div class="page-header">
  <h1>Mes rendez-vous</h1>
  <p>Pour modifier ou annuler un rendez-vous, contactez le cabinet par téléphone.</p>
</div>
<div class="card">
  <div class="card-body" style="padding:0;">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Date</th><th>Heure</th><th>Praticien</th><th>Spécialité</th><th>Motif</th><th>Statut</th></tr></thead>
        <tbody>
        <?php if (empty($rdvs)): ?>
        <tr><td colspan="6" style="padding:32px; text-align:center; color:var(--gray-400);">Aucun rendez-vous enregistré.</td></tr>
        <?php else: ?>
        <?php foreach ($rdvs as $r):
          $past = strtotime($r['date_heure']) < time();
        ?>
        <tr style="<?= $past ? 'opacity:.6' : '' ?>">
          <td class="font-bold"><?= date('d/m/Y', strtotime($r['date_heure'])) ?></td>
          <td><?= date('H:i', strtotime($r['date_heure'])) ?></td>
          <td><?= e($r['praticien_nom']) ?></td>
          <td><span class="badge badge-blue"><?= e($r['praticien_role']) ?></span></td>
          <td><?= e($r['motif'] ?? '—') ?></td>
          <td><?php
            $badges = ['planifie'=>'badge-blue','confirme'=>'badge-green','annule'=>'badge-red','termine'=>'badge-gray'];
            $labels = ['planifie'=>'Planifié','confirme'=>'Confirmé','annule'=>'Annulé','termine'=>'Terminé'];
          ?><span class="badge <?= $badges[$r['statut']] ?? 'badge-gray' ?>"><?= $labels[$r['statut']] ?? '' ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="card mt-4">
  <div class="card-body">
    <p class="text-sm text-gray">📞 Pour prendre, modifier ou annuler un rendez-vous, appelez le cabinet au <strong>01 23 45 67 89</strong>.</p>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
