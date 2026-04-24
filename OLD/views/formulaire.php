<?php
// ============================================================
//  views/formulaire.php
//  COUCHE VUE — Formulaire création ET modification
//  BTS SIO SLAM — Séquence MVC
// ============================================================
//
//  Variables reçues du Contrôleur :
//    $produit    : array  — données du produit (vide si création)
//    $categories : array  — liste des catégories pour le <select>
//    $erreurs    : array  — messages d'erreur de validation
//    $titre      : string — titre de la page
//
//  Ce formulaire est réutilisé pour la création ET la modification.
//  La différence : si $produit['id'] est défini → c'est une modification.
?>

<?php $estModification = !empty($produit['id']); ?>

<!-- ── En-tête ── -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0 fw-bold">
            <i class="bi bi-<?= $estModification ? 'pencil-square' : 'plus-circle' ?> text-primary me-2"></i>
            <?= htmlspecialchars($titre) ?>
        </h1>
        <small class="text-muted">
            <?= $estModification
                ? 'Modification du produit #' . $produit['id']
                : 'Remplissez le formulaire pour ajouter un nouveau produit' ?>
        </small>
    </div>
</div>

<!-- ── Affichage des erreurs de validation ── -->
<?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger d-flex gap-2 align-items-start">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
        <div>
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($erreurs as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- ── Formulaire ── -->
<!--
    Action = selon création ou modification
    Method = POST (les données sensibles ne passent pas dans l'URL)
-->
<form method="POST"
      action="index.php?action=<?= $estModification ? 'update&id=' . $produit['id'] : 'store' ?>"
      novalidate>

    <div class="row g-4">

        <!-- Colonne principale -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold text-muted">
                        <i class="bi bi-info-circle me-1"></i>Informations du produit
                    </h6>
                </div>
                <div class="card-body p-4">

                    <!-- Nom -->
                    <div class="mb-4">
                        <label for="nom" class="form-label fw-semibold">
                            Nom du produit <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nom" name="nom"
                               class="form-control form-control-lg <?= !empty($erreurs) && empty($produit['nom']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($produit['nom'] ?? '') ?>"
                               placeholder="Ex : Clavier mécanique Keychron K3"
                               required autofocus>
                        <div class="form-text">Le nom sera affiché dans le catalogue.</div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea id="description" name="description"
                                  class="form-control" rows="4"
                                  placeholder="Caractéristiques, utilisation…"><?= htmlspecialchars($produit['description'] ?? '') ?></textarea>
                        <div class="form-text">Facultatif — apparaît dans la fiche détail.</div>
                    </div>

                    <!-- Prix et Stock sur la même ligne -->
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="prix" class="form-label fw-semibold">
                                Prix (€) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" id="prix" name="prix"
                                       class="form-control"
                                       value="<?= $produit['prix'] ?? '0.00' ?>"
                                       step="0.01" min="0" placeholder="0.00" required>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="stock" class="form-label fw-semibold">
                                Quantité en stock <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" id="stock" name="stock"
                                       class="form-control"
                                       value="<?= $produit['stock'] ?? '0' ?>"
                                       step="1" min="0" placeholder="0" required>
                                <span class="input-group-text">unités</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold text-muted">
                        <i class="bi bi-tag me-1"></i>Catégorie
                    </h6>
                </div>
                <div class="card-body p-4">
                    <label for="id_categorie" class="form-label fw-semibold">Catégorie</label>
                    <select id="id_categorie" name="id_categorie" class="form-select">
                        <option value="">— Sans catégorie —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($produit['id_categorie'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-<?= $estModification ? 'check-circle' : 'plus-circle' ?> me-2"></i>
                        <?= $estModification ? 'Enregistrer les modifications' : 'Ajouter le produit' ?>
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Annuler
                    </a>
                </div>
            </div>

            <!-- Encadré pédagogique -->
            <div class="alert alert-info mt-3 small">
                <i class="bi bi-lightbulb-fill me-1"></i>
                <strong>Cycle MVC ici :</strong><br>
                Ce formulaire est la <strong>Vue</strong>.<br>
                En cliquant "Enregistrer", une requête <code>POST</code> est envoyée au <strong>Contrôleur</strong>,
                qui délègue l'insertion au <strong>Model</strong>, puis redirige vers la liste.
            </div>
        </div>
    </div>

</form>
