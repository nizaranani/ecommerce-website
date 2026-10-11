<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_admin();
$page_title = "Admin — Produits";

$errors  = [];
$success = '';

// Message apres suppression
if (isset($_GET['msg']) && $_GET['msg'] === 'supprime') {
    $success = "Produit supprime.";
}

// ---- AJOUT / MODIFICATION ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token invalide.";
    } else {
        $id          = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
        $nom         = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix        = $_POST['prix'] ?? '';
        $stock       = $_POST['stock'] ?? '';
        $categorie_id = isset($_POST['categorie_id']) && is_numeric($_POST['categorie_id']) ? (int)$_POST['categorie_id'] : null;

        // Validation
        if ($nom === '')                          $errors[] = "Le nom est obligatoire.";
        if (!is_numeric($prix) || $prix < 0)     $errors[] = "Prix invalide.";
        if (!is_numeric($stock) || $stock < 0)   $errors[] = "Stock invalide.";

        // Gestion de l'image
        $image = $_POST['old_image'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($_FILES['image']['type'], $allowed)) {
                $errors[] = "Format image non autorise (jpg, png, gif, webp).";
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $errors[] = "L'image ne doit pas depasser 2 Mo.";
            } else {
                $ext   = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid('prod_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $image);
            }
        }

        if (empty($errors)) {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE products SET nom=:nom, description=:desc, prix=:prix, stock=:stock, categorie_id=:cat, image=:img WHERE id=:id");
                $stmt->execute([':nom' => $nom, ':desc' => $description, ':prix' => $prix, ':stock' => $stock, ':cat' => $categorie_id, ':img' => $image, ':id' => $id]);
                $success = "Produit modifie.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO products (nom, description, prix, stock, categorie_id, image) VALUES (:nom,:desc,:prix,:stock,:cat,:img)");
                $stmt->execute([':nom' => $nom, ':desc' => $description, ':prix' => $prix, ':stock' => $stock, ':cat' => $categorie_id, ':img' => $image]);
                $success = "Produit ajoute.";
            }
        }
    }
}

// Charger le produit a modifier
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$products   = $pdo->query("SELECT p.*, c.nom AS categorie FROM products p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/header_admin.php'; ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Gestion des Produits</h3>
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

    <!-- Formulaire ajout / modification -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white"><?= $edit ? 'Modifier le produit' : 'Ajouter un produit' ?></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php csrf_input(); ?>

                 
                <?php if ($edit): ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit['image'] ?? '') ?>">
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nom du produit *</label>
                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($edit['nom'] ?? $_POST['nom'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Prix (MAD) *</label>
                        <input type="number" step="0.01" min="0" name="prix" class="form-control" value="<?= htmlspecialchars($edit['prix'] ?? $_POST['prix'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock *</label>
                        <input type="number" min="0" name="stock" class="form-control" value="<?= htmlspecialchars($edit['stock'] ?? $_POST['stock'] ?? '0') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Categorie</label>
                        <select name="categorie_id" class="form-select">
                            <option value="">-- Choisir --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($edit['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($edit['description'] ?? $_POST['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Image (jpg/png/gif, max 2Mo)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if (!empty($edit['image'])): ?>
                            <small class="text-muted">Actuelle : <?= htmlspecialchars($edit['image']) ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-dark"><?= $edit ? 'Mettre a jour' : 'Ajouter' ?></button>
                        <?php if ($edit): ?><a href="products.php" class="btn btn-outline-secondary ms-2">Annuler</a><?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des produits -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Categorie</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Aucun produit.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td>
                            <?php if ($p['image'] && file_exists('../uploads/products/' . $p['image'])): ?>
                                <img src="../uploads/products/<?= htmlspecialchars($p['image']) ?>" width="50" class="rounded">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['nom']) ?></td>
                        <td><?= htmlspecialchars($p['categorie'] ?? '—') ?></td>
                        <td><?= number_format($p['prix'], 2) ?> MAD</td>
                        <td><?= $p['stock'] ?></td>
                        <td>
                            <a href="products.php?edit=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="delete_product.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer_admin.php'; ?>