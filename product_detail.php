<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.nom AS categorie FROM products p LEFT JOIN categories c ON p.categorie_id = c.id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Produit introuvable.");
}

$page_title = htmlspecialchars($product['nom']) . " — MonShop";
?>
<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-5">
            <?php if ($product['image'] && file_exists('uploads/products/' . $product['image'])): ?>
                <img src="uploads/products/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded shadow" alt="">
            <?php else: ?>
                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height:300px;color:#fff;">
                    Pas d'image
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <span class="badge bg-secondary mb-2"><?= htmlspecialchars($product['categorie'] ?? 'Sans categorie') ?></span>
            <h2><?= htmlspecialchars($product['nom']) ?></h2>
            <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
            <h4 class="text-success"><?= number_format($product['prix'], 2) ?> MAD</h4>
            <p class="text-muted">Stock disponible : <strong><?= $product['stock'] ?></strong></p>
            <a href="products.php" class="btn btn-outline-secondary mt-3">Retour aux produits</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>