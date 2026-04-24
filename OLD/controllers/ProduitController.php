<?php
// =============================================================
//  controllers/ProduitController.php
//  COUCHE CONTRÔLEUR — Chef d'orchestre de l'application
//  BTS SIO SLAM — Séquence MVC
// =============================================================
//
//  ┌─────────────────────────────────────────────────────────┐
//  │  RÔLE DU CONTRÔLEUR                                     │
//  │  • Reçoit la requête HTTP (GET/POST + paramètres)       │
//  │  • Appelle le Model pour les données                    │
//  │  • Choisit la View à afficher                           │
//  │  • NE contient pas de SQL, ni de HTML                   │
//  └─────────────────────────────────────────────────────────┘

namespace controllers;

use models\Categorie;
use models\Produit;

require_once __DIR__ . '/../models/Produit.php';
require_once __DIR__ . '/../models/Categorie.php';

class ProduitController
{
    private Produit $modelProduit;
    private Categorie $modelCategorie;

    public function __construct()
    {
        $this->modelProduit = new Produit();
        $this->modelCategorie = new Categorie();
    }

    // ─────────────────────────────────────────────────────────
    //  ACTION : index — Liste de tous les produits
    //  URL : index.php  ou  index.php?action=index
    // ─────────────────────────────────────────────────────────
    public function index(): void
    {
        // 1. Récupération des données via le Model
        $terme = $_GET['recherche'] ?? '';
        $produits = $terme
            ? $this->modelProduit->search($terme)
            : $this->modelProduit->findAll();

        // 2. Variables transmises à la View
        $titre = 'Catalogue produits';
        $message = $_SESSION['message'] ?? null;
        $type_msg = $_SESSION['type_msg'] ?? 'success';
        unset($_SESSION['message'], $_SESSION['type_msg']);

        // 3. Chargement de la View
        $this->render('liste', compact('produits', 'titre', 'message', 'type_msg', 'terme'));
    }

    // ─────────────────────────────────────────────────────────
    //  ACTION : create — Formulaire de création
    //  URL : index.php?action=create
    // ─────────────────────────────────────────────────────────
    public function create(): void
    {
        $categories = $this->modelCategorie->findAll();
        $titre = 'Ajouter un produit';
        $erreurs = [];
        $produit = [];    // produit vide pour le formulaire

        $this->render('formulaire', compact('categories', 'titre', 'erreurs', 'produit'));
    }

    // ─────────────────────────────────────────────────────────
    //  ACTION : store — Traitement du formulaire d'ajout (POST)
    //  URL : index.php?action=store  [méthode POST]
    // ─────────────────────────────────────────────────────────
    public function store(): void
    {
        $data = $this->extrairePost();

        // Validation métier via le Model
        $erreurs = $this->modelProduit->valider($data);

        if (!empty($erreurs)) {
            // Erreurs → retour au formulaire avec les erreurs et les données saisies
            $categories = $this->modelCategorie->findAll();
            $titre = 'Ajouter un produit';
            $produit = $data;
            $this->render('formulaire', compact('categories', 'titre', 'erreurs', 'produit'));
            return;
        }

        // Insertion en base
        $this->modelProduit->create($data);

        // Message flash + redirection (pattern PRG : Post/Redirect/Get)
        $_SESSION['message'] = 'Produit ajouté avec succès !';
        $_SESSION['type_msg'] = 'success';
        header('Location: index.php');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    //  ACTION : edit — Formulaire de modification
    //  URL : index.php?action=edit&id=X
    // ─────────────────────────────────────────────────────────
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $produit = $this->modelProduit->findById($id);

        if (!$produit) {
            $this->notFound();
            return;
        }

        $categories = $this->modelCategorie->findAll();
        $titre = 'Modifier le produit';
        $erreurs = [];

        $this->render('formulaire', compact('categories', 'titre', 'erreurs', 'produit'));
    }

    // ─────────────────────────────────────────────────────────
    //  ACTION : update — Traitement de la modification (POST)
    //  URL : index.php?action=update&id=X  [méthode POST]
    // ─────────────────────────────────────────────────────────
    public function update(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $data = $this->extrairePost();

        $erreurs = $this->modelProduit->valider($data);

        if (!empty($erreurs)) {
            $categories = $this->modelCategorie->findAll();
            $titre = 'Modifier le produit';
            $produit = array_merge(['id' => $id], $data);
            $this->render('formulaire', compact('categories', 'titre', 'erreurs', 'produit'));
            return;
        }

        $this->modelProduit->update($id, $data);

        $_SESSION['message'] = 'Produit modifié avec succès !';
        $_SESSION['type_msg'] = 'success';
        header('Location: index.php');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    //  ACTION : delete — Suppression (POST pour la sécurité)
    //  URL : index.php?action=delete&id=X  [méthode POST]
    // ─────────────────────────────────────────────────────────
    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->modelProduit->delete($id);

        $_SESSION['message'] = 'Produit supprimé.';
        $_SESSION['type_msg'] = 'warning';
        header('Location: index.php');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    //  ACTION : show — Fiche détail d'un produit
    //  URL : index.php?action=show&id=X
    // ─────────────────────────────────────────────────────────
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $produit = $this->modelProduit->findById($id);

        if (!$produit) {
            $this->notFound();
            return;
        }

        $titre = $produit['nom'];
        $this->render('detail', compact('produit', 'titre'));
    }

    // ─────────────────────────────────────────────────────────
    //  MÉTHODES PRIVÉES — Utilitaires internes au contrôleur
    // ─────────────────────────────────────────────────────────

    /**
     * Extrait et filtre les données POST du formulaire produit.
     * htmlspecialchars() protège contre les injections XSS.
     */
    private function extrairePost(): array
    {
        return [
            'nom' => trim(htmlspecialchars($_POST['nom'] ?? '')),
            'description' => trim(htmlspecialchars($_POST['description'] ?? '')),
            'prix' => $_POST['prix'] ?? 0,
            'stock' => $_POST['stock'] ?? 0,
            'id_categorie' => $_POST['id_categorie'] ?? null,
        ];
    }

    /**
     * Charge une View en lui injectant les variables via extract().
     *
     * @param string $view Nom du fichier dans views/ (sans .php)
     * @param array $data Variables à rendre disponibles dans la vue
     */
    private function render(string $view, array $data = []): void
    {
        // extract() transforme les clés du tableau en variables locales
        // Ex: $data['produits'] devient $produits dans la vue
        extract($data);

        require __DIR__ . '/../views/templates/header.php';
        require __DIR__ . "/../views/$view.php";
        require __DIR__ . '/../views/templates/footer.php';
    }

    /**
     * Affiche une page 404 si un produit n'est pas trouvé.
     */
    private function notFound(): void
    {
        http_response_code(404);
        $titre = 'Produit introuvable';
        $this->render('404', compact('titre'));
    }
}
