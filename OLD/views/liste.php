<?php
// ============================================================
//  views/liste.php
//  COUCHE VUE — Affichage de la liste des produits
//  BTS SIO SLAM — Séquence MVC
// ============================================================
//
//  Variables reçues du Contrôleur (via extract()) :
//    $produits  : array  — tableau de tous les produits
//    $titre     : string — titre de la page
//    $message   : string|null — message flash (ajout/modif/suppression)
//    $type_msg  : string — type Bootstrap de l'alerte (success, warning…)
//    $terme     : string — terme de recherche en cours
?>

<!-- ── En-tête de la vue ── -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 fw-bold">
            <i class="bi bi-boxes text-primary me-2"></i><?= htmlspecialchars($titre) ?>
        </h1>
        <small class="text-muted">
            <?= count($produits) ?> produit(s)
            <?= $terme ? 'pour « ' . htmlspecialchars($terme) . ' »' : 'en stock' ?>
        </small>
    </div>
    <a href="index.php?action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Ajouter un produit
    </a>
</div>

<!-- ── Message flash (succès / avertissement) ── -->
<?php if ($message): ?>
    <div class="alert alert-<?= $type_msg ?> alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-<?= $type_msg === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- ── Barre de recherche ── -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" action="index.php" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="recherche" class="form-control border-start-0 ps-0"
                       placeholder="Rechercher un produit…"
                       value="<?= htmlspecialchars($terme) ?>">
            </div>
            <button type="submit" class="btn btn-outline-primary">Chercher</button>
            <?php if ($terme): ?>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- ── Tableau des produits ── -->
<?php if (empty($produits)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="mt-3 text-muted fs-5">Aucun produit trouvé.</p>
        <a href="index.php?action=create" class="btn btn-primary mt-2">
            <i class="bi bi-plus-circle me-1"></i>Ajouter le premier produit
        </a>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th class="text-end">Prix</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td class="ps-4 text-muted small"><?= $produit['id'] ?></td>
                        <td>
                            <div class="fw-semibold">
                                <?= htmlspecialchars($produit['nom']) ?>
                            </div>
                            <?php if ($produit['description']): ?>
                                <small class="text-muted d-block text-truncate" style="max-width:320px;">
                                    <?= htmlspecialchars($produit['description']) ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($produit['categorie_nom']): ?>
                                <span class="badge rounded-pill"
                                      style="background-color:<?= $produit['categorie_couleur'] ?>;">
                                    <?= htmlspecialchars($produit['categorie_nom']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end fw-semibold text-nowrap">
                            <?= number_format($produit['prix'], 2, ',', ' ') ?> €
                        </td>
                        <td class="text-center">
                            <?php
                            $classe = match(true) {
                                $produit['stock'] === 0   => 'danger',
                                $produit['stock'] < 5     => 'warning',
                                default                   => 'success',
                            };
                            ?>
                            <span class="badge bg-<?= $classe ?>">
                                <?= $produit['stock'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <!-- Voir le détail -->
                                <a href="index.php?action=show&id=<?= $produit['id'] ?>"
                                   class="btn btn-outline-secondary" title="Détail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <!-- Modifier -->
                                <a href="index.php?action=edit&id=<?= $produit['id'] ?>"
                                   class="btn btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <!-- Supprimer -->
                                <button type="button"
                                        class="btn btn-outline-danger"
                                        title="Supprimer"
                                        onclick="confirmerSuppression(<?= $produit['id'] ?>, '<?= htmlspecialchars(addslashes($produit['nom'])) ?>')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- ── Formulaire de suppression (caché) ── -->
<!-- La suppression passe par POST pour éviter les suppressions accidentelles via URL -->
<form id="formSuppression" method="POST" action="index.php" style="display:none;">
    <input type="hidden" name="id" id="champId">
    <!-- Le paramètre ?action=delete est passé via l'action du formulaire JS -->
</form>

<!-- ── Modal de confirmation ── -->
<div class="modal fade" id="modalSuppression" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Voulez-vous vraiment supprimer <strong id="nomProduitModal"></strong> ?
                <p class="text-danger small mt-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i>Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="btnConfirmerSupp">
                    <i class="bi bi-trash3 me-1"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Affiche la modal de confirmation puis soumet le formulaire de suppression.
 * Note : on utilise un POST pour la suppression (sécurité — pas de suppression via GET)
 */
function confirmerSuppression(id, nom) {
    document.getElementById('nomProduitModal').textContent = nom;

    document.getElementById('btnConfirmerSupp').onclick = function () {
        const form = document.getElementById('formSuppression');
        form.action = 'index.php?action=delete&id=' + id;
        form.submit();
    };

    const modal = new bootstrap.Modal(document.getElementById('modalSuppression'));
    modal.show();
}
</script>
