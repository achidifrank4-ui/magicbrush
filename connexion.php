<?php
ob_start(); // Tampon de sortie — permet les redirections même si du HTML a été envoyé
// connexion.php
require_once 'config/config.php';
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();


// Déjà connecté → rediriger
if (isset($_SESSION['user_id'])) {
    $redirect = $_GET['redirect'] ?? 'compte.php';
    header('Location: ' . ($redirect === 'admin' ? 'admin/index.php' : $redirect));
    exit;
}

$error   = '';
$success = '';
$tab     = $_GET['tab'] ?? 'login'; // 'login' ou 'register'
$redirect = $_GET['redirect'] ?? '';

// --- CONNEXION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['role']      = $user['role'];

            if ($user['role'] === 'admin' || $redirect === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: ' . ($redirect ?: 'compte.php'));
            }
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
    $tab = 'login';
}

// --- INSCRIPTION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $prenom   = trim($_POST['prenom'] ?? '');
    $nom      = trim($_POST['nom'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$prenom || !$nom || !$email || !$password) {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($password !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier si l'email existe déjà
        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password_hash, role) VALUES (:nom, :prenom, :email, :hash, 'customer')");
            $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':hash' => $hash]);

            $success = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
            $tab = 'login';
        }
    }
    if (!$success) $tab = 'register';
}

$page_title = 'Connexion — Magic Brush';
require_once 'includes/header.php';
?>

<div style="min-height: 80vh; display: flex; align-items: center; background: linear-gradient(135deg, var(--color-bg) 0%, #e8f0e9 100%); padding: var(--spacing-lg) 0;">
    <div class="container" style="max-width: 480px; margin: 0 auto;">

        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="index.php" class="brand-logo" style="font-size: 2rem; justify-content: center; margin-bottom: 0.5rem; display: flex;">
                <i class="ph-fill ph-paw-print"></i> Magic Brush
            </a>
        </div>

        <!-- Card -->
        <div style="background: var(--color-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); overflow: hidden;">

            <!-- Onglets -->
            <div style="display: flex; border-bottom: 1px solid var(--color-border);">
                <a href="?tab=login<?= $redirect ? '&redirect='.$redirect : '' ?>" style="flex: 1; text-align: center; padding: 1.25rem; font-weight: 600; font-family: var(--font-heading); color: <?= $tab === 'login' ? 'var(--color-primary)' : 'var(--color-text-light)' ?>; border-bottom: 3px solid <?= $tab === 'login' ? 'var(--color-primary)' : 'transparent' ?>; text-decoration: none; transition: var(--transition);">
                    Connexion
                </a>
                <a href="?tab=register<?= $redirect ? '&redirect='.$redirect : '' ?>" style="flex: 1; text-align: center; padding: 1.25rem; font-weight: 600; font-family: var(--font-heading); color: <?= $tab === 'register' ? 'var(--color-primary)' : 'var(--color-text-light)' ?>; border-bottom: 3px solid <?= $tab === 'register' ? 'var(--color-primary)' : 'transparent' ?>; text-decoration: none; transition: var(--transition);">
                    Créer un compte
                </a>
            </div>

            <div style="padding: 2rem;">
                <?php if ($error): ?>
                <div style="background: #fdecea; border: 1px solid #f5c6cb; color: #721c24; padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph ph-warning-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph ph-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
                <?php endif; ?>

                <?php if ($tab === 'login'): ?>
                <!-- Formulaire Connexion -->
                <form method="POST" action="?tab=login<?= $redirect ? '&redirect='.$redirect : '' ?>">
                    <input type="hidden" name="action" value="login">
                    <div class="admin-form-group" style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-weight: 500; margin-bottom: 0.4rem;">Email</label>
                        <input type="email" name="email" required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 1rem; outline: none; transition: border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='var(--color-border)'"
                               placeholder="votre@email.com">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 500; margin-bottom: 0.4rem;">Mot de passe</label>
                        <input type="password" name="password" required autocomplete="current-password"
                               style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 1rem; outline: none; transition: border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='var(--color-border)'"
                               placeholder="••••••••" autocomplete="current-password" >
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.05rem;">
                        Se connecter
                    </button>
                </form>

                <p style="text-align: center; margin-top: 1.5rem; color: var(--color-text-light); font-size: 0.9rem;">
                    Pas encore de compte ? <a href="?tab=register" style="color: var(--color-primary); font-weight: 600;">Créer un compte</a>
                </p>

                <?php else: ?>
                <!-- Formulaire Inscription -->
                <form method="POST" action="?tab=register<?= $redirect ? '&redirect='.$redirect : '' ?>">
                    <input type="hidden" name="action" value="register">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-weight: 500; margin-bottom: 0.4rem;">Prénom</label>
                            <input type="text" name="prenom" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
                                   style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 1rem; outline: none;"
                                   onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='var(--color-border)'">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 500; margin-bottom: 0.4rem;">Nom</label>
                            <input type="text" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                                   style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 1rem; outline: none;"
                                   onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='var(--color-border)'">
                        </div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 500; margin-bottom: 0.4rem;">Email</label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 1rem; outline: none;"
                               onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='var(--color-border)'"
                               placeholder="votre@email.com">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 500; margin-bottom: 0.4rem;">Mot de passe <span style="font-size: 0.8rem; color: var(--color-text-light;">(min. 8 caractères)</span></label>
                        <input type="password" name="password" required minlength="8"
                               style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 1rem; outline: none;"
                               onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='var(--color-border)'">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 500; margin-bottom: 0.4rem;">Confirmer le mot de passe</label>
                        <input type="password" name="confirm" required
                               style="width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 1rem; outline: none;"
                               onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='var(--color-border)'">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.05rem;">
                        Créer mon compte
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
