<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_admin();

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: products.php');
    exit;
}

// Verification CSRF sur POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Token invalide.");
    }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header('Location: products.php?msg=supprime');
    exit;
}

// Afficher la page de confirmation
$stmt = $pdo->prepare("SELECT nom FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

$page_title = "Confirmer suppression";
?>
<?php include '../includes/header_admin.php'; ?>

<div class="container my-5">
    <div class="card shadow-sm" style="max-width:500px;margin:auto;">
        <div class="card-body text-center p-4">
            <h5 class="mb-3 text-danger">Confirmer la suppression</h5>
            <p>Voulez-vous vraiment supprimer le produit :</p>
            <p class="fw-bold"><?= htmlspecialchars($product['nom']) ?></p>
            <form method="POST">
                <?php csrf_input(); ?>
                <button class="btn btn-danger me-2">Oui, supprimer</button>
                <a href="products.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer_admin.php'; ?>