<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
$page_title = "Inscription — MonShop";

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token de securite invalide.";
    } else {
        $nom  = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mdp  = $_POST['mot_de_passe'] ?? '';
        $mdp2 = $_POST['mot_de_passe2'] ?? '';

        if ($nom === '')   $errors[] = "Le nom est obligatoire.";
        if ($email === '') $errors[] = "L'email est obligatoire.";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format d'email invalide.";
        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/',$mdp)) $errors[] = "Le mot de passe doit contenir au moins 8 caracteres et au moin un alphabet majescule et un nombre.";
        if ($mdp !== $mdp2)   $errors[] = "Les mots de passe ne correspondent pas.";

        if (empty($errors)) {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $check->execute([':email' => $email]);
            if ($check->fetch()) {
                $errors[] = "Cet email est deja utilise.";
            } else {
                $hash = password_hash($mdp, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe) VALUES (:nom, :email, :mdp)");
                $stmt->execute([':nom' => $nom, ':email' => $email, ':mdp' => $hash]);
                $success = "Inscription reussie ! <a href='login.php'>Connectez-vous</a>.";
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-4 text-center">Inscription</h4>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <?php csrf_input(); ?>
                        <div class="mb-3">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe (min 6 caracteres)</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" name="mot_de_passe2" class="form-control" required>
                        </div>
                        <button class="btn btn-dark w-100">S'inscrire</button>
                    </form>
                    <p class="mt-3 text-center">Deja un compte ? <a href="login.php">Connectez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>