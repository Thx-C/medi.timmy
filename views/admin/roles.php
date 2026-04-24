<?php
// views/admin/roles.php
$pageTitle   = 'Rôles & Permissions';
$activeRoute = 'admin.roles';
ob_start();
?>
    <div class="page-header flex justify-between items-center">
        <div><h1>Rôles & Permissions</h1><p>Définissez les rôles et leurs droits d'accès</p></div>
        <button class="btn btn-primary" onclick="document.getElementById('modal-new-role').classList.add('open')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau rôle
        </button>
    </div>

    <div style="display:flex; flex-direction:column; gap:20px;">
        <?php foreach ($roles as $role):
            $rolePerms = (new RoleModel())->getPermissionsForRole($role['id']);
            ?>
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <span class="card-title"><?= e($role['label']) ?></span>
                        <span class="badge badge-gray text-xs"><?= e($role['nom']) ?></span>
                        <?php if ($role['est_systeme']): ?>
                            <span class="badge badge-amber">Système</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!$role['est_systeme']): ?>
                        <button class="btn btn-danger btn-sm" onclick="deleteRole(<?= $role['id'] ?>, '<?= e($role['label']) ?>')">Supprimer</button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="perm-grid">
                        <?php foreach ($permissions as $perm): ?>
                            <label class="perm-item">
                                <input type="checkbox"
                                       name="perm_<?= $role['id'] ?>_<?= $perm['id'] ?>"
                                       value="<?= e($perm['code']) ?>"
                                        <?= in_array($perm['code'], $rolePerms) ? 'checked' : '' ?>>
                                <div>
                                    <div class="perm-item-label"><?= e($perm['label']) ?></div>
                                    <div class="perm-item-desc"><?= e($perm['description'] ?? '') ?></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex gap-2 mt-4" style="justify-content:flex-end;">
                        <button class="btn btn-primary btn-sm" onclick="savePerms(<?= $role['id'] ?>)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Sauvegarder les permissions
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- MODAL NOUVEAU RÔLE -->
    <div class="modal-overlay" id="modal-new-role">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">Nouveau rôle</span>
                <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom (identifiant technique)</label>
                    <input type="text" class="form-control" id="nr-nom" placeholder="ex: infirmier_senior">
                </div>
                <div class="form-group">
                    <label class="form-label">Libellé (affiché)</label>
                    <input type="text" class="form-control" id="nr-label" placeholder="ex: Infirmier Senior">
                </div>
                <div class="form-group">
                    <label class="form-label">Permissions initiales</label>
                    <div class="perm-grid" id="nr-perms">
                        <?php foreach ($permissions as $perm): ?>
                            <label class="perm-item">
                                <input type="checkbox" name="nr_perm" value="<?= e($perm['code']) ?>">
                                <div>
                                    <div class="perm-item-label"><?= e($perm['label']) ?></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex gap-2" style="justify-content:flex-end; margin-top:8px;">
                    <button class="btn btn-ghost" onclick="this.closest('.modal-overlay').classList.remove('open')">Annuler</button>
                    <button class="btn btn-primary" onclick="createRole()">Créer le rôle</button>
                </div>
            </div>
        </div>
    </div>

<?php
$extraScript = <<<'JS'
<script>
function savePerms(roleId) {
  const checked = [...document.querySelectorAll(`input[name^="perm_${roleId}_"]:checked`)].map(i => i.value);
  const fd = new FormData();
  fd.append('csrf_token', CSRF_TOKEN);
  fd.append('role_id', roleId);
  checked.forEach(v => fd.append('permissions[]', v));
  fetch(BASE_URL + '/index.php?route=admin.role.perms', { method:'POST', body:fd })
    .then(r => r.json()).then(res => {
      if (res.success) toast('Permissions sauvegardées', 'success');
      else toast('Erreur', 'error');
    });
}

function createRole() {
  const nom   = document.getElementById('nr-nom').value.trim();
  const label = document.getElementById('nr-label').value.trim();
  if (!nom || !label) { toast('Nom et libellé requis', 'error'); return; }
  const perms = [...document.querySelectorAll('#nr-perms input:checked')].map(i => i.value);

  const fd = new FormData();
  fd.append('csrf_token', CSRF_TOKEN);
  fd.append('nom', nom);
  fd.append('label', label);
  perms.forEach(v => fd.append('permissions[]', v));
  fetch(BASE_URL + '/index.php?route=admin.role.create', { method:'POST', body:fd })
    .then(r => r.json()).then(res => {
      if (res.success) { toast('Rôle créé', 'success'); setTimeout(() => location.reload(), 800); }
      else toast('Erreur', 'error');
    });
}

function deleteRole(id, name) {
  if (!confirm(`Supprimer le rôle "${name}" ?`)) return;
  apiPost('admin.role.delete', { id }).then(res => {
    if (res.success) { toast('Rôle supprimé', 'info'); setTimeout(() => location.reload(), 800); }
    else toast('Impossible de supprimer ce rôle', 'error');
  });
}
</script>
JS;
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';