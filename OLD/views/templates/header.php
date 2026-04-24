<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre ?? 'Gestion de stock') ?> — StockMVC</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Feuille de style personnalisée -->
    <link href="public/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ============================================================
     COUCHE VUE — Header (commun à toutes les pages)
     Généré par PHP côté serveur à chaque requête.
     ============================================================ -->

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">

        <!-- Logo / Titre de l'application -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
            <i class="bi bi-box-seam-fill fs-5"></i>
            <span>StockMVC</span>
            <span class="badge bg-warning text-dark ms-1 fw-normal" style="font-size:.65rem;">Architecture MVC</span>
        </a>

        <!-- Bouton responsive (mobile) -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (!isset($_GET['action']) || $_GET['action'] === 'index') ? 'active' : '' ?>"
                       href="index.php">
                        <i class="bi bi-list-ul me-1"></i>Catalogue
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($_GET['action'] ?? '') === 'create') ? 'active' : '' ?>"
                       href="index.php?action=create">
                        <i class="bi bi-plus-circle me-1"></i>Ajouter
                    </a>
                </li>
            </ul>

            <!-- Badge pédagogique : couche en cours -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-white-50 small d-none d-lg-inline">Requête HTTP → Contrôleur → Modèle → Vue</span>
                <span class="badge bg-light text-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>Rendu serveur
                </span>
            </div>
        </div>
    </div>
</nav>

<main class="container-fluid py-4 px-4">
