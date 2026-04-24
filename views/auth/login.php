<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — MediTimmy</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">
</head>
<body>
<div class="login-wrap">
  <!-- LEFT: Form -->
  <div class="login-left">
    <div class="login-card">
      <div class="login-logo">
        <img src="<?= BASE_URL ?>/public/img/logo.png" alt="MediTimmy">
        <h1>Medi<span>Timmy</span></h1>
        <p>Gestion de cabinet médical</p>
      </div>

      <?php $flash = getFlash(); if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <?= e($flash['msg']) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="<?= BASE_URL ?>/index.php?route=login">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group">
          <label class="form-label">Identifiant</label>
          <input type="text" name="username" class="form-control" placeholder="nom.prenom" required autofocus autocomplete="username">
        </div>

        <div class="form-group">
          <label class="form-label">Mot de passe</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn-primary w-full" style="justify-content:center; padding:12px;">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
          Se connecter
        </button>
      </form>

      <p class="text-center text-sm text-gray mt-4">
        Compte créé par l'administrateur. <br>
        Contact : <strong>admin@meditimmy.fr</strong>
      </p>
    </div>
  </div>

  <!-- RIGHT: Hero -->
  <div class="login-right">
    <div class="login-hero">
      <h2>Bienvenue sur<br>MediTimmy</h2>
      <p>La solution complète de gestion pour votre cabinet médical. Agenda intelligent, dossiers patients sécurisés et collaboration simplifiée.</p>
      <div class="login-features">
        <div class="login-feature">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span>Agenda drag & drop</span>
        </div>
        <div class="login-feature">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span>Contrôle des accès par rôle</span>
        </div>
        <div class="login-feature">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>Dossiers médicaux sécurisés</span>
        </div>
        <div class="login-feature">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <span>Notifications email automatiques</span>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
