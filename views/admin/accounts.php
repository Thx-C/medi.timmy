<?php
// views/admin/accounts.php
$pageTitle   = 'Comptes utilisateurs';
$activeRoute = 'admin.accounts';
ob_start();
?>
<div class="page-header flex justify-between items-center">
  <div><h1>Comptes utilisateurs</h1><p>Gérez les accès au cabinet</p></div>
  <button class="btn btn-primary" onclick="document.getElementById('modal-create').classList.add('open')">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Nouveau compte
  </button>
</div>

<div class="card">
  <div class="card-body" style="padding-bottom:8px; padding-top:16px;">
    <input type="text" id="search-users" class="form-control" placeholder="🔍  Rechercher..." style="max-width:340px; margin-bottom:16px;">
  </div>
  <div class="card-body" style="padding:0;">
    <div class="table-wrap">
      <table id="users-table">
        <thead><tr><th>Utilisateur</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td>
            <div class="font-bold"><?= e($u['prenom'] . ' ' . $u['nom']) ?></div>
            <div class="text-xs text-gray"><?= e($u['username']) ?></div>
          </td>
          <td><?= e($u['email'] ?? '—') ?></td>
          <td><span class="badge badge-blue"><?= e($u['role_label']) ?></span></td>
          <td>
            <?php if ($u['actif']): ?>
            <span class="badge badge-green">Actif</span>
            <?php else: ?>
            <span class="badge badge-red">Désactivé</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="flex gap-2">
              <select class="form-control" style="width:auto; font-size:.8rem; padding:5px 8px;"
                onchange="changeRole(<?= $u['id'] ?>, this.value)">
                <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['role_id'] ? 'selected' : '' ?>><?= e($r['label']) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-ghost btn-sm" onclick="resetPwd(<?= $u['id'] ?>, '<?= e($u['prenom'] . ' ' . $u['nom']) ?>')">
                Réinit. MDP
              </button>
              <?php if ($u['id'] != $_SESSION['user']['id']): ?>
              <button class="btn btn-sm <?= $u['actif'] ? 'btn-danger' : 'btn-success' ?>"
                onclick="toggleUser(<?= $u['id'] ?>, this)">
                <?= $u['actif'] ? 'Désactiver' : 'Activer' ?>
              </button>
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

<!-- MODAL CRÉER COMPTE -->
<div class="modal-overlay" id="modal-create">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Nouveau compte utilisateur</span>
      <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Nom *</label><input type="text" class="form-control" id="c-nom" required></div>
        <div class="form-group"><label class="form-label">Prénom *</label><input type="text" class="form-control" id="c-prenom" required></div>
      </div>
      <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" id="c-email"></div>
      <div class="form-group"><label class="form-label">Téléphone</label><input type="text" class="form-control" id="c-tel"></div>
      <div class="form-group">
        <label class="form-label">Rôle *</label>
        <select class="form-control" id="c-role">
          <?php foreach ($roles as $r): ?>
          <?php if ($r['nom'] !== 'patient'): ?>
          <option value="<?= $r['id'] ?>"><?= e($r['label']) ?></option>
          <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </div>
      <p class="text-sm text-gray mb-4">L'identifiant sera généré automatiquement. Le mot de passe temporaire sera affiché après création.</p>
      <div class="flex gap-2" style="justify-content:flex-end;">
        <button class="btn btn-ghost" onclick="this.closest('.modal-overlay').classList.remove('open')">Annuler</button>
        <button class="btn btn-primary" onclick="createAccount()">Créer le compte</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CREDENTIALS -->
<div class="modal-overlay" id="modal-cred">
  <div class="modal">
    <div class="modal-header"><span class="modal-title">✅ Compte créé</span></div>
    <div class="modal-body">
      <p class="mb-4">Transmettez ces identifiants à l'utilisateur :</p>
      <div class="credential-box" id="cred-box"></div>
      <div class="flex gap-2" style="justify-content:flex-end; margin-top:16px;">
        <button class="btn btn-ghost" onclick="navigator.clipboard.writeText(document.getElementById('cred-box').innerText).then(()=>toast('Copié !','success'))">Copier</button>
        <button class="btn btn-primary" onclick="this.closest('.modal-overlay').classList.remove('open'); location.reload()">Fermer</button>
      </div>
    </div>
  </div>
</div>

<?php
$extraScript = <<<'JS'
<script>
document.getElementById('search-users').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#users-table tbody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});

function createAccount() {
  const nom    = document.getElementById('c-nom').value.trim();
  const prenom = document.getElementById('c-prenom').value.trim();
  if (!nom || !prenom) { toast('Nom et prénom requis', 'error'); return; }
  apiPost('admin.account.create', {
    nom, prenom,
    email:    document.getElementById('c-email').value,
    telephone:document.getElementById('c-tel').value,
    role_id:  document.getElementById('c-role').value,
  }).then(res => {
    if (res.success) {
      document.getElementById('modal-create').classList.remove('open');
      document.getElementById('cred-box').innerHTML =
        `<span>Identifiant :</span> ${res.username}\n<span>Mot de passe :</span> ${res.password}\n\n<span style="color:var(--amber)">⚠ Mot de passe temporaire — à changer à la première connexion.</span>`;
      document.getElementById('modal-cred').classList.add('open');
    } else { toast('Erreur lors de la création', 'error'); }
  });
}

function toggleUser(id, btn) {
  if (!confirm('Confirmer ?')) return;
  apiPost('admin.account.toggle', { id }).then(res => {
    if (res.success) { toast('Statut modifié', 'success'); setTimeout(() => location.reload(), 800); }
  });
}

function resetPwd(id, name) {
  if (!confirm(`Réinitialiser le mot de passe de ${name} ?`)) return;
  apiPost('admin.account.reset', { id }).then(res => {
    if (res.success) {
      document.getElementById('cred-box').innerHTML =
        `Nouveau mot de passe de ${name} :\n\n<span>${res.password}</span>\n\n<span style="color:var(--amber)">⚠ Mot de passe temporaire.</span>`;
      document.getElementById('modal-cred').classList.add('open');
    }
  });
}

function changeRole(userId, roleId) {
  apiPost('admin.account.role', { user_id: userId, role_id: roleId })
    .then(res => { if (res.success) toast('Rôle mis à jour', 'success'); });
}
</script>
JS;
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
