<?php
// models/PatientModel.php

/**
 * Modèle gérant toutes les opérations en base de données
 * liées à la table "patients".
 */
class PatientModel {

    // Instance PDO partagée par toutes les méthodes du modèle
    private PDO $pdo;

    // Constructeur : récupère la connexion PDO via la fonction globale getPDO()
    public function __construct() { $this->pdo = getPDO(); }

    /**
     * Récupère tous les patients avec les infos de leur compte utilisateur lié.
     * @return array  Liste complète des patients triée par nom puis prénom
     */
    public function getAll(): array {
        // LEFT JOIN users pour avoir username et statut actif même si pas de compte lié
        return $this->pdo->query("
            SELECT p.*, u.username, u.actif 
            FROM patients p 
            LEFT JOIN users u ON u.id = p.user_id 
            ORDER BY p.nom, p.prenom
        ")->fetchAll();
    }

    /**
     * Trouve un patient par son ID en base.
     * @param int $id   ID du patient recherché
     * @return array|null  Données du patient, ou null s'il n'existe pas
     */
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.username, u.email as user_email 
            FROM patients p 
            LEFT JOIN users u ON u.id = p.user_id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        // fetch() retourne false si rien trouvé → on renvoie null à la place
        return $stmt->fetch() ?: null;
    }

