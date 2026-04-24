
<?php
session_start();

// Configuration base de données
$host = 'localhost'; 
$bdd = 'meditimmy';
$username = 'root';
$password = '475Ju56n@';

$error = "";

/// Vérification si le formulaire a été envoyé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST['uname'] ?? '';
    $psw = $_POST['psw'] ?? '';
    $error = '';

    // Vérification que les champs ne sont pas vides
    if (empty($uname) || empty($psw)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Connexion à la base de données
            $pdo = new PDO("mysql:host=$host;dbname=$bdd;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Vérification si l'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE username = :username");
            $stmt->execute(['username' => $uname]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $error = "Ce nom d'utilisateur est déjà pris. Veuillez en choisir un autre.";
            } else {
                // Hashage du mot de passe
                $hashedPassword = password_hash($psw, PASSWORD_DEFAULT);

                // Insertion du nouvel utilisateur
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (username, password, date_creation) VALUES (:username, :password, NOW())");
                $stmt->execute([
                    'username' => $uname,
                    'password' => $hashedPassword
                ]);

                // Redirection vers la page de connexion ou d'accueil
                header("Location: login.php?success=1");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MédiRDV – Créer un compte patient</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1a6fa8 0%, #0d9488 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 30px 20px;
    }
        .btn-back-home {
      position: fixed; top: 18px; left: 18px;
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,0.18); color: #fff;
      border: 1.5px solid rgba(255,255,255,0.4);
      border-radius: 10px; padding: 9px 16px;
      font-size: 14px; font-weight: 600; text-decoration: none;
      backdrop-filter: blur(6px); transition: background .2s, transform .2s;
      z-index: 100;
    }
    .card {
      background: #fff;
      border-radius: 16px;
      width: 90%;
      max-width: 480px;
      padding: 32px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }

    /* Header */
    .header { text-align: center; margin-bottom: 24px; }
    .logo-icon {
      width: 60px; height: 60px;
      background: linear-gradient(135deg, #1a6fa8, #0d9488);
      border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      margin-bottom: 10px;
    }
    .logo-icon svg { width: 28px; height: 28px; fill: none; stroke: #fff; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .title { font-size: 1.4rem; font-weight: 700; color: #1a6fa8; margin-bottom: 4px; }
    .subtitle { font-size: 0.82rem; color: #64748b; }

    /* Steps indicator */
    .steps {
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 28px; gap: 0;
    }
    .step {
      display: flex; flex-direction: column; align-items: center; gap: 6px;
      position: relative;
    }
    .step-circle {
      width: 32px; height: 32px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 700;
      border: 2px solid #e2e8f0;
      background: #fff; color: #94a3b8;
      transition: all .3s;
    }
    .step.active .step-circle { background: linear-gradient(135deg,#1a6fa8,#0d9488); color: #fff; border-color: transparent; }
    .step.done .step-circle { background: #0d9488; color: #fff; border-color: transparent; }
    .step-label { font-size: 11px; color: #94a3b8; font-weight: 500; white-space: nowrap; }
    .step.active .<?php

?>step-label { color: #1a6fa8; font-weight: 600; }
    .step.done .step-label { color: #0d9488; }
    .step-line { width: 48px; height: 2px; background: #e2e8f0; margin-bottom: 18px; transition: background .3s; }
    .step-line.done { background: #0d9488; }

    /* Section label */
    .section-label {
      font-size: 11px; font-weight: 700; text-transform: uppercase;
      letter-spacing: .8px; color: #94a3b8; margin: 20px 0 12px;
      display: flex; align-items: center; gap: 8px;
    }
    .section-label::after { content: ''; flex: 1; height: 1px; background: #f1f5f9; }

    /* Form */
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-group { margin-bottom: 16px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .required { color: #e53e3e; margin-left: 2px; }
    .input-wrap { position: relative; }
    .input-wrap svg {
      position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
      width: 16px; height: 16px; stroke: #94a3b8; stroke-width: 1.8; fill: none; pointer-events: none;
    }
    .input-wrap.has-icon input { padding-left: 36px; }
    input[type=text], input[type=email], input[type=password], input[type=date], input[type=tel], select {
      width: 100%; padding: 11px 12px;
      border: 1.5px solid #e2e8f0; border-radius: 10px;
      font-size: 14px; color: #1e293b; outline: none;
      transition: border-color .2s, box-shadow .2s; background: #f8fafc;
      appearance: none; -webkit-appearance: none;
    }
    input:focus, select:focus {
      border-color: #1a6fa8; box-shadow: 0 0 0 3px rgba(26,111,168,.1); background: #fff;
    }
    input.error { border-color: #e53e3e; box-shadow: 0 0 0 3px rgba(229,62,62,.08); }
    .error-msg { font-size: 12px; color: #e53e3e; margin-top: 4px; display: none; }
    .error-msg.show { display: block; }

    /* Password strength */
    .pwd-strength { margin-top: 8px; }
    .pwd-bars { display: flex; gap: 4px; margin-bottom: 4px; }
    .pwd-bar { flex: 1; height: 3px; border-radius: 2px; background: #e2e8f0; transition: background .3s; }
    .pwd-bar.weak { background: #e53e3e; }
    .pwd-bar.medium { background: #f6ad55; }
    .pwd-bar.strong { background: #0d9488; }
    .pwd-text { font-size: 11px; color: #94a3b8; }

    /* Checkbox */
    .checkbox-wrap { display: flex; align-items: flex-start; gap: 10px; margin: 16px 0; }
    .checkbox-wrap input[type=checkbox] { width: 16px; height: 16px; accent-color: #1a6fa8; cursor: pointer; flex-shrink: 0; margin-top: 2px; }
    .checkbox-wrap label { font-size: 13px; color: #64748b; font-weight: 400; cursor: pointer; line-height: 1.5; }
    .checkbox-wrap a { color: #1a6fa8; text-decoration: none; }
    .checkbox-wrap a:hover { text-decoration: underline; }

    /* Buttons */
    .btn-row { display: flex; gap: 12px; margin-top: 24px; }
    .btn-back {
      flex: 0 0 auto; padding: 13px 20px;
      background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px;
      font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer;
      transition: background .2s; display: flex; align-items: center; gap: 6px;
    }
    .btn-back:hover { background: #f8fafc; }
    .btn-back svg { width: 16px; height: 16px; stroke: #64748b; stroke-width: 2; fill: none; }
    .btn-primary {
      flex: 1; padding: 13px;
      background: linear-gradient(135deg, #1a6fa8, #0d9488);
      color: #fff; border: none; border-radius: 10px;
      font-size: 15px; font-weight: 600; cursor: pointer;
      transition: opacity .2s, transform .2s;
    }
    .btn-primary:hover { opacity: .92; transform: translateY(-1px); }

    /* Login link */
    .login-link {
      text-align: center; margin-top: 20px; padding-top: 18px;
      border-top: 1px solid #f1f5f9; font-size: 13px; color: #64748b;
    }
    .login-link a { color: #0d9488; font-weight: 600; text-decoration: none; }
    .login-link a:hover { text-decoration: underline; }

    /* Step panels */
    .step-panel { display: none; }
    .step-panel.active { display: block; }

    /* Success panel */
    .success-panel {
      text-align: center; padding: 20px 0;
    }
    .success-icon {
      width: 72px; height: 72px;
      background: #ecfdf5; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      margin-bottom: 16px;
    }
    .success-icon svg { width: 36px; height: 36px; stroke: #0d9488; stroke-width: 2.5; fill: none; }
    .success-title { font-size: 1.3rem; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .success-text { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 24px; }
    .success-email { font-weight: 600; color: #1a6fa8; }

    /* Select arrow */
    .select-wrap { position: relative; }
    .select-wrap::after {
      content: ''; position: absolute; right: 12px; top: 50%;
      transform: translateY(-50%); width: 0; height: 0;
      border-left: 5px solid transparent; border-right: 5px solid transparent;
      border-top: 6px solid #94a3b8; pointer-events: none;
    }

    /* Info tip */
    .info-tip {
      background: #eff6ff; border-left: 3px solid #1a6fa8;
      border-radius: 0 8px 8px 0; padding: 10px 14px;
      font-size: 12px; color: #1e40af; line-height: 1.5; margin-bottom: 16px;
    }
    .info-tip strong { font-weight: 600; }
  </style>
</head>
<body>
    <a href="pagedeco.php" class="btn-back-home">
    <svg viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
    Retour
  </a>
  <div class="card">
    <!-- Header -->
    <div class="header">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <div class="title">MédiRDV</div>
      <div class="subtitle">Créer votre compte </div>
    </div>

    <!-- Steps -->
    <div class="steps">
      <div class="step active" id="s1">
        <div class="step-circle">1</div>
        <div class="step-label">Identité</div>
      </div>
      <div class="step-line" id="line1"></div>
      <div class="step" id="s2">
        <div class="step-circle">2</div>
        <div class="step-label">Contact</div>
      </div>
      <div class="step-line" id="line2"></div>
      <div class="step" id="s3">
        <div class="step-circle">3</div>
        <div class="step-label">Sécurité</div>
      </div>
    </div>

    <!-- Step 1 : Identité -->
    <div class="step-panel active" id="panel1">
      <div class="info-tip">
        <strong>Informations requises :</strong> Ces données sont utilisées pour votre dossier médical et vos ordonnances.
      </div>
      <div class="section-label">Informations personnelles</div>
      <div class="row">
        <div class="form-group">
          <label>Civilité <span class="required">*</span></label>
          <div class="select-wrap">
            <select id="civilite">
              <option value="">Choisir</option>
              <option>M.</option>
              <option>Mme</option>
              <option>Autre</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Date de naissance <span class="required">*</span></label>
          <input type="date" id="dob">
        </div>
      </div>
      <div class="row">
        <div class="form-group">
          <label>Prénom <span class="required">*</span></label>
          <div class="input-wrap has-icon">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" id="prenom" placeholder="Jean">
          </div>
          <div class="error-msg" id="prenom-err">Prénom requis</div>
        </div>
        <div class="form-group">
          <label>Nom <span class="required">*</span></label>
          <div class="input-wrap has-icon">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" id="nom" placeholder="Dupont">
          </div>
          <div class="error-msg" id="nom-err">Nom requis</div>
        </div>
      </div>
      <div class="btn-row">
        <button class="btn-primary" onclick="goStep(2)">Continuer →</button>
      </div>
    </div>

    <!-- Step 2 : Contact -->
    <div class="step-panel" id="panel2">
      <div class="section-label">Coordonnées</div>
      <div class="form-group">
        <label>Adresse e-mail <span class="required">*</span></label>
        <div class="input-wrap has-icon">
          <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          <input type="email" id="email" placeholder="jean.dupont@email.com">
        </div>
        <div class="error-msg" id="email-err">Adresse e-mail invalide</div>
      </div>
      <div class="form-group">
        <label>Téléphone portable <span class="required">*</span></label>
        <div class="input-wrap has-icon">
          <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 4.1 5.14 2 2 0 0 1 6.09 3h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.68 2.81a2 2 0 0 1-.45 2.11L10.09 11a16 16 0 0 0 6.86 6.86l1.27-1.27a2 2 0 0 1 2.11-.45c.9.32 1.85.55 2.81.68A2 2 0 0 1 22 16.92z"/></svg>
          <input type="tel" id="tel" placeholder="06 12 34 56 78">
        </div>
        <div class="error-msg" id="tel-err">Numéro de téléphone invalide</div>
      </div>
      <div class="section-label">Adresse postale</div>
      <div class="form-group">
        <label>Rue <span class="required">*</span></label>
        <div class="input-wrap has-icon">
          <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
          <input type="text" id="adresse" placeholder="12 rue de la Paix">
        </div>
      </div>
      <div class="row">
        <div class="form-group">
          <label>Code postal</label>
          <input type="text" id="cp" placeholder="75001" maxlength="5">
        </div>
        <div class="form-group">
          <label>Ville</label>
          <input type="text" id="ville" placeholder="Paris">
        </div>
      </div>
      
      <div class="btn-row">
        <button class="btn-back" onclick="goStep(1)">
          <svg viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
          Retour
        </button>
        <button class="btn-primary" onclick="goStep(3)">Continuer →</button>
      </div>
    </div>

    <!-- Step 3 : Sécurité -->
    <div class="step-panel" id="panel3">
      <div class="section-label">Sécurité du compte</div>
      <div class="form-group">
        <label>Mot de passe <span class="required">*</span></label>
        <div class="input-wrap has-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="password" placeholder="••••••••" oninput="checkStrength(this.value)">
        </div>
        <div class="pwd-strength">
          <div class="pwd-bars">
            <div class="pwd-bar" id="bar1"></div>
            <div class="pwd-bar" id="bar2"></div>
            <div class="pwd-bar" id="bar3"></div>
            <div class="pwd-bar" id="bar4"></div>
          </div>
          <div class="pwd-text" id="pwd-label">Minimum 8 caractères</div>
        </div>
        <div class="error-msg" id="pwd-err">Mot de passe trop faible (min. 8 caractères)</div>
      </div>
      <div class="form-group">
        <label>Confirmer le mot de passe <span class="required">*</span></label>
        <div class="input-wrap has-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="password2" placeholder="••••••••">
        </div>
        <div class="error-msg" id="pwd2-err">Les mots de passe ne correspondent pas</div>
      </div>

      <div class="section-label">Consentements</div>
      <div class="checkbox-wrap">
        <input type="checkbox" id="cgu">
        <label for="cgu">J'accepte les <a href="#">conditions générales d'utilisation</a> et la <a href="#">politique de confidentialité</a> de MédiRDV <span class="required">*</span></label>
      </div>
      <div class="checkbox-wrap">
        <input type="checkbox" id="rgpd">



        <label for="rgpd">J'accepte le traitement de mes données de santé conformément au <a href="https://www.cnil.fr/fr/quest-ce-ce-quune-donnee-de-sante">RGPD</a></label>
      </div>
      <div class="checkbox-wrap">
        <input type="checkbox" id="notif">
        <label for="notif">Je souhaite recevoir des rappels de rendez-vous par SMS et e-mail</label>
      </div>

      <div class="btn-row">
        <button class="btn-back" onclick="goStep(2)">
          <svg viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
          Retour
        </button>
        <button class="btn-primary" onclick="submitForm()">Créer mon compte</button>
      </div>
    </div>

    <!-- Step 4 : Succès -->
    <div class="step-panel" id="panel4">
      <div class="success-panel">
        <div class="success-icon">
          <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
        </div>
        <div class="success-title">Compte créé avec succès !</div>
        <div class="success-text">
          Un e-mail de confirmation a été envoyé à<br>
          <span class="success-email" id="confirm-email"></span><br><br>
          Veuillez cliquer sur le lien de validation pour activer votre compte.
        </div>
        <button class="btn-primary" onclick="window.location.href='login.html'" style="max-width:260px;margin:0 auto;display:block;">
          Se connecter →
        </button>
      </div>
    </div>

    <!-- Login link -->
    <div class="login-link" id="login-link">
      Déjà un compte ? <a href="login.php">Se connecter</a>
    </div>
  </div>

  <script>
    let currentStep = 1;

    function goStep(n) {
      if (n > currentStep && !validateStep(currentStep)) return;

      document.getElementById('panel' + currentStep).classList.remove('active');
      document.getElementById('s' + currentStep)?.classList.remove('active');

      if (n <= 3) {
        for (let i = 1; i < n; i++) {
          document.getElementById('s' + i)?.classList.add('done');
          if (i < 3) {
            const line = document.getElementById('line' + i);
            if (line) line.classList.add('done');
          }
        }
        document.getElementById('s' + n)?.classList.add('active');
        document.getElementById('s' + n)?.classList.remove('done');
      }

      currentStep = n;
      document.getElementById('panel' + n).classList.add('active');

      if (n === 4) {
        document.getElementById('login-link').style.display = 'none';
        document.querySelectorAll('.steps').forEach(el => el.style.display = 'none');
      }
    }

    function validateStep(step) {
      let valid = true;
      if (step === 1) {
        const prenom = document.getElementById('prenom').value.trim();
        const nom = document.getElementById('nom').value.trim();
        if (!prenom) { showErr('prenom', true); valid = false; } else showErr('prenom', false);
        if (!nom) { showErr('nom', true); valid = false; } else showErr('nom', false);
      }
      if (step === 2) {
        const email = document.getElementById('email').value.trim();
        const tel = document.getElementById('tel').value.trim();
        const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        if (!emailOk) { showErr('email', true); valid = false; } else showErr('email', false);
        if (!tel) { showErr('tel', true); valid = false; } else showErr('tel', false);
      }
      return valid;
    }

    function showErr(id, show) {
      const input = document.getElementById(id);
      const msg = document.getElementById(id + '-err');
      if (input) input.classList.toggle('error', show);
      if (msg) msg.classList.toggle('show', show);
    }

    function checkStrength(val) {
      const bars = [document.getElementById('bar1'), document.getElementById('bar2'),
                    document.getElementById('bar3'), document.getElementById('bar4')];
      const label = document.getElementById('pwd-label');
      bars.forEach(b => { b.className = 'pwd-bar'; });
      let score = 0;
      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;
      const cls = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
      const labels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
      for (let i = 0; i < score; i++) bars[i].classList.add(cls);
      label.textContent = val.length === 0 ? 'Minimum 8 caractères' : labels[score] || 'Faible';
      label.style.color = cls === 'strong' ? '#0d9488' : cls === 'medium' ? '#f6ad55' : '#e53e3e';
    }

    function submitForm() {
      const pwd = document.getElementById('password').value;
      const pwd2 = document.getElementById('password2').value;
      const cgu = document.getElementById('cgu').checked;
      let valid = true;

      if (pwd.length < 8) { showErr('pwd', true); valid = false; } else showErr('pwd', false);
      if (pwd !== pwd2 || !pwd2) { showErr('pwd2', true); valid = false; } else showErr('pwd2', false);
      if (!cgu) { document.getElementById('cgu').style.outline = '2px solid #e53e3e'; valid = false; }
      else document.getElementById('cgu').style.outline = '';

      if (!valid) return;

      const email = document.getElementById('email').value.trim();
      document.getElementById('confirm-email').textContent = email;
      goStep(4);
    }

    // Format NSS
    document.getElementById('nss').addEventListener('input', function() {
      let v = this.value.replace(/\s/g,'');
      this.value = v.replace(/(.{1})(.{2})(.{2})(.{2})(.{3})(.{3})(.{2})?/, (m,...g) => g.filter(Boolean).join(' '));
    });
  </script>
</body>
</html>