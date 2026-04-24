<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'MediTimmy') ?> — MediTimmy</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">
<?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body>
<div class="app-layout">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <img src="<?= BASE_URL ?>/public/img/logo.png" alt="MediTimmy">
      <div class="sidebar-logo-text">Medi<span>Timmy</span></div>
    </div>
    <div class="sidebar-user">
      <div class="sidebar-user-name"><?= e($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?></div>
      <span class="sidebar-user-role"><?= e($_SESSION['user']['role_label']) ?></span>
    </div>

    <nav class="sidebar-nav">
      <div class="sidebar-section-label">Principal</div>
      <a href="?route=dashboard" class="<?= ($activeRoute ?? '') === 'dashboard' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Tableau de bord
      </a>

      <?php if (RoleMiddleware::can('voir_agenda')): ?>
      <a href="?route=agenda" class="<?= ($activeRoute ?? '') === 'agenda' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Agenda
      </a>
      <?php endif; ?>

      <?php if (RoleMiddleware::can('voir_mes_rdv')): ?>
      <a href="?route=mes-rdv" class="<?= ($activeRoute ?? '') === 'mes-rdv' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Mes rendez-vous
      </a>
      <?php endif; ?>

      <?php if (RoleMiddleware::can('voir_patients')): ?>
      <div class="sidebar-section-label">Patients</div>
      <a href="?route=patients" class="<?= ($activeRoute ?? '') === 'patients' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Patients
      </a>
      <?php endif; ?>

      <?php if (RoleMiddleware::can('voir_dossiers')): ?>
      <a href="?route=dossiers" class="<?= ($activeRoute ?? '') === 'dossiers' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Dossiers médicaux
      </a>
      <?php endif; ?>

      <?php if (RoleMiddleware::can('gerer_comptes') || RoleMiddleware::can('gerer_roles')): ?>
      <div class="sidebar-section-label">Administration</div>
      <?php if (RoleMiddleware::can('gerer_comptes')): ?>
      <a href="?route=admin.accounts" class="<?= ($activeRoute ?? '') === 'admin.accounts' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Comptes utilisateurs
      </a>
      <?php endif; ?>
      <?php if (RoleMiddleware::can('gerer_roles')): ?>
      <a href="?route=admin.roles" class="<?= ($activeRoute ?? '') === 'admin.roles' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Rôles & Permissions
      </a>
      <?php endif; ?>
      <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
      <a href="?route=settings">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
        Paramètres
      </a>
      <a href="?route=logout" style="margin-top:10px; color: #EF4444;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Déconnexion
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title"><?= e($pageTitle ?? 'Tableau de bord') ?></div>
      <div class="topbar-right">
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>" style="margin:0; padding:8px 14px; font-size:.8rem;">
          <?= e($flash['msg']) ?>
        </div>
        <?php endif; ?>
        <div class="topbar-avatar" title="<?= e($_SESSION['user']['username']) ?>">
          <?= strtoupper(substr($_SESSION['user']['prenom'], 0, 1) . substr($_SESSION['user']['nom'], 0, 1)) ?>
        </div>
      </div>
    </header>

    <main class="page-content">
      <?php echo $content ?? ''; ?>
    </main>
  </div>
</div>

<div id="toast-container"></div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const CSRF_TOKEN = '<?= csrfToken() ?>';
const USER_ROLE = '<?= e($_SESSION['user']['role_nom']) ?>';
const USER_PERMS = <?= json_encode($_SESSION['user']['permissions'] ?? []) ?>;

function can(perm) { return USER_PERMS.includes(perm); }

function toast(msg, type = 'info') {
  const t = document.createElement('div');
  t.className = 'toast ' + type;
  t.innerHTML = msg;
  document.getElementById('toast-container').appendChild(t);
  setTimeout(() => t.remove(), 3500);
}

function apiPost(route, data = {}) {
  const fd = new FormData();
  fd.append('csrf_token', CSRF_TOKEN);
  Object.entries(data).forEach(([k, v]) => fd.append(k, v));
  return fetch(BASE_URL + '/index.php?route=' + route, { method: 'POST', body: fd }).then(r => r.json());
}

// Tab system
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.dataset.tab;
    btn.closest('.tabs').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(target)?.classList.add('active');
  });
});
</script>
<?php if (!empty($extraScript)) echo $extraScript; ?>
</body>
</html>
