<?php
// ============================================================
//  views/templates/footer.php
//  COUCHE VUE — Pied de page commun
//  Affiche des informations pédagogiques sur le cycle MVC.
// ============================================================
?>
</main><!-- /container -->

<!-- ── Bandeau pédagogique ── -->
<footer class="mt-auto py-3 bg-dark text-white">
    <div class="container-fluid px-4">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <small class="text-white-50">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Architecture MVC</strong> — Cette page a été entièrement générée côté serveur par PHP.
                    Chaque action provoque une nouvelle requête HTTP et un rechargement complet.
                </small>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-white-50">
                    BTS SIO SLAM · Séquence Vue.js ·
                    <span class="text-warning">PHP <?= PHP_VERSION ?></span>
                </small>
            </div>
        </div>

        <!-- Schéma du cycle MVC affiché en pied de page -->
        <div class="mt-2 d-flex align-items-center gap-1 flex-wrap">
            <?php
            $couche_active = match($_GET['action'] ?? 'index') {
                'store', 'update', 'delete' => 'Modèle',
                'create', 'edit'            => 'Vue',
                default                     => 'Contrôleur',
            };
            $etapes = ['Navigateur', 'Contrôleur', 'Modèle', 'Vue', 'Navigateur'];
            foreach ($etapes as $i => $etape) :
                $active = $etape === $couche_active;
            ?>
                <span class="badge <?= $active ? 'bg-warning text-dark' : 'bg-secondary' ?> fw-normal">
                    <?= $etape ?>
                </span>
                <?php if ($i < count($etapes) - 1): ?>
                <i class="bi bi-arrow-right text-white-50 small"></i>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS (bundle = Popper inclus) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
