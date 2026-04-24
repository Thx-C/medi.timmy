<?php
// views/patient/create.php
$pageTitle   = 'Nouveau patient';
$activeRoute = 'patients';
ob_start();
?>
<div class="page-header">
  <h1>Nouveau patient</h1>
  <p>Créer une fiche patient et optionnellement un compte d'accès</p>
</div>

<div class="grid-2" style="gap:24px; align-items:start;">
  <div class="card">
    <div class="card-header"><span class="card-title">Informations patient</span></div>
    <div class="card-body">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nom *</label>
          <input type="text" class="form-control" id="p-nom" required>
        </div>
        <div class="form-group">
          <label class="form-label">Prénom *</label>
          <input type="text" class="form-control" id="p-prenom" required>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Date de naissance</label>
        <input type="date" class="form-control" id="p-naissance">
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="p-email" placeholder="patient@email.fr">
      </div>
      <div class="form-group">
        <label class="form-label">Téléphone</label>
        <input type="text" class="form-control" id="p-tel" placeholder="06 XX XX XX XX">
      </div>
      <div class="form-group">
        <label class="form-label">Adresse</label>
        <textarea class="form-control" id="p-adresse" rows="2" placeholder="Adresse complète"></textarea>
      </div>
    </div>
  </div>

  <div>
    <div class="card mb-4">
      <div class="card-header"><span class="card-title">Compte d'accès patient</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">Mode de compte</label>
          <select class="form-control" id="link-mode" onchange="toggleLinkMode(this.value)">
            <option value="new">Créer un nouveau compte (username auto-généré)</option>
            <option value="existing">Lier à un compte existant</option>
            <option value="none">Sans compte pour l'instant</option>
          </select>
        </div>

        <div id="block-new">
          <div class="form-group">
            <label class="form-label">Rôle du compte</label>
            <select class="form-control" id="p-role-id">
              <?php foreach ($roles as $r): ?>
              <?php if ($r['nom'] === 'patient'): ?>
              <option value="<?= $r['id'] ?>" selected><?= e($r['label']) ?></option>
              <?php else: ?>
              <option value="<?= $r['id'] ?>"><?= e($r['label']) ?></option>
              <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
          <p class="text-sm text-gray">L'identifiant sera généré automatiquement à partir du nom. Le mot de passe temporaire sera affiché après création.</p>
        </div>

        <div id="block-existing" style="display:none;">
          <div class="form-group">
            <label class="form-label">ID utilisateur existant</label>
            <input type="number" class="form-control" id="p-existing-user" placeholder="ID utilisateur">
          </div>
        </div>
      </div>
    </div>

    <button class="btn btn-primary w-full" style="justify-content:center;" onclick="createPatient()">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      Créer le patient
    </button>
  </div>
</div>

<!-- MODAL CREDENTIAL RESULT -->
<div class="modal-overlay" id="modal-cred">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">✅ Patient créé avec succès</span>
      <button class="modal-close" onclick="window.location='?route=patients'">✕</button>
    </div>
    <div class="modal-body">
      <p class="mb-4">Le patient a été créé. Voici les identifiants de connexion à transmettre :</p>
      <div class="credential-box" id="cred-display"></div>
      <div class="flex gap-2" style="justify-content:flex-end; margin-top:16px;">
        <button class="btn btn-ghost" onclick="copyCredentials()">Copier</button>
        <a href="?route=patients" class="btn btn-primary">Fermer</a>
      </div>
    </div>
  </div>
</div>

<?php
$extraScript = <<<JS
<script>
function toggleLinkMode(v) {
  document.getElementById('block-new').style.display      = v === 'new'      ? '' : 'none';
  document.getElementById('block-existing').style.display = v === 'existing' ? '' : 'none';
}

function createPatient() {
  const nom    = document.getElementById('p-nom').value.trim();
  const prenom = document.getElementById('p-prenom').value.trim();
  if (!nom || !prenom) { toast('Nom et prénom requis', 'error'); return; }

  const mode = document.getElementById('link-mode').value;
  const data = {
    nom, prenom,
    date_naissance: document.getElementById('p-naissance').value,
    email:          document.getElementById('p-email').value,
    telephone:      document.getElementById('p-tel').value,
    adresse:        document.getElementById('p-adresse').value,
    link_mode:      mode,
    patient_role_id:document.getElementById('p-role-id').value,
    existing_user_id:document.getElementById('p-existing-user').value || '',
  };

  apiPost('patient.create', data).then(res => {
    if (res.success) {
      if (res.username) {
        document.getElementById('cred-display').innerHTML =
          `<span>Identifiant :</span> \${res.username}<br><span>Mot de passe :</span> \${res.password}<br><br><span style="color:var(--amber)">⚠ À transmettre au patient. Mot de passe temporaire.</span>`;
      } else {
        document.getElementById('cred-display').innerHTML = '<span>Patient créé sans compte d\'accès.</span>';
      }
      document.getElementById('modal-cred').classList.add('open');
    } else {
      toast('Erreur lors de la création', 'error');
    }
  });
}

function copyCredentials() {
  const text = document.getElementById('cred-display').innerText;
  navigator.clipboard.writeText(text).then(() => toast('Copié !', 'success'));
}
</script>
JS;
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
