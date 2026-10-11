<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_admin();

// Verifier CSRF
if (!verify_csrf_token($_GET['csrf_token'] ?? '')) {
    die("Token invalide.");
}

// Recuperer tous les produits
$stmt = $pdo->query("SELECT p.id, p.nom, p.description, p.prix, p.stock, c.nom AS categorie, p.created_at
                     FROM products p
                     LEFT JOIN categories c ON p.categorie_id = c.id
                     ORDER BY p.id");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// En-tetes HTTP pour telecharger le CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="produits_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// BOM pour que Excel lise bien l'UTF-8
fputs($output, "\xEF\xBB\xBF");

// En-tete du CSV
fputcsv($output, ['ID', 'Nom', 'Description', 'Prix (MAD)', 'Stock', 'Categorie', 'Date ajout'], ';');

// Lignes
foreach ($products as $p) {
    fputcsv($output, [
        $p['id'],
        $p['nom'],
        $p['description'],
        $p['prix'],
        $p['stock'],
        $p['categorie'] ?? 'Sans categorie',
        $p['created_at']
    ], ';');
}

fclose($output);
exit;
