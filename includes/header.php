<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $page_title ?? 'MonShop' ?></title>
  <!-- Bootstrap CSS local (fichier téléchargé depuis getbootstrap.com) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }

    .product-card img {
      height: 180px;
      object-fit: cover;
    }

    .product-card {
      transition: none;
    }

    .product-card:hover {
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    footer {
      background: #212529;
      color: #aaa;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php">MonShop</a>
      <div class="navbar-nav ms-auto flex-row gap-2">
        <a class="nav-link" href="index.php">Accueil</a>
        <a class="nav-link" href="products.php">Produits</a>
        <?php if (is_logged_in()): ?>
          <?php if (is_admin()): ?>
            <a class="nav-link text-warning" href="admin/index.php">Admin</a>
          <?php endif; ?>
          <a class="nav-link" href="logout.php">Deconnexion</a>
        <?php else: ?>
          <a class="nav-link" href="login.php">Connexion</a>
          <a class="nav-link" href="register.php">Inscription</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>