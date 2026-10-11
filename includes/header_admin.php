<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $page_title ?? 'Admin — MonShop' ?></title>
  <!-- Bootstrap CSS local -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }

    footer {
      background: #212529;
      color: #aaa;
    }
  </style>
</head>

<body>

  <!-- Navbar Admin -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="../index.php">MonShop</a>
      <div class="navbar-nav ms-auto flex-row gap-2">
        <a class="nav-link text-warning" href="index.php">Admin</a>
        <a class="nav-link" href="products.php">Produits</a>
        <a class="nav-link" href="categories.php">Categories</a>
        <a class="nav-link" href="../logout.php">Deconnexion</a>
      </div>
    </div>
  </nav>