<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediTimmy — Gestion de cabinet médical</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">
<style>
  .hero {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--blue-900) 0%, var(--blue-700) 55%, var(--teal) 100%);
    display: flex; flex-direction: column;
  }
  .hero-nav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 60px;
    border-bottom: 1px solid rgba(255,255,255,.1);
  }
  .hero-nav-logo {
    display: flex; align-items: center; gap: 14px;
  }
  .hero-nav-logo img { width: 48px; height: 48px; border-radius: 50%; }
  .hero-nav-logo span {
    font-family: var(--font-display); font-weight: 800; font-size: 1.4rem; color: #fff;
  }
  .hero-nav-logo span em { color: var(--blue-300); font-style: normal; }
  .hero-body {
    flex: 1; display: flex; align-items: center; justify-content: center;
    padding: 60px;
    flex-direction: column; text-align: center;
  }
  .hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.12); color: var(--blue-200);
    padding: 6px 16px; border-radius: 20px;
    font-size: .8rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
    margin-bottom: 24px; border: 1px solid rgba(255,255,255,.15);
  }
  .hero-title {
    font-family: var(--font-display); font-weight: 800;
    font-size: clamp(2.2rem, 5vw, 3.8rem); color: #fff;
    line-height: 1.15; margin-bottom: 20px;
    max-width: 780px;
  }
  .hero-title span { color: var(--blue-300); }
  .hero-sub {
    color: rgba(255,255,255,.75); font-size: 1.1rem;
    max-width: 560px; line-height: 1.7; margin-bottom: 40px;
  }
  .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; justify-content: center; }
  .btn-white {
    background: #fff; color: var(--blue-700);
    padding: 14px 30px; border-radius: var(--radius-sm);
    font-weight: 800; font-size: 1rem;
    display: inline-flex; align-items: center; gap: 8px;
    transition: all .2s; box-shadow: 0 4px 20px rgba(0,0,0,.15);
  }
  .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.2); color: var(--blue-800); }
  .btn-outline-white {
    background: rgba(255,255,255,.1); color: #fff;
    padding: 14px 30px; border-radius: var(--radius-sm);
    font-weight: 800; font-size: 1rem; border: 2px solid rgba(255,255,255,.3);
    display: inline-flex; align-items: center; gap: 8px;
    transition: all .2s;
  }
  .btn-outline-white:hover { background: rgba(255,255,255,.2); color: #fff; }

  .features {
    background: #fff; padding: 80px 60px;
  }
  .features-title {
    text-align: center; font-family: var(--font-display);
    font-weight: 800; font-size: 2rem; color: var(--gray-900);
    margin-bottom: 8px;
  }
  .features-sub { text-align: center; color: var(--gray-500); margin-bottom: 52px; }
  .features-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 28px; max-width: 1100px; margin: 0 auto;
  }
  .feature-card {
    padding: 28px; border-radius: var(--radius);
    border: 1.5px solid var(--gray-200);
    transition: all .2s;
  }
  .feature-card:hover { border-color: var(--blue-300); box-shadow: var(--shadow-md); transform: translateY(-3px); }
  .feature-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
  }
  .feature-icon svg { width: 26px; height: 26px; }
  .feature-title { font-family: var(--font-display); font-weight: 700; font-size: 1.05rem; margin-bottom: 8px; }
  .feature-desc { color: var(--gray-500); font-size: .9rem; line-height: 1.6; }

  .roles-section { background: var(--gray-50); padding: 80px 60px; }
  .roles-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 18px; max-width: 1100px; margin: 48px auto 0;
  }
  .role-card {
    background: #fff; border-radius: var(--radius); padding: 22px 20px;
    border: 1.5px solid var(--gray-200); text-align: center;
    transition: all .2s;
  }
  .role-card:hover { border-color: var(--blue-300); box-shadow: var(--shadow); }
  .role-emoji { font-size: 2rem; margin-bottom: 8px; }
  .role-name { font-weight: 700; font-size: .95rem; color: var(--gray-900); }
  .role-desc { font-size: .8rem; color: var(--gray-500); margin-top: 4px; }

  .footer {
    background: var(--gray-900); color: var(--gray-400);
    text-align: center; padding: 28px 40px; font-size: .85rem;
  }
  .footer strong { color: #fff; }
</style>
</head>
<body>

<!-- HERO -->
<section class="hero">
  <nav class="hero-nav">
    <div class="hero-nav-logo">
      <img src="<?= BASE_URL ?>/public/img/logo.png" alt="MediTimmy">
      <span>Medi<em>Timmy</em></span>
    </div>
    <a href="?route=login" class="btn-white" style="padding:10px 22px; font-size:.9rem;">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
      Se connecter
    </a>
  </nav>
  <div class="hero-body">
    <div class="hero-badge">
      <svg fill="currentColor" viewBox="0 0 24 24" width="14" height="14"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      Application de gestion médicale v1.0
    </div>
    <h1 class="hero-title">
      La gestion de votre cabinet,<br>
      <span>simplifiée et sécurisée</span>
    </h1>
    <p class="hero-sub">
      MediTimmy centralise agenda, dossiers patients et gestion des accès dans une interface fluide, pensée pour les professionnels de santé.
    </p>
    <div class="hero-cta">
      <a href="?route=login" class="btn-white">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
        Accéder à l'application
      </a>
      <a href="#features" class="btn-outline-white">
        Découvrir les fonctionnalités ↓
      </a>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="features" id="features">
  <h2 class="features-title">Tout ce dont votre cabinet a besoin</h2>
  <p class="features-sub">Une solution complète, conçue pour les équipes médicales de toutes tailles.</p>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon" style="background:var(--blue-50); color:var(--blue-600);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <div class="feature-title">Agenda Drag & Drop</div>
      <div class="feature-desc">Glissez-déposez les rendez-vous pour les replanifier instantanément. Vue semaine, mois et liste.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#ECFDF5; color:var(--green);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <div class="feature-title">Dossiers médicaux</div>
      <div class="feature-desc">Antécédents, allergies, traitements, consultations — tout l'historique médical en un coup d'œil.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#F5F3FF; color:var(--purple);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <div class="feature-title">Contrôle des accès</div>
      <div class="feature-desc">Rôles personnalisables avec permissions granulaires. Chaque utilisateur ne voit que ce dont il a besoin.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#FFFBEB; color:var(--amber);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <div class="feature-title">Gestion multi-rôles</div>
      <div class="feature-desc">Médecins, infirmiers, secrétaires, patients — chaque rôle dispose d'une interface adaptée à ses besoins.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#F0F9FF; color:var(--teal);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <div class="feature-title">Notifications email</div>
      <div class="feature-desc">Envoi automatique des identifiants, confirmations de RDV et rappels 24h avant la consultation.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#FEF2F2; color:var(--red);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      </div>
      <div class="feature-title">Tableau de bord</div>
      <div class="feature-desc">Statistiques en temps réel : RDV du jour, de la semaine, patients enregistrés, taux d'annulation.</div>
    </div>
  </div>
</section>

<!-- ROLES -->
<section class="roles-section">
  <h2 class="features-title">Une interface pour chaque rôle</h2>
  <p class="features-sub">MediTimmy s'adapte aux besoins spécifiques de chaque membre de votre équipe.</p>
  <div class="roles-grid">
    <div class="role-card">
      <div class="role-emoji">👨‍⚕️</div>
      <div class="role-name">Médecin</div>
      <div class="role-desc">Dossiers complets, consultations, agenda, prescriptions</div>
    </div>
    <div class="role-card">
      <div class="role-emoji">💉</div>
      <div class="role-name">Infirmier</div>
      <div class="role-desc">Dossiers médicaux, soins, agenda complet</div>
    </div>
    <div class="role-card">
      <div class="role-emoji">🩺</div>
      <div class="role-name">Autre praticien</div>
      <div class="role-desc">Accès médical complet, consultations spécialisées</div>
    </div>
    <div class="role-card">
      <div class="role-emoji">📋</div>
      <div class="role-name">Secrétaire</div>
      <div class="role-desc">Agenda, fiches administratives, sans données médicales</div>
    </div>
    <div class="role-card">
      <div class="role-emoji">🔑</div>
      <div class="role-name">Admin local</div>
      <div class="role-desc">Gestion des comptes, rôles et permissions</div>
    </div>
    <div class="role-card">
      <div class="role-emoji">🧑‍💼</div>
      <div class="role-name">Patient</div>
      <div class="role-desc">Consultation de ses rendez-vous uniquement</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <p><strong>MediTimmy</strong> v1.0 — Application de gestion de cabinet médical — Architecture MVC PHP</p>
  <p style="margin-top:6px; font-size:.8rem;">Connexion admin : <strong>admin / Admin1234!</strong> &nbsp;|&nbsp; Médecin : <strong>dr.martin / Medecin1!</strong> &nbsp;|&nbsp; Secrétaire : <strong>sec.dupont / Secret1!</strong></p>
</footer>

</body>
</html>
