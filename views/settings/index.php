<?php
// views/settings/index.php
$pageTitle   = 'Paramètres';
$activeRoute = 'settings';
ob_start();
?>
<div class="page-header"><h1>Paramètres</h1><p>Gérez vos préférences et sécurité du compte</p></div>

<?php $flash = getFlash(); if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<?php if ($_SESSION['user']['temp_pass'] ?? false): ?>
<div class="alert alert-warning">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
  Votre mot de passe est temporaire. Veuillez le modifier ci-dessous.
</div>
<?php endif; ?>

<div class="grid-2" style="gap:24px; align-items:start; max-width:900px;">
  <div class="card">
    <div class="card-header"><span class="card-title">Informations du compte</span></div>
    <div class="card-body">
      <table style="font-size:.9rem; width:100%;"><tbody>
        <tr><td class="text-gray" style="padding:8px 12px 8px 0; width:150px;">Identifiant</td><td class="font-bold"><?= e($_SESSION['user']['username']) ?></td></tr>
        <tr><td class="text-gray" style="padding:8px 12px 8px 0;">Nom complet</td><td><?= e($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?></td></tr>
        <tr><td class="text-gray" style="padding:8px 12px 8px 0;">Email</td><td><?= e($_SESSION['user']['email'] ?? '—') ?></td></tr>
        <tr><td class="text-gray" style="padding:8px 12px 8px 0;">Rôle</td><td><span class="badge badge-blue"><?= e($_SESSION['user']['role_label']) ?></span></td></tr>
      </tbody></table>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Changer le mot de passe</span></div>
    <div class="card-body">
      <form method="POST" action="?route=settings.save">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <div class="form-group">
          <label class="form-label">Mot de passe actuel</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nouveau mot de passe</label>
          <input type="password" name="new_password" class="form-control" required minlength="8">
        </div>
        <div class="form-group">
          <label class="form-label">Confirmer le mot de passe</label>
          <input type="password" name="confirm_password" class="form-control" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">Modifier le mot de passe</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Mes permissions</span></div>
    <div class="card-body">
      <div style="display:flex; flex-wrap:wrap; gap:8px;">
        <?php foreach ($_SESSION['user']['permissions'] ?? [] as $perm): ?>
        <span class="badge badge-blue"><?= e($perm) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
