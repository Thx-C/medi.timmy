<?php
// views/consultation/create.php
$pageTitle   = 'Nouvelle consultation';
$activeRoute = 'patients';
ob_start();
?>
    <div class="page-header">
        <h1>Nouvelle consultation</h1>
        <?php if ($patient): ?><p>Patient : <strong><?= e($patient['prenom'] . ' ' . $patient['nom']) ?></strong></p><?php endif; ?>
    </div>
    <div class="card" style="max-width:780px;">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date de consultation</label>
                    <input type="datetime-local" class="form-control" id="c-date" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Motif</label>
                    <input type="text" class="form-control" id="c-motif" value="<?= e($rdv['motif'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Examen clinique</label>
                <textarea class="form-control" id="c-examen" rows="3" placeholder="Observations cliniques..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Diagnostic</label>
                <textarea class="form-control" id="c-diagnostic" rows="3" placeholder="Diagnostic posé..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Traitement prescrit</label>
                <textarea class="form-control" id="c-traitement" rows="3" placeholder="Ordonnance / prescriptions..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Notes complémentaires</label>
                <textarea class="form-control" id="c-notes" rows="2"></textarea>
            </div>
            <div class="flex gap-2" style="justify-content:flex-end;">
                <?php if ($patient): ?>
                    <a href="?route=patient.show&id=<?= $patient['id'] ?>" class="btn btn-ghost">Annuler</a>
                <?php endif; ?>
                <button class="btn btn-primary" id="btn-save" onclick="saveConsultation()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer la consultation
                </button>
            </div>
        </div>
    </div>
<?php
$patientId = $patient['id'] ?? 0;
$rdvId     = $rdv['id'] ?? 0;
$extraScript = <<<JS
<script>
function saveConsultation() {
  const btn = document.getElementById('btn-save');

  if (!{$patientId}) {
    toast('Patient introuvable. Veuillez recharger la page.', 'error');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Enregistrement…';

  const data = {
    patient_id:          '{$patientId}',
    rendez_vous_id:      '{$rdvId}',
    date_consultation:   document.getElementById('c-date').value.replace('T', ' ') + ':00',
    motif:               document.getElementById('c-motif').value,
    examen_clinique:     document.getElementById('c-examen').value,
    diagnostic:          document.getElementById('c-diagnostic').value,
    traitement_prescrit: document.getElementById('c-traitement').value,
    notes:               document.getElementById('c-notes').value,
  };

  apiPost('consultation.create', data)
    .then(res => {
      if (res && res.success) {
        toast('Consultation enregistrée', 'success');
        setTimeout(() => window.location = '?route=patient.show&id={$patientId}', 1000);
      } else {
        toast(res.error || 'Erreur lors de l\'enregistrement', 'error');
        btn.disabled = false;
        btn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer la consultation';
      }
    })
    .catch(err => {
      console.error('saveConsultation error:', err);
      toast('Erreur réseau ou réponse invalide du serveur', 'error');
      btn.disabled = false;
      btn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer la consultation';
    });
}
</script>
JS;
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';