    /**
     * Trouve le patient associé à un compte utilisateur donné.
     * Utile pour retrouver le profil patient d'un user connecté.
     * @param int $userId   ID du compte utilisateur
     * @return array|null   Données du patient, ou null si aucun lien
     */
    public function findByUserId(int $userId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Recherche des patients par nom, prénom ou email (recherche partielle).
     * @param string $q   Terme de recherche saisi par l'utilisateur
     * @return array      Liste des patients correspondants (max 30 résultats)
     */
    public function search(string $q): array {
        // Encadre le terme avec % pour une recherche "contient"
        $like = '%' . $q . '%';
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.username 
            FROM patients p 
            LEFT JOIN users u ON u.id = p.user_id 
            WHERE p.nom LIKE ? OR p.prenom LIKE ? OR p.email LIKE ? 
            ORDER BY p.nom 
            LIMIT 30
        ");
        // Le terme $like est passé 3 fois, un par colonne recherchée
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    /**
     * Insère un nouveau patient dans la base de données
     * et retourne son ID généré automatiquement.
     * @param array $data  Tableau associatif contenant les infos du patient
     * @return int         L'ID du patient nouvellement créé
     */
    public function create(array $data): int {
        // Prépare la requête SQL avec des "?" comme marqueurs de position
        // (évite les injections SQL)
        $stmt = $this->pdo->prepare("
            INSERT INTO patients 
                (user_id, nom, prenom, date_naissance, email, telephone, adresse) 
            VALUES 
                (?,?,?,?,?,?,?)
        ");
        // Exécute la requête en remplaçant chaque "?" par la valeur correspondante
        // "?? null" = si la clé n'existe pas dans $data, on insère NULL en base
        $stmt->execute([
            $data['user_id']        ?? null,  // Lié au compte utilisateur (optionnel)
            $data['nom'],                     // Nom de famille (obligatoire)
            $data['prenom'],                  // Prénom (obligatoire)
            $data['date_naissance'] ?? null,  // Date de naissance (optionnelle)
            $data['email']          ?? null,  // Email (optionnel)
            $data['telephone']      ?? null,  // Téléphone (optionnel)
            $data['adresse']        ?? null,  // Adresse postale (optionnelle)
        ]);
        // Récupère l'ID auto-incrémenté généré par le INSERT
        // et le cast en int (lastInsertId() retourne une string par défaut)
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Met à jour les informations d'un patient existant.
     * @param int   $id    ID du patient à modifier
     * @param array $data  Nouvelles valeurs à enregistrer
     * @return void
     */
    public function update(int $id, array $data): void {
        $stmt = $this->pdo->prepare("
            UPDATE patients 
            SET nom=?, prenom=?, date_naissance=?, email=?, telephone=?, adresse=? 
            WHERE id=?
        ");
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['date_naissance'] ?? null,  // Optionnel
            $data['email']          ?? null,  // Optionnel
            $data['telephone']      ?? null,  // Optionnel
            $data['adresse']        ?? null,  // Optionnel
            $id                               // Condition WHERE, passé en dernier
        ]);
    }

    /**
     * Lie un patient à un compte utilisateur existant.
     * Utilisé lors de la création de compte pour un patient déjà en base.
     * @param int $patientId  ID du patient à mettre à jour
     * @param int $userId     ID du compte utilisateur à associer
     * @return void
     */
    public function linkUser(int $patientId, int $userId): void {
        // Simple UPDATE : on renseigne la FK user_id sur le patient ciblé
        $this->pdo->prepare("UPDATE patients SET user_id=? WHERE id=?")
                  ->execute([$userId, $patientId]);
    }
}<?php
// models/PatientModel.php

class PatientModel {
    private PDO $pdo;
    public function __construct() { $this->pdo = getPDO(); }

    public function getAll(): array {
        return $this->pdo->query("SELECT p.*, u.username, u.actif FROM patients p LEFT JOIN users u ON u.id=p.user_id ORDER BY p.nom, p.prenom")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT p.*, u.username, u.email as user_email FROM patients p LEFT JOIN users u ON u.id=p.user_id WHERE p.id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE user_id=?");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function search(string $q): array {
        $like = '%' . $q . '%';
        $stmt = $this->pdo->prepare("SELECT p.*, u.username FROM patients p LEFT JOIN users u ON u.id=p.user_id WHERE p.nom LIKE ? OR p.prenom LIKE ? OR p.email LIKE ? ORDER BY p.nom LIMIT 30");
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

   /**
 * Insère un nouveau patient dans la base de données
 * et retourne son ID généré automatiquement.
 *
 * @param array $data  Tableau associatif contenant les infos du patient
 * @return int         L'ID du patient nouvellement créé
 */
public function create(array $data): int {

    // Prépare la requête SQL avec des "?" comme marqueurs de position
    // (évite les injections SQL)
    $stmt = $this->pdo->prepare("
        INSERT INTO patients 
            (user_id, nom, prenom, date_naissance, email, telephone, adresse) 
        VALUES 
            (?,?,?,?,?,?,?)
    ");

    // Exécute la requête en remplaçant chaque "?" par la valeur correspondante
    // "?? null" = si la clé n'existe pas dans $data, on insère NULL en base
    $stmt->execute([
        $data['user_id']        ?? null,  // Lié au compte utilisateur (optionnel)
        $data['nom'],                     // Nom de famille (obligatoire)
        $data['prenom'],                  // Prénom (obligatoire)
        $data['date_naissance'] ?? null,  // Date de naissance (optionnelle)
        $data['email']          ?? null,  // Email (optionnel)
        $data['telephone']      ?? null,  // Téléphone (optionnel)
        $data['adresse']        ?? null,  // Adresse postale (optionnelle)
    ]);

    // Récupère l'ID auto-incrémenté généré par le INSERT
    // et le cast en int (lastInsertId() retourne une string par défaut)
    return (int)$this->pdo->lastInsertId();
}

    public function update(int $id, array $data): void {
        $stmt = $this->pdo->prepare("UPDATE patients SET nom=?, prenom=?, date_naissance=?, email=?, telephone=?, adresse=? WHERE id=?");
        $stmt->execute([$data['nom'], $data['prenom'], $data['date_naissance'] ?? null, $data['email'] ?? null, $data['telephone'] ?? null, $data['adresse'] ?? null, $id]);
    }

    public function linkUser(int $patientId, int $userId): void {
        $this->pdo->prepare("UPDATE patients SET user_id=? WHERE id=?")->execute([$userId, $patientId]);
    }
}
