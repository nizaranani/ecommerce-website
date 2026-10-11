<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_admin();
$page_title = "Admin — Categories";

$errors  = [];
$success = '';

// Message apres suppression
if (isset($_GET['msg']) && $_GET['msg'] === 'supprime') {
    $success = "Categorie supprimee.";
}

// ---- AJOUT / MODIFICATION ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token invalide.";
    } else {
        $id  = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
        $nom = trim($_POST['nom'] ?? '');

        if ($nom === '') $errors[] = "Le nom est obligatoire.";

        if (empty($errors)) {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE categories SET nom = :nom WHERE id = :id");
                $stmt->execute([':nom' => $nom, ':id' => $id]);
                $success = "Categorie modifiee.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (:nom)");
                $stmt->execute([':nom' => $nom]);
                $success = "Categorie ajoutee.";
            }
        }
    }
}

// Charger pour modification
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$categories = $pdo->query("SELECT c.*, COUNT(p.id) AS nb_products FROM categories c LEFT JOIN products p ON p.categorie_id = c.id GROUP BY c.id ORDER BY c.nom")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/header_admin.php'; ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Gestion des Categories</h3>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Tableau de bord</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <!-- Formulaire -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white"><?= $edit ? 'Modifier la categorie' : 'Ajouter une categorie' ?></div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <?php csrf_input(); ?>
                <?php if ($edit): ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <?php endif; ?>
                <div class="col-md-6">
                    <label class="form-label">Nom de la categorie *</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($edit['nom'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button class="btn btn-dark me-2"><?= $edit ? 'Modifier' : 'Ajouter' ?></button>
                    <?php if ($edit): ?><a href="categories.php" class="btn btn-outline-secondary">Annuler</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Nb Produits</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['nom']) ?></td>
                    <td><?= $cat['nb_products'] ?></td>
                    <td>
                        <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="delete_category.php?id=<?= $cat['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer_admin.php'; ?>