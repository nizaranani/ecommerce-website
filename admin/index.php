<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_admin();
$page_title = "Admin — Tableau de bord";

$total_products   = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_users      = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>
<?php include '../includes/header_admin.php'; ?>

<div class="container my-4">
    <h3>Tableau de bord Admin</h3>
    <hr>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Produits</h5>
                    <h2><?= $total_products ?></h2>
                    <a href="products.php" class="btn btn-light btn-sm mt-2">Gerer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Categories</h5>
                    <h2><?= $total_categories ?></h2>
                    <a href="categories.php" class="btn btn-light btn-sm mt-2">Gerer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5>Utilisateurs</h5>
                    <h2><?= $total_users ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <a href="export_csv.php?csrf_token=<?= generate_csrf_token() ?>" class="btn btn-success">Exporter produits en CSV</a>
        <a href="../index.php" class="btn btn-outline-secondary ms-2">Retour au site</a>
    </div>
</div>

<?php include '../includes/footer_admin.php'; ?>