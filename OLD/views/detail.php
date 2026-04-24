<?php
// ============================================================
//  views/detail.php
//  COUCHE VUE — Fiche détail d'un produit
//  BTS SIO SLAM — Séquence MVC
// ============================================================
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0 fw-bold">
        <i class="bi bi-box-seam text-primary me-2"></i>
        <?= htmlspecialchars($produit['nom']) ?>
    </h1>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <?php if ($produit['categorie_nom']): ?>
                    <span class="badge rounded-pill mb-3"
                          style="background-color:<?= $produit['categorie_couleur'] ?? '#6c757d' ?>;">
                        <?= htmlspecialchars($produit['categorie_nom']) ?>
                    </span>
                <?php endif; ?>

                <h2 class="h4 fw-bold"><?= htmlspecialchars($produit['nom']) ?></h2>

                <p class="text-muted">
                    <?= $produit['description']
                        ? nl2br(htmlspecialchars($produit['description']))
                        : '<em>Aucune description.</em>' ?>
                </p>

                <hr>

                <div class="row g-3 text-center">
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3">
                            <div class="text-muted small mb-1">Prix unitaire</div>
                            <div class="fs-4 fw-bold text-primary">
                                <?= number_format($produit['prix'], 2, ',', ' ') ?> €
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <?php
                        $classe = match(true) {
                            $produit['stock'] === 0  => 'danger',
                            $produit['stock'] < 5    => 'warning',
                            default                  => 'success',
                        };
                        ?>
                        <div class="bg-light rounded p-3">
                            <div class="text-muted small mb-1">Stock</div>
                            <div class="fs-4 fw-bold text-<?= $classe ?>">
                                <?= $produit['stock'] ?> unités
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3">
                            <div class="text-muted small mb-1">Valeur totale</div>
                            <div class="fs-4 fw-bold text-dark">
                                <?= number_format($produit['prix'] * $produit['stock'], 2, ',', ' ') ?> €
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-muted small">
                    <i class="bi bi-clock me-1"></i>
                    Créé le <?= date('d/m/Y à H:i', strtotime($produit['created_at'])) ?>
                    · Mis à jour le <?= date('d/m/Y à H:i', strtotime($produit['updated_at'])) ?>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 d-grid gap-2">
                <a href="index.php?action=edit&id=<?= $produit['id'] ?>" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Modifier ce produit
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul me-1"></i>Retour au catalogue
                </a>
            </div>
        </div>
    </div>
</div>
