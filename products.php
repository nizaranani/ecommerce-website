<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
$page_title = "Produits — MonShop";

// --- Pagination ---
$per_page     = 6;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset       = ($current_page - 1) * $per_page;

// --- Filtres ---
$search     = trim($_GET['search'] ?? '');
$cat_filter = isset($_GET['categorie']) && is_numeric($_GET['categorie']) ? (int)$_GET['categorie'] : 0;

$where  = "WHERE 1=1";
$params = [];

if ($search !== '') {
    $where .= " AND p.nom OR p.prix LIKE :search";
    $params[':search'] = "%$search%";
}
if ($cat_filter > 0) {
    $where .= " AND p.categorie_id = :cat";
    $params[':cat'] = $cat_filter;
}

// Compter le total
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where");
$count_stmt->execute($params);
$total       = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// Recuperer les produits
$params[':limit']  = $per_page;
$params[':offset'] = $offset;
$stmt = $pdo->prepare("SELECT p.*, c.nom AS categorie FROM products p LEFT JOIN categories c ON p.categorie_id = c.id $where ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $val) {
    if ($key === ':limit' || $key === ':offset') {
        $stmt->bindValue($key, $val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $val);
    }
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Categories pour le filtre
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <h3 class="mb-3">Tous les Produits</h3>

    <!-- Formulaire de filtre -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un produit(prix,nom)..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <select name="categorie" class="form-select">
                <option value="0">Toutes les categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat_filter == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-dark w-100">Filtrer</button>
        </div>
    </form>

    <!-- Grille des produits -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <p class="text-muted">Aucun produit trouve.</p>
        <?php endif; ?>
        <?php foreach ($products as $p): ?>
            <div class="col-sm-6 col-md-4">
                <div class="card h-100 product-card">
                    <?php if ($p['image'] && file_exists('uploads/products/' . $p['image'])): ?>
                        <img src="uploads/products/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:180px;color:#fff;">
                            Pas d'image
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-secondary mb-1"><?= htmlspecialchars($p['categorie'] ?? '—') ?></span>
                        <h6 class="card-title"><?= htmlspecialchars($p['nom']) ?></h6>
                        <p class="text-muted small"><?= htmlspecialchars(substr($p['description'] ?? '', 0, 60)) ?>...</p>
                        <p class="text-success fw-bold"><?= number_format($p['prix'], 2) ?> MAD</p>
                        <p class="text-muted small">Stock : <?= $p['stock'] ?></p>
                        <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm mt-auto">Voir detail</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&categorie=<?= $cat_filter ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>