<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
$page_title = "Accueil — MonShop";

$stmt = $pdo->query("SELECT p.*, c.nom AS categorie FROM products p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.created_at DESC LIMIT 4");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<!-- Hero -->
<div class="bg-dark text-white py-5 mb-4">
    <div class="container text-center">
        <h1 class="display-5 fw-bold">Bienvenue sur MonShop</h1>
        <p class="lead">Les meilleurs produits au meilleur prix</p>
        <a href="products.php" class="btn btn-warning btn-lg">Voir tous les produits</a>
    </div>
</div>

<!-- Derniers Produits -->
<div class="container mb-5">
    <h3 class="mb-4">Nouveaux Produits</h3>
    <div class="row g-4">
        <?php foreach ($products as $p): ?>
            <div class="col-sm-6 col-md-3">
                <div class="card h-100 product-card">
                    <?php if ($p['image'] && file_exists('uploads/products/' . $p['image'])): ?>
                        <img src="uploads/products/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:180px;color:#fff;">
                            Pas d'image
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-secondary mb-1"><?= htmlspecialchars($p['categorie'] ?? 'Sans categorie') ?></span>
                        <h6 class="card-title"><?= htmlspecialchars($p['nom']) ?></h6>
                        <p class="text-success fw-bold mt-auto"><?= number_format($p['prix'], 2) ?> MAD</p>
                        <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm">Voir detail</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>