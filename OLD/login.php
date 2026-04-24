<?php
session_start();

// Génération du jeton CSRF si inexistant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Configuration base de données
$host = 'localhost';
$bdd = 'meditimmy';
$username = 'root';
$password = '475Ju56n@';
$error = "";

// En-têtes de sécurité HTTP
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Vérification si le formulaire a été envoyé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du jeton CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Erreur de validation du formulaire. Veuillez réessayer.";
    } else {
        $uname = $_POST['uname'] ?? '';
        $psw = $_POST['psw'] ?? '';

        try {
            // Connexion à la base de données
            $pdo = new PDO("mysql:host=$host;dbname=$bdd;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Vérification des identifiants dans la base de données
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE username = :username");
            $stmt->execute(['username' => $uname]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($psw, $user['password'])) {
                $_SESSION['user'] = $uname;
                $_SESSION['user_id'] = $user['id'];

                // Mise à jour de la date de dernière connexion
                $stmt = $pdo->prepare("UPDATE utilisateurs SET date_derniere_connexion = NOW() WHERE id = :id");
                $stmt->execute(['id' => $user['id']]);

                header("Location: index.php");
                exit();
            } else {
                $error = "Identifiants invalides. Veuillez réessayer.";
            }
        } catch(PDOException $e) {
            $error = "Erreur de connexion. Veuillez réessayer plus tard.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MédiRDV – Connexion</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1a6fa8 0%, #0d9488 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
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
      max-width: 400px;
      padding: 32px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }
    .logo { text-align: center; margin-bottom: 8px; }
    .logo-icon {
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, #1a6fa8, #0d9488);
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }
    .logo-icon svg {
      width: 32px; height: 32px;
      fill: none; stroke: #fff;
      stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }
    .title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a6fa8;
      text-align: center;
      margin-bottom: 4px;
    }
    .subtitle {
      font-size: 0.85rem;
      color: #64748b;
      text-align: center;
      margin-bottom: 16px;
    }
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #ecfdf5;
      color: #0d9488;
      font-size: 11px;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 20px;
      margin-bottom: 20px;
    }
    .badge svg {
      width: 13px; height: 13px;
      stroke: #0d9488; stroke-width: 2.5; fill: none;
    }
    label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
    }
    .input-wrap { position: relative; margin-bottom: 16px; }
    .input-wrap svg {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 18px; height: 18px;
      stroke: #94a3b8; stroke-width: 1.8; fill: none;
    }
    input[type=text], input[type=password] {
      width: 100%;
      padding: 12px 12px 12px 40px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: 15px;
      color: #1e293b;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
      background: #f8fafc;
    }
    input[type=text]:focus, input[type=password]:focus {
      border-color: #1a6fa8;
      box-shadow: 0 0 0 3px rgba(26,111,168,.12);
      background: #fff;
    }
    .forgot { text-align: right; margin-top: -8px; margin-bottom: 16px; }
    .forgot a { font-size: 13px; color: #1a6fa8; text-decoration: none; font-weight: 500; }
    .forgot a:hover { text-decoration: underline; }
    .remember { display: flex; align-items: center; gap: 8px; margin: 8px 0 20px; }
    .remember input[type=checkbox] { width: 16px; height: 16px; accent-color: #1a6fa8; cursor: pointer; }
    .remember label { font-size: 13px; color: #64748b; font-weight: 400; margin: 0; cursor: pointer; }
    .btn-primary {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #1a6fa8, #0d9488);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: opacity .2s, transform .2s;
      letter-spacing: .3px;
    }
    .btn-primary:hover { opacity: .92; transform: translateY(-1px); }
    .divider {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 20px 0;
      color: #94a3b8;
      font-size: 13px;
    }
    .divider::before, .divider::after {
      content: ''; flex: 1;
      height: 1px; background: #e2e8f0;
    }
    .btn-secondary {
      width: 100%;
      padding: 12px;
      background: #fff;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 500;
      color: #374151;
      cursor: pointer;
      transition: background .2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .btn-secondary:hover { background: #f1f5f9; }
    .btn-secondary svg { width: 18px; height: 18px; }
    .register {
      text-align: center;
      margin-top: 20px;
      padding-top: 18px;
      border-top: 1px solid #f1f5f9;
      font-size: 13px;
      color: #64748b;
    }
    .Ncompte-link {
      text-align: center; margin-top: 20px; padding-top: 18px;
      border-top: 1px solid #f1f5f9; font-size: 13px; color: #64748b;
    }
    .Ncompte-link a { color: #0d9488; font-weight: 600; text-decoration: none; }
    .Ncompte-link a:hover { text-decoration: underline; }
    .register a { color: #0d9488; font-weight: 600; text-decoration: none; }
    .register a:hover { text-decoration: underline; }
    .error {
      color: #ef4444;
      font-size: 13px;
      margin-bottom: 16px;
      text-align: center;
    }
  </style>
</head>
<body>
  <a href="pagedeco.php" class="btn-back-home">
    <svg viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
    Retour
  </a>
  <div class="card">
    <div class="logo">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <div class="title">MédiRDV</div>
      <div class="subtitle">Votre espace de prise de rendez-vous en ligne</div>
      <div style="display:flex; justify-content:center;">
        <div class="badge">
          <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Connexion sécurisée
        </div>
      </div>
    </div>

    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <label for="email">Adresse e-mail</label>
      <div class="input-wrap">
        <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        <input type="text" id="email" name="uname" placeholder="prenom.nom@email.com" value="<?= htmlspecialchars($_POST['uname'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <label for="password">Mot de passe</label>
      <div class="input-wrap">
        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <input type="password" id="password" name="psw" placeholder="•••••••">
      </div>

      <div class="remember">
        <input type="checkbox" id="remember">
        <label for="remember">Rester connecté</label>
      </div>

      <button type="submit" class="btn-primary">Se connecter</button>
    </form>

    <div class="Ncompte-link" id="Ncompte-link">
      Pas de compte ? <a href="Ncompte.php">Créer un compte</a>
    </div>
  </div>
</body>
</html>