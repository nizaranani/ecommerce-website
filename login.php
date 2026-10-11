<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
$page_title = "Connexion — MonShop";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token de securite invalide.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $mdp   = $_POST['mot_de_passe'] ?? '';

        if ($email === '') $errors[] = "L'email est obligatoire.";
        if ($mdp === '')   $errors[] = "Le mot de passe est obligatoire.";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($mdp, $user['mot_de_passe'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['role']     = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $errors[] = "Email ou mot de passe incorrect.";
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
                    <h4 class="mb-4 text-center">Connexion</h4>

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
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                        <button class="btn btn-dark w-100">Se connecter</button>
                    </form>
                    <p class="mt-3 text-center">Pas de compte ? <a href="register.php">S'inscrire</a></p>
                    <hr>
                    <p class="text-muted small text-center">Demo admin : nizar@gmail.com / Nizaranani1</p>
                    <p class="text-muted small text-center">Demo client : client@gmail.com / client123</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>